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
          <form role="form" id="quickForm" action="/" method="post">
            <div class="container container-admin-adduser">
              <ul class="nav nav-tabs">
                <li class="active"><a href="#adduser" data-toggle="tab" class="btn btn-info" role="button"><?php echo GET_Lang::nachricht('_USERDATA_NEW_USER'); ?></a></li>
              </ul>
              <div class="tab-content">
                <div id="adduser" class="tab-pane fade in <?php echo ((session::GetVar('code')=='1')? "active show" : ""); ?>">
                  <div class="row">
                    <!-- left column -->
                    <div class="col-md-6">
                      <!-- general form elements -->
                      <div class="card card-primary">
                        <div class="card-header">
                          <h3 class="card-title"><?php echo GET_Lang::nachricht('_USERDATA_USER'); ?></h3>
                        </div>
                        <!-- /.card-header -->
                          <div class="card-body">
                            <div class="form-group">
                              <label for="inputusername"><?php echo GET_Lang::nachricht('_LOGIN_NAME'); ?></label>
                              <input type="text" onkeyup="showuser(this.value)" class="form-control" name="uname" id="InputUsername" placeholder="<?php echo GET_Lang::nachricht('_LOGIN_NAME'); ?>">
                            </div>
                            <div class="form-group">
                              <label for="InputEmail"><?php echo GET_Lang::nachricht('_USERDATA_EMAIL'); ?></label>
                              <input type="email" class="form-control" name="mail" id="InputEmail" placeholder="<?php echo GET_Lang::nachricht('_USERDATA_EMAIL'); ?>">
                            </div>
                            <div class="form-group">
                              <label for="InputPassword"><?php echo GET_Lang::nachricht('_USERDATA_PASSWORD'); ?></label>
                              <input type="password" class="form-control" name="pass" id="InputPassword" placeholder="<?php echo GET_Lang::nachricht('_USERDATA_PASSWORD'); ?>">
                            </div>
                          </div>
                          <!-- /.card-body -->
                          <div class="card-footer">
                            <button type="submit" class="btn btn-primary" name="op" value="adduser"><?php echo GET_Lang::nachricht('_USERDATA_SAVE'); ?></button>
                          </div>
                      </div>
                      <!-- /.card -->
                    </div>
                    <!-- right column -->
                    <div class="col-md-6">
                      <!-- general form elements disabled -->
                      <div class="card card-warning">
                        <div class="card-header">
                          <h3 class="card-title"><?php echo GET_Lang::nachricht('_USERDATA_OPTIONS'); ?></h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                          <div class="row">
                            <!-- input options -->
                              <div class="form-group">
                                <label class="col-form-label" for="inputWarning"><i class="fa fa-unlock"></i> <?php echo GET_Lang::nachricht('_USERDATA_FROM_DATE'); ?></label>
                                <input type="text" class="form-control is-warning" name="fromdate" id="datepicker1" placeholder="Date ...">
                              </div>
                              <div class="form-group">
                                <label class="col-form-label" for="inputWarning"><i class="fa fa-lock"></i> <?php echo GET_Lang::nachricht('_USERDATA_TO_DATE'); ?></label>
                                <input type="text" class="form-control is-warning" name="todate" id="datepicker2" placeholder="Date ...">
                              </div>
                          </div>
                          <hr />
                          <div class="row">
                            <div class="col-sm-12">
                              <div class="form-group">
                                <div class="custom-control custom-switch">
                                  <input type="checkbox" class="custom-control-input" name="activeadmin" id="adminSwitch">
                                  <label class="custom-control-label" for="adminSwitch"><?php echo GET_Lang::nachricht('_USERDATA_ISADMIN'); ?></label>
                                </div>
<?php if(defined('preview')){ ?>
                                <div class="custom-control custom-switch">
                                  <input type="checkbox" class="custom-control-input" disabled name="makeconfigadmin" id="configadminSwitch">
                                  <label class="custom-control-label" for="configadminSwitch"><?php echo GET_Lang::nachricht('_USERDATA_ISCONFIGADMIN'); ?></label>
                                </div>
                                <div class="custom-control custom-switch">
                                  <input type="checkbox" class="custom-control-input" disabled name="makelogadmin" id="logadminSwitch">
                                  <label class="custom-control-label" for="logadminSwitch"><?php echo GET_Lang::nachricht('_USERDATA_ISLOGADMIN'); ?></label>
                                </div>
                                <div class="custom-control custom-switch">
                                  <input type="checkbox" class="custom-control-input" disabled name="makeuseradmin" id="useradminSwitch">
                                  <label class="custom-control-label" for="useradminSwitch"><?php echo GET_Lang::nachricht('_USERDATA_ISUSERADMIN'); ?></label>
                                </div>
                                <div class="custom-control custom-switch">
                                  <input type="checkbox" class="custom-control-input" disabled name="maketlsadmin" id="tlsadminSwitch">
                                  <label class="custom-control-label" for="tlsadminSwitch"><?php echo GET_Lang::nachricht('_USERDATA_ISTLSADMIN'); ?></label>
                                </div>
<?php } ?>
                              </div>
                            </div> 
                          </div>
                        </div>
                        <!-- /.card-body -->
                      </div>
                      <!-- /.card -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
<script>
  $( "#datepicker1,#datepicker2" ).datepicker({
    dateFormat: "yy-mm-dd" //Database compatible date()
  });
</script>

<?php
include(REAL_BASE_DIR.'/include/html/modules/admin/admin-content.modal.php');


