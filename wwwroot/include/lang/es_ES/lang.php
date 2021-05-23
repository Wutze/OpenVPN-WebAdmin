<?php
/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020 OpenVPN-WebAdmin
 *
 * NOTICE OF LICENSE
 *
 * GNU AFFERO GENERAL PUBLIC LICENSE V3
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/agpl-3.0.en.html
 *
 * @fork Original Idea and parts in this script from: https://github.com/Chocobozzz/OpenVPN-Admin
 *
 * @author    Wutze
 * @author    https://github.com/victorhck - thank you
 * @copyright 2020 OpenVPN-WebAdmin
 * @link			https://github.com/Wutze/OpenVPN-WebAdmin
 * @see				Internal Documentation ~/doc/
 * @version		1.0.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');
/**
 * Language es_ES
 */

$message['_NO_USER']                = "Nombre de usuario desconocido";
$message['_SITE_TITLE']             = "Título de página";
$message['_LANGUGE_ERROR']          = "Error en el archivo de idioma";
$message['_NEED_JAVASCRIPT']        = "Lo siento, ¡JavaScript es necesario!";

$message['_HOME']                   = "Inicio";
$message['_OVERVIEW']               = "Información general";
$message['_INFOS_PLUS']             = "Información adicional";
$message['_WELCOME']                = "Bienvenido";

$message['_USERS']                  = "Usuario";
$message['_DISK']                   = "Disco";
$message['_SYSTEM']                 = "Sistema";
$message['_ERROR']                  = "Error";
$message['_SAVE']                   = "Guardar";
$message['_RESTART']                = "¡Atención! ¡Reinicie el servidor o clientes después de los cambios!";
$message['_NEW']                    = "Nuevo";

$message['_U_NAME']                 = "Usuario";
$message['_U_GROUP']                = "Grupo";
$message['_U_ENABLE']               = "Habilitado";
$message['_U_FROM']                 = "desde";
$message['_U_TO']                   = "a";
$message['_U_ONLINE']               = "En línea";
$message['_U_NETIP']                = "Dirección IP del usuario";
$message['_U_GATEWAYIP']            = "IP de puerta de enlace del servidor";
$message['_U_EXTEND_VIEW']          = "Vista avanzada para: ";
$message['_U_TIMESETTINGS']         = "Límites de tiempo";
$message['_U_NETSETTINGS']          = "Red";
$message['_U_PLUS']                 = "Otro (desactivar)";

$message['_ATTENTION_FW']           = "¡Deberías saber qué estás haciendo!";
$message['_ATTENTION_CF']           = "Debería reiniciarse el cliente y/o servidor después de los cambios";

$message['_VIEW']                   = "ver";

$message['_LOGIN']                  = "Iniciar sesión";
$message['_LOGIN_NAME']             = "Nombre de usuario";
$message['_LOGIN_PASS']             = "Contraseña";
$message['_LOGIN_DATA']             = "Por favor, ingrese los datos de inicio de sesión";
$message['_LOGIN_SAVE']             = "recordar inicio de sesión";
$message['_LOGOUT']                 = "X";
$message['_YOUR_LOGIN']             = "Tu cuenta";
$message['_USER_RIGHTS']            = "Sus derechos de usuario";

$message['_SAVE_PW']                = "Guardar";
$message['_LOGIN_PASS_CONTROL']     = "Control";
$message['_CHANGE_PASS']            = "cambiar contraseña";
$message['_INPUT_NEW_PASS']         = "Nueva contraseña";
$message['_RETYPE_NEW_PASS']        = "confirmar contraseña";
$message['_USER_EMAIL']             = "Tu dirección de correo electrónico";
$message['_USER_NAME']              = "Tu nombre";
$message['_USER_DATA']              = "cambiar tus datos";
$message['_SAVE_USER_DATA']         = "guardar cambios";

$message['_VPN_CONFIGS']            = "Configuraciones";
$message['_OSX_CONFIG']             = "Configuración OSX";
$message['_WIN_CONFIG']             = "Configuración WIN";
$message['_LIN_CONFIG']             = "Configuración Android";

$message['_CONF_SAVED']             = "configuración guardada";
$message['_CONF_SAVE_ERROR']        = "error al guardar";

$message['_SSL_CERTS']              = "Certificados SSL";
$message['_SSL_CERTS_NEW']          = "nuevo certificado";
$message['_SSL_CERTS_EDIT']         = "editar certificado";
$message['_SSL_CERTS_LIST']         = "Listar certificados";

$message['_TODAY']                  = date('Y.m.d');

$message['_VPN_DATA_SAV']           = "Guardar tus ...";
$message['_VPN_DATA_TT']            = "Seleccionar y descargar tu definición de cliente";
$message['_VPN_DATA_OSX']           = "Configuración OSX";
$message['_VPN_DATA_WIN']           = "Configuración WIN";
$message['_VPN_DATA_LIN']           = "Configuración Android/Linux";
$message['_VPN_CLIENT_SAV']         = "Docu/Descargar ...";
$message['_VPN_CLIENT_TT']          = "Documentación/Descargar cliente VPN ...";
$message['_VPN_CLIENT_OSX']         = "OSX";
$message['_VPN_CLIENT_WIN']         = "WIN";
$message['_VPN_CLIENT_LIN']         = "Andoid";
$message['_VPN_CLIENT_EXT']         = "Más información";


$message['_USERDATA_EMAIL']         = "Correo electrónico";
$message['_USERDATA_PASSWORD']      = "Contraseña";
$message['_USERDATA_SAVE']          = "crear usuario";
$message['_USERDATA_ISADMIN']       = "Usuario recibe derechos completos de administrador";
$message['_USERDATA_ISCONFIGADMIN'] = "Usuario recibe derechos de configuración";
$message['_USERDATA_ISLOGADMIN']    = "Usuario obtiene derechos para revisar los registros (logs)";
$message['_USERDATA_ISUSERADMIN']   = "Usuario recibe derechos de administración de usuario";
$message['_USERDATA_ISTLSADMIN']    = "Usuario recibe derechos de gestión de certificado";
$message['_USERDATA_FROM_DATE']     = "Accede desde:";
$message['_USERDATA_TO_DATE']       = "Accede a:";
$message['_USERDATA_NEW_USER']      = "Crear un nuevo usuario";
$message['_USERDATA_USER']          = "Datos de usuario";
$message['_USERDATA_OPTIONS']       = "Opciones";


/** 
 * error messages
 * @uses class::Get_Lang
 * @param $var + array-id
 * @return Message in modal dialog
 */
$errormessage[1]                    = $message['_USERDATA_SAVE']." fallo o contraseña vacía";
$errormessage[2]                    = "Función todavía no disponible. Lo sentimos.";
$errormessage[3]                    = "¡Contraseña recién asignada no era idéntica o estaba vacía!";
$errormessage[4]                    = "¡Contraseña cambiada! ¡Por favor, inicie sesión de nuevo!";

$freedom = array(
                'La persona que prefiera seguridad por encima de libertad es con razón un esclava. (Aristóteles)',
                'La libertad perece si no despreciamos todo lo que intenta someterse a un yugo. (Séneca)',
                'No dejes que esto te deprima. Se valiente, salvaje y maravilloso. (Pipi Langstrumpf)',
                'Prefiero una libertad peligros que una servidumbre tranquila. (Rousseau)'
);



?>
