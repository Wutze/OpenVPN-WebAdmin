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

class ConfigService
{
private string $basePath;
private string $historyBasePath;

/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $basePath Eingabewert fuer basePath.
 * @return mixed|null Rueckgabewert der Funktion.
 */
public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? dirname(__DIR__, 2) . '/storage/conf';
        $this->historyBasePath = $this->basePath . '/history';
    }

/**
 * Liefert eine Liste von systems.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
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
 * Liest config und gibt den Wert zurueck.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
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
 * Speichert config persistent.
 *
 * @param mixed $system Eingabewert fuer system.
 * @param mixed $content Eingabewert fuer content.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
public function saveConfig(string $system, string $content): array
    {
        $path = $this->clientPath($system);
        $dir = dirname($path);
        $hadExistingConfig = is_file($path);

        if (!is_dir($dir)) {
            throw new \RuntimeException('Systemordner existiert nicht: ' . $system);
        }

        $historyFile = null;
        if (is_file($path)) {
            try {
                $historyDir = $this->historyDir($system);
                if (!is_dir($historyDir) && !mkdir($historyDir, 0775, true) && !is_dir($historyDir)) {
                    throw new \RuntimeException('Historienordner konnte nicht erstellt werden.');
                }

                // Include microseconds and a random suffix so rapid consecutive saves
                // cannot overwrite an earlier snapshot in the same second.
                $historyFile = date('Ymd_His_u') . '_' . bin2hex(random_bytes(3)) . '_client.ovpn';
                $snapshotPath = $historyDir . '/' . $historyFile;
                if (!copy($path, $snapshotPath)) {
                    throw new \RuntimeException('Vorherige Konfiguration konnte nicht archiviert werden.');
                }
            } catch (\Throwable $e) {
                Debug::log('saveConfig history archive failed', $e->getMessage(), ['system' => $system, 'path' => $path]);
                // Don't report a successful history update when the snapshot failed.
                // The config save remains available, and the caller can surface this state.
                $historyFile = null;
            }
        }

        $this->writeAtomic($path, $content);

        return [
            'history_file' => $historyFile,
            'history_saved' => !$hadExistingConfig || $historyFile !== null,
            'path' => $path,
        ];
    }

/**
 * Liest history list und gibt den Wert zurueck.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
public function getHistoryList(string $system): array
    {
        $files = [];
        foreach ($this->historyDirectories($system) as $historyDir) {
            if (!is_dir($historyDir)) {
                continue;
            }

            foreach (scandir($historyDir) ?: [] as $file) {
                if ($file !== '.' && $file !== '..' && str_ends_with($file, '.ovpn')) {
                    $files[$file] ??= $historyDir . '/' . $file;
                }
            }
        }

        krsort($files);

        $rows = [];
        foreach ($files as $file => $full) {
            $rows[] = [
                'file' => $file,
                'mtime' => date('Y-m-d H:i:s', (int)filemtime($full)),
            ];
        }

        return $rows;
    }

/**
 * Liest diff stats against current und gibt den Wert zurueck.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
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
 * Vergleicht history file und liefert die Unterschiede.
 *
 * @param mixed $system Eingabewert fuer system.
 * @param mixed $historyFile Eingabewert fuer historyFile.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
public function diffHistoryFile(string $system, string $historyFile): array
    {
        $old = $this->getHistoryContent($system, $historyFile);
        $current = $this->getConfig($system);

        return $this->buildDiffData($old, $current);
    }

/**
 * Liest history content und gibt den Wert zurueck.
 *
 * @param mixed $system Eingabewert fuer system.
 * @param mixed $historyFile Eingabewert fuer historyFile.
 * @return string Rueckgabe als Text.
 */
private function getHistoryContent(string $system, string $historyFile): string
    {
        $safe = basename($historyFile);
        foreach ($this->historyDirectories($system) as $historyDir) {
            $path = $historyDir . '/' . $safe;
            if (is_file($path)) {
                return (string)file_get_contents($path);
            }
        }

        throw new \RuntimeException('Historien-Datei nicht gefunden: ' . $safe);
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

/**
 * Fuehrt client path entsprechend der internen Logik aus.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
private function clientPath(string $system): string
    {
        $system = $this->sanitizeSystem($system);
        return $this->basePath . '/' . $system . '/client.ovpn';
    }

/**
 * Fuehrt history dir entsprechend der internen Logik aus.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
private function historyDir(string $system): string
    {
        foreach ($this->historyDirectories($system) as $historyDir) {
            if (is_dir($historyDir)) {
                if (is_writable($historyDir)) {
                    return $historyDir;
                }
                continue;
            }

            $parent = dirname($historyDir);
            while (!is_dir($parent) && dirname($parent) !== $parent) {
                $parent = dirname($parent);
            }
            if (is_dir($parent) && is_writable($parent)) {
                return $historyDir;
            }
        }

        throw new \RuntimeException('Kein beschreibbarer Historienordner fuer System: ' . $system);
    }

    /**
     * Liefert alle bekannten Speicherorte der System-Historie in Prioritaetsreihenfolge.
     *
     * @return string[]
     */
    private function historyDirectories(string $system): array
    {
        $system = $this->sanitizeSystem($system);
        return [
            $this->historyBasePath . '/' . $system,
            $this->basePath . '/' . $system . '/history',
        ];
    }

/**
 * Fuehrt sanitize system entsprechend der internen Logik aus.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
private function sanitizeSystem(string $system): string
    {
        $system = trim($system);
        if ($system === '' || !preg_match('/^[a-zA-Z0-9._-]+$/', $system)) {
            throw new \RuntimeException('Ungueltiger Systemname.');
        }

        return $system;
    }

/**
 * Schreibt atomar in die Zieldatei (tmp + rename).
 * Dadurch funktionieren Saves auch dann, wenn die bestehende Datei restriktive Rechte hat,
 * aber das Verzeichnis beschreibbar ist.
 *
 * @param mixed $path
 * @param mixed $content
 * @return void
 */
private function writeAtomic(string $path, string $content): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            throw new \RuntimeException('Zielordner existiert nicht: ' . $dir);
        }

        $tmp = @tempnam($dir, 'ovpn_');
        if ($tmp === false) {
            throw new \RuntimeException('Temporäre Datei konnte nicht erstellt werden: ' . $dir);
        }

        if (@file_put_contents($tmp, $content) === false) {
            @unlink($tmp);
            throw new \RuntimeException('Temporäre Konfiguration konnte nicht geschrieben werden: ' . $tmp);
        }

        @chmod($tmp, 0664);

        if (@rename($tmp, $path)) {
            return;
        }

        // Fallback fuer einige Mounts/FS-Kombinationen
        if (@copy($tmp, $path)) {
            @unlink($tmp);
            return;
        }

        @unlink($tmp);
        throw new \RuntimeException('client.ovpn konnte nicht gespeichert werden: ' . $path);
    }
}
