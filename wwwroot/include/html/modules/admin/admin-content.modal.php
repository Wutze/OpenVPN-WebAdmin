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






<div class="modal" id="modal" role="dialog" aria-labelledby="modalLabel" tabindex="-1">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel"><?php echo GET_Lang::nachricht('_U_EXTEND_VIEW'); ?><span id="username"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="nav flex-column nav-pills col-5 col-sm-3 col-xs-3 col-md-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true"><?php echo GET_Lang::nachricht('_OVERVIEW'); ?></a>
            <a class="nav-link" id="timesttings-tab" data-toggle="pill" href="#timesttings" role="tab" aria-controls="timesttings" aria-selected="false"><?php echo GET_Lang::nachricht('_U_TIMESETTINGS'); ?></a>
            <a class="nav-link disabled" id="netsettings-tab" data-toggle="pill" href="#netsettings" role="tab" aria-controls="netsettings" aria-selected="false"><?php echo GET_Lang::nachricht('_U_NETSETTINGS'); ?></a>
            <a class="nav-link disabled" id="othersettings-tab" data-toggle="pill" href="#othersettings" role="tab" aria-controls="othersettings" aria-selected="false"><?php echo GET_Lang::nachricht('_U_PLUS'); ?></a>
          </div>

          <div class="tab-content col-7 col-sm-9 col-xs-9 col-md-9 modal-user" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
              <div class="row">
                <div class="col-xs-12 col-md-6">
                  User-ID: <span id="uuid"></span>
                </div>
                <div class="col-xs-12 col-md-6">
                  Logins: <span id="logins"></span>
                </div>
                <div class="col-xs-12 col-md-6">
                  Last Login: <span id="lastlogin"></span>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="timesttings" role="tabpanel" aria-labelledby="timesttings-tab">
              <div class="modal-body" style="">
                <div class="row">
                  <div class="form-group col-xs-12 col-md-6">
                    <label class="col-form-label" for="inputWarning"><i class="fa fa-unlock"></i> <?php echo GET_Lang::nachricht('_U_FROM'); ?></label>
                    <input type="text" class="form-control is-warning" name="fromdate" id="datepicker3" placeholder="Date ...">
                  </div>
                  <div class="form-group col-xs-12 col-md-6">
                    <label class="col-form-label" for="inputWarning"><i class="fa fa-lock"></i> <?php echo GET_Lang::nachricht('_U_TO'); ?></label>
                    <input type="text" class="form-control is-warning" name="todate" id="datepicker4" placeholder="Date ...">
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="netsettings" role="tabpanel" aria-labelledby="netsettings-tab">
            Tab 3
            </div>
            <div class="tab-pane fade" id="othersettings" role="tabpanel" aria-labelledby="othersettings-tab">
            Tab 4
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
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Save</button>
      </div>
    </div>
  </div>
</div>


<script>
  $( "#datepicker3,#datepicker4" ).datepicker({
    dateFormat: "yy-mm-dd" //Database compatible date()
  });
</script>

