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

easyrsa_require_exact_dates() {
  local help version
  help="$(cd "$EASYRSA_DIR" && EASYRSA_PKI="$PKI_DIR" "$EASYRSA_BIN" 2>&1 || true)"
  version="$(cd "$EASYRSA_DIR" && EASYRSA_PKI="$PKI_DIR" "$EASYRSA_BIN" --version 2>&1 || true)"
  [[ "$help" == *"--startdate=DATE"* && "$help" == *"--enddate=DATE"* ]] && EASYRSA_SUPPORTS_EXACT_DATES=1
  [[ "$help" == *"expire <file_name_base>"* ]] && EASYRSA_SUPPORTS_EXPIRE=1
  say INFO "$(printf '%s' "$version" | head -n 1): exact date options $([[ "$EASYRSA_SUPPORTS_EXACT_DATES" -eq 1 ]] && printf supported || printf unavailable); expire command $([[ "$EASYRSA_SUPPORTS_EXPIRE" -eq 1 ]] && printf supported || printf unavailable)."
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
      die "Easy-RSA does not support --startdate and --enddate; cannot issue uid ${uid} with its individual date limits."
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
  init_log
  choose_language
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
