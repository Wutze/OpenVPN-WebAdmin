# Programmierstil

## Code Aufbau

Grundsätzlich gilt, dass alle Aufrufe über die in der Webroot liegende **index.php** erfolgen müssen. Andere Aufrufe sind nicht erlaubt. Jeder Aufruf benötigt die Option **?op=inhalt**, dem weitere Variablen im Form von **&var=xxx&var2=xxx** folgen dürfen. Im Code ist darauf zu achten, dass **&** als **&amp;amp;** geschrieben wird. Ist **op** nicht vorhanden, wird stets die Startseite, je nachdem ob der Benutzer eingeloggt ist oder nicht, angezeigt. Die erlaubten Inhalte der Variablen **op** sind in der _include/class/class.request.php_ im Array **var $op=** hinterlegt. Jeder andere Inhalt in dieser Variable führt zu einer Fehlermeldung mit gleichzeitigem Logout aus der Anwendung.

## Code-Einrückung

Es gibt nicht "die richtige Form", weswegen ich mich entschieden habe im PHP-Code jeweils 2 Leerzeichen als Standardeinrückung zu verwenden. Auch das umbrechen der geschweiften Klammer ({) nach Funktionsdeklarationen, Klassen oder ähnlichem, überlasse ich jedem selbst. Jedoch habe ich die Bitte, passt auf den ausgegebenen HTML-Quelltext auf, dass der am Ende lesbar bleibt. Die Einrückung hier wurde mit jeweils einem Leerzeichen begonnen, versucht Euch bitte daran zu halten.

_Ausnahme_: Es handelt sich um HTML-Text der ausschließlich in Debug-Funktionen zu finden ist.

## Dokumentation im Code

Versucht bitte nach Möglichkeit neue Funktionen oder Klassen zu dokumentieren. Wer möchte kann seine Initialen mit **@author** in die Dokumentation einfügen. Bitte dokumentiert auch den auszugebeneden HTML-Quelltext mit

````html
<!-- Text -->
....
<!-- /Text -->
````

Bitte macht Euch diese Mühe, andere Entwickler werden Euch dankbar sein wenn mal wieder nicht geschlossene DIV-Tags zu suchen sind.

## Struktur der Anwendung

### Webanwendung

Die Webanwendung ist vollständnig im Ordner _wwwroot_ zu finden. Der Aufruf der Webseite ist wie folgt:

````code
index.php
       |
       - load.php
               |
               - config.php
               - all available modules
               - all available Languages (see _/include/html/modules/languages/_)
               - all standard classes (Session, json, livedata, secure, data)
                    |
                    - class.request.php (Central control File [Broker])
                        |    |    |    |
                        |    |    |    ->
                        |    |    ->
                        |    ->
                        ->                all other Scripts, Modules what ever

````

Essentiell für den Aufbau der Webseite sind dann folgende, im Ordner _/include/html/_ enthaltenen, Dateien:

* htmlfoot.php
* htmlhead.php
* main-html.php

Wie die Namen schon verraten, das sind die Dateien die für den Aufbau der Webseite verantwortlich sind. Die _main-html.php_ lädt dann alle benötigten Dateien, je nach Zugriffsrechten des Benutzers.

### Dateibenennung

Dateien die Ergänzungen in den Menüs beinhalten, sollten einfach im Ordner _/include/html/_ abgelegt werden.

Administrative Erweiterungen sollten mit _admin-beschreibung.php_ benannt werden. Alle weiteren und für alle Benutzer einzusehenden Dateien dann mit _main-beschreibung.php_.

Content, also alles was dann anzuzeigende Inhalte betrifft, sollten im Ordner _/include/html/content/_ gespeichert werden. Auch hier bitte eine entsprechende, selbsterklärende Beschreibung der Dateien benutzen. (Im Moment herrscht hier sicherlich noch etwas Unordnung, bitte nicht davon stören lassen. Wer mag darf da gern aufräumen ;o))

Wollt Ihr neue Anwendungen programmieren, dann gibt es zwei Möglichkeiten:

1. Im Ordner _include/html/modules/_ einen neuen Ordner anlegen und automatisch laden lassen
2. Einfach was neues dazu bauen und in einem passenden Ordner ablegen

### Installation

Im Ordner _installation_ sind alle relevanten Dateien/Daten für eine Erstinstallation oder Update abgelegt. Dort befinden sich auch die Sprachfiles für die Installation sowie Snippets für alle möglichen Ideen, die dem Benutzer später die Arbeit mit dem System erleichtern könnten.

### Dokumentation Anwendungen

Im Ordner **doc** sollen alle Bilder und Dokumentationen usw. abgespeichert werden. Selbiges würde auch für eventuelle Beispielwebseiten gelten.
