# Documentación en Español

## Introducción

Cierras la puerta principal de tu casa. Pero ¿por qué dejas abierta la puerta de trasera? Esto es equiparable con casi todos los accesos a internet de tus dispositivos de internet de las cosas (IoT), cámaras web y otros dispositivos.

Hola amigos y amigas.

Este es el primer paso de una versión completamente renovada del gestor de acceso mediante un navegador web para asegurar el acceso a redes internas mediante OpenVPN. Esto _NO_ es una versión segura de momento. Eso sólo se alcanzará con la versión 1.1.0. Pero me gustaría darte la oportunidad de probarla por ti mismo hoy mismo, y así poder contribuir con ideas o incluso con tu propio código.

## Actualizar

Podrás actualizar desde el original, la publicación 0.8 y 1.0.x, estarán disponibles con la versión 1.1.0.

## Instalación

El proceso de instalación ha sido comprobado, pero puede surgir algún error.

## El archivo de configuración config.php

En realidad, las constantes definidas son fácilmente identificadas por su propio nombre. (Ejemplo válido desde la versión 1.1.0 que difiere de versiones previas)

````php
$dbhost = 'db.home';
$dbport = '3306';
$dbname = 'tester';
$dbuser = 'tester';
$dbpass = 'tester';
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

La entrada ___LOGINSITE__ define el contenido que debe ser cargado para la página de inicio de sesión (login) que puedes inprimir tu mismo. Las diferentes páginas de inicio de sesión están almacenadas en __include/html/login/ [ carpeta ]__. El archivo __login.php__ será cargado y mostrado automáticamente.

### Página de inicio de sesión definida por el usuario

Ya existen algunos ejemplos en __login1, login2 y login3__. Que pueden servir para hacerte una idea. Hay 5 cosas a tener en cuenta para un correcto funcionamiento:

````php
include (REAL_BASE_DIR.'/include/html/login/login.functions.php');
````

Con esta entrada cargas llas funciones de plantilla que son necesarias para la transmisión de los datos de inicio de sesión.

#### entradas requeridas

##### Loginfield: Nombre de usuario

````php
echo username();
````

##### Loginfield: Contraseña

````php
echo password();
````

##### Loginfield: Campos ocultos

````php
echo hiddenfields();
````

##### Loginfield: Botón

````php
echo button();
````

A menudo ocurre que el inicio (bootstrap) u otros estilos que son cargados requieren más información sobre las clases. De manera sencilla puedes pasar la información apropiada de la siguiente manera:

````php
echo button('btn-primary btn-block');
````

El sistema emite otras especificaciones, que son descritas brevemente a continuación:

````php
# El nombre del sitio web, introducido en el archivo config.php
echo _SITE_NAME;

````

## Mostrando variable desde los archivos de idiomas

De manera predeterminada, el contenido del array __$message__ siempre es mostrado. La mejor manera de hacer esta llamada es mediante:

````php
echo GET_Lang::message("_LOGIN_DATA");

````

Como la calse __class.language.php__ está también siempre cargado, la llamada puede hacerse de esta forma. La clase busca la clave correspondiente, aquí ___LOGIN_DATA__ y la muestra. Las claves que no existan son mostradas de manera automática con un __LANGUAGE_ERROR__ y la entrada que se ha pasado con error.

Si quieres cargar otros arrays desde el archivo de idioma, simplemente pasa el nombre del array, la clase encontrará el valor y lo mostrará.

````php
echo GET_Lang::message("4", "errormessage");

````

Esta entrada mostraría la clave 4 del array __errormessage__.

## Personalizando las páginas de error

Desde la versión 1.2.0 tienes la posibilidad de crear tus propias páginas de error. Las páginas de error siempre son llamadas con la entrada de la página de inicio de sesión y deben también estar disponibles en la carpeta correspondiente - ver "Archivo de configuración config.php" - como error.php.

_Traducido desde el documento en inglés_
