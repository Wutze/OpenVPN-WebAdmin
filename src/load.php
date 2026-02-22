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

/**
 * Zentrales Bootstrap für das OpenVPN Webadmin
 * Wird von allen entrypoints (index.php, login.php, api.php …) eingebunden
 */

/**
 * .env frueh laden, damit config/config.php getenv('DEBUG') korrekt auswerten kann.
 */
$envPath = dirname(__DIR__) . '/.env';
if (is_file($envPath) && is_readable($envPath)) {
    $lines = @file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($lines)) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$k, $v] = array_map('trim', explode('=', $line, 2));
            if ($k === '') {
                continue;
            }

            $v = trim($v, "\"'");
            $_ENV[$k] = $v;
            putenv($k . '=' . $v);
        }
    }
}

// Konfiguration laden
$config = require dirname(__DIR__) . '/config/config.php';

require dirname(__DIR__) . '/vendor/autoload.php';

use Micro\OpenvpnWebadmin\Core\Database;
use Micro\OpenvpnWebadmin\Core\Session;
use Micro\OpenvpnWebadmin\Core\Lang;

/**
 * damit das Debugging überall verfügbar ist
 */
class_alias(\Micro\OpenvpnWebadmin\Core\Debug::class, 'Debug');
Debug::init($config['debug'] ? 'development' : 'production');

/**
 * alle anderen notwendigen Klassen zentral verfügbar
 */
if (class_exists(Lang::class))     class_alias(Lang::class, 'Lang');
if (class_exists(Database::class)) class_alias(Database::class, 'DB');
if (class_exists(Session::class))  class_alias(Session::class, 'Session');


/**
 * Baue DB Verbindung auf
 * @var mixed
 */
//$db = Database::getInstance($config['db'])->getConnection();

$db = Database::getInstance($config['db'])->getConnection();


/**
 * Session starten
 */
Session::start($db, $config['session']['lifetime']);

/**
 * Sprache laden
 */
Lang::init();

/**
 * Security headers
 */
$sitetoolsHost = '';
$sitetools = (string)($config['sitetools'] ?? '');
if ($sitetools !== '') {
    $parsedHost = parse_url($sitetools, PHP_URL_HOST);
    if (is_string($parsedHost) && $parsedHost !== '') {
        $sitetoolsHost = $parsedHost;
    }
}

$cspNonce = rtrim(strtr(base64_encode(random_bytes(18)), '+/', '-_'), '=');
define('_CSP_NONCE', $cspNonce);

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header('Cross-Origin-Opener-Policy: same-origin');
header('Cross-Origin-Resource-Policy: same-origin');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

$cspScriptSrc = ["'self'", "'nonce-{$cspNonce}'"];
$cspStyleSrc = ["'self'", "'unsafe-inline'"];
$cspFontSrc = ["'self'", 'data:'];
$cspImgSrc = ["'self'", 'data:', 'blob:'];
if ($sitetoolsHost !== '') {
    $cspScriptSrc[] = 'https://' . $sitetoolsHost;
    $cspStyleSrc[] = 'https://' . $sitetoolsHost;
    $cspFontSrc[] = 'https://' . $sitetoolsHost;
    $cspImgSrc[] = 'https://' . $sitetoolsHost;
}
header(
    'Content-Security-Policy: ' .
    "default-src 'self'; " .
    'script-src ' . implode(' ', array_unique($cspScriptSrc)) . '; ' .
    'style-src ' . implode(' ', array_unique($cspStyleSrc)) . '; ' .
    'font-src ' . implode(' ', array_unique($cspFontSrc)) . '; ' .
    'img-src ' . implode(' ', array_unique($cspImgSrc)) . '; ' .
    "connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'"
);



define('_SITETOOLS', $config['sitetools']);
define('_PROJECT_BASE_DIR', dirname(__DIR__));
define('_HTML_BASE_DIR', _PROJECT_BASE_DIR . '/html');

$configuredLoginPath = (string)($config['loginpath'] ?? 'login1');
if (!preg_match('/^[A-Za-z0-9_-]+$/', $configuredLoginPath)) {
    $configuredLoginPath = 'login1';
}

$candidateLoginDir = _HTML_BASE_DIR . '/' . $configuredLoginPath;
if (!is_dir($candidateLoginDir)) {
    $configuredLoginPath = 'login1';
}

define('_LOGINPATH', $configuredLoginPath);
define('_LOGIN_THEME_DIR', _HTML_BASE_DIR . '/' . _LOGINPATH);

//Debug::debug($config, _SITETOOLS);
