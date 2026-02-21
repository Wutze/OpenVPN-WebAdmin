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

namespace Micro\OpenvpnWebadmin\Core;

class DataController
{
    /**
     * Kurzbeschreibung Funktion handle
     *
     * @return void
     */
public function handle(): void
    {
        $select = (string)($_GET['select'] ?? '');
        if (!Session::isUser()) {
            $this->json(['status' => 'error', 'message' => $this->msg('_API_NOT_LOGGED_IN')], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->handleGet($select);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($select);
            return;
        }

        $this->json(['status' => 'error', 'message' => $this->msg('_API_METHOD_NOT_ALLOWED')], 405);
    }

    /**
     * Kurzbeschreibung Funktion handleGet
     *
     * @param mixed $select
     * @return void
     */
private function handleGet(string $select): void
    {
        switch ($select) {
            case 'user':
                $this->requireAdminJson();
                $this->json($this->getUsers());
                break;
            case 'log':
                $this->requireAdminJson();
                $this->json($this->getLogs());
                break;
            case 'profiles':
                $service = new ProfileService();
                $this->json([
                    'status' => 'ok',
                    'rows' => $service->listSystems(),
                ]);
                break;
            case 'config':
                $this->requireAdminJson();
                $this->handleConfigGet();
                break;
            case 'settings':
                $this->requireAdminJson();
                $this->handleSettingsGet();
                break;
            case 'dashboard_stats':
                $this->requireAdminJson();
                $this->json($this->getDashboardStats());
                break;
            case 'diag_login':
                $this->json($this->getLoginDiagnostics());
                break;
            default:
                $this->json(['status' => 'error', 'message' => $this->msg('_API_UNKNOWN_DATA_SOURCE')], 404);
        }
    }

    /**
     * Kurzbeschreibung Funktion handlePost
     *
     * @param mixed $select
     * @return void
     */
private function handlePost(string $select): void
    {
        switch ($select) {
            case 'user':
                $this->requireAdminJson();
                $this->handleUserAction();
                break;
            case 'account':
                $this->handleAccountAction();
                break;
            case 'profiles':
                $this->requireAdminJson();
                $this->handleProfileAction();
                break;
            case 'config':
                $this->requireAdminJson();
                $this->handleConfigPost();
                break;
            case 'settings':
                $this->requireAdminJson();
                $this->handleSettingsPost();
                break;
            default:
                $this->json(['status' => 'error', 'message' => $this->msg('_API_UNKNOWN_DATA_SOURCE')], 404);
        }
    }

    /**
     * Kurzbeschreibung Funktion getUsers
     *
     * @return array
     */
private function getUsers(): array
    {
        $model = new UserModel();

        $limit = max(1, (int)($_GET['limit'] ?? 50));
        $offset = max(0, (int)($_GET['offset'] ?? 0));
        $search = trim((string)($_GET['search'] ?? ''));

        return [
            'total' => $model->countUsers($search),
            'rows' => $model->getAllUsers($limit, $offset, $search),
        ];
    }

    /**
     * Kurzbeschreibung Funktion getLogs
     *
     * @return array
     */
private function getLogs(): array
    {
        $model = new LogModel();
        $limit = max(1, (int)($_GET['limit'] ?? 50));
        $offset = max(0, (int)($_GET['offset'] ?? 0));
        $search = trim((string)($_GET['search'] ?? ''));

        return [
            'total' => $model->countLogs($search),
            'rows' => $model->getAllLogs($limit, $offset, $search),
        ];
    }

    /**
     * Kurzbeschreibung Funktion getDashboardStats
     *
     * @return array
     */
private function getDashboardStats(): array
    {
        $userModel = new UserModel();
        $totalUsers = $userModel->countUsers('');
        $onlineUsers = $userModel->countOnlineUsers();

        $load = sys_getloadavg();
        $load1 = is_array($load) ? round((float)($load[0] ?? 0.0), 2) : 0.0;

        $diskPath = '/';
        $diskTotal = @disk_total_space($diskPath);
        $diskFree = @disk_free_space($diskPath);
        $diskUsedPercent = 0.0;
        if ($diskTotal && $diskTotal > 0 && $diskFree !== false) {
            $diskUsedPercent = round((1 - ($diskFree / $diskTotal)) * 100, 1);
        }

        // Dummy for now; source will be wired later.
        $errorLogCount = 0;

        return [
            'status' => 'ok',
            'load_1m' => $load1,
            'disk_used_percent' => $diskUsedPercent,
            'users_total' => $totalUsers,
            'users_online' => $onlineUsers,
            'error_count' => $errorLogCount,
            'error_count_dummy' => true,
        ];
    }

    /**
     * Kurzbeschreibung Funktion handleUserAction
     *
     * @return void
     */
private function handleUserAction(): void
    {
        $action = (string)($_POST['action'] ?? '');
        $model = new UserModel();

        try {
            switch ($action) {
                case 'create':
                    $username = trim((string)($_POST['username'] ?? ''));
                    $password = (string)($_POST['password'] ?? '');
                    $isAdmin = ((string)($_POST['is_admin'] ?? '0')) === '1';
                    $this->assertValidUsername($username);

                    if ($username === '' || $password === '') {
                        $this->json(['status' => 'error', 'message' => $this->msg('_API_USER_USERNAME_PASSWORD_REQUIRED')], 422);
                    }
                    if ($model->userExists($username)) {
                        $this->json(['status' => 'error', 'message' => $this->msg('_API_USER_EXISTS')], 409);
                    }

                    $model->createUser($username, $password, $isAdmin);
                    $this->json(['status' => 'ok', 'message' => $this->msg('_API_USER_CREATED')]);
                    break;

                case 'delete':
                    $username = trim((string)($_POST['username'] ?? ''));
                    $this->assertValidUsername($username);
                    if ($username === '') {
                        $this->json(['status' => 'error', 'message' => $this->msg('_API_USERNAME_MISSING')], 422);
                    }

                    $current = Session::getUser();
                    if (($current['name'] ?? '') === $username) {
                        $this->json(['status' => 'error', 'message' => $this->msg('_API_USER_DELETE_SELF_FORBIDDEN')], 422);
                    }

                    $model->deleteUser($username);
                    $this->json(['status' => 'ok', 'message' => $this->msg('_MSG_USER_DELETED')]);
                    break;

                case 'set_role':
                    $username = trim((string)($_POST['username'] ?? ''));
                    $isAdmin = ((string)($_POST['is_admin'] ?? '0')) === '1';
                    $this->assertValidUsername($username);
                    if ($username === '') {
                        $this->json(['status' => 'error', 'message' => $this->msg('_API_USERNAME_MISSING')], 422);
                    }

                    $model->setUserRole($username, $isAdmin);
                    $this->json(['status' => 'ok', 'message' => $this->msg('_API_ROLE_UPDATED')]);
                    break;

                case 'set_enabled':
                    $username = trim((string)($_POST['username'] ?? ''));
                    $enabled = ((string)($_POST['enabled'] ?? '0')) === '1';
                    $this->assertValidUsername($username);
                    if ($username === '') {
                        $this->json(['status' => 'error', 'message' => $this->msg('_API_USERNAME_MISSING')], 422);
                    }

                    $model->setUserEnabled($username, $enabled);
                    $this->json(['status' => 'ok', 'message' => $this->msg('_API_STATUS_UPDATED')]);
                    break;

                case 'set_password':
                    $username = trim((string)($_POST['username'] ?? ''));
                    $password = (string)($_POST['password'] ?? '');
                    $this->assertValidUsername($username);
                    if ($username === '' || $password === '') {
                        $this->json(['status' => 'error', 'message' => $this->msg('_API_USER_USERNAME_PASSWORD_REQUIRED')], 422);
                    }

                    $model->setUserPasswordByName($username, $password);
                    $this->json(['status' => 'ok', 'message' => $this->msg('_API_PASSWORD_UPDATED')]);
                    break;

                case 'set_limits':
                    $username = trim((string)($_POST['username'] ?? ''));
                    $startDate = trim((string)($_POST['start_date'] ?? ''));
                    $endDate = trim((string)($_POST['end_date'] ?? ''));
                    $this->assertValidUsername($username);

                    if ($username === '') {
                        $this->json(['status' => 'error', 'message' => $this->msg('_API_USERNAME_MISSING')], 422);
                    }

                    $model->setUserLimits($username, $startDate !== '' ? $startDate : null, $endDate !== '' ? $endDate : null);
                    $this->json(['status' => 'ok', 'message' => $this->msg('_API_LIMITS_UPDATED')]);
                    break;

                default:
                    $this->json(['status' => 'error', 'message' => $this->msg('_API_USER_ACTION_UNKNOWN')], 400);
            }
        } catch (\Throwable $e) {
            $this->internalError('handleUserAction', $e);
        }
    }

    /**
     * Kurzbeschreibung Funktion handleAccountAction
     *
     * @return void
     */
private function handleAccountAction(): void
    {
        try {
            $action = (string)($_POST['action'] ?? '');
            if ($action !== 'change_password') {
                $this->json(['status' => 'error', 'message' => $this->msg('_API_ACCOUNT_ACTION_UNKNOWN')], 400);
            }

            $currentPassword = (string)($_POST['current_password'] ?? '');
            $newPassword = (string)($_POST['new_password'] ?? '');

            if ($currentPassword === '' || $newPassword === '') {
                $this->json(['status' => 'error', 'message' => $this->msg('_API_ACCOUNT_PASSWORD_FIELDS_REQUIRED')], 422);
            }

            if (strlen($newPassword) < 8) {
                $this->json(['status' => 'error', 'message' => $this->msg('_API_ACCOUNT_PASSWORD_TOO_SHORT')], 422);
            }

            $user = Session::getUser();
            $uid = (int)($user['uid'] ?? 0);
            if ($uid <= 0) {
                $this->json(['status' => 'error', 'message' => $this->msg('_API_SESSION_INVALID')], 401);
            }

            $model = new UserModel();
            if (!$model->verifyPassword($uid, $currentPassword)) {
                $this->json(['status' => 'error', 'message' => $this->msg('_API_ACCOUNT_CURRENT_PASSWORD_INVALID')], 422);
            }

            $model->setUserPasswordById($uid, $newPassword);
            $this->json(['status' => 'ok', 'message' => $this->msg('_API_ACCOUNT_PASSWORD_CHANGED')]);
        } catch (\Throwable $e) {
            $this->internalError('handleAccountAction', $e);
        }
    }

    /**
     * Kurzbeschreibung Funktion handleProfileAction
     *
     * @return void
     */
private function handleProfileAction(): void
    {
        $action = (string)($_POST['action'] ?? '');
        if ($action !== 'build_zip') {
            $this->json(['status' => 'error', 'message' => $this->msg('_API_PROFILE_ACTION_UNKNOWN')], 400);
        }

        $system = (string)($_POST['system'] ?? '');
        $service = new ProfileService();

        try {
            $zipPath = $service->buildZip($system);
            $this->json([
                'status' => 'ok',
                'message' => $this->msg('_API_PROFILE_ZIP_CREATED'),
                'zip_file' => basename($zipPath),
            ]);
        } catch (\Throwable $e) {
            $this->internalError('handleProfileAction', $e);
        }
    }

    /**
     * Kurzbeschreibung Funktion handleConfigGet
     *
     * @return void
     */
private function handleConfigGet(): void
    {
        $service = new ConfigService();
        $action = (string)($_GET['action'] ?? 'systems');

        try {
            switch ($action) {
                case 'systems':
                    $this->json([
                        'status' => 'ok',
                        'systems' => $service->listSystems(),
                    ]);
                    break;
                case 'get':
                    $system = (string)($_GET['system'] ?? '');
                    $this->json([
                        'status' => 'ok',
                        'system' => $system,
                        'content' => $service->getConfig($system),
                        'history' => $service->getDiffStatsAgainstCurrent($system),
                    ]);
                    break;
                case 'diff':
                    $system = (string)($_GET['system'] ?? '');
                    $historyFile = (string)($_GET['history_file'] ?? '');
                    $diff = $service->diffHistoryFile($system, $historyFile);
                    $this->json([
                        'status' => 'ok',
                        'system' => $system,
                        'history_file' => $historyFile,
                        'added' => $diff['added'],
                        'removed' => $diff['removed'],
                        'diff' => $diff['diff'],
                    ]);
                    break;
                default:
                    $this->json(['status' => 'error', 'message' => $this->msg('_API_CONFIG_GET_ACTION_UNKNOWN')], 400);
            }
        } catch (\Throwable $e) {
            $this->internalError('handleConfigGet', $e);
        }
    }

    /**
     * Kurzbeschreibung Funktion handleConfigPost
     *
     * @return void
     */
private function handleConfigPost(): void
    {
        $service = new ConfigService();
        $action = (string)($_POST['action'] ?? '');

        if ($action !== 'save') {
            $this->json(['status' => 'error', 'message' => $this->msg('_API_CONFIG_POST_ACTION_UNKNOWN')], 400);
        }

        $system = (string)($_POST['system'] ?? '');
        $content = (string)($_POST['content'] ?? '');

        $missing = [];
        foreach (['client', '<ca>', '</ca>', '<tls-auth>', '</tls-auth>'] as $needle) {
            if (!str_contains($content, $needle)) {
                $missing[] = $needle;
            }
        }
        if ($missing !== []) {
            $this->json([
                'status' => 'error',
                'message' => $this->msgf('_API_REQUIRED_PARTS_MISSING', implode(', ', $missing)),
            ], 422);
        }

        try {
            $result = $service->saveConfig($system, $content);

            // ZIP direkt nach Änderungen aktualisieren
            try {
                (new ProfileService())->buildZip($system);
            } catch (\Throwable $e) {
                // Kein harter Fehler für Config-Save
            }

            $this->json([
                'status' => 'ok',
                'message' => $this->msg('_API_CONFIG_SAVED'),
                'system' => $system,
                'history_file' => $result['history_file'],
            ]);
        } catch (\Throwable $e) {
            $this->internalError('handleConfigPost', $e);
        }
    }

    /**
     * Kurzbeschreibung Funktion handleSettingsGet
     *
     * @return void
     */
private function handleSettingsGet(): void
    {
        $service = new ServerSettingsService();
        $action = (string)($_GET['action'] ?? 'get');

        try {
            switch ($action) {
                case 'get':
                    $data = $service->getCurrent();
                    $this->json([
                        'status' => 'ok',
                        'content' => $data['content'],
                        'source' => $data['source'],
                        'save_path' => $data['save_path'],
                        'history' => $data['history'],
                    ]);
                    break;
                case 'diff':
                    $current = $service->getCurrent();
                    $historyFile = (string)($_GET['history_file'] ?? '');
                    $diff = $service->diffHistoryFileAgainstCurrent($historyFile, (string)$current['content']);
                    $this->json([
                        'status' => 'ok',
                        'history_file' => $historyFile,
                        'added' => $diff['added'],
                        'removed' => $diff['removed'],
                        'diff' => $diff['diff'],
                    ]);
                    break;
                default:
                    $this->json(['status' => 'error', 'message' => $this->msg('_API_SETTINGS_GET_ACTION_UNKNOWN')], 400);
            }
        } catch (\Throwable $e) {
            $this->internalError('handleSettingsGet', $e);
        }
    }

    /**
     * Kurzbeschreibung Funktion handleSettingsPost
     *
     * @return void
     */
private function handleSettingsPost(): void
    {
        $service = new ServerSettingsService();
        $action = (string)($_POST['action'] ?? '');
        if ($action !== 'save') {
            $this->json(['status' => 'error', 'message' => $this->msg('_API_SETTINGS_POST_ACTION_UNKNOWN')], 400);
        }

        $content = (string)($_POST['content'] ?? '');
        $missing = [];
        foreach (['mode server', 'proto', 'port', 'ca ', 'cert ', 'key ', 'server '] as $needle) {
            if (!str_contains($content, $needle)) {
                $missing[] = $needle;
            }
        }
        if ($missing !== []) {
            $this->json([
                'status' => 'error',
                'message' => $this->msgf('_API_REQUIRED_PARTS_MISSING', implode(', ', $missing)),
            ], 422);
        }

        try {
            $saved = $service->save($content);
            $this->json([
                'status' => 'ok',
                'message' => $this->msg('_API_SETTINGS_SAVED'),
                'path' => $saved['path'],
            ]);
        } catch (\Throwable $e) {
            $this->internalError('handleSettingsPost', $e);
        }
    }

    /**
     * Kurzbeschreibung Funktion requireAdminJson
     *
     * @return void
     */
private function requireAdminJson(): void
    {
        if (!Session::isAdmin()) {
            $this->json(['status' => 'error', 'message' => $this->msg('_API_ADMIN_ONLY')], 403);
        }
    }

    /**
     * Kurzbeschreibung Funktion msg
     *
     * @param mixed $key
     * @return string
     */
private function msg(string $key): string
    {
        return Lang::get($key);
    }

    /**
     * Kurzbeschreibung Funktion msgf
     *
     * @param mixed $key
     * @param mixed $arg
     * @return string
     */
private function msgf(string $key, string $arg): string
    {
        return sprintf(Lang::get($key), $arg);
    }

    /**
     * Kurzbeschreibung Funktion assertValidUsername
     *
     * @param mixed $username
     * @return void
     */
private function assertValidUsername(string $username): void
    {
        if ($username === '' || !preg_match('/^[a-zA-Z0-9_.@-]{1,64}$/', $username)) {
            $this->json(['status' => 'error', 'message' => $this->msg('_API_USERNAME_INVALID')], 422);
        }
    }

    /**
     * Kurzbeschreibung Funktion getLoginDiagnostics
     *
     * @return array
     */
private function getLoginDiagnostics(): array
    {
        $out = [
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'session_user' => Session::getUser(),
        ];

        try {
            $db = Database::getInstance()->getConnection();
            $out['db_connection'] = 'ok';

            $tables = ['user', 'groupnames', 'sessions2'];
            $out['tables'] = [];
            foreach ($tables as $table) {
                try {
                    $stmt = $db->query("SELECT COUNT(*) AS c FROM {$table}");
                    $cnt = (int)($stmt->fetch()['c'] ?? 0);
                    $out['tables'][$table] = ['ok' => true, 'rows' => $cnt];
                } catch (\Throwable $e) {
                    $out['tables'][$table] = ['ok' => false, 'error' => 'query_failed'];
                }
            }

            try {
                $stmt = $db->query("SELECT uid, user_name, gid, user_pass FROM user ORDER BY uid ASC LIMIT 50");
                $rows = $stmt->fetchAll();
                $out['users_preview'] = array_map(static function (array $u): array {
                    $hash = (string)($u['user_pass'] ?? '');
                    $format = 'legacy_or_plain';
                    if (str_starts_with($hash, '$2y$') || str_starts_with($hash, '$argon2')) {
                        $format = 'password_hash';
                    } elseif (preg_match('/^[a-f0-9]{32}$/i', $hash)) {
                        $format = 'md5';
                    } elseif (preg_match('/^[a-f0-9]{40}$/i', $hash)) {
                        $format = 'sha1';
                    }

                    return [
                        'uid' => (int)($u['uid'] ?? 0),
                        'user_name' => (string)($u['user_name'] ?? ''),
                        'gid' => (int)($u['gid'] ?? 0),
                        'hash_format' => $format,
                    ];
                }, $rows);
            } catch (\Throwable $e) {
                $out['users_preview_error'] = 'query_failed';
            }

            $username = trim((string)($_GET['username'] ?? ''));
            $password = (string)($_GET['password'] ?? '');
            if ($username !== '') {
                $stmt = $db->prepare('SELECT uid, user_name, gid, user_pass FROM user WHERE user_name = :u LIMIT 1');
                $stmt->execute(['u' => $username]);
                $user = $stmt->fetch();

                if (!$user) {
                    $out['login_check'] = [
                        'username' => $username,
                        'user_found' => false,
                    ];
                } else {
                    $storedHash = (string)($user['user_pass'] ?? '');
                    $isPasswordHash = str_starts_with($storedHash, '$2y$') || str_starts_with($storedHash, '$argon2');
                    $matches = null;
                    $match_type = null;

                    if ($password !== '') {
                        if (password_verify($password, $storedHash)) {
                            $matches = true;
                            $match_type = 'password_hash';
                        } elseif (hash_equals($storedHash, $password)) {
                            $matches = true;
                            $match_type = 'plaintext';
                        } elseif (hash_equals($storedHash, md5($password))) {
                            $matches = true;
                            $match_type = 'md5';
                        } elseif (hash_equals($storedHash, sha1($password))) {
                            $matches = true;
                            $match_type = 'sha1';
                        } else {
                            $matches = false;
                            $match_type = 'no_match';
                        }
                    }

                    $out['login_check'] = [
                        'username' => $username,
                        'user_found' => true,
                        'uid' => (int)$user['uid'],
                        'gid' => (int)$user['gid'],
                        'hash_format' => $isPasswordHash ? 'password_hash' : 'legacy_or_plain',
                        'password_matches' => $matches,
                        'password_match_type' => $match_type,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Debug::log('diag_login failed', $e->getMessage());
            $out['status'] = 'error';
            $out['db_connection'] = 'failed';
            $out['message'] = 'internal_error';
        }

        return $out;
    }

    /**
     * Kurzbeschreibung Funktion json
     *
     * @param mixed $data
     * @param mixed $status
     * @return void
     */
    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    private function internalError(string $context, \Throwable $e): void
    {
        Debug::log($context, $e->getMessage());
        $this->json([
            'status' => 'error',
            'message' => $this->msg('_MSG_GENERIC_ERROR'),
        ], 500);
    }
}
