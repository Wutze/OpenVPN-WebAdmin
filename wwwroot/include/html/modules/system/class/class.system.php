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
class system{
  /** sidebar menu code */
  const navcode ='
                    <!-- system menu -->
                    <li class="nav-item">
                    <a class="nav-link" id="dev-tab" data-toggle="pill" href="#syss" role="menu" aria-controls="sys" aria-selected="false">
                      <i class="fa fa-medkit"></i>
                      <p>System</p>
                    </a>
                    </li>
                    <!-- /firewall menu -->
';
  /** this const open the tabpanel for firewall content */
  const content_head = '
              <div class="tab-pane fade" id="syss" role="tabpanel" aria-labelledby="syss-tab">
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
    $out = "<button>Restart Firewall</button>";
    $out.= "<button>Restart OpenVPN-Server</button>";
    $out.= "<button>Restart System</button>";
    return $out;
  }



}

