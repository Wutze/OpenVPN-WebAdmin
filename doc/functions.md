# Funktionsreferenz

Automatisch generiert am: 2026-02-22 14:35:05

## `src/Core/CaTlsService.php`

### `public __construct($basePath)`

- Datei: `src/Core/CaTlsService.php`
- Zeile: 33
- Rueckgabe: `mixed|null`

```
/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $basePath Eingabewert fuer basePath.
 * @return mixed|null Rueckgabewert der Funktion.
 */
```

### `public getCurrent()`

- Datei: `src/Core/CaTlsService.php`
- Zeile: 45
- Rueckgabe: `array`

```
/**
 * Liest current und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `public save($caContent, $tlsContent)`

- Datei: `src/Core/CaTlsService.php`
- Zeile: 66
- Rueckgabe: `array`

```
/**
 * Speichert Daten persistent.
 *
 * @param mixed $caContent Eingabewert fuer caContent.
 * @param mixed $tlsContent Eingabewert fuer tlsContent.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private ensureStorage()`

- Datei: `src/Core/CaTlsService.php`
- Zeile: 83
- Rueckgabe: `void`

```
/**
 * Fuehrt ensure storage entsprechend der internen Logik aus.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private ensureFile($path)`

- Datei: `src/Core/CaTlsService.php`
- Zeile: 102
- Rueckgabe: `void`

```
/**
 * Fuehrt ensure file entsprechend der internen Logik aus.
 *
 * @param mixed $path Eingabewert fuer path.
 * @return void Kein Rueckgabewert.
 */
```

### `private writeSecureFile($path, $content)`

- Datei: `src/Core/CaTlsService.php`
- Zeile: 120
- Rueckgabe: `void`

```
/**
 * Schreibt secure file in das Zielsystem.
 *
 * @param mixed $path Eingabewert fuer path.
 * @param mixed $content Eingabewert fuer content.
 * @return void Kein Rueckgabewert.
 */
```

### `private resolveUsableBasePath()`

- Datei: `src/Core/CaTlsService.php`
- Zeile: 146
- Rueckgabe: `string`

```
/**
 * Fuehrt resolve usable base path entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
```

### `private canUsePath($path)`

- Datei: `src/Core/CaTlsService.php`
- Zeile: 171
- Rueckgabe: `bool`

```
/**
 * Prueft, ob use path zutrifft.
 *
 * @param mixed $path Eingabewert fuer path.
 * @return bool True bei Erfolg, sonst false.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/ConfigService.php`

### `public __construct($basePath)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 32
- Rueckgabe: `mixed|null`

```
/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $basePath Eingabewert fuer basePath.
 * @return mixed|null Rueckgabewert der Funktion.
 */
```

### `public listSystems()`

- Datei: `src/Core/ConfigService.php`
- Zeile: 43
- Rueckgabe: `array`

```
/**
 * Liefert eine Liste von systems.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `public getConfig($system)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 73
- Rueckgabe: `string`

```
/**
 * Liest config und gibt den Wert zurueck.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
```

### `public saveConfig($system, $content)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 90
- Rueckgabe: `array`

```
/**
 * Speichert config persistent.
 *
 * @param mixed $system Eingabewert fuer system.
 * @param mixed $content Eingabewert fuer content.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `public getHistoryList($system)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 133
- Rueckgabe: `array`

```
/**
 * Liest history list und gibt den Wert zurueck.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `public getDiffStatsAgainstCurrent($system)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 164
- Rueckgabe: `array`

```
/**
 * Liest diff stats against current und gibt den Wert zurueck.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `public diffHistoryFile($system, $historyFile)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 186
- Rueckgabe: `array`

```
/**
 * Vergleicht history file und liefert die Unterschiede.
 *
 * @param mixed $system Eingabewert fuer system.
 * @param mixed $historyFile Eingabewert fuer historyFile.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private getHistoryContent($system, $historyFile)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 201
- Rueckgabe: `string`

```
/**
 * Liest history content und gibt den Wert zurueck.
 *
 * @param mixed $system Eingabewert fuer system.
 * @param mixed $historyFile Eingabewert fuer historyFile.
 * @return string Rueckgabe als Text.
 */
```

### `private buildDiffData($oldText, $newText)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 219
- Rueckgabe: `array`

```
/**
 * Erzeugt diff data auf Basis der Eingabedaten.
 *
 * @param mixed $oldText Eingabewert fuer oldText.
 * @param mixed $newText Eingabewert fuer newText.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private diffOps($a, $b)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 255
- Rueckgabe: `array`

```
/**
 * Vergleicht ops und liefert die Unterschiede.
 *
 * @param mixed $a Eingabewert fuer a.
 * @param mixed $b Eingabewert fuer b.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private clientPath($system)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 308
- Rueckgabe: `string`

```
/**
 * Fuehrt client path entsprechend der internen Logik aus.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
```

### `private historyDir($system)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 320
- Rueckgabe: `string`

```
/**
 * Fuehrt history dir entsprechend der internen Logik aus.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
```

### `private sanitizeSystem($system)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 332
- Rueckgabe: `string`

```
/**
 * Fuehrt sanitize system entsprechend der internen Logik aus.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
```

### `private writeAtomic($path, $content)`

- Datei: `src/Core/ConfigService.php`
- Zeile: 351
- Rueckgabe: `void`

```
/**
 * Schreibt atomar in die Zieldatei (tmp + rename).
 * Dadurch funktionieren Saves auch dann, wenn die bestehende Datei restriktive Rechte hat,
 * aber das Verzeichnis beschreibbar ist.
 *
 * @param mixed $path
 * @param mixed $content
 * @return void
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/DataController.php`

### `public handle()`

- Datei: `src/Core/DataController.php`
- Zeile: 28
- Rueckgabe: `void`

```
/**
 * Verarbeitet die Anfrage entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleGet($select)`

- Datei: `src/Core/DataController.php`
- Zeile: 54
- Rueckgabe: `void`

```
/**
 * Verarbeitet get entsprechend der Logik.
 *
 * @param mixed $select Eingabewert fuer select.
 * @return void Kein Rueckgabewert.
 */
