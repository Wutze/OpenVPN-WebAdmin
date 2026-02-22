#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck disable=SC1091
source "${SCRIPT_DIR}/functions.sh"

REPO_URL=""
REPO_BRANCH=""
SOURCE_DIR=""
DEPLOY_DIR=""
WEB_OWNER=""
WEB_GROUP=""
LOGIN_THEME=""
SITETOOLS=""

DB_HOST=""
DB_PORT=""
DB_NAME=""
DB_USER=""
DB_PASS=""
DB_CREATE_LOCAL=""
DB_ROOT_PASSWORD=""

ADMIN_USER=""
ADMIN_PASS=""

OPENVPN_SERVER_CONF=""
OPENVPN_SCRIPTS_DIR=""
WEBSERVER_PACKAGE=""

PACKAGES=()

setup_prelude() {
  load_install_config
  init_log
  show_header
  choose_language

  show_section "${MSG_SECTION_SYSTEM_CHECK:-System check}"
  require_root
  detect_os
  assert_supported_os
  ensure_cmd apt-get
  ensure_cmd sed
  ensure_cmd grep
}

collect_inputs() {
  show_section "${MSG_SECTION_INPUT:-Installation parameters}"

  ask_input REPO_URL "${MSG_REPO_URL:-Repository URL}" "${INSTALL_REPO_URL}"
  ask_input REPO_BRANCH "${MSG_REPO_BRANCH:-Repository branch}" "${INSTALL_REPO_BRANCH}"
  ask_input SOURCE_DIR "${MSG_SOURCE_DIR:-Local source directory}" "${INSTALL_SOURCE_DIR}"
  ask_input DEPLOY_DIR "${MSG_DEPLOY_DIR:-WebAdmin target directory}" "${DEFAULT_DEPLOY_DIR}"
  ask_input WEB_OWNER "${MSG_WEB_OWNER:-Web owner user}" "${DEFAULT_WEB_OWNER}"
  ask_input WEB_GROUP "${MSG_WEB_GROUP:-Web owner group}" "${DEFAULT_WEB_GROUP}"
  ask_input LOGIN_THEME "${MSG_LOGIN_THEME:-Login theme (login1/login2/login3)}" "${DEFAULT_LOGIN_THEME}"
  ask_input SITETOOLS "${MSG_SITETOOLS:-Local sitetools path (no external URL)}" "${DEFAULT_SITETOOLS}"

  ask_input DB_HOST "${MSG_DB_HOST:-Database host}" "${DEFAULT_DB_HOST}"
  ask_input DB_PORT "${MSG_DB_PORT:-Database port}" "${DEFAULT_DB_PORT}"
  ask_input DB_NAME "${MSG_DB_NAME:-Database name}" "${DEFAULT_DB_NAME}"
  ask_input DB_USER "${MSG_DB_USER:-Database user}" "${DEFAULT_DB_USER}"
  ask_secret DB_PASS "${MSG_DB_PASS:-Database password}"

  local db_local_default="no"
  if [ "${DB_HOST}" = "localhost" ] || [ "${DB_HOST}" = "127.0.0.1" ]; then
    db_local_default="yes"
  fi
  ask_yes_no DB_CREATE_LOCAL "${MSG_DB_CREATE_LOCAL:-Create local MariaDB database and user}" "${db_local_default}"

  DB_ROOT_PASSWORD=""
  if [ "${DB_CREATE_LOCAL}" = "yes" ]; then
    ask_input DB_ROOT_PASSWORD "${MSG_DB_ROOT_PASS_OPTIONAL:-MariaDB root password (leave empty for socket auth)}" ""
  fi

  ask_input ADMIN_USER "${MSG_ADMIN_USER:-Initial admin username}" "admin"
  ask_secret ADMIN_PASS "${MSG_ADMIN_PASS:-Initial admin password}"

  local default_conf_path="${DEFAULT_OPENVPN_SERVER_CONF}"
  if [ ! -f "${default_conf_path}" ] && [ -f "${ALT_OPENVPN_SERVER_CONF}" ]; then
    default_conf_path="${ALT_OPENVPN_SERVER_CONF}"
  fi
  ask_input OPENVPN_SERVER_CONF "${MSG_OPENVPN_CONF:-OpenVPN server.conf path}" "${default_conf_path}"
  ask_input OPENVPN_SCRIPTS_DIR "${MSG_OPENVPN_SCRIPTS_DIR:-OpenVPN scripts directory}" "${DEFAULT_OPENVPN_SCRIPTS_DIR}"

  ask_input WEBSERVER_PACKAGE "${MSG_WEBSERVER_PACKAGE:-Optional webserver package (none/apache2/nginx)}" "none"
}

