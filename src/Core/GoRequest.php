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

use Micro\OpenvpnWebadmin\Templates\Account;
use Micro\OpenvpnWebadmin\Templates\Config;
use Micro\OpenvpnWebadmin\Templates\Dashboard;
use Micro\OpenvpnWebadmin\Templates\Log;
use Micro\OpenvpnWebadmin\Templates\Profiles;
use Micro\OpenvpnWebadmin\Templates\Settings;
use Micro\OpenvpnWebadmin\Templates\Template;
use Micro\OpenvpnWebadmin\Templates\User;

class GoRequest
{
    private string $action = 'main';
    private array $values = [];

    /**
     * Setzt dynamisch eine Eigenschaft der Klasse auf den uebergebenen Wert.
     *
     * @param mixed $key
     * @param mixed $val
     * @return void
     */
public function set_value(string $key, $val): void
    {
        $this->$key = $val;
    }

    /**
     * Der zentrale handler/router in der Verarbeitung
     * über diese Datei werden alle Anfragen an das System "geroutet"
     * und entsprechend auf die erlaubten "Operationen" (op) hin überprüft
     * Verteilt dann auf die entsprechenden Klassen und Funktionen
     *
     * @return void
     */
public function main(): void
    {
        $allowedOps = [
            'login', 'checklogin', 'logout', 'main', 'data', 'live',
            'users', 'dashboard', 'logs', 'account', 'config', 'settings',
            'profiles', 'download', 'setlang', 'loginasset'
        ];

        if (!in_array($this->action, $allowedOps, true)) {
            $this->showError();
            return;
        }

        foreach ($_POST as $k => $v) {
            $this->values[$k] = $v;
        }
        $this->enforceAccessPolicy();

        switch ($this->action) {
            case 'login':
                $this->showLogin();
                break;
            case 'checklogin':
                $this->checkLogin();
                break;
            case 'loginasset':
                $this->serveLoginAsset();
                break;
            case 'logout':
                $this->logout();
                break;
            case 'data':
                (new DataController())->handle();
                break;
            case 'live':
                $this->showLive();
                break;
            case 'users':
                $this->ensureAdminOrForbidden();
                $this->showUsers();
                break;
            case 'logs':
                $this->ensureAdminOrForbidden();
                $this->showLogs();
                break;
            case 'config':
                $this->ensureAdminOrForbidden();
                $this->showConfig();
                break;
            case 'settings':
                $this->ensureAdminOrForbidden();
                $this->showSettings();
                break;
            case 'account':
                $this->showAccount();
                break;
            case 'profiles':
                $this->showProfiles();
                break;
            case 'download':
                $this->downloadProfileZip();
                break;
            case 'setlang':
                $this->setLanguage();
                break;
            case 'main':
            case 'dashboard':
            default:
                $this->showMain();
                break;
        }
    }

    /**
     * Zeigt die Login-Seite ueber den LoginController an.
     *
     * @return void
     */
private function showLogin(): void
    {
        (new LoginController())->showLogin();
    }

    /**
     * Verarbeitet den Login-Versuch ueber den LoginController.
     *
     * @return void
     */
private function checkLogin(): void
    {
        (new LoginController())->handleLogin();
    }

    /**
     * Baut die gemeinsamen Template-Daten fuer Seitenaufrufe auf.
     *
     * @param mixed $activeOp
     * @return array
     */
private function baseTemplateData(string $activeOp): array
    {
        $data = [
            'user' => Session::getUser(),
            'activeOp' => $activeOp,
            'currentLang' => Lang::getCurrent(),
            'availableLangs' => Lang::getAvailableLanguages(),
        ];

        if ($this->canUseDebugModal()) {
            $data['debugModal'] = $this->buildDebugModalData();
        }

        return $data;
    }

