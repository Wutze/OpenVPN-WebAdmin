#!/usr/bin/env bash
# this File is part of OpenVPN-WebAdmin - (c) 2026 OpenVPN-WebAdmin
#
# NOTICE OF LICENSE
#
# GNU AFFERO GENERAL PUBLIC LICENSE V3
# that is bundled with this package in the file LICENSE.md.
# It is also available through the world-wide-web at this URL:
# https://www.gnu.org/licenses/agpl-3.0.en.html
#

#
# @author    Wutze
# @copyright 2026 OpenVPN-WebAdmin
# @link			https://github.com/Wutze/OpenVPN-WebAdmin
# @see				Internal Documentation ~/doc/
# @version		2.0.0
# @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues

#
# OpenVPN-WebAdmin
# Benutzer- und Passwortauthentifizierung
# Individuelle IP-Zuweisung über CCD
#

umask 027
set -o pipefail

# --------------------------------------------------
# Konfiguration
# --------------------------------------------------

. /etc/openvpn/scripts/config.sh
. /etc/openvpn/scripts/functions.sh

CCD_DIR="/etc/openvpn/ccd"

# --------------------------------------------------
# Logging, to original openvpn.log
# --------------------------------------------------

ovpn_log() {
    echo "$(LC_TIME=C date '+%a %b %d %H:%M:%S %Y') [OVPN] $*" >> /var/log/openvpn/openvpn.log
}

# --------------------------------------------------
# Benutzer übernehmen
# --------------------------------------------------

username="${username:-}"
password="${password:-}"
certificate_cn="${common_name:-}"

ovpn_log "Start Login Process for: $username: [INFO]"
ovpn_log "$username: certificate CN received: ${certificate_cn:-<empty>} [INFO]"

if [[ -z "$username" || -z "$password" || -z "$certificate_cn" ]]; then
    ovpn_log "$username: authentication: [ERROR] missing credentials or certificate identity"
    exit 1
fi

# Benutzername als CCD-Dateiname absichern
if [[ ! "$username" =~ ^[a-zA-Z0-9][a-zA-Z0-9_.@-]*$ ]]; then
    ovpn_log "$username: authentication: [ERROR] invalid username"
    exit 1
fi

username_sql=$(echap "$username")

# Bestehende Session-ID-Erzeugung
session_id=$(dbus-uuidgen)

# --------------------------------------------------
# Datenbankverbindung
# --------------------------------------------------

if [[ -z "${DBHOST:-}" || -z "${DBUSER:-}" ||
      -z "${DBNAME:-}" || -z "${DBPASS:-}" ]]; then
    ovpn_log "$username: authentication: [ERROR] database configuration missing"
    exit 1
fi

db_query() {
    MYSQL_PWD="$DBPASS" mysql \
        --protocol=TCP \
        --connect-timeout=5 \
        --batch \
        --raw \
        --skip-column-names \
        --host="$DBHOST" \
        --port="$DBPORT" \
        --user="$DBUSER" \
        "$DBNAME" \
        --execute="$1" 2>/dev/null
}

# --------------------------------------------------
# Authentifizierung
# --------------------------------------------------

