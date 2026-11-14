#!/usr/bin/env bash
# OpenVPN-WebAdmin: OpenVPN and Easy-RSA setup / client certificate import.

set -Eeuo pipefail

OPENVPN_DIR=/etc/openvpn
EASYRSA_DIR="${OPENVPN_DIR}/easy-rsa"
SCRIPTS_DIR="${OPENVPN_SCRIPTS_DIR:-/etc/openvpn/scripts}"
CLIENT_DIR="${OPENVPN_CLIENT_CERT_DIR:-/etc/openvpn/client-certificates}"
PKI_DIR="${EASYRSA_DIR}/pki"
EASYRSA_BIN=""
LANG_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/lang"
LANG_CHOICE=""

select_language() {
  local auto_lang="en_GB" choice=""
  case "${LANG%%.*}" in de_DE*) auto_lang=de_DE ;; fr_FR*) auto_lang=fr_FR ;; en_*) auto_lang=en_GB ;; esac
  [ -f "${LANG_DIR}/de_DE" ] || { printf 'Sprachdatei fehlt: %s\n' "${LANG_DIR}/de_DE" >&2; exit 1; }
  # Keep newly introduced messages available until translations are added to
  # the other language files; their existing entries can still override these.
  # shellcheck disable=SC1091
  source "${LANG_DIR}/de_DE"
  if command -v whiptail >/dev/null 2>&1; then
    choice="$(whiptail --title 'OpenVPN-WebAdmin Setup' --menu "$MSG_SETUP_RSA_LANGUAGE_SELECT" 14 72 4 \
      auto "Auto (${auto_lang})" de_DE Deutsch en_GB English fr_FR Francais 3>&1 1>&2 2>&3)" || exit 1
  else
    printf '%s\n' "$MSG_SETUP_RSA_LANGUAGE_SELECT"
    read -r -p "$MSG_SETUP_RSA_LANGUAGE_PROMPT " choice
    case "$choice" in 1) choice=de_DE ;; 2) choice=en_GB ;; 3) choice=fr_FR ;; *) choice=auto ;; esac
  fi
  case "$choice" in de_DE|en_GB|fr_FR) LANG_CHOICE="$choice" ;; *) LANG_CHOICE="$auto_lang" ;; esac
  [ -f "${LANG_DIR}/${LANG_CHOICE}" ] || { printf 'Sprachdatei fehlt: %s\n' "${LANG_DIR}/${LANG_CHOICE}" >&2; exit 1; }
  # shellcheck disable=SC1090
  [ "$LANG_CHOICE" = de_DE ] || source "${LANG_DIR}/${LANG_CHOICE}"
}

say() { printf '[%s] %s\n' "$1" "$2"; }
die() { say "${MSG_SETUP_RSA_ERROR:-ERROR}" "$*" >&2; exit 1; }
need_root() { [ "$(id -u)" -eq 0 ] || die "${MSG_SETUP_RSA_MUSTROOT:-Run this script as root.}"; }
ask() {
  local prompt="$1" default="$2" answer
  read -r -p "${prompt} [${default}]: " answer
  printf '%s' "${answer:-$default}"
}

on_error() {
  local rc=$?
  say "${MSG_SETUP_RSA_ERROR:-ERROR}" "${MSG_SETUP_RSA_FAILED_LINE:-Fehler in Zeile} ${BASH_LINENO[0]:-?} (${MSG_SETUP_RSA_EXIT_CODE:-Exit-Code} ${rc}). ${MSG_SETUP_RSA_DETAILS_HINT:-Details stehen gegebenenfalls in der Easy-RSA-Ausgabe oberhalb.}" >&2
  exit "$rc"
}
trap on_error ERR

install_if_missing() {
  local package="$1" command_name="$2"
  if command -v "$command_name" >/dev/null 2>&1 || { [ "$command_name" = easyrsa ] && { [ -x /usr/share/easy-rsa/easyrsa ] || [ -x /usr/share/easy-rsa/3/easyrsa ]; }; }; then
    say INFO "$(printf "$MSG_SETUP_RSA_PACKAGE_PRESENT" "$package")"
  else
    command -v apt-get >/dev/null 2>&1 || die "$MSG_SETUP_RSA_APT_MISSING"
    say INFO "$(printf "$MSG_SETUP_RSA_INSTALL_PACKAGE" "$package")"
    apt-get update
    DEBIAN_FRONTEND=noninteractive apt-get install -y "$package"
  fi
}

