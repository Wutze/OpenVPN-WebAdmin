# Funktionsreferenz

Automatisch generiert am: 2026-02-21 18:47:55

## `./src/Core/ConfigService.php`

### `public __construct($basePath)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 16
- Rueckgabe: `mixed|null`

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
/**
     * Kurzbeschreibung Funktion sanitizeSystem
     *
     * @param mixed $system
     * @return string
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/Database.php`

### `private __construct($config)`

- Datei: `./src/Core/Database.php`
- Zeile: 35
- Rueckgabe: `mixed|null`

```code
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $config
     * @return mixed|null
     */
```

### `public static getInstance($config)`

- Datei: `./src/Core/Database.php`
- Zeile: 57
- Rueckgabe: `Database`

```code
/**
     * Kurzbeschreibung Funktion getInstance
     *
     * @param mixed $config
     * @return Database
     */
```

### `public getConnection()`

- Datei: `./src/Core/Database.php`
- Zeile: 73
- Rueckgabe: `PDO`

```code
/**
     * Kurzbeschreibung Funktion getConnection
     *
     * @return PDO
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/DataController.php`

### `public handle()`

- Datei: `./src/Core/DataController.php`
- Zeile: 28
- Rueckgabe: `void`

```code
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

```code
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

```code
/**
     * Kurzbeschreibung Funktion handlePost
     *
     * @param mixed $select
     * @return void
     */
```

### `private getUsers()`

- Datei: `./src/Core/DataController.php`
- Zeile: 130
- Rueckgabe: `array`

```code
/**
     * Kurzbeschreibung Funktion getUsers
     *
     * @return array
     */
```

### `private getLogs()`

- Datei: `./src/Core/DataController.php`
- Zeile: 149
- Rueckgabe: `array`

```code
/**
     * Kurzbeschreibung Funktion getLogs
     *
     * @return array
     */
```

### `private getDashboardStats()`

- Datei: `./src/Core/DataController.php`
- Zeile: 167
- Rueckgabe: `array`

```code
/**
     * Kurzbeschreibung Funktion getDashboardStats
     *
     * @return array
     */
```

### `private handleUserAction()`

- Datei: `./src/Core/DataController.php`
- Zeile: 203
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion handleUserAction
     *
     * @return void
     */
```

### `private handleAccountAction()`

- Datei: `./src/Core/DataController.php`
- Zeile: 335
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion handleAccountAction
     *
     * @return void
     */
```

### `private handleProfileAction()`

- Datei: `./src/Core/DataController.php`
- Zeile: 377
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion handleProfileAction
     *
     * @return void
     */
```

### `private handleConfigGet()`

- Datei: `./src/Core/DataController.php`
- Zeile: 404
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion handleConfigGet
     *
     * @return void
     */
```

### `private handleConfigPost()`

- Datei: `./src/Core/DataController.php`
- Zeile: 452
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion handleConfigPost
     *
     * @return void
     */
```

### `private handleSettingsGet()`

- Datei: `./src/Core/DataController.php`
- Zeile: 503
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion handleSettingsGet
     *
     * @return void
     */
```

### `private handleSettingsPost()`

- Datei: `./src/Core/DataController.php`
- Zeile: 545
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion handleSettingsPost
     *
     * @return void
     */
```

### `private requireAdminJson()`

- Datei: `./src/Core/DataController.php`
- Zeile: 584
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion requireAdminJson
     *
     * @return void
     */
```

### `private msg($key)`

- Datei: `./src/Core/DataController.php`
- Zeile: 597
- Rueckgabe: `string`

```code
/**
     * Kurzbeschreibung Funktion msg
     *
     * @param mixed $key
     * @return string
     */
```

### `private msgf($key, $arg)`

- Datei: `./src/Core/DataController.php`
- Zeile: 609
- Rueckgabe: `string`

```code
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
- Zeile: 620
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion assertValidUsername
     *
     * @param mixed $username
     * @return void
     */
