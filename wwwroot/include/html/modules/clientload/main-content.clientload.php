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
 * @version		1.4.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');

/** load Markdown Class */
include_once(REAL_BASE_DIR.'/include/html/modules/clientload/class/Parsedown.php');
/** 
 * look in folder /data/clientload if there a file named [ language ].clientload.content.php, otherwise take the original file de_DE
 * 
 */
if(file_exists(REAL_BASE_DIR.'/data/clientload/'.Session::GetVar('lang').'.clientload.content.php')){
  include_once(REAL_BASE_DIR.'/data/clientload/'.Session::GetVar('lang').'.clientload.content.php');
}else{
  include_once(REAL_BASE_DIR.'/data/clientload/en_EN.clientload.content.example.php');
};

$print_markdown = New Parsedown;
$print_markdown->setSafeMode(true);

?>

          <!-- main content clientload -->
          <div class="tab-pane fade position-relative p-3 bg-white" id="cllosx" role="tabpanel" aria-labelledby="cllosx">
            <div class="ribbon-wrapper ribbon-lg">
              <div class="ribbon bg-cemetery">
                <?php echo Get_Lang::nachricht('_VPN_CLIENT_OSX'); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="card card-default">
                  <div class="card-header bg-cemetery">
                    <h3 class="card-title">
                      <ion-icon name="logo-apple"></ion-icon>
                      <?php echo Get_Lang::nachricht('_VPN_CLIENT_OSX'); ?>

                    </h3>
                  </div>
                  <div class="card-body">
                    <?php echo $print_markdown->text($out_osx); ?>
                  </div>
                </div>
              </div>

<?php
if($out_osx_ext){
?>

              <div class="col-md-6">
                <div class="card card-default">
                  <div class="card-header bg-cemetery">
                    <h3 class="card-title">
                      <ion-icon name="logo-apple"></ion-icon>
                      <?php echo Get_Lang::nachricht('_VPN_CLIENT_EXT'); ?>

                    </h3>
                  </div>
                  <div class="card-body">
                    <?php echo $print_markdown->text($out_osx_ext); ?>
                  </div>
                </div>
              </div>


<?php }; ?>

            </div>
          </div>
          
          <div class="tab-pane fade position-relative p-3 bg-white" id="cllwin" role="tabpanel" aria-labelledby="cllwin">
            <div class="ribbon-wrapper ribbon-lg">
              <div class="ribbon bg-cemetery">
              <?php echo Get_Lang::nachricht('_VPN_CLIENT_WIN'); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="card card-default">
                  <div class="card-header bg-cemetery">
                    <h3 class="card-title">
                    <ion-icon name="logo-windows"></ion-icon>

                      <?php echo Get_Lang::nachricht('_VPN_CLIENT_WIN'); ?>

                    </h3>
                  </div>
                  <div class="card-body">
                    <?php echo $print_markdown->text($out_win); ?>
                  </div>
                </div>
              </div>

<?php
if($out_win_ext){
?>
              <div class="col-md-6">
                <div class="card card-default">
                  <div class="card-header bg-cemetery">
                    <h3 class="card-title">
                    <ion-icon name="logo-windows"></ion-icon>

                      <?php echo Get_Lang::nachricht('_VPN_CLIENT_EXT'); ?>

                    </h3>
                  </div>
                  <div class="card-body">
                    <?php echo $print_markdown->text($out_win_ext); ?>
                  </div>
                </div>
              </div>

<?php }; ?>


            </div>
          </div>
          
          <div class="tab-pane fade position-relative p-3 bg-white" id="clllin" role="tabpanel" aria-labelledby="clllin">
            <div class="ribbon-wrapper ribbon-lg">
              <div class="ribbon bg-cemetery">
                <?php echo Get_Lang::nachricht('_VPN_CLIENT_LIN'); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="card card-default">
                  <div class="card-header bg-cemetery">
                    <h3 class="card-title">
                    <ion-icon name="logo-android"></ion-icon>
                      <?php echo Get_Lang::nachricht('_VPN_CLIENT_LIN'); ?>

                    </h3>
                  </div>
                  <div class="card-body">
                    <?php echo $print_markdown->text($out_lin); ?>
                  </div>
                </div>
              </div>

<?php
if($out_lin_ext){
?>
              <div class="col-md-6">
                <div class="card card-default">
                  <div class="card-header bg-cemetery">
                    <h3 class="card-title">
                    <ion-icon name="logo-android"></ion-icon>

                      <?php echo Get_Lang::nachricht('_VPN_CLIENT_EXT'); ?>

                    </h3>
                  </div>
                  <div class="card-body">
                    <?php echo $print_markdown->text($out_lin_ext); ?>
                  </div>
                </div>
              </div>

<?php }; ?>

            </div>
          </div>
          <!-- /main content clieantload -->