find_easyrsa() {
  if command -v easyrsa >/dev/null 2>&1; then EASYRSA_BIN=$(command -v easyrsa); return; fi
  for candidate in /usr/share/easy-rsa/easyrsa /usr/share/easy-rsa/3/easyrsa; do
    if [ -x "$candidate" ]; then EASYRSA_BIN=$candidate; return; fi
  done
  die "$MSG_SETUP_RSA_EASYRSA_NOT_FOUND"
}

prepare_vars() {
  local country province city org email ou days
  country=$(ask COUNTRY "$MSG_SETUP_RSA_COUNTRY" DE)
  province=$(ask PROVINCE "$MSG_SETUP_RSA_PROVINCE" 'NRW')
  city=$(ask CITY "$MSG_SETUP_RSA_CITY" 'Dortmund')
  org=$(ask ORG "$MSG_SETUP_RSA_ORG" 'OpenVPN-WebAdmin')
  email=$(ask EMAIL "$MSG_SETUP_RSA_EMAIL" 'admin@example.home')
  ou=$(ask OU "$MSG_SETUP_RSA_OU" 'xOpenVPN')
  days=$(ask "$MSG_SETUP_RSA_VALIDITY_DAYS" '3650')
  [[ "$country" =~ ^[A-Za-z]{2}$ ]] || die "$MSG_SETUP_RSA_COUNTRY_INVALID"
  [[ "$days" =~ ^[0-9]+$ ]] && [ "$days" -gt 0 ] || die "$MSG_SETUP_RSA_VALIDITY_INVALID"
  install -d -m 0700 "$EASYRSA_DIR"
  {
    printf 'set_var EASYRSA_REQ_COUNTRY %q\n' "$country"
    printf 'set_var EASYRSA_REQ_PROVINCE %q\n' "$province"
    printf 'set_var EASYRSA_REQ_CITY %q\n' "$city"
    printf 'set_var EASYRSA_REQ_ORG %q\n' "$org"
    printf 'set_var EASYRSA_REQ_EMAIL %q\n' "$email"
    printf 'set_var EASYRSA_REQ_OU %q\n' "$ou"
    printf 'set_var EASYRSA_REQ_CN %q\n' 'OpenVPN-WebAdmin'
    printf 'set_var EASYRSA_CERT_EXPIRE %q\n' "$(( (days + 364) / 365 ))"
    printf 'set_var EASYRSA_CA_EXPIRE %q\n' "$(( (days + 364) / 365 ))"
  } > "${EASYRSA_DIR}/vars"
  chmod 0600 "${EASYRSA_DIR}/vars"
  export EASYRSA_CERT_EXPIRE=$(( (days + 364) / 365 ))
}

easyrsa_run() {
  (cd "$EASYRSA_DIR" && EASYRSA_PKI="$PKI_DIR" EASYRSA_BATCH=1 EASYRSA_REQ_CN="${EASYRSA_REQ_CN:-OpenVPN-WebAdmin}" "$EASYRSA_BIN" "$@")
}

ensure_client_dir() {
  install -d -o root -g www-data -m 0750 "$CLIENT_DIR"
}

first_setup() {
  say INFO "$MSG_SETUP_RSA_FIRST_SETUP"
  prepare_vars
  install -d -m 0700 "$PKI_DIR"
  easyrsa_run init-pki
  easyrsa_run build-ca nopass
  EASYRSA_REQ_CN=server easyrsa_run gen-req server nopass
  easyrsa_run sign-req server server
  easyrsa_run gen-dh
  openvpn --genkey secret "${OPENVPN_DIR}/ta.key"
  install -m 0644 "${PKI_DIR}/ca.crt" "${OPENVPN_DIR}/ca.crt"
  install -m 0644 "${PKI_DIR}/issued/server.crt" "${OPENVPN_DIR}/server.crt"
  install -m 0600 "${PKI_DIR}/private/server.key" "${OPENVPN_DIR}/server.key"
  install -m 0644 "${PKI_DIR}/dh.pem" "${OPENVPN_DIR}/dh.pem"
  chmod 0600 "${OPENVPN_DIR}/ta.key"
  create_server_conf
  ensure_client_dir
  say OK "$(printf "$MSG_SETUP_RSA_FIRST_DONE" "$PKI_DIR")"
}

