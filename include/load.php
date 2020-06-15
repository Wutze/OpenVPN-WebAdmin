<?php
/**
 * this File is part of OpenVPN-Admin - (c) 2020 OpenVPN-Admin
 *
 * NOTICE OF LICENSE
 *
 * GNU AFFERO GENERAL PUBLIC LICENSE V3
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/agpl-3.0.en.html
 *
 * Original Script from: https://github.com/Chocobozzz/OpenVPN-Admin
 *
 * @fork      https://github.com/Wutze/OpenVPN-Admin
 * @author    Wutze
 * @copyright 2020 OpenVPN-Admin
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

/* Direktaufruf verhindern */
(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');
define('MainFileLoaded', true);

/* Site-Name */
define('_SITE_NAME',"OVPN-WebAdmin");
define('HOME_URL',"vpn.home");
define('_DEFAULT_LANGUAGE','de_DE');

/** Login Site */
define('_LOGINSITE','login1');

/* Cookie-Lebenszeit für anonyme User */
define('SESSION_LIFETIME_NOUSER',"1440");
define("SESSION_LIFETIME", (6 * 24 * 60 * 60)); # Standard 6 Stunden (ersten Wert ändern, falls nötig)
define("SETINACTIVE_MINS", (5 * 60)); # Standard 5 Minuten
define("COOKIE_LIFETIME", (30 * 24 * 60 * 60)); # 30 Tage

$_SERVER['HTTP_USER_AGENT'] = strip_tags($_SERVER['HTTP_USER_AGENT']);
define('USER_AGENT', $_SERVER['HTTP_USER_AGENT']);


require_once(REAL_BASE_DIR."/include/config.php");
## User and Database Data
define('_DB_UNAME',$user);
define('_DB_PW',$pass);
define('_DB_DB',$db);
define('_DB_SERVER',$host);
define('_DB_TYPE',"mysqli");
define('_DB_PORT',$port);
define('_DB_DEBUG',TRUE);
define('_SESSION_DEBUG',false);

# Backward compatibility
#$options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
#$bdd = new PDO("mysql:host="._DB_SERVER.";port="._DB_PORT.";dbname="._DB_DB."", _DB_UNAME, _DB_PW, $options);


define('_ADODB',REAL_BASE_DIR."/include/ADOdb/");

/* Dateien laden */
include_once(REAL_BASE_DIR."/include/class/class.session.php");
include_once(REAL_BASE_DIR."/include/class/class.language.php");
include_once(REAL_BASE_DIR."/include/class/class.request.php");
include_once(REAL_BASE_DIR."/include/class/class.secure.php");
include_once(REAL_BASE_DIR."/include/functions.php");
require_once(REAL_BASE_DIR.'/include/class/class.data.php');
require_once(REAL_BASE_DIR.'/include/class/class.livedata.php');
require_once(REAL_BASE_DIR.'/include/class/class.jsonObject.php');
require_once(REAL_BASE_DIR.'/include/class/class.configfiles.php');

include_once(_ADODB.'/adodb.inc.php');
#$data = newAdoConnection(_DB_TYPE);
#$data->connect(_DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB);

## Sprachfiles fest eingebaut
#include_once(REAL_BASE_DIR."/include/lang/de_DE/lang.php");


ob_start();
/* Session starten */
$se = new websessions;
$se->define_session();
$se->start_session();
/* setze default Language */
(!session::GetVar('lang')) ? session::SetVar('lang',_DEFAULT_LANGUAGE) : "";
(!session::GetVar('session_id')) ? session::SetVar('session_id',SESSION_NAME) : "";
(!session::GetVar('isuser')) ? session::SetVar('isuser',session::GetVar('isuser')) : "";

 ?>