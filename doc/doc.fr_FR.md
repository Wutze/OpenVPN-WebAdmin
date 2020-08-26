# Documentation en français

## Introduction

Vous verrouillez votre porte d'entrée. Mais pourquoi laissez-vous les entrées de derrière ouvertes ? Il en va de même pour presque tous les accès Internet à votre IdO, vos webcams et autres appareils.

Bonjour les amis.

Il s'agit de la première étape d'une version entièrement réécrite pour la gestion des accès via un navigateur web afin de garantir l'accès aux réseaux internes via OpenVPN. Il s'agit d'une version _PAS_ sécurisée pour le moment. Ce n'est possible qu'avec la version 1.1.0. Mais je voudrais vous donner l'occasion de le tester par vous-même aujourd'hui, afin que vous puissiez apporter des idées ou même votre propre code.

## Mise à jour

Une mise à jour de l'original, les versions 0.8 et 1.0.x, sera disponible avec la version 1.1.0.

## Installation

L'installation a été testée, mais des erreurs peuvent encore se produire.

## Fichier de configuration config.php

En fait, les constantes définies devraient s'expliquer d'elles-mêmes par leur nom. (Exemple valable à partir de la version 1.1.0 et différent des versions précédentes)

````php
$dbhost = 'db.home' ;
$dbport = '3306' ;
$dbname = 'testeur' ;
$dbuser = 'testeur' ;
$dbpass = 'testeur' ;
$dbtype = 'mysqli' ;
$dbdebug = FALSE ;
$sessdebug = FALSE ;

/* nom du site */
define('_SITE_NAME', "OVPN-WebAdmin") ;
define('HOME_URL', "vpn.home") ;
define('_DEFAULT_LANGUAGE', 'fr_FR') ;

/** Site de connexion */
define('_LOGINSITE', 'login1') ;
````

L'entrée ___LOGINSITE__ définit le contenu à charger pour la page de connexion, que vous pouvez imprimer vous-même. Les différentes pages de connexion sont stockées sous __include/html/login/ [ dossier ]__. Le fichier __login.php__ sera chargé et affiché automatiquement.

### Page de connexion définie par l'utilisateur

Il y a déjà quelques exemples sous __login1, login2 et login3__. Vous pouvez réaliser toutes vos propres idées. Les conditions pour un fonctionnement correct sont au nombre de 5 :

````php
include (REAL_BASE_DIR.'/include/html/login/login.functions.php') ;
````

Avec cette entrée, vous chargez les fonctions du modèle qui sont nécessaires pour la transmission des données de connexion.

#### entrées obligatoires

##### Loginfield : Nom d'utilisateur

````php
echo username() ;
````

##### Champ de connexion : Mot de passe

````php
echo password() ;
````

##### Loginfield : champs cachés

````php
echo hiddenfields() ;
````

##### Loginfield : bouton

````php
echo button() ;
````

Il arrive souvent que le bootstrap ou d'autres styles à charger nécessitent des informations complémentaires sur les classes. Vous pouvez facilement transmettre les informations appropriées de la manière suivante :

````php
echo button("btn-primary btn-block") ;
````

Le système émet des spécifications supplémentaires, qui sont brièvement décrites ci-dessous :

````php
# Le nom du site web, entré dans le fichier config.php
echo _SITE_NAME ;

````

## Afficher les variables des fichiers de langue

Par défaut, le contenu du tableau __$message__ est toujours affiché. La meilleure façon de l'appeler est via :

````php
echo GET_Lang::message("_LOGIN_DATA") ;

````

Comme la classe __class.language.php__ est toujours chargée elle aussi, l'appel peut se faire de cette manière. La classe recherche alors la clé correspondante, ici ___LOGIN_DATA__ et l'affiche. Les clés qui n'existent pas sont automatiquement affichées avec un __LANGUAGE_ERROR__ et la mauvaise entrée passée.

Si vous voulez charger d'autres tableaux à partir du fichier de langue, il suffit de passer le nom du tableau, la classe trouvera la valeur et l'affichera.

````php
echo GET_Lang::message("4", "errormessage") ;

````

Cette entrée produirait la clé 4 du tableau __errormessage__.

## Pages d'erreur personnalisées

Depuis la version 1.2.0, vous avez la possibilité de créer vos propres pages d'erreur. Les pages d'erreur sont toujours appelées avec l'entrée de la page de connexion et doivent donc être également disponibles dans le dossier correspondant - voir "Fichier de configuration config.php" - en tant que error.php.

Traduit avec www.DeepL.com/Translator (version gratuite)