```

### `private handlePost($select)`

- Datei: `src/Core/DataController.php`
- Zeile: 102
- Rueckgabe: `void`

```
/**
 * Verarbeitet post entsprechend der Logik.
 *
 * @param mixed $select Eingabewert fuer select.
 * @return void Kein Rueckgabewert.
 */
```

### `private getUsers()`

- Datei: `src/Core/DataController.php`
- Zeile: 138
- Rueckgabe: `array`

```
/**
 * Liest users und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private getLogs()`

- Datei: `src/Core/DataController.php`
- Zeile: 157
- Rueckgabe: `array`

```
/**
 * Liest logs und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private getDashboardStats()`

- Datei: `src/Core/DataController.php`
- Zeile: 175
- Rueckgabe: `array`

```
/**
 * Liest dashboard stats und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private handleUserAction()`

- Datei: `src/Core/DataController.php`
- Zeile: 211
- Rueckgabe: `void`

```
/**
 * Verarbeitet user action entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleAccountAction()`

- Datei: `src/Core/DataController.php`
- Zeile: 343
- Rueckgabe: `void`

```
/**
 * Verarbeitet account action entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleProfileAction()`

- Datei: `src/Core/DataController.php`
- Zeile: 385
- Rueckgabe: `void`

```
/**
 * Verarbeitet profile action entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleConfigGet()`

- Datei: `src/Core/DataController.php`
- Zeile: 412
- Rueckgabe: `void`

```
/**
 * Verarbeitet config get entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleConfigPost()`

- Datei: `src/Core/DataController.php`
- Zeile: 460
- Rueckgabe: `void`

```
/**
 * Verarbeitet config post entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleSettingsGet()`

- Datei: `src/Core/DataController.php`
- Zeile: 518
- Rueckgabe: `void`

```
/**
 * Verarbeitet settings get entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleSettingsPost()`

- Datei: `src/Core/DataController.php`
- Zeile: 560
- Rueckgabe: `void`

```
/**
 * Verarbeitet settings post entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleCaTlsGet()`

- Datei: `src/Core/DataController.php`
- Zeile: 599
- Rueckgabe: `void`

```
/**
 * Verarbeitet ca tls get entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleCaTlsPost()`

- Datei: `src/Core/DataController.php`
- Zeile: 632
- Rueckgabe: `void`

```
/**
 * Verarbeitet ca tls post entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private requireAdminJson()`

- Datei: `src/Core/DataController.php`
- Zeile: 666
- Rueckgabe: `void`

```
/**
 * Fuehrt require admin json entsprechend der internen Logik aus.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private msg($key)`

- Datei: `src/Core/DataController.php`
- Zeile: 679
- Rueckgabe: `string`

```
/**
 * Fuehrt msg entsprechend der internen Logik aus.
 *
 * @param mixed $key Eingabewert fuer key.
 * @return string Rueckgabe als Text.
 */
```

### `private msgf($key, $arg)`

- Datei: `src/Core/DataController.php`
- Zeile: 691
- Rueckgabe: `string`

```
/**
 * Fuehrt msgf entsprechend der internen Logik aus.
 *
 * @param mixed $key Eingabewert fuer key.
 * @param mixed $arg Eingabewert fuer arg.
 * @return string Rueckgabe als Text.
 */
```

### `private assertValidUsername($username)`

- Datei: `src/Core/DataController.php`
- Zeile: 702
- Rueckgabe: `void`

```
/**
 * Fuehrt assert valid username entsprechend der internen Logik aus.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return void Kein Rueckgabewert.
 */
```

### `private isValidIpv4($value)`

- Datei: `src/Core/DataController.php`
- Zeile: 715
- Rueckgabe: `bool`

```
/**
 * Prueft, ob valid ipv4 zutrifft.
 *
 * @param mixed $value Eingabewert fuer value.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `private getLoginDiagnostics()`

- Datei: `src/Core/DataController.php`
- Zeile: 725
- Rueckgabe: `array`

```
/**
 * Liest login diagnostics und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private json($data, $status)`

- Datei: `src/Core/DataController.php`
- Zeile: 839
- Rueckgabe: `void`

```
/**
 * Fuehrt json entsprechend der internen Logik aus.
 *
 * @param mixed $data Eingabewert fuer data.
 * @param mixed $status Eingabewert fuer status.
 * @return void Kein Rueckgabewert.
 */
```

### `private internalError($context, $e)`

- Datei: `src/Core/DataController.php`
- Zeile: 854
- Rueckgabe: `void`

```
/**
 * Fuehrt internal error entsprechend der internen Logik aus.
 *
 * @param mixed $context Eingabewert fuer context.
 * @param mixed $e Eingabewert fuer e.
 * @return void Kein Rueckgabewert.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/Database.php`

### `private __construct($config)`

- Datei: `src/Core/Database.php`
- Zeile: 35
- Rueckgabe: `mixed|null`

```
/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $config Eingabewert fuer config.
 * @return mixed|null Rueckgabewert der Funktion.
 */
```

### `public static getInstance($config)`

- Datei: `src/Core/Database.php`
- Zeile: 57
- Rueckgabe: `Database`

```
/**
 * Liest instance und gibt den Wert zurueck.
 *
 * @param mixed $config Eingabewert fuer config.
 * @return Database Rueckgabewert der Funktion.
 */