```

### `private isValidIpv4($value)`

- Datei: `./src/Core/DataController.php`
- Zeile: 633
- Rueckgabe: `bool`

```code
/**
     * Kurzbeschreibung Funktion isValidIpv4
     *
     * @param mixed $value
     * @return bool
     */
```

### `private getLoginDiagnostics()`

- Datei: `./src/Core/DataController.php`
- Zeile: 643
- Rueckgabe: `array`

```code
/**
     * Kurzbeschreibung Funktion getLoginDiagnostics
     *
     * @return array
     */
```

### `private json($data, $status)`

- Datei: `./src/Core/DataController.php`
- Zeile: 757
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion json
     *
     * @param mixed $data
     * @param mixed $status
     * @return void
     */
```

### `private internalError($context, $e)`

- Datei: `./src/Core/DataController.php`
- Zeile: 772
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion internalError
     *
     * @param mixed $context
     * @param mixed $e
     * @return void
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/Debug.php`

### `public static init($env, $logfile, $debug)`

- Datei: `./src/Core/Debug.php`
- Zeile: 32
- Rueckgabe: `void`

```code
/**
     * Initialisiert Debug-Umgebung
     */
```

### `public static debug($vars)`

- Datei: `./src/Core/Debug.php`
- Zeile: 45
- Rueckgabe: `void`

```code
/**
     * Debug-Ausgabe auf der Seite (Bootstrap Collapse)
     */
```

### `public static log($vars)`

- Datei: `./src/Core/Debug.php`
- Zeile: 101
- Rueckgabe: `void`

```code
/**
     * Debug-Ausgabe in Logfile
     */
```

### `public static dd($vars)`

- Datei: `./src/Core/Debug.php`
- Zeile: 121
- Rueckgabe: `void`

```code
/**
     * Debug + exit
     */
```

### `public static handleException($e)`

- Datei: `./src/Core/Debug.php`
- Zeile: 130
- Rueckgabe: `void`

```code
/**
     * Exception-Handler
     */
```

### `public static handleError($errno, $errstr, $errfile, $errline)`

- Datei: `./src/Core/Debug.php`
- Zeile: 156
- Rueckgabe: `bool`

```code
/**
     * Error-Handler
     */
```

### `public static render()`

- Datei: `./src/Core/Debug.php`
- Zeile: 172
- Rueckgabe: `void`

```code
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

```code
/**
     * Setzt dynamisch eine Eigenschaft der Klasse auf den uebergebenen Wert.
     *
     * @param mixed $key
     * @param mixed $val
     * @return void
     */
```

### `public main()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 55
- Rueckgabe: `void`

```code
/**
     * Der zentrale handler/router in der Verarbeitung
     * über diese Datei werden alle Anfragen an das System "geroutet"
     * und entsprechend auf die erlaubten "Operationen" (op) hin überprüft
     * Verteilt dann auf die entsprechenden Klassen und Funktionen
     *
     * @return void
     */
```

### `private showLogin()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 133
- Rueckgabe: `void`

```code
/**
     * Zeigt die Login-Seite ueber den LoginController an.
     *
     * @return void
     */
```

### `private checkLogin()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 143
- Rueckgabe: `void`

```code
/**
     * Verarbeitet den Login-Versuch ueber den LoginController.
     *
     * @return void
     */
```

### `private baseTemplateData($activeOp)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 154
- Rueckgabe: `array`

```code
/**
     * Baut die gemeinsamen Template-Daten fuer Seitenaufrufe auf.
     *
     * @param mixed $activeOp
     * @return array
     */
```

### `private renderPage($title, $content, $activeOp)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 178
- Rueckgabe: `void`

```code
/**
     * Rendert eine komplette Seite mit Layout, Header und Inhalt.
     *
     * @param mixed $title
     * @param mixed $content
     * @param mixed $activeOp
     * @return void
     */
```

### `private showMain()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 189
- Rueckgabe: `void`

```code
/**
     * Zeigt die Dashboard-Seite an.
     *
     * @return void
     */
```

### `private showUsers()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 200
- Rueckgabe: `void`

