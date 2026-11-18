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

namespace Micro\OpenvpnWebadmin\Core;

use Micro\OpenvpnWebadmin\Util\Lang;

class DataController
{
/**
 * Verarbeitet die Anfrage entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
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
 * Verteilt GET-Datenquellen, prüft je Quelle die Adminrolle und serialisiert Ergebnisse als JSON.
 *
 * @param string $select Angeforderte Datenquelle aus `select`.
 * @return void Kein Rueckgabewert.
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
            case 'profile_users':
                $this->requireAdminJson();
                $limit = max(1, (int)($_GET['limit'] ?? 10));
                $offset = max(0, (int)($_GET['offset'] ?? 0));
                $search = trim((string)($_GET['search'] ?? ''));
                $this->json((new UserModel())->getVpnProfileUsers($limit, $offset, $search));
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
 * Verteilt schreibende API-Aktionen; Verwaltungsquellen verlangen Adminrechte.
 *
 * @param string $select Angeforderte Aktionsquelle aus `select`.
 * @return void Kein Rueckgabewert.
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
 * Liest users und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
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
 * Liest logs und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
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
 * Liest dashboard stats und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
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
 * Verarbeitet user action entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
private function handleUserAction(): void
    {
        $action = (string)($_POST['action'] ?? '');

        try {
            // Construct the model inside the exception boundary as database
            // connection failures must use the JSON API error response too.
            $model = new UserModel();
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

                case 'set_network':
                    $username = trim((string)($_POST['username'] ?? ''));
                    $enabled = ((string)($_POST['enabled'] ?? '0')) === '1';
                    $fixedIp = trim((string)($_POST['fixed_ip'] ?? ''));
                    $this->assertValidUsername($username);

                    if ($username === '') {
                        $this->json(['status' => 'error', 'message' => $this->msg('_API_USERNAME_MISSING')], 422);
                    }

                    if ($fixedIp !== '') {
                        $enabled = true;
                    }

                    if ($enabled) {
                        if (!$this->isValidIpv4($fixedIp)) {
                            $this->json(['status' => 'error', 'message' => $this->msg('_API_FIXED_IP_INVALID')], 422);
                        }
                        if ($model->isFixedIpInUseByOtherUser($username, $fixedIp)) {
                            $this->json(['status' => 'error', 'message' => $this->msg('_API_FIXED_IP_IN_USE')], 409);
                        }
                    } else {
                        $fixedIp = '';
                    }

                    $model->setUserFixedIp($username, $fixedIp !== '' ? $fixedIp : null);
                    $this->json(['status' => 'ok', 'message' => $this->msg('_API_NETWORK_UPDATED')]);
                    break;

                default:
                    $this->json(['status' => 'error', 'message' => $this->msg('_API_USER_ACTION_UNKNOWN')], 400);
            }
        } catch (\Throwable $e) {
            $this->internalError('handleUserAction', $e);
        }
    }

/**
 * Verarbeitet account action entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
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
 * Verarbeitet config get entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
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
 * Verarbeitet config post entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
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
        foreach (['client'] as $needle) {
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

            $this->json([
                'status' => 'ok',
                'message' => $this->msg('_API_CONFIG_SAVED'),
                'system' => $system,
                'history_file' => $result['history_file'],
                'history_saved' => $result['history_saved'],
            ]);
        } catch (\Throwable $e) {
            Debug::logException($e, 'handleConfigPost');
            Debug::log('handleConfigPost', $e->getMessage(), [
                'system' => $system,
            ]);
            $this->json([
                'status' => 'error',
                'message' => $e->getMessage() !== '' ? $e->getMessage() : $this->msg('_MSG_GENERIC_ERROR'),
            ], 500);
        }
    }

/**
 * Verarbeitet settings get entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
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
 * Verarbeitet settings post entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
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
 * Fuehrt require admin json entsprechend der internen Logik aus.
 *
 * @return void Kein Rueckgabewert.
 */
private function requireAdminJson(): void
    {
        if (!Session::isAdmin()) {
            $this->json(['status' => 'error', 'message' => $this->msg('_API_ADMIN_ONLY')], 403);
        }
    }

/**
 * Übersetzt einen Sprachschlüssel über den aktuellen Sprachkatalog.
 *
 * @param string $key Schlüssel des Sprachkatalogs.
 * @return string Übersetzter Text oder das Verhalten von `Lang::get()` bei unbekanntem Schlüssel.
 */
private function msg(string $key): string
    {
        return Lang::get($key);
    }

/**
 * Übersetzt einen Sprachschlüssel und setzt genau ein `sprintf`-Argument ein.
 *
 * @param string $key Formatstring-Schlüssel.
 * @param string $arg Einzusetzender Formatwert.
 * @return string Formatierter Übersetzungstext.
 */
private function msgf(string $key, string $arg): string
    {
        return sprintf(Lang::get($key), $arg);
    }

    /**
     * Akzeptiert nichtleere Benutzernamen aus ASCII-Buchstaben, Ziffern, `_ . @ -` bis Länge 64.
     *
     * @param string $username Zu validierender Benutzername.
     * @return void Kein Rueckgabewert.
     * Ungültige Eingabe beendet die Anfrage mit API-Status 422.
     */
    private function assertValidUsername(string $username): void
    {
        if ($username === '' || !preg_match('/^[a-zA-Z0-9_.@-]{1,64}$/', $username)) {
            $this->json(['status' => 'error', 'message' => $this->msg('_API_USERNAME_INVALID')], 422);
        }
    }

/**
 * Prüft, ob der vollständige Wert eine gültige IPv4-Adresse ist.
 *
 * @param string $value Zu prüfender Text.
 * @return bool True bei Erfolg, sonst false.
 */
private function isValidIpv4(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

/**
 * Erstellt DB-/Tabellen- und Benutzerdiagnosen einschließlich Passwortformatprüfung; Passwörter selbst werden nicht zurückgegeben.
 *
 * @return array Struktur mit Status, Zeitstempel, Sessionbenutzer, Tabellenstatus, Benutzer-Hashformaten und optionalem `login_check`.
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
     * Setzt Status und JSON-Header, kodiert Daten, schreibt sie und beendet den Prozess.
     *
     * @param array<string, mixed> $data Zu serialisierende Antwortdaten.
     * @param int $status HTTP-Statuscode.
     * @return void Kein Rueckgabewert.
     */
    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

/**
 * Protokolliert Exception und Kontext und antwortet mit generischem HTTP-500-JSON.
 *
 * @param string $context Fehlerkontext für das Protokoll.
 * @param \Throwable $e Auslösender Fehler.
 * @return void Kein Rueckgabewert.
 */
private function internalError(string $context, \Throwable $e): void
    {
        Debug::logException($e, $context);
        Debug::log($context, $e->getMessage());
        $this->json([
            'status' => 'error',
            'message' => $this->msg('_MSG_GENERIC_ERROR'),
        ], 500);
    }
}
