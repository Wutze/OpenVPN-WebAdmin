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

use PDO;
use SessionHandlerInterface;
use Throwable;

/**
Session-Handler mit PDO-Backend für Tabelle `sessions2`
Kompatibel mit Struktur:
  sesskey   varchar(64)
  expiry    datetime
  expireref varchar(250)
  created   datetime
  modified  datetime
  sessdata  longtext
*/
class Session implements SessionHandlerInterface
{
private PDO $db;
private string $table = 'sessions2';
private int $lifetime;

/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $db Eingabewert fuer db.
 * @param mixed $lifetime Eingabewert fuer lifetime.
 * @return mixed|null Rueckgabewert der Funktion.
 */
public function __construct(PDO $db, int $lifetime = 1440)
    {
        $this->db = $db;
        $this->lifetime = $lifetime;

        session_set_save_handler($this, true);

        // Sichere Cookie-Parameter
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'domain'   => '',
            'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    // ---------------------------------------------------------------------
    // SessionHandlerInterface Implementierung
    // ---------------------------------------------------------------------

/**
 * Fuehrt open entsprechend der internen Logik aus.
 *
 * @param mixed $savePath Eingabewert fuer savePath.
 * @param mixed $sessionName Eingabewert fuer sessionName.
 * @return bool True bei Erfolg, sonst false.
 */
public function open($savePath, $sessionName): bool
    {
        return true;
    }

/**
 * Fuehrt close entsprechend der internen Logik aus.
 *
 * @return bool True bei Erfolg, sonst false.
 */
public function close(): bool
    {
        return true;
    }

/**
 * Laedt Daten aus der Quelle.
 *
 * @param mixed $id Eingabewert fuer id.
 * @return string|false Rueckgabewert der Funktion.
 */
public function read($id): string|false
    {
        try {
            $sql = "SELECT sessdata
                    FROM {$this->table}
                    WHERE sesskey = :id
                      AND expiry > NOW()
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $data = $stmt->fetchColumn();

            return $data ?: '';
        } catch (Throwable $e) {
            if (class_exists(Debug::class)) {
                Debug::log('Session read fallback', $e->getMessage());
            }
            return '';
        }
    }

/**
 * Schreibt Daten in das Zielsystem.
 *
 * @param mixed $id Eingabewert fuer id.
 * @param mixed $data Eingabewert fuer data.
 * @return bool True bei Erfolg, sonst false.
 */
public function write($id, $data): bool
    {
        try {
            $expiry = date('Y-m-d H:i:s', time() + $this->lifetime);
            $now    = date('Y-m-d H:i:s');

            $sql = "REPLACE INTO {$this->table}
                    (sesskey, expiry, expireref, created, modified, sessdata)
                    VALUES (:id, :expiry, '', :created, :modified, :data)";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':id'       => $id,
                ':expiry'   => $expiry,
                ':created'  => $now,
                ':modified' => $now,
                ':data'     => $data
            ]);
        } catch (Throwable $e) {
            if (class_exists(Debug::class)) {
                Debug::log('Session write fallback', $e->getMessage());
            }
            return false;
        }
    }

/**
 * Fuehrt destroy entsprechend der internen Logik aus.
 *
 * @param mixed $id Eingabewert fuer id.
 * @return bool True bei Erfolg, sonst false.
 */
public function destroy($id): bool
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE sesskey = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (Throwable $e) {
            if (class_exists(Debug::class)) {
                Debug::log('Session destroy fallback', $e->getMessage());
            }
            return true;
        }
    }

/**
 * Fuehrt gc entsprechend der internen Logik aus.
 *
 * @param mixed $max_lifetime Eingabewert fuer max_lifetime.
 * @return int|false Rueckgabewert der Funktion.
 */
public function gc($max_lifetime): int|false
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE expiry < NOW()";
            return $this->db->exec($sql);
        } catch (Throwable $e) {
            if (class_exists(Debug::class)) {
                Debug::log('Session gc fallback', $e->getMessage());
            }
            return 0;
        }
    }

    // ---------------------------------------------------------------------
    // Statische Hilfsmethoden
    // ---------------------------------------------------------------------

    /**
     * Initialisiert und startet die Session.
     */
    public static function start(PDO $db, int $lifetime = 1440): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', '1');
            // Native PHP session as default for reliable login persistence.
            // The PDO-based session handler can be re-enabled later if needed.
            session_set_save_handler(new \SessionHandler(), true);
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path'     => '/',
                'domain'   => '',
                'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();

            if (class_exists(Debug::class)) {
                Debug::log('Session start', [
                    'driver' => 'native',
                    'session_id' => session_id(),
                    'cookie_name' => session_name(),
                ]);
            }
        }
    }

