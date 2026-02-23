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
WEBSERVER_CONFIGURE="no"
WEBSERVER_TARGET=""
WEBSERVER_MODE="standalone"
WEBSERVER_SUBDIR="/openvpnwebadmin"
WEBSERVER_SERVER_NAME="_"
ENABLE_REWRITE="no"
ASSETS_ALLOW_NPM_FALLBACK="yes"

PACKAGES=()

input_preview() {
  local key="$1"
  case "${key}" in
    DB_PASS|ADMIN_PASS)
      printf '%s' "********"
      ;;
    DB_ROOT_PASSWORD)
      if [ -n "${DB_ROOT_PASSWORD}" ]; then
        printf '%s' "********"
      else
        printf '%s' "(empty)"
      fi
      ;;
    *)
      local value="${!key-}"
      if [ -n "${value}" ]; then
        printf '%.46s' "${value}"
      else
        printf '%s' "(empty)"
      fi
      ;;
  esac
}

edit_single_input() {
  local key="$1"

  case "${key}" in
    REPO_URL) ask_input REPO_URL "${MSG_REPO_URL:-Repository URL}" "${REPO_URL}" ;;
    REPO_BRANCH) ask_input REPO_BRANCH "${MSG_REPO_BRANCH:-Repository branch}" "${REPO_BRANCH}" ;;
    SOURCE_DIR) ask_input SOURCE_DIR "${MSG_SOURCE_DIR:-Local source directory}" "${SOURCE_DIR}" ;;
    DEPLOY_DIR) ask_input DEPLOY_DIR "${MSG_DEPLOY_DIR:-WebAdmin target directory}" "${DEPLOY_DIR}" ;;
    WEB_OWNER) ask_input WEB_OWNER "${MSG_WEB_OWNER:-Web owner user}" "${WEB_OWNER}" ;;
    WEB_GROUP) ask_input WEB_GROUP "${MSG_WEB_GROUP:-Web owner group}" "${WEB_GROUP}" ;;
    LOGIN_THEME)
      ask_choice LOGIN_THEME "${MSG_LOGIN_THEME:-Login theme (login1/login2/login3)}" "${LOGIN_THEME}" \
        "login1" "login1" \
        "login2" "login2" \
        "login3" "login3"
      ;;
    SITETOOLS) ask_input SITETOOLS "${MSG_SITETOOLS:-Local sitetools path (no external URL)}" "${SITETOOLS}" ;;
    DB_HOST) ask_input DB_HOST "${MSG_DB_HOST:-Database host}" "${DB_HOST}" ;;
    DB_PORT) ask_input DB_PORT "${MSG_DB_PORT:-Database port}" "${DB_PORT}" ;;
    DB_NAME) ask_input DB_NAME "${MSG_DB_NAME:-Database name}" "${DB_NAME}" ;;
    DB_USER) ask_input DB_USER "${MSG_DB_USER:-Database user}" "${DB_USER}" ;;
    DB_PASS) ask_secret DB_PASS "${MSG_DB_PASS:-Database password}" ;;
    DB_CREATE_LOCAL)
      ask_yes_no DB_CREATE_LOCAL "${MSG_DB_CREATE_LOCAL:-Create local MariaDB database and user}" "${DB_CREATE_LOCAL}"
      if [ "${DB_CREATE_LOCAL}" = "no" ]; then
        DB_ROOT_PASSWORD=""
      fi
      ;;
    DB_ROOT_PASSWORD)
      if [ "${DB_CREATE_LOCAL}" = "yes" ]; then
        ask_input DB_ROOT_PASSWORD "${MSG_DB_ROOT_PASS_OPTIONAL:-MariaDB root password (leave empty for socket auth)}" "${DB_ROOT_PASSWORD}"
      fi
      ;;
    ADMIN_USER) ask_input ADMIN_USER "${MSG_ADMIN_USER:-Initial admin username}" "${ADMIN_USER}" ;;
    ADMIN_PASS) ask_secret ADMIN_PASS "${MSG_ADMIN_PASS:-Initial admin password}" ;;
    OPENVPN_SERVER_CONF) ask_input OPENVPN_SERVER_CONF "${MSG_OPENVPN_CONF:-OpenVPN server.conf path}" "${OPENVPN_SERVER_CONF}" ;;
    OPENVPN_SCRIPTS_DIR) ask_input OPENVPN_SCRIPTS_DIR "${MSG_OPENVPN_SCRIPTS_DIR:-OpenVPN scripts directory}" "${OPENVPN_SCRIPTS_DIR}" ;;
    WEBSERVER_PACKAGE)
      ask_choice WEBSERVER_PACKAGE "${MSG_WEBSERVER_PACKAGE:-Optional webserver package (none/apache2/nginx)}" "${WEBSERVER_PACKAGE}" \
        "none" "none" \
        "apache2" "apache2" \
        "nginx" "nginx"
      collect_webserver_options
      ;;
    WEBSERVER_CONFIGURE)
      ask_yes_no WEBSERVER_CONFIGURE "${MSG_WEBSERVER_CONFIGURE:-Configure webserver automatically now}" "${WEBSERVER_CONFIGURE}"
      ;;
    WEBSERVER_TARGET)
      ask_choice WEBSERVER_TARGET "${MSG_WEBSERVER_TARGET:-Webserver type for configuration}" "${WEBSERVER_TARGET}" \
        "apache2" "apache2" \
        "nginx" "nginx"
      ;;
    WEBSERVER_MODE)
      ask_choice WEBSERVER_MODE "${MSG_WEBSERVER_USE_SUBDIR:-Expose app under /openvpnwebadmin path instead of standalone site}" "${WEBSERVER_MODE}" \
        "subdir" "${MSG_MODE_SUBDIR:-Subdirectory (/openvpnwebadmin)}" \
        "standalone" "${MSG_MODE_STANDALONE:-Standalone site}"
      ;;
    WEBSERVER_SUBDIR)
      ask_input WEBSERVER_SUBDIR "${MSG_WEBSERVER_SUBDIR:-Subdirectory path}" "${WEBSERVER_SUBDIR}"
      ;;
    WEBSERVER_SERVER_NAME)
      ask_input WEBSERVER_SERVER_NAME "${MSG_WEBSERVER_SERVER_NAME:-ServerName / Hostname}" "${WEBSERVER_SERVER_NAME}"
      ;;
    ENABLE_REWRITE)
      ask_yes_no ENABLE_REWRITE "${MSG_USE_REWRITE:-Enable rewrite mode}" "${ENABLE_REWRITE}"
      ;;
    ASSETS_ALLOW_NPM_FALLBACK)
      ask_yes_no ASSETS_ALLOW_NPM_FALLBACK "${MSG_ASSETS_ALLOW_NPM_FALLBACK:-If prebuilt asset archive is missing, build with npm fallback}" "${ASSETS_ALLOW_NPM_FALLBACK}"
      ;;
  esac
}