    /**
     * Rendert eine komplette Seite mit Layout, Header und Inhalt.
     *
     * @param mixed $title
     * @param mixed $content
     * @param mixed $activeOp
     * @return void
     */
private function renderPage(string $title, string $content, string $activeOp): void
    {
        $tpl = new Template($title, $content, $this->baseTemplateData($activeOp));
        echo $tpl->render();
    }

    /**
     * Zeigt die Dashboard-Seite an.
     *
     * @return void
     */
private function showMain(): void
    {
        $content = (new Dashboard())->index();
        $this->renderPage('OpenVPN WebAdmin - Dashboard', $content, 'dashboard');
    }

    /**
     * Zeigt die Benutzerverwaltung an.
     *
     * @return void
     */
private function showUsers(): void
    {
        $content = (new User())->index();
        $this->renderPage('OpenVPN WebAdmin - Benutzer', $content, 'users');
    }

    /**
     * Zeigt die Log-Ansicht an.
     *
     * @return void
     */
private function showLogs(): void
    {
        $content = (new Log())->index();
        $this->renderPage('OpenVPN WebAdmin - Logs', $content, 'logs');
    }

    /**
     * Zeigt den Konfigurations-Editor fuer Client-Profile an.
     *
     * @return void
     */
private function showConfig(): void
    {
        $content = (new Config())->index();
        $this->renderPage('OpenVPN WebAdmin - Konfiguration', $content, 'config');
    }

    /**
     * Zeigt den Editor fuer die VPN-Server-Einstellungen an.
     *
     * @return void
     */
private function showSettings(): void
    {
        $content = (new Settings())->index();
        $this->renderPage('OpenVPN WebAdmin - Settings', $content, 'settings');
    }

    /**
     * Zeigt die Seite fuer Konfigurations-Downloads an.
     *
     * @return void
     */
private function showProfiles(): void
    {
        $content = (new Profiles())->index();
        $this->renderPage('OpenVPN WebAdmin - Konfigurationsdateien', $content, 'profiles');
    }

    /**
     * Zeigt die Seite zum Verwalten des eigenen Accounts an.
     *
     * @return void
     */
private function showAccount(): void
    {
        $content = (new Account())->index();
        $this->renderPage('OpenVPN WebAdmin - Mein Account', $content, 'account');
    }

    /**
     * Liefert eine einfache Live-Status-Antwort als JSON.
     *
     * @return void
     */
private function showLive(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['online' => rand(0, 10)]);
    }

    /**
     * Meldet den Benutzer ab und antwortet je nach Request-Typ mit Redirect oder JSON.
     *
     * @return void
     */
private function logout(): void
    {
        $isAjax = (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) || (
            isset($_SERVER['CONTENT_TYPE']) &&
            stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
        );

        Session::destroyAll();

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'ok']);
            return;
        }

        header('Location: ?op=login');
        exit;
    }

    /**
     * Prueft Adminrechte und zeigt sonst eine Zugriff-verweigert-Seite an.
     *
     * @return void
     */
private function ensureAdminOrForbidden(): void
    {
        if (!Session::isAdmin()) {
            $content = '<div class="alert alert-danger"><h4>' . Lang::get('_ACCESS_DENIED') . '</h4><p>' . Lang::get('_ACCESS_DENIED_TEXT') . '</p></div>';
            $this->renderPage('OpenVPN WebAdmin - ' . Lang::get('_ACCESS_DENIED'), $content, 'dashboard');
            exit;
        }
    }

    /**
     * Liefert die angeforderte Profil-ZIP zum Download aus.
     *
     * @return void
     */
private function downloadProfileZip(): void
    {
        $system = (string)($_GET['system'] ?? '');
        $service = new ProfileService();

        try {
            $zipPath = $service->getZipPath($system);
        } catch (\Throwable $e) {
            http_response_code(404);
            echo 'Datei nicht gefunden: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            return;
        }

        $fileName = basename($zipPath);
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . (string)filesize($zipPath));
        readfile($zipPath);
        exit;
    }

    /**
     * Zeigt die Fehlerseite aus dem Theme oder einen Text-Fallback an.
     *
     * @return void
     */
