# Dokumentation Deutsch

## Intro

Deine Haustür schließt Du ab. Weswegen lässt Du die Hintereingänge aber offen? So und nicht anders verhält es sich mit fast allen Internetzugängen zu Deinem IoT, den Webcams und anderen Geräten.

Hallo Freunde.

Das ist der erste Schritt für eine vollständig neu geschriebene Version für eine Zugriffsverwaltung per Webbrowser, um den Zugriff auf interne Netzwerke per OpenVPN zu gewährleisten. Hierbei handelt es sich im Moment um _KEINE_ sichere Version. Diese wird erst mit Version 1.1.0 erreicht sein. Ich möchte Euch aber heute schon die Möglichkeit geben selbst zu testen, um Ideen oder sogar eigenen Code einbringen zu können.

## Upgrade

Ein Upgrade vom Original, Release 0.8 und 1.0.x, wird es mit Version 1.1.0 geben.

## Installation

Die Installation ist zwar getestet, es kann aber noch Fehler geben.

## Konfigurationsdatei config.php

Eigentlich sollten die definierten Konstanten vom Namen her selbsterklärend sein. (Beispiel ab 1.1.0 gültig und unterscheidet sich zu den vorherigen Versionen)

````php
$dbhost = 'db.home';
$dbport = '3306';
$dbname = 'tester';
$dbuser = 'tester';
$dbpass = 'tester';
$dbtype = 'mysqli';
$dbdebug = FALSE;
$sessdebug = FALSE;

/* Site-Name */
define('_SITE_NAME',"OVPN-WebAdmin");
define('HOME_URL',"vpn.home");
define('_DEFAULT_LANGUAGE','de_DE');

/** Login Site */
define('_LOGINSITE','login1');
````

Der Eintrag ___LOGINSITE__ definiert den zu ladenden Inhalt für die Loginseite, die Ihr Euch jeweils selbst ausschen könnt. Die verschiedenen Loginseiten werden gespeichert unter __include/html/login/ [ Ordner ]__. Es wird dann automatisch die __login.php__ geladen und zur Anzeige gebracht.

### Benutzerdefinierte Loginseite

Es gibt ja schon einige Beispiele unter __login1, login2 und login3__. Ihr könnt jede eigene Idee umsetzen. Bedingung für ein korrektes funktionieren sind 5 Dinge:

````php
include (REAL_BASE_DIR.'/include/html/login/login.functions.php');
````

Mit diesem Eintrag ladet Ihr die Templatefunktionen die zur übermittlung der Logindaten benötigt werden.

#### benötigte Einträge

##### Loginfield: Username

````php
echo username();
````

##### Loginfield: Passwort

````php
echo password();
````

##### Loginfield: hidden Fields

````php
echo hiddenfields();
````

##### Loginfield: Button

````php
echo button();
````

Oftmals kommt es vor, dass bootstrap oder andere zu ladende Styles, weitere Angaben zu den Klassen benötigen. Die entsprechenden Angaben könnt Ihr ganz einfach auf die folgende Weise übergeben:

````php
echo button('btn-primary btn-block');
````

Das System gibt weitere Angaben heraus, die im folgenden kurz beschrieben werden:

````php
# Der Name der Webseite, eingetragen in der config.php
echo _SITE_NAME;

````

## Variablen aus den Sprachfiles anzeigen

Standardmäßig wird immer der Inhalt aus dem Array __$message__ angezeigt. Der Aufruf erfolgt am besten per:

````php
echo GET_Lang::nachricht("_LOGIN_DATA");

````

Da die Klasse __class.language.php__ immer mit geladen wird, kann der Aufruf auf diese Weise erfolgen. Die Klasse sucht dann den entsprechenden Schlüssel, hier ___LOGIN_DATA__ und bringt ihn zur Anzeige. Schlüssel die nicht existieren werden automatisch mit einem __LANGUAGE_ERROR__ und dem übergebenen falschen Eintrag angezeigt.

Möchtet Ihr andere Arrays aus dem Sprachfiles laden, übergebt Ihr einfach den Namen des Arrays, die Klasse findet den Wert und zeigt es Euch an.

````php
echo GET_Lang::nachricht("4","errormessage");

````

Dieser Eintrag würde den Schlüssel 4 aus dem Array __errormessage__ ausgeben.

## Angepasste Fehlerseiten

Seit Version 1.2.0 habt Ihr die Möglichkeit eigene Fehlerseiten zu kreieren. Die Fehlerseiten werden immer mit dem Eintrag der Login Seite ausgerufen und müssen demnach auch im entsprechenden Ordner - siehe "Konfigurationsdatei config.php" - als error.php vorhanden sein.
