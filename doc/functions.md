# Funktionsreferenz

Automatisch generiert am: 2026-11-18 07:11:20

## `./src/Core/ConfigService.php`

### `public __construct($basePath)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 32
- Rueckgabe: `mixed|null`

```
/**
 * Legt den Speicherort für die client.ovpn Dateien und dessen Historienverzeichnis fest.
 * Ohne expliziten Pfad wird `storage/conf` relativ zum Projektstamm verwendet.
 *
 * @param string|null $basePath Optionaler Speicherort der Systemkonfigurationen.
 */
```

### `public listSystems()`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 44
- Rueckgabe: `array`

```
/**
 * Ermittelt alphabetisch sortierte Systeme mit einer vorhandenen `client.ovpn`.
 * Ein fehlendes oder nicht lesbares Verzeichnis ergibt eine leere Liste.
 *
 * @return string[] Verzeichnisnamen der gefundenen Systeme.
 */
```

### `public getConfig($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 75
- Rueckgabe: `string`

```
/**
 * Liest die `client.ovpn` des validierten Systems vollständig als Text.
 *
 * @param string $system Systemverzeichnisname.
 * @return string Dateiinhalt der Client-Konfiguration.
 * @throws \RuntimeException Wenn der Systemname ungültig ist oder die Datei fehlt.
 */
```

### `public saveConfig($system, $content)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 93
- Rueckgabe: `array`

```
/**
 * Archiviert die bisherige Konfiguration (falls vorhanden) und ersetzt sie atomar.
 *
 * @param string $system Systemverzeichnisname.
 * @param string $content Vollständiger neuer Inhalt für `client.ovpn`.
 * @return array{history_file: string|null, history_saved: bool, path: string} Speicherpfad und Status des Snapshots.
 * @throws \RuntimeException Wenn das Systemverzeichnis fehlt oder das Schreiben fehlschlägt.
 */
```

### `public getHistoryList($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 142
- Rueckgabe: `array`

```
/**
 * Sammelt `.ovpn`-Snapshots aus den unterstützten Historienorten und sortiert sie absteigend.
 *
 * @param string $system Systemverzeichnisname.
 * @return array<int, array{file: string, mtime: string}> Snapshotname und Änderungszeit.
 * @throws \RuntimeException Bei ungültigem Systemnamen.
 */
```

### `public getDiffStatsAgainstCurrent($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 177
- Rueckgabe: `array`

```
/**
 * Ergänzt jeden Snapshot um die Zeilenzahlen seiner Abweichung zur aktuellen Konfiguration.
 *
 * @param string $system Systemverzeichnisname.
 * @return array<int, array{file: string, mtime: string, added: int, removed: int}> Snapshots mit Änderungszahlen.
 * @throws \RuntimeException Wenn aktuelle Konfiguration oder Systemname ungültig ist.
 */
```

### `public diffHistoryFile($system, $historyFile)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 200
- Rueckgabe: `array`

```
/**
 * Vergleicht einen Snapshot mit der aktuellen `client.ovpn`.
 *
 * @param string $system Systemverzeichnisname.
 * @param string $historyFile Snapshotdateiname; Verzeichnisteile werden verworfen.
 * @return array{added: int, removed: int, diff: string} Zeilenzahlen und zeilenweiser Unified-ähnlicher Vergleich.
 * @throws \RuntimeException Wenn Snapshot oder aktuelle Konfiguration fehlt.
 */
```

### `private getHistoryContent($system, $historyFile)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 216
- Rueckgabe: `string`

```
/**
 * Liest einen Snapshot aus einem der Historienorte des Systems.
 *
 * @param string $system Systemverzeichnisname.
 * @param string $historyFile Snapshotname; mit `basename()` auf einen Dateinamen begrenzt.
 * @return string Vollständiger Snapshotinhalt.
 * @throws \RuntimeException Wenn keine passende Datei gefunden wird.
 */
```

### `private buildDiffData($oldText, $newText)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 236
- Rueckgabe: `array`

```
/**
 * Berechnet Zähler und Textdarstellung eines zeilenweisen Vergleichs.
 *
 * @param string $oldText Vorheriger Inhalt.
 * @param string $newText Neuer Inhalt.
 * @return array{added: int, removed: int, diff: string} Vergleichstext mit `  `, `+ ` und `- ` je Zeile.
 */
```

### `private diffOps($a, $b)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 272
- Rueckgabe: `array`

```
/**
 * Erzeugt per Längentabelle der längsten gemeinsamen Teilfolge geordnete Diff-Operationen.
 *
 * @param string[] $a Alte Zeilen.
 * @param string[] $b Neue Zeilen.
 * @return array<int, array{type: '='|'+'|'-', line: string}> Gleiche, hinzugefügte oder entfernte Zeilen.
 */
```

### `private clientPath($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 326
- Rueckgabe: `string`

```
/**
 * Erstellt nach Validierung des Systemnamens den Pfad zu seiner Client-Konfiguration.
 *
 * @param string $system Systemverzeichnisname.
 * @return string Absoluter Pfad zu `client.ovpn`.
 * @throws \RuntimeException Bei ungültigem Namen.
 */
```

### `private historyDir($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 339
- Rueckgabe: `string`

```
/**
 * Wählt den ersten vorhandenen beschreibbaren oder anlegbaren Historienpfad.
 *
 * @param string $system Systemverzeichnisname.
 * @return string Bevorzugter Historienpfad.
 * @throws \RuntimeException Wenn kein Historienort beschreibbar ist.
 */
```

### `private historyDirectories($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 366
- Rueckgabe: `array`

```
/**
 * Liefert alle bekannten Speicherorte der System-Historie in Prioritaetsreihenfolge.
 *
 * @return string[]
 */
```

### `private sanitizeSystem($system)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 382
- Rueckgabe: `string`

```
/**
 * Entfernt Rand-Leerzeichen und weist Systemnamen außerhalb des erlaubten Zeichensatzes zurück.
 *
 * @param string $system Ungeprüfter Systemname.
 * @return string Bereinigter Systemname.
 * @throws \RuntimeException Bei leerem oder ungültigem Namen.
 */
```

### `private writeAtomic($path, $content)`

- Datei: `./src/Core/ConfigService.php`
- Zeile: 402
- Rueckgabe: `void`

```
/**
 * Schreibt atomar in die Zieldatei (tmp + rename).
 * Dadurch funktionieren Saves auch dann, wenn die bestehende Datei restriktive Rechte hat,
 * aber das Verzeichnis beschreibbar ist.
 *
 * @param string $path Zieldatei.
 * @param string $content Zu schreibender Inhalt.
 * @return void
 * @throws \RuntimeException Wenn Verzeichnis, temporäre Datei oder Ersetzen fehlschlägt.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/Database.php`

### `private __construct($config)`

- Datei: `./src/Core/Database.php`
- Zeile: 36
- Rueckgabe: `mixed|null`

```
/**
 * Baut eine PDO-Verbindung mit MySQL-DSN und Exceptions auf.
 * Bei Fehlern der Verbindung wird ein Fehler ausgegeben.
 *
 * @param array{host: string, port: int|string, dbname: string, charset: string, user: string, pass: string} $config Verbindungsparameter.
 * @throws \RuntimeException Es wird bei Verbindungsfehlern protokolliert, HTTP 500 gesetzt und das Skript mit `die` beendet.
 */
```

### `public static getInstance($config)`

- Datei: `./src/Core/Database.php`
- Zeile: 59
- Rueckgabe: `Database`

```
/**
 * Liefert die Verbindung und initialisiert sie beim ersten Aufruf.
 *
 * @param array|null $config Zugangsdaten; beim ersten Aufruf erforderlich, später ignoriert.
 * @return self Gemeinsame Database-Instanz.
 * @throws \RuntimeException Wenn die Erstinitialisierung ohne Konfiguration erfolgt.
 */
```

### `public getConnection()`

- Datei: `./src/Core/Database.php`
- Zeile: 75
- Rueckgabe: `PDO`

```
/**
 * Gibt die von PDO verwaltete Datenbankverbindung zurück.
 *
 * @return PDO PDO-Verbindung.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/DataController.php`

