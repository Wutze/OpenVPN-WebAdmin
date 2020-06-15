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
 * Original Script from: https://github.com/Chocobozzz/OpenVPN-Admin
 *
 * @author    Wutze
 * @copyright 2020 OpenVPN-Admin
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 *
 * $Revision: 2 $
 * $Author: Wutze $
 * $Date: 2020-05-28 11:05:39 +0100 (Do, 28. Mai 2020) $
 */


/**
 * define root dir
 * load the first file with all definable options
 */
define('REAL_BASE_DIR', dirname(__FILE__));
require_once(REAL_BASE_DIR."/include/load.php");


/**
 * Define first $_REQUEST and check plausibility
 * @return load loginpage | load all other pages
 */
if (!@$_REQUEST["op"]){
	if (Session::GetVar('isuser')){
		$op="main";
	 }else{
		$op="login";
	 }
}else{
	if (Session::GetVar('isuser')){
		$op=$_REQUEST['op'];
	 }else{
		$op=$_REQUEST['op'];
	 }
}

/**
 * call the http-broker - class.request.php - and make all sites
 */
$out = new set_request;
$out->set_value('action',$op);
$out->set_value('uname',Session::GetVar('uname'));
$out->set_value('uid',Session::GetVar('uid'));
$out->set_value('isuser',Session::GetVar('isuser'));
$out->set_value('isadmin',Session::GetVar('isadmin'));
$out->set_value('request',$_REQUEST);
$out->main();

?>