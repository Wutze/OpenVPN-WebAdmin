# OpenVPN-WebAdmin 2.0.1

## Changes

- User option to enable or disable update notifications
- Improved update functionality with new version detection
- Setup adapted to the revised update structure

### Update Notifications

Update notifications can be enabled in `config.php`. This feature is optional and does not need to be enabled. Disabling this feature has no adverse effects. The only difference is that information about available new versions will no longer be displayed automatically.

Example configuration:

```php
'lang' => 'en_GB',
'update_check' => true,
'update_check_dns_host' => 'ovpn.361-grad.eu',
```
You can also identify the version yourself:

```bash
dig TXT ovpn.361-grad.eu +short
```

*Version 2.0.1*

---

# OpenVPN-WebAdmin 2.0.1

## Änderungen

- Benutzereinstellung zur Aktivierung bzw. Deaktivierung der Update-Benachrichtigungen
- Verbesserte Update-Funktion mit neuer Versionserkennung
- Setup an die überarbeitete Update-Struktur angepasst

### Update-Benachrichtigungen

Die Update-Informationen können in der `config.php` aktiviert werden. Diese Funktion ist optional und muss nicht aktiviert werden. Bei deaktivierter Funktion entstehen keine Nachteile. Lediglich die automatische Information über verfügbare neue Versionen entfällt.

Beispielkonfiguration:

```php
'lang' => 'de_DE',
'update_check' => true,
'update_check_dns_host' => 'ovpn.361-grad.eu',
```

Du kannst die Version auch selbst erkennen:

```bash
dig TXT ovpn.361-grad.eu +short
```

*Version 2.0.1*



