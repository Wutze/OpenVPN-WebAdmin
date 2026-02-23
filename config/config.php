<?php
/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020/2025 OpenVPN-WebAdmin
 *
 * NOTICE OF LICENSE
 *
 * GNU AFFERO GENERAL PUBLIC LICENSE V3
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/agpl-3.0.en.html
 *
 * @author      Wutze
 * @copyright   2025 OpenVPN-WebAdmin
 * @link        https://github.com/Wutze/OpenVPN-WebAdmin
 * @see         Internal Documentation ~/doc/
 * @version     2.0.0
 */

/**
 * Zentrale Konfiguration
 */

return [
    'debug' => filter_var(getenv('DEBUG') ?: 'false', FILTER_VALIDATE_BOOL),
    'rewrite' => filter_var(getenv('REWRITE') ?: 'false', FILTER_VALIDATE_BOOL),
    'loginpath' => 'login1',
    /**
     * die Variable sitetools gibt an, wo deine ganzen JS und CSS Dateien liegen
     * das hier gibt dir die Möglichkeit sie von einem externen zentralen Server zu laden
     * @option 'FQDN' | '/tools' von deiner Webseite ohne Backslash am Ende!
     */
    'sitetools' => 'https://tools.home',


    // Datenbank
    'db' => [
        'host' => 'db2.home',
        'port' => 3306,
        'dbname' => 'ovpn-admin2',
        'user' => 'ovpn-admin2',
        'pass' => 'ovpn-admin2',
        'charset' => 'utf8mb4',
    ],

    // Session
    'session' => [
        'table'    => 'sessions2',
        'lifetime' => 3600,  // 1 Stunde
    ],

    // Sprache
    'lang' => 'de_DE', // kann z. B. aus URL ermittelt werden

    // Server-Konfiguration für Settings-Editor
    // source_url: geschuetzter interner Link (optional)
    // source_path: lokaler Lesepfad (optional)
    // save_path: lokaler Schreibpfad für edits
    'vpn_server_config' => [
        'source_url' => getenv('OVPN_SERVER_CONFIG_URL') ?: '',
        'source_path' => getenv('OVPN_SERVER_CONFIG_PATH') ?: (dirname(__DIR__) . '/storage/conf/server/server.conf'),
        'save_path' => getenv('OVPN_SERVER_CONFIG_SAVE_PATH') ?: (dirname(__DIR__) . '/storage/conf/server/server.conf'),
        'history_path' => getenv('OVPN_SERVER_CONFIG_HISTORY_PATH') ?: (dirname(__DIR__) . '/storage/conf/history/server'),
        // z.B. "Bearer abc123"
        'auth_header' => getenv('OVPN_SERVER_CONFIG_AUTH_HEADER') ?: '',
        'timeout' => 6,
    ],
];
