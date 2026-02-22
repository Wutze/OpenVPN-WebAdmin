#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck disable=SC1091
source "${SCRIPT_DIR}/functions.sh"

load_install_config
init_log
choose_language

info "${MSG_SETUP_START:-Starting setup}"
require_root
detect_os
assert_supported_os

ensure_cmd apt-get

ask_input REPO_URL "${MSG_REPO_URL:-Repository URL}" "${INSTALL_REPO_URL}"
ask_input REPO_BRANCH "${MSG_REPO_BRANCH:-Repository branch}" "${INSTALL_REPO_BRANCH}"
ask_input SOURCE_DIR "${MSG_SOURCE_DIR:-Local source directory}" "${INSTALL_SOURCE_DIR}"
ask_input DEPLOY_DIR "${MSG_DEPLOY_DIR:-WebAdmin target directory}" "${DEFAULT_DEPLOY_DIR}"
ask_input WEB_OWNER "${MSG_WEB_OWNER:-Web owner user}" "${DEFAULT_WEB_OWNER}"
ask_input WEB_GROUP "${MSG_WEB_GROUP:-Web owner group}" "${DEFAULT_WEB_GROUP}"
ask_input LOGIN_THEME "${MSG_LOGIN_THEME:-Login theme (login1/login2/login3)}" "${DEFAULT_LOGIN_THEME}"
ask_input SITETOOLS "${MSG_SITETOOLS:-Local sitetools path (no external URL)}" "${DEFAULT_SITETOOLS}"

if [[ "${SITETOOLS}" =~ ^https?:// ]]; then
  fatal "${MSG_SITETOOLS_EXTERNAL_FORBIDDEN:-External sitetools URLs are not allowed.}"
fi

ask_input DB_HOST "${MSG_DB_HOST:-Database host}" "${DEFAULT_DB_HOST}"
ask_input DB_PORT "${MSG_DB_PORT:-Database port}" "${DEFAULT_DB_PORT}"
ask_input DB_NAME "${MSG_DB_NAME:-Database name}" "${DEFAULT_DB_NAME}"
ask_input DB_USER "${MSG_DB_USER:-Database user}" "${DEFAULT_DB_USER}"
ask_secret DB_PASS "${MSG_DB_PASS:-Database password}"

DB_CREATE_LOCAL_DEFAULT="no"
if [ "${DB_HOST}" = "localhost" ] || [ "${DB_HOST}" = "127.0.0.1" ]; then
  DB_CREATE_LOCAL_DEFAULT="yes"
fi
ask_yes_no DB_CREATE_LOCAL "${MSG_DB_CREATE_LOCAL:-Create local MariaDB database and user}" "${DB_CREATE_LOCAL_DEFAULT}"

DB_ROOT_PASSWORD=""
if [ "${DB_CREATE_LOCAL}" = "yes" ]; then
  ask_input DB_ROOT_PASSWORD "${MSG_DB_ROOT_PASS_OPTIONAL:-MariaDB root password (leave empty for socket auth)}" ""
fi

ask_input ADMIN_USER "${MSG_ADMIN_USER:-Initial admin username}" "admin"
ask_secret ADMIN_PASS "${MSG_ADMIN_PASS:-Initial admin password}"

DEFAULT_CONF_PATH="${DEFAULT_OPENVPN_SERVER_CONF}"
if [ ! -f "${DEFAULT_CONF_PATH}" ] && [ -f "${ALT_OPENVPN_SERVER_CONF}" ]; then
  DEFAULT_CONF_PATH="${ALT_OPENVPN_SERVER_CONF}"
fi
ask_input OPENVPN_SERVER_CONF "${MSG_OPENVPN_CONF:-OpenVPN server.conf path}" "${DEFAULT_CONF_PATH}"
ask_input OPENVPN_SCRIPTS_DIR "${MSG_OPENVPN_SCRIPTS_DIR:-OpenVPN scripts directory}" "${DEFAULT_OPENVPN_SCRIPTS_DIR}"

ask_input WEBSERVER_PACKAGE "${MSG_WEBSERVER_PACKAGE:-Optional webserver package (none/apache2/nginx)}" "none"
case "${WEBSERVER_PACKAGE}" in
  none|apache2|nginx) ;;
  *) fatal "${MSG_INVALID_WEBSERVER:-Invalid webserver package option.}" ;;
