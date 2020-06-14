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
  function getMigrationSchemas() {
    return [ 0, 5 ];
  }

  function updateSchema($bdd, $newKey) {
    if ($newKey === 0) {
      $req_string = 'INSERT INTO `application` (sql_schema) VALUES (?)';
    }
    else {
      $req_string = 'UPDATE `application` SET `sql_schema` = ?';
    }

    $req = $bdd->prepare($req_string);
    $req->execute(array($newKey));
  }

  function printError($str) {
    echo '<div class="alert alert-danger" role="alert">' . $str . '</div>';
  }

  function printSuccess($str) {
    echo '<div class="alert alert-success" role="alert">' . $str . '</div>';
  }

  function isInstalled($bdd) {
    $req = $bdd->prepare("SHOW TABLES LIKE 'admin'");
    $req->execute();

    if(!$req->fetch())
      return false;

    return true;
  }
/*
  function hashPass($pass) {
    return password_hash($pass, PASSWORD_DEFAULT);
  }

  function passEqual($pass, $hash) {
    return password_verify($pass, $hash);
  }
*/
  function debug()
  {
    echo "<div style='background: #ff8e00;
    height: 350px;
    overflow: scroll;
    width: 90%;
    border: 1px solid #000;
    padding: 10px;'><pre style='font-size: 11px; text-align: left; background: #FCFCFC; color: Black;'>\n";
    $args = func_get_args();
    foreach ($args as $i => $value) {
        echo 'Argument <b>', ($i + 1) , '</b>: ', trim(wordwrap(htmlspecialchars(print_r($value, true)), 200)), PHP_EOL;
    }
    echo "</pre></div>";
}

/**
 * read historical config documents
 * @return html formatted history Conf-Files
 */
function getHistory_old($cfg_file) {
  ?>
  <div class="alert alert-info" role="alert"><b>History</b>
    <div class="panel-group" id="accordion<?php echo Session::GetVar('session_id'); ?>">
      <?php foreach (array_reverse(glob('../vpn/conf/'.basename(pathinfo($cfg_file, PATHINFO_DIRNAME)).'/history/*'),true) as $i => $file): ?>
      <div class="panel panel-primary">
        <div class="panel-primary">
          <a data-toggle="collapse" data-parent="#accordion<?php echo Session::GetVar('session_id'); ?>" href="#collapse<?php echo Session::GetVar('session_id'); ?>-<?php echo $i ?>">
          <?php
            $history_file_name = basename($file);
            $chunks = explode('_', $history_file_name);
            echo date('d.m.y', $chunks[0]);
          ?>
          </a>
        </div>
        <div id="collapse<?php echo Session::GetVar('session_id'); ?>-<?php echo $i ?>" class="panel-collapse collapse">
          <div class="position-relative p-3 bg-gray" style="height: 180px">
            <div class="ribbon-wrapper ribbon-lg">
              <div class="ribbon bg-danger">
                <?php echo date("d.m.y",$chunks[0]); ?>
              </div>
            </div>
            <div class="well">
              <pre>
                <?php echo readfile($file) ?>
              </pre>
            </div>
          </div>
        </div>
        
      </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php
  return;
}

/**
 *  [update_config] => true
    [config_file] => ../vpn/conf/server/server.conf
    [config_content]
  * auf _POST reagieren?! Alter, der tickt nicht richtig!
 */

function save_config(){
#debug($_POST);
  $pathinfo = pathinfo($_POST['config_file']);

  $config_full_uri = $_POST['config_file']; // the complete path to the file, including the file (name) its self and the fully qualified path
  $config_full_path = "../vpn/conf/server/"; // path to file (without filename its self)
  $config_name = basename($_POST['config_file']); // config file name only (without path)
  $config_parent_dir = basename($config_full_path); // name of the dir that contains the config file (without path)

  /*
   * create backup for history
   *
   */
  if (!file_exists($dir="$config_full_path/history"))
     mkdir($dir, 0777, true);
  $ts = time();
  copy("$config_full_uri", "$config_full_path/history/${ts}_${config_name}");

  /*
   *  write config
   */
  $conf_success = file_put_contents($_POST['config_file'], $_POST['config_content']);

  echo json_encode([
    'debug' => [
        'config_file' => $_POST['config_file'],
        'config_content' => $_POST['config_content']
    ],
    'config_success' => $conf_success !== false,
  ]);
}

/**
 * Print simple Slogan, defined in lang.php on your Language Folder
 * @return Slogan
 */
function get_slogan(){
  include(REAL_BASE_DIR."/include/lang/".session::GetVar('lang')."/lang.php");
  $a = count($freedom);
  $a = $a-1;
  $z = rand(0,$a);
  return $freedom[$z];
}


function load_zipfile($conf_dir){
  $conf_array = array('win'=>'windows','lin'=>'gnu-linux','osx'=>'osx');
  $rootPath = realpath("./../vpn/conf/$conf_array[$conf_dir]");

  // Initialize archive object ;;;; why doing this every time the user logs in, when the cert is static?
  $archive_base_name = "openvpn-$conf_array[$conf_dir]";
  $archive_name = $archive_base_name.'.'.Session::GetVar('uname').".zip";
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

  unlink($archive_path);
}
?>
