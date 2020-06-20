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
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item has-treeview menu-close">
            <a href="#" class="nav-link active bg-danger">
              <i class="nav-icon fab fa-expeditedssl"></i>
              <p>
              <?php echo GET_Lang::nachricht('_SSL_CERTS'); ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a class="nav-link" id="ssl-tab" data-toggle="pill" href="#ssl" role="tab" aria-controls="ssl" aria-selected="false">
                <i class="fas fa-shield-alt"></i>
                  <p><?php echo Get_Lang::nachricht('_SSL_CERTS_NEW'); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="ssl2-tab" data-toggle="pill" href="#ssl2" role="tab" aria-controls="ssl2" aria-selected="false">
                  <i class="far fa-edit"></i>
                  <p><?php echo Get_Lang::nachricht('_SSL_CERTS_EDIT'); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="ssl3-tab" data-toggle="pill" href="#ssl3" role="tab" aria-controls="ssl3" aria-selected="false">
                  <i class="fas fa-fingerprint"></i>
                  <p><?php echo Get_Lang::nachricht('_SSL_CERTS_LIST'); ?></p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
<?php };