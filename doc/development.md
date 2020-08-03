# Devemlopment (DE/EN)

## Einführung DE

![Previsualisation Development Button](doc/dev1.png)
![Previsualisation Development Overview](doc/dev2.png)

Sobald in Eurer config.php dieser Eintrag vorhanden ist, wird die Webseite, wie im Screenshot zu erkennen, ergänzt. Der Button __Debug__ bringt die zu überwachenden Variablen dann zur Anzeige. Sollte ein Fehler im PHP-Script sein, undefinierte Variablen oder sonstiges, wird der Button rot anfangen zu blinken. Im geöffneten Debugging-Modul wird dann der jeweils letzte PHP-Fehler im errorhandler angezeigt, der dann auch automatisch geöffnet ist. Überwachte Variablen könnt ihr nun ausblenden, vergrößern oder maximieren. Nach jedem Reload der Webseite werden diese aber wieder zur Anzeige gebracht.

````php
/**
 * only for development!
 * please comment out if no longer needed!
 * comment out the "define function" to enable
 */
define('dev','dev/dev.php');
if (defined('dev')){
  include_once('dev/class.dev.php');
}
````

Ihr habt an jeder Stelle im Code die Möglichkeit neue Aufrufe hinzuzufügen mit diesem Code:

````php
(defined('dev'))? $GLOBALS['devint']->collect('broker',$variable) : "";
````

Was hier noch mit __broker__ bezeichnet ist, sollte durch Euch einen aussagefähigen Namen bekommen, damit die Herkunft der zu überwachenden Variable erkannt werden kann. Gefolgt von der zu überwachenden Variablen.

Möchtet Ihr das Script zwischendrin abbrechen, um den automatischen Reload der Webseite zu unterbinden, dann genügt folgender Aufruf:

````php
(defined('dev'))? $GLOBALS['devint']->ends() : "";
````

Daraufhin wird das Script abgebrochen und alle bis dahin gesammelten Variablen werden zur Anzeige gebracht.

Ein weiterer Aufruf kann über den __error_handler__ so erfolgen:

````php
trigger_error('Irgend eine Aussage: '. $variable);
````

Hierbei wird dann aber lediglich der letzte aller Aufrufe zur Anzeige gebracht. Ihr solltet aber mit diesem Einsatz vorsichtig sein, da alle PHP-Fehler sonst nicht zur Anzeige kommen werden.

## Introduction EN

As soon as this entry is available in your config.php, everything that is exposed is automatically displayed.

````php
/**
 * only for development!
 * please comment out if no longer needed!
 * comment out the "define function" to enable
 */
define('dev', 'dev/dev.php');
if (defined('dev')){
  include_once('dev/class.dev.php');
}
````

You have the possibility to add new calls with this code at any place in the code:

````php
(defined('dev))? $GLOBALS['devint']->collect('broker',$variable) : "";
````

What is marked __broker__ here should be given a meaningful name by you, so that the origin of the module to be monitored can be identified. Followed by the variable to be monitored.

If you want to abort the script in between, the following call is sufficient:

````php
(defined('dev))? $GLOBALS['devint']->ends() : "";
````

The script is then aborted and all variables collected up to that point are displayed.

A further call can be made using the __error_handler__ like this:

````php
trigger_error('Any statement: '. $variable);
````

However, only the last of all calls is displayed.
