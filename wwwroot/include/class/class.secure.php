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
 * @version		1.4.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');

/**
 * class passwd/secure
 * get input data and verify users or admins
 * set
 */
class passwd{
	var $option_crypt = PASSWORD_DEFAULT;
	var $allowedchars = '/^[a-z0-9\_\-]*$/';

	function set_pass($pass) {
		return password_hash($pass, $this->option_crypt);
	}

	private function control_pass() {
		if (password_verify($this->data->request['passwd'],$this->res['pass'])) {
			$this->data->uid = (int)$this->res['uid'];
			$this->data->uname = $this->res['uname'];
			$this->data->gid = (int)$this->res['gid'];
			$this->data->level = $this->res['groupname'];
			Session::SetVar('uname',$this->data->uname);
			Session::SetVar('uid',(int)$this->data->uid);
			Session::SetVar('gid',(int)$this->data->gid);
			Session::SetVar('session',$_SESSION['session_id']);
			Session::SetVar('isuser',TRUE);
			((int)$this->data->gid === 1)? Session::SetVar('isadmin',TRUE) : Session::SetVar('isadmin',FALSE);
			((int)$this->data->isenable === 1)? $out = true : $out = false;
			return $out;
		} else {
			#Session::Destroy();
			return false;
		}
	}

	function control_user(){
		/** 
		 * only for development and debugging mode
		 * this loads the development class
		*/
		if (defined('dev')){
			$GLOBALS['devint']->collect('control user',$this);
			#$GLOBALS['devint']->ends();
		};
		if (Session::GetVar('isuser')){
			return;
		}
		if(!self::check_vars($this->data->request['uname'])){
			return;
		}
		$data = newAdoConnection(_DB_TYPE);
		$data->connect(_DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB);

		$this->sql = "SELECT user.user_name AS uname,
									user.uid AS uid,
									user.gid AS gid, 
									user.user_pass AS pass, 
									user.user_enable AS isenable, 
									groupnames.name AS groupname 
									FROM { oj groupnames AS groupnames 
									LEFT OUTER JOIN user AS user 
									ON groupnames.gid = user.gid } 
									WHERE user.user_name = '".self::check_vars($this->data->request['uname'])."'";

		$this->res = $data->getRow($this->sql);
		$this->okornot = self::control_pass();
	}

	static function check_vars($var){
		(preg_match('/^[a-zA-Z0-9\.\_\-]*$/', $var) ? $var=$var : $var="error");
		return $var;
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
 * create new users
 * @param derzeit nix
 * @author wutze
 * @return 
 */
class createchangeuser extends passwd{
	var $legal_actions = array(
														'change'=>'change',
														'saveuserchanges'=>'save',
														'adduser'=>'adduser'
													);
	var $legal_make = array(
														'delete'=>'delete',
														'update'=>'update',
														'sendmail'=>'sendmail',
														'selfupdate'=>'selfupdate'
													);
	function toggle_action(){
		/** 
		 * only for development and debugging mode
		 * this loads the development class
		*/
		if (defined('dev')){
			$GLOBALS['devint']->collect('class passwd',$this);
			#$GLOBALS['devint']->ends();
		};
		(array_key_exists($this->req['op'],$this->legal_actions)) ? $this->gotox = $this->legal_actions[$this->req['op']] : $this->gotox = 'error';
		switch($this->gotox){
			case "adduser";
				self::makenewuser();
			break;
			
			case "save";
				(array_key_exists($this->req['make'],$this->legal_make)) ? $this->make = $this->legal_make[$this->req['make']] : $this->make = 'error';
				switch($this->make){
					case "delete";
						self::remove_user($this->req['uid']);
					break;

					case "update";
						self::update_user($this->req['uid']);
					break;

					case "selfupdate";
						if ($this->req['passwd1'] === $this->req['passwd2'] and !empty($this->req['passwd1']) and !empty($this->req['passwd2'])){
							self::self_update_user($this->req['uid']);
							Session::SetVar('code','4');
							header("Location: /");
						}else{
							Session::SetVar('code','3');
							header("Location: /");
						}
					break;

					case "sendmail";
						Session::SetVar('code','2');
						header("Location: /");
					break;

					case "error";
						header("Location: ?op=error");
					break;


				}

			break;
			case "change";

			break;
			case "error";
				header("Location: ?op=error");
			break;
		}




	}

  function makenewuser(){
    ($this->isadmin) ? $this->ok = TRUE : header("Location: ?op=error");
    if (preg_match($this->allowedchars, $this->uname) and $this->isadmin and !empty($this->uname)){
			/** check first for illegal characters */
			$username = passwd::check_vars($this->uname);
			/** searches first if the user does not exist */
			$sql = "SELECT * FROM `user` WHERE user_id = '$username'";
			$data = newAdoConnection(_DB_TYPE);
			$data->connect(_DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB);
			$this->result = $data->getone($sql);
			/** if the user is present, back to the input page with the question "what is this?" */
			if ($this->result){
				Session::SetVar('code','1');
				header("Location: /");
			}

			$ip = "127.0.0.1";

			if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			#		echo("$ip is a valid IP address");
			} else {
			#		echo("$ip is not a valid IP address");
			}

			/** no further errors, then create the new user */
			/** create insert new user */
			$this->table = "user";
			$this->record = array();
			$this->record['user_name'] = $this->uname;
			$this->record['gid'] = ($this->activeadmin) ? 1 : 2;
			$this->record['user_pass'] = password_hash($this->pass,$this->option_crypt);
			$this->record['user_mail'] = ($this->mail) ? $this->mail : FALSE;
			$this->record['user_phone'] = 0;
			$this->record['user_online'] = 0;
			$this->record['user_enable'] = 1;
			$this->record['user_start_date'] = ($this->fromdate) ? $this->fromdate : FALSE;
			$this->record['user_end_date'] = ($this->todate) ? $this->todate : FALSE;

			/** execute db-query */
			$data->autoExecute($this->table,$this->record,'INSERT');
		}else{
			Session::SetVar('code','1');
			header("Location: /");
		};
	}

		function remove_user($uid){
			$sql = "DELETE FROM `user` WHERE `user`.`uid` = $uid";
			$data = newAdoConnection(_DB_TYPE);
			$data->connect(_DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB);
			$data->execute($sql);
			return;

		}

		function update_user($uid){
			$data = newAdoConnection(_DB_TYPE);
			$data->connect(_DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB);
			$table = 'user';
			$record["gid"] = (isset($this->req['activeadmin']))? 1 : 2;

			(isset($this->req['activeuser']))? $record["user_enable"] = 1 : $record["user_enable"] = 0;


			(isset($this->req['fromdate']))? $record["user_start_date"] =  $this->req['fromdate'] : FALSE;
			(isset($this->req['todate']))? $record["user_end_date"] =  $this->req['todate'] : FALSE;


			(isset($this->req['mail']))? $record["user_mail"] =  $this->req['mail'] : FALSE;
			(isset($this->req['pass']))? $record["user_pass"] = password_hash($this->req['pass'],$this->option_crypt) : '';
			$where = "uid = $uid";
			 
			$data->autoExecute($table,$record,'UPDATE', $where);
			return;

		}

		function self_update_user($uid){
			$data = newAdoConnection(_DB_TYPE);
			$data->connect(_DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB);
			$table = 'user';
			$record["user_pass"] = password_hash($this->req['passwd1'],$this->option_crypt);
			$where = "uid = $uid";
			 
			$data->autoExecute($table,$record,'UPDATE', $where);
			return;

		}


}