### `public handle()`

- Datei: `./src/Core/DataController.php`
- Zeile: 30
- Rueckgabe: `void`

```
/**
 * Verarbeitet die Anfrage entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleGet($select)`

- Datei: `./src/Core/DataController.php`
- Zeile: 56
- Rueckgabe: `void`

```
/**
 * Verteilt GET-Datenquellen, prüft je Quelle die Adminrolle und serialisiert Ergebnisse als JSON.
 *
 * @param string $select Angeforderte Datenquelle aus `select`.
 * @return void Kein Rueckgabewert.
 */
```

### `private handlePost($select)`

- Datei: `./src/Core/DataController.php`
- Zeile: 107
- Rueckgabe: `void`

```
/**
 * Verteilt schreibende API-Aktionen; Verwaltungsquellen verlangen Adminrechte.
 *
 * @param string $select Angeforderte Aktionsquelle aus `select`.
 * @return void Kein Rueckgabewert.
 */
```

### `private getUsers()`

- Datei: `./src/Core/DataController.php`
- Zeile: 135
- Rueckgabe: `array`

```
/**
 * Liest users und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private getLogs()`

- Datei: `./src/Core/DataController.php`
- Zeile: 154
- Rueckgabe: `array`

```
/**
 * Liest logs und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private getDashboardStats()`

- Datei: `./src/Core/DataController.php`
- Zeile: 172
- Rueckgabe: `array`

```
/**
 * Liest dashboard stats und gibt den Wert zurueck.
 *
 * @return array Rueckgabe als Array mit den ermittelten Daten.
 */
```

### `private handleUserAction()`

- Datei: `./src/Core/DataController.php`
- Zeile: 208
- Rueckgabe: `void`

```
/**
 * Verarbeitet user action entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleAccountAction()`

- Datei: `./src/Core/DataController.php`
- Zeile: 342
- Rueckgabe: `void`

```
/**
 * Verarbeitet account action entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleConfigGet()`

- Datei: `./src/Core/DataController.php`
- Zeile: 384
- Rueckgabe: `void`

```
/**
 * Verarbeitet config get entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleConfigPost()`

- Datei: `./src/Core/DataController.php`
- Zeile: 432
- Rueckgabe: `void`

```
/**
 * Verarbeitet config post entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleSettingsGet()`

- Datei: `./src/Core/DataController.php`
- Zeile: 484
- Rueckgabe: `void`

```
/**
 * Verarbeitet settings get entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private handleSettingsPost()`

- Datei: `./src/Core/DataController.php`
- Zeile: 526
- Rueckgabe: `void`

```
/**
 * Verarbeitet settings post entsprechend der Logik.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private requireAdminJson()`

- Datei: `./src/Core/DataController.php`
- Zeile: 565
- Rueckgabe: `void`

```
/**
 * Fuehrt require admin json entsprechend der internen Logik aus.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private msg($key)`

- Datei: `./src/Core/DataController.php`
- Zeile: 578
- Rueckgabe: `string`

```
/**
 * Übersetzt einen Sprachschlüssel über den aktuellen Sprachkatalog.
 *
 * @param string $key Schlüssel des Sprachkatalogs.
 * @return string Übersetzter Text oder das Verhalten von `Lang::get()` bei unbekanntem Schlüssel.
 */
```

### `private msgf($key, $arg)`

- Datei: `./src/Core/DataController.php`
- Zeile: 590
- Rueckgabe: `string`

```
/**
 * Übersetzt einen Sprachschlüssel und setzt genau ein `sprintf`-Argument ein.
 *
 * @param string $key Formatstring-Schlüssel.
 * @param string $arg Einzusetzender Formatwert.
 * @return string Formatierter Übersetzungstext.
 */
```

### `private assertValidUsername($username)`

- Datei: `./src/Core/DataController.php`
- Zeile: 602
- Rueckgabe: `void`

```
/**
 * Akzeptiert nichtleere Benutzernamen aus ASCII-Buchstaben, Ziffern, `_ . @ -` bis Länge 64.
 *
 * @param string $username Zu validierender Benutzername.
 * @return void Kein Rueckgabewert.
 * Ungültige Eingabe beendet die Anfrage mit API-Status 422.
 */
```

### `private isValidIpv4($value)`

- Datei: `./src/Core/DataController.php`
- Zeile: 615
- Rueckgabe: `bool`

```
/**
 * Prüft, ob der vollständige Wert eine gültige IPv4-Adresse ist.
 *
 * @param string $value Zu prüfender Text.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `private getLoginDiagnostics()`

- Datei: `./src/Core/DataController.php`
- Zeile: 625
- Rueckgabe: `array`

```
/**
 * Erstellt DB-/Tabellen- und Benutzerdiagnosen einschließlich Passwortformatprüfung; Passwörter selbst werden nicht zurückgegeben.
 *
 * @return array Struktur mit Status, Zeitstempel, Sessionbenutzer, Tabellenstatus, Benutzer-Hashformaten und optionalem `login_check`.
 */
```

### `private json($data, $status)`

- Datei: `./src/Core/DataController.php`
- Zeile: 739
- Rueckgabe: `void`

```
/**
 * Setzt Status und JSON-Header, kodiert Daten, schreibt sie und beendet den Prozess.
 *
 * @param array<string, mixed> $data Zu serialisierende Antwortdaten.
 * @param int $status HTTP-Statuscode.
 * @return void Kein Rueckgabewert.
 */
```

### `private internalError($context, $e)`

- Datei: `./src/Core/DataController.php`
- Zeile: 754
- Rueckgabe: `void`

```
/**
 * Protokolliert Exception und Kontext und antwortet mit generischem HTTP-500-JSON.
 *
 * @param string $context Fehlerkontext für das Protokoll.
 * @param \Throwable $e Auslösender Fehler.
 * @return void Kein Rueckgabewert.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/Debug.php`

### `public static init($env, $logfile, $debug)`

- Datei: `./src/Core/Debug.php`
- Zeile: 33
- Rueckgabe: `void`

```
/**
 * Initialisiert Debug-Umgebung
 */
```

### `public static getExceptionsLogPath()`

- Datei: `./src/Core/Debug.php`
- Zeile: 54
- Rueckgabe: `string`

```
Kein Docblock gefunden.
```

### `public static debug($vars)`

- Datei: `./src/Core/Debug.php`
- Zeile: 62
- Rueckgabe: `void`

```
/**
 * Debug-Ausgabe auf der Seite (Bootstrap Collapse)
 */
```

### `private static buildAutoContextForObject($object)`

- Datei: `./src/Core/Debug.php`
- Zeile: 125
- Rueckgabe: `array`

```
/**
 * Baut automatisch einen zweiten Debug-Kontext fuer Objekt-Calls auf.
 *
 * @param object $object
 * @return array<string,mixed>
 */
```

### `private static extractObjectProperties($object)`

- Datei: `./src/Core/Debug.php`
- Zeile: 143
- Rueckgabe: `array`

```
/**
 * Extrahiert alle Objekt-Properties inklusive private/protected per Reflection.
 *
 * @param object $object
 * @return array<string,mixed>
 */
```

### `public static log($vars)`

- Datei: `./src/Core/Debug.php`
- Zeile: 168
- Rueckgabe: `void`

```
/**
 * Schreibt fehlgeschlagene Loginversuche bei aktiviertem Debugging in die Debug-Logdatei.
 */
```

### `public static logException($e, $context)`

- Datei: `./src/Core/Debug.php`
- Zeile: 193
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

- Datei: `./src/Core/Debug.php`
- Zeile: 219
- Rueckgabe: `void`

```
/**
 * Debug + exit
 */
```

### `public static handleException($e)`

- Datei: `./src/Core/Debug.php`
- Zeile: 228
- Rueckgabe: `void`

```
/**
 * Exception-Handler
 */
```

### `public static handleError($errno, $errstr, $errfile, $errline)`

- Datei: `./src/Core/Debug.php`
- Zeile: 265
- Rueckgabe: `bool`

```
/**
 * Error-Handler
 */
