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

class CaTlsService
{
private string $basePath;
private string $caPath;
private string $tlsPath;

/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $basePath Eingabewert fuer basePath.
 * @return mixed|null Rueckgabewert der Funktion.
 */
public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? $this->resolveUsableBasePath();
        $this->caPath = $this->basePath . '/client_ca.pem';
        $this->tlsPath = $this->basePath . '/client_tls.key';
    }

/**
 * Liest current und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
public function getCurrent(): array
    {
        $this->ensureStorage();
        $this->ensureFile($this->caPath);
        $this->ensureFile($this->tlsPath);

        return [
            'ca' => (string)file_get_contents($this->caPath),
            'tls' => (string)file_get_contents($this->tlsPath),
            'ca_path' => $this->caPath,
            'tls_path' => $this->tlsPath,
        ];
    }

/**
 * Speichert Daten persistent.
 *
 * @param mixed $caContent Eingabewert fuer caContent.
 * @param mixed $tlsContent Eingabewert fuer tlsContent.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
public function save(string $caContent, string $tlsContent): array
    {
        $this->ensureStorage();
        $this->writeSecureFile($this->caPath, $caContent);
        $this->writeSecureFile($this->tlsPath, $tlsContent);

        return [
            'ca_path' => $this->caPath,
            'tls_path' => $this->tlsPath,
        ];
    }

/**
 * Fuehrt ensure storage entsprechend der internen Logik aus.
 *
 * @return void Kein Rueckgabewert.
 */
private function ensureStorage(): void
    {
        if (!is_dir($this->basePath) && !@mkdir($this->basePath, 0700, true) && !is_dir($this->basePath)) {
            throw new \RuntimeException('CA/TLS Speicherverzeichnis konnte nicht erstellt werden.');
        }

        if (!is_readable($this->basePath) || !is_writable($this->basePath)) {
            throw new \RuntimeException('CA/TLS Speicherverzeichnis ist nicht beschreibbar: ' . $this->basePath);
        }

        @chmod($this->basePath, 0700);
    }

/**
 * Fuehrt ensure file entsprechend der internen Logik aus.
 *
 * @param mixed $path Eingabewert fuer path.
 * @return void Kein Rueckgabewert.
 */
private function ensureFile(string $path): void
    {
        if (!is_file($path)) {
            if (@file_put_contents($path, '') === false) {
                throw new \RuntimeException('CA/TLS Datei konnte nicht erstellt werden.');
            }
        }

        @chmod($path, 0600);
    }

/**
 * Schreibt secure file in das Zielsystem.
 *
 * @param mixed $path Eingabewert fuer path.
 * @param mixed $content Eingabewert fuer content.
 * @return void Kein Rueckgabewert.
 */
private function writeSecureFile(string $path, string $content): void
    {
        $tmp = @tempnam($this->basePath, 'ovpn_');
        if ($tmp === false) {
            throw new \RuntimeException('Temporäre Datei konnte nicht erstellt werden.');
        }

        if (@file_put_contents($tmp, $content) === false) {
            @unlink($tmp);
            throw new \RuntimeException('CA/TLS Datei konnte nicht geschrieben werden.');
        }

        @chmod($tmp, 0600);
        if (!@rename($tmp, $path)) {
            @unlink($tmp);
            throw new \RuntimeException('CA/TLS Datei konnte nicht gespeichert werden.');
        }

        @chmod($path, 0600);
    }

/**
 * Fuehrt resolve usable base path entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
private function resolveUsableBasePath(): string
    {
        $root = dirname(__DIR__, 2);
        $candidates = [
            $root . '/storage/secure',
            $root . '/storage/conf/.secure',
        ];

        foreach ($candidates as $candidate) {
            if ($this->canUsePath($candidate)) {
                return $candidate;
            }
        }

        // letzter Versuch: bevor wir komplett scheitern, den ersten Kandidaten zurückgeben
        // damit die Fehlermeldung in ensureStorage den exakten Pfad zeigt.
        return $candidates[0];
    }

/**
 * Prueft, ob use path zutrifft.
 *
 * @param mixed $path Eingabewert fuer path.
 * @return bool True bei Erfolg, sonst false.
 */
private function canUsePath(string $path): bool
    {
        if (!is_dir($path)) {
            if (!@mkdir($path, 0700, true) && !is_dir($path)) {
                return false;
            }
        }

        @chmod($path, 0700);
        return is_readable($path) && is_writable($path);
    }
}
