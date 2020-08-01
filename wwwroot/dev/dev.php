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
 * @version		1.1.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');



 ?>
 <!-- nicht anfassen, wird benötigt -->
            <div class="tab-pane fade" id="dev" role="tabpanel" aria-labelledby="dev-tab">
            <div class="col-12 col-sm-12">
              <div class="ribbon-wrapper ribbon-lg">
                <div class="ribbon bg-danger">
                  DEV
                </div>
              </div>
<h4>Devel</h4>
 <!-- /nicht anfassen, wird benötigt -->
<?php
/**
 * ! ab hier kannst Du die Seite aufbauen
 * ▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼
 */


$prevVal = shell_exec("cat /proc/stat");
$prevArr = explode(' ',trim($prevVal));
$prevTotal = $prevArr[2] + $prevArr[3] + $prevArr[4] + $prevArr[5];
$prevIdle = $prevArr[5];
usleep(0.15 * 1000000);
$val = shell_exec("cat /proc/stat");
$arr = explode(' ', trim($val));
$total = $arr[2] + $arr[3] + $arr[4] + $arr[5];
$idle = $arr[5];
$intervalTotal = intval($total - $prevTotal);
$stat['cpu'] =  intval(100 * (($intervalTotal - ($idle - $prevIdle)) / $intervalTotal));



$cpu_result = shell_exec("cat /proc/cpuinfo | grep model\ name");
$stat['cpu_model'] = strstr($cpu_result, "\n", true);
preg_match_all('/([a-z\s\w]*):([0-9\s\wa-zA-Z]*)/m', $stat['cpu_model'], $matches, PREG_SET_ORDER, 0);
$stat['cpu_model'] = $matches[0][2];
//memory stat
$stat['mem_percent'] = round(shell_exec("free | grep Mem | awk '{print $3/$2 * 100.0}'"), 2);
$mem_result = shell_exec("cat /proc/meminfo | grep MemTotal");
$stat['mem_total'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
$mem_result = shell_exec("cat /proc/meminfo | grep MemFree");
$stat['mem_free'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
$stat['mem_used'] = $stat['mem_total'] - $stat['mem_free'];



preg_match_all('/^(MemTotal|MemFree|MemAvailable)(:[\W]*)([0-9]*)/m', shell_exec("cat /proc/meminfo"), $matches, PREG_SET_ORDER, 0);

preg_match_all('/^(NAME=|VERSION=)"([0-9a-zA-Z\.\s\(\)]*)"/m', shell_exec("cat /etc/os-release"), $matches, PREG_SET_ORDER, 0);

$stat['osversion'] = $matches[1][2];
$stat['osname'] = $matches[0][2];


$GLOBALS['devint']->collect('dev',$stat);



?>




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
                    <b>OVPN Version:</b> <a class="float-right">1.2.0</a>
                  </li>
                  <li class="list-group-item">
                    <b>CPU:</b> <a class="float-right"><?php echo $stat['cpu_model']; ?></a>
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
                <ul class="list-group list-group-unbordered mb-4">
                  <li class="list-group-item">
                    <b>RAM total:</b> <a class="float-right"><?php echo $stat['mem_total']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>RAM free:</b> <a class="float-right"><?php echo $stat['mem_free']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>RAM used:</b> <a class="float-right"><?php echo $stat['mem_used']; ?></a>
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
                    <b>OS Name:</b> <a class="float-right"><?php echo $stat['osname']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>OS Version:</b> <a class="float-right"><?php echo $stat['osversion']; ?></a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>





<?php





 
/**
 * ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲
 * ! bis hier her wird die Seite gebraucht
 * 
 */

?>
 <!-- nicht anfassen, wird benötigt -->
            </div>
          </div>
<!-- Modal -->
<div class="modal fade" id="debug" tabindex="-1" role="dialog" aria-labelledby="debugTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Debugging</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php $GLOBALS['devint']->ret_dev(); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <a type="button" class="btn btn-primary" href="/">reload</a>
      </div>
    </div>
  </div>
</div>
<?php $GLOBALS['devint']->cl(); ?>
 <!-- /nicht anfassen, wird benötigt -->
