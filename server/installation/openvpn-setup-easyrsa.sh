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

# shellcheck disable=SC2034
# shellcheck disable=SC2059
# shellcheck disable=SC1091

set -Eeuo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "${SCRIPT_DIR}/functions.sh"

OPENVPN_DIR=/etc/openvpn
EASYRSA_DIR="${OPENVPN_DIR}/easy-rsa"
SCRIPTS_DIR="${OPENVPN_SCRIPTS_DIR:-/etc/openvpn/scripts}"
CLIENT_DIR="${OPENVPN_CLIENT_CERT_DIR:-/etc/openvpn/client-certificates}"
PKI_DIR="${EASYRSA_DIR}/pki"
EASYRSA_BIN=""
EASYRSA_SUPPORTS_EXACT_DATES=0
EASYRSA_SUPPORTS_EXPIRE=0
WEBADMIN_DEPLOY_DIR=""

say() {
  local level="$1" message="$2"
  case "$level" in
    INFO) info "$message" ;;
    OK) ok "$message" ;;
    *) error "$message" ;;
  esac
}
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
    apt-get update >>"${LOG_FILE}" 2>&1
    DEBIAN_FRONTEND=noninteractive apt-get install -y "$package" >>"${LOG_FILE}" 2>&1
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
  country=$(ask_prefill COUNTRY "$MSG_SETUP_RSA_COUNTRY" $SET_COUNTRY)
  city=$(ask_prefill CITY "$MSG_SETUP_RSA_CITY" $SET_CITY)
  province=$(ask_prefill PROVINCE "$MSG_SETUP_RSA_PROVINCE" $SET_PROVINZ)
  org=$(ask_prefill ORG "$MSG_SETUP_RSA_ORG" $SET_ORG)
  email=$(ask_prefill EMAIL "$MSG_SETUP_RSA_EMAIL" $SET_MAIL)
  ou=$(ask_prefill OU "$MSG_SETUP_RSA_OU" $SET_OU)
  days=$(ask_prefill "$MSG_SETUP_RSA_VALIDITY_DAYS" '3650')
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
  (cd "$EASYRSA_DIR" && EASYRSA_PKI="$PKI_DIR" EASYRSA_BATCH=1 EASYRSA_REQ_CN="${EASYRSA_REQ_CN:-OpenVPN-WebAdmin}" "$EASYRSA_BIN" "$@") >>"${LOG_FILE}" 2>&1
}

easyrsa_require_exact_dates() {
  local help version
  help="$(cd "$EASYRSA_DIR" && EASYRSA_PKI="$PKI_DIR" "$EASYRSA_BIN" help options 2>&1 || true)"
  help+=$'\n'"$(cd "$EASYRSA_DIR" && EASYRSA_PKI="$PKI_DIR" "$EASYRSA_BIN" help 2>&1 || true)"
  version="$(cd "$EASYRSA_DIR" && EASYRSA_PKI="$PKI_DIR" "$EASYRSA_BIN" --version 2>&1 || true)"

  [[ "$help" == *"--startdate"* && "$help" == *"--enddate"* ]] && EASYRSA_SUPPORTS_EXACT_DATES=1
  [[ "$help" == *"expire"* ]] && EASYRSA_SUPPORTS_EXPIRE=1

  log_line INFO "$(printf '%s' "$version" | head -n 1): exact date options $([[ "$EASYRSA_SUPPORTS_EXACT_DATES" -eq 1 ]] && printf supported || printf unavailable); expire command $([[ "$EASYRSA_SUPPORTS_EXPIRE" -eq 1 ]] && printf supported || printf unavailable)."
}

