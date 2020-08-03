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
	/**
	 * @param op array()
	 * array is checked for allowed values over $_REQUEST['op'], otherwise an error message is generated if wrong or incorrect data is passed
	 * You can also make functions independent of the passed function call
	 * @see Documentation
	 */
	var $op = array(
					'adduser'=>'adduser',
					'saveuserchanges'=>'saveuserchanges',
					'logout'=>'logout',
					'login'=>'login',
					'checklogin'=>'checklogin',
					'checkdata'=>'checkdata',
					'main'=>'main',
					'ssl'=>'ssl',
					'data'=>'data',
					'live'=>'live',
					'savefile' => 'savefile',
					'delfile' => 'delfile',
					'loadzip' => 'loadzip',
					'language'=>'language',
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
		(defined('dev'))? $GLOBALS['devint']->collect('broker',$this) : "";
		/** broker */
		switch($this->gotox){
			/** print main site as login */
			case "login";
				require_once(REAL_BASE_DIR.'/include/html/login/'._LOGINSITE.'/login.php');
			break;

			/** load dynamic data */
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
			 * @return main site
			 */
			case "main";
				($this->isuser) ? "" : header("Location: ?op=error");
				html::head();
				require_once(REAL_BASE_DIR.'/include/html/main-html.php');
				html::foot();
			break;

			/** wird im Moment noch nicht benötigt -  will be available from version 2.0.0*/
			case "ssl";
				html::head();
				require_once(REAL_BASE_DIR.'/include/html/main-html.php');
				html::foot();
			break;

			/** Userverwaltung */
			case "adduser";
			case "saveuserchanges";
				$manipulate_user = new createchangeuser;
				$manipulate_user->set_value('uname',$this->request['uname']);
				(isset($this->request['mail'])) ? $manipulate_user->set_value('mail',$this->request['mail']) : "";
				(isset($this->request['pass'])) ? $manipulate_user->set_value('pass',$this->request['pass']) : "";
				(isset($this->request['fromdate'])) ? $manipulate_user->set_value('fromdate',$this->request['fromdate']) : "";
				(isset($this->request['todate'])) ? $manipulate_user->set_value('todate',$this->request['todate']) : "";
				(isset($this->request['makeadmin'])) ? $manipulate_user->set_value('makeadmin',$this->request['makeadmin']) : $manipulate_user->set_value('makeadmin',FALSE);
				$manipulate_user->set_value('isadmin',$this->isadmin);
				$manipulate_user->set_value('isuser',$this->isuser);
				$manipulate_user->set_value('req',$this->request);
				$manipulate_user->toggle_action();

				html::head();
				require_once(REAL_BASE_DIR.'/include/html/main-html.php');
				html::foot();
			break;

			/** load and save config files */
			case "savefile";
			case "loadzip";
			case "delfile";
				$conffile = new configfiles;
				$conffile->set_value('isadmin',$this->isadmin);
				$conffile->set_value('isuser',$this->isuser);
				$conffile->set_value('action',$this->gotox);
				$conffile->set_value('file',(!@$this->request['file']) ? $this->request['config_file'] : $this->request['file']);
				$conffile->main();
			break;

			/** logout from webfrontend */
			case "logout";
				Session::Destroy();
				header("Location: .");
			break;

			/** logout from webfrontend */
			case "language";
				Session::SetVar('lang',$this->request['lang']);
				header("Location: .");
			break;

			/** 
			 * For invalid or incorrect entries
			 * @return force logout and session destroy
			 */
			case "error";
				require_once(REAL_BASE_DIR.'/include/html/login/'._LOGINSITE.'/error.php');
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
