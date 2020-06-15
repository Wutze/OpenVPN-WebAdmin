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
 * Central broker who monitors and distributes all transferred values via POST.
 * ! Class not yet finished !
 * TODO: Dokumentation
 * @param Translates and controls the values given by parameter ?op
 * @return string as website
 * @version 1.0.0
 * */
class set_request{
	public $isuser = FALSE;
	public $isadmin = FALSE;
	var $op = array('admin'=>'admin',
					'user'=>'user',
					'logout'=>'logout',
					'login'=>'login',
					'checklogin'=>'checklogin',
					'main'=>'main',
					'ssl'=>'ssl',
					'data'=>'data',
					'live'=>'live',
					'savefile' => 'savefile',
					'loadzip' => 'loadzip',
					'error'=>'error');
	var $uid = FALSE;
	var $uname = "none";
	var $level_name = array(1=>'Admin',2=>'User');
	var $request = FALSE;
	var $zipfile = array(
									'osx' => 'osx',
									'win' => 'win',
									'lin' => 'lin');


	/**
	 * Each call is controlled by this function
	 * @call from index.php
	 * @return html code
	 */
	function main(){
		(array_key_exists($this->action,$this->op)) ? $this->gotox = $this->op[$this->action] : $this->gotox = 'error';
		$this->isuser = (int)Session::GetVar('isuser');
		$this->isadmin = (int)Session::GetVar('isadmin');
		$this->uid = (int)Session::GetVar('uid');
		$this->level = (int)Session::GetVar('level');
		$this->uname = Session::GetVar('uname');
#debug($this);
#exit;
		switch($this->gotox){
			/** print main site as login */
			case "login";
				require_once(REAL_BASE_DIR.'/include/html/login/'._LOGINSITE.'/login.php');
			break;

			/** for load log-data */
			case "data";
				$getdata = new godata;
				$getdata->set_value('data',$this);
				$getdata->set_value('action',$this->request['select']);
				$getdata->set_value('req',$this->request);
				$getdata->main();
			break;

			/** 
			 * @return Live System Data
			 */
			case "live";
				$livedata = new golivedata;
				$livedata->set_value('isuser',$this->isuser);
				$livedata->set_value('action',$this->request);
				$livedata->main();
			break;

			/** 
			 * login check
			 * @return main site after correct login or error site
			 * @internal delete session cookie
			 */
			case "checklogin";
				$control = new passwd;
				$control->set_value('data',$this);
				$control->control_user();
				(!Session::GetVar('isuser')) ? header("Location: ?op=error") : header("Location: .");
			break;

			/** 
			 * @param mixed inputs from $op
			 * @return main site with login
			 */
			case "main";
				html::head();
				require_once(REAL_BASE_DIR.'/include/html/main-html.php');
				html::foot();
			break;

			/** wird im Moment noch nicht benötigt */
			case "ssl";
				html::head();
				require_once(REAL_BASE_DIR.'/include/html/main-html.php');
				html::foot();
			break;

			/** wird im Moment noch nicht benötigt */
			case "admin";
				if($this->level != 1){
					echo "Dreck";
					#header("Location: ?op=login");
				}
			break;

			/** wird im Moment noch nicht benötigt */
			case "user";
				if($this->level != 2){
					echo "User-Dreck";
					#header("Location: ?op=login");
				}
			break;

			/** load and save config files */
			case "savefile";
			case "loadzip";
				$conffile = new config_files;
				$conffile->set_value('isadmin',$this->isadmin);
				$conffile->set_value('isuser',$this->isuser);
				$conffile->set_value('action',$this->gotox);
				$conffile->set_value('file',(!$this->request['file']) ? false : $this->request['file']);
				$conffile->main();
			break;

			/** 
			 * create and load zipfile
			 * @return zipfile
			*/
			case "loadzip_old";
				(array_key_exists($this->request['file'],$this->zipfile)) ? $file = $this->request['file'] : header("Location: .");
				(Session::GetVar('isuser')) ? '' : header("Location: ?op=error") ;
				load_zipfile($file);
			break;

			/** logout from webfrontend */
			case "logout";
				Session::Destroy();
				header("Location: .");
			break;

			/** 
			 * For invalid or incorrect entries
			 * @return force logout and log
			 */
			case "error";
				html::head();
				require_once(REAL_BASE_DIR.'/include/html/error.php');
				#debug($this);
				html::foot();
				Session::Destroy();
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


}

/**
 * To load HTML standard codes as extension from set_request 
 */
class html extends set_request{

	/**
	 * load html head
	 */
	public static function head(){
		include(REAL_BASE_DIR.'/include/html/htmlhead.php');
	}

	/**
	 * load html foot
	 */
	public static function foot(){
		include(REAL_BASE_DIR.'/include/html/htmlfoot.php');
	}

	/**
	 * for next versions load js scripts
	 */
	function jsfoot(){
		return false;
	}

	/**
	 * set value
	 * @return defined vars for this class
	 */
	function set_value($key, $val){
		$this->$key = $val;
	}
}





/**
 * -- wechsel
 * @param $var huhu das da
 * ! weg
 * todo: dadada
 * ? keine Ahnung
 * @return  multiple
 */






?>