```

### `public static render()`

- Datei: `./src/Core/Debug.php`
- Zeile: 293
- Rueckgabe: `void`

```
/**
 * Stellt Inhalte fuer die Ausgabe dar.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `public static getBufferedOutput()`

- Datei: `./src/Core/Debug.php`
- Zeile: 312
- Rueckgabe: `string`

```
/**
 * Liefert die gesammelte HTML-Debug-Ausgabe für das Debug-Modal.
 *
 * @return string
 */
```

### `private static renderExceptionScreen($error)`

- Datei: `./src/Core/Debug.php`
- Zeile: 333
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

- Datei: `./src/Core/Debug.php`
- Zeile: 351
- Rueckgabe: `void`

```
/**
 * Stellt sicher, dass die Logziele existieren und beschreibbar sind.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/GoRequest.php`

### `public __construct($action)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 46
- Rueckgabe: `mixed|null`

```
/**
 * Initialisiert den Request-Handler mit der gewuenschten Operation.
 *
 * @param mixed $action
 * @return mixed|null
 */
```

### `public main()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 59
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

- Datei: `./src/Core/GoRequest.php`
- Zeile: 144
- Rueckgabe: `void`

```
/**
 * Zeigt die Login-Seite ueber den LoginController an.
 *
 * @return void
 */
```

### `private checkLogin()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 154
- Rueckgabe: `void`

```
/**
 * Verarbeitet den Login-Versuch ueber den LoginController.
 *
 * @return void
 */
```

### `private baseTemplateData($activeOp)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 165
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

- Datei: `./src/Core/GoRequest.php`
- Zeile: 190
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

- Datei: `./src/Core/GoRequest.php`
- Zeile: 201
- Rueckgabe: `void`

```
/**
 * Zeigt die Dashboard-Seite an.
 *
 * @return void
 */
```

### `private showUsers()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 212
- Rueckgabe: `void`

```
/**
 * Zeigt die Benutzerverwaltung an.
 *
 * @return void
 */
```

### `private showLogs()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 223
- Rueckgabe: `void`

```
/**
 * Zeigt die Log-Ansicht an.
 *
 * @return void
 */
```

### `private showConfig()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 234
- Rueckgabe: `void`

```
/**
 * Zeigt den Konfigurations-Editor fuer Client-Profile an.
 *
 * @return void
 */
```

### `private showSettings()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 245
- Rueckgabe: `void`

```
/**
 * Zeigt den Editor fuer die VPN-Server-Einstellungen an.
 *
 * @return void
 */
```

### `private showDocumentation()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 256
- Rueckgabe: `void`

```
/**
 * Zeigt die durchsuchbare Funktionsdokumentation an.
 *
 * @return void
 */
```

### `private showProfiles()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 272
- Rueckgabe: `void`

```
/**
 * Zeigt die Seite fuer Konfigurations-Downloads an.
 *
 * @return void
 */
```

### `private showAccount()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 283
- Rueckgabe: `void`

```
/**
 * Zeigt die Seite zum Verwalten des eigenen Accounts an.
 *
 * @return void
 */
```

### `private showLive()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 294
- Rueckgabe: `void`

```
/**
 * Liefert eine einfache Live-Status-Antwort als JSON.
 *
 * @return void
 */
```

### `private logout()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 305
- Rueckgabe: `void`

```
/**
 * Meldet den Benutzer ab und antwortet je nach Request-Typ mit Redirect oder JSON.
 *
 * @return void
 */
```

### `private ensureAdminOrForbidden()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 332
- Rueckgabe: `void`

```
/**
 * Prueft Adminrechte und zeigt sonst eine Zugriff-verweigert-Seite an.
 *
 * @return void
 */
```

### `private downloadProfile()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 346
- Rueckgabe: `void`

```
/**
 * Erstellt und liefert die autorisierte Client-Konfiguration im Speicher aus.
 *
 * @return void
 */
```

### `private logProfileDownloadFailure($event, $context)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 421
- Rueckgabe: `void`

```
/**
 * Protokolliert nur nicht geheime Download-Metadaten fuer die Serverdiagnose.
 *
 * @param array<string, int|string|null> $context
 */
```

### `private denyDownload($status)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 427
- Rueckgabe: `never`

```
Kein Docblock gefunden.
```

### `private showError()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 446
- Rueckgabe: `void`

```
/**
 * Zeigt die Fehlerseite aus dem Theme oder einen Text-Fallback an.
 *
 * @return void
 */
```

### `private setLanguage()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 462
- Rueckgabe: `void`

```
/**
 * Setzt die gewaehlte Sprache und leitet sicher auf die vorherige Seite zurueck.
 *
 * @return void
 */
```

### `private showHelpDoc()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 484
- Rueckgabe: `void`

```
/**
```

### `private isSafeRedirectTarget($target)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 519
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

- Datei: `./src/Core/GoRequest.php`
- Zeile: 564
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

- Datei: `./src/Core/GoRequest.php`
- Zeile: 600
- Rueckgabe: `void`

```
/**
 * Liefert erlaubte Login-Assets sicher aus dem Theme-Verzeichnis aus.
 *
 * @return void
 */
```

### `private enforceDataAccessPolicy($method)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 660
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

- Datei: `./src/Core/GoRequest.php`
- Zeile: 678
- Rueckgabe: `void`

```
/**
 * Prueft zustandsaendernde Requests auf gleiche Herkunft und gueltiges CSRF-Token.
 *
 * @return void
 */
```

### `private isSameOriginRequest()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 695
- Rueckgabe: `bool`

```
/**
 * Ermittelt, ob die Anfrage von derselben Origin bzw. demselben Host stammt.
 *
 * @return bool
 */
```

### `private denyRequest($status, $message)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 724
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

- Datei: `./src/Core/GoRequest.php`
- Zeile: 744
- Rueckgabe: `string`

```
/**
 * Liest das CSRF-Token aus POST-Daten oder einem JSON-Body aus.
 *
 * @return string
 */
```

### `private canUseDebugModal()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 767
- Rueckgabe: `bool`

```
/**
 * Nur Admin mit gesetztem DEBUG=true darf das Debug-Modal sehen.
 */
```

### `private readDotEnvValue($key)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 787
- Rueckgabe: `string`

```
/**
 * Liest einen einzelnen Key aus der .env Datei (falls vorhanden).
 */
```

### `private buildDebugModalData()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 826
- Rueckgabe: `array`

```
/**
 * Baut die Inhalte für das Debug-Modal.
 *
 * @return array<string, mixed>
 */
```

### `private readDebugFile($path)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 848
- Rueckgabe: `string`

```
/**
 * Liest eine Logdatei robust ein und begrenzt sehr große Inhalte.
 */
```

### `private buildInternalUrl($url)`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 874
- Rueckgabe: `string`

```
/**
 * Baut interne Ziel-URLs aus Legacy-?op Links.
 *
 * @param string $url
 * @return string
 */
```

### `private getGoRequestDebugVars()`

- Datei: `./src/Core/GoRequest.php`
- Zeile: 884
- Rueckgabe: `array`

```
/**
 * Liefert die in GoRequest erzeugten/verwalteten Variablen fuer Debug.
 *
 * @return array<string, mixed>
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
 * Stellt login fuer die Ausgabe dar.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `public handleLogin()`

- Datei: `./src/Core/LoginController.php`
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

- Datei: `./src/Core/LoginController.php`
- Zeile: 160
- Rueckgabe: `void`

```
/**
 * Sendet einen Location-Header mit normalisierter interner URL und beendet die Anfrage.
 *
 * @param string $url Internes Redirectziel.
 * @return void Kein Rueckgabewert.
 */
```

### `private buildRedirectUrl($url)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 172
- Rueckgabe: `string`

```
/**
 * Normalisiert ein internes Ziel mittels `Url::normalizeInternal()`.
 *
 * @param string $url Legacy-Query, Pfad oder absolute URL.
 * @return string Normalisiertes Redirectziel.
 */
```

### `private clientIp()`

- Datei: `./src/Core/LoginController.php`
- Zeile: 182
- Rueckgabe: `string`

