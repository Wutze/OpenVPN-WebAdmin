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
 * @link      https://github.com/Wutze/OpenVPN-WebAdmin
 * @see       Internal Documentation ~/doc/
 * @version   1.4.1
 * @todo      new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');

?>
      <!-- user-card -->
      <div class="row">
        <div class="col-md-12 col-xs-12">
          <div class="card card-default">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-bullhorn"></i>
                  Main-Site
              </h3>
            </div>
            <!-- /.user-card-header -->
            <div class="card-body">
              <div class="callout callout-danger">
                <h5>Herzlich Willkommen</h5>
                  <p>Das ist der OpenVPN-Server von: </p>
              </div>
              <div class="callout callout-info">
                <h5>Deine Informationen</h5>
                  <p>Du kannst hier alle relevanten Informationen zu Deinem Account finden.</p>
              </div>
              <div class="callout callout-warning">
                <h5>Passwort ändern</h5>
                  <p>Klicke auf Your AAccount und Du kannst Dein Passwort ändern.</p>
              </div>
              <div class="callout callout-success">
                <h5>Konfiguŕation speichern</h5>
                  <p>Unter Save Your Config File kannst Du die aktuelle Konfiguration zum Server downloaden.</p>
              </div>
            </div>
            <!-- /.user-card-body -->
          </div>
        </div>
      </div>
      <!-- /.user-card -->