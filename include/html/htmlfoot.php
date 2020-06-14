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
if (Session::GetVar('isuser')){
?>

    <!-- Main Footer -->
    <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline col-6">
      <?php echo get_slogan(); ?>
      <!--Anything you want-->
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; <?php echo "10.000BC to ".date('Y'); ?> <a href="https://xvpn.ddnss.org">OVPN-Admin</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->



  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5><?php echo GET_Lang::nachricht("_CHANGE_PASS") ?></h5>
      <h5><?php echo GET_Lang::nachricht("_INPUT_NEW_PASS") ?></h5>
      <p><input type="password" id="passwd1" name="passwd1" class="form-control" placeholder="<?php echo GET_Lang::nachricht("_LOGIN_PASS") ?>"></p>
      <h5><?php echo GET_Lang::nachricht("_RETYPE_NEW_PASS") ?></h5>
      <p><input type="password" id="passwd2" name="passwd2" class="form-control" placeholder="<?php echo GET_Lang::nachricht("_LOGIN_PASS_CONTROL") ?>"></p>
      <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block" name="op" value="checkpw"><?php echo GET_Lang::nachricht("_SAVE_PW") ?></button>
      </div>
    </div>
    <div class="p-3">
      <h5><?php echo GET_Lang::nachricht("_USER_DATA") ?></h5>
      <p><input type="email" id="email" name="email" class="form-control" placeholder="<?php echo GET_Lang::nachricht("_USER_EMAIL") ?>"></p>
      <p><input type="input" id="uname" name="uname" class="form-control" placeholder="<?php echo GET_Lang::nachricht("_USER_NAME") ?>"></p>
      <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block" name="op" value="checkpw"><?php echo GET_Lang::nachricht("_SAVE_USER_DATA") ?></button>
      </div>
    </div>
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
<script src="js/neu.js"></script>


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
