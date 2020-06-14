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

?>

              <?php
/** view only for Admins */
if((int)Session::GetVar('gid') === 1){
?>
              <li class="nav-item">
                <a class="nav-link" id="user-tab" data-toggle="pill" href="#user" role="tab" aria-controls="user" aria-selected="false">
                  <i class="fas fa-chalkboard-teacher"></i>
                  <p>User</p>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="admin-tab" data-toggle="pill" href="#admin" role="tab" aria-controls="admin" aria-selected="false">
                  <i class="fas fa-atom"></i>
                  <p>Admin</p>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="config-tab" data-toggle="pill" href="#config" role="tab" aria-controls="config" aria-selected="false">
                  <i class="fas fa-server"></i>
                  <p><?php echo GET_Lang::nachricht('_SERVER_CONFIG'); ?></p>
                </a>
              </li>
<?php }; ?>






