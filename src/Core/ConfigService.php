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

class ConfigService
{
    private string $basePath;
    private string $historyBasePath;

    /**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $basePath
     * @return mixed|null
     */
public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? dirname(__DIR__, 2) . '/storage/conf';
        $this->historyBasePath = $this->basePath . '/history';
    }

    /**
     * Kurzbeschreibung Funktion listSystems
     *
     * @return array
     */
public function listSystems(): array
    {
        if (!is_dir($this->basePath)) {
            return [];
        }

        $items = scandir($this->basePath) ?: [];
        $systems = [];

        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || $item === 'server' || $item === 'history') {
                continue;
            }

            $dir = $this->basePath . '/' . $item;
            if (is_dir($dir) && is_file($dir . '/client.ovpn')) {
                $systems[] = $item;
            }
        }

        sort($systems);
        return $systems;
    }

    /**
     * Kurzbeschreibung Funktion getConfig
     *
     * @param mixed $system
     * @return string
     */
public function getConfig(string $system): string
    {
        $path = $this->clientPath($system);
        if (!is_file($path)) {
            throw new \RuntimeException('client.ovpn nicht gefunden fuer System: ' . $system);
        }

        return (string)file_get_contents($path);
    }

    /**
     * Kurzbeschreibung Funktion saveConfig
     *
     * @param mixed $system
     * @param mixed $content
     * @return array
     */
public function saveConfig(string $system, string $content): array
    {
        $path = $this->clientPath($system);
        $dir = dirname($path);

        if (!is_dir($dir)) {
            throw new \RuntimeException('Systemordner existiert nicht: ' . $system);
        }

        $historyFile = null;
        if (is_file($path)) {
            $historyDir = $this->historyDir($system);
            if (!is_dir($historyDir) && !mkdir($historyDir, 0775, true) && !is_dir($historyDir)) {
                throw new \RuntimeException('Historienordner konnte nicht erstellt werden.');
            }

            $historyFile = date('Ymd_His') . '_client.ovpn';
            $snapshotPath = $historyDir . '/' . $historyFile;
            if (!copy($path, $snapshotPath)) {
                throw new \RuntimeException('Vorherige Konfiguration konnte nicht archiviert werden.');
            }
        }

        if (file_put_contents($path, $content) === false) {
            throw new \RuntimeException('client.ovpn konnte nicht gespeichert werden.');
        }

        return [
            'history_file' => $historyFile,
            'path' => $path,
        ];
    }

    /**
     * Kurzbeschreibung Funktion getHistoryList
     *
     * @param mixed $system
     * @return array
     */
public function getHistoryList(string $system): array
    {
        $historyDir = $this->historyDir($system);
        if (!is_dir($historyDir)) {
            return [];
        }

        $files = array_values(array_filter(scandir($historyDir) ?: [], static function (string $file): bool {
            return $file !== '.' && $file !== '..' && str_ends_with($file, '.ovpn');
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
     * Kurzbeschreibung Funktion getDiffStatsAgainstCurrent
     *
     * @param mixed $system
     * @return array
     */
public function getDiffStatsAgainstCurrent(string $system): array
    {
        $current = $this->getConfig($system);
        $rows = $this->getHistoryList($system);

        foreach ($rows as &$row) {
            $old = $this->getHistoryContent($system, $row['file']);
            $diffData = $this->buildDiffData($old, $current);
            $row['added'] = $diffData['added'];
            $row['removed'] = $diffData['removed'];
        }

        return $rows;
    }

    /**
     * Kurzbeschreibung Funktion diffHistoryFile
     *
     * @param mixed $system
     * @param mixed $historyFile
     * @return array
     */
public function diffHistoryFile(string $system, string $historyFile): array
    {
        $old = $this->getHistoryContent($system, $historyFile);
        $current = $this->getConfig($system);

        return $this->buildDiffData($old, $current);
    }

    /**
     * Kurzbeschreibung Funktion getHistoryContent
     *
     * @param mixed $system
     * @param mixed $historyFile
     * @return string
     */
private function getHistoryContent(string $system, string $historyFile): string
    {
        $safe = basename($historyFile);
        $path = $this->historyDir($system) . '/' . $safe;
        if (!is_file($path)) {
            throw new \RuntimeException('Historien-Datei nicht gefunden: ' . $safe);
        }

        return (string)file_get_contents($path);
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

    /**
     * Kurzbeschreibung Funktion clientPath
     *
     * @param mixed $system
     * @return string
     */
private function clientPath(string $system): string
    {
        $system = $this->sanitizeSystem($system);
        return $this->basePath . '/' . $system . '/client.ovpn';
    }

    /**
     * Kurzbeschreibung Funktion historyDir
     *
     * @param mixed $system
     * @return string
     */
private function historyDir(string $system): string
    {
        $system = $this->sanitizeSystem($system);
        return $this->historyBasePath . '/' . $system;
    }

    /**
     * Kurzbeschreibung Funktion sanitizeSystem
     *
     * @param mixed $system
     * @return string
     */
private function sanitizeSystem(string $system): string
    {
        $system = trim($system);
        if ($system === '' || !preg_match('/^[a-zA-Z0-9._-]+$/', $system)) {
            throw new \RuntimeException('Ungueltiger Systemname.');
        }

        return $system;
    }
}
