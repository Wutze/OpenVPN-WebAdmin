# Sprach- und Hilfesystem

## Sprache

`Micro\OpenvpnWebadmin\Util\Lang` lädt `Lang/<locale>/lang.php`. Die vorhandenen Locale-Verzeichnisse werden dynamisch anhand ihres ISO-Codes erkannt. Die Anwendung startet die Session in `src/load.php`, liest anschließend `$_SESSION['lang']` und übergibt den Wert an `Lang::init()`. Bei einem Sprachwechsel speichert der Router den gewählten Code in der vorhandenen Session und initialisiert die Klasse neu. Die wiederverwendbare Sprachklasse enthält keine Cookie- oder Session-Verwaltung.

## Hilfetexte und Dokumentverweise

Ein normaler Textschlüssel kann mit einem Marker ergänzt werden. Der Marker wird bei der Textausgabe entfernt; das Modal ergänzt am markierten Text ein Hilfesymbol.

```php
'_WELCOME' => 'Willkommen [HELP_DOC/other/:TEXT]',
```

`[HELP_DOC/<bereich>/:<dokument>]` verweist auf `doc/<bereich>/<dokument>.md`. Beispiele:

- `HELP_DOC/other/:TEXT` → `doc/other/TEXT.md`
- `HELP_DOC/system/:HINWEIS` → `doc/system/HINWEIS.md`

Die Bestandteile dürfen nur Buchstaben, Ziffern, Unterstriche und Bindestriche enthalten. `HELP_`-Schlüssel enthalten Beschriftungen und Meldungen für das Hilfemodal. Fehlende Markdown-Dateien werden als nicht verfügbar gemeldet, es wird hier keine Standard-Datei einer anderen Sprache verwendet.

## Für Entwickler

Die Sprachfiles werden alle nach `/src/Lang` kopiert und benutzen als Ordnername den ISO-Code der jeweiligen Sprache, das Sprachfile selbst erhält immer den Dateinamen `lang.php`. Es ist darauf zu achten, dass die einzelnen Einträge *IMMER* mit der gleichen Zeilennummer erscheinen! Hast du keine Übersetzung für eine Variable innerhalb des Sprachtemplates gefunden, dann nutze Englisch als Vorlage dafür.

## Updates

Im Moment werden Sprachfiles durch das Original vom Entwickler überschrieben, so dass eigene Ergänzungen zu den Hilfetexten überschrieben werden. Das gleiche gilt auch für die Hilfetexte, die bis jetzt alle durch die git-Server-Version ersetzt werden. Eine Änderung hierfür ist geplant und steht auf meiner [Wishlist](../WISHLIST.md).
