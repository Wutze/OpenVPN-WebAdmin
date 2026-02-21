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
     * Kurzbeschreibung Funktion __construct
     *
     * @return mixed|null
     */
public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Kurzbeschreibung Funktion getAllUsers
     *
     * @param mixed $limit
     * @param mixed $offset
     * @param mixed $search
     * @return array
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
                u.user_online
            FROM user u
            LEFT JOIN groupnames g ON u.gid = g.gid
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
     * Kurzbeschreibung Funktion countUsers
     *
     * @param mixed $search
     * @return int
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
     * Kurzbeschreibung Funktion countOnlineUsers
     *
     * @return int
     */
public function countOnlineUsers(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM user WHERE user_online = 1');
        return (int)$stmt->fetchColumn();
    }

    /**
     * Kurzbeschreibung Funktion userExists
     *
     * @param mixed $username
     * @return bool
     */
public function userExists(string $username): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM user WHERE user_name = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Kurzbeschreibung Funktion createUser
     *
     * @param mixed $username
     * @param mixed $password
     * @param mixed $isAdmin
     * @return void
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
     * Kurzbeschreibung Funktion setUserEnabled
     *
     * @param mixed $username
     * @param mixed $enabled
     * @return void
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
     * Kurzbeschreibung Funktion setUserRole
     *
     * @param mixed $username
     * @param mixed $isAdmin
     * @return void
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
     * Kurzbeschreibung Funktion setUserPasswordByName
     *
     * @param mixed $username
     * @param mixed $newPassword
     * @return void
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
     * Kurzbeschreibung Funktion setUserPasswordById
     *
     * @param mixed $uid
     * @param mixed $newPassword
     * @return void
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
     * Kurzbeschreibung Funktion setUserLimits
     *
     * @param mixed $username
     * @param mixed $startDate
     * @param mixed $endDate
     * @return void
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
     * Kurzbeschreibung Funktion deleteUser
     *
     * @param mixed $username
     * @return void
     */
public function deleteUser(string $username): void
    {
        $stmt = $this->db->prepare('DELETE FROM user WHERE user_name = :username');
        $stmt->execute([':username' => $username]);
    }

    /**
     * Kurzbeschreibung Funktion verifyPassword
     *
     * @param mixed $uid
     * @param mixed $password
     * @return bool
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
     * Kurzbeschreibung Funktion resolveGroupId
     *
     * @param mixed $isAdmin
     * @return int
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
