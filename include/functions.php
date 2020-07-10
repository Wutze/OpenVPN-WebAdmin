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

function debugsss(){
    echo "<div style='background: #ff8e00;
    height: 350px;
    overflow: scroll;
    width: 90%;
    border: 1px solid #000;
    padding: 10px;'><pre style='font-size: 11px; text-align: left; background: #FCFCFC; color: Black;'>\n";
    $args = func_get_args();
    foreach ($args as $i => $value) {
        echo 'Argument <b>', ($i + 1) , '</b>: ', trim(wordwrap(htmlspecialchars(print_r($value, true)), 200)), PHP_EOL;
    }
    echo "</pre></div>";
}

/**
 * Print simple Slogan, defined in lang.php on your Language Folder
 * @return Slogan
 */
function get_slogan(){
  include(REAL_BASE_DIR."/include/lang/".session::GetVar('lang')."/lang.php");
  $a = count($freedom);
  $a = $a-1;
  $z = rand(0,$a);
  return $freedom[$z];
}

?>
