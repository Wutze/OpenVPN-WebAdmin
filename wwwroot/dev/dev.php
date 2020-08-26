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



 ?>
 <!-- nicht anfassen, wird benötigt -->
            <div class="tab-pane fade" id="dev" role="tabpanel" aria-labelledby="dev-tab">
            <div class="col-12 col-sm-12">
              <div class="ribbon-wrapper ribbon-lg">
                <div class="ribbon bg-danger">
                  DEV
                </div>
              </div>
<h4 class='callout'>Devel</h4>
 <!-- /nicht anfassen, wird benötigt -->
<?php
/**
 * ! ab hier kannst Du die Seite aufbauen
 * ▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼
 */


 
echo "<div class='callout callout-success'>Welcome in the developer hell</div>";

function scan_dir($path){
  $getdir=new RecursiveDirectoryIterator($path,FilesystemIterator::SKIP_DOTS);

  $bytestotal=0;
  $nbfiles=0;
  foreach (new RecursiveIteratorIterator($getdir) as $filename=>$alldata) {
    $filesize=$alldata->getSize();
    $bytestotal+=$filesize;
    $nbfiles++;
    $files[] = $filename;
  }

  $bytestotal=number_format($bytestotal);

  return array('total_files'=>$nbfiles,'total_size'=>$bytestotal,'files'=>$files);
}

$test = scan_dir(REAL_BASE_DIR);


?>

        <div class="row">
          <div class=".col-xs-3 col-md-6">
            <div class="info-box mb-3 bg-warning">
              <span class="info-box-icon"><i class="fas fa-tag"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Files</span>
                <span class="info-box-number"><?php echo $test['total_files']; ?></span>
              </div>
            </div>
            <div class="info-box mb-3 bg-success">
              <span class="info-box-icon"><i class="far fa-heart"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Size</span>
                <span class="info-box-number"><?php echo $test['total_size']; ?> bytes</span>
              </div>
            </div>
          </div>
          <div class=".col-xs-3 col-md-6">
            <div class="info-box mb-3 bg-danger">
              <span class="info-box-icon"><i class="fas fa-cloud-download-alt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Downloads</span>
                <span class="info-box-number">45</span>
              </div>
            </div>
            <div class="info-box mb-3 bg-info">
              <span class="info-box-icon"><i class="far fa-comment"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Issues</span>
                <span class="info-box-number">3</span>
              </div>
            </div>
          </div>
        </div>
        


<!-- Link trigger modal -->
<a href="?op=loadmodul&modname=user&go=getuserdata&uid=1" data-remote="false" data-toggle="modal" data-uuid="xxx" data-target="#ExtendUserModal" class="btn btn-default">
    Launch Modal
</a>

<!-- Default bootstrap modal example -->
<div class="modal fade" id="ExtendUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>



<?php





 
/**
 * ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲
 * ! bis hier her wird die Seite gebraucht
 * 
 */

?>
 <!-- nicht anfassen, wird benötigt -->
            </div>
          </div>
<!-- Modal -->
<div class="modal fade" id="debug" tabindex="-1" role="dialog" aria-labelledby="debugTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Debugging</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php $GLOBALS['devint']->ret_dev(); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <a type="button" class="btn btn-primary" href="/">reload</a>
      </div>
    </div>
  </div>
</div>
<?php $GLOBALS['devint']->cl(); ?>
 <!-- /nicht anfassen, wird benötigt -->
