## New user

Each user is issued with their own certificate, which they must use to log in to OpenVPN. Therefore, after creating a new user, it is essential to run the script

```bash
/opt/openvpn-webadmin-src/server/installation/openvpn-setup-easyrsa.sh
```
and follow the instructions.

```code
● green - active  
● red - disable  
● blue - active -> missing RSA Keys  
● gray - offline
```

In the Active column, a green LED indicates an active user with a key; a
blinking blue LED indicates a missing or empty key and that the script must be
run. Disabled users remain red. A grey LED in this column means the key
could not be checked; check the certificate directory and its permissions.
The check looks for `<user-id>.key` in `OPENVPN_CLIENT_CERT_DIR` (default
`/etc/openvpn/client-certificates`). It checks key presence, not certificate
validity.
