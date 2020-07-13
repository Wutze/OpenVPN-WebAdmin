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
if (Session::GetVar('isuser') || defined('dev')){
?>

    <!-- Main Footer -->
    <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline col-6">
      <?php echo get_slogan(); ?>
      <!--Anything you want-->
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; <?php echo "10.000BC to ".date('Y'); ?> <a href="https://github.com/Wutze/OpenVPN-WebAdmin/">OVPN-Admin</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->



  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <form action="/" method="post">
      <h5><?php echo GET_Lang::nachricht("_CHANGE_PASS") ?></h5>
      <h5><?php echo GET_Lang::nachricht("_INPUT_NEW_PASS") ?></h5>
      <p><input type="password" id="passwd1" name="passwd1" class="form-control" placeholder="<?php echo GET_Lang::nachricht("_LOGIN_PASS") ?>"></p>
      <h5><?php echo GET_Lang::nachricht("_RETYPE_NEW_PASS") ?></h5>
      <p><input type="password" id="passwd2" name="passwd2" class="form-control" placeholder="<?php echo GET_Lang::nachricht("_LOGIN_PASS_CONTROL") ?>"></p>
      <input type="hidden" name="uid" value="<?php echo Session::GetVar('uid') ?>">
      <input type="hidden" name="make" value="selfupdate">
      <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block" name="op" value="saveuserchanges"><?php echo GET_Lang::nachricht("_SAVE_PW") ?></button>
      </div>
      </form>
    </div>
<!-- users self change internal data current not enable, see the next versions -->
  </aside>
  <!-- /.control-sidebar -->



<script src="node_modules/bootstrap/js/dist/modal.js"></script>
<script src="node_modules/bootstrap/js/dist/tooltip.js"></script>
<script src="node_modules/bootstrap/js/dist/tab.js"></script>
<script src="node_modules/bootstrap/js/dist/collapse.js"></script>
<script src="node_modules/bootstrap/js/dist/popover.js"></script>
<script src="node_modules/bootstrap-table/dist/bootstrap-table.min.js"></script>
<script src="node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
<script src="node_modules/bootstrap-table/dist/extensions/editable/bootstrap-table-editable.min.js"></script>
<script src="node_modules/bootstrap-table/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
<script src="node_modules/x-editable/dist/bootstrap-editable/js/bootstrap-editable.js"></script>
<script src="js/user.js"></script>
<?php
if(Session::GetVar('isadmin')){
  ?><script src="js/admin.js"></script><?php
}
?>


<!-- jQuery -->
<script src="node_modules/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="node_modules/admin-lte/dist/js/adminlte.min.js"></script>
<?php
}
?>

</body>
</html>
