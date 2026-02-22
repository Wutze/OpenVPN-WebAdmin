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

class UserModel
{
private PDO $db;

/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @return mixed|null Rueckgabewert der Funktion.
 */
public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

/**
 * Liest all users und gibt den Wert zurueck.
 *
 * @param mixed $limit Eingabewert fuer limit.
 * @param mixed $offset Eingabewert fuer offset.
 * @param mixed $search Eingabewert fuer search.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
public function getAllUsers(int $limit = 50, int $offset = 0, string $search = ''): array
    {
        $sql = "
            SELECT
                u.uid,
                u.user_name AS uname,
                COALESCE(g.name, '') AS gname,
                u.gid,
                u.user_enable,
                u.user_start_date,
                u.user_end_date,
                u.user_online,
                ui.fixed_ip
            FROM user u
            LEFT JOIN groupnames g ON u.gid = g.gid
            LEFT JOIN (
                SELECT uid, MIN(user_ip) AS fixed_ip
                FROM user_ip
                GROUP BY uid
            ) ui ON ui.uid = u.uid
            WHERE 1
        ";

        $params = [];
        if ($search !== '') {
            $sql .= " AND (u.user_name LIKE :search OR g.name LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY u.user_name ASC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

/**
 * Fuehrt count users entsprechend der internen Logik aus.
 *
 * @param mixed $search Eingabewert fuer search.
 * @return int Rueckgabewert der Funktion.
 */
public function countUsers(string $search = ''): int
    {
        $sql = 'SELECT COUNT(*) FROM user u LEFT JOIN groupnames g ON u.gid = g.gid WHERE 1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (u.user_name LIKE :search OR g.name LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

/**
 * Fuehrt count online users entsprechend der internen Logik aus.
 *
 * @return int Rueckgabewert der Funktion.
 */
public function countOnlineUsers(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM user WHERE user_online = 1');
        return (int)$stmt->fetchColumn();
    }

/**
 * Fuehrt user exists entsprechend der internen Logik aus.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return bool True bei Erfolg, sonst false.
 */
public function userExists(string $username): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM user WHERE user_name = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        return (bool)$stmt->fetchColumn();
    }

/**
 * Erzeugt user auf Basis der Eingabedaten.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $password Eingabewert fuer password.
 * @param mixed $isAdmin Eingabewert fuer isAdmin.
 * @return void Kein Rueckgabewert.
 */
public function createUser(string $username, string $password, bool $isAdmin = false): void
    {
        $gid = $this->resolveGroupId($isAdmin);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            'INSERT INTO user (user_name, user_pass, gid, user_enable, user_start_date, user_end_date, user_online)
             VALUES (:username, :password, :gid, 1, CURDATE(), NULL, 0)'
        );

        $stmt->execute([
            ':username' => $username,
            ':password' => $hash,
            ':gid' => $gid,
        ]);
    }

/**
 * Setzt user enabled auf den uebergebenen Wert.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $enabled Eingabewert fuer enabled.
 * @return void Kein Rueckgabewert.
 */
public function setUserEnabled(string $username, bool $enabled): void
    {
        $stmt = $this->db->prepare('UPDATE user SET user_enable = :enabled WHERE user_name = :username');
        $stmt->execute([
            ':enabled' => $enabled ? 1 : 0,
            ':username' => $username,
        ]);
    }

/**
 * Setzt user role auf den uebergebenen Wert.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $isAdmin Eingabewert fuer isAdmin.
 * @return void Kein Rueckgabewert.
 */
public function setUserRole(string $username, bool $isAdmin): void
    {
        $gid = $this->resolveGroupId($isAdmin);
        $stmt = $this->db->prepare('UPDATE user SET gid = :gid WHERE user_name = :username');
        $stmt->execute([
            ':gid' => $gid,
            ':username' => $username,
        ]);
    }

/**
 * Setzt user password by name auf den uebergebenen Wert.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $newPassword Eingabewert fuer newPassword.
 * @return void Kein Rueckgabewert.
 */
public function setUserPasswordByName(string $username, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('UPDATE user SET user_pass = :password WHERE user_name = :username');
        $stmt->execute([
            ':password' => $hash,
            ':username' => $username,
        ]);
    }

/**
 * Setzt user password by id auf den uebergebenen Wert.
 *
 * @param mixed $uid Eingabewert fuer uid.
 * @param mixed $newPassword Eingabewert fuer newPassword.
 * @return void Kein Rueckgabewert.
 */
