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


 class ssl{

  public static function navcode(){
    include(REAL_BASE_DIR."/include/html/modules/ssl/main-sidebar.ssl.php");
  }

  public static function content(){
    include(REAL_BASE_DIR."/include/html/modules/ssl/main-content.ssl.php");
  }






 }
 