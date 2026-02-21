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


define('REAL_BASE_DIR', dirname(__FILE__));
require_once dirname(__DIR__) . "/src/load.php";
use Micro\OpenvpnWebadmin\Core\GoRequest;

/**
 * Start Website
 * @var mixed
 */
$out = new GoRequest();
$out->set_value('action', $_GET['op'] ?? 'main');
$out->main();
