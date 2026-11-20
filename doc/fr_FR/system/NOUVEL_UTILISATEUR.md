## Nouvel utilisateur

Chaque utilisateur se voit attribuer son propre certificat, qu'il doit utiliser pour se connecter à OpenVPN. Par conséquent, après avoir créé un nouvel utilisateur, il est indispensable d'exécuter le script

```bash
/opt/openvpn-webadmin-src/server/installation/openvpn-setup-easyrsa.sh
```
et de suivre les instructions.

### Codes LED

```code
● Vert - actif
● Rouge - désactivé
● Bleu - actif -> clés RSA absentes
● Gris - hors ligne
```

Les LED peuvent également clignoter. Pour un utilisateur actif, le clignotement bleu indique que le fichier de clé est manquant ou vide et que le script ci-dessus doit être exécuté. Un voyant gris dans la colonne « Actif » signifie que la vérification de la clé n'a pas pu être effectuée ; vérifiez le répertoire des certificats et ses permissions.

Le fichier vérifié est `<ID utilisateur>.key` dans `OPENVPN_CLIENT_CERT_DIR` (par défaut `/etc/openvpn/client-certificates`). La vérification confirme uniquement la présence du fichier de clé, et non la validité du certificat.