validate_inputs() {
  show_section "${MSG_SECTION_VALIDATE:-Validation}"

  case "${WEBSERVER_PACKAGE}" in
    none|apache2|nginx) ;;
    *) fatal "${MSG_INVALID_WEBSERVER:-Invalid webserver package option.}" ;;
  esac

  if [[ "${SITETOOLS}" =~ ^https?:// ]]; then
    fatal "${MSG_SITETOOLS_EXTERNAL_FORBIDDEN:-External sitetools URLs are not allowed.}"
  fi

  ensure_absolute_path "${SOURCE_DIR}" || fatal "${MSG_PATH_ABSOLUTE_REQUIRED:-Path must be absolute}: ${SOURCE_DIR}"
  ensure_absolute_path "${DEPLOY_DIR}" || fatal "${MSG_PATH_ABSOLUTE_REQUIRED:-Path must be absolute}: ${DEPLOY_DIR}"
  ensure_absolute_path "${OPENVPN_SCRIPTS_DIR}" || fatal "${MSG_PATH_ABSOLUTE_REQUIRED:-Path must be absolute}: ${OPENVPN_SCRIPTS_DIR}"

  id -u "${WEB_OWNER}" >/dev/null 2>&1 || fatal "${MSG_USER_NOT_FOUND:-User not found}: ${WEB_OWNER}"
  getent group "${WEB_GROUP}" >/dev/null 2>&1 || fatal "${MSG_GROUP_NOT_FOUND:-Group not found}: ${WEB_GROUP}"

  ok "${MSG_INPUT_VALID:-All input values are valid.}"
}

build_package_list() {
  PACKAGES=(
    git
    rsync
    ca-certificates
    curl
    unzip
    tar
    sed
    gawk
    grep
    openvpn
    mariadb-client
    php-cli
    php-common
    php-mysql
    php-mbstring
    php-xml
    php-curl
    php-zip
    php-intl
    php-gd
    php-fpm
  )

  if [ "${DB_CREATE_LOCAL}" = "yes" ]; then
    PACKAGES+=(mariadb-server)
  fi

  case "${WEBSERVER_PACKAGE}" in
    apache2)
      PACKAGES+=(apache2 libapache2-mod-php)
      ;;
    nginx)
      PACKAGES+=(nginx)
      ;;
  esac
}

