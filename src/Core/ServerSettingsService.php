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

class ServerSettingsService
{
private array $cfg;

/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $cfg Eingabewert fuer cfg.
 * @return mixed|null Rueckgabewert der Funktion.
 */
public function __construct(?array $cfg = null)
    {
        $all = $cfg ?? require dirname(__DIR__, 2) . '/config/config.php';
        $vpn = $all['vpn_server_config'] ?? [];

        $this->cfg = [
            'source_url' => (string)($vpn['source_url'] ?? ''),
            'source_path' => (string)($vpn['source_path'] ?? ''),
            'save_path' => (string)($vpn['save_path'] ?? (dirname(__DIR__, 2) . '/storage/conf/server/server.conf')),
            'history_path' => (string)($vpn['history_path'] ?? (dirname(__DIR__, 2) . '/storage/conf/history/server')),
            'auth_header' => (string)($vpn['auth_header'] ?? ''),
            'timeout' => (int)($vpn['timeout'] ?? 6),
        ];
    }

/**
 * Liest current und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
public function getCurrent(): array
    {
        $content = '';
        $sourceInfo = '';

        if ($this->cfg['source_url'] !== '') {
            try {
                $content = $this->fetchFromUrl($this->cfg['source_url']);
                $sourceInfo = 'url:' . $this->cfg['source_url'];
                $this->writeCache($content);
            } catch (\Throwable $e) {
                $content = $this->readSavePath();
                $sourceInfo = 'cache:' . $this->cfg['save_path'] . ' (URL-Fallback: ' . $e->getMessage() . ')';
            }
        } elseif ($this->cfg['source_path'] !== '') {
            if (!is_file($this->cfg['source_path']) && $this->cfg['source_path'] === $this->cfg['save_path']) {
                $this->ensureSaveFileExists();
            }
            $content = $this->readFile($this->cfg['source_path']);
            $sourceInfo = 'file:' . $this->cfg['source_path'];
        } else {
            $content = $this->readSavePath();
            $sourceInfo = 'file:' . $this->cfg['save_path'];
        }

        return [
            'content' => $content,
            'source' => $sourceInfo,
            'save_path' => $this->cfg['save_path'],
            'history' => $this->getDiffStatsAgainstCurrent($content),
        ];
    }

/**
 * Speichert Daten persistent.
 *
 * @param mixed $content Eingabewert fuer content.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
public function save(string $content): array
    {
        if ($this->cfg['save_path'] === '') {
            throw new \RuntimeException('Kein save_path konfiguriert.');
        }

        $savePath = $this->cfg['save_path'];
        $saveDir = dirname($savePath);
        if (!is_dir($saveDir) && !mkdir($saveDir, 0775, true) && !is_dir($saveDir)) {
            throw new \RuntimeException('Speicherordner kann nicht erstellt werden: ' . $saveDir);
        }

        if (is_file($savePath)) {
            $this->archiveFile($savePath);
        }

        if (file_put_contents($savePath, $content) === false) {
            throw new \RuntimeException('Server-Konfiguration konnte nicht gespeichert werden.');
        }

        return [
            'path' => $savePath,
        ];
    }

/**
 * Liest history list und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
public function getHistoryList(): array
    {
        $historyDir = $this->cfg['history_path'];
        if (!is_dir($historyDir)) {
            return [];
        }

        $files = array_values(array_filter(scandir($historyDir) ?: [], static function (string $f): bool {
            return $f !== '.' && $f !== '..' && str_ends_with($f, '.conf');
        }));
        rsort($files);

        $rows = [];
        foreach ($files as $file) {
            $full = $historyDir . '/' . $file;
            $rows[] = [
                'file' => $file,
                'mtime' => date('Y-m-d H:i:s', (int)filemtime($full)),
            ];
        }

        return $rows;
    }

/**
 * Vergleicht history file against current und liefert die Unterschiede.
 *
 * @param mixed $historyFile Eingabewert fuer historyFile.
 * @param mixed $current Eingabewert fuer current.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
public function diffHistoryFileAgainstCurrent(string $historyFile, string $current): array
    {
        $safe = basename($historyFile);
        $path = rtrim($this->cfg['history_path'], '/') . '/' . $safe;
        if (!is_file($path)) {
            throw new \RuntimeException('Historien-Datei nicht gefunden: ' . $safe);
        }

        $old = (string)file_get_contents($path);
        return $this->buildDiffData($old, $current);
    }

/**
 * Liest diff stats against current und gibt den Wert zurueck.
 *
 * @param mixed $current Eingabewert fuer current.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
private function getDiffStatsAgainstCurrent(string $current): array
    {
        $rows = $this->getHistoryList();

        foreach ($rows as &$row) {
            $path = rtrim($this->cfg['history_path'], '/') . '/' . $row['file'];
            $old = (string)file_get_contents($path);
            $d = $this->buildDiffData($old, $current);
            $row['added'] = $d['added'];
            $row['removed'] = $d['removed'];
        }

        return $rows;
    }

/**
 * Fuehrt fetch from url entsprechend der internen Logik aus.
 *
 * @param mixed $url Eingabewert fuer url.
 * @return string Rueckgabe als Text.
 */
private function fetchFromUrl(string $url): string
    {
        if (!preg_match('#^https?://#i', $url)) {
            throw new \RuntimeException('source_url muss mit http/https beginnen.');
        }

        $headers = [];
        if ($this->cfg['auth_header'] !== '') {
            $headers[] = 'Authorization: ' . $this->cfg['auth_header'];
        }

        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $this->cfg['timeout'],
                'header' => implode("\r\n", $headers),
            ],
        ]);

        $data = @file_get_contents($url, false, $ctx);
        if ($data === false) {
            throw new \RuntimeException('Quelle nicht erreichbar.');
        }

        return (string)$data;
    }

