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

use Micro\OpenvpnWebadmin\Core\Database;
use PDO;

class LogModel
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
 * Liest all logs und gibt den Wert zurueck.
 *
 * @param mixed $limit Eingabewert fuer limit.
 * @param mixed $offset Eingabewert fuer offset.
 * @param mixed $search Eingabewert fuer search.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
public function getAllLogs(int $limit = 50, int $offset = 0, string $search = null): array
    {
        $sql = "SELECT * FROM log WHERE 1";
        $params = [];

        if ($search) {
            $sql .= " AND (user_name LIKE :search OR log_trusted_ip LIKE :search OR log_remote_ip LIKE :search)";
            $params[':search'] = "%$search%";
        }

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
 * Fuehrt count logs entsprechend der internen Logik aus.
 *
 * @param mixed $search Eingabewert fuer search.
 * @return int Rueckgabewert der Funktion.
 */
public function countLogs(string $search = null): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM log WHERE 1";
        $params = [];

        if ($search) {
            $sql .= " AND (user_name LIKE :search OR log_trusted_ip LIKE :search OR log_remote_ip LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }
}