# OpenVPN WebAdmin 1.4.1

You lock your front door. But why do you leave the back entrances open? This is the same with almost all Internet accesses to your IoT, webcams and other devices.

Create and manage your virtual private network via web browser and OpenVPN. This system is a simple and easy method for your private user manager. The system is suitable for families, shared flats or companies that value free software. If you want to become independent from big cloud providers, if you really care about the security of your data without having to reveal the communication to secret services or data collectors, you will find your way with this system.

![GitHub OpenVPN-WebAdmin last commit](https://img.shields.io/github/last-commit/Wutze/OpenVPN-WebAdmin?style=plastic)
![GitHub OpenVPN-WebAdmin repo size](https://img.shields.io/github/repo-size/Wutze/OpenVPN-WebAdmin?style=plastic)
![GitHub OpenVPN-WebAdmin commits since latest release](https://img.shields.io/github/commits-since/Wutze/OpenVPN-WebAdmin/1.4.0?style=plastic)
![OpenVPN-WebAdmin Stable Branch](https://img.shields.io/badge/Master-Stable%20Branch-green)
![OpenVPN-WebAdmin Development Branch](https://img.shields.io/badge/Devel-Development%20Branch-yellow)

[![Twitter Follow](https://img.shields.io/twitter/follow/huwutze?color=blue&label=HuWutze&logo=Twitter&style=plastic)](https://twitter.de/HuWutze)
[![Liberapay patrons](https://img.shields.io/liberapay/patrons/Wutze?style=plastic)](https://liberapay.com/Wutze/)

## Extended documentation

[![Doku English OpenVPN-WebAdmin](https://img.shields.io/badge/Documentation-EN-%230034F0?style=plastic)](doc/doc.en_EN.md)
[![Doku Deutsch/German OpenVPN-WebAdmin](https://img.shields.io/badge/Dokumentation-DE-%230034F0?style=plastic)](doc/doc.de_DE.md)
[![Doku Französisch/French OpenVPN-WebAdmin](https://img.shields.io/badge/Dokumentation-FR-%230034F0?style=plastic)](doc/doc.fr_FR.md)

[![Update Doku OpenVPN-WebAdmin](https://img.shields.io/badge/Update--Doku--1.3.0-DE/EN/FR/RU-%230034F0?style=plastic)](doc/update-to-1.x.x.info.md)

## Webfrontend Übersetzungen vorhanden in

## Webfrontend translations available in

## Les traductions de Webfrontend sont disponibles en

## 有译文

## Traducción disponible en español (thank you @victorhck )

* Deutsch, German, en allemand, 德语
* Englisch, English, Anglais, 英文
* Französisch, French, en français, 法国人
* Chinesich (vereinfacht), Chinese (simplified), Chinois (simplifié), 中文（简体）
* Spanisch, Spanish, Espagnol, 西班牙, Español

## Live-Preview

[![Preview OpenVPN-WebAdmin](https://img.shields.io/badge/Preview-opvn.ddnss.eu-%23B10079?style=plastic)](http://ovpn.ddnss.eu/)

Login/Password: admin/admin or user/user

## Screenshots

Administrate its OpenVPN with a web interface (logs visualisations, users managing...) with MariaDB.

![Previsualisation Login OpenVPN-WebAdmin](doc/img/login.png)
![Previsualisation Administration OpenVPN-WebAdmin](doc/img/overview.png)
![Previsualisation Useradministration OpenVPN-WebAdmin](doc/img/useradmin.png)

## General note

This installation is mainly optimised for a server without further services. In particular, if you already have a MySQL server, you should read the documentation!

## Note CentOS 8

Without deactivated firewall the installation will not be completed. Only those who have a clean installation do not need a firewall.

## Prerequisite

* GNU/Linux with Bash and root access
* Fresh install of OpenVPN
* Web server (NGinx, Apache...)
* MariaDB (see note MySQL)
* PHP >= 7.x with modules:
  * zip
  * mysql
  * json
* yarn
* unzip
* wget
* sed
* curl
* git
* net-tools (for the includes simple firewall)

## Note MySQL

If you already have a database server, you can also use it and do not need to install it locally. You only need a database and a user name and password.

For a local installation of a MySQL server, you will automatically be asked to enter a root password.

## Tested on

* Debian 10/Buster, PHP 7.2.x, 10.3.22-MariaDB.
* RaspberryPi 4 with Debian Buster
* CentOS 8

Feel free to open issues. <https://github.com/Wutze/OpenVPN-WebAdmin/issues>

## Installation

![Previsualisation Setup](doc/img/setup.png)

* Currently you can choose from the languages German, English and French
* Setup OpenVPN and the web application:

````code
cd /opt/
git clone https://github.com/Wutze/OpenVPN-WebAdmin openvpn-admin
cd openvpn-admin

# You can also use the example configuration Configuration.
# The installation script will automatically adopt it.
cp installation/config.conf.sample installation/config.conf

# Edit your config.conf e.g. with nano
nano installation/config.conf

# start main installation
# Dependencies of the packages are automatically detected and installed.
./install.sh
````

### Note

The message appearing during the installation can simply be completed with Enter.

````bash
Common Name (eg: your user, host, or server name) [Easy-RSA CA]:
````

## Setup Webserver

Setup the web server (Apache, NGinx...) to serve the web application. Using the example below.

````code
nano /etc/apache2/sites-enabled/[ your apache config ]
````

See the example configurations at the end of this page.

## Attention

**You must reboot the server after installation, otherwise the vpn server will not start correctly and no connection will be established!**

The VPN server and especially the administrative web interface should never be directly connected to the Internet. The danger of compromising your own network is simply too great.

For this reason, finally create a port forwarding on your Internet Router to this VPN-Server. Check the documentation of the router manufacturer or search the Internet for instructions.

## Update

Change to the original installation folder _/opt/openvpn-admin_

````bash
cd /opt/openvpn-admin
git pull
bash ./update.sh
````

Follow the instructions

## OpenVPN-Clients and Documentation to install

### Apple iOS

* <https://apps.apple.com/us/app/openvpn-connect/id590379981>
* Documentation (German) <https://www.thomas-krenn.com/de/wiki/IOS_11_als_OpenVPN_Client_konfigurieren>

### Android

* <https://play.google.com/store/apps/details?id=de.blinkt.openvpn&hl=de>
* Go to download, download the zip file, unzip it into a separate folder, open the OpenVPN app and download the client.conf. Everything else happens automatically. Enter the password and you are ready to go.

### Windows 10

* <https://openvpn.net/client-connect-vpn-for-windows/>

The full functionality of OpenVPN under Windows 10 can unfortunately only be achieved by running the program under admin rights. This applies in particular to the routing into the VPN network, which does not work without admin rights. Additionally, the client version 3 of OpenVPN is in my opinion not usable to its full extent. For this reason I recommend, especially for people who want to know what they are doing and also want to adjust the configuration, the old version 2. Here is the direct link. <https://openvpn.net/downloads/openvpn-connect-v2-windows.msi>

### all

Looks at the configuration of the VPN app. If necessary, adjust the address of your gateway to the VPN server. Most routers can handle a free Dyn-DNS, so you only have to give the name, no IP address.

## Apache Example

````conf
<VirtualHost *:80>

        ServerAdmin webmaster@localhost
        DocumentRoot /srv/www/openvpn-admin

        # change Logfile Path or comment out in CentOS8
        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

AccessFileName .htaccess
<FilesMatch "^\.ht">
        Require all denied
</FilesMatch>

<Directory /srv/www/openvpn-admin/>
        Options Indexes FollowSymLinks
        AllowOverride all
        Require all granted
</Directory>

</VirtualHost>

````

## You can use SSL with your Apache (Example)

You can also use the server keys for the OpenVPN server to secure your website via HTTPS. The configuration for the web server will look like this. Enable ssl with "a2enmod ssl"

You can see with https:// [ website ] /

````conf
<VirtualHost *:443>

        ServerAdmin webmaster@localhost
        DocumentRoot /srv/www/openvpn-admin

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

AccessFileName .htaccess
<FilesMatch "^\.ht">
        Require all denied
</FilesMatch>

<Directory /srv/www/openvpn-admin/>
        Options Indexes FollowSymLinks
        AllowOverride all
        Require all granted
</Directory>

        SSLEngine On
        SSLProtocol all -SSLv2 -SSLv3
        SSLCipherSuite ECDH+AESGCM:DH+AESGCM:ECDH+AES256:DH+AES256:ECDH+AES128:DH+AES:ECDH+3DES:DH+3DES:RSA+AESGCM:RSA+AES:RSA+3DES:!aNULL:!MD5:!DSS
        SSLCertificateFile /etc/openvpn/server.crt
        SSLCertificateKeyFile /etc/openvpn/server.key

</VirtualHost>
````

## Special Thanks to external Coders

* <https://github.com/erusev/parsedown> for Markdown Parser Class in Modul Clientdownload
* Kate Morley - <http://iamkate.com/> for PHP Diff Class in Modul Client Configuration

### Hardware-Preview

![Previsualisation Preview OpenVPN-WebAdmin-Server](doc/img/19zoll.png)
