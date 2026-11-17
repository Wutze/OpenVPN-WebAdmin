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

class CaTlsService
{
private string $basePath;
private string $caPath;
private string $tlsPath;

/**
 * Setzt Speicherpfade für CA- und TLS-Datei; ohne Basis nimmt es den ersten nutzbaren Standardpfad.
 *
 * @param string|null $basePath Optionales Speicherverzeichnis.
 */
public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? $this->resolveUsableBasePath();
        $this->caPath = $this->basePath . '/client_ca.pem';
        $this->tlsPath = $this->basePath . '/client_tls.key';
    }

/**
 * Stellt Speicher und Dateien sicher und liest beide Schlüsselmaterialien.
 *
 * @return array{ca: string, tls: string, ca_path: string, tls_path: string} Inhalte und Pfade der Dateien.
 * @throws \RuntimeException Wenn das Speicherverzeichnis oder eine Datei nicht angelegt werden kann.
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
 * Schreibt CA- und TLS-Inhalt jeweils über eine temporäre Datei mit restriktiven Rechten.
 *
 * @param string $caContent Inhalt der CA-Datei.
 * @param string $tlsContent Inhalt der TLS-Authentifizierungsdatei.
 * @return array{ca_path: string, tls_path: string} Speicherorte der geschriebenen Dateien.
 * @throws \RuntimeException Wenn Verzeichnis oder temporäre Dateien nicht geschrieben/umbenannt werden können.
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
 * Erstellt das Speicherverzeichnis bei Bedarf, prüft Zugriffsrechte und setzt Modus 0700.
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
 * Legt eine fehlende Schlüsseldatei leer an und setzt Modus 0600.
 *
 * @param string $path Zu prüfender Datei-Pfad.
 * @return void Kein Rueckgabewert.
 * @throws \RuntimeException Wenn eine fehlende Datei nicht erstellt werden kann.
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
 * Schreibt Inhalt in eine temporäre Datei im Speicherverzeichnis und ersetzt damit das Ziel.
 *
 * @param string $path Zieldatei.
 * @param string $content Zu speichernder Schlüsselinhalt.
 * @return void Kein Rueckgabewert.
 * @throws \RuntimeException Wenn temporäres Schreiben oder Umbenennen fehlschlägt.
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
 * Prüft `storage/secure` und danach `storage/conf/.secure`, indem es fehlende Verzeichnisse anzulegen versucht.
 *
 * @return string Erster nutzbarer Kandidat oder `storage/secure`, damit ein späterer Fehler den Pfad benennt.
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
 * Erstellt bei Bedarf den Pfad, setzt Modus 0700 und prüft Les- und Schreibbarkeit.
 *
 * @param string $path Zu prüfender Verzeichnispfad.
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
