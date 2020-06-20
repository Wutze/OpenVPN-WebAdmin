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
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');
/**
 * Language en_EN
 */

$message['_NO_USER']                = "Unknown user name";
$message['_SITE_TITLE']             = "Page title";
$message['_LANGUGE_ERROR']          = "Error in the language file";

$message['_HOME']                   = "Home";
$message['_OVERVIEW']               = "Overview";
$message['_INFOS_PLUS']             = "Additional information";

$message['_VIEW']                   = "view";

$message['_LOGIN']                  = "Login";
$message['_LOGIN_NAME']             = "Username";
$message['_LOGIN_PASS']             = "Password";
$message['_LOGIN_DATA']             = "Please enter login data";
$message['_LOGIN_SAVE']             = "remember login";
$message['_LOGOUT']                 = "X";
$message['_YOUR_LOGIN']             = "Your account";
$message['_USER_RIGHTS']            = "Your User rights";

$message['_SAVE_PW']                = "Save";
$message['_LOGIN_PASS_CONTROL']     = "Control";
$message['_CHANGE_PASS']            = "change password";
$message['_INPUT_NEW_PASS']         = "New Password";
$message['_RETYPE_NEW_PASS']        = "confirm password";
$message['_USER_EMAIL']             = "Your mailadress";
$message['_USER_NAME']              = "Your Name";
$message['_USER_DATA']              = "change your data";
$message['_SAVE_USER_DATA']         = "save changes";

$message['_SERVER_CONFIG']          = "Server Configuration";
$message['_OSX_CONFIG']             = "OSX Configuration";
$message['_WIN_CONFIG']             = "WIN Configuration";
$message['_LIN_CONFIG']             = "Android Configuration";

$message['_SSL_CERTS']              = "SSL-Certificates";
$message['_SSL_CERTS_NEW']          = "new Certificate";
$message['_SSL_CERTS_EDIT']         = "edit certificate";
$message['_SSL_CERTS_LIST']         = "List certificates";

$message['_TODAY']                  = date('m.d.Y');

$message['_VPN_DATA_SAV']           = "Save your ...";
$message['_VPN_DATA_OSX']           = "OSX-Config";
$message['_VPN_DATA_WIN']           = "WIN-Config";
$message['_VPN_DATA_LIN']           = "Android/Linux-Config";


$message['_USERDATA_EMAIL']         = "eMail";
$message['_USERDATA_PASSWORD']      = "Password";
$message['_USERDATA_SAVE']          = "create user";
$message['_USERDATA_ISADMIN']       = "User gets admin rights";
$message['_USERDATA_FROM_DATE']     = "Access from:";
$message['_USERDATA_TO_DATE']       = "Access to:";

/** 
 * error messages
 * @uses class::Get_Lang
 * @param $var + array-id
 * @return Message in modal dialog
 */
$errormessage[1]                    = $message['_USERDATA_SAVE']." failed or empty passowrd";
$errormessage[2]                    = "Function not yet available. Sorry.";
$errormessage[3]                    = "Newly assigned password was not identical or empty!";
$errormessage[4]                    = "Password changed! Please new Login!";

$freedom = array(
                'Anyone who prefers security over freedom is rightly a slave. (Aristoteles)',
                'Die Freiheit geht zugrunde, wenn wir nicht alles verachten, was uns unter ein Joch beugen will. (Seneca)',
                'Don\'t let it get you down; be bold and wild and wonderful. (Pipi Langstrumpf)',
                'I prefer dangerous freedom to quiet servitude. (Rousseau)'
);



?>
