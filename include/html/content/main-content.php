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
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <?php
          @include(REAL_BASE_DIR."/include/html/modules/main-overview.php");
        ?>
        <div class="tab-content" id="v-pills-tabContent col-sm-12 block-grid">
          <div class="tab-pane fade position-relative p-3 bg-white" id="log" role="tabpanel" aria-labelledby="log-tab">
            <div class="ribbon-wrapper ribbon-lg">
              <div class="ribbon bg-primary">
                Log
              </div>
            </div>
            <table class="table-sm"
              id="table"
              data-toggle="table"
              data-side-pagination="server"
              data-pagination="true"
              data-filter-control="true"
              data-url="?op=data&amp;order=asc&amp;offset=0&amp;limit=10&amp;select=log">
<?php
if(Session::GetVar('isadmin')){
  $userfilter = 'data-filter-control="select"';
  $portfilter = 'data-filter-control="select"';
}else{
  $userfilter = '';
  $portfilter = '';
}
?>
              <thead class="thead-dark">
                <tr>
                   <th data-field="log_id" >ID</th>
                   <th data-field="user_id" <?php echo $userfilter; ?>>User</th>
                   <th data-field="log_trusted_ip">IP</th>
                   <th data-field="log_trusted_port" <?php echo $portfilter; ?>>Port</th>
                   <th data-field="log_remote_ip">Remote IP</th>
                   <th data-field="log_remote_port">Remote Port</th>
                   <th data-field="log_start_time">Start Time</th>
                   <th data-field="log_end_time">End Time</th>
                   <th data-field="log_received">rec</th>
                   <th data-field="log_send">sent</th>
                </tr>
              </thead>
            </table>
          </div>
<?php
if(Session::GetVar('isadmin')){
?>
          <div class="tab-pane fade position-relative p-3 bg-white" id="user" role="tabpanel" aria-labelledby="user-tab">
            <div class="ribbon-wrapper ribbon-lg">
              <div class="ribbon bg-success">
                User
              </div>
            </div>
            <table class="table-sm"
              id="table"
              data-toggle="table"
              data-side-pagination="server"
              data-pagination="true"
              data-filter-control="true"
              data-url="?op=data&amp;oder=asc&amp;select=user">
              <thead class="thead-dark">
                <tr>
                  <th data-field="uid" >UID</th>
                  <th data-field="uname" >Name</th>
                  <th data-field="gname">Gruppe</th>
                  <th data-field="user_online">Online</th>
                  <th data-field="user_enable">On</th>
                  <th data-field="user_start_date">von</th>
                  <th data-field="user_end_date">bis</th>
                </tr>
              </thead>
            </table>
          </div>

          <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
            <div class="row">
              <div class="col-sm-4">
                <div class="position-relative p-3 bg-gray" style="height: 180px">
                  <div class="ribbon-wrapper ribbon-lg">
                    <div class="ribbon bg-info">
                      Info
                    </div>
                  </div>
                  Info <br>
                  <small>Alles was da nun rein soll.</small>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="position-relative p-3 bg-gray" style="height: 180px">
                  <div class="ribbon-wrapper ribbon-lg">
                    <div class="ribbon bg-success">
                      Attention
                    </div>
                  </div>
                  Ein Achtung <br>
                  <small>Weils hat ein Achtung sein muss. Weil ich will das so, weils kein was anderes gibt und so muss.</small>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="position-relative p-3 bg-gray" style="height: 180px">
                  <div class="ribbon-wrapper ribbon-lg">
                    <div class="ribbon bg-danger">
                      Error
                    </div>
                  </div>
                  Fehlermeldungen <br>
                  <small>Fehler und Warnungen</small>
                </div>
              </div>
            </div>
          </div>
<?php

$out = new config_files;
$out->set_value('action','print');
$out->set_value('file','server');
$out->set_value('isuser',Session::GetVar('isuser'));
$out->set_value('isadmin',Session::GetVar('isadmin'));
$out->main();

?>
       
          <div class="tab-pane fade position-relative p-3 bg-white" id="ssl" role="tabpanel" aria-labelledby="ssl-tab">
            <div class="ribbon-wrapper ribbon-lg">
              <div class="ribbon bg-success">
                <?php echo Get_Lang::nachricht('_SSL_CERTS_NEW'); ?>
              </div>
            </div>
            <?php echo Get_Lang::nachricht('_SSL_CERTS_NEW'); ?>
          </div>
          
          <div class="tab-pane fade position-relative p-3 bg-white" id="ssl2" role="tabpanel" aria-labelledby="ssl2-tab">
            <div class="ribbon-wrapper ribbon-lg">
              <div class="ribbon bg-success">
              <?php echo Get_Lang::nachricht('_SSL_CERTS_EDIT'); ?>
              </div>
            </div>
            <?php echo Get_Lang::nachricht('_SSL_CERTS_EDIT'); ?>
          </div>
          
          <div class="tab-pane fade position-relative p-3 bg-white" id="ssl3" role="tabpanel" aria-labelledby="ssl3-tab">
            <div class="ribbon-wrapper ribbon-lg">
              <div class="ribbon bg-success">
                <?php echo Get_Lang::nachricht('_SSL_CERTS_LIST'); ?>
              </div>
            </div>
            <?php echo Get_Lang::nachricht('_SSL_CERTS_LIST'); ?>
          </div>
<?php
};
?>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


