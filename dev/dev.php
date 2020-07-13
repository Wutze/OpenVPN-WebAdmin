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
<h4>Devel</h4>
 <!-- /nicht anfassen, wird benötigt -->
<?php
/**
 * ! ab hier kannst Du die Seite aufbauen
 * 
 * 
 * 
 */

DEFINE ('ADODB_OUTP','myLogger');
 
function myLogger($msg,$discard)
{
    print "\nThis is my messsage: $msg";
} 



$rr = new get_modules;
$rr->search_mod_dir();
$re = '/([a-z]{2})/';
foreach($rr->loaddir as $value) {
  #preg_match($re, $value, $matches);
  #echo $matches[0];
  echo $value;
  echo "<br>";
}
$GLOBALS['devint']->collect('dev.php languages',$_REQUEST);
$GLOBALS['devint']->collect('dev.php globals',$GLOBALS);
#$GLOBALS['devint']->ends();
/**
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