```code
/**
     * Zeigt die Benutzerverwaltung an.
     *
     * @return void
     */
```

### `private showLogs()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 211
- Rueckgabe: `void`

```code
/**
     * Zeigt die Log-Ansicht an.
     *
     * @return void
     */
```

### `private showConfig()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 222
- Rueckgabe: `void`

```code
/**
     * Zeigt den Konfigurations-Editor fuer Client-Profile an.
     *
     * @return void
     */
```

### `private showSettings()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 233
- Rueckgabe: `void`

```code
/**
     * Zeigt den Editor fuer die VPN-Server-Einstellungen an.
     *
     * @return void
     */
```

### `private showProfiles()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 244
- Rueckgabe: `void`

```code
/**
     * Zeigt die Seite fuer Konfigurations-Downloads an.
     *
     * @return void
     */
```

### `private showAccount()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 255
- Rueckgabe: `void`

```code
/**
     * Zeigt die Seite zum Verwalten des eigenen Accounts an.
     *
     * @return void
     */
```

### `private showLive()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 266
- Rueckgabe: `void`

```code
/**
     * Liefert eine einfache Live-Status-Antwort als JSON.
     *
     * @return void
     */
```

### `private logout()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 277
- Rueckgabe: `void`

```code
/**
     * Meldet den Benutzer ab und antwortet je nach Request-Typ mit Redirect oder JSON.
     *
     * @return void
     */
```

### `private ensureAdminOrForbidden()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 304
- Rueckgabe: `void`

```code
/**
     * Prueft Adminrechte und zeigt sonst eine Zugriff-verweigert-Seite an.
     *
     * @return void
     */
```

### `private downloadProfileZip()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 318
- Rueckgabe: `void`

```code
/**
     * Liefert die angeforderte Profil-ZIP zum Download aus.
     *
     * @return void
     */
```

### `private showError()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 344
- Rueckgabe: `void`

```code
/**
     * Zeigt die Fehlerseite aus dem Theme oder einen Text-Fallback an.
     *
     * @return void
     */
```

### `private setLanguage()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 360
- Rueckgabe: `void`

```code
/**
     * Setzt die gewaehlte Sprache und leitet sicher auf die vorherige Seite zurueck.
     *
     * @return void
     */
```

### `private isSafeRedirectTarget($target)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 382
- Rueckgabe: `bool`

```code
/**
     * Kurzbeschreibung Funktion isSafeRedirectTarget
     *
     * @param mixed $target
     * @return bool
     */
```

### `private enforceAccessPolicy()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 427
- Rueckgabe: `void`

```code
/**
     * Erzwingt zentrale Zugriffsregeln fuer Login, Rollen, Origin und CSRF.
     *
     * Security-Matrix (Single Source of Truth):
     *
     * OP               Login required   Admin required   CSRF+Origin on POST
     * ----------------------------------------------------------------------
     * login            no               no               no
     * checklogin       no               no               no
     * setlang          yes              no               no (GET only)
     * logout           yes              no               yes
     * dashboard/main   yes              no               n/a
     * account          yes              no               n/a
     * profiles         yes              no               n/a
     * download         yes              no               n/a
     * users            yes              yes              n/a
     * logs             yes              yes              n/a
     * config           yes              yes              n/a
     * settings         yes              yes              n/a
     * data(select=*)   yes              depends          yes for POST
     *
     * data-select admin-only:
     * user, log, config, settings, dashboard_stats, diag_login
     *
     * @return void
     */
```

### `private serveLoginAsset()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 463
- Rueckgabe: `void`

```code
/**
     * Liefert erlaubte Login-Assets sicher aus dem Theme-Verzeichnis aus.
     *
     * @return void
     */
```

### `private enforceDataAccessPolicy($method)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 523
- Rueckgabe: `void`

```code
/**
     * Wendet Zugriffskontrollen fuer data-Requests anhand von select und Methode an.
     *
     * @param mixed $method
     * @return void
     */
```

### `private verifyStateChangingRequest()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 541
- Rueckgabe: `void`

