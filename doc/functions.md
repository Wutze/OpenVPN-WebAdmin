# Funktionsreferenz

Automatisch generiert am: 2026-02-21 13:41:34

## `./src/Core/ConfigService.php`

### `public __construct($basePath)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 16
- Rueckgabe: `mixed|null`

```
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $basePath
     * @return mixed|null
     */
```

### `public listSystems()`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 27
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion listSystems
     *
     * @return array
     */
```

### `public getConfig($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 57
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion getConfig
     *
     * @param mixed $system
     * @return string
     */
```

### `public saveConfig($system, $content)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 74
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion saveConfig
     *
     * @param mixed $system
     * @param mixed $content
     * @return array
     */
```

### `public getHistoryList($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 113
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion getHistoryList
     *
     * @param mixed $system
     * @return array
     */
```

### `public getDiffStatsAgainstCurrent($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 144
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion getDiffStatsAgainstCurrent
     *
     * @param mixed $system
     * @return array
     */
```

### `public diffHistoryFile($system, $historyFile)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 166
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion diffHistoryFile
     *
     * @param mixed $system
     * @param mixed $historyFile
     * @return array
     */
```

### `private getHistoryContent($system, $historyFile)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 181
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion getHistoryContent
     *
     * @param mixed $system
     * @param mixed $historyFile
     * @return string
     */
```

### `private buildDiffData($oldText, $newText)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 199
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion buildDiffData
     *
     * @param mixed $oldText
     * @param mixed $newText
     * @return array
     */
```

### `private diffOps($a, $b)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 235
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion diffOps
     *
     * @param mixed $a
     * @param mixed $b
     * @return array
     */
```

### `private clientPath($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 288
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion clientPath
     *
     * @param mixed $system
     * @return string
     */
```

### `private historyDir($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 300
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion historyDir
     *
     * @param mixed $system
     * @return string
     */
```

### `private sanitizeSystem($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 312
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion sanitizeSystem
     *
     * @param mixed $system
     * @return string
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/DataController.php`

### `public handle()`

- Datei: `./src/Core/DataController.php`
- Zeile: 28
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion handle
     *
     * @return void
     */
```

### `private handleGet($select)`

- Datei: `./src/Core/DataController.php`
- Zeile: 54
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion handleGet
     *
     * @param mixed $select
     * @return void
     */
```

### `private handlePost($select)`

- Datei: `./src/Core/DataController.php`
- Zeile: 98
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion handlePost
     *
     * @param mixed $select
     * @return void
     */
```

### `private getUsers()`

- Datei: `./src/Core/DataController.php`
- Zeile: 129
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion getUsers
     *
     * @return array
     */
```

### `private getLogs()`

- Datei: `./src/Core/DataController.php`
- Zeile: 148
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion getLogs
     *
     * @return array
     */
```

### `private getDashboardStats()`

- Datei: `./src/Core/DataController.php`
- Zeile: 166
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion getDashboardStats
     *
     * @return array
     */
```

### `private handleUserAction()`

- Datei: `./src/Core/DataController.php`
- Zeile: 202
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion handleUserAction
     *
     * @return void
     */
```

### `private handleAccountAction()`

- Datei: `./src/Core/DataController.php`
- Zeile: 305
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion handleAccountAction
     *
     * @return void
     */
```

### `private handleProfileAction()`

- Datei: `./src/Core/DataController.php`
- Zeile: 343
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion handleProfileAction
     *
     * @return void
     */
```

### `private handleConfigGet()`

- Datei: `./src/Core/DataController.php`
- Zeile: 370
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion handleConfigGet
     *
     * @return void
     */
```

### `private handleConfigPost()`

- Datei: `./src/Core/DataController.php`
- Zeile: 418
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion handleConfigPost
     *
     * @return void
     */
```

### `private handleSettingsGet()`

- Datei: `./src/Core/DataController.php`
- Zeile: 469
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion handleSettingsGet
     *
     * @return void
     */
```

### `private handleSettingsPost()`

- Datei: `./src/Core/DataController.php`
- Zeile: 511
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion handleSettingsPost
     *
     * @return void
     */
```

### `private requireAdminJson()`

- Datei: `./src/Core/DataController.php`
- Zeile: 550
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion requireAdminJson
     *
     * @return void
     */
```

### `private msg($key)`

- Datei: `./src/Core/DataController.php`
- Zeile: 563
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion msg
     *
     * @param mixed $key
     * @return string
     */
```

### `private msgf($key, $arg)`

- Datei: `./src/Core/DataController.php`
- Zeile: 575
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion msgf
     *
     * @param mixed $key
     * @param mixed $arg
     * @return string
     */
```

### `private assertValidUsername($username)`

- Datei: `./src/Core/DataController.php`
- Zeile: 586
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion assertValidUsername
     *
     * @param mixed $username
     * @return void
     */
```

### `private getLoginDiagnostics()`

- Datei: `./src/Core/DataController.php`
- Zeile: 598
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion getLoginDiagnostics
     *
     * @return array
     */
```

### `private json($data, $status)`

- Datei: `./src/Core/DataController.php`
- Zeile: 711
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion json
     *
     * @param mixed $data
     * @param mixed $status
     * @return void
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/Database.php`

### `private __construct($config)`

- Datei: `./src/Core/Database.php`
- Zeile: 35
- Rueckgabe: `mixed|null`

```
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $config
     * @return mixed|null
     */
```

### `public static getInstance($config)`

- Datei: `./src/Core/Database.php`
- Zeile: 54
- Rueckgabe: `Database`

```
/**
     * Kurzbeschreibung Funktion getInstance
     *
     * @param mixed $config
     * @return Database
     */
```

### `public getConnection()`

- Datei: `./src/Core/Database.php`
- Zeile: 70
- Rueckgabe: `PDO`

```
/**
     * Kurzbeschreibung Funktion getConnection
     *
     * @return PDO
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/Debug.php`

### `public static init($env, $logfile, $debug)`

- Datei: `./src/Core/Debug.php`
- Zeile: 32
- Rueckgabe: `void`

```
/**
     * Initialisiert Debug-Umgebung
     */
```

### `public static debug($vars)`

- Datei: `./src/Core/Debug.php`
- Zeile: 45
- Rueckgabe: `void`

```
/**
     * Debug-Ausgabe auf der Seite (Bootstrap Collapse)
     */
```

### `public static log($vars)`

- Datei: `./src/Core/Debug.php`
- Zeile: 101
- Rueckgabe: `void`

```
/**
     * Debug-Ausgabe in Logfile
     */
```

### `public static dd($vars)`

- Datei: `./src/Core/Debug.php`
- Zeile: 121
- Rueckgabe: `void`

```
/**
     * Debug + exit
     */
```

### `public static handleException($e)`

- Datei: `./src/Core/Debug.php`
- Zeile: 130
- Rueckgabe: `void`

```
/**
     * Exception-Handler
     */
```

### `public static handleError($errno, $errstr, $errfile, $errline)`

- Datei: `./src/Core/Debug.php`
- Zeile: 156
- Rueckgabe: `bool`

```
/**
     * Error-Handler
     */
```

### `public static render()`

- Datei: `./src/Core/Debug.php`
- Zeile: 172
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion render
     *
     * @return void
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/GoRequest.php`

### `public set_value($key, $val)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 42
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion set_value
     *
     * @param mixed $key
     * @param mixed $val
     * @return void
     */
```

### `public main()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 52
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion main
     *
     * @return void
     */
```

### `private showLogin()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 130
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion showLogin
     *
     * @return void
     */
```

### `private checkLogin()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 140
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion checkLogin
     *
     * @return void
     */
```

### `private baseTemplateData($activeOp)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 151
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion baseTemplateData
     *
     * @param mixed $activeOp
     * @return array
     */
```

### `private renderPage($title, $content, $activeOp)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 169
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion renderPage
     *
     * @param mixed $title
     * @param mixed $content
     * @param mixed $activeOp
     * @return void
     */
```

### `private showMain()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 180
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion showMain
     *
     * @return void
     */
```

### `private showUsers()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 191
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion showUsers
     *
     * @return void
     */
```

### `private showLogs()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 202
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion showLogs
     *
     * @return void
     */
```

### `private showConfig()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 213
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion showConfig
     *
     * @return void
     */
```

### `private showSettings()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 224
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion showSettings
     *
     * @return void
     */
```

### `private showProfiles()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 235
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion showProfiles
     *
     * @return void
     */
```

### `private showAccount()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 246
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion showAccount
     *
     * @return void
     */
```

### `private showLive()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 257
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion showLive
     *
     * @return void
     */
```

### `private logout()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 268
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion logout
     *
     * @return void
     */
```

### `private ensureAdminOrForbidden()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 295
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion ensureAdminOrForbidden
     *
     * @return void
     */
```

### `private downloadProfileZip()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 309
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion downloadProfileZip
     *
     * @return void
     */
```

### `private showError()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 335
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion showError
     *
     * @return void
     */
```

### `private setLanguage()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 351
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion setLanguage
     *
     * @return void
     */
```

### `private enforceAccessPolicy()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 372
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion enforceAccessPolicy
     *
     * @return void
     */
```

### `private serveLoginAsset()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 430
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion serveLoginAsset
     *
     * @return void
     */
```

### `private enforceDataAccessPolicy($method)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 490
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion enforceDataAccessPolicy
     *
     * @param mixed $method
     * @return void
     */
```

### `private verifyStateChangingRequest()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 508
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion verifyStateChangingRequest
     *
     * @return void
     */
```

### `private isSameOriginRequest()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 525
- Rueckgabe: `bool`

```
/**
     * Kurzbeschreibung Funktion isSameOriginRequest
     *
     * @return bool
     */
```

### `private denyRequest($status, $message)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 554
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion denyRequest
     *
     * @param mixed $status
     * @param mixed $message
     * @return void
     */
```

### `private getRequestCsrfToken()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 574
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion getRequestCsrfToken
     *
     * @return string
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/Lang.php`

### `public static init($lang)`

- Datei: `./src/Core/Lang.php`
- Zeile: 29
- Rueckgabe: `void`

```
/**
     * Initialisiert die Sprache (Session muss vorher gestartet sein)
     */
```

### `public static get($key, $default)`

- Datei: `./src/Core/Lang.php`
- Zeile: 55
- Rueckgabe: `string`

```
/**
     * Einzeltext holen
     */
```

### `public static getAll()`

- Datei: `./src/Core/Lang.php`
- Zeile: 63
- Rueckgabe: `array`

```
/**
     * Komplettes Message-Array (für Templates / Backwards-Compatibility)
     */
```

### `public static getCurrent()`

- Datei: `./src/Core/Lang.php`
- Zeile: 71
- Rueckgabe: `string`

```
/**
     * Aktuelle Sprache
     */
```

### `public static setCurrent($lang)`

- Datei: `./src/Core/Lang.php`
- Zeile: 79
- Rueckgabe: `void`

```
/**
     * Sprache setzen und neu laden
     */
```

### `public static getAvailableLanguages()`

- Datei: `./src/Core/Lang.php`
- Zeile: 92
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion getAvailableLanguages
     *
     * @return array
     */
```

### `public static getLanguageMeta($code)`

- Datei: `./src/Core/Lang.php`
- Zeile: 122
- Rueckgabe: `array`

```
/**
     * Liefert Label + Flagge für Sprach-Auswahl.
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/LogModel.php`

### `public __construct()`

- Datei: `./src/Core/LogModel.php`
- Zeile: 33
- Rueckgabe: `mixed|null`

```
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @return mixed|null
     */
```

### `public getAllLogs($limit, $offset, $search)`

- Datei: `./src/Core/LogModel.php`
- Zeile: 46
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion getAllLogs
     *
     * @param mixed $limit
     * @param mixed $offset
     * @param mixed $search
     * @return array
     */
```

### `public countLogs($search)`

- Datei: `./src/Core/LogModel.php`
- Zeile: 76
- Rueckgabe: `int`

```
/**
     * Kurzbeschreibung Funktion countLogs
     *
     * @param mixed $search
     * @return int
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/LoginController.php`

### `public showLogin()`

- Datei: `./src/Core/LoginController.php`
- Zeile: 34
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion showLogin
     *
     * @return void
     */
```

### `public handleLogin()`

- Datei: `./src/Core/LoginController.php`
- Zeile: 52
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion handleLogin
     *
     * @return void
     */
```

### `private redirect($url)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 145
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion redirect
     *
     * @param mixed $url
     * @return void
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/ProfileService.php`

### `public __construct($basePath)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 15
- Rueckgabe: `mixed|null`

```
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $basePath
     * @return mixed|null
     */
```

### `public listSystems()`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 25
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion listSystems
     *
     * @return array
     */
```

### `public buildZip($system)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 76
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion buildZip
     *
     * @param mixed $system
     * @return string
     */
```

### `public getZipPath($system)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 118
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion getZipPath
     *
     * @param mixed $system
     * @return string
     */
```

### `private sanitizeSystem($system)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 136
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion sanitizeSystem
     *
     * @param mixed $system
     * @return string
     */
```

### `private zipPathFor($system)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 152
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion zipPathFor
     *
     * @param mixed $system
     * @return string
     */
```

### `private isAllowedSystemDir($system, $dir)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 164
- Rueckgabe: `bool`

```
/**
     * Kurzbeschreibung Funktion isAllowedSystemDir
     *
     * @param mixed $system
     * @param mixed $dir
     * @return bool
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/ServerSettingsService.php`

### `public __construct($cfg)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 15
- Rueckgabe: `mixed|null`

```
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $cfg
     * @return mixed|null
     */
```

### `public getCurrent()`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 35
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion getCurrent
     *
     * @return array
     */
```

### `public save($content)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 74
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion save
     *
     * @param mixed $content
     * @return array
     */
```

### `public getHistoryList()`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 104
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion getHistoryList
     *
     * @return array
     */
```

### `public diffHistoryFileAgainstCurrent($historyFile, $current)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 135
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion diffHistoryFileAgainstCurrent
     *
     * @param mixed $historyFile
     * @param mixed $current
     * @return array
     */
```

### `private getDiffStatsAgainstCurrent($current)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 153
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion getDiffStatsAgainstCurrent
     *
     * @param mixed $current
     * @return array
     */
```

### `private fetchFromUrl($url)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 174
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion fetchFromUrl
     *
     * @param mixed $url
     * @return string
     */
```

### `private writeCache($content)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 207
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion writeCache
     *
     * @param mixed $content
     * @return void
     */
```

### `private archiveFile($file)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 228
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion archiveFile
     *
     * @param mixed $file
     * @return void
     */
```

### `private readSavePath()`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 246
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion readSavePath
     *
     * @return string
     */
```

### `private readFile($path)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 269
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion readFile
     *
     * @param mixed $path
     * @return string
     */
```

### `private ensureSaveFileExists()`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 283
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion ensureSaveFileExists
     *
     * @return void
     */
```

### `private buildDiffData($oldText, $newText)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 308
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion buildDiffData
     *
     * @param mixed $oldText
     * @param mixed $newText
     * @return array
     */
```

### `private diffOps($a, $b)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 344
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion diffOps
     *
     * @param mixed $a
     * @param mixed $b
     * @return array
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/Session.php`

### `public __construct($db, $lifetime)`

- Datei: `./src/Core/Session.php`
- Zeile: 48
- Rueckgabe: `mixed|null`

```
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $db
     * @param mixed $lifetime
     * @return mixed|null
     */
```

### `public open($savePath, $sessionName)`

- Datei: `./src/Core/Session.php`
- Zeile: 77
- Rueckgabe: `bool`

```
/**
     * Kurzbeschreibung Funktion open
     *
     * @param mixed $savePath
     * @param mixed $sessionName
     * @return bool
     */
```

### `public close()`

- Datei: `./src/Core/Session.php`
- Zeile: 87
- Rueckgabe: `bool`

```
/**
     * Kurzbeschreibung Funktion close
     *
     * @return bool
     */
```

### `public read($id)`

- Datei: `./src/Core/Session.php`
- Zeile: 98
- Rueckgabe: `string|false`

```
/**
     * Kurzbeschreibung Funktion read
     *
     * @param mixed $id
     * @return string|false
     */
```

### `public write($id, $data)`

- Datei: `./src/Core/Session.php`
- Zeile: 126
- Rueckgabe: `bool`

```
/**
     * Kurzbeschreibung Funktion write
     *
     * @param mixed $id
     * @param mixed $data
     * @return bool
     */
```

### `public destroy($id)`

- Datei: `./src/Core/Session.php`
- Zeile: 158
- Rueckgabe: `bool`

```
/**
     * Kurzbeschreibung Funktion destroy
     *
     * @param mixed $id
     * @return bool
     */
```

### `public gc($max_lifetime)`

- Datei: `./src/Core/Session.php`
- Zeile: 178
- Rueckgabe: `int|false`

```
/**
     * Kurzbeschreibung Funktion gc
     *
     * @param mixed $max_lifetime
     * @return int|false
     */
```

### `public static start($db, $lifetime)`

- Datei: `./src/Core/Session.php`
- Zeile: 198
- Rueckgabe: `void`

```
/**
     * Initialisiert und startet die Session.
     */
```

### `public static setVar($key, $value)`

- Datei: `./src/Core/Session.php`
- Zeile: 227
- Rueckgabe: `void`

```
/**
     * Setzt eine Session-Variable.
     */
```

### `public static getVar($key, $default)`

- Datei: `./src/Core/Session.php`
- Zeile: 235
- Rueckgabe: `mixed`

```
/**
     * Holt eine Session-Variable.
     */
```

### `public static removeVar($key)`

- Datei: `./src/Core/Session.php`
- Zeile: 243
- Rueckgabe: `void`

```
/**
     * Entfernt eine Session-Variable.
     */
```

### `public static isUser()`

- Datei: `./src/Core/Session.php`
- Zeile: 251
- Rueckgabe: `bool`

```
/**
     * Prüft, ob ein Benutzer angemeldet ist.
     */
```

### `public static isAdmin()`

- Datei: `./src/Core/Session.php`
- Zeile: 259
- Rueckgabe: `bool`

```
/**
     * Prüft, ob aktueller Benutzer Admin ist.
     */
```

### `public static getUser()`

- Datei: `./src/Core/Session.php`
- Zeile: 281
- Rueckgabe: `?array`

```
/**
     * Gibt den aktuellen Benutzer zurück.
     */
```

### `public static setUser($user)`

- Datei: `./src/Core/Session.php`
- Zeile: 289
- Rueckgabe: `void`

```
/**
     * Setzt den angemeldeten Benutzer.
     */
```

### `public static destroyAll()`

- Datei: `./src/Core/Session.php`
- Zeile: 297
- Rueckgabe: `void`

```
/**
     * Zerstört die komplette Session inkl. Cookie.
     */
```

### `public static getCsrfToken()`

- Datei: `./src/Core/Session.php`
- Zeile: 317
- Rueckgabe: `string`

```
/**
     * CSRF token for state-changing requests.
     */
```

### `public static verifyCsrfToken($token)`

- Datei: `./src/Core/Session.php`
- Zeile: 332
- Rueckgabe: `bool`

```
/**
     * Kurzbeschreibung Funktion verifyCsrfToken
     *
     * @param mixed $token
     * @return bool
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/UserController.php`

### `public index()`

- Datei: `./src/Core/UserController.php`
- Zeile: 34
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion index
     *
     * @return void
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/UserModel.php`

### `public __construct()`

- Datei: `./src/Core/UserModel.php`
- Zeile: 32
- Rueckgabe: `mixed|null`

```
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @return mixed|null
     */
```

### `public getAllUsers($limit, $offset, $search)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 45
- Rueckgabe: `array`

```
/**
     * Kurzbeschreibung Funktion getAllUsers
     *
     * @param mixed $limit
     * @param mixed $offset
     * @param mixed $search
     * @return array
     */
```

### `public countUsers($search)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 87
- Rueckgabe: `int`

```
/**
     * Kurzbeschreibung Funktion countUsers
     *
     * @param mixed $search
     * @return int
     */
```

### `public countOnlineUsers()`

- Datei: `./src/Core/UserModel.php`
- Zeile: 111
- Rueckgabe: `int`

```
/**
     * Kurzbeschreibung Funktion countOnlineUsers
     *
     * @return int
     */
```

### `public userExists($username)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 123
- Rueckgabe: `bool`

```
/**
     * Kurzbeschreibung Funktion userExists
     *
     * @param mixed $username
     * @return bool
     */
```

### `public createUser($username, $password, $isAdmin)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 138
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion createUser
     *
     * @param mixed $username
     * @param mixed $password
     * @param mixed $isAdmin
     * @return void
     */
```

### `public setUserEnabled($username, $enabled)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 162
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion setUserEnabled
     *
     * @param mixed $username
     * @param mixed $enabled
     * @return void
     */
```

### `public setUserRole($username, $isAdmin)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 178
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion setUserRole
     *
     * @param mixed $username
     * @param mixed $isAdmin
     * @return void
     */
```

### `public setUserPasswordByName($username, $newPassword)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 195
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion setUserPasswordByName
     *
     * @param mixed $username
     * @param mixed $newPassword
     * @return void
     */
```

### `public setUserPasswordById($uid, $newPassword)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 212
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion setUserPasswordById
     *
     * @param mixed $uid
     * @param mixed $newPassword
     * @return void
     */
```

### `public setUserLimits($username, $startDate, $endDate)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 230
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion setUserLimits
     *
     * @param mixed $username
     * @param mixed $startDate
     * @param mixed $endDate
     * @return void
     */
```

### `public deleteUser($username)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 253
- Rueckgabe: `void`

```
/**
     * Kurzbeschreibung Funktion deleteUser
     *
     * @param mixed $username
     * @return void
     */
```

### `public verifyPassword($uid, $password)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 266
- Rueckgabe: `bool`

```
/**
     * Kurzbeschreibung Funktion verifyPassword
     *
     * @param mixed $uid
     * @param mixed $password
     * @return bool
     */
```

### `private resolveGroupId($isAdmin)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 284
- Rueckgabe: `int`

```
/**
     * Kurzbeschreibung Funktion resolveGroupId
     *
     * @param mixed $isAdmin
     * @return int
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Templates/Account.php`

### `public index()`

- Datei: `./src/Templates/Account.php`
- Zeile: 12
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Templates/Config.php`

### `public index()`

- Datei: `./src/Templates/Config.php`
- Zeile: 12
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Templates/Dashboard.php`

### `public index()`

- Datei: `./src/Templates/Dashboard.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Templates/Log.php`

### `public index()`

- Datei: `./src/Templates/Log.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Templates/Profiles.php`

### `public index()`

- Datei: `./src/Templates/Profiles.php`
- Zeile: 12
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Templates/Settings.php`

### `public index()`

- Datei: `./src/Templates/Settings.php`
- Zeile: 12
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Templates/Template.php`

### `public __construct($title, $content, $data)`

- Datei: `./src/Templates/Template.php`
- Zeile: 37
- Rueckgabe: `mixed|null`

```
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $title
     * @param mixed $content
     * @param mixed $data
     * @return mixed|null
     */
```

### `private loadPart($name, $vars)`

- Datei: `./src/Templates/Template.php`
- Zeile: 47
- Rueckgabe: `string`

```
/**
     * Lädt eine Layout-Datei aus /src/Layout/
     */
```

### `public render()`

- Datei: `./src/Templates/Template.php`
- Zeile: 65
- Rueckgabe: `string`

```
/**
     * Baut die komplette Seite zusammen
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Templates/User.php`

### `public index()`

- Datei: `./src/Templates/User.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