create_server_conf() {
  if [ -e "${OPENVPN_DIR}/server.conf" ]; then
    say INFO "$(printf "$MSG_SETUP_RSA_CONF_EXISTS" "${OPENVPN_DIR}/server.conf")"
    return
  fi
  cat > "${OPENVPN_DIR}/server.conf" <<'EOF'
port 1194
proto udp
dev tun
ca /etc/openvpn/ca.crt
cert /etc/openvpn/server.crt
key /etc/openvpn/server.key
dh /etc/openvpn/dh.pem
tls-auth /etc/openvpn/ta.key 0
server 10.8.0.0 255.255.255.0
topology subnet
keepalive 10 120
persist-key
persist-tun
status /var/log/openvpn/openvpn-status.log
verb 3
EOF
  chmod 0644 "${OPENVPN_DIR}/server.conf"
}

renew_server() {
  [ -d "$PKI_DIR" ] || die "$(printf "$MSG_SETUP_RSA_PKI_MISSING" "$PKI_DIR")"
  local days serial name
  days=$(ask "$MSG_SETUP_RSA_VALIDITY_YEARS" '10')
  [[ "$days" =~ ^[0-9]+$ ]] && [ "$days" -gt 0 ] || die "$MSG_SETUP_RSA_VALIDITY_INVALID"
  export EASYRSA_CERT_EXPIRE="$days"
  # Use a distinct CN so issuing the replacement does not revoke or replace any
  # existing client certificate or require revoking an older server certificate.
  serial=$(date -u +%Y%m%d%H%M%S)
  name="server-${serial}"
  EASYRSA_REQ_CN="$name" easyrsa_run gen-req "$name" nopass
  easyrsa_run sign-req server "$name"
  install -m 0644 "${PKI_DIR}/issued/${name}.crt" "${OPENVPN_DIR}/server.crt"
  install -m 0600 "${PKI_DIR}/private/${name}.key" "${OPENVPN_DIR}/server.key"
  if [ ! -s "${OPENVPN_DIR}/dh.pem" ]; then easyrsa_run gen-dh; install -m 0644 "${PKI_DIR}/dh.pem" "${OPENVPN_DIR}/dh.pem"; fi
  if [ ! -s "${OPENVPN_DIR}/ta.key" ]; then openvpn --genkey secret "${OPENVPN_DIR}/ta.key"; chmod 0600 "${OPENVPN_DIR}/ta.key"; fi
  create_server_conf
  say OK "$(printf "$MSG_SETUP_RSA_SERVER_RENEWED" "$name")"
}

load_db_config() {
  [ -f "${SCRIPTS_DIR}/config.sh" ] || die "$(printf "$MSG_SETUP_RSA_DB_CONFIG_MISSING" "${SCRIPTS_DIR}/config.sh")"
  # shellcheck disable=SC1090
  source "${SCRIPTS_DIR}/config.sh"
  : "${DBHOST:?DBHOST fehlt in config.sh}" "${DBPORT:?DBPORT fehlt in config.sh}" "${DBUSER:?DBUSER fehlt in config.sh}" "${DBPASS:?DBPASS fehlt in config.sh}" "${DBNAME:?DBNAME fehlt in config.sh}"
  command -v mysql >/dev/null 2>&1 || die "$MSG_SETUP_RSA_MYSQL_MISSING"
}

