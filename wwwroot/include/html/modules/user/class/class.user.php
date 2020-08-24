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
		$sql = 'SELECT user.* FROM user AS user WHERE uid = '.$uid.' ';

		$o = new jsonObject;
		$o->id   = "0";
		$o->text = "userdata";
		$o->user = $data->getRow($sql);
		$o->make_json();

		return $o;

	}
}