```

### `public getConnection()`

- Datei: `src/Core/Database.php`
- Zeile: 73
- Rueckgabe: `PDO`

```
/**
 * Liest connection und gibt den Wert zurueck.
 *
 * @return PDO Rueckgabewert der Funktion.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/Debug.php`

### `public static init($env, $logfile, $debug)`

- Datei: `src/Core/Debug.php`
- Zeile: 33
- Rueckgabe: `void`

```
/**
 * Initialisiert Debug-Umgebung
 */
```

### `public static debug($vars)`

- Datei: `src/Core/Debug.php`
- Zeile: 51
- Rueckgabe: `void`

```
/**
 * Debug-Ausgabe auf der Seite (Bootstrap Collapse)
 */
```

### `public static log($vars)`

- Datei: `src/Core/Debug.php`
- Zeile: 107
- Rueckgabe: `void`

```
/**
 * Debug-Ausgabe in Logfile
 */
```

### `public static logException($e, $context)`

- Datei: `src/Core/Debug.php`
- Zeile: 132
- Rueckgabe: `void`

```
/**
 * Schreibt Exceptions strukturiert in exceptions.log (nur bei DEBUG=true).
 *
 * @param mixed $e
 * @param mixed $context
 * @return void
 */
```

### `public static dd($vars)`

- Datei: `src/Core/Debug.php`
- Zeile: 158
- Rueckgabe: `void`

```
/**
 * Debug + exit
 */
```

### `public static handleException($e)`

- Datei: `src/Core/Debug.php`
- Zeile: 167
- Rueckgabe: `void`

```
/**
 * Exception-Handler
 */
```

### `public static handleError($errno, $errstr, $errfile, $errline)`

- Datei: `src/Core/Debug.php`
- Zeile: 192
- Rueckgabe: `bool`

```
/**
 * Error-Handler
 */
```

### `public static render()`

- Datei: `src/Core/Debug.php`
- Zeile: 225
- Rueckgabe: `void`

```
/**
 * Stellt Inhalte fuer die Ausgabe dar.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private static renderExceptionScreen($error)`

- Datei: `src/Core/Debug.php`
- Zeile: 238
- Rueckgabe: `void`

```
/**
 * Gibt ungefangene Exceptions direkt im Browser aus (nur bei DEBUG=true).
 *
 * @param mixed $error
 * @return void
 */
```

### `private static ensureLogTargets()`

- Datei: `src/Core/Debug.php`
- Zeile: 256
- Rueckgabe: `void`

```
/**
 * Stellt sicher, dass die Logziele existieren und beschreibbar sind.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/GoRequest.php`

### `public set_value($key, $val)`

- Datei: `src/Core/GoRequest.php`
- Zeile: 43
- Rueckgabe: `void`

```
/**
 * Setzt dynamisch eine Eigenschaft der Klasse auf den uebergebenen Wert.
 *
 * @param mixed $key
 * @param mixed $val
 * @return void
 */
```

### `public main()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 56
- Rueckgabe: `void`

```
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

- Datei: `src/Core/GoRequest.php`
- Zeile: 138
- Rueckgabe: `void`

```
/**
 * Zeigt die Login-Seite ueber den LoginController an.
 *
 * @return void
 */
```

### `private checkLogin()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 148
- Rueckgabe: `void`

```
/**
 * Verarbeitet den Login-Versuch ueber den LoginController.
 *
 * @return void
 */
```

### `private baseTemplateData($activeOp)`

- Datei: `src/Core/GoRequest.php`
- Zeile: 159
- Rueckgabe: `array`

```
/**
 * Baut die gemeinsamen Template-Daten fuer Seitenaufrufe auf.
 *
 * @param mixed $activeOp
 * @return array
 */
```

### `private renderPage($title, $content, $activeOp)`

- Datei: `src/Core/GoRequest.php`
- Zeile: 183
- Rueckgabe: `void`

```
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

- Datei: `src/Core/GoRequest.php`
- Zeile: 194
- Rueckgabe: `void`

```
/**
 * Zeigt die Dashboard-Seite an.
 *
 * @return void
 */
```

### `private showUsers()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 205
- Rueckgabe: `void`

```
/**
 * Zeigt die Benutzerverwaltung an.
 *
 * @return void
 */
```

### `private showLogs()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 216
- Rueckgabe: `void`

```
/**
 * Zeigt die Log-Ansicht an.
 *
 * @return void
 */
```

### `private showConfig()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 227
- Rueckgabe: `void`

```
/**
 * Zeigt den Konfigurations-Editor fuer Client-Profile an.
 *
 * @return void
 */
```

### `private showSettings()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 238
- Rueckgabe: `void`

```
/**
 * Zeigt den Editor fuer die VPN-Server-Einstellungen an.
 *
 * @return void
 */
```

### `private showCaTls()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 249
- Rueckgabe: `void`

```
/**
 * Zeigt die Seite fuer CA/TLS Inhalte an.
 *
 * @return void
 */
```

### `private showProfiles()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 260
- Rueckgabe: `void`

```
/**
 * Zeigt die Seite fuer Konfigurations-Downloads an.
 *
 * @return void
 */
```

### `private showAccount()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 271
- Rueckgabe: `void`

```
/**
 * Zeigt die Seite zum Verwalten des eigenen Accounts an.
 *
 * @return void
 */
```

### `private showLive()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 282
- Rueckgabe: `void`

```
/**
 * Liefert eine einfache Live-Status-Antwort als JSON.
 *
 * @return void
 */
```

### `private logout()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 293
- Rueckgabe: `void`

```
/**
 * Meldet den Benutzer ab und antwortet je nach Request-Typ mit Redirect oder JSON.
 *
 * @return void
 */
```