private function showError(): void
    {
        $errorFile = (defined('_LOGIN_THEME_DIR') ? _LOGIN_THEME_DIR : dirname(__DIR__, 2) . '/html/login1') . '/error.php';
        if (is_file($errorFile)) {
            require $errorFile;
            return;
        }

        echo Lang::get('_ERROR_PAGE_NOT_FOUND');
    }

    /**
     * Setzt die gewaehlte Sprache und leitet sicher auf die vorherige Seite zurueck.
     *
     * @return void
     */
private function setLanguage(): void
    {
        $lang = (string)($_GET['lang'] ?? '');
        if ($lang !== '') {
            Lang::setCurrent($lang);
        }

        $target = (string)($_SERVER['HTTP_REFERER'] ?? '?op=dashboard');
        if ($target === '' || !$this->isSafeRedirectTarget($target)) {
            $target = '?op=dashboard';
        }

        header('Location: ' . $target);
        exit;
    }

    /**
     * Kurzbeschreibung Funktion isSafeRedirectTarget
     *
     * @param mixed $target
     * @return bool
     */
private function isSafeRedirectTarget(string $target): bool
    {
        if (str_starts_with($target, '?') || str_starts_with($target, '/')) {
            return true;
        }

        $parts = parse_url($target);
        if (!is_array($parts)) {
            return false;
        }
        $targetHost = (string)($parts['host'] ?? '');
        $currentHost = (string)($_SERVER['HTTP_HOST'] ?? '');
        if ($targetHost === '' || $currentHost === '') {
            return false;
        }

        return strcasecmp($targetHost, $currentHost) === 0;
    }

    /**
     * Erzwingt zentrale Zugriffsregeln fuer Login, Rollen, Origin und CSRF.
     *
     * Security-Matrix (Single Source of Truth):
     *
     * OP               Login required   Admin required   CSRF+Origin on POST
     * ----------------------------------------------------------------------
     * login            no               no               no
     * checklogin       no               no               no
     * setlang          yes              no               no (GET only)
     * logout           yes              no               yes
     * dashboard/main   yes              no               n/a
     * account          yes              no               n/a
     * profiles         yes              no               n/a
     * download         yes              no               n/a
     * users            yes              yes              n/a
     * logs             yes              yes              n/a
     * config           yes              yes              n/a
     * settings         yes              yes              n/a
     * data(select=*)   yes              depends          yes for POST
     *
     * data-select admin-only:
     * user, log, config, settings, dashboard_stats, diag_login
     *
     * @return void
     */
private function enforceAccessPolicy(): void
    {
        $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $isAuthFree = in_array($this->action, ['login', 'checklogin', 'loginasset'], true);

        if (!$isAuthFree && !Session::isUser()) {
            Debug::log('AUTH redirect to login', [
                'action' => $this->action,
                'session_id' => session_id(),
                'cookie_present' => isset($_COOKIE[session_name()]),
                'cookie_name' => session_name(),
            ]);
            header('Location: ?op=login');
            exit;
        }

        if ($this->action === 'data') {
            $this->enforceDataAccessPolicy($method);
            return;
        }

        // Central protection for state-changing non-data operations (e.g. logout).
        if ($method === 'POST' && !$isAuthFree) {
            $this->verifyStateChangingRequest();
        }

        if (in_array($this->action, ['users', 'logs', 'config', 'settings'], true)) {
            $this->ensureAdminOrForbidden();
        }
    }

    /**
     * Liefert erlaubte Login-Assets sicher aus dem Theme-Verzeichnis aus.
     *
     * @return void
     */