esac

ensure_absolute_path "${SOURCE_DIR}" || fatal "${MSG_PATH_ABSOLUTE_REQUIRED:-Path must be absolute}: ${SOURCE_DIR}"
ensure_absolute_path "${DEPLOY_DIR}" || fatal "${MSG_PATH_ABSOLUTE_REQUIRED:-Path must be absolute}: ${DEPLOY_DIR}"
ensure_absolute_path "${OPENVPN_SCRIPTS_DIR}" || fatal "${MSG_PATH_ABSOLUTE_REQUIRED:-Path must be absolute}: ${OPENVPN_SCRIPTS_DIR}"

id -u "${WEB_OWNER}" >/dev/null 2>&1 || fatal "${MSG_USER_NOT_FOUND:-User not found}: ${WEB_OWNER}"
getent group "${WEB_GROUP}" >/dev/null 2>&1 || fatal "${MSG_GROUP_NOT_FOUND:-Group not found}: ${WEB_GROUP}"

info "${MSG_INSTALL_PACKAGES:-Installing required packages}"
packages=(
  git
  rsync
  ca-certificates
  openvpn
  mariadb-client
  php-cli
  php-mysql
  php-mbstring
  php-xml
  php-curl
)
if [ "${DB_CREATE_LOCAL}" = "yes" ]; then
  packages+=(mariadb-server)
fi
if [ "${WEBSERVER_PACKAGE}" != "none" ]; then
  packages+=("${WEBSERVER_PACKAGE}")
fi

apt-get update -y >>"${LOG_FILE}" 2>&1
DEBIAN_FRONTEND=noninteractive apt-get install -y "${packages[@]}" >>"${LOG_FILE}" 2>&1

if [ "${DB_CREATE_LOCAL}" = "yes" ]; then
  systemctl enable --now mariadb >>"${LOG_FILE}" 2>&1 || systemctl enable --now mysql >>"${LOG_FILE}" 2>&1
fi

ensure_cmd git
if [ -d "${SOURCE_DIR}/.git" ]; then
  info "${MSG_REPO_UPDATE:-Updating existing source repository}"
  git -C "${SOURCE_DIR}" fetch --all --prune >>"${LOG_FILE}" 2>&1
  git -C "${SOURCE_DIR}" checkout "${REPO_BRANCH}" >>"${LOG_FILE}" 2>&1
  git -C "${SOURCE_DIR}" pull --ff-only origin "${REPO_BRANCH}" >>"${LOG_FILE}" 2>&1
elif [ -d "${SOURCE_DIR}" ] && [ -n "$(ls -A "${SOURCE_DIR}" 2>/dev/null)" ]; then
  fatal "${MSG_SOURCE_NOT_EMPTY:-Source directory exists and is not empty}: ${SOURCE_DIR}"
else
  info "${MSG_REPO_CLONE:-Cloning repository}"
  mkdir -p "$(dirname "${SOURCE_DIR}")"
  git clone --branch "${REPO_BRANCH}" "${REPO_URL}" "${SOURCE_DIR}" >>"${LOG_FILE}" 2>&1
fi

DB_SQL_FILE="${SOURCE_DIR}/server/installation/database.sql"
SCRIPTS_SOURCE_DIR="${SOURCE_DIR}/server/scripte"
APP_CONFIG_FILE="${SOURCE_DIR}/config/config.php"