install_client_files() {
  local uid="$1"
  install -o root -g www-data -m 0640 "${PKI_DIR}/issued/${uid}.crt" "${CLIENT_DIR}/${uid}.crt"
  install -o root -g www-data -m 0640 "${PKI_DIR}/private/${uid}.key" "${CLIENT_DIR}/${uid}.key"
  install -o root -g www-data -m 0640 "${PKI_DIR}/ca.crt" "${CLIENT_DIR}/${uid}.ca.crt"
  if [ -s "${OPENVPN_DIR}/ta.key" ]; then install -o root -g www-data -m 0640 "${OPENVPN_DIR}/ta.key" "${CLIENT_DIR}/${uid}.ta.key"; fi
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

prepare_webadmin_link() {
  local web_user
  if [ -n "${WHIPTAIL_BIN}" ]; then
    ask_input WEBADMIN_DEPLOY_DIR "${MSG_DEPLOY_DIR:-WebAdmin target directory}" "${DEFAULT_DEPLOY_DIR}"
  else
    WEBADMIN_DEPLOY_DIR="$(ask "${MSG_DEPLOY_DIR:-WebAdmin target directory}" "${DEFAULT_DEPLOY_DIR}")"
  fi
  [[ "${WEBADMIN_DEPLOY_DIR}" = /* ]] || die "WebAdmin target directory must be absolute."
  [ -d "${WEBADMIN_DEPLOY_DIR}/storage/conf" ] || die "WebAdmin storage directory not found: ${WEBADMIN_DEPLOY_DIR}/storage/conf"
  [ ! -L "${WEBADMIN_DEPLOY_DIR}/storage/conf/server" ] || die "Server configuration directory must not be a symlink."
  [ ! -d "${WEBADMIN_DEPLOY_DIR}/storage/conf/server/server.conf" ] || die "Server configuration link path is a directory."
  command -v setfacl >/dev/null 2>&1 || die "setfacl is required; install the acl package before continuing."
  web_user="$(stat -c '%U' "${WEBADMIN_DEPLOY_DIR}")"
  id -u "${web_user}" >/dev/null 2>&1 || die "Unknown WebAdmin deployment owner: ${web_user}"
  [ "${web_user}" != root ] || die "WebAdmin deployment must not run as root."
}

link_server_conf() {
  local conf_file="${OPENVPN_DIR}/server.conf"
  local link_dir="${WEBADMIN_DEPLOY_DIR}/storage/conf/server"
  local link_path="${link_dir}/server.conf"
  local backup_file index web_user user

  web_user="$(stat -c '%U' "${WEBADMIN_DEPLOY_DIR}")"
  id -u "${web_user}" >/dev/null 2>&1 || die "Unknown WebAdmin deployment owner: ${web_user}"
  [ "${web_user}" != root ] || die "WebAdmin deployment must not run as root."

  [ ! -L "${link_dir}" ] || die "Server configuration directory must not be a symlink: ${link_dir}"
  install -d -o root -g www-data -m 0750 "${link_dir}"
  setfacl -b -k "${link_dir}"
  chmod 0750 "${link_dir}"
  setfacl -m "u:${web_user}:rx" "${link_dir}"
  chown root:www-data "${conf_file}"
  setfacl -b "${conf_file}"
  chmod 0640 "${conf_file}"
  setfacl -m "u:${web_user}:r" "${conf_file}"

  if [ -L "${link_path}" ] && [ "$(readlink -- "${link_path}")" = "${conf_file}" ]; then
    :
  elif [ -e "${link_path}" ] || [ -L "${link_path}" ]; then
    [ ! -d "${link_path}" ] || die "Server configuration link path is a directory: ${link_path}"
    backup_file="${link_path}.bak.$(date '+%Y%m%d%H%M%S')"
    index=0
    while [ -e "${backup_file}" ] || [ -L "${backup_file}" ]; do
      index=$((index + 1))
      backup_file="${link_path}.bak.$(date '+%Y%m%d%H%M%S').${index}"
    done
    mv -- "${link_path}" "${backup_file}"
    say INFO "Existing server configuration saved: ${backup_file}"
    ln -s -- "${conf_file}" "${link_path}"
  else
    ln -s -- "${conf_file}" "${link_path}"
  fi
  for user in www-data "${web_user}"; do
    if ! su -s /bin/sh -c 'test -r "$1" && ! test -w "$1"' "${user}" -- sh "${link_path}"; then
      die "${user} must be able to read, but not write, ${link_path}"
    fi
  done
  say OK "Read-only server configuration link installed: ${link_path}"
}

create_server_conf() {
  local conf_file="${OPENVPN_DIR}/server.conf"
  local backup_file=""
  local temp_file=""

  if [ -e "${conf_file}" ] || [ -L "${conf_file}" ]; then
    backup_file="${conf_file}.bak.$(date '+%Y%m%d%H%M%S')"
    [ ! -e "${backup_file}" ] || die "Backup file already exists: ${backup_file}"
    cp -a -- "${conf_file}" "${backup_file}"
    say INFO "Backup created: ${backup_file}"
  fi

  temp_file="$(mktemp "${OPENVPN_DIR}/.server.conf.XXXXXX")"
  if ! cat > "${temp_file}" <<'EOF'
# OpenVPN-WebAdmin 2.x - Server Configuration

port 1194
proto udp
dev tun

# --------------------------------------------------
# Zertifikate und Schlüssel
# --------------------------------------------------

ca /etc/openvpn/ca.crt
cert /etc/openvpn/server.crt
key /etc/openvpn/server.key
dh /etc/openvpn/dh.pem

# --------------------------------------------------
# TLS-Absicherung
# --------------------------------------------------

tls-auth /etc/openvpn/ta.key 0
tls-version-min 1.2
verify-client-cert require

# --------------------------------------------------
# Benutzer- und Passwortauthentifizierung
# --------------------------------------------------

auth-user-pass-verify /etc/openvpn/scripts/login.v2.sh via-env
username-as-common-name

# --------------------------------------------------
# Verschlüsselung
# --------------------------------------------------

data-ciphers AES-256-GCM:AES-128-GCM:CHACHA20-POLY1305
auth SHA256
allow-compression no

# --------------------------------------------------
# VPN-Netzwerk
# --------------------------------------------------

server 10.8.0.0 255.255.255.0
topology subnet

# all traffic through the VPN
# all dns requests through the VPN
# comment out this line

push "redirect-gateway def1"
#push "dhcp-option DNS 192.168.104.7"

# --------------------------------------------------
# Client-Verwaltung
# --------------------------------------------------

ifconfig-pool-persist /var/log/openvpn/ipp.txt
client-config-dir /etc/openvpn/ccd

# --------------------------------------------------
# Status und Protokollierung
# --------------------------------------------------

status /var/log/openvpn/openvpn-status.log
log-append /var/log/openvpn/openvpn.log
verb 3

# --------------------------------------------------
# Verbindung
# --------------------------------------------------

keepalive 10 120
persist-key
persist-tun
explicit-exit-notify 1

# --------------------------------------------------
# WebAdmin-Scripts
# --------------------------------------------------

script-security 3

client-connect /etc/openvpn/scripts/connect.sh
client-disconnect /etc/openvpn/scripts/disconnect.sh
EOF
  then
    rm -f -- "${temp_file}"
    die "Could not write new server configuration."
  fi

  chown root:www-data "${temp_file}"
  chmod 0640 "${temp_file}"
  mv -f -- "${temp_file}" "${conf_file}"
  link_server_conf
  say OK "New server configuration installed: ${conf_file}"
}

renew_server() {
  [ -d "$PKI_DIR" ] || die "$(printf "$MSG_SETUP_RSA_PKI_MISSING" "$PKI_DIR")"
  local days serial name
  days=$(ask_prefill "$MSG_SETUP_RSA_VALIDITY_DAYS" '3650')
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
  source "${SCRIPTS_DIR}/config.sh"
  : "${DBHOST:?DBHOST fehlt in config.sh}" "${DBPORT:?DBPORT fehlt in config.sh}" "${DBUSER:?DBUSER fehlt in config.sh}" "${DBPASS:?DBPASS fehlt in config.sh}" "${DBNAME:?DBNAME fehlt in config.sh}"
  command -v mysql >/dev/null 2>&1 || die "$MSG_SETUP_RSA_MYSQL_MISSING"
}

sync_clients() {
  [ -f "${PKI_DIR}/ca.crt" ] || die "$(printf "$MSG_SETUP_RSA_CA_MISSING" "$PKI_DIR")"
  load_db_config
  ensure_client_dir
  local rows uid username start_date end_date cert cert_start cert_end desired_start desired_end expired effective_end_date cert_end_epoch expected_end_epoch expected_end start_sql end_sql anniversary anniversary_epoch timezone snapshot snapshot_status
  timezone="$(detect_system_timezone)" || die "Could not determine the system timezone; refusing to issue certificates with ambiguous date limits."
  if [ -n "${TIMEZONE:-}" ] && [ "$TIMEZONE" != "$timezone" ]; then
    say INFO "System timezone changed from configured ${TIMEZONE}; using detected ${timezone}."
  fi
  say INFO "Using system timezone ${timezone} for client certificate date limits."
  easyrsa_require_exact_dates
  rows=$(MYSQL_PWD="$DBPASS" mysql --batch --skip-column-names --raw \
    --host="$DBHOST" --port="$DBPORT" --user="$DBUSER" "$DBNAME" \
    --execute="SELECT uid, user_name, COALESCE(DATE_FORMAT(user_start_date, '%Y-%m-%d'), 'NULL'), COALESCE(DATE_FORMAT(user_end_date, '%Y-%m-%d'), 'NULL') FROM user WHERE user_enable = 1 ORDER BY uid") || die "$MSG_SETUP_RSA_DB_QUERY_FAILED"
  [ -n "$rows" ] || { say INFO "$MSG_SETUP_RSA_NO_USERS"; return; }
  while IFS=$'\t' read -r uid username start_date end_date; do
    [[ "$uid" =~ ^[0-9]+$ ]] || { say "${MSG_SETUP_RSA_ERROR}" "$(printf "$MSG_SETUP_RSA_INVALID_UID" "$uid")"; continue; }
    [ "$start_date" != NULL ] || start_date=""
    [ "$end_date" != NULL ] || end_date=""
    if [ -n "$start_date" ] && [ -n "$end_date" ] && [[ "$start_date" > "$end_date" ]]; then
      die "Invalid user date range for uid ${uid}: start date ${start_date} is after end date ${end_date}."
    fi
    if { [ -n "$start_date" ] || [ -n "$end_date" ]; } && [ "$EASYRSA_SUPPORTS_EXACT_DATES" -ne 1 ]; then
      error "$(printf "$MSG_SETUP_RSA_DATES_UNSUPPORTED" "$(printf '%s' "$(cd "$EASYRSA_DIR" && EASYRSA_PKI="$PKI_DIR" "$EASYRSA_BIN" --version 2>&1 || true)" | head -n 1)" "$uid")"
      continue
    fi
    cert="${PKI_DIR}/issued/${uid}.crt"
    snapshot="${PKI_DIR}/webadmin-validity/${uid}.dates"
    desired_start=""
    desired_end=""
    if [ -n "$start_date" ]; then desired_start=$(timezone_start_utc "$timezone" "$start_date") || die "Invalid user_start_date for uid $uid: $start_date"; fi
    if [ -n "$end_date" ]; then
      desired_end=$(timezone_end_utc "$timezone" "$end_date") || die "Invalid user_end_date for uid $uid: $end_date"
      effective_end_date="$end_date"
    elif [ -n "$start_date" ]; then
      desired_end=$(one_year_after_timezone_start_utc "$timezone" "$start_date") || die "Could not calculate one-year validity for uid $uid"
      anniversary=$(TZ="$timezone" date --date="$start_date + 1 year" +%F) || die "Could not calculate validity anniversary for uid $uid"
      anniversary_epoch=$(TZ="$timezone" date --date="$anniversary 00:00:00" +%s) || die "Could not calculate expiration date for uid $uid"
      effective_end_date=$(date_in_timezone "$timezone" "$((anniversary_epoch - 1))") || die "Could not calculate expiration date for uid $uid"
    else
      effective_end_date=""
    fi

    expired=0
    if [ -n "$effective_end_date" ] && [[ "$effective_end_date" < "$(TZ="$timezone" date +%F)" ]]; then expired=1; fi
    cert_start=""; cert_end=""
    if [ -s "$cert" ]; then
      cert_start=$(cert_time_utc "$cert" startdate) || cert_start=""
      cert_end=$(cert_time_utc "$cert" enddate) || cert_end=""
    fi

    if [ -s "$cert" ]; then
      if certificate_limit_snapshot_status "$snapshot" "$start_date" "$end_date"; then
        snapshot_status=0
      else
        snapshot_status=$?
      fi
      local matches=1
      [ -z "$desired_start" ] || [ "$cert_start" = "$desired_start" ] || matches=0
      [ -z "$desired_end" ] || [ "$cert_end" = "$desired_end" ] || matches=0
      [ "$snapshot_status" -ne 2 ] || matches=0
      # With no individual date limits, issue a fresh one-year certificate on
      # every synchronization run, as requested.
      if [ -z "$start_date" ] && [ -z "$end_date" ]; then matches=0; fi
      if [ "$matches" -eq 1 ]; then
        if [ "$snapshot_status" -eq 1 ]; then
          write_certificate_limit_snapshot "$snapshot" "$start_date" "$end_date" \
            || die "Could not save the validity snapshot for uid ${uid}."
        fi
        if [ "$expired" -eq 1 ] && { [ -n "$start_date" ] || [ -n "$end_date" ]; } && ! openssl x509 -in "$cert" -checkend 0 -noout >/dev/null 2>&1; then
          if [ -n "$start_date" ]; then start_sql="user_start_date = '${start_date}'"; else start_sql='user_start_date IS NULL'; fi
          if [ -n "$end_date" ]; then end_sql="user_end_date = '${end_date}'"; else end_sql='user_end_date IS NULL'; fi
          MYSQL_PWD="$DBPASS" mysql --batch --skip-column-names --raw --host="$DBHOST" --port="$DBPORT" --user="$DBUSER" "$DBNAME" \
            --execute="UPDATE user SET user_enable = 0 WHERE uid = ${uid} AND user_enable = 1 AND ${start_sql} AND ${end_sql}" || die "$MSG_SETUP_RSA_DB_QUERY_FAILED"
          say INFO "uid ${uid}: unchanged individual limit expired; user marked inactive."
          continue
        fi
        if [ -s "${PKI_DIR}/private/${uid}.key" ]; then install_client_files "$uid"; fi
        say INFO "uid ${uid}: existing certificate matches current limits; unchanged."
        continue
      fi
    fi

    local -a date_opts=()
    [ -z "$desired_start" ] || date_opts+=("--startdate=$desired_start")
    if [ -n "$desired_end" ]; then date_opts+=("--enddate=$desired_end")
    else date_opts+=("--days=365"); fi
    say INFO "$(printf "$MSG_SETUP_RSA_ISSUE_CLIENT" "$uid" "${username:-$MSG_SETUP_RSA_NO_NAME}")"
    if [ -s "$cert" ]; then
      [ "$EASYRSA_SUPPORTS_EXPIRE" -eq 1 ] || die "This Easy-RSA version cannot archive the previous certificate for uid ${uid}; upgrade Easy-RSA before renewal."
      if ! easyrsa_run expire "$uid" || ! easyrsa_run "${date_opts[@]}" sign-req client "$uid"; then
        say "${MSG_SETUP_RSA_ERROR}" "$(printf "$MSG_SETUP_RSA_ISSUE_FAILED" "$uid")"; continue
      fi
    elif [ -s "${PKI_DIR}/reqs/${uid}.req" ] && [ -s "${PKI_DIR}/private/${uid}.key" ]; then
      if ! easyrsa_run "${date_opts[@]}" sign-req client "$uid"; then
        say "${MSG_SETUP_RSA_ERROR}" "$(printf "$MSG_SETUP_RSA_ISSUE_FAILED" "$uid")"; continue
      fi
    elif ! EASYRSA_REQ_CN="$uid" easyrsa_run gen-req "$uid" nopass || ! easyrsa_run "${date_opts[@]}" sign-req client "$uid"; then
      say "${MSG_SETUP_RSA_ERROR}" "$(printf "$MSG_SETUP_RSA_ISSUE_FAILED" "$uid")"; continue
    fi
    cert_start=$(cert_time_utc "$cert" startdate) || die "Could not read issued notBefore for uid $uid"
    cert_end=$(cert_time_utc "$cert" enddate) || die "Could not read issued notAfter for uid $uid"
    [ -z "$desired_start" ] || [ "$cert_start" = "$desired_start" ] || die "Easy-RSA issued unexpected notBefore for uid $uid (expected $desired_start, got $cert_start)"
    [ -z "$desired_end" ] || [ "$cert_end" = "$desired_end" ] || die "Easy-RSA issued unexpected notAfter for uid $uid (expected $desired_end, got $cert_end)"
    if [ -z "$desired_end" ] && [ -z "$start_date" ]; then
      cert_end_epoch=$(date --date="$cert_start" +%s)
      expected_end_epoch=$((cert_end_epoch + 365 * 86400))
      expected_end=$(date --date="@$expected_end_epoch" -u +%Y%m%d%H%M%SZ)
      [ "$cert_end" = "$expected_end" ] || die "Easy-RSA did not issue the default one-year validity for uid $uid"
    fi
    install_client_files "$uid"
    write_certificate_limit_snapshot "$snapshot" "$start_date" "$end_date" \
      || die "Could not save the validity snapshot for uid ${uid}."
  done <<< "$rows"
  chown root:www-data "$CLIENT_DIR"
  chmod 0750 "$CLIENT_DIR"
  say OK "$MSG_SETUP_RSA_SYNC_DONE"
}

main() {
  need_root
  load_install_config
  init_log
  show_header
  if [ -n "${WHIPTAIL_BIN}" ]; then
    LANG_CHOICE="$(${WHIPTAIL_BIN} --title 'OpenVPN-WebAdmin Setup' --menu "${MSG_SETUP_RSA_LANGUAGE_SELECT:-Sprache auswählen}" 12 72 4 \
      de_DE Deutsch en_GB English fr_FR Français 3>&1 1>&2 2>&3)" || abort_by_user
    [ -f "${LANG_DIR}/${LANG_CHOICE}" ] || LANG_CHOICE=de_DE
    load_language "${LANG_CHOICE}"
  else
    LANG_CHOICE=de_DE
    load_language "${LANG_CHOICE}"
  fi
  show_section "${MSG_SETUP_RSA_SECTION:-OpenVPN / Easy-RSA}"
  install_if_missing openvpn openvpn
  install_if_missing easy-rsa easyrsa
  find_easyrsa
  install -d -m 0755 "$OPENVPN_DIR"
  if [ ! -d "$EASYRSA_DIR" ]; then
    prepare_webadmin_link
    first_setup
    return
  fi
  local choice
  if [ -n "${WHIPTAIL_BIN}" ]; then
    choice="$(${WHIPTAIL_BIN} --title 'OpenVPN-WebAdmin Setup' --menu "$(printf "$MSG_SETUP_RSA_EXISTING_PKI" "$EASYRSA_DIR")" 14 78 5 \
      1 "$MSG_SETUP_RSA_OPTION_RENEW" 2 "$MSG_SETUP_RSA_OPTION_USERS" 3 "${MSG_SETUP_RSA_OPTION_EXIT:-Beenden}" 3>&1 1>&2 2>&3)" || abort_by_user
  else
    printf '\n1) %s\n2) %s\n3) %s\n' "$MSG_SETUP_RSA_OPTION_RENEW" "$MSG_SETUP_RSA_OPTION_USERS" "${MSG_SETUP_RSA_OPTION_EXIT:-Beenden}"
    read -r -p "$MSG_SETUP_RSA_CHOICE " choice
  fi
  case "$choice" in
    1) prepare_webadmin_link; renew_server ;;
    2) sync_clients ;;
    3) exit 0 ;;
    *) die "$MSG_SETUP_RSA_CHOICE_INVALID" ;;
  esac
}

if [[ "${BASH_SOURCE[0]}" == "$0" ]]; then
  main "$@"
fi
