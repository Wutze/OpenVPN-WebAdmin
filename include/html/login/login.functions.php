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

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');

/**
 * These 4 functions create the fields needed for the login. Please have a look at the enclosed login templates.
 * @return Inputfield username
 * @return Inputfield password
 * @return hiddenfields for the system
 * @return Button for Login
 */

/**
 * Username
 * @return Loginfield Username
 */
function username(){
  return $out = '<input type="text" id="uname" name="uname" class="form-control" placeholder="'.GET_Lang::nachricht("_LOGIN_NAME").'">';
}

/**
 * Password
 * @return Loginfield Password
 */
function password(){

  return $out = '<input type="password" id="passwd" name="passwd" class="form-control" placeholder="'.GET_Lang::nachricht("_LOGIN_PASS").'">';

}

/**
 * Hidden Fields
 * @return hidden fields
 */
function hiddenfields(){
  return $out = '<!--<input type="checkbox" id="remember" name="remember">-->
                <input type="hidden" name="session" value="'.SESSION_NAME.'">
                <input type="hidden" name="isuser" value="'.Session::GetVar('isuser').'">';
}

/**
 * Button
 * @return Login Button
 */
function button($option=""){
  return $out = '<button type="submit" class="btn btn-primary '.$option.'" name="op" value="checklogin">'.GET_Lang::nachricht("_LOGIN").'</button>';
}




?>