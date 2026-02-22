#!/usr/bin/env bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"

CONFIG_FILE="${SCRIPT_DIR}/config.sh"
LANG_DIR="${SCRIPT_DIR}/lang"

LOG_FILE=""
OS_ID=""
OS_VERSION_ID=""
OS_CODENAME=""
LANG_CHOICE=""

init_log() {
  LOG_FILE="${LOG_FILE:-${LOG_FILE_DEFAULT:-/var/log/openvpn-webadmin-setup.log}}"
  mkdir -p "$(dirname "${LOG_FILE}")"
  touch "${LOG_FILE}"
}

log_line() {
  local level="$1"
  shift
  local msg="$*"
  printf '%s [%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "${level}" "${msg}" | tee -a "${LOG_FILE}" >/dev/null
}

info() { log_line INFO "$*"; }
warn() { log_line WARN "$*"; }
error() { log_line ERROR "$*"; }

fatal() {
  error "$*"
  exit 1
}

load_install_config() {
  [ -f "${CONFIG_FILE}" ] || fatal "Missing config file: ${CONFIG_FILE}"
  # shellcheck disable=SC1090
  source "${CONFIG_FILE}"
}

load_language() {
  local lang_file="${LANG_DIR}/${1}"
  [ -f "${lang_file}" ] || fatal "Missing language file: ${lang_file}"
  # shellcheck disable=SC1090
  source "${lang_file}"
}

choose_language() {
  local auto_lang="en_GB"
  case "${LANG%%.*}" in
    de_DE*) auto_lang="de_DE" ;;
    fr_FR*) auto_lang="fr_FR" ;;
    en_*) auto_lang="en_GB" ;;
  esac

  echo
  echo "1) Deutsch"
  echo "2) English"
  echo "3) Francais"
  read -r -p "${MSG_LANGUAGE_SELECT:-Select language} [1-3, Enter=AUTO]: " lang_num

  case "${lang_num}" in
    1) LANG_CHOICE="de_DE" ;;
    2) LANG_CHOICE="en_GB" ;;
    3) LANG_CHOICE="fr_FR" ;;
    *) LANG_CHOICE="${auto_lang}" ;;
  esac

  load_language "${LANG_CHOICE}"
  info "${MSG_LANGUAGE_SET:-Language set}: ${LANG_CHOICE}"
}

require_root() {
  [ "${EUID}" -eq 0 ] || fatal "${MSG_NEED_ROOT:-Run this setup as root.}"
}

detect_os() {
  [ -f /etc/os-release ] || fatal "${MSG_OS_NOT_FOUND:-/etc/os-release not found.}"
  # shellcheck disable=SC1091
  source /etc/os-release
  OS_ID="${ID:-}"
  OS_VERSION_ID="${VERSION_ID:-}"
  OS_CODENAME="${VERSION_CODENAME:-}"
  info "${MSG_OS_DETECTED:-Detected OS}: ${OS_ID} ${OS_VERSION_ID} ${OS_CODENAME}"
}

version_in_list() {
  local needle="$1"
  shift
  local item
  for item in "$@"; do
    [ "${needle}" = "${item}" ] && return 0
  done
  return 1
}

assert_supported_os() {
  case "${OS_ID}" in
    debian)
      version_in_list "${OS_VERSION_ID}" "${SUPPORTED_DEBIAN_VERSIONS[@]}" || \
        fatal "${MSG_OS_UNSUPPORTED:-Unsupported OS version.} (${OS_ID} ${OS_VERSION_ID})"
      ;;
    ubuntu)
      version_in_list "${OS_VERSION_ID}" "${SUPPORTED_UBUNTU_LTS_VERSIONS[@]}" || \
        fatal "${MSG_OS_UNSUPPORTED:-Unsupported OS version.} (${OS_ID} ${OS_VERSION_ID})"
      ;;
    *)
      fatal "${MSG_OS_UNSUPPORTED:-Unsupported OS version.} (${OS_ID} ${OS_VERSION_ID})"
      ;;
  esac
}

ask_input() {
  local var_name="$1"
  local prompt="$2"
  local default_value="${3:-}"
  local value=""

  if [ -n "${default_value}" ]; then
    read -r -p "${prompt} [${default_value}]: " value
    value="${value:-${default_value}}"
  else
    read -r -p "${prompt}: " value
  fi

  printf -v "${var_name}" '%s' "${value}"
}

ask_secret() {
  local var_name="$1"
  local prompt="$2"
  local value=""

  while true; do
    read -r -s -p "${prompt}: " value
    echo
    [ -n "${value}" ] && break
    warn "${MSG_EMPTY_NOT_ALLOWED:-This value must not be empty.}"
  done

  printf -v "${var_name}" '%s' "${value}"
}

ask_yes_no() {
  local var_name="$1"
  local prompt="$2"
  local default_value="${3:-yes}"
  local answer=""

  while true; do
    read -r -p "${prompt} [yes/no, default=${default_value}]: " answer
    answer="${answer:-${default_value}}"
    case "${answer}" in
      yes|YES|y|Y)
        printf -v "${var_name}" '%s' "yes"
        return 0
        ;;
      no|NO|n|N)
        printf -v "${var_name}" '%s' "no"
        return 0
        ;;
      *)
        warn "${MSG_ANSWER_YES_NO:-Please answer yes or no.}"
        ;;
    esac
  done
}

ensure_absolute_path() {
  case "$1" in
    /*) return 0 ;;
    *) return 1 ;;
  esac
}

backup_file() {
  local file="$1"
  [ -f "${file}" ] || return 0
  local backup="${file}.bak.$(date '+%Y%m%d%H%M%S')"
  cp -a "${file}" "${backup}"
  info "${MSG_BACKUP_CREATED:-Backup created}: ${backup}"
}

append_if_missing() {
  local file="$1"
  local line="$2"
  grep -Fqx "${line}" "${file}" 2>/dev/null || echo "${line}" >> "${file}"
}

set_or_append_openvpn_line() {
  local file="$1"
  local key="$2"
  local full_line="$3"

  if grep -Eq "^[[:space:]]*${key}[[:space:]]+" "${file}"; then
    sed -i "s#^[[:space:]]*${key}[[:space:]].*#${full_line}#" "${file}"
  else
    echo "${full_line}" >> "${file}"
  fi
}

ensure_cmd() {
  command -v "$1" >/dev/null 2>&1 || fatal "${MSG_MISSING_COMMAND:-Missing command}: $1"
}
