<?php
/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020/2026 OpenVPN-WebAdmin
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
 * @version     2.0.1
 */

declare(strict_types=1);

use Micro\OpenvpnWebadmin\Core\VersionCheckService;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__, 3) . '/src/Core/VersionCheckService.php';

try {
    $configPath = $argv[1] ?? '';
    if (!is_file($configPath)) {
        throw new RuntimeException('Application configuration not found.');
    }
    $config = require $configPath;
    $host = $config['update_check_dns_host'] ?? 'ovpn.361-grad.eu';
    if (!is_string($host)) {
        throw new RuntimeException('Invalid release DNS host.');
    }
    $root = dirname(dirname($configPath));
    $version = VersionCheckService::getCachedReleaseVersion($host, $root . '/storage/cache');
    if ($version === null) {
        exit(2);
    }
    echo $version, "\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Release cache error: ' . $e->getMessage() . "\n");
    exit(1);
}
