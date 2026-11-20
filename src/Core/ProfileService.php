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

class ProfileService
{
    private string $basePath;
    private string $clientCertPath;
    private ?UserModel $userModel;

    /**
     * Setzt Vorlagen- und Zertifikatsspeicherpfade sowie optional das injizierte Benutzermodell.
     *
     * @param string|null $basePath Verzeichnis mit systembezogenen Client-Vorlagen.
     * @param string|null $clientCertPath Verzeichnis mit Zertifikatsdateien, standardmäßig aus `OPENVPN_CLIENT_CERT_DIR`.
     * @param UserModel|null $userModel Modell für Benutzer- und Rollenabfragen; null erzeugt es bei Bedarf.
     */
    public function __construct(?string $basePath = null, ?string $clientCertPath = null, ?UserModel $userModel = null)
    {
        $this->basePath = $basePath ?? dirname(__DIR__, 2) . '/storage/conf';
        $this->clientCertPath = $clientCertPath
            ?? (getenv('OPENVPN_CLIENT_CERT_DIR') ?: '/etc/openvpn/client-certificates');
        $this->userModel = $userModel;
    }

    /**
     * Prueft den vom Easy-RSA-Skript installierten privaten Benutzerschluessel.
     *
     * @return bool|null True bei vorhandenem Schluessel, false wenn er fehlt, null wenn die Pruefung nicht moeglich ist.
     */
    public function hasClientKey(int $uid): ?bool
    {
        if ($uid <= 0) {
            return null;
        }

        clearstatcache();
        if (!file_exists($this->clientCertPath) && !is_link($this->clientCertPath)) {
            $parent = dirname($this->clientCertPath);
            return is_dir($parent) && is_executable($parent) ? false : null;
        }
        $directory = realpath($this->clientCertPath);
        if ($directory === false || !is_dir($directory) || !is_readable($directory) || !is_executable($directory)) {
            return null;
        }

        $key = $directory . '/' . $uid . '.key';
        if (!file_exists($key) && !is_link($key)) {
            return false;
        }
        $path = realpath($key);
        if ($path === false || dirname($path) !== $directory || !is_file($path) || !is_readable($path)) {
            return null;
        }
        $size = filesize($path);
        return $size === false ? null : $size > 0;
    }

    /**
     * Liefert nur die Betriebssysteme mit vorhandener Vorlage.
     *
     * @return array<int, array{system: string, label: string}>
     */
    public function listSystems(): array
    {
        $labels = [
            'osx' => 'macOS',
            'linux' => 'Linux',
            'windows' => 'Windows',
        ];
        $systems = [];

        foreach ($labels as $system => $label) {
            $template = $this->basePath . '/' . $system . '/client.ovpn';
            $systemPath = realpath($this->basePath . '/' . $system);
            $templatePath = realpath($template);
            if ($systemPath !== false
                && $templatePath !== false
                && dirname($templatePath) === $systemPath
                && is_file($templatePath)
                && is_readable($templatePath)) {
                $systems[] = ['system' => $system, 'label' => $label];
            }
        }

        return $systems;
    }