/**
 * Fuehrt regenerate id entsprechend der internen Logik aus.
 *
 * @return void Kein Rueckgabewert.
 */
public static function regenerateId(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Setzt eine Session-Variable.
     */
    public static function setVar(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Holt eine Session-Variable.
     */
    public static function getVar(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Entfernt eine Session-Variable.
     */
    public static function removeVar(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Prüft, ob ein Benutzer angemeldet ist.
     */
    public static function isUser(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Prüft, ob aktueller Benutzer Admin ist.
     */
    public static function isAdmin(): bool
    {
        $user = self::getUser();
        if (!$user) {
            return false;
        }

        $uid = (int)($user['uid'] ?? 0);
        if ($uid > 0) {
            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare(
                    'SELECT u.gid, COALESCE(g.name, \'\') AS role_name
                     FROM user u
                     LEFT JOIN groupnames g ON u.gid = g.gid
                     WHERE u.uid = :uid
                     LIMIT 1'
                );
                $stmt->execute([':uid' => $uid]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (is_array($row)) {
                    $gid = (int)($row['gid'] ?? 0);
                    $roleName = (string)($row['role_name'] ?? '');
                    $isAdmin = self::isStrictAdminRoleName($roleName);

                    // Keep session in sync with authoritative DB role.
                    $_SESSION['user']['gid'] = $gid;
                    $_SESSION['user']['role'] = $roleName;
                    $_SESSION['user']['is_admin'] = $isAdmin;

                    return $isAdmin;
                }
            } catch (\Throwable $e) {
                if (class_exists(Debug::class)) {
                    Debug::log('Session::isAdmin DB check failed', $e->getMessage());
                }
            }
        }

        if (array_key_exists('is_admin', $user)) {
            return self::toBool($user['is_admin']);
        }

        return false;
    }

    /**
     * Gibt den aktuellen Benutzer zurück.
     */
    public static function getUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Setzt den angemeldeten Benutzer.
     */
    public static function setUser(array $user): void
    {
        $_SESSION['user'] = $user;
    }

    /**
     * Zerstört die komplette Session inkl. Cookie.
     */
    public static function destroyAll(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params['path'], $params['domain'],
                    $params['secure'], $params['httponly']
                );
            }

            session_destroy();
        }
    }

    /**
     * CSRF token for state-changing requests.
     */
    public static function getCsrfToken(): string
    {
        if (!isset($_SESSION['_csrf_token']) || !is_string($_SESSION['_csrf_token']) || $_SESSION['_csrf_token'] === '') {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    /**
     * Fuehrt verify csrf token entsprechend der internen Logik aus.
     *
     * @param mixed $token Eingabewert fuer token.
     * @return bool True bei Erfolg, sonst false.
     */
    public static function verifyCsrfToken(?string $token): bool
    {
        $sessionToken = $_SESSION['_csrf_token'] ?? '';
        if (!is_string($sessionToken) || $sessionToken === '') {
            return false;
        }

        if (!is_string($token) || $token === '') {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

/**
 * Fuehrt to bool entsprechend der internen Logik aus.
 *
 * @param mixed $value Eingabewert fuer value.
 * @return bool True bei Erfolg, sonst false.
 */
private static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return ((int)$value) === 1;
        }
        if (is_string($value)) {
            $v = strtolower(trim($value));
            return in_array($v, ['1', 'true', 'yes', 'on'], true);
        }

        return false;
    }

/**
 * Prueft, ob strict admin role name zutrifft.
 *
 * @param mixed $roleName Eingabewert fuer roleName.
 * @return bool True bei Erfolg, sonst false.
 */
private static function isStrictAdminRoleName(string $roleName): bool
    {
        $role = strtolower(trim($roleName));
        if ($role === '') {
            return false;
        }

        return in_array($role, [
            'admin',
            'admins',
            'administrator',
            'administrators',
            'administratoren',
        ], true);
    }
}
