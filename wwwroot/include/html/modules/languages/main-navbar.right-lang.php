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
 * @version		1.1.1
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');

$rr = new get_modules;
$rr->load_languages();
$re = '/([a-z]{2})/';
preg_match($re, Session::GetVar('lang'), $setcurrlang);
?>
      <!-- language menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="flag-icon flag-icon-<?php echo (($setcurrlang[0]== "en")? "gb": $setcurrlang[0]) ; ?>"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <form action="/" method="post">
        <input type="hidden" name="op" value="language">
<?php
foreach($rr->loaddir as $value) { # foreach
  preg_match($re, $value, $matches);
?>
          <button class="dropdown-item" name="lang" value="<?php echo $value; ?>">
            <div class="media">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                <?php echo $value; ?>
                  <span class="float-right text-sm text-danger"><i class="flag-icon flag-icon-<?php echo (($matches[0]== "en")? "gb": $matches[0]) ; ?> mr-1"></i></span>
                </h3>
              </div>
            </div>
          </button>
<?php
}; # /foreach
?>
        </form>
        </div>
      </li>
      <!-- /end language menu -->