sync_clients() {
  [ -f "${PKI_DIR}/ca.crt" ] || die "$(printf "$MSG_SETUP_RSA_CA_MISSING" "$PKI_DIR")"
  load_db_config
  ensure_client_dir
  local rows uid username existing_count
  rows=$(MYSQL_PWD="$DBPASS" mysql --batch --skip-column-names --raw \
    --host="$DBHOST" --port="$DBPORT" --user="$DBUSER" "$DBNAME" \
    --execute='SELECT uid, user_name FROM user WHERE user_enable = 1 ORDER BY uid') || die "$MSG_SETUP_RSA_DB_QUERY_FAILED"
  [ -n "$rows" ] || { say INFO "$MSG_SETUP_RSA_NO_USERS"; return; }
  while IFS=$'\t' read -r uid username; do
    [[ "$uid" =~ ^[0-9]+$ ]] || { say "${MSG_SETUP_RSA_ERROR}" "$(printf "$MSG_SETUP_RSA_INVALID_UID" "$uid")"; continue; }
    local cert="${CLIENT_DIR}/${uid}.crt" key="${CLIENT_DIR}/${uid}.key" ca="${CLIENT_DIR}/${uid}.ca.crt" tls="${CLIENT_DIR}/${uid}.ta.key"
    existing_count=0
    for f in "$cert" "$key" "$ca" "$tls"; do [ -e "$f" ] && existing_count=$((existing_count + 1)); done
    if [ "$existing_count" -gt 0 ]; then
      say INFO "$(printf "$MSG_SETUP_RSA_CERT_EXISTS" "$uid" "${username:-$MSG_SETUP_RSA_NO_NAME}")"
      continue
    fi
    if [ -s "${PKI_DIR}/issued/${uid}.crt" ] && [ -s "${PKI_DIR}/private/${uid}.key" ]; then
      say INFO "$(printf "$MSG_SETUP_RSA_CERT_IN_PKI" "$uid")"
      install -o root -g www-data -m 0640 "${PKI_DIR}/issued/${uid}.crt" "$cert"
      install -o root -g www-data -m 0640 "${PKI_DIR}/private/${uid}.key" "$key"
      install -o root -g www-data -m 0640 "${PKI_DIR}/ca.crt" "$ca"
      if [ -s "${OPENVPN_DIR}/ta.key" ]; then install -o root -g www-data -m 0640 "${OPENVPN_DIR}/ta.key" "$tls"; fi
      continue
    fi
    say INFO "$(printf "$MSG_SETUP_RSA_ISSUE_CLIENT" "$uid" "${username:-$MSG_SETUP_RSA_NO_NAME}")"
    if ! easyrsa_run build-client-full "$uid" nopass; then
      say "${MSG_SETUP_RSA_ERROR}" "$(printf "$MSG_SETUP_RSA_ISSUE_FAILED" "$uid")"; continue
    fi
    install -o root -g www-data -m 0640 "${PKI_DIR}/issued/${uid}.crt" "$cert"
    install -o root -g www-data -m 0640 "${PKI_DIR}/private/${uid}.key" "$key"
    install -o root -g www-data -m 0640 "${PKI_DIR}/ca.crt" "$ca"
    if [ -s "${OPENVPN_DIR}/ta.key" ]; then install -o root -g www-data -m 0640 "${OPENVPN_DIR}/ta.key" "$tls"; fi
  done <<< "$rows"
  chown root:www-data "$CLIENT_DIR"
  chmod 0750 "$CLIENT_DIR"
  say OK "$MSG_SETUP_RSA_SYNC_DONE"
}

main() {
  select_language
  need_root
  install_if_missing openvpn openvpn
  install_if_missing easy-rsa easyrsa
  find_easyrsa
  install -d -m 0755 "$OPENVPN_DIR"
  if [ ! -d "$EASYRSA_DIR" ]; then
    first_setup
    return
  fi
  printf '\n%s\n1) %s\n2) %s\n' "$(printf "$MSG_SETUP_RSA_EXISTING_PKI" "$EASYRSA_DIR")" "$MSG_SETUP_RSA_OPTION_RENEW" "$MSG_SETUP_RSA_OPTION_USERS"
  local choice
  read -r -p "$MSG_SETUP_RSA_CHOICE " choice
  case "$choice" in
    1) renew_server ;;
    2) sync_clients ;;
    *) die "$MSG_SETUP_RSA_CHOICE_INVALID" ;;
  esac
}

main "$@"
