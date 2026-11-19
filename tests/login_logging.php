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
 * @version     2.0.0
 */

/**
 * AI-generated test script for verifying login logging behavior across different
 * authentication scenarios, including error handling, CSRF validation,
 * rate limiting, and database failures.
 */

declare(strict_types=1);

namespace Micro\OpenvpnWebadmin\Core {
    class Database
    {
        public static string $scenario = '';

        public static function getInstance(): self
        {
            return new self();
        }

        public function getConnection(): FakeConnection
        {
            return new FakeConnection();
        }
    }

    class FakeConnection
    {
        public function prepare(string $sql): FakeStatement
        {
            if (str_contains($sql, 'LEFT JOIN') && in_array(Database::$scenario, ['fallback_success', 'database_failure'], true)) {
                throw new \PDOException('join failed');
            }
            if (str_starts_with($sql, 'SELECT *') && Database::$scenario === 'database_failure') {
                throw new \PDOException('fallback failed');
            }
            return new FakeStatement();
        }
    }

    class FakeStatement
    {
        public function execute(array $params): bool
        {
            return true;
        }

        public function fetch(): array|false
        {
            if (Database::$scenario === 'unknown_user') {
                return false;
            }
            return [
                'uid' => 42,
                'gid' => 2,
                'user_name' => 'example',
                'user_pass' => password_hash('correct-password', PASSWORD_DEFAULT),
                'role_name' => 'user',
            ];
        }
    }

    class Session
    {
        public static function verifyCsrfToken(string $token): bool
        {
            return $token === 'valid-token';
        }

        public static function regenerateId(): void
        {
        }

        public static function setVar(string $key, mixed $value): void
        {
            $_SESSION[$key] = $value;
        }
    }
}

namespace {
    use Micro\OpenvpnWebadmin\Core\Database;
    use Micro\OpenvpnWebadmin\Core\Debug;
    use Micro\OpenvpnWebadmin\Core\LoginController;
    use Micro\OpenvpnWebadmin\Core\Url;

    require dirname(__DIR__) . '/src/Core/Debug.php';
    require dirname(__DIR__) . '/src/Core/Url.php';
    require dirname(__DIR__) . '/src/Core/LoginController.php';
    class_alias(Url::class, 'Url');

    if (($argv[1] ?? '') === '--child') {
        $scenario = $argv[2];
        $dir = $argv[3];
        Database::$scenario = $scenario;
        $_ENV['DEBUG_EXCEPTIONS_LOGFILE'] = $dir . '/exceptions.log';
        Debug::init('development', $dir . '/debug.log', $scenario !== 'debug_disabled');
        session_save_path($dir);
        session_id('webadmintest');
        session_start();
        $_SESSION = [];
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_POST = [
            'username' => 'example',
            'password' => in_array($scenario, ['wrong_password', 'unknown_user', 'debug_disabled'], true)
                ? 'wrong-password'
                : 'correct-password',
            '_csrf' => $scenario === 'invalid_csrf' ? 'invalid-token' : 'valid-token',
        ];
        if ($scenario === 'empty_credentials') {
            $_POST['password'] = '';
        }
        if ($scenario === 'rate_limited') {
            $key = hash('sha256', 'example|127.0.0.1');
            $_SESSION['_login_guard'][$key] = ['lock_until' => time() + 60];
        }
        register_shutdown_function(static function (): void {
            echo isset($_SESSION['user']) ? 'authenticated' : 'not authenticated';
        });
        (new LoginController())->handleLogin();
        exit(1);
    }

    $cases = [
        'success' => [null, false],
        'fallback_success' => [null, true],
        'invalid_csrf' => ['LOGIN blocked invalid csrf', false],
        'rate_limited' => ['LOGIN blocked rate limit', false],
        'empty_credentials' => ['LOGIN empty credentials', false],
        'unknown_user' => ['LOGIN failed', false],
        'wrong_password' => ['LOGIN failed', false],
        'database_failure' => ['LOGIN failed database query', true],
        'debug_disabled' => [null, false],
    ];

    foreach ($cases as $scenario => [$expected, $expectException]) {
        $dir = sys_get_temp_dir() . '/webadmin-login-test-' . bin2hex(random_bytes(8));
        if (!mkdir($dir, 0700)) {
            throw new \RuntimeException('Cannot create test directory');
        }
        $process = proc_open([PHP_BINARY, __FILE__, '--child', $scenario, $dir], [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);
        if (!is_resource($process)) {
            throw new \RuntimeException('Cannot start test process');
        }
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $status = proc_close($process);
        $log = is_file($dir . '/debug.log') ? file_get_contents($dir . '/debug.log') : '';
        $exceptions = is_file($dir . '/exceptions.log') ? file_get_contents($dir . '/exceptions.log') : '';
        $expectedState = in_array($scenario, ['success', 'fallback_success'], true)
            ? 'authenticated' : 'not authenticated';
        $valid = $status === 0 && $stdout === $expectedState && $stderr === ''
            && ($expected === null ? $log === '' : substr_count($log, $expected) === 1 && substr_count($log, 'Argument 1 (string):') === 1)
            && ($expectException ? $exceptions !== '' : $exceptions === '')
            && !str_contains($log, 'correct-password')
            && !str_contains($log, 'wrong-password');
        foreach (['debug.log', 'exceptions.log', 'sess_webadmintest'] as $name) {
            if (is_file($dir . '/' . $name)) {
                unlink($dir . '/' . $name);
            }
        }
        rmdir($dir);
        if (!$valid) {
            throw new \RuntimeException("Login logging test failed: $scenario (exit $status, stdout: $stdout, stderr: $stderr)");
        }
        echo "$scenario: OK\n";
    }
}