### `private ensureAdminOrForbidden()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 320
- Rueckgabe: `void`

```
/**
 * Prueft Adminrechte und zeigt sonst eine Zugriff-verweigert-Seite an.
 *
 * @return void
 */
```

### `private downloadProfileZip()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 334
- Rueckgabe: `void`

```
/**
 * Liefert die angeforderte Profil-ZIP zum Download aus.
 *
 * @return void
 */
```

### `private showError()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 360
- Rueckgabe: `void`

```
/**
 * Zeigt die Fehlerseite aus dem Theme oder einen Text-Fallback an.
 *
 * @return void
 */
```

### `private setLanguage()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 376
- Rueckgabe: `void`

```
/**
 * Setzt die gewaehlte Sprache und leitet sicher auf die vorherige Seite zurueck.
 *
 * @return void
 */
```

### `private isSafeRedirectTarget($target)`

- Datei: `src/Core/GoRequest.php`
- Zeile: 398
- Rueckgabe: `bool`

```
/**
 * Prueft, ob safe redirect target zutrifft.
 *
 * @param mixed $target Eingabewert fuer target.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `private enforceAccessPolicy()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 443
- Rueckgabe: `void`

```
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

- Datei: `src/Core/GoRequest.php`
- Zeile: 479
- Rueckgabe: `void`

```
/**
 * Liefert erlaubte Login-Assets sicher aus dem Theme-Verzeichnis aus.
 *
 * @return void
 */
```

### `private enforceDataAccessPolicy($method)`

- Datei: `src/Core/GoRequest.php`
- Zeile: 539
- Rueckgabe: `void`

```
/**
 * Wendet Zugriffskontrollen fuer data-Requests anhand von select und Methode an.
 *
 * @param mixed $method
 * @return void
 */
```

### `private verifyStateChangingRequest()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 557
- Rueckgabe: `void`

```
/**
 * Prueft zustandsaendernde Requests auf gleiche Herkunft und gueltiges CSRF-Token.
 *
 * @return void
 */
```

### `private isSameOriginRequest()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 574
- Rueckgabe: `bool`

```
/**
 * Ermittelt, ob die Anfrage von derselben Origin bzw. demselben Host stammt.
 *
 * @return bool
 */
```

### `private denyRequest($status, $message)`

- Datei: `src/Core/GoRequest.php`
- Zeile: 603
- Rueckgabe: `void`

```
/**
 * Bricht die Anfrage mit Fehlerstatus ab und gibt JSON oder HTML-Fehler aus.
 *
 * @param mixed $status
 * @param mixed $message
 * @return void
 */
```

### `private getRequestCsrfToken()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 623
- Rueckgabe: `string`

```
/**
 * Liest das CSRF-Token aus POST-Daten oder einem JSON-Body aus.
 *
 * @return string
 */
```

### `private canUseDebugModal()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 646
- Rueckgabe: `bool`

```
/**
 * Nur Admin mit gesetztem DEBUG=true darf das Debug-Modal sehen.
 */
```

### `private readDotEnvValue($key)`

- Datei: `src/Core/GoRequest.php`
- Zeile: 666
- Rueckgabe: `string`

```
/**
 * Liest einen einzelnen Key aus der .env Datei (falls vorhanden).
 */
```

### `private buildDebugModalData()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 705
- Rueckgabe: `array`

```
/**
 * Baut die Inhalte für das Debug-Modal.
 *
 * @return array<string, mixed>
 */
```

### `private readDebugFile($path)`

- Datei: `src/Core/GoRequest.php`
- Zeile: 726
- Rueckgabe: `string`

```
/**
 * Liest eine Logdatei robust ein und begrenzt sehr große Inhalte.
 */
```

### `private getGoRequestDebugVars()`

- Datei: `src/Core/GoRequest.php`
- Zeile: 751
- Rueckgabe: `array`

```
/**
 * Liefert die in GoRequest erzeugten/verwalteten Variablen fuer Debug.
 *
 * @return array<string, mixed>
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/Lang.php`

### `public static init($lang)`

- Datei: `src/Core/Lang.php`
- Zeile: 29
- Rueckgabe: `void`

```
/**
 * Initialisiert die Sprache (Session muss vorher gestartet sein)
 */
```

### `public static get($key, $default)`

- Datei: `src/Core/Lang.php`
- Zeile: 57
- Rueckgabe: `string`

```
/**
 * Einzeltext holen
 */
```

### `public static getAll()`

- Datei: `src/Core/Lang.php`
- Zeile: 65
- Rueckgabe: `array`

```
/**
 * Komplettes Message-Array (für Templates / Backwards-Compatibility)
 */
```

### `public static getCurrent()`

- Datei: `src/Core/Lang.php`
- Zeile: 73
- Rueckgabe: `string`

```
/**
 * Aktuelle Sprache
 */
```

### `public static setCurrent($lang)`

- Datei: `src/Core/Lang.php`
- Zeile: 81
- Rueckgabe: `void`

```
/**
 * Sprache setzen und neu laden
 */
```

### `public static getAvailableLanguages()`

- Datei: `src/Core/Lang.php`
- Zeile: 95
- Rueckgabe: `array`

```
/**
 * Liest available languages und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `public static getLanguageMeta($code)`

- Datei: `src/Core/Lang.php`
- Zeile: 125
- Rueckgabe: `array`

```
/**
 * Liefert Label + Flagge für Sprach-Auswahl.
 */
```

### `private static buildLocaleLabel($langCode, $countryCode)`

- Datei: `src/Core/Lang.php`
- Zeile: 147
- Rueckgabe: `string`

```
/**
 * Erzeugt locale label auf Basis der Eingabedaten.
 *
 * @param mixed $langCode Eingabewert fuer langCode.
 * @param mixed $countryCode Eingabewert fuer countryCode.
 * @return string Rueckgabe als Text.
 */