[ -f "${DB_SQL_FILE}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${DB_SQL_FILE}"
[ -f "${APP_CONFIG_FILE}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${APP_CONFIG_FILE}"
[ -d "${SCRIPTS_SOURCE_DIR}" ] || fatal "${MSG_MISSING_DIR:-Missing required directory}: ${SCRIPTS_SOURCE_DIR}"

info "${MSG_DEPLOY_FILES:-Deploying application files}"
mkdir -p "${DEPLOY_DIR}"
rsync -a --exclude '.git' "${SOURCE_DIR}/" "${DEPLOY_DIR}/" >>"${LOG_FILE}" 2>&1

php_escape_single() {
  printf '%s' "$1" | sed "s/'/\\\\'/g"
}

CFG_DB_HOST="$(php_escape_single "${DB_HOST}")"
CFG_DB_PORT="$(php_escape_single "${DB_PORT}")"
CFG_DB_NAME="$(php_escape_single "${DB_NAME}")"
CFG_DB_USER="$(php_escape_single "${DB_USER}")"
CFG_DB_PASS="$(php_escape_single "${DB_PASS}")"
CFG_SITETOOLS="$(php_escape_single "${SITETOOLS}")"
CFG_LOGIN_THEME="$(php_escape_single "${LOGIN_THEME}")"

TARGET_CONFIG_FILE="${DEPLOY_DIR}/config/config.php"
backup_file "${TARGET_CONFIG_FILE}"

cat > "${TARGET_CONFIG_FILE}" <<PHP
<?php
return [
    'debug' => filter_var(getenv('DEBUG') ?: 'false', FILTER_VALIDATE_BOOL),
    'loginpath' => '${CFG_LOGIN_THEME}',
    'sitetools' => '${CFG_SITETOOLS}',
    'db' => [
        'host' => '${CFG_DB_HOST}',
        'port' => (int)'${CFG_DB_PORT}',
        'dbname' => '${CFG_DB_NAME}',
        'user' => '${CFG_DB_USER}',
        'pass' => '${CFG_DB_PASS}',
        'charset' => 'utf8mb4',
    ],
    'session' => [
        'table'    => 'sessions2',
        'lifetime' => 3600,
    ],
    'lang' => 'de_DE',
    'vpn_server_config' => [
        'source_url' => getenv('OVPN_SERVER_CONFIG_URL') ?: '',
        'source_path' => getenv('OVPN_SERVER_CONFIG_PATH') ?: (dirname(__DIR__) . '/storage/conf/server/server.conf'),
        'save_path' => getenv('OVPN_SERVER_CONFIG_SAVE_PATH') ?: (dirname(__DIR__) . '/storage/conf/server/server.conf'),
        'history_path' => getenv('OVPN_SERVER_CONFIG_HISTORY_PATH') ?: (dirname(__DIR__) . '/storage/conf/history/server'),
        'auth_header' => getenv('OVPN_SERVER_CONFIG_AUTH_HEADER') ?: '',
        'timeout' => 6,
    ],
];
PHP

mysql_user_exec() {
  local sql="$1"
  mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USER}" -p"${DB_PASS}" -e "${sql}"
}

mysql_root_exec() {
  local sql="$1"
  if [ -n "${DB_ROOT_PASSWORD}" ]; then
    mysql -uroot -p"${DB_ROOT_PASSWORD}" -e "${sql}"
  else
    mysql -uroot -e "${sql}"
  fi
}

sql_escape_single() {
  printf '%s' "$1" | sed "s/'/''/g"
}

if [ "${DB_CREATE_LOCAL}" = "yes" ]; then
  info "${MSG_DB_PREPARE_LOCAL:-Preparing local database and user}"
  DB_NAME_SQL="$(sql_escape_single "${DB_NAME}")"
  DB_USER_SQL="$(sql_escape_single "${DB_USER}")"
  DB_PASS_SQL="$(sql_escape_single "${DB_PASS}")"

  mysql_root_exec "CREATE DATABASE IF NOT EXISTS \\`${DB_NAME_SQL}\\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  mysql_root_exec "CREATE USER IF NOT EXISTS '${DB_USER_SQL}'@'localhost' IDENTIFIED BY '${DB_PASS_SQL}';"
  mysql_root_exec "GRANT ALL PRIVILEGES ON \\`${DB_NAME_SQL}\\`.* TO '${DB_USER_SQL}'@'localhost';"
  mysql_root_exec "FLUSH PRIVILEGES;"
fi

info "${MSG_DB_IMPORT:-Importing database schema}"
mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < "${DB_SQL_FILE}"

ADMIN_HASH="$(php -r 'echo password_hash($argv[1], PASSWORD_DEFAULT);' "${ADMIN_PASS}")"
ADMIN_USER_SQL="$(sql_escape_single "${ADMIN_USER}")"
ADMIN_HASH_SQL="$(sql_escape_single "${ADMIN_HASH}")"

mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" -e "
INSERT INTO groupnames (gid, name)
VALUES (1, 'admin'), (2, 'user')
ON DUPLICATE KEY UPDATE name = VALUES(name);
"

mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" -e "
INSERT INTO user (user_name, gid, user_pass, user_enable, user_start_date, user_end_date, user_online)
VALUES ('${ADMIN_USER_SQL}', 1, '${ADMIN_HASH_SQL}', 1, CURDATE(), NULL, 0)
ON DUPLICATE KEY UPDATE
  gid = VALUES(gid),
  user_pass = VALUES(user_pass),
  user_enable = 1;
"

info "${MSG_OPENVPN_SCRIPTS_DEPLOY:-Deploying OpenVPN scripts}"
install -d -m 0750 "${OPENVPN_SCRIPTS_DIR}"
install -m 0750 "${SCRIPTS_SOURCE_DIR}/connect.sh" "${OPENVPN_SCRIPTS_DIR}/connect.sh"
install -m 0750 "${SCRIPTS_SOURCE_DIR}/disconnect.sh" "${OPENVPN_SCRIPTS_DIR}/disconnect.sh"
install -m 0750 "${SCRIPTS_SOURCE_DIR}/functions.sh" "${OPENVPN_SCRIPTS_DIR}/functions.sh"
install -m 0640 "${SCRIPTS_SOURCE_DIR}/config.sample.sh" "${OPENVPN_SCRIPTS_DIR}/config.sh"

DB_HOST_ESC="$(printf '%s' "${DB_HOST}" | sed 's/[\\/&]/\\\\&/g')"
DB_PORT_ESC="$(printf '%s' "${DB_PORT}" | sed 's/[\\/&]/\\\\&/g')"
DB_USER_ESC="$(printf '%s' "${DB_USER}" | sed 's/[\\/&]/\\\\&/g')"
DB_PASS_ESC="$(printf '%s' "${DB_PASS}" | sed 's/[\\/&]/\\\\&/g')"
DB_NAME_ESC="$(printf '%s' "${DB_NAME}" | sed 's/[\\/&]/\\\\&/g')"

sed -i "s/^DBHOST=.*/DBHOST='${DB_HOST_ESC}'/" "${OPENVPN_SCRIPTS_DIR}/config.sh"
sed -i "s/^DBPORT=.*/DBPORT='${DB_PORT_ESC}'/" "${OPENVPN_SCRIPTS_DIR}/config.sh"
sed -i "s/^DBUSER=.*/DBUSER='${DB_USER_ESC}'/" "${OPENVPN_SCRIPTS_DIR}/config.sh"
sed -i "s/^DBPASS=.*/DBPASS='${DB_PASS_ESC}'/" "${OPENVPN_SCRIPTS_DIR}/config.sh"
sed -i "s/^DBNAME=.*/DBNAME='${DB_NAME_ESC}'/" "${OPENVPN_SCRIPTS_DIR}/config.sh"

[ -f "${OPENVPN_SERVER_CONF}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${OPENVPN_SERVER_CONF}"
backup_file "${OPENVPN_SERVER_CONF}"

set_or_append_openvpn_line "${OPENVPN_SERVER_CONF}" "script-security" "script-security 2"
set_or_append_openvpn_line "${OPENVPN_SERVER_CONF}" "client-connect" "client-connect ${OPENVPN_SCRIPTS_DIR}/connect.sh"
set_or_append_openvpn_line "${OPENVPN_SERVER_CONF}" "client-disconnect" "client-disconnect ${OPENVPN_SCRIPTS_DIR}/disconnect.sh"

mkdir -p "${DEPLOY_DIR}/storage/logs"
chown -R "${WEB_OWNER}:${WEB_GROUP}" "${DEPLOY_DIR}"
chown -R root:root "${OPENVPN_SCRIPTS_DIR}"

info "${MSG_DONE:-Setup finished successfully.}"
info "${MSG_DONE_NEXT:-Restart OpenVPN and your webserver to apply all changes.}"
