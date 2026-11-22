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
 * @version     2.0.1
 */

namespace Micro\OpenvpnWebadmin\Core;

/**
 * Vergleicht die installierte Version mit einer per DNS-TXT bereitgestellten Version.
 * DNS-Ergebnisse einschließlich fehlgeschlagener Abfragen werden zeitlich begrenzt
 * zwischengespeichert; der Service lädt keine Updates herunter.
 */
class VersionCheckService
{
    private const CACHE_TTL = 43200;

    private string $hostname;
    private string $cacheFile;
    private string $statusFile;
    private \Closure $dnsLookup;

    /**
     * Konfiguriert die Versionsprüfung und den hostbezogenen Cache.
     *
     * @param string $hostname DNS-Hostname für die TXT-Abfrage; Leerraum und abschließende Punkte werden entfernt.
     * @param string $cacheDirectory Verzeichnis für die JSON-Cachedatei.
     * @param string $statusFile JSON-Datei mit den installierten Versionen und dem Migrationsstatus.
     * @param callable|null $dnsLookup Optionale DNS-Abfrage für Tests; erhält den Hostnamen und liefert DNS-Datensätze oder false.
     */
    public function __construct(
        string $hostname,
        string $cacheDirectory,
        string $statusFile,
        ?callable $dnsLookup = null
    ) {
        $this->hostname = rtrim(trim($hostname), '.');
        $this->cacheFile = self::cacheFilePath($this->hostname, $cacheDirectory);
        $this->statusFile = $statusFile;
        $this->dnsLookup = $dnsLookup !== null
            ? \Closure::fromCallable($dnsLookup)
            : static fn(string $host): array|false => @dns_get_record($host, DNS_TXT);
    }