```code
/**
     * Prueft zustandsaendernde Requests auf gleiche Herkunft und gueltiges CSRF-Token.
     *
     * @return void
     */
```

### `private isSameOriginRequest()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 558
- Rueckgabe: `bool`

```code
/**
     * Ermittelt, ob die Anfrage von derselben Origin bzw. demselben Host stammt.
     *
     * @return bool
     */
```

### `private denyRequest($status, $message)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 587
- Rueckgabe: `void`

```code
/**
     * Bricht die Anfrage mit Fehlerstatus ab und gibt JSON oder HTML-Fehler aus.
     *
     * @param mixed $status
     * @param mixed $message
     * @return void
     */
```

### `private getRequestCsrfToken()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 607
- Rueckgabe: `string`

```code
/**
     * Liest das CSRF-Token aus POST-Daten oder einem JSON-Body aus.
     *
     * @return string
     */
```

### `private canUseDebugModal()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 630
- Rueckgabe: `bool`

```code
/**
     * Nur Admin mit gesetztem DEBUG=true darf das Debug-Modal sehen.
     */
```

### `private readDotEnvValue($key)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 650
- Rueckgabe: `string`

```code
/**
     * Liest einen einzelnen Key aus der .env Datei (falls vorhanden).
     */
```

### `private buildDebugModalData()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 689
- Rueckgabe: `array`

```code
/**
     * Baut die Inhalte für das Debug-Modal.
     *
     * @return array<string, mixed>
     */
```

### `private readDebugFile($path)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 710
- Rueckgabe: `string`

```code
/**
     * Liest eine Logdatei robust ein und begrenzt sehr große Inhalte.
     */
```

### `private getGoRequestDebugVars()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 735
- Rueckgabe: `array`

```code
/**
     * Liefert die in GoRequest erzeugten/verwalteten Variablen fuer Debug.
     *
     * @return array<string, mixed>
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/Lang.php`

### `public static init($lang)`

- Datei: `./src/Core/Lang.php`
- Zeile: 29
- Rueckgabe: `void`

```code
/**
     * Initialisiert die Sprache (Session muss vorher gestartet sein)
     */
```

### `public static get($key, $default)`

- Datei: `./src/Core/Lang.php`
- Zeile: 57
- Rueckgabe: `string`

```code
/**
     * Einzeltext holen
     */
```

### `public static getAll()`

- Datei: `./src/Core/Lang.php`
- Zeile: 65
- Rueckgabe: `array`

```code
/**
     * Komplettes Message-Array (für Templates / Backwards-Compatibility)
     */
```

### `public static getCurrent()`

- Datei: `./src/Core/Lang.php`
- Zeile: 73
- Rueckgabe: `string`

```code
/**
     * Aktuelle Sprache
     */
```

### `public static setCurrent($lang)`

- Datei: `./src/Core/Lang.php`
- Zeile: 81
- Rueckgabe: `void`

```code
/**
     * Sprache setzen und neu laden
     */
```

### `public static getAvailableLanguages()`

- Datei: `./src/Core/Lang.php`
- Zeile: 95
- Rueckgabe: `array`

```code
/**
     * Kurzbeschreibung Funktion getAvailableLanguages
     *
     * @return array
     */
```

### `public static getLanguageMeta($code)`

- Datei: `./src/Core/Lang.php`
- Zeile: 125
- Rueckgabe: `array`

```code
/**
     * Liefert Label + Flagge für Sprach-Auswahl.
     */
```

### `private static buildLocaleLabel($langCode, $countryCode)`

- Datei: `./src/Core/Lang.php`
- Zeile: 147
- Rueckgabe: `string`

```code
/**
     * Kurzbeschreibung Funktion buildLocaleLabel
     *
     * @param mixed $langCode
     * @param mixed $countryCode
     * @return string
     */
```

### `private static countryCodeToFlag($countryCode)`

- Datei: `./src/Core/Lang.php`
- Zeile: 171
- Rueckgabe: `string`

