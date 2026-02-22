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

class ProfileService
{
private string $basePath;

/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $basePath Eingabewert fuer basePath.
 * @return mixed|null Rueckgabewert der Funktion.
 */
public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? dirname(__DIR__, 2) . '/storage/conf';
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
            if ($item === '.' || $item === '..') {
                continue;
            }

            $dir = $this->basePath . '/' . $item;
            if (!$this->isAllowedSystemDir($item, $dir)) {
                continue;
            }

            $fileList = [];
            foreach (scandir($dir) ?: [] as $name) {
                if ($name === '.' || $name === '..') {
                    continue;
                }
                $full = $dir . '/' . $name;
                if (is_file($full)) {
                    $fileList[] = $name;
                }
            }

            $zipPath = $this->zipPathFor($item);
            $systems[] = [
                'system' => $item,
                'file_count' => count($fileList),
                'files' => $fileList,
                'zip_exists' => is_file($zipPath),
                'zip_file' => basename($zipPath),
                'zip_mtime' => is_file($zipPath) ? date('Y-m-d H:i:s', (int)filemtime($zipPath)) : null,
            ];
        }

        usort($systems, static fn(array $a, array $b): int => strcmp($a['system'], $b['system']));
        return $systems;
    }

/**
 * Erzeugt zip auf Basis der Eingabedaten.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
public function buildZip(string $system): string
    {
        $system = $this->sanitizeSystem($system);
        $sourceDir = $this->basePath . '/' . $system;

        if (!$this->isAllowedSystemDir($system, $sourceDir)) {
            throw new \RuntimeException('Unbekanntes System: ' . $system);
        }

        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('ZipArchive ist nicht verfuegbar.');
        }

        $zipPath = $this->zipPathFor($system);
        $zip = new \ZipArchive();
        $openResult = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($openResult !== true) {
            throw new \RuntimeException('ZIP-Datei konnte nicht erstellt werden. Fehlercode: ' . $openResult);
        }

        $entries = scandir($sourceDir) ?: [];
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $fullPath = $sourceDir . '/' . $entry;
            if (is_file($fullPath)) {
                if ($entry === 'client.ovpn') {
                    $content = (string)file_get_contents($fullPath);
                    $zip->addFromString($entry, $this->injectCaTlsBlocks($content));
                } else {
                    $zip->addFile($fullPath, $entry);
                }
            }
        }

        $zip->close();
        return $zipPath;
    }

/**
 * Liest zip path und gibt den Wert zurueck.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
public function getZipPath(string $system): string
    {
        $system = $this->sanitizeSystem($system);
        $zipPath = $this->zipPathFor($system);

        if (!is_file($zipPath)) {
            return $this->buildZip($system);
        }

        return $zipPath;
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
 * Fuehrt zip path for entsprechend der internen Logik aus.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
private function zipPathFor(string $system): string
    {
        return $this->basePath . '/' . $system . '.zip';
    }

    /**
     * Prueft, ob allowed system dir zutrifft.
     *
     * @param mixed $system Eingabewert fuer system.
     * @param mixed $dir Eingabewert fuer dir.
     * @return bool True bei Erfolg, sonst false.
     */
    private function isAllowedSystemDir(string $system, string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        if (in_array($system, ['server', 'history'], true)) {
            return false;
        }

        return is_file($dir . '/client.ovpn');
    }

/**
 * Fuehrt inject ca tls blocks entsprechend der internen Logik aus.
 *
 * @param mixed $content Eingabewert fuer content.
 * @return string Rueckgabe als Text.
 */
private function injectCaTlsBlocks(string $content): string
    {
        try {
            $service = new CaTlsService();
            $data = $service->getCurrent();
            $ca = trim((string)($data['ca'] ?? ''));
            $tls = trim((string)($data['tls'] ?? ''));

            if ($ca !== '') {
                $content = $this->replaceOvpnBlock($content, 'ca', $ca);
            }
            if ($tls !== '') {
                $content = $this->replaceOvpnBlock($content, 'tls-auth', $tls);
            }
        } catch (\Throwable $e) {
            // CA/TLS ist optional: ZIP-Build darf bei fehlenden Daten nicht abbrechen.
        }

        return $content;
    }

/**
 * Fuehrt replace ovpn block entsprechend der internen Logik aus.
 *
 * @param mixed $content Eingabewert fuer content.
 * @param mixed $tag Eingabewert fuer tag.
 * @param mixed $blockContent Eingabewert fuer blockContent.
 * @return string Rueckgabe als Text.
 */
private function replaceOvpnBlock(string $content, string $tag, string $blockContent): string
    {
        $block = "<{$tag}>\n{$blockContent}\n</{$tag}>";
        $pattern = '/<' . preg_quote($tag, '/') . '>\s*[\s\S]*?\s*<\/' . preg_quote($tag, '/') . '>/i';

        if (preg_match($pattern, $content) === 1) {
            return (string)preg_replace($pattern, $block, $content, 1);
        }

        return rtrim($content) . "\n\n" . $block . "\n";
    }
}
