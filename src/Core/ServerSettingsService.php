<?php

namespace Micro\OpenvpnWebadmin\Core;

class ServerSettingsService
{
    private array $cfg;

    /**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $cfg
     * @return mixed|null
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
     * Kurzbeschreibung Funktion getCurrent
     *
     * @return array
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
     * Kurzbeschreibung Funktion save
     *
     * @param mixed $content
     * @return array
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
     * Kurzbeschreibung Funktion getHistoryList
     *
     * @return array
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
     * Kurzbeschreibung Funktion diffHistoryFileAgainstCurrent
     *
     * @param mixed $historyFile
     * @param mixed $current
     * @return array
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
     * Kurzbeschreibung Funktion getDiffStatsAgainstCurrent
     *
     * @param mixed $current
     * @return array
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
     * Kurzbeschreibung Funktion fetchFromUrl
     *
     * @param mixed $url
     * @return string
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
     * Kurzbeschreibung Funktion writeCache
     *
     * @param mixed $content
     * @return void
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
     * Kurzbeschreibung Funktion archiveFile
     *
     * @param mixed $file
     * @return void
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
     * Kurzbeschreibung Funktion readSavePath
     *
     * @return string
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
     * Kurzbeschreibung Funktion readFile
     *
     * @param mixed $path
     * @return string
     */
private function readFile(string $path): string
    {
        if (!is_file($path)) {
            throw new \RuntimeException('Datei nicht gefunden: ' . $path);
        }

        return (string)file_get_contents($path);
    }

    /**
     * Kurzbeschreibung Funktion ensureSaveFileExists
     *
     * @return void
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
     * Kurzbeschreibung Funktion buildDiffData
     *
     * @param mixed $oldText
     * @param mixed $newText
     * @return array
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
     * Kurzbeschreibung Funktion diffOps
     *
     * @param mixed $a
     * @param mixed $b
     * @return array
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
