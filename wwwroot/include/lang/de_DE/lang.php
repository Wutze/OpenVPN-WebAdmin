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
 * Language de_DE
 */

$message['_NO_USER']                = "Unbekannter Benutzername";
$message['_SITE_TITLE']             = "Seitentitel";
$message['_LANGUGE_ERROR']          = "Fehler im Sprachfile";

$message['_HOME']                   = "Home";
$message['_OVERVIEW']               = "Übersicht";
$message['_INFOS_PLUS']             = "Zusatzinfos";
$message['_WELCOME']                = "Willkommen";

$message['_USERS']                  = "Benutzer";
$message['_DISK']                   = "Festplatte";
$message['_SYSTEM']                 = "System";
$message['_ERROR']                  = "Fehler";
$message['_SAVE']                   = "Speichern";
$message['_RESTART']                = "Achtung! Starten Sie Server oder Clients nach Änderungen neu!";
$message['_NEW']                    = "Neu";

$message['_U_NAME']                 = "Name";
$message['_U_GROUP']                = "Gruppe";
$message['_U_ENABLE']               = "Aktiviert";
$message['_U_FROM']                 = "von";
$message['_U_TO']                   = "bis";
$message['_U_ONLINE']               = "Online";

$message['_ATTENTION_FW']           = "Du solltest wissen was Du tust!";
$message['_ATTENTION_CF']           = "Nach Änderungen sollten Client und Server neu gestartet werden";

$message['_VIEW']                   = "anzeigen";

$message['_LOGIN']                  = "Login";
$message['_LOGIN_NAME']             = "Benutzername";
$message['_LOGIN_PASS']             = "Passwort";
$message['_LOGIN_DATA']             = "Bitte Login-Daten eingeben.";
$message['_LOGIN_SAVE']             = "Login merken";
$message['_LOGOUT']                 = "X";
$message['_YOUR_LOGIN']             = "Dein Account";
$message['_USER_RIGHTS']            = "Deine Benutzerrechte";

$message['_SAVE_PW']                = "Speichern";
$message['_LOGIN_PASS_CONTROL']     = "Kontrolle";
$message['_CHANGE_PASS']            = "Passwort ändern";
$message['_INPUT_NEW_PASS']         = "Neues Paswort";
$message['_RETYPE_NEW_PASS']        = "Passwort bestätigen";
$message['_USER_EMAIL']             = "Deine eMail";
$message['_USER_NAME']              = "Dein Name";
$message['_USER_DATA']              = "Deine Daten ändern";
$message['_SAVE_USER_DATA']         = "Änderungen speichern";

$message['_VPN_CONFIGS']            = "Konfigurationen";
$message['_OSX_CONFIG']             = "OSX Konfiguration";
$message['_WIN_CONFIG']             = "WIN Konfiguration";
$message['_LIN_CONFIG']             = "Android Konfiguration";

$message['_SSL_CERTS']              = "SSL-Zertifikate";
$message['_SSL_CERTS_NEW']          = "Neues Zertifikat";
$message['_SSL_CERTS_EDIT']         = "Edit Zertifikat";
$message['_SSL_CERTS_LIST']         = "Liste der Zertifikate";

$message['_TODAY']                  = date('d.m.Y');

$message['_VPN_DATA_SAV']           = "Speichere Deine ...";
$message['_VPN_DATA_OSX']           = "OSX-Config";
$message['_VPN_DATA_WIN']           = "WIN-Config";
$message['_VPN_DATA_LIN']           = "Android/Linux-Config";


$message['_USERDATA_EMAIL']         = "eMail";
$message['_USERDATA_PASSWORD']      = "Password";
$message['_USERDATA_SAVE']          = "Benutzer anlegen";
$message['_USERDATA_ISADMIN']       = "Benutzer erhält Admin-Rechte";
$message['_USERDATA_FROM_DATE']     = "Zugriff ab:";
$message['_USERDATA_TO_DATE']       = "Zugriff bis:";



/** 
 * error messages
 * @uses class::Get_Lang
 * @param $var + array-id
 * @return Message in modal dialog
 */
$errormessage[1]                    = $message['_USERDATA_SAVE']." fehlgeschlagen. ".$message['_LOGIN_NAME']." existiert schon.";
$errormessage[2]                    = "Funktion noch nicht verfügbar. Sorry.";
$errormessage[3]                    = "Neu vergebenes Passwort war nicht identisch oder leer!";
$errormessage[4]                    = "Passwort geändert! Bitte neu einloggen!";

/**
 * footer gimmick from the Slogan kitchen (Sprücheküche) 
 */
$freedom = array(
                'Wer Sicherheit der Freiheit vorzieht, ist zu Recht ein Sklave. (Aristoteles)',
                'Die Freiheit geht zugrunde, wenn wir nicht alles verachten, was uns unter ein Joch beugen will. (Seneca)',
                'Lass dich nicht unterkriegen; sei frech und wild und wunderbar. (Pipi Langstrumpf)',
                'Mir ist die gefährliche Freiheit lieber als eine ruhige Knechtschaft. (Rousseau)'
);



?>