```code
/**
     * Kurzbeschreibung Funktion countryCodeToFlag
     *
     * @param mixed $countryCode
     * @return string
     */
```

### `private static sanitizeLangCode($lang)`

- Datei: `./src/Core/Lang.php`
- Zeile: 189
- Rueckgabe: `string`

```code
/**
     * Kurzbeschreibung Funktion sanitizeLangCode
     *
     * @param mixed $lang
     * @return string
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/LoginController.php`

### `public showLogin()`

- Datei: `./src/Core/LoginController.php`
- Zeile: 34
- Rueckgabe: `void`

```code
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

```code
/**
     * Kurzbeschreibung Funktion handleLogin
     *
     * @return void
     */
```

### `private redirect($url)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 160
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion redirect
     *
     * @param mixed $url
     * @return void
     */
```

### `private clientIp()`

- Datei: `./src/Core/LoginController.php`
- Zeile: 171
- Rueckgabe: `string`

```code
/**
     * Kurzbeschreibung Funktion clientIp
     *
     * @return string
     */
```

### `private auditUserRef($username)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 183
- Rueckgabe: `string`

```code
/**
     * Kurzbeschreibung Funktion auditUserRef
     *
     * @param mixed $username
     * @return string
     */
```

### `private loginKey($username)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 198
- Rueckgabe: `string`

```code
/**
     * Kurzbeschreibung Funktion loginKey
     *
     * @param mixed $username
     * @return string
     */
```

### `private isLoginRateLimited($username)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 209
- Rueckgabe: `bool`

```code
/**
     * Kurzbeschreibung Funktion isLoginRateLimited
     *
     * @param mixed $username
     * @return bool
     */
```

### `private registerLoginFailure($username)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 232
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion registerLoginFailure
     *
     * @param mixed $username
     * @return void
     */
```

### `private clearLoginFailures($username)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 274
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion clearLoginFailures
     *
     * @param mixed $username
     * @return void
     */
```

### `private isStrictAdminRoleName($roleName)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 292
- Rueckgabe: `bool`

```code
/**
     * Kurzbeschreibung Funktion isStrictAdminRoleName
     *
     * @param mixed $roleName
     * @return bool
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/LogModel.php`

### `public __construct()`

- Datei: `./src/Core/LogModel.php`
- Zeile: 33
- Rueckgabe: `mixed|null`

```code
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

```code
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

```code
/**
     * Kurzbeschreibung Funktion countLogs
     *
     * @param mixed $search
     * @return int
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/ProfileService.php`

### `public __construct($basePath)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 15
- Rueckgabe: `mixed|null`

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
/**
     * Initialisiert und startet die Session.
     */
```

### `public static regenerateId()`

- Datei: `./src/Core/Session.php`
- Zeile: 230
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion regenerateId
     *
     * @return void
     */
```

### `public static setVar($key, $value)`

- Datei: `./src/Core/Session.php`
- Zeile: 240
- Rueckgabe: `void`

```code
/**
     * Setzt eine Session-Variable.
     */
```

### `public static getVar($key, $default)`

- Datei: `./src/Core/Session.php`
- Zeile: 248
- Rueckgabe: `mixed`

```code
/**
     * Holt eine Session-Variable.
     */
```

### `public static removeVar($key)`

- Datei: `./src/Core/Session.php`
- Zeile: 256
- Rueckgabe: `void`

```code
/**
     * Entfernt eine Session-Variable.
     */
```

### `public static isUser()`

- Datei: `./src/Core/Session.php`
- Zeile: 264
- Rueckgabe: `bool`

```code
/**
     * Prüft, ob ein Benutzer angemeldet ist.
     */
```

### `public static isAdmin()`

- Datei: `./src/Core/Session.php`
- Zeile: 272
- Rueckgabe: `bool`

```code
/**
     * Prüft, ob aktueller Benutzer Admin ist.
     */
```

### `public static getUser()`

- Datei: `./src/Core/Session.php`
- Zeile: 321
- Rueckgabe: `?array`

```code
/**
     * Gibt den aktuellen Benutzer zurück.
     */
```

