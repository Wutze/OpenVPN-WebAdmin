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
 * @version		1.5.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');

?>





<form role="form" id="quickForm" action="/" method="post">
<div class="modal" id="modal" role="dialog" aria-labelledby="modalLabel" tabindex="-1">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel"><?php echo GET_Lang::nachricht('_U_EXTEND_VIEW'); ?><span id="username" style="background: yellow;"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="nav flex-column nav-pills col-5 col-sm-3 col-xs-3 col-md-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true"><?php echo GET_Lang::nachricht('_OVERVIEW'); ?></a>
            <a class="nav-link" id="timesttings-tab" data-toggle="pill" href="#timesttings" role="tab" aria-controls="timesttings" aria-selected="false"><?php echo GET_Lang::nachricht('_U_TIMESETTINGS'); ?></a>
            <a class="nav-link" id="netsettings-tab" data-toggle="pill" href="#netsettings" role="tab" aria-controls="netsettings" aria-selected="false"><?php echo GET_Lang::nachricht('_U_NETSETTINGS'); ?></a>
            <a class="nav-link disable" id="othersettings-tab" data-toggle="pill" href="#othersettings" role="tab" aria-controls="othersettings" aria-selected="false"><?php echo GET_Lang::nachricht('_U_PLUS'); ?></a>
          </div>

          <div class="tab-content col-7 col-sm-9 col-xs-9 col-md-9 modal-user" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
              <div class="row">
                <div class="col-xs-6 col-md-6">
                  User-ID: <span id="uuid"></span>
                </div>
                <div class="col-xs-6 col-md-6">
                  Logins: <span id="logins"></span>
                </div>
                <div class="col-xs-6 col-md-6">
                  Last Login: <span id="lastlogin"></span>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-xs-6 col-md-6">
                    Is Admin: <span id="isAdmin"></span>
                </div>
                <div class="form-group">
                  <div class="col-xs-6 col-md-6">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" name="activeadmin" id="isAdminSwitch">
                      <label class="custom-control-label" for="isAdminSwitch"></label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-6 col-md-6">
                    <p>User enable:</p>
                </div>
                <div class="form-group">
                  <div class="col-xs-6 col-md-6">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" name="activeuser" id="UserEnableSwitch">
                      <label class="custom-control-label" for="UserEnableSwitch"></label>
                    </div>
                  </div>
                </div>
              </div>

            </div>
            <div class="tab-pane fade" id="timesttings" role="tabpanel" aria-labelledby="timesttings-tab">
              <div class="modal-body" style="">
                <div class="row">
                  <div class="form-group col-xs-6 col-md-6">
                    <label class="col-form-label" for="inputWarning"><i class="fa fa-unlock"></i> <?php echo GET_Lang::nachricht('_U_FROM'); ?></label>
                    <input type="text" class="form-control is-warning" name="fromdate" id="datepicker3" placeholder="Date ...">
                  </div>
                  <div class="form-group col-xs-6 col-md-6">
                    <label class="col-form-label" for="inputWarning"><i class="fa fa-lock"></i> <?php echo GET_Lang::nachricht('_U_TO'); ?></label>
                    <input type="text" class="form-control is-warning" name="todate" id="datepicker4" placeholder="Date ...">
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="netsettings" role="tabpanel" aria-labelledby="netsettings-tab">
              <div class="modal-body" style="">
                <div class="row">
                  <div class="form-group col-xs-6 col-md-6">
                    <label class="col-form-label" ><i class="fas fa-network-wired"></i> <?php echo GET_Lang::nachricht('_U_NETIP'); ?></label>
                    <input type="text" onkeyup="showip(this.value)" class="form-control" name="userip" id="userip" placeholder="User IP">
                  </div>
                  <div class="form-group col-xs-6 col-md-6">
                    <label class="col-form-label" ><i class="fas fa-server"></i> <?php echo GET_Lang::nachricht('_U_GATEWAYIP'); ?></label>
                    <input type="text" class="form-control" name="serverip" id="serverip" placeholder="Gateway">
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="othersettings" role="tabpanel" aria-labelledby="othersettings-tab">
Placeholder
            </div>
          </div>
        </div>
      </div>

      <div class="modal-body" style="">
        <div class="row">

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="hidden" name="op" value="saveuserchanges">
        <input type="hidden" name="session" value="<?php echo Session::GetVar('session_id'); ?>">
        <input type="hidden" id="uid" name="uid" value="">
        <input type="hidden" id="uname" name="uname" value="">
        <button type="submit" id="saveuser" class="btn btn-primary" name="make" value="update">Save</button>
      </div>
    </div>
  </div>
</div>
</form>

<script>
  $( "#datepicker3,#datepicker4" ).datepicker({
    dateFormat: "yy-mm-dd" //Database compatible date()
  });
</script>

