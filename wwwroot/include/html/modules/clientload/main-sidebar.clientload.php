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
 * @version		1.4.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');

?>

            <!-- load client-software -->
            <nav>
              <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview menu-close">
                  <a href="#" class="nav-link active bg-cemetery" data-toggle="tooltip" title="<?php echo Get_Lang::nachricht('_VPN_CLIENT_TT'); ?>">
                    <i class="nav-icon fa fa-download"></i>
                    <p>
                    <?php echo GET_Lang::nachricht('_VPN_CLIENT_SAV'); ?>

                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview nav-pills nav-sidebar flex-column" data-widget="treeview" role="tabcll" data-accordion="false">
                    <li class="nav-item">
                      <a class="nav-link" id="cllosx-tab" data-toggle="pill" href="#cllosx" role="tabcll" aria-controls="cll" aria-selected="false">
                        <ion-icon name="logo-apple"></ion-icon>
                        <p><?php echo Get_Lang::nachricht('_VPN_CLIENT_OSX'); ?></p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="cllwin-tab" data-toggle="pill" href="#cllwin" role="tabcll" aria-controls="cll" aria-selected="false">
                        <ion-icon name="logo-windows"></ion-icon>
                        <p><?php echo Get_Lang::nachricht('_VPN_CLIENT_WIN'); ?></p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="clllin-tab" data-toggle="pill" href="#clllin" role="tabcll" aria-controls="cll" aria-selected="false">
                        <ion-icon name="logo-android"></ion-icon>
                        <p><?php echo Get_Lang::nachricht('_VPN_CLIENT_LIN'); ?></p>
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </nav>
            <!-- /load client-software -->