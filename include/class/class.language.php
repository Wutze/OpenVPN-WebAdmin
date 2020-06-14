<?php
/*
 * OVPN-Admin
 *
 * (c) by Wutze 2020 Version 1.0
 * Twitter -> @HuWutze
 *
 * Lizenziert unter der EUPL, Version 1.1
 *
 * Sie dürfen dieses Werk ausschließlich gemäß
 * dieser Lizenz nutzen.
 *
 * Eine Kopie der Lizenz finden Sie hier:
 * http://joinup.ec.europa.eu/software/page/eupl/licence-eupl
 *
 * Sofern nicht durch anwendbare Rechtsvorschriften
 * gefordert oder in schriftlicher Form vereinbart, wird
 * die unter der Lizenz verbreitete Software "so wie sie
 * ist", OHNE JEGLICHE GEWÄHRLEISTUNG ODER BEDINGUNGEN -
 * ausdrücklich oder stillschweigend - verbreitet.
 *
 * Die sprachspezifischen Genehmigungen und Beschränkungen
 * unter der Lizenz sind dem Lizenztext zu entnehmen.
 *
 * Weitere Informationen sind im Ordner /doc zu finden
 * */



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
