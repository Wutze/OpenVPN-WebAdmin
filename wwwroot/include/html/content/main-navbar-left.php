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
 * @version		1.2.1
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');
?>
    <!-- Left navbar Top -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="/" class="nav-link"><?php echo GET_Lang::nachricht('_HOME'); ?></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link"><?php echo GET_Lang::nachricht('_TODAY'); ?></a>
      </li>
<?php
if (defined('dev') and Session::GetVar('isadmin')){
  echo devel::topnav;
}
?>
    </ul>
    <!-- file saved or not - flying message -->
    <div id="messagestage"><?php echo GET_Lang::nachricht('_CONF_SAVED'); ?></div>
    <div id="messagestageer"><?php echo GET_Lang::nachricht('_CONF_SAVE_ERROR'); ?></div>
