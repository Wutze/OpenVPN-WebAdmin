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

class UserModel
{
private PDO $db;

/**
 * Bezieht die PDO-Verbindung aus dem Datenbank-Singleton.
 */
public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

/**
 * Liest Benutzer inklusive Gruppenname und erster fester IP, optional nach Benutzer- oder Gruppenname gefiltert.
 *
 * @param int $limit Maximale Zeilenzahl.
 * @param int $offset Anzahl übersprungener Zeilen.
 * @param string $search Teilstring für Benutzer- und Gruppenname; leer deaktiviert die Suche.
 * @return array<int, array<string, mixed>> Assoziative PDO-Zeilen mit `uid`, `uname`, `gname`, Status/Fristen und `fixed_ip`.
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
            // Native PDO prepares do not allow a named placeholder to be
            // reused twice in one statement.
            $sql .= " AND (u.user_name LIKE :user_search OR g.name LIKE :group_search)";
            $searchPattern = '%' . $search . '%';
            $params[':user_search'] = $searchPattern;
            $params[':group_search'] = $searchPattern;
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
 * Zählt Benutzer passend zum optionalen Suchtext in Benutzer- oder Gruppenname.
 *
 * @param string $search Teilstring; leer zählt alle Benutzer.
 * @return int Anzahl passender Datensätze.
 */
public function countUsers(string $search = ''): int
    {
        $sql = 'SELECT COUNT(*) FROM user u LEFT JOIN groupnames g ON u.gid = g.gid WHERE 1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (u.user_name LIKE :user_search OR g.name LIKE :group_search)';
            $searchPattern = '%' . $search . '%';
            $params[':user_search'] = $searchPattern;
            $params[':group_search'] = $searchPattern;
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Liefert aktive, fuer VPN-Zugaenge vorgesehene Benutzer mit serverseitiger Suche.
     *
     * @return array{total: int, rows: array<int, array<string, mixed>>}
     */
    public function getVpnProfileUsers(int $limit, int $offset, string $search = ''): array
    {
        $limit = max(1, min(100, $limit));
        $offset = max(0, $offset);
        $where = 'u.user_enable = 1
                  AND (u.user_start_date IS NULL OR u.user_start_date <= CURDATE())
                  AND (u.user_end_date IS NULL OR u.user_end_date >= CURDATE())';
        $params = [];
        if ($search !== '') {
            $where .= ' AND u.user_name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $count = $this->db->prepare("SELECT COUNT(*) FROM user u WHERE {$where}");
        $count->execute($params);

        $stmt = $this->db->prepare(
            "SELECT u.uid, u.user_name AS uname
             FROM user u
             WHERE {$where}
             ORDER BY u.user_name ASC, u.uid ASC
             LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'total' => (int)$count->fetchColumn(),
            'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    /**
     * Liest den Benutzerstatus und die autoritative Rolle fuer den Download.
     *
     * @return array<string, mixed>|null
     */
    public function getVpnProfileUser(int $uid): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT u.uid, u.user_name, u.gid, u.user_enable, u.user_start_date, u.user_end_date,
                    COALESCE(g.name, '') AS role_name
             FROM user u
             LEFT JOIN groupnames g ON g.gid = u.gid
             WHERE u.uid = :uid
             LIMIT 1"
        );
        $stmt->execute([':uid' => $uid]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($user) ? $user : null;
    }

/**
 * Zählt Datensätze mit `user_online = 1`.
 *
 * @return int Zahl der als online markierten Benutzer.
 */
public function countOnlineUsers(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM user WHERE user_online = 1');
        return (int)$stmt->fetchColumn();
    }

/**
 * Prüft per vorbereiteter Abfrage, ob ein Benutzername vorhanden ist.
 *
 * @param string $username Exakter Benutzername.
 * @return bool True bei Erfolg, sonst false.
 */
public function userExists(string $username): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM user WHERE user_name = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        return (bool)$stmt->fetchColumn();
    }

/**
 * Fügt einen aktivierten Benutzer mit gehashtem Passwort und anhand der Gruppen konfigurierter Rolle ein.
 *
 * @param string $username Benutzername.
 * @param string $password Klartextpasswort, das vor Speicherung mit `password_hash()` verarbeitet wird.
 * @param bool $isAdmin Wählt die ermittelte Admin- statt Benutzergruppe.
 * @return void Kein Rueckgabewert.
 * @throws \PDOException Bei Datenbankfehlern; Exceptions der Gruppenabfrage werden ebenfalls weitergegeben.
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
 * Aktualisiert das Aktiv-Flag des Benutzers.
 *
 * @param string $username Exakter Benutzername.
 * @param bool $enabled Aktiviert bei true, deaktiviert bei false.
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
 * Ermittelt die passende Gruppen-ID und weist sie dem Benutzer zu.
 *
 * @param string $username Exakter Benutzername.
 * @param bool $isAdmin Wählt Admin- oder Nicht-Admin-Gruppe.
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
 * Hasht das neue Passwort und aktualisiert es anhand des Benutzernamens.
 *
 * @param string $username Exakter Benutzername.
 * @param string $newPassword Neues Klartextpasswort.
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
 * Hasht das neue Passwort und aktualisiert es anhand der Benutzer-ID.
 *
 * @param int $uid Benutzer-ID.
 * @param string $newPassword Neues Klartextpasswort.
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
     * Aktualisiert Start- und Enddatum; leere Strings werden als SQL-NULL gespeichert.
     *
     * @param string $username Exakter Benutzername.
     * @param string|null $startDate Einschließlich geltendes Startdatum oder null/leer für unbegrenzt.
     * @param string|null $endDate Einschließlich geltendes Enddatum oder null/leer für unbegrenzt.
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
 * Ersetzt die IP-Zuordnungen des Benutzers durch eine feste IP oder löscht sie bei leerem Wert.
 *
 * @param string $username Exakter Benutzername; bei nicht gefundener UID erfolgt keine Änderung.
 * @param string|null $fixedIp Neue IP oder null/leer zum Entfernen der Zuordnung.
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
 * Prüft, ob eine feste IP bereits einem anderen Benutzer zugeordnet ist.
 *
 * @param string $username Auszuschließender Benutzername.
 * @param string $fixedIp Zu prüfende IP-Adresse.
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
 * Löscht den Benutzer anhand des Namens; abhängige Datensätze werden nicht explizit behandelt.
 *
 * @param string $username Exakter Benutzername.
 * @return void Kein Rueckgabewert.
 */
public function deleteUser(string $username): void
    {
        $stmt = $this->db->prepare('DELETE FROM user WHERE user_name = :username');
        $stmt->execute([':username' => $username]);
    }

/**
 * Lädt den Passwort-Hash zur UID und verifiziert ihn mit `password_verify()`.
 *
 * @param int $uid Benutzer-ID.
 * @param string $password Zu prüfendes Klartextpasswort.
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
 * Sucht Admin- und Nicht-Admin-Gruppe anhand ihrer Namen und verwendet Fallback-IDs, wenn Gruppen fehlen.
 *
 * @param bool $isAdmin Ob die Admin-Gruppe ausgewählt werden soll.
 * @return int Gruppen-ID; bei leerer Gruppentabelle 1 für Admin oder 2 für Benutzer.
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