if ! user_data=$(db_query "
    SELECT user.uid, user.user_pass
    FROM user
    WHERE user_name = '$username_sql'
      AND user_enable = 1
      AND (TO_DAYS(now()) >= TO_DAYS(user_start_date)
           OR user_start_date IS NULL)
      AND (TO_DAYS(now()) <= TO_DAYS(user_end_date)
           OR user_end_date IS NULL)"); then
    ovpn_log "$username: authentication: [ERROR] database error"
    exit 1
fi


if [[ -z "$user_data" || "$user_data" == *$'\n'* ]]; then
    ovpn_log "$username: authentication: [ERROR] bad account or unexpected query result"
    exit 1
fi


IFS=$'\t' read -r user_id user_pass extra <<< "$user_data"

if [[ -z "$user_id" || -z "$user_pass" || -n "${extra:-}" ]]; then
    ovpn_log "$username: authentication: [ERROR] invalid user database result"
    exit 1
fi


# --------------------------------------------------
# Zertifikat dem Datenbankbenutzer zuordnen
# --------------------------------------------------
if [[ "$certificate_cn" != "$user_id" ]]; then
    ovpn_log "$username: authentication: [ERROR] certificate does not belong to user"
    exit 1
fi

# NEU: Zertifikatsdatei auf Existenz und Ablaufdatum prüfen
CERT_FILE="/etc/openvpn/easy-rsa/pki/issued/${user_id}.crt"

if [[ ! -f "$CERT_FILE" ]]; then
    ovpn_log "$username: authentication: [ERROR] certificate file not found for UID=$user_id"
    exit 1
fi

if ! openssl x509 -in "$CERT_FILE" -noout -checkend 0 >/dev/null 2>&1; then
    ovpn_log "$username: authentication: [ERROR] certificate expired (CN=$certificate_cn, UID=$user_id)"
    exit 1
fi

ovpn_log "$username: certificate identity verified and valid (CN=$certificate_cn, UID=$user_id) [OK]"



# --------------------------------------------------
# Passwort überprüfen
# --------------------------------------------------

if printf '%s\0%s' "$password" "$user_pass" |
    php -r '
        $data = stream_get_contents(STDIN);
        $pos = strpos($data, "\0");

        if ($pos === false) {
            exit(2);
        }

        $password = substr($data, 0, $pos);
        $hash = substr($data, $pos + 1);

        try {
            exit(password_verify($password, $hash) ? 0 : 1);
        } catch (Throwable $e) {
            exit(2);
        }
    '
then
    result="ok"
else
    php_result=$?
    result="ko"
fi

unset password user_pass user_data

if [[ "$result" != "ok" ]]; then
    if [[ "${php_result:-1}" -eq 2 ]]; then
        ovpn_log "$username: authentication: [ERROR] password verification error"
    else
        ovpn_log "$username: authentication: [ERROR] failed"
    fi
    exit 1
fi

# --------------------------------------------------
# Benutzer-IP ermitteln (optional)
# --------------------------------------------------

if ! IP=$(db_query "
    SELECT CONCAT(COALESCE(user_ip.user_ip, ''), '|', COALESCE(user_ip.server_ip, ''))
    FROM user AS user
    LEFT JOIN user_ip AS user_ip ON user_ip.uid = user.uid
    WHERE user.user_name = '$username_sql'
"); then
    ovpn_log "$username: authentication: [ERROR] IP database query failed"
    exit 1
fi


if [[ "$IP" == *$'\n'* ]]; then
    ovpn_log "$username: authentication: [ERROR] multiple IP assignments"
    exit 1
fi


IFS='|' read -r client_ip server_ip extra <<< "$IP"

if [[ -n "${extra:-}" ]]; then
    ovpn_log "$username: authentication: [ERROR] incomplete IP assignment"
    exit 1
fi


# CCD-Verzeichnis prüfen
if [[ ! -d "$CCD_DIR" ]]; then
    ovpn_log "$username: authentication: [ERROR] CCD directory missing"
    exit 1
fi

CCD_FILE="${CCD_DIR}/${username}"
TMP_FILE=""

cleanup() {
    if [[ -n "$TMP_FILE" && -e "$TMP_FILE" ]]; then
        rm -f -- "$TMP_FILE"
    fi
}


trap cleanup EXIT


# --------------------------------------------------
# Feste IP oder dynamische Pool-Zuweisung
# --------------------------------------------------

if [[ -z "$client_ip" && -z "$server_ip" ]]; then
    # Keine feste IP hinterlegt: dynamischen OpenVPN-Pool verwenden.
    if [[ -e "$CCD_FILE" ]]; then
        if ! rm -f -- "$CCD_FILE"; then
            ovpn_log "$username: authentication: [ERROR] stale CCD file could not be removed"
            exit 1
        fi
    fi

    ovpn_log "$username: dynamic IP assignment [INFO]"

elif [[ -n "$client_ip" && -n "$server_ip" ]]; then
    # Feste IP-Zuweisung: beide Werte müssen gültige IPv4-Adressen sein.
    if ! php -r '
        exit(
            filter_var($argv[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false &&
            filter_var($argv[2], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false
            ? 0 : 1
        );
    ' "$client_ip" "$server_ip"; then
        ovpn_log "$username: authentication: [ERROR] invalid IP address"
        exit 1
    fi

    if [[ ! -w "$CCD_DIR" ]]; then
        ovpn_log "$username: authentication: [ERROR] CCD directory not writable"
        exit 1
    fi


    # CCD-Datei atomar erstellen
    TMP_FILE=$(mktemp "${CCD_DIR}/.${username}.XXXXXX") || {
        ovpn_log "$username: authentication: [ERROR] could not create CCD file"
        exit 1
    }


    if ! printf 'ifconfig-push %s %s\n' "$client_ip" "$server_ip" > "$TMP_FILE"; then
        ovpn_log "$username: authentication: [ERROR] could not write CCD file"
        exit 1
    fi


    chmod 0640 "$TMP_FILE" || {
        ovpn_log "$username: authentication: [ERROR] could not set CCD permissions"
        exit 1
    }


    mv -f -- "$TMP_FILE" "$CCD_FILE" || {
        ovpn_log "$username: authentication: [ERROR] could not activate CCD file"
        exit 1
    }


    TMP_FILE=""
    ovpn_log "$username: set fixed IP: $client_ip $server_ip [OK]"

else
    # Nur eine der beiden IP-Adressen vorhanden: Datensatz ist unvollständig.
    ovpn_log "$username: authentication: [ERROR] incomplete IP assignment"
    exit 1
fi


# --------------------------------------------------
# Erfolgreich
# --------------------------------------------------

ovpn_log "$username: authentication: [OK]"

exit 0