review_inputs_menu() {
  local choice=""

  while true; do
    choice="$("${WHIPTAIL_BIN}" \
      --title "OpenVPN-WebAdmin Setup" \
      --menu "${MSG_EDIT_MENU_PROMPT:-Select a field to edit or continue}" \
      24 96 16 \
      "continue" "${MSG_EDIT_CONTINUE:-Continue with these values}" \
      "REPO_URL" "$(input_preview REPO_URL)" \
      "REPO_BRANCH" "$(input_preview REPO_BRANCH)" \
      "SOURCE_DIR" "$(input_preview SOURCE_DIR)" \
      "DEPLOY_DIR" "$(input_preview DEPLOY_DIR)" \
      "WEB_OWNER" "$(input_preview WEB_OWNER)" \
      "WEB_GROUP" "$(input_preview WEB_GROUP)" \
      "LOGIN_THEME" "$(input_preview LOGIN_THEME)" \
      "SITETOOLS" "$(input_preview SITETOOLS)" \
      "DB_HOST" "$(input_preview DB_HOST)" \
      "DB_PORT" "$(input_preview DB_PORT)" \
      "DB_NAME" "$(input_preview DB_NAME)" \
      "DB_USER" "$(input_preview DB_USER)" \
      "DB_PASS" "$(input_preview DB_PASS)" \
      "DB_CREATE_LOCAL" "$(input_preview DB_CREATE_LOCAL)" \
      "DB_ROOT_PASSWORD" "$(input_preview DB_ROOT_PASSWORD)" \
      "ADMIN_USER" "$(input_preview ADMIN_USER)" \
      "ADMIN_PASS" "$(input_preview ADMIN_PASS)" \
      "OPENVPN_SERVER_CONF" "$(input_preview OPENVPN_SERVER_CONF)" \
      "OPENVPN_SCRIPTS_DIR" "$(input_preview OPENVPN_SCRIPTS_DIR)" \
      "WEBSERVER_PACKAGE" "$(input_preview WEBSERVER_PACKAGE)" \
      "WEBSERVER_CONFIGURE" "$(input_preview WEBSERVER_CONFIGURE)" \
      "WEBSERVER_TARGET" "$(input_preview WEBSERVER_TARGET)" \
      "WEBSERVER_MODE" "$(input_preview WEBSERVER_MODE)" \
      "WEBSERVER_SUBDIR" "$(input_preview WEBSERVER_SUBDIR)" \
      "WEBSERVER_SERVER_NAME" "$(input_preview WEBSERVER_SERVER_NAME)" \
      "ENABLE_REWRITE" "$(input_preview ENABLE_REWRITE)" \
      "ASSETS_ALLOW_NPM_FALLBACK" "$(input_preview ASSETS_ALLOW_NPM_FALLBACK)" \
      3>&1 1>&2 2>&3)" || fatal "${MSG_ABORTED_BY_USER:-Aborted by user.}"

    [ "${choice}" = "continue" ] && break
    edit_single_input "${choice}"
  done
}

