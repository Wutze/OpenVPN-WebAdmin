<?php
declare(strict_types=1);

use Micro\OpenvpnWebadmin\Core\Database;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__, 2);
$config = require $root . '/config/config.php';
require $root . '/vendor/autoload.php';

$migration = file_get_contents(__DIR__ . '/migrations/001_login_attempts.sql');
if ($migration === false) {
    throw new RuntimeException('Unable to read login-attempts database migration.');
}

Database::getInstance($config['db'])->getConnection()->exec($migration);
echo "Login-attempts database migration applied.\n";