```

### `private static countryCodeToFlag($countryCode)`

- Datei: `src/Core/Lang.php`
- Zeile: 171
- Rueckgabe: `string`

```
/**
 * Fuehrt country code to flag entsprechend der internen Logik aus.
 *
 * @param mixed $countryCode Eingabewert fuer countryCode.
 * @return string Rueckgabe als Text.
 */
```

### `private static sanitizeLangCode($lang)`

- Datei: `src/Core/Lang.php`
- Zeile: 189
- Rueckgabe: `string`

```
/**
 * Fuehrt sanitize lang code entsprechend der internen Logik aus.
 *
 * @param mixed $lang Eingabewert fuer lang.
 * @return string Rueckgabe als Text.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/LogModel.php`

### `public __construct()`

- Datei: `src/Core/LogModel.php`
- Zeile: 33
- Rueckgabe: `mixed|null`

```
/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @return mixed|null Rueckgabewert der Funktion.
 */
```

### `public getAllLogs($limit, $offset, $search)`

- Datei: `src/Core/LogModel.php`
- Zeile: 46
- Rueckgabe: `array`

```
/**
 * Liest all logs und gibt den Wert zurueck.
 *
 * @param mixed $limit Eingabewert fuer limit.
 * @param mixed $offset Eingabewert fuer offset.
 * @param mixed $search Eingabewert fuer search.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `public countLogs($search)`

- Datei: `src/Core/LogModel.php`
- Zeile: 76
- Rueckgabe: `int`

```
/**
 * Fuehrt count logs entsprechend der internen Logik aus.
 *
 * @param mixed $search Eingabewert fuer search.
 * @return int Rueckgabewert der Funktion.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/LoginController.php`

### `public showLogin()`

- Datei: `src/Core/LoginController.php`
- Zeile: 34
- Rueckgabe: `void`

```
/**
 * Stellt login fuer die Ausgabe dar.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `public handleLogin()`

- Datei: `src/Core/LoginController.php`
- Zeile: 52
- Rueckgabe: `void`

```
/**
 * Verarbeitet login entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private redirect($url)`

- Datei: `src/Core/LoginController.php`
- Zeile: 160
- Rueckgabe: `void`

```
/**
 * Fuehrt redirect entsprechend der internen Logik aus.
 *
 * @param mixed $url Eingabewert fuer url.
 * @return void Kein Rueckgabewert.
 */
```

### `private clientIp()`

- Datei: `src/Core/LoginController.php`
- Zeile: 171
- Rueckgabe: `string`

```
/**
 * Fuehrt client ip entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
```

### `private auditUserRef($username)`

- Datei: `src/Core/LoginController.php`
- Zeile: 183
- Rueckgabe: `string`

```
/**
 * Fuehrt audit user ref entsprechend der internen Logik aus.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return string Rueckgabe als Text.
 */
```

### `private loginKey($username)`

- Datei: `src/Core/LoginController.php`
- Zeile: 198
- Rueckgabe: `string`

```
/**
 * Fuehrt login key entsprechend der internen Logik aus.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return string Rueckgabe als Text.
 */
```

### `private isLoginRateLimited($username)`

- Datei: `src/Core/LoginController.php`
- Zeile: 209
- Rueckgabe: `bool`

```
/**
 * Prueft, ob login rate limited zutrifft.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `private registerLoginFailure($username)`

- Datei: `src/Core/LoginController.php`
- Zeile: 232
- Rueckgabe: `void`

```
/**
 * Fuehrt register login failure entsprechend der internen Logik aus.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return void Kein Rueckgabewert.
 */
```

### `private clearLoginFailures($username)`

- Datei: `src/Core/LoginController.php`
- Zeile: 274
- Rueckgabe: `void`

```
/**
 * Fuehrt clear login failures entsprechend der internen Logik aus.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return void Kein Rueckgabewert.
 */
```

### `private isStrictAdminRoleName($roleName)`

- Datei: `src/Core/LoginController.php`
- Zeile: 292
- Rueckgabe: `bool`

```
/**
 * Prueft, ob strict admin role name zutrifft.
 *
 * @param mixed $roleName Eingabewert fuer roleName.
 * @return bool True bei Erfolg, sonst false.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/ProfileService.php`

### `public __construct($basePath)`

- Datei: `src/Core/ProfileService.php`
- Zeile: 31
- Rueckgabe: `mixed|null`

```
/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $basePath Eingabewert fuer basePath.
 * @return mixed|null Rueckgabewert der Funktion.
 */
```

### `public listSystems()`

- Datei: `src/Core/ProfileService.php`
- Zeile: 41
- Rueckgabe: `array`

```
/**
 * Liefert eine Liste von systems.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `public buildZip($system)`

- Datei: `src/Core/ProfileService.php`
- Zeile: 92
- Rueckgabe: `string`

```
/**
 * Erzeugt zip auf Basis der Eingabedaten.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
```

### `public getZipPath($system)`

- Datei: `src/Core/ProfileService.php`
- Zeile: 139
- Rueckgabe: `string`

```
/**
 * Liest zip path und gibt den Wert zurueck.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
```

### `private sanitizeSystem($system)`

- Datei: `src/Core/ProfileService.php`
- Zeile: 157
- Rueckgabe: `string`

```
/**
 * Fuehrt sanitize system entsprechend der internen Logik aus.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
```

### `private zipPathFor($system)`

- Datei: `src/Core/ProfileService.php`
- Zeile: 173
- Rueckgabe: `string`

```
/**
 * Fuehrt zip path for entsprechend der internen Logik aus.
 *
 * @param mixed $system Eingabewert fuer system.
 * @return string Rueckgabe als Text.
 */
