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

?>

<?php
/** view only for Admins */
if((int)Session::GetVar('gid') === 1){
?>
              <li class="nav-item">
                <a class="nav-link <?php echo ((@$_REQUEST['code']=='1')? "active" : ""); ?>" id="user-tab" data-toggle="pill" href="#user" role="tab" aria-controls="user" aria-selected="false">
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