    /**
     * Prueft Session-Benutzer und Rolle, erstellt ein ZIP vollstaendig im Speicher.
     *
     * @return array{filename: string, content: string}
     */
    public function createDownload(int $sessionUid, ?int $requestedUid, string $system): array
    {
        if ($sessionUid <= 0) {
            throw new \DomainException('session_uid_invalid');
        }

        $users = $this->userModel ?? new UserModel();
        $sessionUser = $users->getVpnProfileUser($sessionUid);
        if ($sessionUser === null || (int)($sessionUser['user_enable'] ?? 0) !== 1) {
            throw new \DomainException('session_user_unavailable');
        }

        $isAdmin = (int)$sessionUser['gid'] === 1 || $this->isAdminRole((string)$sessionUser['role_name']);
        $targetUid = $requestedUid ?? $sessionUid;
        if (!$isAdmin && $targetUid !== $sessionUid) {
            throw new \DomainException('target_uid_forbidden');
        }

        $targetUser = $targetUid === $sessionUid
            ? $sessionUser
            : $users->getVpnProfileUser($targetUid);
        if ($targetUser === null || !$this->isVpnActive($targetUser)) {
            throw new \RuntimeException('target_user_unavailable');
        }

        if (!in_array($system, ['osx', 'linux', 'windows'], true)) {
            throw new \RuntimeException('unsupported_system');
        }

        $availableSystems = array_column($this->listSystems(), 'system');
        if (!in_array($system, $availableSystems, true)) {
            throw new \RuntimeException('system_template_unavailable');
        }

        $files = [];
        $stage = 'configuration_template';
        try {
            $template = $this->readTemplate($system);

            $stage = 'client_certificate_files';
            $clientFiles = $this->readClientFiles($targetUid);

            $stage = 'client_certificate_validation';
            $this->validateClientFiles($clientFiles, $targetUid);

            $stage = 'configuration_assembly';
            $files['client.ovpn'] = $this->buildClientConfig($template, $clientFiles);

            $resolvScript = $this->readOptionalTemplateFile($system, 'update-resolv.sh');
            if ($resolvScript !== null) {
                $files['update-resolv.sh'] = $resolvScript;
            }

            $files['ca.crt'] = $clientFiles['ca'];
            $files['client.crt'] = $clientFiles['cert'];
            $files['client.key'] = $clientFiles['key'];
            $files['ta.key'] = $clientFiles['tls'];

            $stage = 'zip_creation';
            $archive = $this->createZipInMemory($files);
            $safeUsername = trim((string)preg_replace('/[^A-Za-z0-9._-]+/', '_', (string)$targetUser['user_name']), '._-');
            if ($safeUsername === '') {
                throw new \RuntimeException('Download nicht verfuegbar.');
            }

            return [
                'filename' => $safeUsername . '.' . date('Y-m-d') . '.openvpn-client.zip',
                'content' => $archive,
            ];
        } catch (\Throwable $e) {
            throw new \RuntimeException('Profile download failed at stage: ' . $stage, 0, $e);
        } finally {
            unset($files, $clientFiles, $template, $resolvScript, $archive);
        }
    }

    /** Prüft Aktivflag und inklusive Start-/Enddatum gegen das heutige Datum. */
    private function isVpnActive(array $user): bool
    {
        if ((int)($user['user_enable'] ?? 0) !== 1) {
            return false;
        }

        $today = date('Y-m-d');
        $startDate = (string)($user['user_start_date'] ?? '');
        $endDate = (string)($user['user_end_date'] ?? '');

        return ($startDate === '' || $startDate <= $today)
            && ($endDate === '' || $endDate >= $today);
    }

    /** Erkennt nur die explizit unterstützten Admin-Rollennamen. */
    private function isAdminRole(string $roleName): bool
    {
        return in_array(strtolower(trim($roleName)), [
            'admin',
            'admins',
            'administrator',
            'administrators',
            'administratoren',
        ], true);
    }

    /** Liest eine nichtleere Client-Vorlage und verhindert Pfadauflösung außerhalb des Systemordners. */
    private function readTemplate(string $system): string
    {
        $path = $this->basePath . '/' . $system . '/client.ovpn';
        $realBase = realpath($this->basePath . '/' . $system);
        $realPath = realpath($path);
        if ($realBase === false || $realPath === false || dirname($realPath) !== $realBase || !is_readable($realPath)) {
            throw new \RuntimeException('Download nicht verfuegbar.');
        }

        $content = file_get_contents($realPath);
        if (!is_string($content) || trim($content) === '') {
            throw new \RuntimeException('client_template_unreadable_or_empty');
        }

        return $content;
    }

    /** Liest eine optionale, lesbare Datei direkt im Vorlagenordner; fehlende Dateien ergeben null. */
    private function readOptionalTemplateFile(string $system, string $name): ?string
    {
        $directory = realpath($this->basePath . '/' . $system);
        $path = realpath($this->basePath . '/' . $system . '/' . $name);
        if ($path === false) {
            return null;
        }
        if ($directory === false || dirname($path) !== $directory || !is_file($path) || !is_readable($path)) {
            throw new \RuntimeException('Download nicht verfuegbar.');
        }

        $content = file_get_contents($path);
        if (!is_string($content)) {
            throw new \RuntimeException('Download nicht verfuegbar.');
        }

        return $content;
    }

