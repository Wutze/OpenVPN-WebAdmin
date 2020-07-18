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
 * @author      Wutze
 * @copyright   2020 OpenVPN-WebAdmin
 * @link	    https://github.com/Wutze/OpenVPN-WebAdmin
 * @see		    Internal Documentation ~/doc/
 * @version		1.0.0
 * @todoo       new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');

$out = new configfiles;
$out->set_value('action','print');
$out->set_value('isuser',Session::GetVar('isuser'));
$out->set_value('isadmin',Session::GetVar('isadmin'));
?>
          <div class="col-12 col-sm-12">
            <div class="card card-primary card-tabs">
              <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="tab-osx-tab" data-toggle="pill" href="#tab-osx" role="tab" aria-controls="tab-osx" aria-selected="true">OSX</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="tab-lin-tab" data-toggle="pill" href="#tab-lin" role="tab" aria-controls="tab-lin" aria-selected="false">Linux</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="tab-win-tab" data-toggle="pill" href="#tab-win" role="tab" aria-controls="tab-win" aria-selected="false">Windows</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="tab-server-tab" data-toggle="pill" href="#tab-server" role="tab" aria-controls="tab-server" aria-selected="false">Server</a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                  <div class="tab-pane fade active show" id="tab-osx" role="tabpanel" aria-labelledby="tab-osx-tab">
                  <?php
$out->set_value('file','osx');
$out->main();
?>                  </div>
                  <div class="tab-pane fade" id="tab-lin" role="tabpanel" aria-labelledby="tab-lin-tab">
                  <?php
$out->set_value('file','lin');
$out->main();
?>                  </div>
                  <div class="tab-pane fade" id="tab-win" role="tabpanel" aria-labelledby="tab-win-tab">
                  <?php
$out->set_value('file','win');
$out->main();
?>                  </div>
                  <div class="tab-pane fade" id="tab-server" role="tabpanel" aria-labelledby="tab-server-tab">
<?php
$out->set_value('file','server');
$out->main();
?>
                  </div>
                </div>
              </div>
              <!-- /.card -->
            </div>
          </div>
<?php

