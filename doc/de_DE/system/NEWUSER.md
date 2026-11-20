## Neuer Benutzer

Jeder Benutzer bekommt sein eigenes Zertifikat ausgestellt, mit dem er sich am OpenVPN anmelden muss. Deswegen ist nach dem anlegen eines neuen Benutzers zwingend das Script:

```bash
/opt/openvpn-webadmin-src/server/installation/openvpn-setup-easyrsa.sh
```
ausführen.

### LED-Codes

```code
● Grün - aktiv  
● Rot - deaktiviert  
● Blau - aktiv -> keine RSA Keys  
● Grau - offline
```

Bei einem aktiven Benutzer bedeutet blaues Blinken, dass die Schlüsseldatei fehlt oder leer ist und das obige Skript ausgeführt werden muss. Grau in der Spalte „Aktiv“ bedeutet, dass die Schlüsselprüfung nicht möglich war; das Zertifikatsverzeichnis und dessen Berechtigungen prüfen. Geprüft wird die Datei `<Benutzer-ID>.key` in `OPENVPN_CLIENT_CERT_DIR` (standardmäßig `/etc/openvpn/client-certificates`). Die Prüfung bestätigt nur
das Vorhandensein der Schlüsseldatei, nicht die Gültigkeit des Zertifikats.