```

### `private isAllowedSystemDir($system, $dir)`

- Datei: `src/Core/ProfileService.php`
- Zeile: 185
- Rueckgabe: `bool`

```
/**
 * Prueft, ob allowed system dir zutrifft.
 *
 * @param mixed $system Eingabewert fuer system.
 * @param mixed $dir Eingabewert fuer dir.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `private injectCaTlsBlocks($content)`

- Datei: `src/Core/ProfileService.php`
- Zeile: 204
- Rueckgabe: `string`

```
/**
 * Fuehrt inject ca tls blocks entsprechend der internen Logik aus.
 *
 * @param mixed $content Eingabewert fuer content.
 * @return string Rueckgabe als Text.
 */
```

### `private replaceOvpnBlock($content, $tag, $blockContent)`

- Datei: `src/Core/ProfileService.php`
- Zeile: 233
- Rueckgabe: `string`

```
/**
 * Fuehrt replace ovpn block entsprechend der internen Logik aus.
 *
 * @param mixed $content Eingabewert fuer content.
 * @param mixed $tag Eingabewert fuer tag.
 * @param mixed $blockContent Eingabewert fuer blockContent.
 * @return string Rueckgabe als Text.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/ServerSettingsService.php`

### `public __construct($cfg)`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 31
- Rueckgabe: `mixed|null`

```
/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $cfg Eingabewert fuer cfg.
 * @return mixed|null Rueckgabewert der Funktion.
 */
```

### `public getCurrent()`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 51
- Rueckgabe: `array`

```
/**
 * Liest current und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `public save($content)`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 90
- Rueckgabe: `array`

```
/**
 * Speichert Daten persistent.
 *
 * @param mixed $content Eingabewert fuer content.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `public getHistoryList()`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 120
- Rueckgabe: `array`

```
/**
 * Liest history list und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `public diffHistoryFileAgainstCurrent($historyFile, $current)`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 151
- Rueckgabe: `array`

```
/**
 * Vergleicht history file against current und liefert die Unterschiede.
 *
 * @param mixed $historyFile Eingabewert fuer historyFile.
 * @param mixed $current Eingabewert fuer current.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private getDiffStatsAgainstCurrent($current)`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 169
- Rueckgabe: `array`

```
/**
 * Liest diff stats against current und gibt den Wert zurueck.
 *
 * @param mixed $current Eingabewert fuer current.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private fetchFromUrl($url)`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 190
- Rueckgabe: `string`

```
/**
 * Fuehrt fetch from url entsprechend der internen Logik aus.
 *
 * @param mixed $url Eingabewert fuer url.
 * @return string Rueckgabe als Text.
 */
```

### `private writeCache($content)`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 223
- Rueckgabe: `void`

```
/**
 * Schreibt cache in das Zielsystem.
 *
 * @param mixed $content Eingabewert fuer content.
 * @return void Kein Rueckgabewert.
 */
```

### `private archiveFile($file)`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 244
- Rueckgabe: `void`

```
/**
 * Fuehrt archive file entsprechend der internen Logik aus.
 *
 * @param mixed $file Eingabewert fuer file.
 * @return void Kein Rueckgabewert.
 */
```

### `private readSavePath()`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 262
- Rueckgabe: `string`

```
/**
 * Laedt save path aus der Quelle.
 *
 * @return string Rueckgabe als Text.
 */
```

### `private readFile($path)`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 285
- Rueckgabe: `string`

```
/**
 * Laedt file aus der Quelle.
 *
 * @param mixed $path Eingabewert fuer path.
 * @return string Rueckgabe als Text.
 */
```

### `private ensureSaveFileExists()`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 299
- Rueckgabe: `void`

```
/**
 * Fuehrt ensure save file exists entsprechend der internen Logik aus.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private buildDiffData($oldText, $newText)`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 324
- Rueckgabe: `array`

```
/**
 * Erzeugt diff data auf Basis der Eingabedaten.
 *
 * @param mixed $oldText Eingabewert fuer oldText.
 * @param mixed $newText Eingabewert fuer newText.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private diffOps($a, $b)`

- Datei: `src/Core/ServerSettingsService.php`
- Zeile: 360
- Rueckgabe: `array`

```
/**
 * Vergleicht ops und liefert die Unterschiede.
 *
 * @param mixed $a Eingabewert fuer a.
 * @param mixed $b Eingabewert fuer b.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/Session.php`

### `public __construct($db, $lifetime)`

- Datei: `src/Core/Session.php`
- Zeile: 48
- Rueckgabe: `mixed|null`

```
/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $db Eingabewert fuer db.
 * @param mixed $lifetime Eingabewert fuer lifetime.
 * @return mixed|null Rueckgabewert der Funktion.
 */
```

### `public open($savePath, $sessionName)`

- Datei: `src/Core/Session.php`
- Zeile: 77
- Rueckgabe: `bool`

```
/**
 * Fuehrt open entsprechend der internen Logik aus.
 *
 * @param mixed $savePath Eingabewert fuer savePath.
 * @param mixed $sessionName Eingabewert fuer sessionName.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `public close()`

- Datei: `src/Core/Session.php`
- Zeile: 87
- Rueckgabe: `bool`

```
/**
 * Fuehrt close entsprechend der internen Logik aus.
 *
 * @return bool True bei Erfolg, sonst false.
 */
```

### `public read($id)`

- Datei: `src/Core/Session.php`
- Zeile: 98
- Rueckgabe: `string|false`

```
/**
 * Laedt Daten aus der Quelle.
 *
 * @param mixed $id Eingabewert fuer id.
 * @return string|false Rueckgabewert der Funktion.
 */
