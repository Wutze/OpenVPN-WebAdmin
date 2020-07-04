# Update OpenVPN-WebAdmin

## Update von Version 0.8 + 1.0.x zu 1.1.0

### German

Wenn das Update-Script keine passende Konfiguration findet, Du aber dennoch eine Installation auf Deinem VPN-Server besitzt, dann musst Du eine eigene Konfigurationsdatei erstellen.

Lege bitte diese Datei an und speichere sie als _config.ovpn-admin.upd_ im Ordner _/var/lib/ovpn-admin/_ und fülle sie mit den entsprechenden Daten aus. Starte danach das Updatescript erneut.

````bash
DBHOST="db.home"
DBUSER="ovpn-name"
DBNAME="ovpn"
BASEPATH="ovpn-admin"
WEBROOT="/srv/www/"
WWWOWNER="www-data"
````

Während des Updates wirst Du nach einem neuen administrativen Benutzer gefragt. Bitte trage einen nicht vergebenen Benutzernamen ein, der für das nächste Login Admin-Rechte erhalten wird. Alle anderen User werden durch das Update nur noch normale Zugriffsrechte besitzen.

### English

If the update script does not find a suitable configuration, but you still have an installation on your VPN server, then you must create your own configuration file.

Please create this file and save it as _config.ovpn-admin.upd_ in the folder _/var/lib/ovpn-admin/_ and fill it out with the appropriate data. Afterwards, run the update script again.

````bash
DBHOST="db.home"
DBUSER="ovpn-name"
DBNAME="ovpn"
BASEPATH="ovpn-admin"
WEBROOT="/srv/www/"
WWWOWNER="www-data"
````

During the update you will be asked for a new administrative user. Please enter an unassigned username that will be given admin rights for the next login. All other users will only have normal access rights after the update.

### Russian

Если сценарий обновления не находит подходящей конфигурации, но у вас все еще есть инсталляция на вашем VPN сервере, то вы должны создать свой собственный файл конфигурации.

Пожалуйста, создайте этот файл и сохраните его как _config.ovpn-admin.upd_ в папке _/var/lib/ovpn-admin/_ и заполните соответствующими данными. После этого снова запустите скрипт обновления.

````bash
DBHOST="db.home"
DBUSER="ovpn-name"
DBNAME="ovpn"
BASEPATH="ovpn-admin"
WEBROOT="/srv/www/"
WWWOWNER="www-data"
````

Во время обновления у вас будет запрошен новый административный пользователь. Пожалуйста, введите неназначенное имя пользователя, которому будут даны права администратора при следующем входе в систему. Все остальные пользователи будут иметь обычные права доступа только после обновления.

### France

Si le script de mise à jour ne trouve pas de configuration appropriée, mais que vous avez quand même une installation sur votre serveur VPN, vous devez alors créer votre propre fichier de configuration.

Veuillez créer ce fichier et l'enregistrer sous le nom _config.ovpn-admin.upd_ dans le dossier _/var/lib/ovpn-admin/_ et le remplir avec les données appropriées. Ensuite, lancez à nouveau le script de mise à jour.

````bash
DBHOST="db.home"
DBUSER="ovpn-name"
DBNAME="ovpn"
BASEPATH="ovpn-admin"
WEBROOT="/srv/www/"
WWWOWNER="www-data"
````

Au cours de la mise à jour, il vous sera demandé un nouvel utilisateur administratif. Veuillez entrer un nom d'utilisateur non attribué qui recevra les droits d'administration pour la prochaine connexion. Tous les autres utilisateurs n'auront des droits d'accès normaux qu'après la mise à jour.