    /**
     * @return array{ca: string, cert: string, key: string, tls: string}
     */
    private function readClientFiles(int $uid): array
    {
        if ($uid <= 0 || !is_dir($this->clientCertPath)) {
            throw new \RuntimeException('client_certificate_directory_unavailable');
        }

        $directory = realpath($this->clientCertPath);
        if ($directory === false || !is_readable($directory)) {
            throw new \RuntimeException('client_certificate_directory_unreadable');
        }

        $suffixes = [
            'cert' => '.crt',
            'key' => '.key',
            'ca' => '.ca.crt',
            'tls' => '.ta.key',
        ];
        $files = [];
        foreach ($suffixes as $name => $suffix) {
            $path = realpath($directory . '/' . $uid . $suffix);
            if ($path === false || dirname($path) !== $directory || !is_file($path) || !is_readable($path)) {
                throw new \RuntimeException('client_certificate_file_unavailable:' . $name);
            }

            $content = file_get_contents($path);
            if (!is_string($content) || trim($content) === '') {
                throw new \RuntimeException('client_certificate_file_empty:' . $name);
            }
            $files[$name] = $name === 'tls'
                ? $this->normalizeTlsAuthKey($content)
                : $content;
        }

        return $files;
    }

    /** Validiert X.509-Zertifikate, Gültigkeit, CA-Signatur, privaten Schlüssel und OpenVPN-TLS-Key-Format. */
    private function validateClientFiles(array $files, int $uid): void
    {
        $certificate = @openssl_x509_read($files['cert']);
        $caCertificate = @openssl_x509_read($files['ca']);
        $privateKey = @openssl_pkey_get_private($files['key']);
        if ($certificate === false || $caCertificate === false || $privateKey === false) {
            throw new \RuntimeException('Download nicht verfuegbar.');
        }

        $certificateInfo = @openssl_x509_parse($certificate);
        $caInfo = @openssl_x509_parse($caCertificate);
        $caKey = @openssl_pkey_get_public($caCertificate);
        $tlsKey = trim($files['tls']);
        if (!is_array($certificateInfo) || !is_array($caInfo) || $caKey === false || $privateKey === false) {
            throw new \RuntimeException('Download nicht verfuegbar.');
        }

        if ((string)($certificateInfo['subject']['CN'] ?? '') !== (string)$uid
            || (int)($certificateInfo['validTo_time_t'] ?? 0) <= time()
            || (int)($certificateInfo['validFrom_time_t'] ?? 0) > time()
            || (int)($caInfo['validTo_time_t'] ?? 0) <= time()
            || (int)($caInfo['validFrom_time_t'] ?? 0) > time()
            || @openssl_x509_verify($certificate, $caKey) !== 1
            || @openssl_x509_check_private_key($certificate, $privateKey) !== true
            || !str_starts_with($tlsKey, '-----BEGIN OpenVPN Static key V1-----')
            || !str_ends_with($tlsKey, '-----END OpenVPN Static key V1-----')) {
            throw new \RuntimeException('Download nicht verfuegbar.');
        }

        unset($certificate, $caCertificate, $privateKey, $caKey);
    }

    /** Extrahiert genau einen wohlgeformten OpenVPN Static key V1-Block und normalisiert Zeilenenden. */
    private function normalizeTlsAuthKey(string $key): string
    {
        $lines = preg_split('/\r\n|\n|\r/', trim($key));
        if (!is_array($lines)) {
            throw new \RuntimeException('tls_auth_key_unrecognized_format');
        }

        $begin = '-----BEGIN OpenVPN Static key V1-----';
        $end = '-----END OpenVPN Static key V1-----';
        $beginIndexes = array_keys($lines, $begin, true);
        $endIndexes = array_keys($lines, $end, true);
        if (count($beginIndexes) !== 1 || count($endIndexes) !== 1) {
            throw new \RuntimeException('tls_auth_key_unrecognized_format');
        }

        $beginIndex = $beginIndexes[0];
        $endIndex = $endIndexes[0];
        if ($endIndex <= $beginIndex) {
            throw new \RuntimeException('tls_auth_key_unrecognized_format');
        }

        foreach (array_merge(
            array_slice($lines, 0, $beginIndex),
            array_slice($lines, $endIndex + 1)
        ) as $line) {
            if (trim($line) !== '' && !str_starts_with(ltrim($line), '#')) {
                throw new \RuntimeException('tls_auth_key_unrecognized_format');
            }
        }

        $keyLines = array_slice($lines, $beginIndex + 1, $endIndex - $beginIndex - 1);
        if (count($keyLines) !== 16) {
            throw new \RuntimeException('tls_auth_key_unrecognized_format');
        }
        foreach ($keyLines as $line) {
            if (!preg_match('/\A[0-9a-fA-F]{32}\z/', $line)) {
                throw new \RuntimeException('tls_auth_key_unrecognized_format');
            }
        }

        return implode("\n", array_slice($lines, $beginIndex, $endIndex - $beginIndex + 1)) . "\n";
    }

