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
                  'loadzip'=>'loadzip',
                  'delfile' => 'delfile');
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
                  'osx'=>'osx-viscosity',
                  'server' => 'server');
  var $conf_filepathname = array(
                  'win' => 'windows/client.ovpn',
                  'lin' => 'gnu-linux/client.ovpn',
                  'osx' => 'osx/client.ovpn',
                  'server' => 'server/server.conf');
  var $conf_filename = array(
                  'win' => 'client.ovpn',
                  'lin' => 'client.ovpn',
                  'osx' => 'client.ovpn',
                  'server' => 'server.conf');
  var $config_full_path = "../vpn/conf/";
  var $history_full_path = "../vpn/history/";
  var $data_temp_dir = "./data/temp/";

  /**
   * main function
   */
  function main(){

    (array_key_exists($this->action,$this->go)) ? $this->gotox = $this->go[$this->action] : $this->gotox = 'ERROR';
    ($this->isuser) ? '' : $this->gotox = 'ERROR';

		/** 
		 * only for development and debugging mode
		 * this loads the development class
		*/
		if (defined('dev')){
			$GLOBALS['devint']->collect('class.configfiles',$this);
		};

    switch($this->gotox){
      case "save";
        $this->save_config();
      break;

      case "loadzip";
        $this->load_zipfile();
      break;

      case "print";
        $this->print_configfiles();
      break;

      case "delfile";
        $this->delfile();
      break;

      case "ERROR";
        header("Location: ?op=error");
      break;
    }
  }

  /**
   * read historical config documents
   * @return html formatted history Conf-Files
   */
  function getHistory($cfg_file) {
    require_once(REAL_BASE_DIR.'/include/class/class.Diff.php');
    $random = rand(0,getrandmax()) ;
    $scanned_directory = @array_slice(scandir($this->history_full_path.$this->conf_array[$cfg_file]."/history/"), 2);
    $scanned_directory = @array_reverse($scanned_directory);
    $i = 0;
    ?>
    <div class="alert alert-info" role="alert"><b>History</b>
      <div class="panel-group" id="accordion<?php echo $random; ?>">
        <?php foreach ($scanned_directory as $file){
        $i = $i+1;
        ?>
        <div class="panel panel-primary">
          <div class="panel-primary">
            <a data-toggle="collapse" data-parent="#accordion<?php echo $random; ?>" href="#collapse<?php echo $random; ?>-<?php echo $i ?>">
            <?php
              $history_file_name = basename($file);
              $chunks = explode('_', $history_file_name);
              echo date('d.m.y', $chunks[0]);
            ?>
            </a>
          </div>
          <div id="collapse<?php echo $random; ?>-<?php echo $i ?>" class="panel-collapse collapse">
            <div class="position-relative p-3 bg-gray">
              <div class="ribbon-wrapper ribbon-lg">
                <div class="ribbon bg-danger">
                  <a href="?op=delfile&amp;file=<?php echo $chunks[0]; ?>"><?php echo date("d.m.y",$chunks[0]); ?></a>
                </div>
              </div>
              <div class="well">
                <pre>
                  <?php echo Diff::toHTML(Diff::compareFiles($this->history_full_path.$this->conf_array[$cfg_file]."/history/".$file, $this->config_full_path.$this->conf_filepathname[$cfg_file])); ?>
                </pre>
              </div>
            </div>
          </div>
        </div>
        <?php }; ?>
      </div>
    </div>
  <?php
    #return;
  }


  /**
   * save configs
   */
  function save_config(){
    /** is not admin - logout user, destroy sessions */
    ($this->isadmin) ? "" : header("Location: ?op=error");
    $timecode = time();
    $this->fullpath_with_file = $this->config_full_path.$this->conf_filepathname[$this->file];
    $this->file_category = basename($this->file);
    $this->backup_dir = $this->history_full_path."".$this->conf_array[$this->file]."/history";
    $this->backupfilename = $timecode."_".$this->conf_filename[$this->file];

    if (!file_exists($this->backup_dir)){
      mkdir($this->history_full_path.$this->conf_array[$this->file],0700,true);
      mkdir($this->backup_dir, 0700, true);
    }
    copy($this->fullpath_with_file, $this->backup_dir."/".$this->backupfilename);

    $conf_success = file_put_contents($this->fullpath_with_file, $_POST['config_content']);

    echo json_encode([
      'debug' => [
          'config_file' => $this->fullpath_with_file,
          'config_content' => $_POST['config_content']
      ],
      'config_success' => $conf_success !== false,
    ]);
  }

  function print_configfiles(){
    $cfg_file=$this->config_full_path.$this->conf_filepathname[$this->file];
    ?>
    <div class="ribbon-wrapper ribbon-lg">
      <div class="ribbon bg-warning text-lg">
        <?php echo $this->file; ?>
      </div>
    </div>
    <fieldset>
      <form class="save-form" method="post">
        <p>Attention! Restart server or clients after changes!<p/>
        <textarea class="alert-danger form-control" data-config-file="<?= $this->file ?>" name="" id="" cols="30" rows="20"><?= @file_get_contents($cfg_file) ?></textarea>
      </form>
    </fieldset>
    <?php echo self::getHistory($this->file) ?>
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
    (array_key_exists($this->file,$this->zipfile)) ? $this->fileok = 1 : $this->fileok = 0;
    if (!$this->fileok or !$this->isuser or $this->file == "server"){
      header("Location: ?op=error");
      return;
    };
    $this->rootpath = realpath($this->config_full_path.$this->conf_array[$this->file]);
    $this->testroot = $this->config_full_path.$this->conf_array[$this->file];
    $this->archive_base_name = "openvpn-".$this->zipfile[$this->file];
    $this->archive_name = $this->archive_base_name.'.'.Session::GetVar('uname').".zip";
    $this->archive_path = $this->data_temp_dir.$this->archive_name;

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

  /** not implemented yet */
  function delfile(){
    header("Location: .");
  }

}





?>
