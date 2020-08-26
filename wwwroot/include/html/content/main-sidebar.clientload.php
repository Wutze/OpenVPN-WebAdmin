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
 * define here urls for download the client programms
 */
$clientexe = array(
  'win' => '/win',
  'osx' => '/osx',
  'lin' => '/lin'
);

?>
                <!-- load client-software -->
                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link active bg-cemetery">
                    <i class="nav-icon fa fa-download"></i>
                    <p><?php echo GET_Lang::nachricht('_VPN_CLIENT_SAV'); ?><i class="right fas fa-angle-left"></i></p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="<?php echo $clientexe['osx']; ?>" class="nav-link">
                        <i class="fab fa-apple nav-icon"></i>
                        <p><?php echo GET_Lang::nachricht('_VPN_CLIENT_OSX'); ?></p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo $clientexe['win']; ?>" class="nav-link">
                        <i class="fab fa-windows nav-icon"></i>
                        <p><?php echo GET_Lang::nachricht('_VPN_CLIENT_WIN'); ?></p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo $clientexe['osx']; ?>" class="nav-link">
                        <i class="fab fa-android nav-icon"></i>
                        <p><?php echo GET_Lang::nachricht('_VPN_CLIENT_LIN'); ?></p>
                      </a>
                    </li>
                  </ul>
                </li>
                <!-- /load client-software -->
