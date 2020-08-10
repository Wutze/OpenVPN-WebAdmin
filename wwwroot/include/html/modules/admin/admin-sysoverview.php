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
 * @version		1.2.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');

$cpu_result = shell_exec("cat /proc/cpuinfo | grep model\ name");
$stat['cpu_model'] = strstr($cpu_result, "\n", true);
preg_match_all('/([a-z\s\w]*):([0-9\s\wa-zA-Z]*)/m', $stat['cpu_model'], $matches, PREG_SET_ORDER, 0);
$stat['cpu_model'] = $matches[0][2];
$stat['mem_percent'] = round(shell_exec("free | grep Mem | awk '{print $3/$2 * 100.0}'"), 2);
$mem_result = shell_exec("cat /proc/meminfo | grep MemTotal");
$stat['mem_total'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
$mem_result = shell_exec("cat /proc/meminfo | grep MemFree");
$stat['mem_free'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
$stat['mem_used'] = $stat['mem_total'] - $stat['mem_free'];
preg_match_all('/^(NAME=|VERSION=)"([0-9a-zA-Z\/\.\s\(\)]*)"/m', shell_exec("cat /etc/os-release"), $matches, PREG_SET_ORDER, 0);
$stat['osversion'] = $matches[1][2];
$stat['osname'] = $matches[0][2];

if(file_exists(REAL_BASE_DIR.'/dev.version.php')){
  include_once(REAL_BASE_DIR.'/dev.version.php');
  $stat['cpu_model'] = cpu;
}elseif(file_exists(REAL_BASE_DIR.'/version.php')){
  include_once(REAL_BASE_DIR.'/version.php');
}

?>
        <!-- sysoverview -->
        <div class="card-main tab-pane fade position-relative p-3 bg-white <?php echo ((!Session::GetVar('code'))? "active show" : ""); ?>" id="sys" role="tabpanel" aria-labelledby="sys">
          <div class="row">
            <div class="col-md-4">
              <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" src="/images/ovpn-original.png" alt="SysPicture">
                  </div>
                  <h3 class="profile-username text-center">OpenVPN-WebAdmin</h3>
                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                      <b>OVPN Version:</b> <a class="float-right"><?php echo version; ?></a>
                    </li>
                    <li class="list-group-item">
                      <b>CPU:</b> <a class="float-right"><?php echo $stat['cpu_model']; ?></a>
                    </li>
                    <li class="list-group-item">
                      <div class="progressdata">
                        <span id="cpu2">load data ...</span>
                        <div class="progress progress-sm active">
                          <div id="cpu" class="progress-bar bg-warning progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                          </div>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-primary card-outline">
                <div class="card-body">
                  <div class="text-center">
                    <i class="fa fa-hdd"></i>
                  </div>
                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                      <b>RAM total:</b> <a class="float-right"><?php echo $stat['mem_total']; ?> GB</a>
                    </li>
                    <li class="list-group-item">
                      <b>RAM free:</b> <a class="float-right"><?php echo $stat['mem_free']; ?> GB</a>
                    </li>
                    <li class="list-group-item">
                      <b>RAM used:</b> <a class="float-right"><?php echo $stat['mem_used']; ?> GB</a>
                    </li>
                    <li class="list-group-item">
                      <div class="progressdata">
                        <span id="ram_used2" style="width: 50%;">load data ...</span> || <span id="ram_free2" style="width: 50%;">load data ...</span>
                        <div class="progress progress-sm active">
                          <div id="ram_used" class="progress-bar bg-danger progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                          </div>
                          <div id="ram_free" class="progress-bar bg-success progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-primary card-outline">
                <div class="card-body">
                  <div class="text-center">
                    <i class="fa fa-cogs"></i>
                  </div>
                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                      <b>System:</b> <a class="float-right"><?php echo $stat['osname']; ?></a>
                    </li>
                    <li class="list-group-item">
                      <b>Version:</b> <a class="float-right"><?php echo $stat['osversion']; ?></a>
                    </li>
                    <li class="list-group-item">
                      <b>PHP:</b> <a class="float-right"><?php echo phpversion(); ?></a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /sysoverview -->
<?php
