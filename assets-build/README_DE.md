## Lokale Bereitstellung der Frontend-Bibliotheken

OpenVPN-WebAdmin verwendet verschiedene Frontend-Bibliotheken, darunter AdminLTE, Bootstrap, jQuery, Bootstrap Table, Flatpickr und Bootstrap Icons.

Diese Bibliotheken werden nicht über externe CDNs eingebunden, sondern vollständig lokal innerhalb der Anwendung bereitgestellt.

Dadurch werden beim Aufruf der Weboberfläche keine Verbindungen zu externen CDN-Anbietern benötigt. Insbesondere werden keine IP-Adressen, Browserinformationen oder sonstigen Verbindungsdaten der Benutzer allein durch das Laden dieser Bibliotheken an externe Anbieter übertragen.

Die lokale Bereitstellung bietet mehrere Vorteile:

- **Datenschutz:** Keine CDN-bedingten Verbindungen zu externen Drittanbietern.
- **Unabhängigkeit:** Die Weboberfläche ist nicht von der Verfügbarkeit externer CDN-Dienste abhängig.
- **Kontrolle:** Die verwendeten Bibliotheken und Versionen werden zentral durch das Projekt festgelegt.
- **Schlanker Produktivserver:** Node.js, npm und die Build-Umgebung müssen nicht auf dem Produktivserver installiert werden.
- **Reproduzierbarkeit:** Die Abhängigkeiten werden über `package.json` und `package-lock.json` festgeschrieben.
- **Integritätsprüfung:** Die bereitgestellten Bibliotheken werden vor der Installation anhand einer SHA256-Prüfsumme überprüft.

### Aufbau und Ablauf

Die Bereitstellung erfolgt über drei aufeinander abgestimmte Shell-Scripts.

#### 1. build-tools-assets.sh – Erstellung des Asset-Pakets

Dieses Script wird auf dem Entwicklungsrechner ausgeführt.

Es übernimmt folgende Aufgaben:

- Installation der festgelegten npm-Abhängigkeiten.
- Zusammenstellung der tatsächlich benötigten Bibliotheksdateien.
- Kopieren der Dateien in ein separates Staging-Verzeichnis.
- Erstellung einer `manifest.json` mit den verwendeten Bibliotheksversionen.
- Verpackung der fertigen Dateien in ein komprimiertes TAR.GZ-Archiv.
- Erstellung einer SHA256-Prüfsumme für das Archiv.

Das Ergebnis ist das fertige Paket:

```text
release/
├── tools-assets.tar.gz
└── tools-assets.tar.gz.sha256
```

Die Build-Umgebung verbleibt vollständig auf dem Entwicklungsrechner. Sie ist kein Bestandteil des Produktivsystems.

#### 2. deploy-tools-assets.sh – Installation der Bibliotheken

Dieses Script übernimmt die Bereitstellung des zuvor erstellten Archivs auf dem Zielsystem.

Der Ablauf:

1. Prüfung des vorhandenen Archivs.
2. Überprüfung der SHA256-Prüfsumme, sofern eine Prüfsummendatei vorhanden ist.
3. Temporäres Entpacken des Archivs.
4. Prüfung der erwarteten Verzeichnisstruktur.
5. Übertragung der Dateien in das öffentliche Verzeichnis der Anwendung.
6. Synchronisierung mittels `rsync`.

Nicht mehr benötigte Dateien werden durch die Synchronisierung entfernt, sodass der installierte Bestand dem Inhalt des aktuellen Pakets entspricht.

Für diesen Vorgang werden weder Node.js noch npm benötigt.

#### 3. setup.sh – Automatische Integration in die Installation

Das zentrale Setup-Script übernimmt die Steuerung der Asset-Bereitstellung während der Installation von OpenVPN-WebAdmin.

Dabei wird zunächst geprüft, ob ein bereits vorbereitetes Asset-Archiv vorhanden ist.

**Fall A: Fertiges Asset-Archiv vorhanden**

- Das vorbereitete Archiv wird verwendet.
- Die Integrität wird anhand der SHA256-Prüfsumme überprüft.
- Die Bibliotheken werden über `deploy-tools-assets.sh` installiert.
- Node.js und npm werden nicht benötigt.

**Fall B: Kein fertiges Asset-Archiv vorhanden**

Falls kein Archiv vorhanden ist, kann optional ein automatischer Fallback aktiviert werden.

Über die Setup-Einstellung `ASSETS_ALLOW_NPM_FALLBACK` wird festgelegt, ob in diesem Fall ein lokaler Build durchgeführt werden darf.

Ist der Fallback aktiviert:

- Das Setup prüft, ob Node.js und npm vorhanden sind.
- Falls erforderlich, werden beide Pakete über die Debian-Paketverwaltung installiert.
- Anschließend wird `build-tools-assets.sh` ausgeführt.
- Die erzeugten Bibliotheken werden direkt danach über `deploy-tools-assets.sh` bereitgestellt.

Ist der Fallback deaktiviert, wird die Installation abgebrochen, wenn das vorbereitete Asset-Archiv fehlt.

### Empfehlung für Produktivsysteme

Für reguläre Produktivinstallationen wird empfohlen, das fertige Asset-Archiv bereits auf dem Entwicklungsrechner zu erstellen und zusammen mit dem Projekt bereitzustellen.

Die Einstellung

```bash
ASSETS_ALLOW_NPM_FALLBACK=no
```

verhindert, dass das Setup bei fehlendem Archiv automatisch Node.js und npm auf dem Produktivserver installiert.

Damit bleibt die Build-Umgebung vollständig vom Produktivsystem getrennt.

### Ergebnis

Die Weboberfläche verwendet ausschließlich lokal bereitgestellte Frontend-Bibliotheken.

Es sind keine externen CDN-Dienste erforderlich. Die Anwendung bleibt unabhängig von externen Anbietern und vermeidet unnötige Datenübertragungen an Dritte.

Node.js und npm werden ausschließlich für die Erstellung der Frontend-Dateien benötigt. Auf dem Produktivserver beschränkt sich die Installation auf die fertigen CSS-, JavaScript-, Schrift- und Bilddateien.

**Entwicklung und Produktivbetrieb bleiben damit konsequent voneinander getrennt.**