private function serveLoginAsset(): void
    {
        $file = (string)($_GET['file'] ?? '');
        if ($file === '' || str_contains($file, "\0") || str_starts_with($file, '/')) {
            http_response_code(400);
            echo 'Bad request';
            exit;
        }

        if (str_contains($file, '../') || str_contains($file, '..\\')) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }

        $themeDir = defined('_LOGIN_THEME_DIR') ? _LOGIN_THEME_DIR : dirname(__DIR__, 2) . '/html/login1';
        $assetPath = $themeDir . '/' . ltrim($file, '/');

        $realThemeDir = realpath($themeDir);
        $realAssetPath = realpath($assetPath);
        if ($realThemeDir === false || $realAssetPath === false || strpos($realAssetPath, $realThemeDir) !== 0 || !is_file($realAssetPath)) {
            http_response_code(404);
            echo 'Not found';
            exit;
        }

        $ext = strtolower(pathinfo($realAssetPath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'css' => 'text/css; charset=utf-8',
            'js' => 'application/javascript; charset=utf-8',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            default => '',
        };

        if ($mime === '') {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . (string)filesize($realAssetPath));
        readfile($realAssetPath);
        exit;
    }

    /**
     * Wendet Zugriffskontrollen fuer data-Requests anhand von select und Methode an.
     *
     * @param mixed $method
     * @return void
     */
private function enforceDataAccessPolicy(string $method): void
    {
        $select = (string)($_GET['select'] ?? '');
        $adminSelects = ['user', 'log', 'config', 'settings', 'dashboard_stats', 'diag_login'];
        if (in_array($select, $adminSelects, true)) {
            $this->ensureAdminOrForbidden();
        }

        if ($method === 'POST') {
            $this->verifyStateChangingRequest();
        }
    }

    /**
     * Prueft zustandsaendernde Requests auf gleiche Herkunft und gueltiges CSRF-Token.
     *
     * @return void
     */
private function verifyStateChangingRequest(): void
    {
        if (!$this->isSameOriginRequest()) {
            $this->denyRequest(403, Lang::get('_API_INVALID_ORIGIN'));
        }

        $csrf = $this->getRequestCsrfToken();
        if (!Session::verifyCsrfToken($csrf)) {
            $this->denyRequest(403, Lang::get('_API_INVALID_CSRF'));
        }
    }

    /**
     * Ermittelt, ob die Anfrage von derselben Origin bzw. demselben Host stammt.
     *
     * @return bool
     */
private function isSameOriginRequest(): bool
    {
        $host = (string)($_SERVER['HTTP_HOST'] ?? '');
        if ($host === '') {
            return false;
        }

        $origin = (string)($_SERVER['HTTP_ORIGIN'] ?? '');
        if ($origin !== '') {
            $originHost = parse_url($origin, PHP_URL_HOST);
            return is_string($originHost) && strcasecmp($originHost, $host) === 0;
        }

        $referer = (string)($_SERVER['HTTP_REFERER'] ?? '');
        if ($referer !== '') {
            $refererHost = parse_url($referer, PHP_URL_HOST);
            return is_string($refererHost) && strcasecmp($refererHost, $host) === 0;
        }

        return false;
    }

    /**
     * Bricht die Anfrage mit Fehlerstatus ab und gibt JSON oder HTML-Fehler aus.
     *
     * @param mixed $status
     * @param mixed $message
     * @return void
     */
private function denyRequest(int $status, string $message): void
    {
        http_response_code($status);
        if ($this->action === 'data') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => $message]);
            exit;
        }

        $content = '<div class="alert alert-danger"><h4>' . Lang::get('_ACCESS_DENIED') . '</h4><p>' .
            htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p></div>';
        $this->renderPage('OpenVPN WebAdmin - ' . Lang::get('_ACCESS_DENIED'), $content, 'dashboard');
        exit;
    }

    /**
     * Liest das CSRF-Token aus POST-Daten oder einem JSON-Body aus.
     *
     * @return string
     */
