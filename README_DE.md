# OpenVPN WebAdmin 2.0.0

Sie schließen Ihre Haustür ab. Aber warum lassen Sie die Hintertüren offen? Genauso verhält es sich mit fast allen Internetzugängen zu Ihren IoT-Geräten, Webcams und anderen Geräten.

Erstellen und verwalten Sie Ihr virtuelles privates Netzwerk über einen Webbrowser und OpenVPN. Dieses System ist eine einfache und unkomplizierte Lösung für Ihre private Netzwerkverwaltung. Es eignet sich für Familien, Wohngemeinschaften oder Unternehmen, die Wert auf freie Software legen. Wenn Sie unabhängig von großen Cloud-Anbietern werden möchten und Ihnen die Sicherheit Ihrer Daten wirklich am Herzen liegt, ohne dass Sie Ihre Kommunikation gegenüber Geheimdiensten oder Datensammlern offenlegen müssen, sind Sie mit diesem System genau richtig.

## Offline assets for `/tools`

Der Ordner `/tools` ist die private Datenschutzvariante, statt Standard-Templates, CSS usw. von irgendwelchen externen Anbietern zu beziehen. Wenn du keine externen CDNs verwenden und `npm` nicht auf dem Webserver ausführen möchtest, dann findest du hier die Anleitung:

1. Build static frontend assets in a trusted build environment:
   `assets-build/scripts/build-tools-assets.sh`
2. Copy `assets-build/release/tools-assets.tar.gz` (+ `.sha256`) to the target server.
3. Deploy static files only:
   `assets-build/scripts/deploy-tools-assets.sh /srv/www/openvpn-webadmin/public`
4. Use local sitetools path in config:
   `'sitetools' => '/tools'`

See `assets-build/README.md` for details.

## Installation

Start with Code:

```code
bash <(wget -qO- 'https://raw.githubusercontent.com/Wutze/OpenVPN-WebAdmin/master/install.sh')
```

```bash
bash <(wget -qO- 'http://gitlab1.home/micro/openvpn-webadmin-intern/-/blob/main/setup.sh')
```

Alles andere wird dann automatisch abgefragt und installiert.

## Update (PHP, CSS, JS only, keep server config)

Use the update script to refresh application PHP files without touching runtime configuration files.

```bash
sudo ./update.sh
```

The updater:
- copies only `*.php|*.css|*.js` files from source to deploy target
- keeps `config/config.php` unchanged
- keeps `.env` unchanged
- does not run database migrations
