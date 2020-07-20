<?php
/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020 OpenVPN-WebAdmin
 *
 * NOTICE OF LICENSE
 *
 * GNU AFFERO GENERAL PUBLIC LICENSE V3
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/agpl-3.0.en.html
 *
 * @fork Original Idea and parts in this script from: https://github.com/Chocobozzz/OpenVPN-Admin
 * 
 * @author    Wutze
 * @copyright 2020 OpenVPN-WebAdmin
 * @link			https://github.com/Wutze/OpenVPN-WebAdmin
 * @see				Internal Documentation ~/doc/
 * @version		1.0.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 * ! If possible, do not change the file, otherwise the update process will be interrupted !
 */

/* Direktaufruf verhindern */
(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');
define('MainFileLoaded', true);

/* Cookie-Lebenszeit für anonyme User */
define('SESSION_LIFETIME_NOUSER',"1440");
define("SESSION_LIFETIME", (6 * 24 * 60 * 60)); # Standard 6 Stunden (ersten Wert ändern, falls nötig)
define("SETINACTIVE_MINS", (5 * 60)); # Standard 5 Minuten
define("COOKIE_LIFETIME", (30 * 24 * 60 * 60)); # 30 Tage

$_SERVER['HTTP_USER_AGENT'] = strip_tags($_SERVER['HTTP_USER_AGENT']);
define('USER_AGENT', $_SERVER['HTTP_USER_AGENT']);


require_once(REAL_BASE_DIR."/include/config.php");
## User and Database Data
define('_DB_UNAME',$dbuser);
define('_DB_PW',$dbpass);
define('_DB_DB',$dbname);
define('_DB_SERVER',$dbhost);
define('_DB_TYPE',$dbtype);
define('_DB_PORT',$dbport);
define('_DB_DEBUG',$dbdebug);
define('_SESSION_DEBUG',$sessdebug);

/** define and load adodb */
define('_ADODB',REAL_BASE_DIR."/include/ADOdb/");
include_once(_ADODB.'/adodb.inc.php');

/* load classes and functions */
include_once(REAL_BASE_DIR."/include/class/class.session.php");
include_once(REAL_BASE_DIR."/include/class/class.language.php");
include_once(REAL_BASE_DIR."/include/class/class.request.php");
include_once(REAL_BASE_DIR."/include/class/class.secure.php");
include_once(REAL_BASE_DIR."/include/functions.php");
require_once(REAL_BASE_DIR.'/include/class/class.data.php');
require_once(REAL_BASE_DIR.'/include/class/class.livedata.php');
require_once(REAL_BASE_DIR.'/include/class/class.jsonObject.php');
require_once(REAL_BASE_DIR.'/include/class/class.configfiles.php');

ob_start();
/* start session */
$se = new websessions;
$se->define_session();
$se->start_session();
/* set default Language */
(!session::GetVar('lang')) ? session::SetVar('lang',_DEFAULT_LANGUAGE) : "";
(!session::GetVar('session_id')) ? session::SetVar('session_id',SESSION_NAME) : "";
(!session::GetVar('isuser')) ? session::SetVar('isuser',session::GetVar('isuser')) : "";

 ?>