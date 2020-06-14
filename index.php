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
 *
 * $Revision: 2 $
 * $Author: Wutze $
 * $Date: 2020-05-28 11:05:39 +0100 (Do, 28. Mai 2020) $
 */

define('REAL_BASE_DIR', dirname(__FILE__));
require_once(REAL_BASE_DIR."/include/load.php");
#debug($_REQUEST);
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

$setvars = new set_request;
$setvars->set_value('action',$op);
$setvars->set_value('uname',Session::GetVar('uname'));
$setvars->set_value('uid',Session::GetVar('uid'));
$setvars->set_value('isuser',Session::GetVar('isuser'));
$setvars->set_value('request',$_REQUEST);
$setvars->main();


#echo $op;
#debug($_REQUEST);
#debug($_SESSION['session_id']);


exit;



	// Get the configuration files ?
	if(isset($_POST['configuration_get'], $_POST['configuration_username'], $_POST['configuration_pass'], $_POST['configuration_os'])
		 && !empty($_POST['configuration_pass'])) {
		$req = $bdd->prepare('SELECT * FROM user WHERE user_id = ?');
		$req->execute(array($_POST['configuration_username']));
		$data = $req->fetch();

		// Error ?
		if($data && passEqual($_POST['configuration_pass'], $data['user_pass'])) {
			// Thanks http://stackoverflow.com/questions/4914750/how-to-zip-a-whole-folder-using-php
			if($_POST['configuration_os'] == "gnu_linux") {
				$conf_dir = 'gnu-linux';
			} elseif($_POST['configuration_os'] == "osx_viscosity") {
				$conf_dir = 'osx-viscosity';
			} else {
				$conf_dir = 'windows';
			}
			$rootPath = realpath("./../vpn/conf/$conf_dir");

			// Initialize archive object ;;;; why doing this every time the user logs in, when the cert is static?
			$archive_base_name = "openvpn-$conf_dir";
			$archive_name = "$archive_base_name.zip";
			$archive_path = "./../vpn/conf/$archive_name";
			$zip = new ZipArchive();
			$zip->open($archive_path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

			$files = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($rootPath),
				RecursiveIteratorIterator::LEAVES_ONLY
			);

			foreach ($files as $name => $file) {
				// Skip directories (they would be added automatically)
				if (!$file->isDir()) {
					// Get real and relative path for current file
					$filePath = $file->getRealPath();
					$relativePath = substr($filePath, strlen($rootPath) + 1);

					// Add current file to archive
					$zip->addFile($filePath, "$archive_base_name/$relativePath");
				}
			}

			// Zip archive will be created only after closing object
			$zip->close();

			//then send the headers to foce download the zip file
			header("Content-type: application/zip");
			header("Content-Disposition: attachment; filename=$archive_name");
			header("Pragma: no-cache");
			header("Expires: 0");
			readfile($archive_path);
		}
		else {
			$error = true;
		}
	}

	// Admin login attempt ?
	else if(isset($_POST['admin_login'], $_POST['admin_username'], $_POST['admin_pass']) && !empty($_POST['admin_pass'])){

		$req = $bdd->prepare('SELECT * FROM admin WHERE admin_id = ?');
		$req->execute(array($_POST['admin_username']));
		$data = $req->fetch();

		// Error ?
		if($data && passEqual($_POST['admin_pass'], $data['admin_pass'])) {
			$_SESSION['admin_id'] = $data['admin_id'];
			header("Location: index.php?admin");
			exit(-1);
		}
		else {
			$error = true;
		}
	}

	?>
