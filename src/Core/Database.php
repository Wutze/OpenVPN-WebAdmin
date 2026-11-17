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
 * @version     2.0.0
 */

namespace Micro\OpenvpnWebadmin\Core;

use PDO;
use PDOException;

class Database
{
private static ?Database $instance = null;
private PDO $connection;

/**
 * Baut eine PDO-Verbindung mit MySQL-DSN und Exceptions als Fehlerbetrieb auf.
 *
 * @param array{host: string, port: int|string, dbname: string, charset: string, user: string, pass: string} $config Verbindungsparameter.
 * @throws \RuntimeException Es wird bei Verbindungsfehlern stattdessen protokolliert, HTTP 500 gesetzt und das Skript mit `die` beendet.
 */
private function __construct(array $config)
    {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
            $this->connection = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('DB connection failed: ' . $e->getMessage());
            http_response_code(500);
            die('Interner Datenbankfehler.');
        }
    }

/**
 * Liefert die Singleton-Verbindung und initialisiert sie beim ersten Aufruf.
 *
 * @param array|null $config Zugangsdaten; beim ersten Aufruf erforderlich, später ignoriert.
 * @return self Gemeinsame Database-Instanz.
 * @throws \RuntimeException Wenn die Erstinitialisierung ohne Konfiguration erfolgt.
 */
public static function getInstance(array $config = null): Database
    {
        if (self::$instance === null) {
            if ($config === null) {
                throw new \RuntimeException("Database config required for first initialization.");
            }
            self::$instance = new self($config);
        }
        return self::$instance;
    }

/**
 * Gibt die von PDO verwaltete Datenbankverbindung zurück.
 *
 * @return PDO PDO-Verbindung.
 */
public function getConnection(): PDO
    {
        return $this->connection;
    }
}
