#!/usr/bin/env bash
# Final OpenVPN integration step for OpenVPN-WebAdmin setup.
set -euo pipefail

MODE="${1:-}"
SERVER_CONF="${2:-}"
SCRIPTS_DIR="${3:-}"
SCRIPTS_SOURCE="${4:-}"
DEPLOY_DIR="${5:-}"
TEMPLATE="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)/storage/conf/server/server.conf.template"

fail() { printf 'OpenVPN integration: ERROR: %s\n' "$*" >&2; exit 1; }
info() { printf 'OpenVPN integration: %s\n' "$*"; }

[[ "${EUID}" -eq 0 ]] || fail "Run this script as root."
[[ "${MODE}" == existing || "${MODE}" == configure || "${MODE}" == new ]] || fail "Invalid setup mode: ${MODE}"
[[ "${SERVER_CONF}" = /* && "${SCRIPTS_DIR}" = /* && "${SCRIPTS_SOURCE}" = /* && "${DEPLOY_DIR}" = /* ]] || fail "All paths must be absolute."
[[ -d "${SCRIPTS_SOURCE}" && -f "${SCRIPTS_SOURCE}/connect.sh" && -f "${SCRIPTS_SOURCE}/disconnect.sh" && -f "${SCRIPTS_SOURCE}/functions.sh" && -f "${SCRIPTS_SOURCE}/config.sample.sh" ]] || fail "WebAdmin OpenVPN scripts are incomplete."
[[ -n "${OVPN_DB_HOST:-}" && -n "${OVPN_DB_PORT:-}" && -n "${OVPN_DB_USER:-}" && -n "${OVPN_DB_NAME:-}" ]] || fail "Database connection values were not provided."

if [[ "${MODE}" != new ]] && ! command -v openvpn >/dev/null 2>&1; then
  fail "The selected existing OpenVPN installation is not installed."
fi
command -v systemctl >/dev/null 2>&1 || fail "systemctl is required to detect the OpenVPN service."

CONF_DIR="$(dirname "${SERVER_CONF}")"
CONF_NAME="$(basename "${SERVER_CONF}")"
[[ "${CONF_NAME}" =~ ^[A-Za-z0-9_.-]+\.conf$ ]] || fail "The server configuration filename must be a simple .conf filename."
INSTANCE="${CONF_NAME%.conf}"
[[ "${SERVER_CONF}${CONF_DIR}" != *$'\n'* && "${SERVER_CONF}${CONF_DIR}" != *'"'* && "${SERVER_CONF}${CONF_DIR}" != *'%'* ]] || \
  fail "Configuration paths containing quotes, newlines, or systemd '%' specifiers are unsupported."

if [[ -f "${SERVER_CONF}" ]]; then
  [[ -r "${SERVER_CONF}" ]] || fail "Configuration is not readable: ${SERVER_CONF}"
else
  [[ "${MODE}" != existing ]] || fail "Configured-installation takeover requires an existing server.conf: ${SERVER_CONF}"
  [[ -f "${TEMPLATE}" ]] || fail "Project server configuration template is missing: ${TEMPLATE}"
fi

TMP_DIR="$(mktemp -d)"
cleanup() { rm -rf "${TMP_DIR}"; }
trap cleanup EXIT
STAGED_CONF="${TMP_DIR}/server.conf"

if [[ -f "${SERVER_CONF}" ]]; then
  cp -a "${SERVER_CONF}" "${STAGED_CONF}"
else
  escaped_conf_dir="$(printf '%s' "${CONF_DIR}" | sed 's/[\\&|]/\\&/g')"
  sed "s|__OPENVPN_CONFIG_DIR__|${escaped_conf_dir}|g" "${TEMPLATE}" > "${STAGED_CONF}"
fi

has_directive() { grep -Eiq "^[[:space:]]*$1([[:space:]]|$)" "$2"; }
directive_values() { awk -v key="$1" 'tolower($1)==tolower(key) { $1=""; sub(/^[[:space:]]+/, ""); print }' "$2"; }

if ! has_directive server "${STAGED_CONF}"; then
  fail "No usable server configuration found (missing 'server' directive). It was left unchanged."
fi

HOOK_CONNECT="${SCRIPTS_DIR}/connect.sh"
HOOK_DISCONNECT="${SCRIPTS_DIR}/disconnect.sh"
for hook in client-connect client-disconnect; do
  expected="${HOOK_CONNECT}"
  [[ "${hook}" == client-disconnect ]] && expected="${HOOK_DISCONNECT}"
  while IFS= read -r current; do
    [[ -z "${current}" || "${current}" == "${expected}" ]] || \
      fail "Existing ${hook} hook '${current}' conflicts with WebAdmin. No files were changed; merge it manually first."
  done < <(directive_values "${hook}" "${STAGED_CONF}")
done

if ! has_directive script-security "${STAGED_CONF}"; then
  printf '\nscript-security 2\n' >> "${STAGED_CONF}"
else
  security_level="$(directive_values script-security "${STAGED_CONF}" | awk 'NR==1 {print $1}')"
  [[ "${security_level}" =~ ^[0-9]+$ ]] || fail "Unrecognized script-security setting."
  if (( security_level < 2 )); then
    sed -i -E 's/^[[:space:]]*script-security[[:space:]]+[0-9]+.*/script-security 2/I' "${STAGED_CONF}"
  fi
fi
for hook in client-connect client-disconnect; do
  expected="${HOOK_CONNECT}"
  [[ "${hook}" == client-disconnect ]] && expected="${HOOK_DISCONNECT}"
  hook_count="$(grep -Eic "^[[:space:]]*${hook}[[:space:]]+" "${STAGED_CONF}" || true)"
  if (( hook_count > 1 )); then
    sed -i -E "/^[[:space:]]*${hook}[[:space:]]+/Id" "${STAGED_CONF}"
    printf '%s %s\n' "${hook}" "${expected}" >> "${STAGED_CONF}"
  elif (( hook_count == 0 )); then
    printf '%s %s\n' "${hook}" "${expected}" >> "${STAGED_CONF}"
  fi
done

if ! has_directive ca "${STAGED_CONF}" || ! has_directive cert "${STAGED_CONF}" || ! has_directive key "${STAGED_CONF}"; then
  fail "Configuration must reference readable CA, server certificate, and private key files."
fi
if ! has_directive proto "${STAGED_CONF}" || ! has_directive dev "${STAGED_CONF}"; then
  fail "Configuration must include both 'proto' and 'dev' directives."
fi
command -v openvpn >/dev/null 2>&1 || fail "The OpenVPN executable is not available after package installation."
if ! openvpn --config "${STAGED_CONF}" --show-ciphers >/dev/null 2>"${TMP_DIR}/openvpn-parse.log"; then
  cat "${TMP_DIR}/openvpn-parse.log" >&2
  fail "OpenVPN rejected the staged configuration; no live configuration was changed."
fi

# Match the selected configuration path to a systemd unit that actually consumes it.
SERVICE=""
if systemctl list-unit-files --type=service --no-legend 2>/dev/null | awk '{print $1}' | grep -Fxq 'openvpn-server@.service' \
  && [[ "${SERVER_CONF}" == "/etc/openvpn/server/${CONF_NAME}" ]]; then
  SERVICE="openvpn-server@${INSTANCE}.service"
elif systemctl list-unit-files --type=service --no-legend 2>/dev/null | awk '{print $1}' | grep -Fxq 'openvpn@.service' \
  && [[ "${SERVER_CONF}" == "/etc/openvpn/${CONF_NAME}" ]]; then
  SERVICE="openvpn@${INSTANCE}.service"
elif systemctl list-unit-files --type=service --no-legend 2>/dev/null | awk '{print $1}' | grep -Fxq 'openvpn.service' \
  && [[ "${SERVER_CONF}" == "/etc/openvpn/server.conf" ]]; then
  SERVICE="openvpn.service"
fi
# Honor site-specific unit overrides when their effective ExecStart names the
# selected config path. This allows nonstandard config locations without
# guessing that a stock unit reads them.
if [[ -z "${SERVICE}" ]]; then
  while IFS= read -r candidate; do
    [[ "${candidate}" == openvpn*.service ]] || continue
    exec_start="$(systemctl show "${candidate}" -p ExecStart --value 2>/dev/null || true)"
    if [[ "${exec_start}" == *"${SERVER_CONF}"* ]]; then
      SERVICE="${candidate}"
      break
    fi
  done < <(
    {
      systemctl list-units --all --type=service --no-legend 2>/dev/null || true
      systemctl list-unit-files --type=service --no-legend 2>/dev/null || true
    } | awk '{print $1}' | grep -E '^openvpn.*\.service$' | sort -u
  )
fi
CUSTOM_UNIT_FILE="/etc/systemd/system/openvpn-webadmin-${INSTANCE}.service"
CUSTOM_UNIT_CREATED=no
if [[ -z "${SERVICE}" && "${MODE}" != existing ]]; then
  if [[ -e "${CUSTOM_UNIT_FILE}" ]]; then
    if grep -Fq -- "${SERVER_CONF}" "${CUSTOM_UNIT_FILE}"; then
      SERVICE="openvpn-webadmin-${INSTANCE}.service"
    else
      fail "${CUSTOM_UNIT_FILE} already exists and targets another configuration; it was left unchanged."
    fi
  else
    SERVICE="openvpn-webadmin-${INSTANCE}.service"
    CUSTOM_UNIT_CREATED=yes
  fi
fi
[[ -n "${SERVICE}" ]] || fail "No installed systemd OpenVPN unit matches ${SERVER_CONF}; no service was changed."
if [[ "${MODE}" == existing ]] && ! systemctl is-active --quiet "${SERVICE}"; then
  fail "The selected installation is not currently active (${SERVICE}); choose configure or resolve its service state first."
fi

# Resolve relative key/certificate paths against the service working directory,
# which may differ from the server.conf directory on a site-specific unit.
if [[ "${CUSTOM_UNIT_CREATED}" == yes ]]; then
  SERVICE_WORKING_DIR="${CONF_DIR}"
else
  SERVICE_WORKING_DIR="$(systemctl show "${SERVICE}" -p WorkingDirectory --value 2>/dev/null || true)"
  SERVICE_WORKING_DIR="${SERVICE_WORKING_DIR:-/}"
fi
for key in ca cert key dh tls-auth tls-crypt; do
  while IFS= read -r args; do
    [[ -n "${args}" ]] || continue
    file="${args%%[[:space:]]*}"
    [[ "${file}" == "none" ]] && continue
    [[ "${file}" = /* ]] || file="${SERVICE_WORKING_DIR%/}/${file}"
    [[ -r "${file}" ]] || fail "Required ${key} file is missing or unreadable from ${SERVICE_WORKING_DIR}: ${file}. Supply it manually, then rerun setup."
  done < <(directive_values "${key}" "${STAGED_CONF}")
done

SERVICE_USER="$(awk 'tolower($1)=="user" {print $2; exit}' "${STAGED_CONF}")"
SERVICE_GROUP="$(awk 'tolower($1)=="group" {print $2; exit}' "${STAGED_CONF}")"
SERVICE_USER="${SERVICE_USER:-nobody}"
if [[ -z "${SERVICE_GROUP}" ]]; then
  SERVICE_GROUP="$(id -gn "${SERVICE_USER}" 2>/dev/null || true)"
fi
[[ -n "${SERVICE_GROUP}" ]] || fail "Cannot determine the OpenVPN runtime group for ${SERVICE_USER}."
getent group "${SERVICE_GROUP}" >/dev/null || fail "OpenVPN runtime group does not exist: ${SERVICE_GROUP}"

mkdir -p "${CONF_DIR}" "${SCRIPTS_DIR}"
if [[ -f "${SCRIPTS_DIR}/config.sh" ]]; then
  cp -a "${SCRIPTS_DIR}/config.sh" "${TMP_DIR}/config.sh"
else
  cp "${SCRIPTS_SOURCE}/config.sample.sh" "${TMP_DIR}/config.sh"
fi
update_config_value() {
  local key="$1" value="$2" quoted tmp
  printf -v quoted '%q' "${value}"
  tmp="${TMP_DIR}/config.updated"
  OVPN_CONFIG_ASSIGNMENT="${quoted}" awk -v key="${key}" '
    BEGIN { done=0 }
    index($0, key "=")==1 { print key "=" ENVIRON["OVPN_CONFIG_ASSIGNMENT"]; done=1; next }
    { print }
    END { if (!done) print key "=" ENVIRON["OVPN_CONFIG_ASSIGNMENT"] }
  ' "${TMP_DIR}/config.sh" > "${tmp}"
  mv "${tmp}" "${TMP_DIR}/config.sh"
}
update_config_value DBHOST "${OVPN_DB_HOST}"
update_config_value DBPORT "${OVPN_DB_PORT}"
update_config_value DBUSER "${OVPN_DB_USER}"
update_config_value DBPASS "${OVPN_DB_PASS:-}"
update_config_value DBNAME "${OVPN_DB_NAME}"

escaped_scripts_dir="$(printf '%s' "${SCRIPTS_DIR}" | sed 's/[\\&|]/\\&/g')"
sed "s|/etc/openvpn/scripts|${escaped_scripts_dir}|g" "${SCRIPTS_SOURCE}/connect.sh" > "${TMP_DIR}/connect.sh"
sed "s|/etc/openvpn/scripts|${escaped_scripts_dir}|g" "${SCRIPTS_SOURCE}/disconnect.sh" > "${TMP_DIR}/disconnect.sh"
cp "${SCRIPTS_SOURCE}/functions.sh" "${TMP_DIR}/functions.sh"
bash -n "${TMP_DIR}/config.sh" || fail "Generated OpenVPN script configuration is not valid shell syntax."
for script in "${TMP_DIR}/connect.sh" "${TMP_DIR}/disconnect.sh" "${TMP_DIR}/functions.sh"; do
  bash -n "${script}" || fail "WebAdmin hook script has invalid shell syntax: ${script}"
done

backup_file() {
  local file="$1"
  [[ -f "${file}" ]] || return 0
  local backup="${file}.bak.$(date '+%Y%m%d%H%M%S')"
  cp -a "${file}" "${backup}"
  info "Backup created: ${backup}"
}

for name in connect.sh disconnect.sh functions.sh; do
  cmp -s "${TMP_DIR}/${name}" "${SCRIPTS_DIR}/${name}" 2>/dev/null || backup_file "${SCRIPTS_DIR}/${name}"
done
cmp -s "${TMP_DIR}/config.sh" "${SCRIPTS_DIR}/config.sh" 2>/dev/null || backup_file "${SCRIPTS_DIR}/config.sh"
if ! cmp -s "${STAGED_CONF}" "${SERVER_CONF}" 2>/dev/null; then
  backup_file "${SERVER_CONF}"
fi

install -d -o root -g "${SERVICE_GROUP}" -m 0750 "${SCRIPTS_DIR}"
for name in connect.sh disconnect.sh functions.sh; do
  if ! cmp -s "${TMP_DIR}/${name}" "${SCRIPTS_DIR}/${name}" 2>/dev/null; then
    install -o root -g "${SERVICE_GROUP}" -m 0750 "${TMP_DIR}/${name}" "${SCRIPTS_DIR}/${name}"
  fi
done
if ! cmp -s "${TMP_DIR}/config.sh" "${SCRIPTS_DIR}/config.sh" 2>/dev/null; then
  install -o root -g "${SERVICE_GROUP}" -m 0640 "${TMP_DIR}/config.sh" "${SCRIPTS_DIR}/config.sh"
fi

if [[ "${CUSTOM_UNIT_CREATED}" == yes ]]; then
  OPENVPN_BIN="$(command -v openvpn)"
  cat > "${TMP_DIR}/openvpn-webadmin.service" <<EOF
[Unit]
Description=OpenVPN server for OpenVPN-WebAdmin (${INSTANCE})
After=network.target

[Service]
Type=simple
WorkingDirectory="${CONF_DIR}"
ExecStart="${OPENVPN_BIN}" --config "${SERVER_CONF}"
Restart=on-failure
RestartSec=5s
KillSignal=SIGINT

[Install]
WantedBy=multi-user.target
EOF
  if command -v systemd-analyze >/dev/null 2>&1; then
    systemd-analyze verify "${TMP_DIR}/openvpn-webadmin.service" >/dev/null 2>"${TMP_DIR}/unit-verify.log" || {
      cat "${TMP_DIR}/unit-verify.log" >&2
      fail "Generated systemd unit failed validation; nothing was installed."
    }
  fi
  install -o root -g root -m 0644 "${TMP_DIR}/openvpn-webadmin.service" "${CUSTOM_UNIT_FILE}"
  systemctl daemon-reload || {
    rm -f "${CUSTOM_UNIT_FILE}"
    systemctl daemon-reload || true
    fail "Could not load the generated OpenVPN systemd unit."
  }
fi

ORIGINAL_ACTIVE=no
if systemctl is-active --quiet "${SERVICE}"; then ORIGINAL_ACTIVE=yes; fi
OLD_CONF="${TMP_DIR}/old.conf"
[[ ! -f "${SERVER_CONF}" ]] || cp -a "${SERVER_CONF}" "${OLD_CONF}"
install -o root -g root -m 0644 "${STAGED_CONF}" "${SERVER_CONF}"

if ! systemctl restart "${SERVICE}" >>"${TMP_DIR}/systemctl.log" 2>&1; then
  if [[ -f "${OLD_CONF}" ]]; then
    cp -a "${OLD_CONF}" "${SERVER_CONF}"
    if [[ "${ORIGINAL_ACTIVE}" == yes ]]; then
      systemctl restart "${SERVICE}" >>"${TMP_DIR}/systemctl.log" 2>&1 || true
    else
      systemctl stop "${SERVICE}" >>"${TMP_DIR}/systemctl.log" 2>&1 || true
    fi
  else
    rm -f "${SERVER_CONF}"
    systemctl stop "${SERVICE}" >>"${TMP_DIR}/systemctl.log" 2>&1 || true
  fi
  if [[ "${CUSTOM_UNIT_CREATED}" == yes ]]; then
    rm -f "${CUSTOM_UNIT_FILE}"
    systemctl daemon-reload || true
  fi
  cat "${TMP_DIR}/systemctl.log" >&2
  fail "OpenVPN failed to restart. Previous configuration was restored. Check the service log."
fi
systemctl is-active --quiet "${SERVICE}" || fail "${SERVICE} did not become active."
if ! systemctl enable "${SERVICE}" >>"${TMP_DIR}/systemctl.log" 2>&1; then
  if [[ -f "${OLD_CONF}" ]]; then
    cp -a "${OLD_CONF}" "${SERVER_CONF}"
    [[ "${ORIGINAL_ACTIVE}" != yes ]] || systemctl restart "${SERVICE}" >>"${TMP_DIR}/systemctl.log" 2>&1 || true
  else
    rm -f "${SERVER_CONF}"
    systemctl stop "${SERVICE}" >>"${TMP_DIR}/systemctl.log" 2>&1 || true
  fi
  if [[ "${CUSTOM_UNIT_CREATED}" == yes ]]; then
    rm -f "${CUSTOM_UNIT_FILE}"
    systemctl daemon-reload || true
  fi
  cat "${TMP_DIR}/systemctl.log" >&2
  fail "Could not enable ${SERVICE}; the previous configuration was restored."
fi
info "Integration completed; ${SERVICE} is active."
