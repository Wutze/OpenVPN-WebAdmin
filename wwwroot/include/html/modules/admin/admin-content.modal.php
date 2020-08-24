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






<div class="modal fade" id="modal" role="dialog" aria-labelledby="modalLabel" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Datepicker</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="height: 400px;">
        <div class="form-group">
          <label class="col-form-label" for="inputWarning"><i class="fa fa-unlock"></i> von</label>
          <input type="text" class="form-control is-warning" name="fromdate" id="datepicker3" placeholder="Date ...">
        </div>
        <div class="form-group">
          <label class="col-form-label" for="inputWarning"><i class="fa fa-lock"></i> bis</label>
          <input type="text" class="form-control is-warning" name="todate" id="datepicker4" placeholder="Date ...">
        </div>
        <div>
          <p class="modalLabel">Test</p>
          <input type="text"  id="username" />uname
          <input type="text" id="uuid" />uuid
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>
  $( "#datepicker3,#datepicker4" ).datepicker({
    dateFormat: "yy-mm-dd" //Database compatible date()
  });
</script>

