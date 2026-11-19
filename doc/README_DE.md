# Übersicht

## Installation

### Systemvoraussetzungen

Getestet und entwickelt unter **Debian 13**. Es genügt ein "nacktes" Betriebssystem mit wget, das Installations-Script installiert automatisch alle notwendigen Bibliotheken, je nach Auswahl eurer Setup-Konfigurastion.

Das Setup benötigt eine **konfigurierte MariaD Datenbank**, lokal oder remote. Es muss ein Benutzer und eine Tabelle mit dem entsprechenden Benutzer existieren, damit das Setup ordnungsgemäß funktioniert.

## Setup 

Starte das Setup als root:

```code
bash <(wget -qO- 'https://raw.githubusercontent.com/Wutze/OpenVPN-WebAdmin/master/install.sh')
```

Das Setup ist mehrsprachig aufgebaut.

Folge den Fragen und Anweisungen, damit wird das OpenVPN-WebAdmin Frontend zuerst installiert. Im Moment ist nur die Konfiguration für Apache getestet, Nginx wird folgen. Du kannst zwischen verschiedenen Optionen auswählen, sie sind im Setup hoffentlich selbsterklärend.

Sobald das Setup erfolgreich beendet ist, der Apache-Webserver erfolgreich konfiguriert wurde, kannst du dich mit den zuvor eigegebenen Daten einloggen.

![OpenVPN-WebAdmin Setup](doc/img/setup-demo.gif)

### Setup openvpn und rsa keys

Du führst jetzt einfach aus:

```code
/opt/openvpn-webadmin-src/server/installation/openvpn-setup-easyrsa.sh
```

Das Setup stellt sicher, dass jetzt easy-rsa installiert wird und der openvpn Server. Da bisher noch keine Keys generiert worden sind, fragt das Setup einige Daten ab, die wichtig sind um die Zertifikate auszustellen. Das Script wird dann durchaus einige Minuten zu tun haben, da die Datei `dh.pem` immer einige Zeit benötigt.

Ist das Setup durchgelaufen, hast du eine funktionierende OpenVPN Konfiguration, mit der du dich aber noch nicht verbinden kannst. Du musst noch Benutzer anlegen und die Schlüssel erzeugen.

### Benutzer anlegen

Du musst - sobald neue Benutzer angelegt worden sind - das `openvpn-setup-easyrsa.sh` erneut ausführen, um die Benutzerzertifikate auszustellen.

```code
/opt/openvpn-webadmin-src/server/installation/openvpn-setup-easyrsa.sh
```

Du hast zur Auswahl das Serverzetifikat zu erneuern oder die zertifikate für die Benutzer erstellen zu lassen. Die Daten werden aus der Datenbank bezogen und entsprechend sicher abgelegt.

## Systemupdate

Das Systemupdate kannst du jederzeit durchführen mit:

```bash
/opt/openvpn-webadmin-src/server/installation/update.sh
```

Dein lokales Repository unter `/opt/openvpn-webadmin-src/` sollte jetzt aktualisiert werden. Aus diesem Repo wird nun deine Webseite aktualisiert. Deine Konfigurationen werden nicht angetastet. Ebenfalls deine eigene Style.css usw. Es ist dein System, verändere was du möchstes. ,o)

Es werden keine zusätzlichen Dateien gelöscht, nur veränderte Systemdateien werden ohne Rückfrage durch die aktuelle Version ersetzt.

### Hinweis

Einige von Euch werden sicherlich Gefallen an den Hilfetexten finden. Noch habe ich keine Lösung/Idee gefunden, wie auch das durch eigene Ergänzungen, eigene Hilfetexte aufgebaut werden kann, ohne dass ein Update diese Einträge kaputt macht. Es steht jedoch auf meiner Wishlist.

