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

use Micro\OpenvpnWebadmin\Util\Lang;
use Micro\OpenvpnWebadmin\Core\Session;
use Micro\OpenvpnWebadmin\Core\Database;
use Micro\OpenvpnWebadmin\Core\Debug;
use PDOException;

class LoginController
{
/**
 * Stellt login fuer die Ausgabe dar.
 *
 * @return void Kein Rueckgabewert.
 */
public function showLogin(): void
    {
        $lang = Lang::getAll();

        $themeLoginFile = (defined('_LOGIN_THEME_DIR') ? _LOGIN_THEME_DIR : dirname(__DIR__, 2) . '/html/login1') . '/login.php';
        if (is_file($themeLoginFile)) {
            include $themeLoginFile;
            return;
        }

        include __DIR__ . '/../Templates/Login/login.php';
    }

/**
 * Verarbeitet login entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
public function handleLogin(): void
    {
        $db = Database::getInstance()->getConnection();
        $user = null;

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $csrf = (string)($_POST['_csrf'] ?? '');
        if (!Session::verifyCsrfToken($csrf)) {
            Debug::log('LOGIN blocked invalid csrf');
            $this->redirect(\Url::op('login', ['error' => 'invalid']));
        }

        if ($this->isLoginRateLimited($username)) {
            Debug::log('LOGIN blocked rate limit', ['user_ref' => $this->auditUserRef($username), 'ip' => $this->clientIp()]);
            $this->redirect(\Url::op('login', ['error' => 'invalid']));
        }
        if (empty($username) || empty($password)) {
            Debug::log('LOGIN empty credentials', ['user_ref' => $this->auditUserRef($username), 'ip' => $this->clientIp()]);
            $this->registerLoginFailure($username);
            $this->redirect(\Url::op('login', ['error' => 'empty']));
        }

        try {
            $stmt = $db->prepare("
                SELECT u.*, g.name AS role_name
                FROM user u
                LEFT JOIN groupnames g ON u.gid = g.gid
                WHERE u.user_name = :username
                LIMIT 1
            ");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();
        } catch (PDOException $e) {
            Debug::logException($e, 'Login JOIN fallback');
            try {
                $stmt = $db->prepare("SELECT * FROM user WHERE user_name = :username LIMIT 1");
                $stmt->execute(['username' => $username]);
                $user = $stmt->fetch();
                if ($user) {
                    $user['role_name'] = '';
                }
            } catch (\Throwable $inner) {
                Debug::logException($inner, 'Login fallback query failed');
                Debug::log('LOGIN failed database query', ['user_ref' => $this->auditUserRef($username), 'ip' => $this->clientIp()]);
                $this->registerLoginFailure($username);
                $this->redirect(\Url::op('login', ['error' => 'invalid']));
            }
        }

        $validPassword = false;
        if ($user) {
            $storedHash = (string)($user['user_pass'] ?? '');
            $validPassword = password_verify($password, $storedHash);

            // Legacy-Fallback: Klartext / md5 / sha1; bei Erfolg direkt auf password_hash migrieren.
            if (!$validPassword) {
                $legacyMatch = hash_equals($storedHash, $password)
                    || hash_equals($storedHash, md5($password))
                    || hash_equals($storedHash, sha1($password));
                if ($legacyMatch) {
                    $validPassword = true;
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $db->prepare("UPDATE user SET user_pass = :pass WHERE uid = :uid");
                    $upd->execute([
                        'pass' => $newHash,
                        'uid' => $user['uid'],
                    ]);
                }
            }
        }

        if ($user && $validPassword) {
            $this->clearLoginFailures($username);
            Session::regenerateId();
            $roleName = (string)($user['role_name'] ?? '');
            $isAdmin = ((int)($user['gid'] ?? 0) === 1) || $this->isStrictAdminRoleName($roleName);
            Session::setVar('user', [
                'uid'  => $user['uid'],
                'name' => $user['user_name'],
                'gid'  => $user['gid'],
                'role' => $roleName,
                'is_admin' => $isAdmin
            ]);
            session_write_close();
            $this->redirect(\Url::op('dashboard'));
        } else {
            $this->registerLoginFailure($username);
            Debug::log('LOGIN failed', ['user_ref' => $this->auditUserRef($username), 'user_found' => (bool)$user, 'password_ok' => $validPassword, 'ip' => $this->clientIp()]);
            $this->redirect(\Url::op('login', ['error' => 'invalid']));
        }
    }

/**
 * Sendet einen Location-Header mit normalisierter interner URL und beendet die Anfrage.
 *
 * @param string $url Internes Redirectziel.
 * @return void Kein Rueckgabewert.
 */
private function redirect(string $url): void
    {
        header('Location: ' . $this->buildRedirectUrl($url));
        exit;
    }

