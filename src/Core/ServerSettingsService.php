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

class ServerSettingsService
{
private array $cfg;

/**
 * Liest VPN-Server-Quell-, Cache- und Historienpfade sowie HTTP-Optionen aus der Konfiguration.
 *
 * @param array|null $cfg Gesamtkonfiguration; null lädt `config/config.php`.
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
 * Bezieht die Serverkonfiguration aus URL, Quelldatei oder lokalem Cache und ergänzt Historien-Diffstatistiken.
 *
 * @return array{content: string, source: string, save_path: string, history: array, read_only: bool} Inhalt, Herkunft, Zielpfad, Historie und Schreibschutz.
 * @throws \RuntimeException Wenn die Quelle und gegebenenfalls der lokale Fallback nicht lesbar sind.
 */
public function getCurrent(): array
    {
        $content = '';
        $sourceInfo = '';

        if ($this->cfg['source_url'] !== '') {
            try {
                $content = $this->fetchFromUrl($this->cfg['source_url']);
                $sourceInfo = 'url:' . $this->cfg['source_url'];
                if (!$this->isReadOnly()) {
                    $this->writeCache($content);
                }
            } catch (\Throwable $e) {
                $content = $this->readSavePath();
                $sourceInfo = 'cache:' . $this->cfg['save_path'] . ' (URL-Fallback: ' . $e->getMessage() . ')';
            }
        } elseif ($this->cfg['source_path'] !== '') {
            if (!is_file($this->cfg['source_path']) && $this->cfg['source_path'] === $this->cfg['save_path'] && !$this->isReadOnly()) {
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
            'read_only' => $this->isReadOnly(),
        ];
    }

/**
 * Verhindert Schreibzugriffe auf verlinkte Serverkonfigurationen.
 */
public function isReadOnly(): bool
    {
        return is_link($this->cfg['source_path']) || is_link($this->cfg['save_path']);
    }

/**
 * Archiviert vorhandene Zieldatei und schreibt anschließend den neuen Serverkonfigurationstext.
 *
 * @param string $content Vollständiger neuer Konfigurationsinhalt.
 * @return array{path: string} Pfad der gespeicherten Datei.
 * @throws \RuntimeException Bei leerem Zielpfad, Verzeichnis-, Archiv- oder Schreibfehler.
 */
public function save(string $content): array
    {
        if ($this->isReadOnly()) {
            throw new \RuntimeException('Server configuration is read-only.');
        }
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
 * Listet `.conf`-Snapshots im konfigurierten Historienverzeichnis absteigend nach Dateiname.
 *
 * @return array<int, array{file: string, mtime: string}> Dateiname und formatierte Änderungszeit; bei fehlendem Ordner leer.
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
 * Vergleicht den per Basisdateiname ausgewählten Snapshot mit dem übergebenen aktuellen Inhalt.
 *
 * @param string $historyFile Snapshotname; Pfadanteile werden durch `basename()` entfernt.
 * @param string $current Aktueller Konfigurationstext.
 * @return array{added: int, removed: int, diff: string} Zeilenänderungen und formatierter Diff.
 * @throws \RuntimeException Wenn der Snapshot fehlt.
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
 * Ergänzt die Historienliste um Anzahl hinzugefügter und entfernter Zeilen gegenüber dem Inhalt.
 *
 * @param string $current Aktueller Serverkonfigurationstext.
 * @return array<int, array{file: string, mtime: string, added: int, removed: int}> Historienzeilen mit Diff-Zählern.
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
 * Ruft eine HTTP(S)-Quelle mit GET, konfiguriertem Timeout und optionalem Authorization-Header ab.
 *
 * @param string $url Abrufadresse; andere Schemes als HTTP und HTTPS werden abgewiesen.
 * @return string Antwortinhalt.
 * @throws \RuntimeException Bei ungültigem Scheme oder fehlgeschlagenem Abruf.
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
 * Schreibt den erfolgreichen URL-Inhalt bestmöglich in den konfigurierten Cache; Fehler werden ignoriert.
 *
 * @param string $content Abzurufender Konfigurationsinhalt.
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
 * Kopiert eine bestehende Datei unter Zeitstempel in den Historienordner.
 *
 * @param string $file Zu archivierende Quelldatei.
 * @return void Kein Rueckgabewert.
 * @throws \RuntimeException Wenn Historienordner oder Kopie nicht erstellt werden kann.
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

        if (!is_file($this->cfg['save_path']) && !$this->isReadOnly()) {
            $this->ensureSaveFileExists();
        }

        if (!is_file($this->cfg['save_path'])) {
            throw new \RuntimeException('Keine lokale Cache-/Save-Datei vorhanden.');
        }

        return $this->readFile($this->cfg['save_path']);
    }

/**
 * Liest eine vorhandene Datei vollständig als Text.
 *
 * @param string $path Quelldateipfad.
 * @return string Dateiinhalt.
 * @throws \RuntimeException Wenn die Datei fehlt.
 */
private function readFile(string $path): string
    {
        if (!is_file($path)) {
            throw new \RuntimeException('Datei nicht gefunden: ' . $path);
        }

        return (string)file_get_contents($path);
    }

/**
 * Legt den Save-Ordner und bei Bedarf eine minimale Serverkonfiguration an; Anlagefehler werden ignoriert.
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
 * Berechnet Anzahl und Textdarstellung gleicher, hinzugefügter und entfernter Zeilen.
 *
 * @param string $oldText Vorheriger Konfigurationstext.
 * @param string $newText Neuer Konfigurationstext.
 * @return array{added: int, removed: int, diff: string} Zähler und mit `  `, `+ `, `- ` markierte Zeilen.
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
 * Erzeugt anhand der längsten gemeinsamen Teilfolge eine geordnete Liste der Zeilenoperationen.
 *
 * @param string[] $a Alte Zeilen.
 * @param string[] $b Neue Zeilen.
 * @return array<int, array{type: '='|'+'|'-', line: string}> Operationen mit Zeilentyp und Text.
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
