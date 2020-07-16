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
 * @version		1.1.0
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
 *    if you have installed debugging tools, after load the page press debug button
 * 
 *    when you like break the script, then call after collect with
 * 
 *    $GLOBALS['devint']->ends();
 */
class devel{
  const navcode ='<li class="nav-item">
 <a class="nav-link" id="dev-tab" data-toggle="pill" href="#dev" role="menu" aria-controls="dev" aria-selected="false">
   <i class="fas fa-user-secret"></i>
   <p>Dev</p>
   <span class="right badge badge-warning">【ツ】</span>
 </a>
</li>';
  const topnav = '      <li>
  <button type="button" class="btn btn-block btn-outline-primary btn-xs" data-toggle="modal" data-target="#debug">
    Debug
  </button>
</li>';
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
  
  function ends(){
    ob_end_clean();
    html::head();
    self::dd();
    html::foot();
    exit;
  }

  function dd(){
echo '<div class="row">';

  /** if php errors, then print this */
  if (error_get_last()){

echo '
  <div class="col-md-4">
  <div class="card card-primary" style="height: inherit; width: inherit;">
    <div class="card-header">
      <h4 class="card-title">php errors</h4>
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
      </div>
      <!-- /.card-tools -->
    </div>
    <!-- /.card-header -->
    <div class="card-body overflow-auto">
      <pre style="font-size: 11px; text-align: left; background: #FCFCFC; color: Black;">
        '.trim(wordwrap(htmlspecialchars(print_r(error_get_last(), true)), 200)), PHP_EOL.'
      </pre>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
  </div>';
  }



  $keys = (array_keys($this->pflomp));
  for($a=0;$a<count($keys);$a++){
    if($this->pflomp[$keys[$a]]){
echo '
  <div class="col-md-4">
    <div class="card card-warning collapsed-card" style="height: inherit; width: inherit;">
      <div class="card-header">
        <h4 class="card-title">from => ', $keys[$a] , '</h4>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
          <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
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

  function collect($key, $val){
    $this->pflomp[$key] = $val;
  }

}

error_reporting(E_ALL ^ E_NOTICE);
$GLOBALS['devint'] = new devel;


