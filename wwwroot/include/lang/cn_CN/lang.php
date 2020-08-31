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
 * @copyright 2020 OpenVPN-WebAdmin
 * @link			https://github.com/Wutze/OpenVPN-WebAdmin
 * @see				Internal Documentation ~/doc/
 * @version		1.0.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');
/**
 * Language cn_CN
 */

$message['_NO_USER']                = "未知用户名";
$message['_SITE_TITLE']             = "网站标题";
$message['_LANGUGE_ERROR']          = "语言文件中的错误";

$message['_HOME']                   = "首页";
$message['_OVERVIEW']               = "概述";
$message['_INFOS_PLUS']             = "其他信息";
$message['_WELCOME']                = "欢迎光临";

$message['_USERS']                  = "用户";
$message['_DISK']                   = "硬盘";
$message['_SYSTEM']                 = "操作系统";
$message['_ERROR']                  = "错误";
$message['_SAVE']                   = "保存";
$message['_RESTART']                = "小心！更改后重新启动服务器或客户端!";
$message['_NEW']                    = "新产品";

$message['_U_NAME']                 = "帳號";
$message['_U_GROUP']                = "用户组";
$message['_U_ENABLE']               = "激活的";
$message['_U_FROM']                 = "从";
$message['_U_TO']                   = "到";
$message['_U_ONLINE']               = "在线咨询";
$message['_U_EXTEND_VIEW']          = "扩展视图为。";
$message['_U_TIMESETTINGS']         = "时间限制。";
$message['_U_NETSETTINGS']          = "网络。(停用)";
$message['_U_PLUS']                 = "其他。(停用)";

$message['_ATTENTION_FW']           = "你应该知道你在做什么！";
$message['_ATTENTION_CF']           = "客户端和服务器在更改后应重新启动。";

$message['_VIEW']                   = "显示";

$message['_LOGIN']                  = "登入";
$message['_LOGIN_NAME']             = "帳號";
$message['_LOGIN_PASS']             = "密码";
$message['_LOGIN_DATA']             = "请输入登录数据。";
$message['_LOGIN_SAVE']             = "记住登录";
$message['_LOGOUT']                 = "X";
$message['_YOUR_LOGIN']             = "您的账户";
$message['_USER_RIGHTS']            = "您的用户权限";

$message['_SAVE_PW']                = "储存数据";
$message['_LOGIN_PASS_CONTROL']     = "控制";
$message['_CHANGE_PASS']            = "更改密码";
$message['_INPUT_NEW_PASS']         = "新密码";
$message['_RETYPE_NEW_PASS']        = "确认密码";
$message['_USER_EMAIL']             = "您的电子邮件地址";
$message['_USER_NAME']              = "您的姓名";
$message['_USER_DATA']              = "更改您的数据";
$message['_SAVE_USER_DATA']         = "保存更改";

$message['_VPN_CONFIGS']            = "配置";
$message['_OSX_CONFIG']             = "OSX 配置";
$message['_WIN_CONFIG']             = "WIN 配置";
$message['_LIN_CONFIG']             = "Android 配置";

$message['_CONF_SAVED']             = "配置保存";
$message['_CONF_SAVE_ERROR']        = "保存时出错";

$message['_SSL_CERTS']              = "SSL-Zertifikate";
$message['_SSL_CERTS_NEW']          = "Neues Zertifikat";
$message['_SSL_CERTS_EDIT']         = "Edit Zertifikat";
$message['_SSL_CERTS_LIST']         = "Liste der Zertifikate";

$message['_TODAY']                  = date('d.m.Y');

$message['_VPN_DATA_TT']            = "选择并下载你的客户端定义。";
$message['_VPN_DATA_OSX']           = "OSX-配置";
$message['_VPN_DATA_WIN']           = "WIN-配置";
$message['_VPN_DATA_LIN']           = "Android/Linux-配置";
$message['_VPN_CLIENT_SAV']         = "文件/下载 ...。";
$message['_VPN_CLIENT_TT']          = "文档/下载VPN客户端...。";
$message['_VPN_CLIENT_SAV']         = "客户端下载 ...";
$message['_VPN_CLIENT_OSX']         = "OSX";
$message['_VPN_CLIENT_WIN']         = "WIN";
$message['_VPN_CLIENT_LIN']         = "Andoid";
$message['_VPN_CLIENT_EXT']         = "更多信息。";


$message['_USERDATA_EMAIL']         = "電子郵件";
$message['_USERDATA_PASSWORD']      = "密码";
$message['_USERDATA_SAVE']          = "创建用户";
$message['_USERDATA_ISADMIN']       = "用户获得完全的管理权限";
$message['_USERDATA_ISCONFIGADMIN'] = "Benutzer erhält Konfigurations-Rechte";
$message['_USERDATA_ISLOGADMIN']    = "Benutzer erhält Logansicht-Rechte";
$message['_USERDATA_ISUSERADMIN']   = "Benutzer erhält Benutzerverwaltung-Rechte";
$message['_USERDATA_ISTLSADMIN']    = "Benutzer erhält Rechte zur Zertifikatverwaltung";
$message['_USERDATA_FROM_DATE']     = "从:";
$message['_USERDATA_TO_DATE']       = "进入:";
$message['_USERDATA_NEW_USER']      = "创建新用户。";
$message['_USERDATA_USER']          = "用户数据。";
$message['_USERDATA_OPTIONS']       = "选项。";


/** 
 * error messages
 * @uses class::Get_Lang
 * @param $var + array-id
 * @return Message in modal dialog
 */
$errormessage[1]                    = $message['_USERDATA_SAVE']." 失败. ".$message['_LOGIN_NAME']." 已有.";
$errormessage[2]                    = "Funktion noch nicht verfügbar. Sorry.";
$errormessage[3]                    = "新分配的密码不相同或为空!";
$errormessage[4]                    = "密码已更改 请再次登录!";

/**
 * footer gimmick from the Slogan kitchen (Sprücheküche) 
 */
$freedom = array(
                '宁愿选择安全也不选择自由的人，理所当然是奴隶。(亚里士多德)',
                '如果我们不鄙视一切试图将我们置于枷锁之下的东西，自由就会消亡。塞内卡。',
                '不要被它打倒，要大胆狂野，精彩纷呈。(皮皮龙袜)',
                '我更喜欢危险的自由，而不是安静的奴役。(卢梭)'
);



?>
