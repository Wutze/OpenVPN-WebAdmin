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


/**
 * give him simple Markdown formatted Text
 */

$out_osx = <<<TEXTOSX
# OSX iOS

* <https://apps.apple.com/us/app/openvpn-connect/id590379981>
* Documentation (German) <https://www.thomas-krenn.com/de/wiki/IOS_11_als_OpenVPN_Client_konfigurieren>

TEXTOSX;


/** 
 * when you like more information for your openvpn-client-user, then fill out this "ext" var
 * It is better to leave the variable empty than to delete it
 */
$out_osx_ext = <<<TEXTOSX
## More Informations
TEXTOSX;



$out_win = <<<TEXTWIN
# Windows 7, 10

* <https://openvpn.net/client-connect-vpn-for-windows/>

The full functionality of OpenVPN under Windows 10 can unfortunately only be achieved by running the program under admin rights. This applies in particular to the routing into the VPN network, which does not work without admin rights. Additionally, the client version 3 of OpenVPN is in my opinion not usable to its full extent. For this reason I recommend, especially for people who want to know what they are doing and also want to adjust the configuration, the old version 2. Here is the direct link. <https://openvpn.net/downloads/openvpn-connect-v2-windows.msi>

TEXTWIN;


$out_win_ext = <<<TEXTWIN

TEXTWIN;



$out_lin = <<<TEXTLIN
# Android

## Download Client

* <https://play.google.com/store/apps/details?id=de.blinkt.openvpn&hl=de>

## Install your VPN-Profile

* Go to "Save your ..." Link in the left on this site, download the zip file, unzip it into a separate folder, open the OpenVPN app and download the client.conf. Everything else happens automatically. Enter the password and you are ready to go.

TEXTLIN;

$helpuser = Session::GetVar('uname');
$out_lin_ext = <<<TEXTLIN
Hello $helpuser

If you have problems with your configuration, you can contact support on phone XXXX NNNNNNNNNN.

TEXTLIN;
