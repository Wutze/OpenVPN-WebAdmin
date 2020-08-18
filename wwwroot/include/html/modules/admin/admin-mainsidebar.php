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

/** view only for Admins */
if((int)Session::GetVar('gid') === 1){
?>

                    <!-- mod admin user + config -->
                    <li class="nav-item">
                      <a class="nav-link <?php echo ((Session::GetVar('code')=='1')? "active" : ""); ?>" id="user-tab" data-toggle="pill" href="#user" role="menu" aria-controls="user" aria-selected="false">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <p><?php echo GET_Lang::nachricht('_USERS'); ?></p>
                      </a>
                    </li>
<?php
if (defined('dev')){
  echo devel::navcode;
}
if (defined('firewall')){
  echo firewall::navcode;
}
if (defined('system')){
  echo system::navcode;
}
if (defined('ssl')){
  echo ssl::navcode();
}
?>
                    <li class="nav-item">
                      <a class="nav-link" id="admin-tab" data-toggle="pill" href="#admin" role="menu" aria-controls="admin" aria-selected="false">
                        <i class="fas fa-atom"></i>
                        <p><?php echo GET_Lang::nachricht('_VPN_CONFIGS'); ?></p>
                      </a>
                    </li>
                    <!-- /mod admin user + config -->
<?php }; ?>
