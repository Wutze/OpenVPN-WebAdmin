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