### `public static setUser($user)`

- Datei: `./src/Core/Session.php`
- Zeile: 329
- Rueckgabe: `void`

```code
/**
     * Setzt den angemeldeten Benutzer.
     */
```

### `public static destroyAll()`

- Datei: `./src/Core/Session.php`
- Zeile: 337
- Rueckgabe: `void`

```code
/**
     * Zerstört die komplette Session inkl. Cookie.
     */
```

### `public static getCsrfToken()`

- Datei: `./src/Core/Session.php`
- Zeile: 357
- Rueckgabe: `string`

```code
/**
     * CSRF token for state-changing requests.
     */
```

### `public static verifyCsrfToken($token)`

- Datei: `./src/Core/Session.php`
- Zeile: 372
- Rueckgabe: `bool`

```code
/**
     * Kurzbeschreibung Funktion verifyCsrfToken
     *
     * @param mixed $token
     * @return bool
     */
```

### `private static toBool($value)`

- Datei: `./src/Core/Session.php`
- Zeile: 392
- Rueckgabe: `bool`

```code
/**
     * Kurzbeschreibung Funktion toBool
     *
     * @param mixed $value
     * @return bool
     */
```

### `private static isStrictAdminRoleName($roleName)`

- Datei: `./src/Core/Session.php`
- Zeile: 414
- Rueckgabe: `bool`

```code
/**
     * Kurzbeschreibung Funktion isStrictAdminRoleName
     *
     * @param mixed $roleName
     * @return bool
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/UserController.php`

### `public index()`

- Datei: `./src/Core/UserController.php`
- Zeile: 34
- Rueckgabe: `void`

```code
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

```code
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

```code
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
- Zeile: 93
- Rueckgabe: `int`

```code
/**
     * Kurzbeschreibung Funktion countUsers
     *
     * @param mixed $search
     * @return int
     */
```

### `public countOnlineUsers()`

- Datei: `./src/Core/UserModel.php`
- Zeile: 117
- Rueckgabe: `int`

```code
/**
     * Kurzbeschreibung Funktion countOnlineUsers
     *
     * @return int
     */
```

### `public userExists($username)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 129
- Rueckgabe: `bool`

```code
/**
     * Kurzbeschreibung Funktion userExists
     *
     * @param mixed $username
     * @return bool
     */
```

### `public createUser($username, $password, $isAdmin)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 144
- Rueckgabe: `void`

```code
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
- Zeile: 168
- Rueckgabe: `void`

```code
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
- Zeile: 184
- Rueckgabe: `void`

```code
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
- Zeile: 201
- Rueckgabe: `void`

```code
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
- Zeile: 218
- Rueckgabe: `void`

```code
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
- Zeile: 236
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion setUserLimits
     *
     * @param mixed $username
     * @param mixed $startDate
     * @param mixed $endDate
     * @return void
     */
```

### `public setUserFixedIp($username, $fixedIp)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 260
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion setUserFixedIp
     *
     * @param mixed $username
     * @param mixed $fixedIp
     * @return void
     */
```

### `public isFixedIpInUseByOtherUser($username, $fixedIp)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 310
- Rueckgabe: `bool`

```code
/**
     * Kurzbeschreibung Funktion isFixedIpInUseByOtherUser
     *
     * @param mixed $username
     * @param mixed $fixedIp
     * @return bool
     */
```

### `public deleteUser($username)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 334
- Rueckgabe: `void`

```code
/**
     * Kurzbeschreibung Funktion deleteUser
     *
     * @param mixed $username
     * @return void
     */
```

### `public verifyPassword($uid, $password)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 347
- Rueckgabe: `bool`

```code
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
- Zeile: 365
- Rueckgabe: `int`

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
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

```code
/**
     * Lädt eine Layout-Datei aus /src/Layout/
     */
```

### `public render()`

- Datei: `./src/Templates/Template.php`
- Zeile: 65
- Rueckgabe: `string`

```code
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

```code
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

<hr style="border:0; height:5px; background:#1e90ff;">