private function getRequestCsrfToken(): string
    {
        if (isset($_POST['_csrf']) && is_string($_POST['_csrf'])) {
            return (string)$_POST['_csrf'];
        }

        $contentType = (string)($_SERVER['CONTENT_TYPE'] ?? '');
        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            if (is_string($raw) && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded) && isset($decoded['_csrf']) && is_string($decoded['_csrf'])) {
                    return $decoded['_csrf'];
                }
            }
        }

        return '';
    }

    /**
     * Nur Admin mit gesetztem DEBUG=true darf das Debug-Modal sehen.
     */
    private function canUseDebugModal(): bool
    {
        if (!Session::isAdmin()) {
            return false;
        }

        $raw = getenv('DEBUG');
        if ($raw === false || $raw === null || $raw === '') {
            $raw = $_ENV['DEBUG'] ?? '';
        }
        if ($raw === false || $raw === null || $raw === '') {
            $raw = $this->readDotEnvValue('DEBUG');
        }

        return filter_var($raw, FILTER_VALIDATE_BOOL);
    }

    /**
     * Liest einen einzelnen Key aus der .env Datei (falls vorhanden).
     */
    private function readDotEnvValue(string $key): string
    {
        $baseDir = defined('_PROJECT_BASE_DIR') ? _PROJECT_BASE_DIR : dirname(__DIR__, 2);
        $envPath = $baseDir . '/.env';
        if (!is_file($envPath) || !is_readable($envPath)) {
            return '';
        }

        $lines = @file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!is_array($lines)) {
            return '';
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }
            if (!str_contains($trimmed, '=')) {
                continue;
            }

            [$k, $v] = array_map('trim', explode('=', $trimmed, 2));
            if ($k !== $key) {
                continue;
            }

            $v = trim($v, "\"'");
            return $v;
        }

        return '';
    }

    /**
     * Baut die Inhalte für das Debug-Modal.
     *
     * @return array<string, mixed>
     */
    private function buildDebugModalData(): array
    {
        $baseDir = defined('_PROJECT_BASE_DIR') ? _PROJECT_BASE_DIR : dirname(__DIR__, 2);
        $debugPath = $baseDir . '/storage/logs/debug.log';
        $exceptionPathPrimary = $baseDir . '/storage/logs/exceptions.log';
        $exceptionPathLegacy = $baseDir . '/storage/logs/exeptions.log';
        $exceptionPath = is_file($exceptionPathPrimary) ? $exceptionPathPrimary : $exceptionPathLegacy;

        return [
            'enabled' => true,
            'debug_log_path' => $debugPath,
            'debug_log_content' => $this->readDebugFile($debugPath),
            'exceptions_log_path' => $exceptionPath,
            'exceptions_log_content' => $this->readDebugFile($exceptionPath),
            'go_request_vars' => $this->getGoRequestDebugVars(),
        ];
    }

    /**
     * Liest eine Logdatei robust ein und begrenzt sehr große Inhalte.
     */
    private function readDebugFile(string $path): string
    {
        if ($path === '' || !is_file($path)) {
            return 'Datei nicht gefunden.';
        }

        $content = @file_get_contents($path);
        if (!is_string($content)) {
            return 'Datei konnte nicht gelesen werden.';
        }

        $maxBytes = 200000;
        if (strlen($content) > $maxBytes) {
            $content = substr($content, -$maxBytes);
            return "[Ausgabe gekuerzt auf letzte {$maxBytes} Bytes]\n\n" . $content;
        }

        return $content === '' ? '(Datei ist leer)' : $content;
    }

    /**
     * Liefert die in GoRequest erzeugten/verwalteten Variablen fuer Debug.
     *
     * @return array<string, mixed>
     */
    private function getGoRequestDebugVars(): array
    {
        return [
            'object_vars' => get_object_vars($this),
            'request_method' => (string)($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            'get' => $_GET,
            'post' => $_POST,
            'user' => Session::getUser(),
        ];
    }
}
