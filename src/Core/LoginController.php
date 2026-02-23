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

use Micro\OpenvpnWebadmin\Core\Lang;
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
        Debug::log('LOGIN attempt', ['user_ref' => $this->auditUserRef($username), 'ip' => $this->clientIp()]);

        if (empty($username) || empty($password)) {
            Debug::log("Leere Logindaten");
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
            Debug::log('LOGIN user lookup (join)', ['user_ref' => $this->auditUserRef($username), 'found' => (bool)$user]);
        } catch (PDOException $e) {
            Debug::log('Login JOIN fallback', $e->getMessage());
            try {
                $stmt = $db->prepare("SELECT * FROM user WHERE user_name = :username LIMIT 1");
                $stmt->execute(['username' => $username]);
                $user = $stmt->fetch();
                if ($user) {
                    $user['role_name'] = '';
                }
                Debug::log('LOGIN user lookup (fallback)', ['user_ref' => $this->auditUserRef($username), 'found' => (bool)$user]);
            } catch (\Throwable $inner) {
                Debug::log('Login fallback query failed', $inner->getMessage());
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
                    Debug::log('LOGIN legacy password migrated', ['user_ref' => $this->auditUserRef($username), 'uid' => (int)$user['uid']]);
                }
            }
        }

        if ($user && $validPassword) {
            $this->clearLoginFailures($username);
            Session::regenerateId();
            $roleName = (string)($user['role_name'] ?? '');
            $isAdmin = $this->isStrictAdminRoleName($roleName);
            Debug::log('LOGIN session before set', [
                'session_id' => session_id(),
                'session_status' => session_status(),
            ]);
            Session::setVar('user', [
                'uid'  => $user['uid'],
                'name' => $user['user_name'],
                'gid'  => $user['gid'],
                'role' => $roleName,
                'is_admin' => $isAdmin
            ]);
            session_write_close();
            Debug::log('LOGIN success', ['user_ref' => $this->auditUserRef($username), 'uid' => (int)$user['uid'], 'is_admin' => $isAdmin]);
            $this->redirect(\Url::op('dashboard'));
        } else {
            $this->registerLoginFailure($username);
            Debug::log('LOGIN failed', ['user_ref' => $this->auditUserRef($username), 'user_found' => (bool)$user, 'password_ok' => $validPassword, 'ip' => $this->clientIp()]);
            $this->redirect(\Url::op('login', ['error' => 'invalid']));
        }
    }

/**
 * Fuehrt redirect entsprechend der internen Logik aus.
 *
 * @param mixed $url Eingabewert fuer url.
 * @return void Kein Rueckgabewert.
 */
private function redirect(string $url): void
    {
        header('Location: ' . $this->buildRedirectUrl($url));
        exit;
    }

    /**
     * Baut eine absolute Redirect-URL aus Legacy-?op Links.
     *
     * @param string $url
     * @return string
     */
    private function buildRedirectUrl(string $url): string
    {
        if (str_starts_with($url, '?op=')) {
            parse_str(ltrim($url, '?'), $params);
            $op = (string)($params['op'] ?? 'main');
            unset($params['op']);

            if (defined('_URL_REWRITE') && _URL_REWRITE === true) {
                if ($op === 'dashboard' || $op === 'main') {
                    $query = $params === [] ? '' : '?' . http_build_query($params);
                    return '/' . ltrim($query, '/');
                }

                if ($op === 'setlang' && isset($params['lang']) && is_string($params['lang']) && $params['lang'] !== '') {
                    $lang = rawurlencode($params['lang']);
                    unset($params['lang']);
                    $query = $params === [] ? '' : '?' . http_build_query($params);
                    return '/setlang/' . $lang . $query;
                }

                $query = $params === [] ? '' : '?' . http_build_query($params);
                return '/' . rawurlencode($op) . $query;
            }

            $query = http_build_query(array_merge(['op' => $op], $params));
            return '/?' . $query;
        }

        if (str_starts_with($url, '?')) {
            return '/?' . ltrim($url, '?');
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        return '/' . ltrim($url, '/');
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
 * Fuehrt audit user ref entsprechend der internen Logik aus.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return string Rueckgabe als Text.
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
 * Fuehrt login key entsprechend der internen Logik aus.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return string Rueckgabe als Text.
 */
private function loginKey(string $username): string
    {
        return hash('sha256', strtolower(trim($username)) . '|' . $this->clientIp());
    }

/**
 * Prueft, ob login rate limited zutrifft.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return bool True bei Erfolg, sonst false.
 */
private function isLoginRateLimited(string $username): bool
    {
        $store = $_SESSION['_login_guard'] ?? [];
        if (!is_array($store)) {
            return false;
        }

        $key = $this->loginKey($username);
        $row = $store[$key] ?? null;
        if (!is_array($row)) {
            return false;
        }

        $lockUntil = (int)($row['lock_until'] ?? 0);
        return $lockUntil > time();
    }

/**
 * Fuehrt register login failure entsprechend der internen Logik aus.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return void Kein Rueckgabewert.
 */
private function registerLoginFailure(string $username): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $key = $this->loginKey($username);
        $store = $_SESSION['_login_guard'] ?? [];
        if (!is_array($store)) {
            $store = [];
        }

        $now = time();
        $row = $store[$key] ?? ['count' => 0, 'first' => $now, 'lock_until' => 0];
        $count = (int)($row['count'] ?? 0);
        $first = (int)($row['first'] ?? $now);
        if (($now - $first) > 600) {
            $count = 0;
            $first = $now;
        }
        $count++;

        $lockUntil = 0;
        if ($count >= 5) {
            $lockUntil = $now + 300;
        }

        $store[$key] = [
            'count' => $count,
            'first' => $first,
            'lock_until' => $lockUntil,
        ];
        $_SESSION['_login_guard'] = $store;
        usleep(300000);
    }

/**
 * Fuehrt clear login failures entsprechend der internen Logik aus.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return void Kein Rueckgabewert.
 */
private function clearLoginFailures(string $username): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $key = $this->loginKey($username);
        if (isset($_SESSION['_login_guard']) && is_array($_SESSION['_login_guard'])) {
            unset($_SESSION['_login_guard'][$key]);
        }
    }

/**
 * Prueft, ob strict admin role name zutrifft.
 *
 * @param mixed $roleName Eingabewert fuer roleName.
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
