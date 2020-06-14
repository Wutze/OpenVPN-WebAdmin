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
 * class passwd/secure
 * get input data and verify users or admins
 * set
 */
class passwd{
	var $option_crypt = "PASSWORD_DEFAULT";

	function set_pass($pass) {
		return password_hash($pass, $this->option_crypt);
	}

	private function control_pass() {
		if (password_verify($this->data->request['passwd'],$this->res['pass'])) {
			$this->data->uid = (int)$this->res['uid'];
			$this->data->uname = $this->res['uname'];
			$this->data->gid = (int)$this->res['gid'];
			$this->data->level = $this->res['gname'];
			Session::SetVar('uname',$this->data->uname);
			Session::SetVar('uid',(int)$this->data->uid);
			Session::SetVar('gid',(int)$this->data->gid);
			Session::SetVar('session',$_SESSION['session_id']);
			Session::SetVar('isuser',TRUE);
			((int)$this->data->gid === 1)? Session::SetVar('isadmin',TRUE) : Session::SetVar('isadmin',FALSE);
			return true;
		} else {
			Session::Destroy();
			return false;
		}
	}

	function control_user(){
		if (Session::GetVar('isuser')){
			return;
		}
		if(!self::check_vars($this->data->request['uname'])){
			return;
		}
		$data = newAdoConnection(_DB_TYPE);
		$data->connect(_DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB);

		$this->sql = "SELECT user.user_id AS uname,
				user.user_pass AS pass,
				user.uid AS uid,
				usergroups.gid AS gid,
				groupnames.name AS gname
				FROM { oj tester.usergroups AS usergroups
				LEFT OUTER JOIN tester.user AS user
				ON usergroups.uid = user.uid
				RIGHT OUTER JOIN tester.groupnames AS groupnames
				ON usergroups.gname = groupnames.gname }
				WHERE user.user_id = '".self::check_vars($this->data->request['uname'])."'";

		$this->res = $data->getRow($this->sql);
		$this->okornot = self::control_pass();
	}

	static function check_vars($var){
		$out = trim($var, "\x00..\x1F");
		return $out;
	}

	/**
	 * set value
	 * @return defined vars for this class
	 */
	function set_value($key, $val){
		$this->$key = $val;
	}
}