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



/**
 * Klasse zur Sprachauswahl
 * ! Im Moment noch hardcoded
 * TODO: Dokumentation
 * */
class GET_Lang{

    /**
     * Funktion zum laden des entsprechenden Sprachelementes
     * @param $go holt die Standard-Variable aus dem Sprachfile -> $message[]
     * @param $var bezieht eine andere im Sprachfile definierte Variable ein.
     * ! Plausibilitätsprüfung fehlt noch
     */
    public static function nachricht($go,$var='message'){
        $gib = self::load_lang($go,$var);
        #echo $gib;
        return $gib;
    }

    /**
     * Die eigentliche Funktion, die die Variablen bezieht und zurück gibt
     * Auswahl anhand der im Cookie definierten Sprache
     * Definiert in der constants.php
     */
    public static function load_lang($go,$var){
        include(REAL_BASE_DIR."/include/lang/".session::GetVar('lang')."/lang.php");
        $g = $$var;
        (array_key_exists($go,$$var)) ? $ret = $g[$go] : $ret = 'LANGUAGE ERROR ( '.$go.' )';
        return $ret;
    }
    /**
     * ! deprecated
     */
    public function set_value($key, $val){
        $this->$key = $val;
    }


}


/**
 * -- wechsel
 * @param $var huhu das da
 * ! weg
 * todo: dadada
 * ? keine Ahnung
 * @return  multiple
 */






?>
