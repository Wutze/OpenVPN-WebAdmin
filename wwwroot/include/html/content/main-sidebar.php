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
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
      <img src="images/logo.png" alt="<?php echo _SITE_NAME; ?>" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light"><?php echo _SITE_NAME; ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar os-host os-theme-light os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-transition">
      <div class="os-padding">
        <div class="os-viewport os-viewport-native-scrollbars-invisible" style="">
          <div class="os-content" style="padding: 0px 8px; height: 100%; width: 100%;">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
              <div class="info">
                <a href="#" class="d-block" data-widget="control-sidebar"><?php echo GET_Lang::nachricht('_WELCOME'); ?> <?php echo Session::GetVar('uname'); ?></a>
              </div>
            </div>
            <!-- Sidebar Menu -->
            <nav class="mt-2">
              <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item has-treeview menu-open">
                  <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                      <?php echo GET_Lang::nachricht('_OVERVIEW'); ?>

                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                      <a class="nav-link" id="log-tab" data-toggle="pill" href="#log" role="menu" aria-controls="log" aria-selected="false">
                        <i class="fas fa-book-open"></i>
                        <p>Log</p>
                      </a>
                    </li>
                    <?php
                    /** view only for Admins */
                    if(Session::GetVar('isadmin')){
                      @include(REAL_BASE_DIR."/include/html/modules/admin/admin-mainsidebar.php");
                    }; ?>
                  </ul>
                </li>
                <li class="nav-item">
                  <a href="#" data-widget="control-sidebar" class="nav-link">
                    <i class="fas fa-user-astronaut"></i>
                    <p>
                      <?php echo GET_Lang::nachricht('_YOUR_LOGIN'); ?>

                      <span class="right badge badge-danger"><?php echo GET_Lang::nachricht('_NEW'); ?></span>
                    </p>
                  </a>
                </li>
                <?php
                /** view only for Admins */
                if(Session::GetVar('isadmin') and defined('modssl')){
                  echo modssl::navcode();
                }; ?>

                <!-- load configs -->
                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link active bg-warning" data-toggle="tooltip" title="<?php echo Get_Lang::nachricht('_VPN_DATA_TT'); ?>">
                    <i class="nav-icon fa fa-download"></i>
                    <p><?php echo GET_Lang::nachricht('_VPN_DATA_SAV'); ?><i class="right fas fa-angle-left"></i></p>
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
            <!-- /load configs -->
                <?php
                /** view only when enable client load */
                if(defined('clientload')){
                  echo clientload::navcode();
                  #include_once(REAL_BASE_DIR.'/include/html/content/main-sidebar.clientload.php');
                };
                ?>
            <!-- /.sidebar-menu -->
          </div>
        </div>
      </div>
    </div>
    <!-- /.sidebar -->
  </aside>