    /**
     * Vergleicht die höchste lokale Version mit dem Cache oder, falls nötig, einem DNS-TXT-Ergebnis.
     *
     * Ein gültiger Cacheeintrag verhindert für zwölf Stunden eine weitere DNS-Abfrage,
     * auch wenn die vorherige Abfrage keine Version geliefert hat.
     *
     * @return string|null Neuere Version im Format X.Y.Z oder null, wenn kein Hinweis angezeigt werden soll.
     */
    public function getNewerVersion(): ?string
    {
        if ($this->hostname === '' || filter_var($this->hostname, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false) {
            return null;
        }

        $installedVersion = $this->readInstalledVersion();
        if ($installedVersion === null) {
            return null;
        }

        $cached = $this->readCache();
        if ($cached !== null) {
            return $this->isNewer($cached['version'], $installedVersion) ? $cached['version'] : null;
        }

        $dnsVersion = $this->lookupVersion();
        $this->writeCache($dnsVersion);

        return $this->isNewer($dnsVersion, $installedVersion) ? $dnsVersion : null;
    }

    /**
     * Liest die gespeicherte Release-Version für das Update, ohne DNS abzufragen.
     *
     * Auch abgelaufene Einträge werden berücksichtigt. Ungültiges JSON wird gelöscht,
     * sofern die Datei seit dem Lesen nicht ausgetauscht wurde.
     *
     * @param string $hostname DNS-Hostname zur Auswahl der zugehörigen Cachedatei.
     * @param string $cacheDirectory Verzeichnis der JSON-Cachedatei.
     * @return string|null Gültige gespeicherte Version oder null bei fehlendem bzw. ungültigem Eintrag.
     * @throws \RuntimeException Bei ungültigem Hostnamen oder unsicherem bzw. fehlgeschlagenem Dateizugriff.
     */
    public static function getCachedReleaseVersion(string $hostname, string $cacheDirectory): ?string
    {
        $hostname = rtrim(trim($hostname), '.');
        if ($hostname === '' || filter_var($hostname, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false) {
            throw new \RuntimeException('Invalid release DNS host.');
        }

        $path = self::cacheFilePath($hostname, $cacheDirectory);
        if (is_link($path)) {
            throw new \RuntimeException('Version cache must not be a symlink.');
        }
        if (!file_exists($path)) {
            return null;
        }
        if (!is_file($path) || !is_readable($path)) {
            throw new \RuntimeException('Version cache is not a readable file.');
        }

        $before = stat($path);
        $contents = file_get_contents($path);
        if ($before === false || $contents === false) {
            throw new \RuntimeException('Cannot read version cache.');
        }
        $cache = json_decode($contents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            clearstatcache(true, $path);
            $after = lstat($path);
            if ($after === false || $before['dev'] !== $after['dev'] || $before['ino'] !== $after['ino']) {
                throw new \RuntimeException('Version cache changed while being read; retry the update.');
            }
            if (!unlink($path)) {
                throw new \RuntimeException('Cannot remove corrupt version cache.');
            }
            return null;
        }

        $entry = self::parseCache($cache);
        return $entry['version'] ?? null;
    }

    /**
     * Bildet den Cachedateipfad aus dem Verzeichnis und dem SHA-256-Hash des normalisierten Hostnamens.
     *
     * @param string $hostname Normalisierter DNS-Hostname.
     * @param string $cacheDirectory Verzeichnis für die Cachedatei.
     * @return string Pfad zur hostbezogenen JSON-Datei.
     */
    private static function cacheFilePath(string $hostname, string $cacheDirectory): string
    {
        return rtrim($cacheDirectory, '/') . '/version-check-' . hash('sha256', $hostname) . '.json';
    }

    /**
     * Ermittelt die höchste installierte Version anhand der Schlüssel der Statusdatei.
     *
     * Die Datei enthält Versionsschlüssel mit Listen ausgeführter Migrationen.
     * Fehlende, nicht lesbare oder ungültige Statusdaten werden protokolliert.
     *
     * @return string|null Höchste Version gemäß version_compare() oder null, wenn keine gefunden wird.
     */
    private function readInstalledVersion(): ?string
    {
        $contents = is_link($this->statusFile) ? false : @file_get_contents($this->statusFile);
        $status = is_string($contents) ? json_decode($contents) : null;
        if (!is_object($status) || get_object_vars($status) === []) {
            error_log('Version check: cannot read valid status file: ' . $this->statusFile);
            return null;
        }

        $latest = null;
        $startVersion = null;
        foreach ($status as $version => $migrations) {
            if (!preg_match('/\A[0-9]+\.[0-9]+\.[0-9]+\z/', $version) || !is_array($migrations)) {
                error_log('Version check: invalid status file: ' . $this->statusFile);
                return null;
            }
            foreach ($migrations as $migration) {
                if (!is_string($migration) || ($migration !== 'StartVersion' && !preg_match('/\A[0-9]+\.update\.sql\z/', $migration))) {
                    error_log('Version check: invalid status file: ' . $this->statusFile);
                    return null;
                }
                if ($migration === 'StartVersion') {
                    if ($startVersion !== null) {
                        error_log('Version check: invalid status file: ' . $this->statusFile);
                        return null;
                    }
                    $startVersion = $version;
                }
            }
            if ($latest === null || version_compare($version, $latest, '>')) {
                $latest = $version;
            }
        }
        if ($startVersion === null) {
            error_log('Version check: invalid status file: ' . $this->statusFile);
            return null;
        }
        foreach (array_keys(get_object_vars($status)) as $version) {
            if (version_compare($version, $startVersion, '<')) {
                error_log('Version check: invalid status file: ' . $this->statusFile);
                return null;
            }
        }

        return $latest;
    }

    /**
     * Fragt TXT-Datensätze ab und validiert deren Inhalt als vollständige Version X.Y.Z.
     *
     * Ungültige Datensätze werden ignoriert. Wiederholungen derselben Version sind
     * zulässig; mehrere unterschiedliche gültige Versionen sind nicht eindeutig.
     *
     * @return string|null Eindeutige gültige Version oder null bei Fehler bzw. fehlendem eindeutigen Ergebnis.
     */
    private function lookupVersion(): ?string
    {
        try {
            $records = ($this->dnsLookup)($this->hostname);
        } catch (\Throwable) {
            return null;
        }

        if (!is_array($records)) {
            return null;
        }

        $versions = [];
        foreach ($records as $record) {
            if (!is_array($record)) {
                continue;
            }
            $txt = $record['txt'] ?? null;
            if (is_string($txt) && preg_match('/\A[0-9]+\.[0-9]+\.[0-9]+\z/', $txt)) {
                $versions[$txt] = true;
            }
        }

        return count($versions) === 1 ? (string)array_key_first($versions) : null;
    }

    /**
     * Liest und validiert einen noch gültigen Cacheeintrag für die reguläre Versionsprüfung.
     *
     * Ein Zeitstempel in der Zukunft oder ein mindestens zwölf Stunden alter Eintrag
     * wird verworfen. Ein Array mit version = null bedeutet eine gespeicherte
     * erfolglose Abfrage und ist von einem fehlenden Cacheeintrag zu unterscheiden.
     *
     * @return array{version: string|null}|null Gültiger Eintrag oder null, wenn der Cache nicht verwendbar ist.
     */
    private function readCache(): ?array
    {
        if (is_link($this->cacheFile) || !is_file($this->cacheFile)) {
            return null;
        }

        $contents = @file_get_contents($this->cacheFile);
        $cache = is_string($contents) ? self::parseCache(json_decode($contents, true)) : null;
        if ($cache === null) {
            return null;
        }

        $now = time();
        if ($cache['checked_at'] > $now || $cache['checked_at'] <= $now - self::CACHE_TTL) {
            return null;
        }

        return ['version' => $cache['version']];
    }

    /**
     * Prüft Struktur und Werte eines dekodierten JSON-Cacheeintrags, unabhängig von dessen Alter.
     *
     * @param mixed $cache Dekodierte Daten mit ganzzahligem Unix-Zeitstempel checked_at und version als X.Y.Z oder null.
     * @return array{checked_at: int, version: string|null}|null Validierter Eintrag oder null bei ungültigen Daten.
     */
    private static function parseCache(mixed $cache): ?array
    {
        if (
            !is_array($cache)
            || !isset($cache['checked_at'])
            || !is_int($cache['checked_at'])
            || !array_key_exists('version', $cache)
        ) {
            return null;
        }

        $version = $cache['version'];
        if ($version !== null && (!is_string($version) || !preg_match('/\A[0-9]+\.[0-9]+\.[0-9]+\z/', $version))) {
            return null;
        }

        return ['checked_at' => $cache['checked_at'], 'version' => $version];
    }

    /**
     * Speichert den aktuellen Zeitstempel und das DNS-Ergebnis über eine temporäre JSON-Datei.
     *
     * Auch null wird als erfolgloses Ergebnis gespeichert. Dateisystemfehler werden
     * stillschweigend behandelt, damit sie den normalen Betrieb nicht beeinträchtigen.
     *
     * @param string|null $version Gültige DNS-Version im Format X.Y.Z oder null.
     */
    private function writeCache(?string $version): void
    {
        $directory = dirname($this->cacheFile);
        if (is_link($directory) || (!is_dir($directory) && !@mkdir($directory, 0770, true) && !is_dir($directory))) {
            return;
        }

        $temporaryFile = @tempnam($directory, 'version-check-');
        if ($temporaryFile === false) {
            return;
        }

        $contents = json_encode(['checked_at' => time(), 'version' => $version]);
        if (!is_string($contents) || @file_put_contents($temporaryFile, $contents, LOCK_EX) === false || !@rename($temporaryFile, $this->cacheFile)) {
            @unlink($temporaryFile);
            return;
        }
        @chmod($this->cacheFile, 0660);
    }

    /**
     * Vergleicht die verfügbare mit der installierten Version mittels version_compare().
     *
     * @param string|null $availableVersion Verfügbare Version oder null, wenn keine ermittelt wurde.
     * @param string $installedVersion Installierte Version.
     * @return bool True nur, wenn eine verfügbare Version neuer als die installierte ist.
     */
    private function isNewer(?string $availableVersion, string $installedVersion): bool
    {
        return $availableVersion !== null && version_compare($availableVersion, $installedVersion, '>');
    }
}