```
/**
 * Fuehrt client ip entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
```

### `private auditUserRef($username)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 194
- Rueckgabe: `string`

```
/**
 * Erzeugt für Logs eine nicht umkehrbare Referenz aus dem normalisierten Benutzernamen.
 *
 * @param string $username Zu anonymisierender Benutzername.
 * @return string `empty` oder SHA-256-Präfix samt Hash.
 */
```

### `private loginKey($username)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 209
- Rueckgabe: `string`

```
/**
 * Bildet den Rate-Limit-Schlüssel aus normalisiertem Benutzernamen und Client-IP.
 *
 * @param string $username Loginname.
 * @return string SHA-256-Schlüssel für den Session-Rate-Limit-Eintrag.
 */
```

### `private isLoginRateLimited($username)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 220
- Rueckgabe: `bool`

```
/**
 * Prüft, ob für Benutzername und Client-IP eine noch aktive Sperre in der Session besteht.
 *
 * @param string $username Loginname.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `private registerLoginFailure($username)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 243
- Rueckgabe: `void`

```
/**
 * Zählt einen Fehlversuch in der aktiven Session; ab fünf Versuchen innerhalb zehn Minuten sperrt es fünf Minuten und verzögert 300 ms.
 *
 * @param string $username Loginname für den Rate-Limit-Schlüssel.
 * @return void Kein Rueckgabewert.
 */
```

### `private clearLoginFailures($username)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 285
- Rueckgabe: `void`

```
/**
 * Entfernt den Rate-Limit-Eintrag dieses Benutzers und der Client-IP aus der aktiven Session.
 *
 * @param string $username Erfolgreich angemeldeter Benutzername.
 * @return void Kein Rueckgabewert.
 */
```

### `private isStrictAdminRoleName($roleName)`

- Datei: `./src/Core/LoginController.php`
- Zeile: 303
- Rueckgabe: `bool`

```
/**
 * Erkennt ausschließlich die erlaubten Admin-Rollennamen nach Trim und Kleinschreibung.
 *
 * @param string $roleName Rollenname aus der Datenbank.
 * @return bool True bei Erfolg, sonst false.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/LogModel.php`

### `public __construct()`

- Datei: `./src/Core/LogModel.php`
- Zeile: 31
- Rueckgabe: `mixed|null`

```
/**
 * Bezieht die gemeinsame PDO-Verbindung aus dem Database-Singleton.
 */
```

### `public getAllLogs($limit, $offset, $search)`

- Datei: `./src/Core/LogModel.php`
- Zeile: 44
- Rueckgabe: `array`

```
/**
 * Liest Logdatensätze absteigend nach Startzeit, optional nach Benutzer- oder IP-Feldern gefiltert.
 *
 * @param int $limit Maximale Zahl der Datensätze.
 * @param int $offset Zahl der zu überspringenden Datensätze.
 * @param string|null $search Teiltext für Benutzername, vertrauenswürdige IP oder Remote-IP; null/leer ohne Filter.
 * @return array<int, array<string, mixed>> Assoziative PDO-Zeilen aus der Tabelle `log`.
 */
```

### `public countLogs($search)`

- Datei: `./src/Core/LogModel.php`
- Zeile: 74
- Rueckgabe: `int`

```
/**
 * Zählt Logeinträge mit demselben optionalen Benutzer- und IP-Suchfilter wie `getAllLogs()`.
 *
 * @param string|null $search Teiltext für Benutzername, vertrauenswürdige IP oder Remote-IP.
 * @return int Zahl der passenden Logeinträge.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/ProfileService.php`

### `public __construct($basePath, $clientCertPath, $userModel)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 34
- Rueckgabe: `mixed|null`

```
/**
 * Setzt Vorlagen- und Zertifikatsspeicherpfade sowie optional das injizierte Benutzermodell.
 *
 * @param string|null $basePath Verzeichnis mit systembezogenen Client-Vorlagen.
 * @param string|null $clientCertPath Verzeichnis mit Zertifikatsdateien, standardmäßig aus `OPENVPN_CLIENT_CERT_DIR`.
 * @param UserModel|null $userModel Modell für Benutzer- und Rollenabfragen; null erzeugt es bei Bedarf.
 */
```

### `public listSystems()`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 47
- Rueckgabe: `array`

```
/**
 * Liefert nur die Betriebssysteme mit vorhandener Vorlage.
 *
 * @return array<int, array{system: string, label: string}>
 */
```

### `public createDownload($sessionUid, $requestedUid, $system)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 77
- Rueckgabe: `array`

```
/**
 * Prueft Session-Benutzer und Rolle, erstellt ein ZIP vollstaendig im Speicher.
 *
 * @return array{filename: string, content: string}
 */
```

### `private isVpnActive($user)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 154
- Rueckgabe: `bool`

```
/**
```

### `private isAdminRole($roleName)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 169
- Rueckgabe: `bool`

```
/**
```

### `private readTemplate($system)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 181
- Rueckgabe: `string`

```
/**
```

### `private readOptionalTemplateFile($system, $name)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 199
- Rueckgabe: `?string`

```
/**
```

### `private readClientFiles($uid)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 221
- Rueckgabe: `array`

```
/**
 * @return array{ca: string, cert: string, key: string, tls: string}
 */
```

### `private validateClientFiles($files, $uid)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 258
- Rueckgabe: `void`

```
/**
```

### `private normalizeTlsAuthKey($key)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 291
- Rueckgabe: `string`

```
/**
```

### `private buildClientConfig($template, $files)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 335
- Rueckgabe: `string`

```
/**
```

### `private createZipInMemory($files)`

- Datei: `./src/Core/ProfileService.php`
- Zeile: 369
- Rueckgabe: `string`

```
/**
 * Erstellt ein ZIP-Archiv ohne Datei- oder Temp-Verzeichniszugriffe.
 *
 * @param array<string, string> $files
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/ServerSettingsService.php`

### `public __construct($cfg)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 30
- Rueckgabe: `mixed|null`

```
/**
 * Liest VPN-Server-Quell-, Cache- und Historienpfade sowie HTTP-Optionen aus der Konfiguration.
 *
 * @param array|null $cfg Gesamtkonfiguration; null lädt `config/config.php`.
 */
```

### `public getCurrent()`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 51
- Rueckgabe: `array`

```
/**
 * Bezieht die Serverkonfiguration aus URL, Quelldatei oder lokalem Cache und ergänzt Historien-Diffstatistiken.
 *
 * @return array{content: string, source: string, save_path: string, history: array} Inhalt, Herkunftshinweis, Zielpfad und Snapshots samt Änderungszählern.
 * @throws \RuntimeException Wenn die Quelle und gegebenenfalls der lokale Fallback nicht lesbar sind.
 */
```

### `public save($content)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 91
- Rueckgabe: `array`

```
/**
 * Archiviert vorhandene Zieldatei und schreibt anschließend den neuen Serverkonfigurationstext.
 *
 * @param string $content Vollständiger neuer Konfigurationsinhalt.
 * @return array{path: string} Pfad der gespeicherten Datei.
 * @throws \RuntimeException Bei leerem Zielpfad, Verzeichnis-, Archiv- oder Schreibfehler.
 */
```

### `public getHistoryList()`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 121
- Rueckgabe: `array`

```
/**
 * Listet `.conf`-Snapshots im konfigurierten Historienverzeichnis absteigend nach Dateiname.
 *
 * @return array<int, array{file: string, mtime: string}> Dateiname und formatierte Änderungszeit; bei fehlendem Ordner leer.
 */
```

### `public diffHistoryFileAgainstCurrent($historyFile, $current)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 153
- Rueckgabe: `array`

```
/**
 * Vergleicht den per Basisdateiname ausgewählten Snapshot mit dem übergebenen aktuellen Inhalt.
 *
 * @param string $historyFile Snapshotname; Pfadanteile werden durch `basename()` entfernt.
 * @param string $current Aktueller Konfigurationstext.
 * @return array{added: int, removed: int, diff: string} Zeilenänderungen und formatierter Diff.
 * @throws \RuntimeException Wenn der Snapshot fehlt.
 */
```

