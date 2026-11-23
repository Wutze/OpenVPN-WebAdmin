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

use Micro\OpenvpnWebadmin\Core\Database;
use PDO;

class LogModel
{
private PDO $db;

/**
 * Bezieht standardmäßig die gemeinsame PDO-Verbindung aus dem Database-Singleton.
 *
 * @param PDO|null $db Optionale Datenbankverbindung.
 */
public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

/**
 * Liest Logdatensätze absteigend nach Startzeit, optional nach Benutzer- oder IP-Feldern gefiltert.
 *
 * @param int $limit Maximale Zahl der Datensätze.
 * @param int $offset Zahl der zu überspringenden Datensätze.
 * @param string|null $search Teiltext für Benutzername, vertrauenswürdige IP oder Remote-IP; null/leer ohne Filter.
 * @return array<int, array<string, mixed>> Assoziative PDO-Zeilen aus der Tabelle `log`.
 */
public function getAllLogs(int $limit = 50, int $offset = 0, ?string $search = null): array
    {
        $sql = "SELECT * FROM log WHERE 1";
        $params = [];

        $sql .= $this->searchFilter($search, $params);

        $sql .= " ORDER BY log_start_time DESC LIMIT :limit OFFSET :offset";
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
 * Zählt Logeinträge mit demselben optionalen Benutzer- und IP-Suchfilter wie `getAllLogs()`.
 *
 * @param string|null $search Teiltext für Benutzername, vertrauenswürdige IP oder Remote-IP.
 * @return int Zahl der passenden Logeinträge.
 */
public function countLogs(?string $search = null): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM log WHERE 1";
        $params = [];

        $sql .= $this->searchFilter($search, $params);

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

/**
 * Summiert Verbindungen und protokollierten Traffic nach Verbindungsbeginn im aktuellen Datenbankmonat.
 *
 * @return array{connections: int, received_gb: float, sent_gb: float, total_gb: float}
 */
public function getCurrentMonthStats(): array
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS connections,
                    ROUND(COALESCE(SUM(log_received), 0) / 1073741824, 2) AS received_gb,
                    ROUND(COALESCE(SUM(log_send), 0) / 1073741824, 2) AS sent_gb,
                    ROUND((COALESCE(SUM(log_received), 0) + COALESCE(SUM(log_send), 0)) / 1073741824, 2) AS total_gb
             FROM log
             WHERE log_start_time >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
               AND log_start_time < DATE_FORMAT(CURDATE() + INTERVAL 1 MONTH, '%Y-%m-01')"
        );
        if ($stmt === false) {
            throw new \RuntimeException('Could not query current month log statistics.');
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            throw new \RuntimeException('Could not read current month log statistics.');
        }

        return [
            'connections' => (int)$row['connections'],
            'received_gb' => (float)$row['received_gb'],
            'sent_gb' => (float)$row['sent_gb'],
            'total_gb' => (float)$row['total_gb'],
        ];
    }

/**
 * Baut für beide Logabfragen denselben Filter mit getrennten PDO-Platzhaltern.
 *
 * @param array<string, string> $params Bindewerte der Abfrage.
 */
private function searchFilter(?string $search, array &$params): string
    {
        if ($search === null || $search === '') {
            return '';
        }

        $pattern = '%' . $search . '%';
        $params[':user_search'] = $pattern;
        $params[':trusted_ip_search'] = $pattern;
        $params[':remote_ip_search'] = $pattern;

        return ' AND (user_name LIKE :user_search OR log_trusted_ip LIKE :trusted_ip_search OR log_remote_ip LIKE :remote_ip_search)';
    }
}
