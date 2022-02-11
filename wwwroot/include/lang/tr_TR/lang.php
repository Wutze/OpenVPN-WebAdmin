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
 * Language tr_TR
 */

$message['_NO_USER']                = "Bilnmeyen kullanıcı adı";
$message['_SITE_TITLE']             = "Sayfa başlığı";
$message['_LANGUGE_ERROR']          = "Dil dosyasında hata";
$message['_NEED_JAVASCRIPT']        = "Üzgünüm, JavaScript gereklidir!";

$message['_HOME']                   = "Başlangıç";
$message['_OVERVIEW']               = "Genel Bakış";
$message['_INFOS_PLUS']             = "Ek bilgiler";
$message['_WELCOME']                = "Hoşgeldiniz";

$message['_USERS']                  = "Kullanıcı";
$message['_DISK']                   = "Disk";
$message['_SYSTEM']                 = "Sistem";
$message['_ERROR']                  = "Hata";
$message['_SAVE']                   = "Kaydet";
$message['_RESTART']                = "Dikkat! Değişiklerinden sonra sunucu veya istemciyi yeniden başlatın!";
$message['_NEW']                    = "Yeni";

$message['_U_NAME']                 = "Kullancı";
$message['_U_GROUP']                = "Grup";
$message['_U_ENABLE']               = "İzin ver";
$message['_U_FROM']                 = "kimden";
$message['_U_TO']                   = "kime";
$message['_U_ONLINE']               = "Aktif";
$message['_U_NETIP']                = "Kullanıcı IP-Adresi";
$message['_U_GATEWAYIP']            = "Gateway IP Sunucu";
$message['_U_EXTEND_VIEW']          = "Gelimiş görünüm: ";
$message['_U_TIMESETTINGS']         = "Süre limitleri";
$message['_U_NETSETTINGS']          = "Ağ";
$message['_U_PLUS']                 = "Diğer (Deaktif)";

$message['_ATTENTION_FW']           = "ne yağtığınızı bilyor olmalısınız!";
$message['_ATTENTION_CF']           = "Değişiklerinden sonra sunucu ve/veya istemci yeniden başlatılmlı";

$message['_VIEW']                   = "görünüm";

$message['_LOGIN']                  = "Giriş";
$message['_LOGIN_NAME']             = "Kullanıcı";
$message['_LOGIN_PASS']             = "Şifre";
$message['_LOGIN_DATA']             = "Lütfen giriş bilgilerini giriniz";
$message['_LOGIN_SAVE']             = "beni hatırla";
$message['_LOGOUT']                 = "X";
$message['_YOUR_LOGIN']             = "Hesabınız";
$message['_USER_RIGHTS']            = "Kullanıcı haklarınız";

$message['_SAVE_PW']                = "Kaydet";
$message['_LOGIN_PASS_CONTROL']     = "Kontrol";
$message['_CHANGE_PASS']            = "Şifre değiştir";
$message['_INPUT_NEW_PASS']         = "Yeni şifre";
$message['_RETYPE_NEW_PASS']        = "Tekrar yeni şifre";
$message['_USER_EMAIL']             = "ePosta adresiniz";
$message['_USER_NAME']              = "İsminiz";
$message['_USER_DATA']              = "Bilgileri değiştir";
$message['_SAVE_USER_DATA']         = "Değişiklikleri kaydet";

$message['_VPN_CONFIGS']            = "Ayarlar";
$message['_OSX_CONFIG']             = "OSX Ayarları";
$message['_WIN_CONFIG']             = "WIN Ayarları";
$message['_LIN_CONFIG']             = "Android Ayarları";

$message['_CONF_SAVED']             = "Ayarlar kaydedildi";
$message['_CONF_SAVE_ERROR']        = "Kayıt hatası";

$message['_SSL_CERTS']              = "SSL-Sertifikaları";
$message['_SSL_CERTS_NEW']          = "Yeni Sertifika";
$message['_SSL_CERTS_EDIT']         = "Sertifikaları Düzenle";
$message['_SSL_CERTS_LIST']         = "Sertifikaları Listele";

$message['_TODAY']                  = date('Y.m.d');

$message['_VPN_DATA_SAV']           = "Kaydet ...";
$message['_VPN_DATA_TT']            = "İstemci tanımlamasını seçin ve indirin";
$message['_VPN_DATA_OSX']           = "OSX-Ayarı";
$message['_VPN_DATA_WIN']           = "WIN-Ayarı";
$message['_VPN_DATA_LIN']           = "Android/Linux-Ayarı";
$message['_VPN_CLIENT_SAV']         = "Doküman/İndir ...";
$message['_VPN_CLIENT_TT']          = "Doküman/VPN istemci indir ...";
$message['_VPN_CLIENT_OSX']         = "OSX";
$message['_VPN_CLIENT_WIN']         = "WIN";
$message['_VPN_CLIENT_LIN']         = "Andoid";
$message['_VPN_CLIENT_EXT']         = "Daha fazla bilgi";


$message['_USERDATA_EMAIL']         = "ePosta";
$message['_USERDATA_PASSWORD']      = "Şifre";
$message['_USERDATA_SAVE']          = "Kullanıcı oluştur";
$message['_USERDATA_ISADMIN']       = "Kullanıcı tüm yönetici yetkilerini alır";
$message['_USERDATA_ISCONFIGADMIN'] = "Kullanıcı ayar yetkilerini alır";
$message['_USERDATA_ISLOGADMIN']    = "Kullanıcı log izleme yetkilerini alır";
$message['_USERDATA_ISUSERADMIN']   = "Kullanıcı ekleme yetkilerini alır";
$message['_USERDATA_ISTLSADMIN']    = "Kullanıcı sertifika yönetim yetkilerini alır";
$message['_USERDATA_FROM_DATE']     = "Erişim tarihi:";
$message['_USERDATA_TO_DATE']       = "Erişim tarihi:";
$message['_USERDATA_NEW_USER']      = "Yeni kullanıcı oluştur";
$message['_USERDATA_USER']          = "Kullanıcı verileri";
$message['_USERDATA_OPTIONS']       = "Seçenekler";


/** 
 * error messages
 * @uses class::Get_Lang
 * @param $var + array-id
 * @return Message in modal dialog
 */
$errormessage[1]                    = $message['_USERDATA_SAVE']." hatalı veya boş şifre";
$errormessage[2]                    = "Fonksiyon henüz mevcut değil. Üzgünüz.";
$errormessage[3]                    = "Yeni şifre uygun değil veya boş!";
$errormessage[4]                    = "Şifre değişti! Lütfen yeniden giriş yapın!";

$freedom = array(
                'Anyone who prefers security over freedom is rightly a slave. (Aristoteles)',
                'Die Freiheit geht zugrunde, wenn wir nicht alles verachten, was uns unter ein Joch beugen will. (Seneca)',
                'Don\'t let it get you down; be bold and wild and wonderful. (Pipi Langstrumpf)',
                'I prefer dangerous freedom to quiet servitude. (Rousseau)',
                'Yurtta sulh, Cihanda sulh (Atatürk)'
);



?>