### `private getDiffStatsAgainstCurrent($current)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 171
- Rueckgabe: `array`

```
/**
 * Ergänzt die Historienliste um Anzahl hinzugefügter und entfernter Zeilen gegenüber dem Inhalt.
 *
 * @param string $current Aktueller Serverkonfigurationstext.
 * @return array<int, array{file: string, mtime: string, added: int, removed: int}> Historienzeilen mit Diff-Zählern.
 */
```

### `private fetchFromUrl($url)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 193
- Rueckgabe: `string`

```
/**
 * Ruft eine HTTP(S)-Quelle mit GET, konfiguriertem Timeout und optionalem Authorization-Header ab.
 *
 * @param string $url Abrufadresse; andere Schemes als HTTP und HTTPS werden abgewiesen.
 * @return string Antwortinhalt.
 * @throws \RuntimeException Bei ungültigem Scheme oder fehlgeschlagenem Abruf.
 */
```

### `private writeCache($content)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 226
- Rueckgabe: `void`

```
/**
 * Schreibt den erfolgreichen URL-Inhalt bestmöglich in den konfigurierten Cache; Fehler werden ignoriert.
 *
 * @param string $content Abzurufender Konfigurationsinhalt.
 * @return void Kein Rueckgabewert.
 */
```

### `private archiveFile($file)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 248
- Rueckgabe: `void`

```
/**
 * Kopiert eine bestehende Datei unter Zeitstempel in den Historienordner.
 *
 * @param string $file Zu archivierende Quelldatei.
 * @return void Kein Rueckgabewert.
 * @throws \RuntimeException Wenn Historienordner oder Kopie nicht erstellt werden kann.
 */
```

### `private readSavePath()`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 266
- Rueckgabe: `string`

```
/**
 * Laedt save path aus der Quelle.
 *
 * @return string Rueckgabe als Text.
 */
```

### `private readFile($path)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 290
- Rueckgabe: `string`

```
/**
 * Liest eine vorhandene Datei vollständig als Text.
 *
 * @param string $path Quelldateipfad.
 * @return string Dateiinhalt.
 * @throws \RuntimeException Wenn die Datei fehlt.
 */
```

### `private ensureSaveFileExists()`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 304
- Rueckgabe: `void`

```
/**
 * Legt den Save-Ordner und bei Bedarf eine minimale Serverkonfiguration an; Anlagefehler werden ignoriert.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `private buildDiffData($oldText, $newText)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 329
- Rueckgabe: `array`

```
/**
 * Berechnet Anzahl und Textdarstellung gleicher, hinzugefügter und entfernter Zeilen.
 *
 * @param string $oldText Vorheriger Konfigurationstext.
 * @param string $newText Neuer Konfigurationstext.
 * @return array{added: int, removed: int, diff: string} Zähler und mit `  `, `+ `, `- ` markierte Zeilen.
 */
```

### `private diffOps($a, $b)`

- Datei: `./src/Core/ServerSettingsService.php`
- Zeile: 365
- Rueckgabe: `array`

```
/**
 * Erzeugt anhand der längsten gemeinsamen Teilfolge eine geordnete Liste der Zeilenoperationen.
 *
 * @param string[] $a Alte Zeilen.
 * @param string[] $b Neue Zeilen.
 * @return array<int, array{type: '='|'+'|'-', line: string}> Operationen mit Zeilentyp und Text.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/Session.php`

### `public __construct($db, $lifetime)`

- Datei: `./src/Core/Session.php`
- Zeile: 47
- Rueckgabe: `mixed|null`

```
/**
 * Registriert diese Instanz als PHP-Sessionhandler und setzt sichere Cookie-Optionen.
 *
 * @param PDO $db Datenbankverbindung für `sessions2`.
 * @param int $lifetime Cookie- und Session-Laufzeit in Sekunden.
 */
```

### `public open($savePath, $sessionName)`

- Datei: `./src/Core/Session.php`
- Zeile: 76
- Rueckgabe: `bool`

```
/**
 * Akzeptiert das Öffnen des Session-Speichers; PDO benötigt kein separates Öffnen.
 *
 * @param string $savePath Von PHP übergebener Speicherpfad (wird nicht verwendet).
 * @param string $sessionName Von PHP übergebener Sitzungsname (wird nicht verwendet).
 * @return bool True bei Erfolg, sonst false.
 */
```

### `public close()`

- Datei: `./src/Core/Session.php`
- Zeile: 86
- Rueckgabe: `bool`

```
/**
 * Akzeptiert das Schließen des Session-Speichers; es sind keine Ressourcen freizugeben.
 *
 * @return bool True bei Erfolg, sonst false.
 */
```

### `public read($id)`

- Datei: `./src/Core/Session.php`
- Zeile: 97
- Rueckgabe: `string|false`

```
/**
 * Liest nicht abgelaufene Sitzungsdaten aus `sessions2`; Datenbankfehler werden protokolliert und als leere Session behandelt.
 *
 * @param string $id PHP-Session-ID als Datenbankschlüssel.
 * @return string|false Rueckgabewert der Funktion.
 */
```

### `public write($id, $data)`

- Datei: `./src/Core/Session.php`
- Zeile: 125
- Rueckgabe: `bool`

```
/**
 * Ersetzt den Datensatz der Session mit Ablaufzeit, Zeitstempeln und serialisierten Daten.
 *
 * @param string $id PHP-Session-ID.
 * @param string $data Von PHP serialisierte Sessiondaten.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `public destroy($id)`

- Datei: `./src/Core/Session.php`
- Zeile: 157
- Rueckgabe: `bool`

```
/**
 * Löscht den Datensatz der angegebenen Session; bei Datenbankfehler wird protokolliert und Erfolg signalisiert.
 *
 * @param string $id Zu löschende PHP-Session-ID.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `public gc($max_lifetime)`

- Datei: `./src/Core/Session.php`
- Zeile: 177
- Rueckgabe: `int|false`

```
/**
 * Entfernt alle abgelaufenen Sitzungsdatensätze; bei Fehler wird protokolliert und 0 zurückgegeben.
 *
 * @param int $max_lifetime PHP-Parameter der GC-Schnittstelle; die Bereinigung nutzt stattdessen `expiry < NOW()`.
 * @return int|false Rueckgabewert der Funktion.
 */
```

### `public static start($db, $lifetime)`

- Datei: `./src/Core/Session.php`
- Zeile: 201
- Rueckgabe: `void`

```
/**
 * Startet bei Bedarf die native PHP-Session mit striktem Modus und sicheren Cookie-Optionen.
 * Der übergebene PDO-Handler wird derzeit nicht aktiviert; Fehler beim Start werden von PHP behandelt.
 *
 * @param PDO $db Datenbankverbindung (aktuell ungenutzt, für den optionalen PDO-Handler vorgesehen).
 * @param int $lifetime Cookie-Laufzeit in Sekunden.
 */
```

### `public static regenerateId()`

- Datei: `./src/Core/Session.php`
- Zeile: 233
- Rueckgabe: `void`

```
/**
 * Fuehrt regenerate id entsprechend der internen Logik aus.
 *
 * @return void Kein Rueckgabewert.
 */
```

### `public static setVar($key, $value)`

- Datei: `./src/Core/Session.php`
- Zeile: 243
- Rueckgabe: `void`

```
/**
 * Setzt eine Session-Variable.
 */
```

### `public static getVar($key, $default)`

- Datei: `./src/Core/Session.php`
- Zeile: 251
- Rueckgabe: `mixed`

```
/**
 * Holt eine Session-Variable.
 */
```

### `public static removeVar($key)`

- Datei: `./src/Core/Session.php`
- Zeile: 259
- Rueckgabe: `void`

```
/**
 * Entfernt eine Session-Variable.
 */
```

### `public static isUser()`

- Datei: `./src/Core/Session.php`
- Zeile: 267
- Rueckgabe: `bool`

```
/**
 * Prüft, ob ein Benutzer angemeldet ist.
 */
