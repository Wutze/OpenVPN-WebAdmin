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
#  var $allowed_query_filters = ['user_id', 'log_trusted_ip','log_trusted_port','log_remote_ip','log_remote_port'];

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

      break;
      case "ERROR";
        header("Location: ?op=error");
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
        $o->make_json();
  }

}





?>
