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
 * Session Verwaltung
 */
class websessions{
    /**
     * Lade ADOdb-Sessionverwaltung
     * Starte Verbindung zur Datenbank
     * Verschlüssele Session
     * schalte Debugging ein, wenn der Schalter in der load.php auf TRUE gesetzt wird.
     */
    public function define_session(){
        include_once(_ADODB . "session/adodb-cryptsession2.php");
        $db = new ADODB_SESSION;
        $db->config(_DB_TYPE, _DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB,$options=false);
        $db->filter(new ADODB_Encrypt_MD5());
        $db->debug(_SESSION_DEBUG);
        $db->read(session_id());
        return $db;
    }

    /**
     * definiere Sessionname und notwendige Angaben
     * starte die Session mit dem zuvor angegebenen Namen
     */
    public function start_session(){
        define("SESSION_NAME" , substr("la" . strtoupper(md5(USER_AGENT . "s" . HOME_URL)), 0, 32));
        @ini_set("session.auto_start" , '0'); // Auto-start session
        @ini_set("session.gc_probability" , '10'); // Garbage collection in %
        @ini_set("session.serialize_handler", 'php'); // How to store data
        @ini_set("session.use_cookies" , '1'); // Use cookie to store the session ID
        @ini_set("session.gc_maxlifetime" , SESSION_LIFETIME); // Sekunden Inactivity timeout for user sessions
        @ini_set("url_rewriter.tags" , ''); // verhindern, dass SID an URL gehaengt wird
        @ini_set("session.use_only_cookies", '1'); // Use only cookie to store the session ID
        @ini_set("session.cookie_httponly" , '1'); // Whether or not to add the httpOnly flag to the cookie, which makes it inaccessible to browser scripting languages such as JavaScript.
        session_name(SESSION_NAME);
        session_start();
    }

    public function set_value($key, $val){
        $this->$key = $val;
    }
}

/**
 * Setze/Lese/Lösche/Ändere Inhalte im Session Cookie
 */
class session extends websessions{

    /**
     * Lese Cookie aus
     * $varname     Session Variable
     * @return      Inhalt
     */
    public static function GetVar($varname)
    {
        $varname = $varname;
        if (!isset($_SESSION[$varname])) {
            return false;
        }
        return $_SESSION[$varname];
    }

    /**
     * Setze Cookie-Variable
     * $varname     Session Variable
     * $value       Inhalt
     * @return      TRUE
     */
    public static function SetVar($varname, $value)
    {
        $_SESSION[$varname] = $value;
        return true;
    }

    /**
     * Lösche Cookie Variable
     * $varname     Session Variable
     * @return      TRUE
     */
    public static function DelVar($varname)
    {
        unset($_SESSION[$varname]);
        return true;
    }

    /**
     * Löscht das Cookie vollständig
     */
    public static function Destroy()
    {
        SetCookie(SESSION_NAME, "", -1);
        $_SESSION = array();
    }
}






?>