```

### `public static isAdmin()`

- Datei: `./src/Core/Session.php`
- Zeile: 275
- Rueckgabe: `bool`

```
/**
 * Prüft, ob aktueller Benutzer Admin ist.
 */
```

### `public static getUser()`

- Datei: `./src/Core/Session.php`
- Zeile: 324
- Rueckgabe: `?array`

```
/**
 * Gibt den aktuellen Benutzer zurück.
 */
```

### `public static setUser($user)`

- Datei: `./src/Core/Session.php`
- Zeile: 332
- Rueckgabe: `void`

```
/**
 * Setzt den angemeldeten Benutzer.
 */
```

### `public static destroyAll()`

- Datei: `./src/Core/Session.php`
- Zeile: 340
- Rueckgabe: `void`

```
/**
 * Zerstört die komplette Session inkl. Cookie.
 */
```

### `public static getCsrfToken()`

- Datei: `./src/Core/Session.php`
- Zeile: 360
- Rueckgabe: `string`

```
/**
 * CSRF token for state-changing requests.
 */
```

### `public static verifyCsrfToken($token)`

- Datei: `./src/Core/Session.php`
- Zeile: 375
- Rueckgabe: `bool`

```
/**
 * Vergleicht ein übermitteltes CSRF-Token konstantzeitlich mit dem Session-Token.
 *
 * @param string|null $token Zu prüfendes Token.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `private static toBool($value)`

- Datei: `./src/Core/Session.php`
- Zeile: 395
- Rueckgabe: `bool`

```
/**
 * Erkennt boolesche Werte sowie `1`, `true`, `yes` und `on` (ohne Beachtung der Großschreibung).
 *
 * @param mixed $value Zu interpretierender Sessionwert.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `private static isStrictAdminRoleName($roleName)`

- Datei: `./src/Core/Session.php`
- Zeile: 417
- Rueckgabe: `bool`

```
/**
 * Erkennt ausschließlich die explizit aufgelisteten Admin-Rollennamen nach Trim und Kleinschreibung.
 *
 * @param string $roleName Rollenbezeichnung aus der Datenbank.
 * @return bool True bei Erfolg, sonst false.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/Url.php`

### `private static basePath()`

- Datei: `./src/Core/Url.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
 * Ermittelt den normalisierten URL-Präfix der Anwendung aus `_APP_BASE_PATH`.
 *
 * @return string Leerstring für nicht gesetzten/root-Präfix, sonst führender Slash ohne Abschlussslash.
 */
```

### `private static appPath($path)`

- Datei: `./src/Core/Url.php`
- Zeile: 46
- Rueckgabe: `string`

```
/**
 * Ergänzt einen relativen oder absoluten Pfad um den konfigurierten Anwendungspräfix.
 *
 * @param string $path Pfad, dessen führende Slashes entfernt und durch genau einen ersetzt werden.
 * @return string Zusammengesetzter interner Anwendungspfad.
 */
```

### `public static op($op, $params)`

- Datei: `./src/Core/Url.php`
- Zeile: 60
- Rueckgabe: `string`

```
/**
 * Baut eine interne URL für eine `op`-Route, je nach Konfiguration als Rewrite- oder Query-URL.
 *
 * @param string $op Routenkennung; leer wird zu `main`.
 * @param array<string,mixed> $params Query-Parameter; im Rewrite-Modus wird `lang` bei `setlang` Teil des Pfads.
 * @return string URL inklusive App-Präfix und gegebenenfalls kodierter Query.
 */
```

### `public static normalizeInternal($url)`

- Datei: `./src/Core/Url.php`
- Zeile: 90
- Rueckgabe: `string`

```
/**
 * Normalisiert einen internen URL-Wert und erhält absolute URLs unverändert.
 *
 * @param string $url Absolute URL, `?op=`-Query, Querystring oder Pfad.
 * @return string Absolute URLs unverändert; interne Werte erhalten den App-Präfix und `?op=` wird über `op()` gebaut.
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
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return void Kein Rueckgabewert.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Core/UserModel.php`

### `public __construct()`

- Datei: `./src/Core/UserModel.php`
- Zeile: 30
- Rueckgabe: `mixed|null`

```
/**
 * Bezieht die PDO-Verbindung aus dem Datenbank-Singleton.
 */
```

### `public getAllUsers($limit, $offset, $search)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 43
- Rueckgabe: `array`

```
/**
 * Liest Benutzer inklusive Gruppenname und erster fester IP, optional nach Benutzer- oder Gruppenname gefiltert.
 *
 * @param int $limit Maximale Zeilenzahl.
 * @param int $offset Anzahl übersprungener Zeilen.
 * @param string $search Teilstring für Benutzer- und Gruppenname; leer deaktiviert die Suche.
 * @return array<int, array<string, mixed>> Assoziative PDO-Zeilen mit `uid`, `uname`, `gname`, Status/Fristen und `fixed_ip`.
 */
```

### `public countUsers($search)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 95
- Rueckgabe: `int`

```
/**
 * Zählt Benutzer passend zum optionalen Suchtext in Benutzer- oder Gruppenname.
 *
 * @param string $search Teilstring; leer zählt alle Benutzer.
 * @return int Anzahl passender Datensätze.
 */
```

### `public getVpnProfileUsers($limit, $offset, $search)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 121
- Rueckgabe: `array`

```
/**
 * Liefert aktive, fuer VPN-Zugaenge vorgesehene Benutzer mit serverseitiger Suche.
 *
 * @return array{total: int, rows: array<int, array<string, mixed>>}
 */
```

### `public getVpnProfileUser($uid)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 162
- Rueckgabe: `?array`

```
/**
 * Liest den Benutzerstatus und die autoritative Rolle fuer den Download.
 *
 * @return array<string, mixed>|null
 */
```

### `public countOnlineUsers()`

- Datei: `./src/Core/UserModel.php`
- Zeile: 183
- Rueckgabe: `int`

```
/**
 * Zählt Datensätze mit `user_online = 1`.
 *
 * @return int Zahl der als online markierten Benutzer.
 */
```

### `public userExists($username)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 195
- Rueckgabe: `bool`

```
/**
 * Prüft per vorbereiteter Abfrage, ob ein Benutzername vorhanden ist.
 *
 * @param string $username Exakter Benutzername.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `public createUser($username, $password, $isAdmin)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 211
- Rueckgabe: `void`

```
/**
 * Fügt einen aktivierten Benutzer mit gehashtem Passwort und anhand der Gruppen konfigurierter Rolle ein.
 *
 * @param string $username Benutzername.
 * @param string $password Klartextpasswort, das vor Speicherung mit `password_hash()` verarbeitet wird.
 * @param bool $isAdmin Wählt die ermittelte Admin- statt Benutzergruppe.
 * @return void Kein Rueckgabewert.
 * @throws \PDOException Bei Datenbankfehlern; Exceptions der Gruppenabfrage werden ebenfalls weitergegeben.
 */
```

### `public setUserEnabled($username, $enabled)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 235
- Rueckgabe: `void`

```
/**
 * Aktualisiert das Aktiv-Flag des Benutzers.
 *
 * @param string $username Exakter Benutzername.
 * @param bool $enabled Aktiviert bei true, deaktiviert bei false.
 * @return void Kein Rueckgabewert.
 */
```

### `public setUserRole($username, $isAdmin)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 251
- Rueckgabe: `void`

```
/**
 * Ermittelt die passende Gruppen-ID und weist sie dem Benutzer zu.
 *
 * @param string $username Exakter Benutzername.
 * @param bool $isAdmin Wählt Admin- oder Nicht-Admin-Gruppe.
 * @return void Kein Rueckgabewert.
 */
```

### `public setUserPasswordByName($username, $newPassword)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 268
- Rueckgabe: `void`

```
/**
 * Hasht das neue Passwort und aktualisiert es anhand des Benutzernamens.
 *
 * @param string $username Exakter Benutzername.
 * @param string $newPassword Neues Klartextpasswort.
 * @return void Kein Rueckgabewert.
 */
```

### `public setUserPasswordById($uid, $newPassword)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 285
- Rueckgabe: `void`

