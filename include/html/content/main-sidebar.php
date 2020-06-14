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

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
      <img src="images/logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light"><?php echo _SITE_NAME; ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block" data-widget="control-sidebar">Welcome <?php echo Session::GetVar('uname'); ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
              <?php echo GET_Lang::nachricht('_OVERVIEW'); ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a class="nav-link" id="log-tab" data-toggle="pill" href="#log" role="tab" aria-controls="log" aria-selected="false">
                  <i class="fas fa-book-open"></i>
                  <p>Log</p>
                </a>
              </li>
              <?php
/** view only for Admins */
if((int)Session::GetVar('gid') === 1){
  @include(REAL_BASE_DIR."/include/html/modules/main-sidebar-admin.php");
}; ?>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" data-widget="control-sidebar" class="nav-link">
              <i class="fas fa-user-astronaut"></i>
              <p>
                Account
                <span class="right badge badge-danger">New</span>
              </p>
            </a>
          </li>
        </ul>
<?php
/** view only for Admins */
if((int)Session::GetVar('gid') === 1){
  @include(REAL_BASE_DIR."/include/html/modules/main-sidebar-ssl.php");
}; ?>
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item has-treeview menu-close">
            <a href="#" class="nav-link active bg-warning">
              <i class="nav-icon fa fa-download"></i>
              <p>
              <?php echo GET_Lang::nachricht('_VPN_DATA_SAV'); ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="?op=loadzip&file=osx" class="nav-link">
                  <i class="fab fa-apple nav-icon"></i>
                  <p><?php echo GET_Lang::nachricht('_VPN_DATA_OSX'); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="?op=loadzip&file=win" class="nav-link">
                  <i class="fab fa-windows nav-icon"></i>
                  <p><?php echo GET_Lang::nachricht('_VPN_DATA_WIN'); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="?op=loadzip&file=lin" class="nav-link">
                  <i class="fab fa-android nav-icon"></i>
                  <p><?php echo GET_Lang::nachricht('_VPN_DATA_LIN'); ?></p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>



