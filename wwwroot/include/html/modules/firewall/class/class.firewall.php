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
 * @version		1.3.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');


/**
 * This class enables the firewall configuration for editing as soon as activated.
 * This class is also used with the advanced firewall configuration.
 * Class to be extended later
 *
 * @see config.php -> to enable	define('firewall',TRUE);
 */
class firewall{
  /** sidebar menu code */
  const navcode ='
                    <!-- firewall menu -->
                    <li class="nav-item">
                      <a class="nav-link" id="dev-tab" data-toggle="pill" href="#fw" role="menu" aria-controls="fw" aria-selected="false">
                        <i class="fa fa-bomb"></i>
                        <p>Firewall</p>
                        <span class="right badge badge-danger">⟰</span>
                      </a>
                    </li>
                    <!-- /firewall menu -->
';
  /** this const open the tabpanel for firewall content */
  const content_head = '
              <div class="tab-pane fade" id="fw" role="tabpanel" aria-labelledby="fw-tab">
                <div class="col-12 col-sm-12">';
  /** this const close the tabpanel for firewall content */
  const content_foot = '
                </div>
              </div>';

  /**
   * Function to grab the Firewall und prepare for output in Content
   * @return generate html-code
   */
  function content(){
    $out = new configfiles;
    $out->set_value('action','print');
    $out->set_value('isuser',Session::GetVar('isuser'));
    $out->set_value('isadmin',Session::GetVar('isadmin'));
    $out->set_value('file','firewall');
    $out->set_value('attention','Du musst wissen was Du tust!');
    $out->main();
  }



}

