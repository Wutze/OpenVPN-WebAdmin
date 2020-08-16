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
 * These 4 functions create the fields needed for the login. Please have a look at the enclosed login templates.
 * @param {*} option set more class options
 * @return Inputfield username
 * @return Inputfield password
 * @return hiddenfields, required for the system
 * @return Button for Login
 */

/**
 * Username
 * @return Loginfield for Username
 */
function username($option=""){
  return $out = '<input type="text" id="uname" name="uname" class="form-control '.$option.'" placeholder="'.GET_Lang::nachricht("_LOGIN_NAME").'">';
}

/**
 * Password
 * @return Loginfield for Password
 */
function password($option=""){

  return $out = '<input type="password" id="passwd" name="passwd" class="form-control '.$option.'" placeholder="'.GET_Lang::nachricht("_LOGIN_PASS").'">';

}

/**
 * required hidden fields
 * @return hidden fields
 */
function hiddenfields($option=""){
  return $out = '
                <input type="hidden" name="session" value="'.SESSION_NAME.'">';
}

/**
 * Button
 * @return Login Button
 */
function button($option=""){
  return $out = '<button type="submit" class="btn btn-primary '.$option.'" name="op" value="checklogin">'.GET_Lang::nachricht("_LOGIN").'</button>';
}

function noscript(){
  $out = '
  <noscript>
  <div class="nonono">
    <div class="mini-led-red-blink">
    </div>
    <div class="login-box-body">
      <p>'.GET_Lang::nachricht("_NEED_JAVASCRIPT").'</p>
    </div>
  </div>
  </noscript>
  ';
  return $out;
}


?>