/**
 * Schreibt cache in das Zielsystem.
 *
 * @param mixed $content Eingabewert fuer content.
 * @return void Kein Rueckgabewert.
 */
private function writeCache(string $content): void
    {
        $savePath = $this->cfg['save_path'];
        if ($savePath === '') {
            return;
        }

        $dir = dirname($savePath);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            return;
        }

        @file_put_contents($savePath, $content);
    }

/**
 * Fuehrt archive file entsprechend der internen Logik aus.
 *
 * @param mixed $file Eingabewert fuer file.
 * @return void Kein Rueckgabewert.
 */
private function archiveFile(string $file): void
    {
        $historyDir = $this->cfg['history_path'];
        if (!is_dir($historyDir) && !mkdir($historyDir, 0775, true) && !is_dir($historyDir)) {
            throw new \RuntimeException('Historienordner kann nicht erstellt werden: ' . $historyDir);
        }

        $snapshot = $historyDir . '/' . date('Ymd_His') . '_server.conf';
        if (!copy($file, $snapshot)) {
            throw new \RuntimeException('Vorherige Version konnte nicht in die Historie geschrieben werden.');
        }
    }

/**
 * Laedt save path aus der Quelle.
 *
 * @return string Rueckgabe als Text.
 */
private function readSavePath(): string
    {
        if ($this->cfg['save_path'] === '') {
            throw new \RuntimeException('Keine lokale Cache-/Save-Datei vorhanden.');
        }

        if (!is_file($this->cfg['save_path'])) {
            $this->ensureSaveFileExists();
        }

        if (!is_file($this->cfg['save_path'])) {
            throw new \RuntimeException('Keine lokale Cache-/Save-Datei vorhanden.');
        }

        return $this->readFile($this->cfg['save_path']);
    }

/**
 * Laedt file aus der Quelle.
 *
 * @param mixed $path Eingabewert fuer path.
 * @return string Rueckgabe als Text.
 */
private function readFile(string $path): string
    {
        if (!is_file($path)) {
            throw new \RuntimeException('Datei nicht gefunden: ' . $path);
        }

        return (string)file_get_contents($path);
    }

/**
 * Fuehrt ensure save file exists entsprechend der internen Logik aus.
 *
 * @return void Kein Rueckgabewert.
 */
private function ensureSaveFileExists(): void
    {
        $savePath = $this->cfg['save_path'];
        if ($savePath === '') {
            return;
        }

        $dir = dirname($savePath);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            return;
        }

        if (!is_file($savePath)) {
            $seed = "mode server\nproto udp\nport 1194\ndev tun\n\n# server 10.8.0.0 255.255.255.0\n";
            @file_put_contents($savePath, $seed);
        }
    }

/**
 * Erzeugt diff data auf Basis der Eingabedaten.
 *
 * @param mixed $oldText Eingabewert fuer oldText.
 * @param mixed $newText Eingabewert fuer newText.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
private function buildDiffData(string $oldText, string $newText): array
    {
        $oldLines = preg_split('/\R/', $oldText) ?: [];
        $newLines = preg_split('/\R/', $newText) ?: [];

        $ops = $this->diffOps($oldLines, $newLines);
        $added = 0;
        $removed = 0;
        $lines = [];

        foreach ($ops as $op) {
            if ($op['type'] === '=') {
                $lines[] = '  ' . $op['line'];
            } elseif ($op['type'] === '+') {
                $added++;
                $lines[] = '+ ' . $op['line'];
            } else {
                $removed++;
                $lines[] = '- ' . $op['line'];
            }
        }

        return [
            'added' => $added,
            'removed' => $removed,
            'diff' => implode("\n", $lines),
        ];
    }

/**
 * Vergleicht ops und liefert die Unterschiede.
 *
 * @param mixed $a Eingabewert fuer a.
 * @param mixed $b Eingabewert fuer b.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
private function diffOps(array $a, array $b): array
    {
        $n = count($a);
        $m = count($b);
        $dp = array_fill(0, $n + 1, array_fill(0, $m + 1, 0));

        for ($i = $n - 1; $i >= 0; $i--) {
            for ($j = $m - 1; $j >= 0; $j--) {
                if ($a[$i] === $b[$j]) {
                    $dp[$i][$j] = $dp[$i + 1][$j + 1] + 1;
                } else {
                    $dp[$i][$j] = max($dp[$i + 1][$j], $dp[$i][$j + 1]);
                }
            }
        }

        $i = 0;
        $j = 0;
        $ops = [];

        while ($i < $n && $j < $m) {
            if ($a[$i] === $b[$j]) {
                $ops[] = ['type' => '=', 'line' => $a[$i]];
                $i++;
                $j++;
            } elseif ($dp[$i + 1][$j] >= $dp[$i][$j + 1]) {
                $ops[] = ['type' => '-', 'line' => $a[$i]];
                $i++;
            } else {
                $ops[] = ['type' => '+', 'line' => $b[$j]];
                $j++;
            }
        }

        while ($i < $n) {
            $ops[] = ['type' => '-', 'line' => $a[$i]];
            $i++;
        }
        while ($j < $m) {
            $ops[] = ['type' => '+', 'line' => $b[$j]];
            $j++;
        }

        return $ops;
    }
}