```
/**
 * Hasht das neue Passwort und aktualisiert es anhand der Benutzer-ID.
 *
 * @param int $uid Benutzer-ID.
 * @param string $newPassword Neues Klartextpasswort.
 * @return void Kein Rueckgabewert.
 */
```

### `public setUserLimits($username, $startDate, $endDate)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 303
- Rueckgabe: `void`

```
/**
 * Aktualisiert Start- und Enddatum; leere Strings werden als SQL-NULL gespeichert.
 *
 * @param string $username Exakter Benutzername.
 * @param string|null $startDate Einschließlich geltendes Startdatum oder null/leer für unbegrenzt.
 * @param string|null $endDate Einschließlich geltendes Enddatum oder null/leer für unbegrenzt.
 * @return void Kein Rueckgabewert.
 */
```

### `public setUserFixedIp($username, $fixedIp)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 327
- Rueckgabe: `void`

```
/**
 * Ersetzt die IP-Zuordnungen des Benutzers durch eine feste IP oder löscht sie bei leerem Wert.
 *
 * @param string $username Exakter Benutzername; bei nicht gefundener UID erfolgt keine Änderung.
 * @param string|null $fixedIp Neue IP oder null/leer zum Entfernen der Zuordnung.
 * @return void Kein Rueckgabewert.
 */
```

### `public isFixedIpInUseByOtherUser($username, $fixedIp)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 377
- Rueckgabe: `bool`

```
/**
 * Prüft, ob eine feste IP bereits einem anderen Benutzer zugeordnet ist.
 *
 * @param string $username Auszuschließender Benutzername.
 * @param string $fixedIp Zu prüfende IP-Adresse.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `public deleteUser($username)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 401
- Rueckgabe: `void`

```
/**
 * Löscht den Benutzer anhand des Namens; abhängige Datensätze werden nicht explizit behandelt.
 *
 * @param string $username Exakter Benutzername.
 * @return void Kein Rueckgabewert.
 */
```

### `public verifyPassword($uid, $password)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 414
- Rueckgabe: `bool`

```
/**
 * Lädt den Passwort-Hash zur UID und verifiziert ihn mit `password_verify()`.
 *
 * @param int $uid Benutzer-ID.
 * @param string $password Zu prüfendes Klartextpasswort.
 * @return bool True bei Erfolg, sonst false.
 */
```

### `private resolveGroupId($isAdmin)`

- Datei: `./src/Core/UserModel.php`
- Zeile: 432
- Rueckgabe: `int`

```
/**
 * Sucht Admin- und Nicht-Admin-Gruppe anhand ihrer Namen und verwendet Fallback-IDs, wenn Gruppen fehlen.
 *
 * @param bool $isAdmin Ob die Admin-Gruppe ausgewählt werden soll.
 * @return int Gruppen-ID; bei leerer Gruppentabelle 1 für Admin oder 2 für Benutzer.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Templates/Account.php`

### `public index()`

- Datei: `./src/Templates/Account.php`
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

## `./src/Templates/Config.php`

### `public index()`

- Datei: `./src/Templates/Config.php`
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

## `./src/Templates/Dashboard.php`

### `public index()`

- Datei: `./src/Templates/Dashboard.php`
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

## `./src/Templates/Documentation.php`

### `public index()`

- Datei: `./src/Templates/Documentation.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
 * Rendert die Funktionsdokumentation aus doc/functions.md als durchsuchbare Akkordeon-Ansicht.
 *
 * @return string
 */
```

### `private parseFunctionsReference()`

- Datei: `./src/Templates/Documentation.php`
- Zeile: 140
- Rueckgabe: `array`

```
/**
 * Parsed doc/functions.md into file -> function entries.
 *
 * @return array{error:string,files:array<string,array<int,array<string,string>>>}
 */
```

### `private joinHtml($parts)`

- Datei: `./src/Templates/Documentation.php`
- Zeile: 232
- Rueckgabe: `string`

```
/**
 * Joins HTML fragments without additional separators.
 *
 * @param array<int,string> $parts
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
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Templates/Profiles.php`

### `public index($systems, $isAdmin)`

- Datei: `./src/Templates/Profiles.php`
- Zeile: 28
- Rueckgabe: `string`

```
/**
 * Zeigt Betriebssystem-Downloads und fuer Admins die Benutzerauswahl.
 *
 * @param array<int, array{system: string, label: string}> $systems
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Templates/Settings.php`

### `public index()`

- Datei: `./src/Templates/Settings.php`
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

## `./src/Templates/Template.php`

### `public __construct($title, $content, $data)`

- Datei: `./src/Templates/Template.php`
- Zeile: 36
- Rueckgabe: `mixed|null`

```
/**
 * Speichert Seitentitel, Hauptinhalt und Variablen für Layout-Teile.
 *
 * @param string $title Dokumenttitel.
 * @param string $content Bereits erzeugter HTML-Hauptinhalt.
 * @param array $data Werte, die an Layout-Teile übergeben werden.
 */
```

### `private loadPart($name, $vars)`

- Datei: `./src/Templates/Template.php`
- Zeile: 51
- Rueckgabe: `string`

```
/**
 * Bindet ein Layout-Snippet mit extrahierten Variablen ein und fängt dessen Ausgabe ab.
 * Geheimhaltungsfelder aus Debug-Daten werden nur aus der lokalen Debugkopie entfernt.
 *
 * @param string $name Dateiname des Layout-Teils ohne `.php`.
 * @param array $vars Variablen für das inkludierte Template.
 * @return string HTML-Ausgabe oder Kommentar, wenn die Layoutdatei fehlt.
 */
```

### `public render()`

- Datei: `./src/Templates/Template.php`
- Zeile: 79
- Rueckgabe: `string`

```
/**
 * Rendert Layout-Kopf, Navigation, Inhalt, Fußbereich und Skripte und wendet optional URL-Rewriting an.
 * Fehlende Teiltemplates werden von `loadPart()` als HTML-Kommentar ausgegeben.
 *
 * @return string Zusammengesetztes HTML-Dokument.
 */
```

### `private rewriteOpUrls($html)`

- Datei: `./src/Templates/Template.php`
- Zeile: 123
- Rueckgabe: `string`

```
/**
 * Schreibt ?op=... Links in "sprechende" Pfade um.
 *
 * @param string $html
 * @return string
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
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Util/HelpDoc.php`

### `public static icon($ref)`

- Datei: `./src/Util/HelpDoc.php`
- Zeile: 37
- Rueckgabe: `string`

```
/**
 * Rendert ein escaped 🛟-Modal-Element aus einem area/doc-Verweis oder einen Leerstring.
 * @param array{area: string, doc: string}|null $ref
 * @return string
 */
```

### `public static iconFor($key)`

- Datei: `./src/Util/HelpDoc.php`
- Zeile: 57
- Rueckgabe: `string`

```
/**
 * Ermittelt den Dokumentverweis eines Sprachschlüssels und rendert dessen Hilfesymbol.
 * @param string $key
 * @return string
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Util/HelpDocService.php`

### `public load($area, $doc)`

- Datei: `./src/Util/HelpDocService.php`
- Zeile: 54
- Rueckgabe: `?array`

```
/**
 * Dokument laden; Fallback auf de_DE, wenn es in der aktiven Sprache fehlt.
 * @return array{area: string, doc: string, lang: string, markdown: string}|null
 * null, wenn weder die Übersetzung noch die deutsche Datei existiert
 * oder die Angaben ungültig sind
 * @param string $area
 * @param string $doc
 */
```

### `public resolveImage($lang, $area, $file)`

- Datei: `./src/Util/HelpDocService.php`
- Zeile: 87
- Rueckgabe: `?array`

```
/**
 * Bild relativ zum Verzeichnis eines Dokuments auflösen.
 * @return array{path: string, mime: string}|null
 * @param string $lang
 * @param string $area
 * @param string $file
 */
```

### `private docPath($lang, $area, $doc)`

- Datei: `./src/Util/HelpDocService.php`
- Zeile: 122
- Rueckgabe: `?string`

```
/**
 * Pfad zu einem Dokument, wenn es existiert und innerhalb von doc/ liegt.
 * @param string $lang
 * @param string $area
 * @param string $doc
 * @return ?string
 */
```

