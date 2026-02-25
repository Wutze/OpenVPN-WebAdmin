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
$action = 'main';

if (isset($_GET['op']) && is_string($_GET['op']) && $_GET['op'] !== '') {
    $action = trim($_GET['op'], " \t\n\r\0\x0B/");
}

if ($action === '' || $action === 'main') {
    $requestPath = parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    if (is_string($requestPath)) {
        $scriptDir = rtrim(str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
        if ($scriptDir !== '' && $scriptDir !== '/' && str_starts_with($requestPath, $scriptDir . '/')) {
            $requestPath = (string)substr($requestPath, strlen($scriptDir));
        }

        $requestPath = trim($requestPath, '/');
        if ($requestPath !== '') {
            if (preg_match('#^setlang/([A-Za-z_]+)$#', $requestPath, $m)) {
                $_GET['lang'] = $m[1];
                $action = 'setlang';
            } elseif (preg_match('#^[A-Za-z0-9_-]+$#', $requestPath)) {
                $action = $requestPath;
            }
        }
    }
}

$action = $action !== '' ? $action : 'main';
$requestPath = parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$scriptDir = rtrim(str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
$rootPath = ($scriptDir === '' || $scriptDir === '/') ? '/' : $scriptDir . '/';
$indexPath = ($scriptDir === '' || $scriptDir === '/') ? '/index.php' : $scriptDir . '/index.php';

if ($action === 'dashboard' || $action === 'main') {
    $params = $_GET;
    unset($params['op']);
    $query = $params === [] ? '' : http_build_query($params);

    if (!is_string($requestPath) || $requestPath !== $rootPath || isset($_GET['op'])) {
        header('Location: ' . $rootPath . ($query === '' ? '' : ('?' . $query)));
        exit;
    }
}

if (defined('_URL_REWRITE') && _URL_REWRITE === false) {
    if (is_string($requestPath) && $requestPath !== $rootPath && $requestPath !== $indexPath) {
        $params = $_GET;
        $params['op'] = $action;
        $query = http_build_query($params);
        header('Location: ' . $rootPath . '?' . $query);
        exit;
    }
}

$out = new GoRequest($action);
$out->main();
