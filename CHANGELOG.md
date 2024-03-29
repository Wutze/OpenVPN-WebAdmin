# OpenVPN-WebAdmin Version History

## 1.4.2

Sorry, only new installation. The version with the updates from 1.4.0 to currently 1.4.2 will only be available with 1.5.0.

- Display OpenVPN-Server Version in the overview
- The OpenVPN server is now installed directly by the maintainer Openvpn. The update cycles of the respective distributions simply take too long. Security comes first.
- Update nodejs 10 to 12
- added Spanish lang (es_ES) in web frontend/installation (Many thanks to @victorhck)
- Changes in the installation procedure

## 1.4.1

- Installation script completely rebuilt
- Clearer structure
- Clearer variables
- many errors eliminated
- Unique designations
- Better comments
- Messages and error messages improved
- Create a log file for better script monitoring
- Improvements in translations
- CentOS8 installation now fully supported and tested!

## 1.4.0

- Required modules (node_modules and ADOdb) created outside the Web folder to make backup easier.
- Renamed some column names in the database for better recognition.
- New modul "clientload" for your User-Support (see readme.md in clientload module folder)
- Modifications in your User-Administration
- Clearer HTML Code for debugging
- Now fully supported by centos (installation and update)
- Various small things fixed
- unnecessary files removed
- Chinese language added for multilanguage support on the website (now (DE/FR/EN/CN))
- Modularisation further simplified for subsequent versions

## 1.3.0

- fix bug in sortable log-tables
- moved, add or delete files (The big clean up)
- clean up code
- change css styles for better look
- add module firewall to edit firewall settings
- fix bugs in update.sh
- add Development Doku
- add Coding Conventions (for the monent only in German)
- change Documentaions
- more translations inserted (DE/FR/EN)

## 1.2.0

### Installation/Update Script

- moved all files and folders for webservice and installation into separate folder
- Install code cleaned up
- Additions and changes in the Install and Update Script

### Webservice

- add multilanguage functions for webservice
- Several bugs fixed
- add debug function for developers
- Preparation for module functions started and installed
- php and html Code cleaned up
- added own error pages into login folder
- Message after saving the configurations now better visible

### VPN-Server

- OpenVPN-Server VPN-Loging log modified. Messages now clearly visible and searchable
- added multitail plugin for logfile (installation/snippets)

## 1.1.1

- bug #19 fixed, rename database based vars

## 1.1.0

- add updatescript from version 0.8 and 1.0.x
- fixed various bugs

## 1.0.0

- new design based on admin-lte
- change to newest boostrap or js versions (see package.json)
- add User self-administration passwords, mail and others
- New code design. All functions are now outside the HTML structure
- central http request broker
- central config.file and separate load.file
- mulitilanguge website (DE/EN)
- change database abstraction layer to ADOdb
- new session layer
- insert system overview for admins
- Users can now see their own vpn login history
- Personalization Logins

## 0.7

- Insert Multilingual Support de_DE and en_EN (Other translations are possible)
- Largely automated installation
- fix mysql error without local Server-Installation
- You can now specify the Database name
- change color-settings webfrontend openvpn.config
- insert new Messages and Informations
- Fix #145 in openvpn/scripts/config.sh
- Obsolete routines removed

## 0.6

- change bower to yarn
- now with Web-Setup OpenVPN server.conf
- Raspeberry Pi with Debian 10 tested
- Configuration files no longer in web folder

## 0.5

- sql files replaced
- new config.sample file
- Installation adapted to Debian 10 - bare/naked installation testet
- Completely revised install.sh
- Setup now complete via install.sh and remove Web-Setup
- New Documentation

## 0.3.2

- Fix with MySQL NO_ZERO_DATE mode

## 0.3.1

- Fix path issues

## 0.3.0

- Add title to webpage
- Improve design and user experience
- Add redirections (after install...)
- Upgrade to EasyRSA 3.x
- Files are in a subdirectory in the Zip configuration