### `private containedPath($path)`

- Datei: `./src/Util/HelpDocService.php`
- Zeile: 138
- Rueckgabe: `?string`

```
/**
 * Auflösen und prüfen, dass der Pfad innerhalb von doc/ bleibt.
 * @param string $path
 * @return ?string
 */
```

### `private isValidName($name)`

- Datei: `./src/Util/HelpDocService.php`
- Zeile: 155
- Rueckgabe: `bool`

```
/**
 * Beschränkt Dokument- und Bereichsnamen auf sichere ASCII-Zeichen ohne Pfadtrenner.
 * @param string $name
 * @return bool
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Util/Lang.php`

### `public static init($lang)`

- Datei: `./src/Util/Lang.php`
- Zeile: 50
- Rueckgabe: `void`

```
/**
 * Initialisiert die Sprache (Session muss vorher gestartet sein).
 * @param ?string $lang
 * @return void
 */
```

### `public static get($key, $default)`

- Datei: `./src/Util/Lang.php`
- Zeile: 66
- Rueckgabe: `string`

```
/**
 * Einzeltext holen.
 * Ein eventuell vorhandener [HELP_DOC/...]-Marker wird aus dem sichtbaren
 * Text entfernt; die Referenz selbst liefert Lang::getDocRef().
 * @param string $key
 * @param string $default
 * @return string
 */
```

### `public static getDocRef($key)`

- Datei: `./src/Util/Lang.php`
- Zeile: 80
- Rueckgabe: `?array`

```
/**
 * Dokumentationsverweis eines Spracheintrags ([HELP_DOC/<bereich>/:<dokument>]).
 * @return array{area: string, doc: string}|null null, wenn kein gültiger Marker vorhanden ist
 * @param string $key
 */
```

### `public static getDocRefMap()`

- Datei: `./src/Util/Lang.php`
- Zeile: 103
- Rueckgabe: `array`

```
/**
 * Alle Dokumentationsverweise der aktiven Sprache als Liste
 * (sichtbarer Text → Bereich/Dokument). Dient der clientseitigen
 * Anbindung des 🛟-Symbols an jede Stelle, an der ein markierter
 * Sprachtext ausgegeben wird.
 * @return array<int, array{text: string, area: string, doc: string}>
 */
```

### `public static getAll()`

- Datei: `./src/Util/Lang.php`
- Zeile: 129
- Rueckgabe: `array`

```
/**
 * Komplettes Message-Array.
 * @return array
 */
```

### `public static getCurrent()`

- Datei: `./src/Util/Lang.php`
- Zeile: 145
- Rueckgabe: `string`

```
/**
 * Aktuelle Sprache.
 * @return string
 */
```

### `public static setCurrent($lang)`

- Datei: `./src/Util/Lang.php`
- Zeile: 157
- Rueckgabe: `void`

```
/**
 * Sprache setzen, in Session und Cookie speichern und neu laden.
 * @param string $lang
 * @return void
 */
```

### `public static getAvailableLanguages()`

- Datei: `./src/Util/Lang.php`
- Zeile: 167
- Rueckgabe: `array`

```
/**
 * Alle verfügbaren Sprachcodes anhand der Ordner Lang/<locale>/lang.php.
 * @return array
 */
```

### `public static getLanguageMeta($code)`

- Datei: `./src/Util/Lang.php`
- Zeile: 195
- Rueckgabe: `array`

```
/**
 * Liefert Label + Flagge für die Sprachauswahl.
 * Die Flagge ist ein SVG aus public/img/flags/ (Dateiname = Ländercode
 * klein, z. B. de.svg); ohne passende Datei ein generisches Icon.
 * @param string $code
 * @return array
 */
```

### `private static ensureInitialized()`

- Datei: `./src/Util/Lang.php`
- Zeile: 214
- Rueckgabe: `void`

```
/**
 * Lädt die Sprachwahl einmalig, wenn ein Aufrufer vor init() erstmals einen Text abfragt.
 * @return void
 */
```

### `private static stripHelpDocMarkers($text)`

- Datei: `./src/Util/Lang.php`
- Zeile: 226
- Rueckgabe: `string`

```
/**
 * Entfernt gültige [HELP_DOC/...]-Marker aus einem sichtbaren Text.
 * @param string $text
 * @return string
 */
```

### `private static loadMessages($lang)`

- Datei: `./src/Util/Lang.php`
- Zeile: 240
- Rueckgabe: `void`

```
/**
 * Lädt das Nachrichtenarray der Sprache und fällt bei fehlender/leerer Datei auf DEFAULT_LANG zurück.
 * @param string $lang
 * @return void
 */
```

### `private static buildLocaleLabel($langCode, $countryCode)`

- Datei: `./src/Util/Lang.php`
- Zeile: 273
- Rueckgabe: `string`

```
/**
 * Baut mit der Intl-Erweiterung einen lokalisierten Sprachnamen, sonst ein stabiles Code-Label.
 * @param string $langCode
 * @param string $countryCode
 * @return string
 */
```

### `private static flagIcon($countryCode)`

- Datei: `./src/Util/Lang.php`
- Zeile: 297
- Rueckgabe: `string`

```
/**
 * Liefert das vorhandene Länderflaggen-SVG oder ein Unicode-Flaggenzeichen.
 * @param string $countryCode
 * @return string
 */
```

### `private static sanitizeLangCode($lang)`

- Datei: `./src/Util/Lang.php`
- Zeile: 322
- Rueckgabe: `string`

```
/**
 * Normalisiert Bindestriche und verwirft Codes außerhalb des Locale-Formats.
 * @param string $lang
 * @return string
 */
```

### `private static sanitizeAvailable($lang)`

- Datei: `./src/Util/Lang.php`
- Zeile: 337
- Rueckgabe: `string`

```
/**
 * Ersetzt nicht installierte Sprachen durch DEFAULT_LANG oder die erste verfügbare Sprache.
 * @param string $lang
 * @return string
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">

## `./src/Util/Markdown.php`

### `public static render($markdown, $imageResolver)`

- Datei: `./src/Util/Markdown.php`
- Zeile: 42
- Rueckgabe: `string`

```
/**
 * Rendert den unterstützten Markdown-Teilumfang zu escaped HTML für das Hilfedokumentationsmodal.
 * @param callable|null $imageResolver Resolver mit String-Quelle und erlaubter String-URL oder null; null verwirft Bilder.
 * @param string $markdown
 * @return string
 */
```

### `private static renderBlock($block, $imageResolver)`

- Datei: `./src/Util/Markdown.php`
- Zeile: 91
- Rueckgabe: `string`

```
/**
 * Rendert einen Codeblock-Platzhalter, eine Überschrift, Liste oder einen Absatz.
 * @param string $block
 * @param ?callable $imageResolver
 * @return string
 */
```

### `private static listItems($lines, $markerPattern)`

- Datei: `./src/Util/Markdown.php`
- Zeile: 127
- Rueckgabe: `array`

```
/**
 * Zerlegt Listenzeilen, hängt eingerückte Folgezeilen an den vorherigen Eintrag.
 * @param string[] $lines Zeilen eines Markdown-Blocks.
 * @return string[] Bereinigte Listeneinträge.
 * @param string $markerPattern
 */
```

### `private static renderList($tag, $items, $imageResolver)`

- Datei: `./src/Util/Markdown.php`
- Zeile: 148
- Rueckgabe: `string`

```
/**
 * Erzeugt eine HTML-Liste aus zuvor erkannten Einträgen und escaped Inline-Markup.
 * @param string $tag
 * @param array $items
 * @param ?callable $imageResolver
 * @return string
 */
```

### `private static renderInline($text, $imageResolver)`

- Datei: `./src/Util/Markdown.php`
- Zeile: 164
- Rueckgabe: `string`

```
/**
 * Escaped Text zuerst und wandelt danach nur unterstützte Inline-Syntax in HTML um.
 * @param string $text
 * @param ?callable $imageResolver
 * @return string
 */
```

<hr style="border:0; height:5px; background:#1e90ff;">