show_summary() {
  show_section "${MSG_SECTION_SUMMARY:-Summary}"
  echo " ${MSG_REPO_URL:-Repository URL}: ${REPO_URL}"
  echo " ${MSG_REPO_BRANCH:-Repository branch}: ${REPO_BRANCH}"
  echo " ${MSG_SOURCE_DIR:-Local source directory}: ${SOURCE_DIR}"
  echo " ${MSG_DEPLOY_DIR:-WebAdmin target directory}: ${DEPLOY_DIR}"
  echo " ${MSG_WEB_OWNER:-Web owner user}: ${WEB_OWNER}:${WEB_GROUP}"
  echo " ${MSG_LOGIN_THEME:-Login theme (login1/login2/login3)}: ${LOGIN_THEME}"
  echo " ${MSG_SITETOOLS:-Local sitetools path (no external URL)}: ${SITETOOLS}"
  echo " ${MSG_DB_HOST:-Database host}: ${DB_HOST}:${DB_PORT}/${DB_NAME}"
  echo " ${MSG_DB_USER:-Database user}: ${DB_USER}"
  echo " ${MSG_DB_CREATE_LOCAL:-Create local MariaDB database and user}: ${DB_CREATE_LOCAL}"
  echo " ${MSG_OPENVPN_CONF:-OpenVPN server.conf path}: ${OPENVPN_SERVER_CONF}"
  echo " ${MSG_OPENVPN_SCRIPTS_DIR:-OpenVPN scripts directory}: ${OPENVPN_SCRIPTS_DIR}"
  echo " ${MSG_WEBSERVER_PACKAGE:-Optional webserver package (none/apache2/nginx)}: ${WEBSERVER_PACKAGE}"

  echo
  echo " ${MSG_REQUIRED_PACKAGES:-Required packages}:"
  local p
  for p in "${PACKAGES[@]}"; do
    echo "  - ${p}"
  done

  ask_yes_no CONFIRM_INSTALL "${MSG_CONFIRM_INSTALL:-Start installation with these settings}" "yes"
  [ "${CONFIRM_INSTALL}" = "yes" ] || fatal "${MSG_ABORTED_BY_USER:-Aborted by user.}"
}

install_required_packages() {
  show_section "${MSG_SECTION_PACKAGES:-Install packages}"
  info "${MSG_INSTALL_PACKAGES:-Installing required packages}"

  apt-get update -y >>"${LOG_FILE}" 2>&1
  DEBIAN_FRONTEND=noninteractive apt-get install -y "${PACKAGES[@]}" >>"${LOG_FILE}" 2>&1

  if [ "${DB_CREATE_LOCAL}" = "yes" ]; then
    systemctl enable --now mariadb >>"${LOG_FILE}" 2>&1 || systemctl enable --now mysql >>"${LOG_FILE}" 2>&1
  fi

  ok "${MSG_PACKAGES_DONE:-Packages installed.}"
}

sync_source_repo() {
  show_section "${MSG_SECTION_REPO:-Source repository}"
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

  ok "${MSG_REPO_READY:-Source repository is ready.}"
}