public function setUserPasswordById(int $uid, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('UPDATE user SET user_pass = :password WHERE uid = :uid');
        $stmt->execute([
            ':password' => $hash,
            ':uid' => $uid,
        ]);
    }

    /**
     * Setzt user limits auf den uebergebenen Wert.
     *
     * @param mixed $username Eingabewert fuer username.
     * @param mixed $startDate Eingabewert fuer startDate.
     * @param mixed $endDate Eingabewert fuer endDate.
     * @return void Kein Rueckgabewert.
     */
    public function setUserLimits(string $username, ?string $startDate, ?string $endDate): void
    {
        $startDate = $startDate !== null && $startDate !== '' ? $startDate : null;
        $endDate = $endDate !== null && $endDate !== '' ? $endDate : null;

        $stmt = $this->db->prepare('
            UPDATE user
            SET user_start_date = :start_date,
                user_end_date = :end_date
            WHERE user_name = :username
        ');
        $stmt->bindValue(':start_date', $startDate, $startDate === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $endDate, $endDate === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
    }

/**
 * Setzt user fixed ip auf den uebergebenen Wert.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $fixedIp Eingabewert fuer fixedIp.
 * @return void Kein Rueckgabewert.
 */
public function setUserFixedIp(string $username, ?string $fixedIp): void
    {
        $stmt = $this->db->prepare('SELECT uid FROM user WHERE user_name = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $uid = (int)$stmt->fetchColumn();
        if ($uid <= 0) {
            return;
        }

        $existing = $this->db->prepare('SELECT netmask, server_ip FROM user_ip WHERE uid = :uid ORDER BY ipid ASC LIMIT 1');
        $existing->execute([':uid' => $uid]);
        $current = $existing->fetch(PDO::FETCH_ASSOC) ?: [];

        if ($fixedIp === null || $fixedIp === '') {
            $delete = $this->db->prepare('DELETE FROM user_ip WHERE uid = :uid');
            $delete->execute([':uid' => $uid]);
            return;
        }

        $netmask = (string)($current['netmask'] ?? '');
        if ($netmask === '') {
            $netmask = (string)(getenv('OVPN_USER_NETMASK') ?: '255.255.255.0');
        }

        $serverIp = (string)($current['server_ip'] ?? '');
        if ($serverIp === '') {
            $serverIp = (string)(getenv('OVPN_USER_SERVER_IP') ?: '10.8.0.1');
        }

        $delete = $this->db->prepare('DELETE FROM user_ip WHERE uid = :uid');
        $delete->execute([':uid' => $uid]);

        $insert = $this->db->prepare(
            'INSERT INTO user_ip (uid, user_ip, netmask, server_ip) VALUES (:uid, :user_ip, :netmask, :server_ip)'
        );
        $insert->execute([
            ':uid' => $uid,
            ':user_ip' => $fixedIp,
            ':netmask' => $netmask,
            ':server_ip' => $serverIp,
        ]);
    }

/**
 * Prueft, ob fixed ip in use by other user zutrifft.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $fixedIp Eingabewert fuer fixedIp.
 * @return bool True bei Erfolg, sonst false.
 */
public function isFixedIpInUseByOtherUser(string $username, string $fixedIp): bool
    {
        $stmt = $this->db->prepare('
            SELECT 1
            FROM user_ip ui
            INNER JOIN user u ON u.uid = ui.uid
            WHERE ui.user_ip = :fixed_ip
              AND u.user_name <> :username
            LIMIT 1
        ');
        $stmt->execute([
            ':fixed_ip' => $fixedIp,
            ':username' => $username,
        ]);

        return (bool)$stmt->fetchColumn();
    }

/**
 * Entfernt user.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return void Kein Rueckgabewert.
 */
public function deleteUser(string $username): void
    {
        $stmt = $this->db->prepare('DELETE FROM user WHERE user_name = :username');
        $stmt->execute([':username' => $username]);
    }

/**
 * Fuehrt verify password entsprechend der internen Logik aus.
 *
 * @param mixed $uid Eingabewert fuer uid.
 * @param mixed $password Eingabewert fuer password.
 * @return bool True bei Erfolg, sonst false.
 */
public function verifyPassword(int $uid, string $password): bool
    {
        $stmt = $this->db->prepare('SELECT user_pass FROM user WHERE uid = :uid LIMIT 1');
        $stmt->execute([':uid' => $uid]);
        $hash = $stmt->fetchColumn();
        if (!$hash) {
            return false;
        }

        return password_verify($password, (string)$hash);
    }

/**
 * Fuehrt resolve group id entsprechend der internen Logik aus.
 *
 * @param mixed $isAdmin Eingabewert fuer isAdmin.
 * @return int Rueckgabewert der Funktion.
 */
private function resolveGroupId(bool $isAdmin): int
    {
        $groups = $this->db->query('SELECT gid, name FROM groupnames ORDER BY gid ASC')->fetchAll(PDO::FETCH_ASSOC);
        if (!$groups) {
            return $isAdmin ? 1 : 2;
        }

        $adminGid = null;
        $userGid = null;

        foreach ($groups as $group) {
            $gid = (int)($group['gid'] ?? 0);
            $name = strtolower((string)($group['name'] ?? ''));

            if ($adminGid === null && str_contains($name, 'admin')) {
                $adminGid = $gid;
                continue;
            }

            if ($userGid === null && !str_contains($name, 'admin')) {
                $userGid = $gid;
            }
        }

        if ($adminGid === null) {
            $adminGid = (int)$groups[0]['gid'];
        }

        if ($userGid === null) {
            foreach ($groups as $group) {
                if ((int)$group['gid'] !== $adminGid) {
                    $userGid = (int)$group['gid'];
                    break;
                }
            }
        }

        if ($userGid === null) {
            $userGid = $adminGid;
        }

        return $isAdmin ? $adminGid : $userGid;
    }
}
