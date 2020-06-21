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
 * @version		1.0.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');


/**
 * load/save config.files, create zip files
 * the main function allows only defined calls - see var $go
 *
 * @return 
 */

class config_files{
  var $go = array('savefile'=>'save',
                  'print'=>'print',
                  'loadzip'=>'loadzip');
  var $files = array('server'=>'server',
                  'win'=>'win',
                  'lin'=>'lin',
                  'osx'=>'osx');
  var $zipfile = array(
                  'osx' => 'osx',
                  'win' => 'win',
                  'lin' => 'lin');
  var $conf_array = array(
                  'win'=>'windows',
                  'lin'=>'gnu-linux',
                  'osx'=>'osx-viscosity');
  
  var $config_full_path = "../vpn/conf/server/";

  /**
   * main function
   */
  function main(){


#debug($this);


    (array_key_exists($this->action,$this->go)) ? $this->gotox = $this->go[$this->action] : $this->gotox = 'ERROR';
    ($this->isuser) ? '' : $this->gotox = 'ERROR';
    switch($this->gotox){
      case "save";
        $this->save_config();
      break;

      case "loadzip";
        $this->load_zipfile();
      break;

      case "print";
        $this->configfiles();
      break;

      case "ERROR";
        echo "error";
      break;
    }
  }

  /**
   * read historical config documents
   * @return html formatted history Conf-Files
   */
  function getHistory($cfg_file) {
    require_once(REAL_BASE_DIR.'/include/class/class.Diff.php');
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
            <div class="position-relative p-3 bg-gray">
              <div class="ribbon-wrapper ribbon-lg">
                <div class="ribbon bg-danger">
                  <?php echo date("d.m.y",$chunks[0]); ?>
                </div>
              </div>
              <div class="well">
                <pre>
                  <?php echo Diff::toHTML(Diff::compareFiles($file, '../vpn/conf/server/server.conf')); ?>
                </pre>
              </div>
            </div>
          </div>
          
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php
    #return;
  }


  /**
   * save config server
   */
  function save_config(){
    /** is not admin - logout user, destroy sessions */
    ($this->isadmin) ? "" : header("Location: ?op=error");
    $this->config_name = basename($_POST['config_file']); // config file name only (without path)
    $this->config_parent_dir = basename($this->config_full_path); // name of the dir that contains the config file (without path)

    /*
     * create backup for history
     *
     */
    if (!file_exists($dir=$this->config_full_path."/history"))
       mkdir($dir, 0700, true);
    $ts = time();
    copy($this->config_full_path."/".$this->config_name, $this->config_full_path."/history/".$ts."_".$this->config_name);
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

  function configfiles(){
    ?>
    <div class="tab-pane fade position-relative p-3 bg-light" id="config" role="tabpanel" aria-labelledby="config-tab">
    <div class="ribbon-wrapper ribbon-lg">
      <div class="ribbon bg-primary">
        <?php echo GET_Lang::nachricht('_SERVER_CONFIG'); ?>
      </div>
    </div>
    <fieldset>
      <form class="save-form" method="post">
        <p>Attention! Restart server after changes!<p/>
        <textarea class="alert-danger form-control" data-config-file="<?= $cfg_file='../vpn/conf/server/server.conf' ?>" name="" id="" cols="30" rows="20"><?= file_get_contents($cfg_file) ?></textarea>
        
      </form>
    </fieldset>
    <?php echo self::getHistory($cfg_file) ?>
  </div>
  <?php 
  }

  /**
  * set value
  * @return defined vars for this class
  */
  function set_value($key, $val){
      $this->$key = $val;
  }


  function load_zipfile(){
    (array_key_exists($this->file,$this->conf_array)) ? $this->fileok = TRUE : $this->fileok = FALSE;
    ($this->fileok) ? "" : header("Location: ?op=error");
    $this->rootpath = realpath("./../vpn/conf/".$this->conf_array[$this->file]);
    $this->testroot = "./../vpn/conf/".$this->conf_array[$this->file];
    $this->archive_base_name = "openvpn-".$this->conf_array[$this->file];
    $this->archive_name = $this->archive_base_name.'.'.Session::GetVar('uname').".zip";
    $this->archive_path = "./data/temp/$this->archive_name";

    $zip = new ZipArchive();
    $zip->open($this->archive_path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
  
    $files = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($this->rootpath),
      RecursiveIteratorIterator::LEAVES_ONLY
    );
  
    foreach ($files as $name => $file) {
      // Skip directories (they would be added automatically)
      if (!$file->isDir()) {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($this->rootpath) + 1);
  
        // Add current file to archive
        $zip->addFile($filePath, "$this->archive_base_name/$relativePath");
      }
    }
  
    // Zip archive will be created only after closing object
    $zip->close();

    //then send the headers to foce download the zip file
    header("Content-type: application/zip");
    header("Content-Disposition: attachment; filename=".$this->archive_name);
    header("Pragma: no-cache");
    header("Expires: 0");
    readfile($this->archive_path);
  
    unlink($this->archive_path);
  }
}





?>