deploy_application_files() {
  show_section "${MSG_SECTION_DEPLOY:-Deploy files}"

  local db_sql_file="${SOURCE_DIR}/server/installation/database.sql"
  local scripts_source_dir="${SOURCE_DIR}/server/scripte"
  local app_config_file="${SOURCE_DIR}/config/config.php"

  [ -f "${db_sql_file}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${db_sql_file}"
  [ -f "${app_config_file}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${app_config_file}"
  [ -d "${scripts_source_dir}" ] || fatal "${MSG_MISSING_DIR:-Missing required directory}: ${scripts_source_dir}"

  mkdir -p "${DEPLOY_DIR}"
  rsync -a --exclude '.git' "${SOURCE_DIR}/" "${DEPLOY_DIR}/" >>"${LOG_FILE}" 2>&1
  mkdir -p "${DEPLOY_DIR}/storage/logs"

  ok "${MSG_DEPLOY_FILES_DONE:-Application files deployed.}"
}

write_app_config() {
  show_section "${MSG_SECTION_CONFIG:-Application config}"

  local target_config_file="${DEPLOY_DIR}/config/config.php"
  backup_file "${target_config_file}"

  local cfg_db_host cfg_db_port cfg_db_name cfg_db_user cfg_db_pass cfg_sitetools cfg_login_theme
  cfg_db_host="$(printf '%s' "${DB_HOST}" | sed "s/'/\\\\'/g")"
  cfg_db_port="$(printf '%s' "${DB_PORT}" | sed "s/'/\\\\'/g")"
  cfg_db_name="$(printf '%s' "${DB_NAME}" | sed "s/'/\\\\'/g")"
  cfg_db_user="$(printf '%s' "${DB_USER}" | sed "s/'/\\\\'/g")"
  cfg_db_pass="$(printf '%s' "${DB_PASS}" | sed "s/'/\\\\'/g")"
  cfg_sitetools="$(printf '%s' "${SITETOOLS}" | sed "s/'/\\\\'/g")"
  cfg_login_theme="$(printf '%s' "${LOGIN_THEME}" | sed "s/'/\\\\'/g")"

  cat > "${target_config_file}" <<PHP
<?php
return [
    'debug' => filter_var(getenv('DEBUG') ?: 'false', FILTER_VALIDATE_BOOL),
    'loginpath' => '${cfg_login_theme}',
    'sitetools' => '${cfg_sitetools}',
    'db' => [
        'host' => '${cfg_db_host}',
        'port' => (int)'${cfg_db_port}',
        'dbname' => '${cfg_db_name}',
        'user' => '${cfg_db_user}',
        'pass' => '${cfg_db_pass}',
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

  ok "${MSG_CONFIG_WRITTEN:-Application config written.}"
}

setup_database() {
  show_section "${MSG_SECTION_DATABASE:-Database setup}"

  local db_sql_file="${SOURCE_DIR}/server/installation/database.sql"

  sql_escape_single() {
    printf '%s' "$1" | sed "s/'/''/g"
  }

  mysql_root_exec() {
    local sql="$1"
    if [ -n "${DB_ROOT_PASSWORD}" ]; then
      mysql -uroot -p"${DB_ROOT_PASSWORD}" -e "${sql}"
    else
      mysql -uroot -e "${sql}"
    fi
  }

  if [ "${DB_CREATE_LOCAL}" = "yes" ]; then
    local db_name_sql db_user_sql db_pass_sql
    db_name_sql="$(sql_escape_single "${DB_NAME}")"
    db_user_sql="$(sql_escape_single "${DB_USER}")"
    db_pass_sql="$(sql_escape_single "${DB_PASS}")"

    mysql_root_exec "CREATE DATABASE IF NOT EXISTS \\`${db_name_sql}\\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql_root_exec "CREATE USER IF NOT EXISTS '${db_user_sql}'@'localhost' IDENTIFIED BY '${db_pass_sql}';"
    mysql_root_exec "GRANT ALL PRIVILEGES ON \\`${db_name_sql}\\`.* TO '${db_user_sql}'@'localhost';"
    mysql_root_exec "FLUSH PRIVILEGES;"
  fi

  mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < "${db_sql_file}"

  local admin_hash admin_user_sql admin_hash_sql
  admin_hash="$(php -r 'echo password_hash($argv[1], PASSWORD_DEFAULT);' "${ADMIN_PASS}")"
  admin_user_sql="$(sql_escape_single "${ADMIN_USER}")"
  admin_hash_sql="$(sql_escape_single "${admin_hash}")"

  mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" -e "
INSERT INTO groupnames (gid, name)
VALUES (1, 'admin'), (2, 'user')
ON DUPLICATE KEY UPDATE name = VALUES(name);
"

  mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" -e "
INSERT INTO user (user_name, gid, user_pass, user_enable, user_start_date, user_end_date, user_online)
VALUES ('${admin_user_sql}', 1, '${admin_hash_sql}', 1, CURDATE(), NULL, 0)
ON DUPLICATE KEY UPDATE
  gid = VALUES(gid),
  user_pass = VALUES(user_pass),
  user_enable = 1;
"

  ok "${MSG_DATABASE_DONE:-Database setup done.}"
}

deploy_openvpn_scripts() {
  show_section "${MSG_SECTION_OPENVPN:-OpenVPN integration}"

  local scripts_source_dir="${SOURCE_DIR}/server/scripte"

  [ -f "${OPENVPN_SERVER_CONF}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${OPENVPN_SERVER_CONF}"

  install -d -m 0750 "${OPENVPN_SCRIPTS_DIR}"
  install -m 0750 "${scripts_source_dir}/connect.sh" "${OPENVPN_SCRIPTS_DIR}/connect.sh"
  install -m 0750 "${scripts_source_dir}/disconnect.sh" "${OPENVPN_SCRIPTS_DIR}/disconnect.sh"
  install -m 0750 "${scripts_source_dir}/functions.sh" "${OPENVPN_SCRIPTS_DIR}/functions.sh"
  install -m 0640 "${scripts_source_dir}/config.sample.sh" "${OPENVPN_SCRIPTS_DIR}/config.sh"

  local db_host_esc db_port_esc db_user_esc db_pass_esc db_name_esc
  db_host_esc="$(printf '%s' "${DB_HOST}" | sed 's/[\\/&]/\\\\&/g')"
  db_port_esc="$(printf '%s' "${DB_PORT}" | sed 's/[\\/&]/\\\\&/g')"
  db_user_esc="$(printf '%s' "${DB_USER}" | sed 's/[\\/&]/\\\\&/g')"
  db_pass_esc="$(printf '%s' "${DB_PASS}" | sed 's/[\\/&]/\\\\&/g')"
  db_name_esc="$(printf '%s' "${DB_NAME}" | sed 's/[\\/&]/\\\\&/g')"

  sed -i "s/^DBHOST=.*/DBHOST='${db_host_esc}'/" "${OPENVPN_SCRIPTS_DIR}/config.sh"
  sed -i "s/^DBPORT=.*/DBPORT='${db_port_esc}'/" "${OPENVPN_SCRIPTS_DIR}/config.sh"
  sed -i "s/^DBUSER=.*/DBUSER='${db_user_esc}'/" "${OPENVPN_SCRIPTS_DIR}/config.sh"
  sed -i "s/^DBPASS=.*/DBPASS='${db_pass_esc}'/" "${OPENVPN_SCRIPTS_DIR}/config.sh"
  sed -i "s/^DBNAME=.*/DBNAME='${db_name_esc}'/" "${OPENVPN_SCRIPTS_DIR}/config.sh"

  backup_file "${OPENVPN_SERVER_CONF}"
  set_or_append_openvpn_line "${OPENVPN_SERVER_CONF}" "script-security" "script-security 2"
  set_or_append_openvpn_line "${OPENVPN_SERVER_CONF}" "client-connect" "client-connect ${OPENVPN_SCRIPTS_DIR}/connect.sh"
  set_or_append_openvpn_line "${OPENVPN_SERVER_CONF}" "client-disconnect" "client-disconnect ${OPENVPN_SCRIPTS_DIR}/disconnect.sh"

  chown -R root:root "${OPENVPN_SCRIPTS_DIR}"
  chmod 0750 "${OPENVPN_SCRIPTS_DIR}"/*.sh

  ok "${MSG_OPENVPN_DONE:-OpenVPN integration done.}"
}

finalize_permissions() {
  show_section "${MSG_SECTION_PERMS:-Permissions}"
  chown -R "${WEB_OWNER}:${WEB_GROUP}" "${DEPLOY_DIR}"
  ok "${MSG_PERMS_DONE:-Permissions updated.}"
}

finish_message() {
  show_section "${MSG_SECTION_DONE:-Finished}"
  ok "${MSG_DONE:-Setup finished successfully.}"
  info "${MSG_DONE_NEXT:-Restart OpenVPN and your webserver to apply all changes.}"
  info "${MSG_LOG_HINT:-Setup log file}: ${LOG_FILE}"
}

main() {
  setup_prelude
  collect_inputs
  validate_inputs
  build_package_list
  show_summary
  install_required_packages
  sync_source_repo
  deploy_application_files
  write_app_config
  setup_database
  deploy_openvpn_scripts
  finalize_permissions
  finish_message
}

main "$@"