    /** Entfernt dateibasierte und eingebettete Schlüssel-Direktiven und hängt die aktuellen Schlüsselblöcke an. */
    private function buildClientConfig(string $template, array $files): string
    {
        $template = (string)preg_replace(
            '/^\s*(?:ca|cert|key|tls-auth)\s+\S+.*(?:\R|$)/mi',
            '',
            $template
        );

        foreach (['ca', 'cert', 'key', 'tls-auth'] as $tag) {
            $template = (string)preg_replace(
                '/<' . preg_quote($tag, '/') . '>\s*[\s\S]*?\s*<\/' . preg_quote($tag, '/') . '>\s*/i',
                '',
                $template
            );
        }

        $blocks = [
            'ca' => $files['ca'],
            'cert' => $files['cert'],
            'key' => $files['key'],
            'tls-auth' => $files['tls'],
        ];
        foreach ($blocks as $tag => $content) {
            $template = rtrim($template) . "\n\n<{$tag}>\n" . trim($content) . "\n</{$tag}>\n";
        }

        return $template;
    }

    /**
     * Erstellt ein ZIP-Archiv ohne Datei- oder Temp-Verzeichniszugriffe.
     *
     * @param array<string, string> $files
     */
    private function createZipInMemory(array $files): string
    {
        $localRecords = '';
        $centralRecords = '';
        $offset = 0;
        $entryCount = 0;
        $dosTime = ((int)date('H') << 11) | ((int)date('i') << 5) | ((int)date('s') >> 1);
        $dosDate = (((int)date('Y') - 1980) << 9) | ((int)date('n') << 5) | (int)date('j');

        foreach ($files as $name => $content) {
            $nameLength = strlen($name);
            $contentLength = strlen($content);
            $compressed = gzdeflate($content);
            if ($compressed === false || $contentLength > 0xFFFFFFFF || strlen($compressed) > 0xFFFFFFFF) {
                throw new \RuntimeException('Download nicht verfuegbar.');
            }

            $compressedLength = strlen($compressed);
            $crc = crc32($content);
            $permissions = match (true) {
                $name === 'client.ovpn', $name === 'client.key', $name === 'ta.key' => 0x81800000,
                str_ends_with($name, '.sh') => 0x81ED0000,
                default => 0x81A40000,
            };
            $local = pack(
                'VvvvvvVVVvv',
                0x04034b50,
                20,
                0,
                8,
                $dosTime,
                $dosDate,
                $crc,
                $compressedLength,
                $contentLength,
                $nameLength,
                0
            ) . $name . $compressed;
            $centralRecords .= pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014b50,
                0x0314,
                20,
                0,
                8,
                $dosTime,
                $dosDate,
                $crc,
                $compressedLength,
                $contentLength,
                $nameLength,
                0,
                0,
                0,
                0,
                $permissions,
                $offset
            ) . $name;

            $localRecords .= $local;
            $offset += strlen($local);
            $entryCount++;
        }

        $centralOffset = strlen($localRecords);
        $centralSize = strlen($centralRecords);
        if ($entryCount > 0xFFFF || $centralOffset > 0xFFFFFFFF || $centralSize > 0xFFFFFFFF) {
            throw new \RuntimeException('Download nicht verfuegbar.');
        }

        return $localRecords . $centralRecords . pack(
            'VvvvvVVv',
            0x06054b50,
            0,
            0,
            $entryCount,
            $entryCount,
            $centralSize,
            $centralOffset,
            0
        );
    }
}
