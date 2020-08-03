# Devemlopment (DE/EN/FR)

## Screenshots

![Previsualisation Development Button](/doc/img/dev1.png)
![Previsualisation Development Overview](/doc/img/dev2.png)

## Einführung DE

Sobald in Eurer config.php dieser Eintrag vorhanden ist, wird die Webseite, wie im Screenshot zu erkennen, ergänzt. Der Button __Debug__ bringt die zu überwachenden Variablen dann zur Anzeige. Sollte ein Fehler im PHP-Script sein, undefinierte Variablen oder sonstiges, wird der Button rot anfangen zu blinken. Im geöffneten Debugging-Fenster wird dann der jeweils letzte PHP-Fehler im errorhandler angezeigt, der sich dann im automatisch erweiterten Modus befindet. Überwachte Variablen könnt ihr nun ausblenden, vergrößern oder maximieren. Nach jedem Reload der Webseite werden diese aber wieder zur Anzeige gebracht.

Zusätzlich wird ein neuer Menüeintrag __Dev__ eingefügt, der eine eigene "Webseite" zur Anzeige bringt. Die Datei __dev/dev.php__ kann dann so von Euch editiert werden, als sei es eine eigenständnige Seite, die innerhalb des Main-Contents angezeigt wird.

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

Hierbei wird dann aber lediglich der letzte aller PHP-Fehler zur Anzeige gebracht. Ihr solltet mit diesem Einsatz vorsichtig sein, da viele PHP-Fehler sonst nicht zur Anzeige kommen werden.

## Introduction EN

As soon as this entry is available in your config.php, the website will be added as shown in the screenshot. The __Debug__ button will then display the variables to be monitored. If there is an error in the PHP script, undefined variables or other things, the button will start flashing red. In the opened debugging window the last PHP error is displayed in the errorhandler, which is then in automatically extended mode. You can now hide, enlarge or maximize monitored variables. After each reload of the web page they will be displayed again.

In addition, a new menu entry __Dev__ is added, which displays a new "web page". The file __dev/dev.php__ can then be edited by you as if it were a separate page that is displayed within the main content.

Translated with www.DeepL.com/Translator (free version)

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

What is marked __broker__ here should be given a meaningful name by you, so that the origin of the variable to be monitored can be identified. Followed by the variable to be monitored.

If you want to abort the script in between to stop the automatic reload of the website, the following call is sufficient:

````php
(defined('dev))? $GLOBALS['devint']->ends() : "";
````

The script is then aborted and all variables collected up to that point are displayed.

A further call can be made using the __error_handler__ like this:

````php
trigger_error('Any statement: '. $variable);
````

However, only the last of all PHP errors will be displayed. You should be careful with this use, otherwise many PHP errors will not be displayed.

## Introduction FR

Dès que cette entrée sera disponible dans votre config.php, le site sera ajouté comme indiqué dans la capture d'écran. Le bouton __Debug__ affichera alors les variables à surveiller. S'il y a une erreur dans le script PHP, des variables indéfinies ou d'autres choses, le bouton se met à clignoter en rouge. Dans la fenêtre de débogage ouverte, la dernière erreur PHP est affichée dans le gestionnaire d'erreurs, qui est alors en mode automatiquement étendu. Vous pouvez maintenant cacher, agrandir ou maximiser les variables surveillées. Après chaque rechargement de la page web, elles seront à nouveau affichées.

En outre, une nouvelle entrée de menu __Dev__ est ajoutée, qui affiche une nouvelle "page web". Le fichier __dev/dev.php__ peut ensuite être édité par vous comme s'il s'agissait d'une page séparée qui s'affiche dans le contenu principal.

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

Vous avez la possibilité d'ajouter de nouveaux appels avec ce code à n'importe quel endroit du code :

````php
(define ('dev)) ? $GLOBALS['devint']->collect('broker',$variable) : "" ;
````

Vous devez donner un nom significatif à ce qui est marqué ici __broker__, afin que l'origine de la variable à surveiller puisse être identifiée. Suivi de la variable à surveiller.

Si vous souhaitez interrompre le script entre-temps pour arrêter la recharge automatique du site, l'appel suivant est suffisant :

````php
(define ('dev)) ? $GLOBALS['devint']->ends() : "" ;
````

Le script est alors interrompu et toutes les variables collectées jusqu'à ce moment sont affichées.

Un autre appel peut être effectué à l'aide du __error_handler__ comme ceci :

````php
trigger_error('Toute déclaration : '. $variable) ;
````

Toutefois, seule la dernière de toutes les erreurs PHP sera affichée. Vous devez faire attention à cette utilisation, sinon de nombreuses erreurs PHP ne seront pas affichées.

Traduit avec www.DeepL.com/Translator (version gratuite)