    /**
     * Normalisiert ein internes Ziel mittels `Url::normalizeInternal()`.
     *
     * @param string $url Legacy-Query, Pfad oder absolute URL.
     * @return string Normalisiertes Redirectziel.
     */
    private function buildRedirectUrl(string $url): string
    {
        return Url::normalizeInternal($url);
    }

/**
 * Fuehrt client ip entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
private function clientIp(): string
    {
        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
        return $ip !== '' ? $ip : 'unknown';
    }

/**
 * Erzeugt für Logs eine nicht umkehrbare Referenz aus dem normalisierten Benutzernamen.
 *
 * @param string $username Zu anonymisierender Benutzername.
 * @return string `empty` oder SHA-256-Präfix samt Hash.
 */
private function auditUserRef(string $username): string
    {
        $u = strtolower(trim($username));
        if ($u === '') {
            return 'empty';
        }
        return 'sha256:' . hash('sha256', $u);
    }

/**
 * Bildet den Rate-Limit-Schlüssel aus normalisiertem Benutzernamen und Client-IP.
 *
 * @param string $username Loginname.
 * @return string SHA-256-Schlüssel für den datenbankweiten Rate-Limit-Eintrag.
 */
private function loginKey(string $username): string
    {
        return hash('sha256', strtolower(trim($username)) . '|' . $this->clientIp());
    }

/**
 * Prüft, ob für Benutzername und Client-IP eine noch aktive datenbankweite Sperre besteht.
 *
 * @param string $username Loginname.
 * @return bool True bei Erfolg, sonst false.
 */
private function isLoginRateLimited(string $username): bool
    {
        $stmt = Database::getInstance()->getConnection()->prepare(
            'SELECT locked_until FROM login_attempts WHERE attempt_key = :attempt_key'
        );
        $stmt->execute(['attempt_key' => $this->loginKey($username)]);
        $row = $stmt->fetch();

        return is_array($row) && (int)$row['locked_until'] > time();
    }

/**
 * Zählt einen Fehlversuch datenbankweit; ab fünf Versuchen innerhalb zehn Minuten sperrt es fünf Minuten und verzögert 300 ms.
 *
 * @param string $username Loginname für den Rate-Limit-Schlüssel.
 * @return void Kein Rueckgabewert.
 */
private function registerLoginFailure(string $username): void
    {
        $now = time();
        $cutoff = $now - 600;
        $stmt = Database::getInstance()->getConnection()->prepare(
            'INSERT INTO login_attempts (attempt_key, attempts, window_started_at, locked_until, updated_at)
             VALUES (?, 1, ?, 0, ?)
             ON DUPLICATE KEY UPDATE
                locked_until = IF(window_started_at <= ?, 0, IF(attempts + 1 >= 5, ?, locked_until)),
                attempts = IF(window_started_at <= ?, 1, attempts + 1),
                window_started_at = IF(window_started_at <= ?, ?, window_started_at),
                updated_at = ?'
        );
        $stmt->execute([
            $this->loginKey($username),
            $now,
            $now,
            $cutoff,
            $now + 300,
            $cutoff,
            $cutoff,
            $now,
            $now,
        ]);

        $cleanup = Database::getInstance()->getConnection()->prepare(
            'DELETE FROM login_attempts WHERE updated_at < ?'
        );
        $cleanup->execute([$now - 86400]);
        usleep(300000);
    }

/**
 * Entfernt den Rate-Limit-Eintrag dieses Benutzers und der Client-IP.
 *
 * @param string $username Erfolgreich angemeldeter Benutzername.
 * @return void Kein Rueckgabewert.
 */
private function clearLoginFailures(string $username): void
    {
        $stmt = Database::getInstance()->getConnection()->prepare(
            'DELETE FROM login_attempts WHERE attempt_key = :attempt_key'
        );
        $stmt->execute(['attempt_key' => $this->loginKey($username)]);
    }

/**
 * Erkennt ausschließlich die erlaubten Admin-Rollennamen nach Trim und Kleinschreibung.
 *
 * @param string $roleName Rollenname aus der Datenbank.
 * @return bool True bei Erfolg, sonst false.
 */
private function isStrictAdminRoleName(string $roleName): bool
    {
        $role = strtolower(trim($roleName));
        if ($role === '') {
            return false;
        }

        // Only explicit admin role names, no substring matches like "webadmin-user".
        return in_array($role, [
            'admin',
            'admins',
            'administrator',
            'administrators',
            'administratoren',
        ], true);
    }
}