```

### `public write($id, $data)`

- Datei: `src/Core/Session.php`
- Zeile: 126
- Rueckgabe: `bool`

```
/**
 * Schreibt Daten in das Zielsystem.
 *
 * @param mixed $id Eingabewert fuer id.
 * @param mixed $data Eingabewert fuer data.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `public destroy($id)`

- Datei: `src/Core/Session.php`
- Zeile: 158
- Rueckgabe: `bool`

```
/**
 * Fuehrt destroy entsprechend der internen Logik aus.
 *
 * @param mixed $id Eingabewert fuer id.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `public gc($max_lifetime)`

- Datei: `src/Core/Session.php`
- Zeile: 178
- Rueckgabe: `int|false`

```
/**
 * Fuehrt gc entsprechend der internen Logik aus.
 *
 * @param mixed $max_lifetime Eingabewert fuer max_lifetime.
 * @return int|false Rueckgabewert der Funktion.
 */
```

### `public static start($db, $lifetime)`

- Datei: `src/Core/Session.php`
- Zeile: 198
- Rueckgabe: `void`

```
/**
 * Initialisiert und startet die Session.
 */
```

### `public static regenerateId()`

- Datei: `src/Core/Session.php`
- Zeile: 230
- Rueckgabe: `void`

```
/**
 * Fuehrt regenerate id entsprechend der internen Logik aus.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `public static setVar($key, $value)`

- Datei: `src/Core/Session.php`
- Zeile: 240
- Rueckgabe: `void`

```
/**
 * Setzt eine Session-Variable.
 */
```

### `public static getVar($key, $default)`

- Datei: `src/Core/Session.php`
- Zeile: 248
- Rueckgabe: `mixed`

```
/**
 * Holt eine Session-Variable.
 */
```

### `public static removeVar($key)`

- Datei: `src/Core/Session.php`
- Zeile: 256
- Rueckgabe: `void`

```
/**
 * Entfernt eine Session-Variable.
 */
```

### `public static isUser()`

- Datei: `src/Core/Session.php`
- Zeile: 264
- Rueckgabe: `bool`

```
/**
 * Prüft, ob ein Benutzer angemeldet ist.
 */
```

### `public static isAdmin()`

- Datei: `src/Core/Session.php`
- Zeile: 272
- Rueckgabe: `bool`

```
/**
 * Prüft, ob aktueller Benutzer Admin ist.
 */
```

### `public static getUser()`

- Datei: `src/Core/Session.php`
- Zeile: 321
- Rueckgabe: `?array`

```
/**
 * Gibt den aktuellen Benutzer zurück.
 */
```

### `public static setUser($user)`

- Datei: `src/Core/Session.php`
- Zeile: 329
- Rueckgabe: `void`

```
/**
 * Setzt den angemeldeten Benutzer.
 */
```

### `public static destroyAll()`

- Datei: `src/Core/Session.php`
- Zeile: 337
- Rueckgabe: `void`

```
/**
 * Zerstört die komplette Session inkl. Cookie.
 */
```

### `public static getCsrfToken()`

- Datei: `src/Core/Session.php`
- Zeile: 357
- Rueckgabe: `string`

```
/**
 * CSRF token for state-changing requests.
 */
```

### `public static verifyCsrfToken($token)`

- Datei: `src/Core/Session.php`
- Zeile: 372
- Rueckgabe: `bool`

```
/**
 * Fuehrt verify csrf token entsprechend der internen Logik aus.
 *
 * @param mixed $token Eingabewert fuer token.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `private static toBool($value)`

- Datei: `src/Core/Session.php`
- Zeile: 392
- Rueckgabe: `bool`

```
/**
 * Fuehrt to bool entsprechend der internen Logik aus.
 *
 * @param mixed $value Eingabewert fuer value.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `private static isStrictAdminRoleName($roleName)`

- Datei: `src/Core/Session.php`
- Zeile: 414
- Rueckgabe: `bool`

```
/**
 * Prueft, ob strict admin role name zutrifft.
 *
 * @param mixed $roleName Eingabewert fuer roleName.
 * @return bool True bei Erfolg, sonst false.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/UserController.php`

### `public index()`

- Datei: `src/Core/UserController.php`
- Zeile: 34
- Rueckgabe: `void`

```
/**
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return void Kein Rueckgabewert.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Core/UserModel.php`

### `public __construct()`

- Datei: `src/Core/UserModel.php`
- Zeile: 32
- Rueckgabe: `mixed|null`

```
/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @return mixed|null Rueckgabewert der Funktion.
 */
```

### `public getAllUsers($limit, $offset, $search)`

- Datei: `src/Core/UserModel.php`
- Zeile: 45
- Rueckgabe: `array`

```
/**
 * Liest all users und gibt den Wert zurueck.
 *
 * @param mixed $limit Eingabewert fuer limit.
 * @param mixed $offset Eingabewert fuer offset.
 * @param mixed $search Eingabewert fuer search.
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `public countUsers($search)`

- Datei: `src/Core/UserModel.php`
- Zeile: 93
- Rueckgabe: `int`

```
/**
 * Fuehrt count users entsprechend der internen Logik aus.
 *
 * @param mixed $search Eingabewert fuer search.
 * @return int Rueckgabewert der Funktion.
 */
```

### `public countOnlineUsers()`

- Datei: `src/Core/UserModel.php`
- Zeile: 117
- Rueckgabe: `int`

```
/**
 * Fuehrt count online users entsprechend der internen Logik aus.
 *
 * @return int Rueckgabewert der Funktion.
 */
