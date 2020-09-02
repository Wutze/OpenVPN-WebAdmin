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


class user{
  private $modulname = "user";
	var $op = array(
		'getuserdata'=>'getuserdata',
		'saveuser'=>'saveuser',
		'error'=>'error');
	
	
  /**
   * main function
   */
  function main(){

    (array_key_exists($this->request['go'],$this->op)) ? $this->gotox = $this->op[$this->request['go']] : $this->gotox = 'ERROR';
    ($this->isadmin) ? '' : $this->gotox = 'ERROR';
    #$GLOBALS['devint']->collect('c.user'.".".rand(0,1000),$this);
    switch($this->gotox){
      case "saveuser";
        #$this->save_config();
      break;

      case "getuserdata";
				$this->erg = getdata::userdata($this->request['uid']);
				#$GLOBALS['devint']->collect('reqmod2',$this);
      break;

      case "print";
        #$this->print_configfiles();
      break;

      case "ERROR";
        #header("Location: ?op=error");
      break;
    }
  }

	
	
	
	public function __construct(){



		



  }

  /**
  * set value
  * @return defined vars for this class
  */
  function set_value($key, $val){
		$this->$key = $val;
}

}


class getdata extends user{

	static function userdata($uid){


		$data = newAdoConnection(_DB_TYPE);
		$data->connect(_DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB);
    $sql1 = 'SELECT user.* FROM user AS user WHERE uid = '.$uid.' ';
    $res1 = $data->getRow($sql1);
    $sql2 = "SELECT user_name, log_start_time, COUNT( log_id ) AS anz FROM log AS log WHERE user_name = '".$res1['user_name']."' ORDER BY log_start_time DESC";
    $res2 = $data->getRow($sql2);
    $sql3 = "SELECT user_ip.user_ip, user_ip.server_ip FROM { oj user_ip AS user_ip RIGHT OUTER JOIN user AS user ON user_ip.uid = user.uid } WHERE user.user_name = '".$res1['user_name']."'";
    $res3 = $data->getRow($sql3);

		$o = new jsonObject;
		$o->id   = "0";
		$o->text = "userdata";
    $o->user = $res1;
    $o->last = $res2;
    $o->usip = $res3;
		$o->make_json();

		return $o;

	}
}