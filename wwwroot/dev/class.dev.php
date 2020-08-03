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
 * Development Class
 * 
 *    only works with loaded dev package
 * 
 *    collect any vars
 * 
 *    $GLOBALS['devint']->collect('give a name',$var);
 * 
 *    if you have installed debugging tools, after load the page, press debug button in the header
 * 
 *    when you like break the script and show only debugging, then call after collect with
 * 
 *    $GLOBALS['devint']->ends();
 * 
 *    this function automatically displays the last php-error when blinking the button
 */
class devel{
  /** sidebar menu code */
  const navcode ='
<!-- devel menu -->
<li class="nav-item">
 <a class="nav-link" id="dev-tab" data-toggle="pill" href="#dev" role="menu" aria-controls="dev" aria-selected="false">
   <i class="fas fa-user-secret"></i>
   <p>Dev</p>
   <span class="right badge badge-warning">【ツ】</span>
 </a>
</li>
<!-- /devel menu -->
';
  /** button header */
  const topnav = '
<!-- devel button -->
<li>
  <button type="button" class="btn btn-block btn-outline-primary btn-xs glow" data-toggle="modal" data-target="#debug">
    Debug
  </button>
</li>
<!-- /devel button -->
';
  /** when the errorhandler has a message then blink button */
  private const glow = '.glow {
    color: #FFFFFF;
    -webkit-animation: glowing 1500ms infinite;
    -moz-animation: glowing 1500ms infinite;
    -o-animation: glowing 1500ms infinite;
    animation: glowing 1500ms infinite;
  }
  @-webkit-keyframes glowing {
    0% { background-color: #0039B2; -webkit-box-shadow: 0 0 3px #0039B2; }
    50% { background-color: #ff0000; -webkit-box-shadow: 0 0 10px #ff0000; }
    100% { background-color: #0039B2; -webkit-box-shadow: 0 0 3px #0039B2; }
  }
  @-moz-keyframes glowing {
    0% { background-color: #0039B2; -moz-box-shadow: 0 0 3px #0039B2; }
    50% { background-color: #ff0000; -moz-box-shadow: 0 0 10px #ff0000; }
    100% { background-color: #0039B2; -moz-box-shadow: 0 0 3px #0039B2; }
  }
  @-o-keyframes glowing {
    0% { background-color: #0039B2; box-shadow: 0 0 3px #0039B2; }
    50% { background-color: #ff0000; box-shadow: 0 0 10px #ff0000; }
    100% { background-color: #0039B2; box-shadow: 0 0 3px #0039B2; }
  }
  @keyframes glowing {
    0% { background-color: #0039B2; box-shadow: 0 0 3px #0039B2; }
    50% { background-color: #ff0000; box-shadow: 0 0 10px #ff0000; }
    100% { background-color: #0039B2; box-shadow: 0 0 3px #0039B2; }
  }';
  /** this var collected all the calls */
  var $pflomp = array();

  /** write devel-output in java debuger */
  function cl(){
    echo '<script>';
    echo 'console.log('. json_encode( $this->pflomp ) .')';
    echo '</script>';
  }

  /** make json formated output */
  function me($input){
    $je = new jsonObject;
    $je->make_json($input);
  }
  
  /** for break and show only error messages */
  function ends(){
    ob_end_clean();
    html::head();
    self::dd();
    html::foot();
    exit;
  }

  /**
   * this function create all the debug outputs
   */
  function dd(){
echo '<div class="row">';

  /** if php errors, then print this if the call errorhandler fails */
  if (error_get_last()){

  /** php errors */
  echo "\n<style>\n".self::glow."\n</style>\n";
  echo '
  <!-- error_get_last -->
  <div class="col-md-4">
    <div class="card card-primary" style="height: inherit; width: inherit;">
      <div class="card-header">
        <h4 class="card-title">php errors</h4>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
          <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
          <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
        </div>
      </div>
      <div class="card-body overflow-auto">
        <pre style="font-size: 11px; text-align: left; background: #FCFCFC; color: Black;">
          '.trim(wordwrap(htmlspecialchars(print_r(error_get_last(), true)), 200)), PHP_EOL.'
        </pre>
      </div>
    </div>
  </div>
  <!-- /error_get_last -->';
  }

  /** 
   * Shows and distinguishes between the different types: errorhandler or collected vars
   * the button will blink only when errorhandler gives a output
   */
  $keys = (array_keys($this->pflomp));
  for($a=0;$a<count($keys);$a++){
    if($this->pflomp[$keys[$a]]){
      if ($keys[$a] == "errorhandler"){
        $colcode = "danger";
        echo "\n<style>\n".self::glow."\n</style>\n";
      }else{
        $colcode = "warning collapsed-card";
      };
  echo '
  <div class="col-md-4">
    <div class="card card-'.$colcode.'" style="height: inherit; width: inherit;">
      <div class="card-header">
        <h4 class="card-title">from => ', $keys[$a] , '</h4>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
          <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
          <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
        </div>
        <!-- /.card-tools -->
      </div>
      <!-- /.card-header -->
      <div class="card-body overflow-auto">
      <pre style="font-size: 11px; text-align: left; background: #FCFCFC; color: Black;">
          '.trim(wordwrap(htmlspecialchars(print_r($this->pflomp[$keys[$a]], true)), 200)), PHP_EOL.'
        </pre>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>';
    }
  }

echo "
</div>";
  }

  function ret_dev(){
    self::dd($this->pflomp);
  }

  /** this function collect all calls */
  function collect($key, $val){
    $this->pflomp[$key] = $val;
  }

}

/** format the error handler output and put in the collect var */
function goerror($errno, $errstr, $errfile, $errline) {
  $a = "error: [$errno]\n";
  $a.= "$errstr\n";
  $a.= "File: $errfile\n";
  $a.= "line: $errline\n";

  $GLOBALS['devint']->collect('errorhandler',$a);
}

/** set the php ini and error handler*/
error_reporting(E_ALL ^ E_NOTICE);
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('output_handler', 'goerror');
set_error_handler('goerror');

/** start devel */
$GLOBALS['devint'] = new devel;