```

### `public userExists($username)`

- Datei: `src/Core/UserModel.php`
- Zeile: 129
- Rueckgabe: `bool`

```
/**
 * Fuehrt user exists entsprechend der internen Logik aus.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `public createUser($username, $password, $isAdmin)`

- Datei: `src/Core/UserModel.php`
- Zeile: 144
- Rueckgabe: `void`

```
/**
 * Erzeugt user auf Basis der Eingabedaten.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $password Eingabewert fuer password.
 * @param mixed $isAdmin Eingabewert fuer isAdmin.
 * @return void Kein Rueckgabewert.
 */
```

### `public setUserEnabled($username, $enabled)`

- Datei: `src/Core/UserModel.php`
- Zeile: 168
- Rueckgabe: `void`

```
/**
 * Setzt user enabled auf den uebergebenen Wert.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $enabled Eingabewert fuer enabled.
 * @return void Kein Rueckgabewert.
 */
```

### `public setUserRole($username, $isAdmin)`

- Datei: `src/Core/UserModel.php`
- Zeile: 184
- Rueckgabe: `void`

```
/**
 * Setzt user role auf den uebergebenen Wert.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $isAdmin Eingabewert fuer isAdmin.
 * @return void Kein Rueckgabewert.
 */
```

### `public setUserPasswordByName($username, $newPassword)`

- Datei: `src/Core/UserModel.php`
- Zeile: 201
- Rueckgabe: `void`

```
/**
 * Setzt user password by name auf den uebergebenen Wert.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $newPassword Eingabewert fuer newPassword.
 * @return void Kein Rueckgabewert.
 */
```

### `public setUserPasswordById($uid, $newPassword)`

- Datei: `src/Core/UserModel.php`
- Zeile: 218
- Rueckgabe: `void`

```
/**
 * Setzt user password by id auf den uebergebenen Wert.
 *
 * @param mixed $uid Eingabewert fuer uid.
 * @param mixed $newPassword Eingabewert fuer newPassword.
 * @return void Kein Rueckgabewert.
 */
```

### `public setUserLimits($username, $startDate, $endDate)`

- Datei: `src/Core/UserModel.php`
- Zeile: 236
- Rueckgabe: `void`

```
/**
 * Setzt user limits auf den uebergebenen Wert.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $startDate Eingabewert fuer startDate.
 * @param mixed $endDate Eingabewert fuer endDate.
 * @return void Kein Rueckgabewert.
 */
```

### `public setUserFixedIp($username, $fixedIp)`

- Datei: `src/Core/UserModel.php`
- Zeile: 260
- Rueckgabe: `void`

```
/**
 * Setzt user fixed ip auf den uebergebenen Wert.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $fixedIp Eingabewert fuer fixedIp.
 * @return void Kein Rueckgabewert.
 */
```

### `public isFixedIpInUseByOtherUser($username, $fixedIp)`

- Datei: `src/Core/UserModel.php`
- Zeile: 310
- Rueckgabe: `bool`

```
/**
 * Prueft, ob fixed ip in use by other user zutrifft.
 *
 * @param mixed $username Eingabewert fuer username.
 * @param mixed $fixedIp Eingabewert fuer fixedIp.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `public deleteUser($username)`

- Datei: `src/Core/UserModel.php`
- Zeile: 334
- Rueckgabe: `void`

```
/**
 * Entfernt user.
 *
 * @param mixed $username Eingabewert fuer username.
 * @return void Kein Rueckgabewert.
 */
```

### `public verifyPassword($uid, $password)`

- Datei: `src/Core/UserModel.php`
- Zeile: 347
- Rueckgabe: `bool`

```
/**
 * Fuehrt verify password entsprechend der internen Logik aus.
 *
 * @param mixed $uid Eingabewert fuer uid.
 * @param mixed $password Eingabewert fuer password.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `private resolveGroupId($isAdmin)`

- Datei: `src/Core/UserModel.php`
- Zeile: 365
- Rueckgabe: `int`

```
/**
 * Fuehrt resolve group id entsprechend der internen Logik aus.
 *
 * @param mixed $isAdmin Eingabewert fuer isAdmin.
 * @return int Rueckgabewert der Funktion.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Templates/Account.php`

### `public index()`

- Datei: `src/Templates/Account.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Templates/CaTls.php`

### `public index()`

- Datei: `src/Templates/CaTls.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Templates/Config.php`

### `public index()`

- Datei: `src/Templates/Config.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Templates/Dashboard.php`

### `public index()`

- Datei: `src/Templates/Dashboard.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Templates/Log.php`

### `public index()`

- Datei: `src/Templates/Log.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Templates/Profiles.php`

### `public index()`

- Datei: `src/Templates/Profiles.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Templates/Settings.php`

### `public index()`

- Datei: `src/Templates/Settings.php`
- Zeile: 29
- Rueckgabe: `string`

```
/**
 * Register VPN Einstellungen
 * Hier wird die Serverkonfiguration geladen, angezeigt und verändert
 *
 * @return string
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Templates/Template.php`

### `public __construct($title, $content, $data)`

- Datei: `src/Templates/Template.php`
- Zeile: 37
- Rueckgabe: `mixed|null`

```
/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $title Eingabewert fuer title.
 * @param mixed $content Eingabewert fuer content.
 * @param mixed $data Eingabewert fuer data.
 * @return mixed|null Rueckgabewert der Funktion.
 */
```

### `private loadPart($name, $vars)`

- Datei: `src/Templates/Template.php`
- Zeile: 47
- Rueckgabe: `string`

```
/**
 * Lädt eine Layout-Datei aus /src/Layout/
 */
```

### `public render()`

- Datei: `src/Templates/Template.php`
- Zeile: 65
- Rueckgabe: `string`

```
/**
 * Baut die komplette Seite zusammen
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `src/Templates/User.php`

### `public index()`

- Datei: `src/Templates/User.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

