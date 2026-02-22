<?php
/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020/2025 OpenVPN-WebAdmin
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
 * @version     2.0.0
 */

namespace Micro\OpenvpnWebadmin\Core;

class ProfileService
{
    private string $basePath;

    /**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $basePath
     * @return mixed|null
     */
public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? dirname(__DIR__, 2) . '/storage/conf';
    }

    /**
     * Kurzbeschreibung Funktion listSystems
     *
     * @return array
     */
public function listSystems(): array
    {
        if (!is_dir($this->basePath)) {
            return [];
        }

        $items = scandir($this->basePath) ?: [];
        $systems = [];

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $dir = $this->basePath . '/' . $item;
            if (!$this->isAllowedSystemDir($item, $dir)) {
                continue;
            }

            $fileList = [];
            foreach (scandir($dir) ?: [] as $name) {
                if ($name === '.' || $name === '..') {
                    continue;
                }
                $full = $dir . '/' . $name;
                if (is_file($full)) {
                    $fileList[] = $name;
                }
            }

            $zipPath = $this->zipPathFor($item);
            $systems[] = [
                'system' => $item,
                'file_count' => count($fileList),
                'files' => $fileList,
                'zip_exists' => is_file($zipPath),
                'zip_file' => basename($zipPath),
                'zip_mtime' => is_file($zipPath) ? date('Y-m-d H:i:s', (int)filemtime($zipPath)) : null,
            ];
        }

        usort($systems, static fn(array $a, array $b): int => strcmp($a['system'], $b['system']));
        return $systems;
    }

    /**
     * Kurzbeschreibung Funktion buildZip
     *
     * @param mixed $system
     * @return string
     */
public function buildZip(string $system): string
    {
        $system = $this->sanitizeSystem($system);
        $sourceDir = $this->basePath . '/' . $system;

        if (!$this->isAllowedSystemDir($system, $sourceDir)) {
            throw new \RuntimeException('Unbekanntes System: ' . $system);
        }

        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('ZipArchive ist nicht verfuegbar.');
        }

        $zipPath = $this->zipPathFor($system);
        $zip = new \ZipArchive();
        $openResult = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($openResult !== true) {
            throw new \RuntimeException('ZIP-Datei konnte nicht erstellt werden. Fehlercode: ' . $openResult);
        }

        $entries = scandir($sourceDir) ?: [];
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $fullPath = $sourceDir . '/' . $entry;
            if (is_file($fullPath)) {
                $zip->addFile($fullPath, $entry);
            }
        }

        $zip->close();
        return $zipPath;
    }

    /**
     * Kurzbeschreibung Funktion getZipPath
     *
     * @param mixed $system
     * @return string
     */
public function getZipPath(string $system): string
    {
        $system = $this->sanitizeSystem($system);
        $zipPath = $this->zipPathFor($system);

        if (!is_file($zipPath)) {
            return $this->buildZip($system);
        }

        return $zipPath;
    }

    /**
     * Kurzbeschreibung Funktion sanitizeSystem
     *
     * @param mixed $system
     * @return string
     */
private function sanitizeSystem(string $system): string
    {
        $system = trim($system);
        if ($system === '' || !preg_match('/^[a-zA-Z0-9._-]+$/', $system)) {
            throw new \RuntimeException('Ungueltiger Systemname.');
        }

        return $system;
    }

    /**
     * Kurzbeschreibung Funktion zipPathFor
     *
     * @param mixed $system
     * @return string
     */
private function zipPathFor(string $system): string
    {
        return $this->basePath . '/' . $system . '.zip';
    }

    /**
     * Kurzbeschreibung Funktion isAllowedSystemDir
     *
     * @param mixed $system
     * @param mixed $dir
     * @return bool
     */
private function isAllowedSystemDir(string $system, string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        if (in_array($system, ['server', 'history'], true)) {
            return false;
        }

        return is_file($dir . '/client.ovpn');
    }
}
