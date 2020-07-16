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
          <!-- main content ssl -->
          <div class="tab-pane fade position-relative p-3 bg-white" id="ssl" role="tabssl" aria-labelledby="ssl">
            <div class="ribbon-wrapper ribbon-lg">
              <div class="ribbon bg-success">
                <?php echo Get_Lang::nachricht('_SSL_CERTS_NEW'); ?>
              </div>
            </div>
            <?php echo Get_Lang::nachricht('_SSL_CERTS_NEW'); ?>
          </div>
          
          <div class="tab-pane fade position-relative p-3 bg-white" id="ssl2" role="tabssl" aria-labelledby="ssl">
            <div class="ribbon-wrapper ribbon-lg">
              <div class="ribbon bg-success">
              <?php echo Get_Lang::nachricht('_SSL_CERTS_EDIT'); ?>
              </div>
            </div>
            <?php echo Get_Lang::nachricht('_SSL_CERTS_EDIT'); ?>
          </div>
          
          <div class="tab-pane fade position-relative p-3 bg-white" id="ssl3" role="tabssl" aria-labelledby="ssl">
            <div class="ribbon-wrapper ribbon-lg">
              <div class="ribbon bg-success">
                <?php echo Get_Lang::nachricht('_SSL_CERTS_LIST'); ?>
              </div>
            </div>
            <?php echo Get_Lang::nachricht('_SSL_CERTS_LIST'); ?>
          </div>
          <!-- /main content ssl -->
