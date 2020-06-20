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

if(Session::GetVar('isadmin')){
?>
        <div class="row">

          <div class="col-lg-3 col-12">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>System</h3>
                <span id="load" class="">load data ...</span>
              </div>
              <div class="icon">
                <i class="fa fa-cogs"></i>
              </div>

            </div>
          </div>

          <div class="col-lg-3 col-12">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>Disk</h3>
                <span id="disk" class="">load data ...</span>
              </div>
              <div class="icon">
                <i class="fa fa-hdd"></i>
              </div>

            </div>
          </div>

          <div class="col-lg-3 col-12">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>Users</h3>
                <span id="users" class="">load data ...</span>
              </div>
              <div class="icon">
                <i class="fas fa-user-plus"></i>
              </div>

            </div>
          </div>

          <div class="col-lg-3 col-12">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>Errors</h3>
                <span id="error" class="">load data ...</span>
              </div>
              <div class="icon">
                <i class="fa fa-exclamation-triangle"></i>
              </div>

            </div>
          </div>
        </div>
<?php
};






