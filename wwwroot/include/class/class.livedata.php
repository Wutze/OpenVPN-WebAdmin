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

/**
 * get dynamic Data from Database with bootstrap, further only include/grid.php
 * Calling the class is only possible via index.php?op= in logged in state via class.request.php
 *
 * @example Call Class with;
 * $getdata = new data;
 * $getdata->set_value('data',$_REQUEST['select']);
 * $getdata->set_value('req',$_REQUEST);
 * $getdata->main();
 *
 * the main function allows only defined calls - see var $go
 *
 * @return json formated data from request log|user|admin for bootstrap, jquery or other
 */
class golivedata{
  var $go = array('load'=>'load','cpu'=>'cpu','user'=>'user');
  var $offset = '/[0-9]*/m';
  var $limit = '/[0-9]*/m';

  function main(){
    (array_key_exists($this->action['go'],$this->go)) ? $this->gotox = $this->action['go'] : $this->gotox = 'ERROR';
    (session::GetVar('isuser')) ? '' : $this->gotox = 'ERROR';
    switch($this->gotox){
      case "load";
        self::getdata();
      break;
      case "cpu";

      break;
      case "user";
      (session::GetVar('isadmin')) ? '' : $this->gotox = 'ERROR';
        self::gouser();

      break;
      case "ERROR";
        Session::Destroy();
        header("Location: /?op=error");
      break;
    }

  }

  /**
  * set value
  * @return defined vars for this class
  */
  function set_value($key, $val){
      $this->$key = $val;
  }

  /**
   * Loads all relevant system data, formats them into the appropriate format and transfers them for display
   * @return json formated sysinfo
   */
  static function getdata(){
    if(!Session::GetVar('isadmin')){
      $o = new jsonObject;
      $o->id   = "0";
      $o->text = "noadmin";
      $o->make_json();
      return;
    }
        $loaddata = sys_getloadavg();
        foreach ($loaddata as $key => $value) {
            $loaddata[$key] = round($value, 2);
        }

        $cpuinfo = file_get_contents('/proc/cpuinfo');
        preg_match_all('/^processor/m', $cpuinfo, $matches);
        $cpuinfo = count($matches[0]);
    
        $mem = explode("\n", file_get_contents("/proc/meminfo"));
        $meminfo = array();
        if(count($mem) > 0){
          foreach ($mem as $line) {
            $expl = explode(":", trim($line));
            if(count($expl) == 2){
              $meminfo[$expl[0]] = intVal(substr($expl[1],0, -3));
            }
          }
          $memory_used = $meminfo["MemTotal"]-$meminfo["MemFree"]-$meminfo["Buffers"]-$meminfo["Cached"];
          $memory_total = $meminfo["MemTotal"];
          $memory_usage = $memory_used/$memory_total;
        }else{
          $memory_usage = -1;
        }

        $memory = array($memory_used,$memory_total,$memory_usage);
        $disktotal = round(disk_total_space("/") / 1000000000);
        $diskfree = round(disk_free_space("/") / 1000000000);

        $disktotal = round(disk_total_space("/") / 1000000000);
        $diskfree = round(disk_free_space("/") / 1000000000);
        $diskused = $disktotal - $diskfree;

        $disk = array($disktotal,$diskfree,$diskused);

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

$stat['mem_percent'] = round(shell_exec("free | grep Mem | awk '{print $3/$2 * 100.0}'"), 2);
$mem_result = shell_exec("cat /proc/meminfo | grep MemTotal");
$stat['mem_total'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
$mem_result = shell_exec("cat /proc/meminfo | grep MemFree");
$stat['mem_free'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
$stat['mem_used'] = $stat['mem_total'] - $stat['mem_free'];

$prozent = $stat['mem_total']/100;
$frei = round($stat['mem_free']/$prozent,2);
$belegt = round($stat['mem_used']/$prozent,2);



        $data = newAdoConnection(_DB_TYPE);
		    $data->connect(_DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB);
        $sql = "select count(*) from user ";
        $users['user'] = $data->GetOne($sql);
        $sql = "select count(*) from user where user_online = '1'  ";
        $users['online'] = $data->GetOne($sql);

        $o = new jsonObject;
        $o->id   = "0";
        $o->text = "systemdatafields";
        $o->load = $loaddata;
        $o->memory = $memory;
        $o->cpus = $cpuinfo;
        $o->disk = $disk;
        $o->user = $users;
        $o->cpu = $stat['cpu'];
        $o->ram_total = $stat['mem_total'];
        $o->ram_free = $frei;
        $o->ram_used = $belegt;
        $o->make_json();
  }

  function gouser(){
    $data = newAdoConnection(_DB_TYPE);
    $data->connect(_DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB);
    $searchuser = $this->action['uname'];
    $this->sql="SELECT `user_id` FROM `user` WHERE `user_id` = '$searchuser'  ";
    $result = $data->execute($this->sql);
    $user = $result->fetchRow();

    $o = new jsonObject;
    $o->id   = "0";
    $o->text = "isuser or not";
    $o->isuser = ($user) ? TRUE : FALSE;
    $o->make_json();

  }

}





?>
