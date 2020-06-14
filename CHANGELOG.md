# OpenVPN-Admin Version History

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
