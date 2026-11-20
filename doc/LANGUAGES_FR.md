# Système de langues et d'aide

## Langue

`Micro\OpenvpnWebadmin\Util\Lang\` charge `Lang/\<locale>/lang.php`. Les répertoires de langues disponibles sont détectés dynamiquement à partir de leur code ISO. L'application démarre la session dans `src/load.php`, lit ensuite `$_SESSION['lang']` et transmet la valeur à `Lang::init()`. Lors d'un changement de langue, le routeur enregistre le code choisi dans la session existante et réinitialise la classe. La classe de gestion des langues réutilisable ne contient aucune gestion des cookies ou des sessions.

## Textes d'aide et références aux documents

Une clé de texte standard peut être complétée par un marqueur. Ce marqueur est supprimé lors de l'affichage du texte ; le modal ajoute une icône d'aide au texte marqué.

```php
'_WELCOME' => 'Bienvenue [HELP_DOC/other/:TEXT]',
```

`[HELP_DOC/<bereich>/:<dokument>]` fait référence à `doc/<bereich>/<dokument>.md`. Exemples :

- `HELP_DOC/other/:TEXT` → `doc/other/TEXT.md`
- `HELP_DOC/system/:HINWEIS` → `doc/system/HINWEIS.md`

Les composants ne peuvent contenir que des lettres, des chiffres, des underscores et des tirets. Les clés `HELP_` contiennent les libellés et les messages destinés au modal d'aide. Les fichiers Markdown manquants sont signalés comme indisponibles ; aucun fichier par défaut d'une autre langue n'est utilisé.

## Pour les développeurs

Tous les fichiers de langue sont copiés dans `/src/Lang` et utilisent le code ISO de la langue comme nom de répertoire. Le fichier de langue lui-même porte toujours le nom `lang.php`. Il faut veiller à ce que les différentes entrées apparaissent **TOUJOURS** à la même ligne dans chaque fichier ! Si tu ne trouves pas de traduction pour une variable dans le modèle de langue, utilise l'anglais comme référence.

## Mises à jour

Actuellement, les fichiers de langue sont remplacés par la version originale du développeur, ce qui entraîne la perte des ajouts personnalisés aux textes d'aide. Il en va de même pour les textes d'aide, qui sont jusqu'à présent tous remplacés par la version provenant du serveur Git. Une modification de ce fonctionnement est prévue et figure dans ma [Wishlist](../WISHLIST.md).
