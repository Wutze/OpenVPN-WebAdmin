# Documentation German

## Intro

Hello, friends.

This is the first step for a completely rewritten version for access management via web browser to ensure access to internal networks via OpenVPN. This is _NOT_ secure version at the moment. This will only be reached with version 1.1.0. But I would like to give you the opportunity to test it by yourself today, so that you can contribute ideas or even your own code.

## Upgrade

An upgrade from the original, Release 0.8, will be available with Version 1.1.0.

## Installation

The installation has been tested, but errors may still occur.

## Configuration file config.php

Actually, the defined constants should be self-explanatory by name.

```php
$host = 'db.home';
$port = '3306';
$db = 'tester';
$user = 'tester';
$pass = 'tester';
$dbtype = 'mysqli';
$dbdebug = FALSE;
$sessdebug = FALSE;

/* site name */
define('_SITE_NAME', "OVPN-WebAdmin");
define('HOME_URL', "vpn.home");
define('_DEFAULT_LANGUAGE','de_DE');

/** Login Site */
define('_LOGINSITE', 'login1');
````

The entry ___LOGINSITE__ defines the content to be loaded for the login page, which you can print out yourself. The different login pages are stored under __include/html/login/ [ folder ]__. The __login.php__ will be loaded and displayed automatically.

### User defined login page

There are already some examples under __login1, login2 and login3__. You can realize every own idea. Condition for a correct functioning are 5 things:

```php
include (REAL_BASE_DIR.'/include/html/login/login.functions.php');
````

With this entry you load the template functions which are needed for the transmission of the login data.

#### required entries

##### Loginfield: Username

```php
echo username();
````

##### Loginfield: Password

```php
echo password();
````

##### Loginfield: hidden Fields

```php
echo hiddenfields();
````

##### Loginfield: button

```php
echo button();
````

It often happens that bootstrap or other styles to be loaded require further information about the classes. You can easily pass the appropriate information in the following way:

```php
echo button('btn-primary btn-block');
````

The system issues further specifications, which are briefly described below:

```php
# The name of the website, entered in the config.php
echo _SITE_NAME;

````

## Display variables from the language files

By default, the content from the __$message__ array is always displayed. The best way to call it is via:

```php
echo GET_Lang::message("_LOGIN_DATA");

````

Since the class __class.language.php__ is always loaded as well, the call can be done this way. The class then searches for the corresponding key, here ___LOGIN_DATA__ and displays it. Keys that do not exist are automatically displayed with a __LANGUAGE_ERROR__ and the passed wrong entry.

If you want to load other arrays from the language file, just pass the name of the array, the class will find the value and display it.

```php
echo GET_Lang::message("4", "errormessage");

````

This entry would output key 4 from the array __errormessage__.

Translated with www.DeepL.com/Translator (free version)
