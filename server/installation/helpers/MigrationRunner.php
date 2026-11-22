<?php
/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020/2026 OpenVPN-WebAdmin
 *
 * NOTICE OF LICENSE
 *
 * GNU AFFERO GENERAL PUBLIC LICENSE V3
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/agpl-3.0.en.html
 *
 * @author      Wutze
 * @copyright   2025 OpenVPN-WebAdmin
 * @link        https://github.com/Wutze/OpenVPN-WebAdmin
 * @see         Internal Documentation ~/doc/
 * @version     2.0.1
 */

declare(strict_types=1);

namespace Micro\OpenvpnWebadmin\Installation\Helpers;

use RuntimeException;

final class MigrationRunner
{
    private const VERSION_PATTERN = '/\A[0-9]+\.[0-9]+\.[0-9]+\z/';
    private string $statusFile;
    private string $migrationsDirectory;

    public function __construct(string $statusFile, string $migrationsDirectory)
    {
        $this->statusFile = $statusFile;
        $this->migrationsDirectory = $migrationsDirectory;
    }

    public static function validVersion(string $version): bool
    {
        return preg_match(self::VERSION_PATTERN, $version) === 1;
    }

    public function initialize(string $version): void
    {
        if (!self::validVersion($version)) {
            throw new RuntimeException('Invalid start version.');
        }
        $this->withLock(function () use ($version): void {
            if (file_exists($this->statusFile) || is_link($this->statusFile)) {
                throw new RuntimeException('Migration status already exists; it will not be overwritten.');
            }
            $this->saveStatus([$version => ['StartVersion']]);
        });
    }

    public function validateTarget(string $targetVersion): void
    {
        if (!self::validVersion($targetVersion)) {
            throw new RuntimeException('Invalid target version.');
        }
        $this->withLock(function () use ($targetVersion): void {
            [, , $latestVersion] = $this->readStatus();
            if (version_compare($targetVersion, $latestVersion, '<')) {
                throw new RuntimeException("Downgrades are not supported: {$latestVersion} -> {$targetVersion}.");
            }
        });
    }

    public function run(string $targetVersion, callable $execute): void
    {
        if (!self::validVersion($targetVersion)) {
            throw new RuntimeException('Invalid target version.');
        }

        $this->withLock(function () use ($targetVersion, $execute): void {
            [$status, $startVersion, $latestVersion] = $this->readStatus();
            if (version_compare($targetVersion, $latestVersion, '<')) {
                throw new RuntimeException("Downgrades are not supported: {$latestVersion} -> {$targetVersion}.");
            }

            if (!is_dir($this->migrationsDirectory) || !is_readable($this->migrationsDirectory)) {
                throw new RuntimeException('Migration directory is missing or unreadable: ' . $this->migrationsDirectory);
            }
            $directories = scandir($this->migrationsDirectory);
            if ($directories === false) {
                throw new RuntimeException('Could not read the migration directory.');
            }
            $versions = [];
            foreach ($directories as $version) {
                if (
                    self::validVersion($version)
                    && is_dir($this->migrationsDirectory . '/' . $version)
                    && version_compare($version, $startVersion, '>')
                    && version_compare($version, $latestVersion, '>=')
                    && version_compare($version, $targetVersion, '<=')
                ) {
                    $versions[] = $version;
                }
            }
            usort($versions, static fn(string $a, string $b): int => version_compare($a, $b));

            foreach ($versions as $version) {
                $directory = $this->migrationsDirectory . '/' . $version;
                $entries = scandir($directory);
                if ($entries === false) {
                    throw new RuntimeException('Could not read migration directory: ' . $directory);
                }
                $files = array_values(array_filter($entries, static fn(string $entry): bool =>
                    preg_match('/\A[0-9]+\.update\.sql\z/', $entry) === 1 && is_file($directory . '/' . $entry)
                ));
                usort($files, static fn(string $a, string $b): int => strnatcmp($a, $b) ?: strcmp($a, $b));
                foreach ($files as $file) {
                    if (in_array($file, $status[$version] ?? [], true)) {
                        continue;
                    }
                    $sql = file_get_contents($directory . '/' . $file);
                    if ($sql === false || trim($sql) === '') {
                        throw new RuntimeException("Migration is unreadable or empty: {$version}/{$file}.");
                    }
                    $execute($sql, $version . '/' . $file);
                    $status[$version][] = $file;
                    $this->saveStatus($status);
                    echo "Applied {$version}/{$file}\n";
                }
            }
            if (!array_key_exists($targetVersion, $status)) {
                $status[$targetVersion] = [];
                $this->saveStatus($status);
            }
        });
    }

    private function readStatus(): array
    {
        if (is_link($this->statusFile) || !is_file($this->statusFile)) {
            throw new RuntimeException('Migration status is missing or is a symlink: ' . $this->statusFile);
        }
        $contents = file_get_contents($this->statusFile);
        $status = $contents === false ? null : json_decode($contents, true);
        if (!is_array($status) || $status === []) {
            throw new RuntimeException('Migration status is invalid.');
        }
        $start = null;
        $latest = null;
        foreach ($status as $version => $files) {
            if (!is_string($version) || !self::validVersion($version) || !is_array($files) || !array_is_list($files)) {
                throw new RuntimeException('Invalid version or migration list in status.');
            }
            foreach ($files as $file) {
                if (!is_string($file) || ($file !== 'StartVersion' && !preg_match('/\A[0-9]+\.update\.sql\z/', $file))) {
                    throw new RuntimeException('Invalid migration name in status.');
                }
                if ($file === 'StartVersion') {
                    if ($start !== null) {
                        throw new RuntimeException('Migration status contains multiple start versions.');
                    }
                    $start = $version;
                }
            }
            if ($latest === null || version_compare($version, $latest, '>')) {
                $latest = $version;
            }
        }
        if ($start === null || $latest === null) {
            throw new RuntimeException('Migration status has no start version.');
        }
        foreach (array_keys($status) as $version) {
            if (version_compare($version, $start, '<')) {
                throw new RuntimeException('Migration status contains a version older than the start version.');
            }
        }
        return [$status, $start, $latest];
    }

    private function withLock(callable $action): void
    {
        $directory = dirname($this->statusFile);
        if (is_link($directory) || (!is_dir($directory) && !mkdir($directory, 0770, true) && !is_dir($directory))) {
            throw new RuntimeException('Cannot create migration status directory.');
        }
        $lockPath = $this->statusFile . '.lock';
        if (is_link($lockPath)) {
            throw new RuntimeException('Migration lock must not be a symlink.');
        }
        $lock = fopen($lockPath, 'c');
        if ($lock === false) {
            throw new RuntimeException('Cannot open migration lock.');
        }
        try {
            if (!flock($lock, LOCK_EX | LOCK_NB)) {
                throw new RuntimeException('Another migration is running.');
            }
            $action();
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function saveStatus(array $status): void
    {
        $json = json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";
        $temporary = tempnam(dirname($this->statusFile), '.setup-status-');
        if ($temporary === false) {
            throw new RuntimeException('Cannot create temporary migration status.');
        }
        try {
            if (file_put_contents($temporary, $json, LOCK_EX) !== strlen($json)) {
                throw new RuntimeException('Cannot write migration status.');
            }
            if (!chmod($temporary, 0644)) {
                throw new RuntimeException('Cannot set migration status permissions.');
            }
            if (!rename($temporary, $this->statusFile)) {
                throw new RuntimeException('Cannot replace migration status.');
            }
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
    }
}