setup_prelude() {
  load_install_config
  init_log
  show_header
  ensure_cmd whiptail
  choose_language

  show_section "${MSG_SECTION_SYSTEM_CHECK:-System check}"
  require_root
  detect_os
  assert_supported_os
  ensure_cmd apt-get
  ensure_cmd sed
  ensure_cmd grep
}

normalize_subdir_path() {
  local value="$1"
  value="${value%/}"
  if [ -z "${value}" ]; then
    value="/openvpnwebadmin"
  fi
  case "${value}" in
    /*) ;;
    *) value="/${value}" ;;
  esac
  printf '%s' "${value}"
}

collect_webserver_options() {
  WEBSERVER_TARGET="${WEBSERVER_PACKAGE}"
  WEBSERVER_MODE="standalone"
  WEBSERVER_SUBDIR="/openvpnwebadmin"
  WEBSERVER_SERVER_NAME="_"
  ENABLE_REWRITE="no"

  if [ "${WEBSERVER_PACKAGE}" = "none" ]; then
    ask_yes_no WEBSERVER_CONFIGURE "${MSG_WEBSERVER_CONFIGURE_EXISTING:-Configure an existing webserver now}" "no"
    if [ "${WEBSERVER_CONFIGURE}" = "yes" ]; then
      ask_choice WEBSERVER_TARGET "${MSG_WEBSERVER_TARGET:-Webserver type for configuration}" "apache2" \
        "apache2" "apache2" \
        "nginx" "nginx"
      ask_yes_no WEBSERVER_MODE_SUBDIR "${MSG_WEBSERVER_USE_SUBDIR_EXISTING:-Expose app under /openvpnwebadmin path (like phpMyAdmin)}" "yes"
      if [ "${WEBSERVER_MODE_SUBDIR}" = "yes" ]; then
        WEBSERVER_MODE="subdir"
        ask_input WEBSERVER_SUBDIR "${MSG_WEBSERVER_SUBDIR:-Subdirectory path}" "/openvpnwebadmin"
      else
        WEBSERVER_MODE="standalone"
        ask_input WEBSERVER_SERVER_NAME "${MSG_WEBSERVER_SERVER_NAME:-ServerName / Hostname}" "_"
      fi
      ask_yes_no ENABLE_REWRITE "${MSG_USE_REWRITE:-Enable rewrite mode}" "yes"
    fi
    return
  fi

  ask_yes_no WEBSERVER_CONFIGURE "${MSG_WEBSERVER_CONFIGURE:-Configure webserver automatically now}" "yes"
  if [ "${WEBSERVER_CONFIGURE}" = "yes" ]; then
    ask_yes_no WEBSERVER_MODE_SUBDIR "${MSG_WEBSERVER_USE_SUBDIR:-Expose app under /openvpnwebadmin path instead of standalone site}" "no"
    if [ "${WEBSERVER_MODE_SUBDIR}" = "yes" ]; then
      WEBSERVER_MODE="subdir"
      ask_input WEBSERVER_SUBDIR "${MSG_WEBSERVER_SUBDIR:-Subdirectory path}" "/openvpnwebadmin"
    else
      WEBSERVER_MODE="standalone"
      ask_input WEBSERVER_SERVER_NAME "${MSG_WEBSERVER_SERVER_NAME:-ServerName / Hostname}" "_"
    fi
    ask_yes_no ENABLE_REWRITE "${MSG_USE_REWRITE:-Enable rewrite mode}" "yes"
  fi
}

collect_inputs() {
  show_section "${MSG_SECTION_INPUT:-Installation parameters}"

  ask_input REPO_URL "${MSG_REPO_URL:-Repository URL}" "${INSTALL_REPO_URL}"
  ask_input REPO_BRANCH "${MSG_REPO_BRANCH:-Repository branch}" "${INSTALL_REPO_BRANCH}"
  ask_input SOURCE_DIR "${MSG_SOURCE_DIR:-Local source directory}" "${INSTALL_SOURCE_DIR}"
  ask_input DEPLOY_DIR "${MSG_DEPLOY_DIR:-WebAdmin target directory}" "${DEFAULT_DEPLOY_DIR}"
  ask_input WEB_OWNER "${MSG_WEB_OWNER:-Web owner user}" "${DEFAULT_WEB_OWNER}"
  ask_input WEB_GROUP "${MSG_WEB_GROUP:-Web owner group}" "${DEFAULT_WEB_GROUP}"
  ask_choice LOGIN_THEME "${MSG_LOGIN_THEME:-Login theme (login1/login2/login3)}" "${DEFAULT_LOGIN_THEME}" \
    "login1" "login1" \
    "login2" "login2" \
    "login3" "login3"
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

  ask_choice WEBSERVER_PACKAGE "${MSG_WEBSERVER_PACKAGE:-Optional webserver package (none/apache2/nginx)}" "none" \
    "none" "none" \
    "apache2" "apache2" \
    "nginx" "nginx"

  collect_webserver_options
  ask_yes_no ASSETS_ALLOW_NPM_FALLBACK "${MSG_ASSETS_ALLOW_NPM_FALLBACK:-If prebuilt asset archive is missing, build with npm fallback}" "yes"

  review_inputs_menu
}

validate_inputs() {
  show_section "${MSG_SECTION_VALIDATE:-Validation}"

  case "${WEBSERVER_PACKAGE}" in
    none|apache2|nginx) ;;
    *) fatal "${MSG_INVALID_WEBSERVER:-Invalid webserver package option.}" ;;
  esac

  case "${WEBSERVER_CONFIGURE}" in
    yes|no) ;;
    *) fatal "${MSG_INVALID_WEBSERVER_CONFIG:-Invalid webserver configuration option.}" ;;
  esac

  case "${ASSETS_ALLOW_NPM_FALLBACK}" in
    yes|no) ;;
    *) fatal "${MSG_INVALID_ASSETS_FALLBACK:-Invalid assets fallback option.}" ;;
  esac

  if [ "${WEBSERVER_CONFIGURE}" = "yes" ]; then
    case "${WEBSERVER_TARGET}" in
      apache2|nginx) ;;
      *) fatal "${MSG_INVALID_WEBSERVER:-Invalid webserver package option.}" ;;
    esac

    case "${WEBSERVER_MODE}" in
      standalone|subdir) ;;
      *) fatal "${MSG_INVALID_WEBSERVER_MODE:-Invalid webserver mode.}" ;;
    esac

    case "${ENABLE_REWRITE}" in
      yes|no) ;;
      *) fatal "${MSG_INVALID_REWRITE_OPTION:-Invalid rewrite option.}" ;;
    esac

    if [ "${WEBSERVER_MODE}" = "subdir" ]; then
      WEBSERVER_SUBDIR="$(normalize_subdir_path "${WEBSERVER_SUBDIR}")"
      if ! [[ "${WEBSERVER_SUBDIR}" =~ ^/[A-Za-z0-9._/-]+$ ]]; then
        fatal "${MSG_INVALID_SUBDIR:-Invalid subdirectory path}: ${WEBSERVER_SUBDIR}"
      fi
    else
      [ -n "${WEBSERVER_SERVER_NAME}" ] || fatal "${MSG_INVALID_SERVER_NAME:-ServerName must not be empty.}"
    fi
  fi

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
    easy-rsa
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

ensure_npm_runtime() {
  if command -v npm >/dev/null 2>&1 && command -v node >/dev/null 2>&1; then
    return 0
  fi

  info "${MSG_ASSETS_INSTALL_NPM:-Installing npm/nodejs for fallback asset build}"
  apt-get update >>"${LOG_FILE}" 2>&1
  DEBIAN_FRONTEND=noninteractive apt-get install -y nodejs npm >>"${LOG_FILE}" 2>&1
}

build_and_deploy_tools_assets() {
  show_section "${MSG_SECTION_ASSETS:-Frontend assets}"

  local assets_dir="${DEPLOY_DIR}/assets-build"
  local build_script="${assets_dir}/scripts/build-tools-assets.sh"
  local deploy_script="${assets_dir}/scripts/deploy-tools-assets.sh"
  local archive_path="${assets_dir}/release/tools-assets.tar.gz"
  local archive_sha="${assets_dir}/release/tools-assets.tar.gz.sha256"

  [ -d "${assets_dir}" ] || fatal "${MSG_MISSING_DIR:-Missing required directory}: ${assets_dir}"
  [ -f "${deploy_script}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${deploy_script}"

  if [ -f "${archive_path}" ]; then
    info "${MSG_ASSETS_USE_ARCHIVE:-Using prebuilt frontend asset archive}"
    if [ -f "${archive_sha}" ]; then
      bash "${deploy_script}" "${DEPLOY_DIR}/public" "${archive_path}" "${archive_sha}" >>"${LOG_FILE}" 2>&1
    else
      bash "${deploy_script}" "${DEPLOY_DIR}/public" "${archive_path}" >>"${LOG_FILE}" 2>&1
    fi
    ok "${MSG_ASSETS_DONE:-Frontend assets are ready in /tools.}"
    return 0
  fi

  [ -f "${build_script}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${build_script}"
  [ "${ASSETS_ALLOW_NPM_FALLBACK}" = "yes" ] || fatal "${MSG_ASSETS_ARCHIVE_MISSING:-Prebuilt asset archive missing and npm fallback is disabled.}"

  warn "${MSG_ASSETS_ARCHIVE_MISSING_FALLBACK:-Prebuilt asset archive missing. Using npm fallback build.}"
  ensure_npm_runtime
  info "${MSG_ASSETS_BUILD:-Building local frontend asset package}"
  (
    cd "${assets_dir}"
    bash "${build_script}"
  ) >>"${LOG_FILE}" 2>&1
  info "${MSG_ASSETS_DEPLOY:-Deploying frontend assets to public/tools}"
  bash "${deploy_script}" "${DEPLOY_DIR}/public" >>"${LOG_FILE}" 2>&1

  ok "${MSG_ASSETS_DONE:-Frontend assets are ready in /tools.}"
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
  echo " ${MSG_WEBSERVER_CONFIGURE:-Configure webserver automatically now}: ${WEBSERVER_CONFIGURE}"
  if [ "${WEBSERVER_CONFIGURE}" = "yes" ]; then
    echo " ${MSG_WEBSERVER_TARGET:-Webserver type for configuration}: ${WEBSERVER_TARGET}"
    echo " ${MSG_WEBSERVER_MODE:-Webserver mode}: ${WEBSERVER_MODE}"
    if [ "${WEBSERVER_MODE}" = "subdir" ]; then
      echo " ${MSG_WEBSERVER_SUBDIR:-Subdirectory path}: ${WEBSERVER_SUBDIR}"
    else
      echo " ${MSG_WEBSERVER_SERVER_NAME:-ServerName / Hostname}: ${WEBSERVER_SERVER_NAME}"
    fi
    echo " ${MSG_USE_REWRITE:-Enable rewrite mode}: ${ENABLE_REWRITE}"
  fi
  echo " ${MSG_ASSETS_ALLOW_NPM_FALLBACK:-If prebuilt asset archive is missing, build with npm fallback}: ${ASSETS_ALLOW_NPM_FALLBACK}"

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

  apt-get update >>"${LOG_FILE}" 2>&1
  local total count pkg
  total="${#PACKAGES[@]}"
  count=0

  for pkg in "${PACKAGES[@]}"; do
    count=$((count + 1))
    render_progress_bar "${count}" "${total}" "${MSG_INSTALL_PACKAGES:-Installing required packages}"
    DEBIAN_FRONTEND=noninteractive apt-get install -y "${pkg}" >>"${LOG_FILE}" 2>&1
  done
  echo

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
  local app_config_template_file="${SOURCE_DIR}/config/config.conf"

  [ -f "${db_sql_file}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${db_sql_file}"
  [ -f "${app_config_template_file}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${app_config_template_file}"
  [ -d "${scripts_source_dir}" ] || fatal "${MSG_MISSING_DIR:-Missing required directory}: ${scripts_source_dir}"

  mkdir -p "${DEPLOY_DIR}"
  rsync -a --exclude '.git' "${SOURCE_DIR}/" "${DEPLOY_DIR}/" >>"${LOG_FILE}" 2>&1
  mkdir -p "${DEPLOY_DIR}/storage/logs"

  ok "${MSG_DEPLOY_FILES_DONE:-Application files deployed.}"
}

write_app_config() {
  show_section "${MSG_SECTION_CONFIG:-Application config}"

  local target_config_file="${DEPLOY_DIR}/config/config.php"
  local target_template_file="${DEPLOY_DIR}/config/config.conf"
  backup_file "${target_config_file}"

  [ -f "${target_template_file}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${target_template_file}"

  local cfg_db_host cfg_db_port cfg_db_name cfg_db_user cfg_db_pass cfg_sitetools cfg_login_theme cfg_rewrite
  cfg_db_host="$(printf '%s' "${DB_HOST}" | sed "s/'/\\\\'/g")"
  cfg_db_port="$(printf '%s' "${DB_PORT}" | sed "s/'/\\\\'/g")"
  cfg_db_name="$(printf '%s' "${DB_NAME}" | sed "s/'/\\\\'/g")"
  cfg_db_user="$(printf '%s' "${DB_USER}" | sed "s/'/\\\\'/g")"
  cfg_db_pass="$(printf '%s' "${DB_PASS}" | sed "s/'/\\\\'/g")"
  cfg_sitetools="$(printf '%s' "${SITETOOLS}" | sed "s/'/\\\\'/g")"
  cfg_login_theme="$(printf '%s' "${LOGIN_THEME}" | sed "s/'/\\\\'/g")"
  if [ "${ENABLE_REWRITE}" = "yes" ]; then
    cfg_rewrite="true"
  else
    cfg_rewrite="false"
  fi

  cp "${target_template_file}" "${target_config_file}"

  sed_escape_repl() {
    printf '%s' "$1" | sed -e 's/[\/&|]/\\&/g'
  }

  local repl_db_host repl_db_port repl_db_name repl_db_user repl_db_pass repl_sitetools repl_login_theme repl_rewrite
  repl_db_host="$(sed_escape_repl "${cfg_db_host}")"
  repl_db_port="$(sed_escape_repl "${cfg_db_port}")"
  repl_db_name="$(sed_escape_repl "${cfg_db_name}")"
  repl_db_user="$(sed_escape_repl "${cfg_db_user}")"
  repl_db_pass="$(sed_escape_repl "${cfg_db_pass}")"
  repl_sitetools="$(sed_escape_repl "${cfg_sitetools}")"
  repl_login_theme="$(sed_escape_repl "${cfg_login_theme}")"
  repl_rewrite="$(sed_escape_repl "${cfg_rewrite}")"

  sed -i \
    -e "s|__CFG_REWRITE__|${repl_rewrite}|g" \
    -e "s|__CFG_LOGIN_THEME__|${repl_login_theme}|g" \
    -e "s|__CFG_SITETOOLS__|${repl_sitetools}|g" \
    -e "s|__CFG_DB_HOST__|${repl_db_host}|g" \
    -e "s|__CFG_DB_PORT__|${repl_db_port}|g" \
    -e "s|__CFG_DB_NAME__|${repl_db_name}|g" \
    -e "s|__CFG_DB_USER__|${repl_db_user}|g" \
    -e "s|__CFG_DB_PASS__|${repl_db_pass}|g" \
    "${target_config_file}"

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

write_default_openvpn_server_conf() {
  local target_file="/etc/openvpn/server.conf"
  [ -f "${target_file}" ] && return 0

  mkdir -p /etc/openvpn
  cat > "${target_file}" <<'EOF'
# OpenVPN server default configuration generated by OpenVPN-WebAdmin setup
# Adjust certificate/key paths for your environment before starting the service.

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

ifconfig-pool-persist /var/log/openvpn/ipp.txt
status /var/log/openvpn/openvpn-status.log
log-append /var/log/openvpn/openvpn.log
verb 3

keepalive 10 120
persist-key
persist-tun
explicit-exit-notify 1

script-security 2
client-config-dir /etc/openvpn/ccd

# OpenVPN-WebAdmin hooks (installed by setup)
client-connect /etc/openvpn/scripts/connect.sh
client-disconnect /etc/openvpn/scripts/disconnect.sh
EOF

  chmod 0644 "${target_file}"
  ok "${MSG_OPENVPN_DEFAULT_CONF_CREATED:-Created default OpenVPN config}: ${target_file}"
}

deploy_openvpn_scripts() {
  show_section "${MSG_SECTION_OPENVPN:-OpenVPN integration}"

  local scripts_source_dir="${SOURCE_DIR}/server/scripte"
  local server_conf_template="${SOURCE_DIR}/storage/conf/server/server.conf"

  write_default_openvpn_server_conf

  if [ ! -f "${OPENVPN_SERVER_CONF}" ]; then
    [ -f "${server_conf_template}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${OPENVPN_SERVER_CONF}"
    mkdir -p "$(dirname "${OPENVPN_SERVER_CONF}")"
    install -m 0644 "${server_conf_template}" "${OPENVPN_SERVER_CONF}"
    ok "${MSG_OPENVPN_CONF_BOOTSTRAP:-OpenVPN server.conf created from template.}"
  fi

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
  mkdir -p /etc/openvpn/ccd

  if systemctl list-unit-files | grep -q '^openvpn-server@.service'; then
    systemctl enable --now openvpn-server@server.service >>"${LOG_FILE}" 2>&1
    ok "${MSG_OPENVPN_SERVICE_ENABLED:-OpenVPN server service enabled}: openvpn-server@server.service"
  elif systemctl list-unit-files | grep -q '^openvpn@.service'; then
    systemctl enable --now openvpn@server.service >>"${LOG_FILE}" 2>&1
    ok "${MSG_OPENVPN_SERVICE_ENABLED:-OpenVPN server service enabled}: openvpn@server.service"
  elif systemctl list-unit-files | grep -q '^openvpn.service'; then
    systemctl enable --now openvpn.service >>"${LOG_FILE}" 2>&1
    ok "${MSG_OPENVPN_SERVICE_ENABLED:-OpenVPN server service enabled}: openvpn.service"
  else
    warn "${MSG_OPENVPN_SERVICE_UNKNOWN:-OpenVPN service unit not found. Please enable the service manually.}"
  fi

  ok "${MSG_OPENVPN_DONE:-OpenVPN integration done.}"
}

detect_php_fpm_upstream() {
  local sock
  for sock in /run/php/php*-fpm.sock /run/php-fpm/www.sock /run/php/php-fpm.sock; do
    if [ -S "${sock}" ]; then
      printf 'unix:%s' "${sock}"
      return
    fi
  done
  printf '127.0.0.1:9000'
}

configure_apache_webadmin() {
  local allow_override="None"
  [ "${ENABLE_REWRITE}" = "yes" ] && allow_override="All"

  if [ "${WEBSERVER_MODE}" = "standalone" ]; then
    local site_file="/etc/apache2/sites-available/openvpn-webadmin.conf"
    cat > "${site_file}" <<EOF
<VirtualHost *:80>
    ServerName ${WEBSERVER_SERVER_NAME}
    DocumentRoot ${DEPLOY_DIR}/public

    <Directory ${DEPLOY_DIR}/public>
        Options FollowSymLinks
        AllowOverride ${allow_override}
        Require all granted
        DirectoryIndex index.php
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/openvpn-webadmin-error.log
    CustomLog \${APACHE_LOG_DIR}/openvpn-webadmin-access.log combined
</VirtualHost>
EOF
    a2ensite openvpn-webadmin.conf >>"${LOG_FILE}" 2>&1
  else
    local conf_file="/etc/apache2/conf-available/openvpn-webadmin.conf"
    cat > "${conf_file}" <<EOF
Alias ${WEBSERVER_SUBDIR} ${DEPLOY_DIR}/public
Alias ${WEBSERVER_SUBDIR}/ ${DEPLOY_DIR}/public/

<Directory ${DEPLOY_DIR}/public>
    Options FollowSymLinks
    AllowOverride None
    Require all granted
    DirectoryIndex index.php
EOF
    if [ "${ENABLE_REWRITE}" = "yes" ]; then
      cat >> "${conf_file}" <<EOF
    FallbackResource ${WEBSERVER_SUBDIR}/index.php
EOF
    fi
    cat >> "${conf_file}" <<EOF
</Directory>
EOF
    a2enconf openvpn-webadmin.conf >>"${LOG_FILE}" 2>&1
  fi

  if [ "${ENABLE_REWRITE}" = "yes" ]; then
    a2enmod rewrite >>"${LOG_FILE}" 2>&1
  fi

  apache2ctl configtest >>"${LOG_FILE}" 2>&1
  systemctl enable --now apache2 >>"${LOG_FILE}" 2>&1
  systemctl reload apache2 >>"${LOG_FILE}" 2>&1
}

detect_nginx_site_file() {
  if [ -f /etc/nginx/sites-available/default ]; then
    printf '%s' "/etc/nginx/sites-available/default"
    return
  fi

  local first_enabled
  first_enabled="$(find /etc/nginx/sites-enabled -maxdepth 1 -type f 2>/dev/null | head -n1 || true)"
  if [ -n "${first_enabled}" ]; then
    printf '%s' "${first_enabled}"
    return
  fi

  printf '%s' "/etc/nginx/conf.d/default.conf"
}

configure_nginx_webadmin() {
  local php_upstream
  php_upstream="$(detect_php_fpm_upstream)"

  if [ "${WEBSERVER_MODE}" = "standalone" ]; then
    local site_file="/etc/nginx/sites-available/openvpn-webadmin.conf"
    cat > "${site_file}" <<EOF
server {
    listen 80;
    server_name ${WEBSERVER_SERVER_NAME};
    root ${DEPLOY_DIR}/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass ${php_upstream};
    }

    location ~ /\. {
        deny all;
    }
}
EOF
    ln -sfn "${site_file}" /etc/nginx/sites-enabled/openvpn-webadmin.conf
  else
    local snippet_file="/etc/nginx/snippets/openvpn-webadmin-location.conf"
    local nginx_target
    nginx_target="$(detect_nginx_site_file)"
    if [ -e "${nginx_target}" ]; then
      nginx_target="$(readlink -f "${nginx_target}")"
    fi
    mkdir -p /etc/nginx/snippets

    cat > "${snippet_file}" <<EOF
location = ${WEBSERVER_SUBDIR} {
    return 301 ${WEBSERVER_SUBDIR}/;
}

location ~ ^${WEBSERVER_SUBDIR}/index\.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_param SCRIPT_FILENAME ${DEPLOY_DIR}/public/index.php;
    fastcgi_param SCRIPT_NAME ${WEBSERVER_SUBDIR}/index.php;
    fastcgi_pass ${php_upstream};
}

location ${WEBSERVER_SUBDIR}/ {
    alias ${DEPLOY_DIR}/public/;
    index index.php;
    try_files \$uri \$uri/ ${WEBSERVER_SUBDIR}/index.php?\$query_string;
}
EOF

    touch "${nginx_target}"
    if ! grep -Eq "server[[:space:]]*\\{" "${nginx_target}"; then
      fatal "${MSG_NGINX_SERVER_BLOCK_MISSING:-No server block found in nginx target config}: ${nginx_target}"
    fi

    if ! grep -q "include /etc/nginx/snippets/openvpn-webadmin-location.conf;" "${nginx_target}"; then
      sed -i '0,/server[[:space:]]*{/s|server[[:space:]]*{|&\
    include /etc/nginx/snippets/openvpn-webadmin-location.conf;|' "${nginx_target}"
    fi
  fi

  nginx -t >>"${LOG_FILE}" 2>&1
  systemctl enable --now nginx >>"${LOG_FILE}" 2>&1
  systemctl reload nginx >>"${LOG_FILE}" 2>&1
}

configure_webserver() {
  show_section "${MSG_SECTION_WEBSERVER:-Webserver setup}"

  if [ "${WEBSERVER_CONFIGURE}" != "yes" ]; then
    info "${MSG_WEBSERVER_SKIP:-Skipping automatic webserver configuration.}"
    return
  fi

  if [ "${WEBSERVER_TARGET}" = "apache2" ]; then
    ensure_cmd apache2ctl
    ensure_cmd a2enmod
    configure_apache_webadmin
  else
    ensure_cmd nginx
    configure_nginx_webadmin
  fi

  ok "${MSG_WEBSERVER_CONFIG_DONE:-Webserver configuration installed and enabled.}"
}

finalize_permissions() {
  show_section "${MSG_SECTION_PERMS:-Permissions}"
  chown -R "${WEB_OWNER}:${WEB_GROUP}" "${DEPLOY_DIR}"
  ok "${MSG_PERMS_DONE:-Permissions updated.}"
}

check_runtime_permissions() {
  show_section "${MSG_SECTION_PERMS_CHECK:-Permission check}"

  local target
  local failed=0
  local -a must_write=(
    "${DEPLOY_DIR}/storage"
    "${DEPLOY_DIR}/storage/conf"
    "${DEPLOY_DIR}/storage/conf/history"
    "${DEPLOY_DIR}/storage/logs"
  )

  while IFS= read -r target; do
    [ -n "${target}" ] || continue
    must_write+=("$(dirname "${target}")")
    must_write+=("${target}")
  done < <(find "${DEPLOY_DIR}/storage/conf" -mindepth 2 -maxdepth 2 -type f -name 'client.ovpn' 2>/dev/null)

  for target in "${must_write[@]}"; do
    if [ ! -e "${target}" ]; then
      warn "${MSG_PERMS_MISSING:-Path missing for permission check}: ${target}"
      failed=1
      continue
    fi

    if su -s /bin/sh -c "test -w \"$target\"" "${WEB_OWNER}" >/dev/null 2>&1; then
      ok "${MSG_PERMS_OK:-Writable for web user}: ${target}"
    else
      warn "${MSG_PERMS_FAIL:-Not writable for web user}: ${target}"
      failed=1
    fi
  done

  if [ "${failed}" -eq 0 ]; then
    ok "${MSG_PERMS_CHECK_OK:-Permission check passed.}"
    return
  fi

  warn "${MSG_PERMS_FIX:-Attempting permission fix...}"
  chown -R "${WEB_OWNER}:${WEB_GROUP}" "${DEPLOY_DIR}/storage"
  find "${DEPLOY_DIR}/storage" -type d -exec chmod 0775 {} +
  find "${DEPLOY_DIR}/storage" -type f -exec chmod 0664 {} +

  failed=0
  for target in "${must_write[@]}"; do
    [ -e "${target}" ] || continue
    if ! su -s /bin/sh -c "test -w \"$target\"" "${WEB_OWNER}" >/dev/null 2>&1; then
      failed=1
      warn "${MSG_PERMS_STILL_FAIL:-Still not writable after fix}: ${target}"
    fi
  done

  if [ "${failed}" -ne 0 ]; then
    fatal "${MSG_PERMS_CHECK_FATAL:-Permission check failed after fix. Please adjust ownership/ACL manually.}"
  fi

  ok "${MSG_PERMS_CHECK_FIXED:-Permission check passed after automatic fix.}"
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
  build_and_deploy_tools_assets
  write_app_config
  configure_webserver
  setup_database
  deploy_openvpn_scripts
  finalize_permissions
  check_runtime_permissions
  finish_message
}

main "$@"
