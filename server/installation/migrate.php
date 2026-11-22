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

use Micro\OpenvpnWebadmin\Installation\Helpers\MigrationRunner;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/helpers/MigrationRunner.php';

$root = dirname(__DIR__, 2);
$mode = $argv[1] ?? '';
$version = $argv[2] ?? '';
$statusFile = $argv[3] ?? ($root . '/storage/updates/setup-status.json');
$migrationsDirectory = $argv[4] ?? (__DIR__ . '/migrations');

try {
    $runner = new MigrationRunner($statusFile, $migrationsDirectory);
    if ($mode === '--init') {
        $runner->initialize($version);
        echo "Initialized migration status at version {$version}.\n";
    } elseif ($mode === '--check') {
        $runner->validateTarget($version);
        echo "Migration status is compatible with {$version}.\n";
    } elseif ($mode === '--run') {
        $connection = null;
        $runner->run($version, static function (string $sql, string $file) use ($root, &$connection): void {
            if ($connection === null) {
                $config = require $root . '/config/config.php';
                $db = $config['db'];
                $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']};charset={$db['charset']}";
                $connection = new PDO($dsn, $db['user'], $db['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
            }
            $statement = $connection->query($sql);
            if ($statement === false) {
                throw new RuntimeException('Migration failed: ' . $file);
            }
            do {
                if ($statement->columnCount() > 0) {
                    $statement->fetchAll(PDO::FETCH_ASSOC);
                }
            } while ($statement->nextRowset());
            $statement->closeCursor();
        });
        echo "Migration status updated to {$version}.\n";
    } else {
        throw new RuntimeException('Usage: php migrate.php --init|--check|--run X.Y.Z [status-file] [migrations-directory]');
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Migration error: ' . $e->getMessage() . "\n");
    exit(1);
}
