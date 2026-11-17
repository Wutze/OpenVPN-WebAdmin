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

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Detect the current system timezone without modifying it. Prefer systemd's
# configured timezone, then derive the IANA name from /etc/localtime.
detect_system_timezone() {
  local timezone localtime_path

  if command -v timedatectl >/dev/null 2>&1; then
    timezone="$(timedatectl show --property=Timezone --value 2>/dev/null || true)"
    if timezone_is_valid "$timezone"; then
      printf '%s\n' "$timezone"
      return 0
    fi
  fi

  localtime_path="$(readlink -f /etc/localtime 2>/dev/null || true)"
  case "$localtime_path" in
    /usr/share/zoneinfo/*)
      timezone="${localtime_path#/usr/share/zoneinfo/}"
      if timezone_is_valid "$timezone"; then
        printf '%s\n' "$timezone"
        return 0
      fi
      ;;
  esac

  return 1
}

timezone_is_valid() {
  local timezone="$1"
  [[ "$timezone" != *..* ]] \
    && [[ "$timezone" =~ ^[A-Za-z0-9._+-]+(/[A-Za-z0-9._+-]+)*$ ]] \
    && [ -f "/usr/share/zoneinfo/${timezone}" ]
}

timezone_start_utc() {
  local timezone="$1" date_value="$2" epoch
  epoch="$(TZ="$timezone" date --date="$date_value 00:00:00" +%s)" || return 1
  date --date="@$epoch" -u +%Y%m%d%H%M%SZ
}

timezone_end_utc() {
  local timezone="$1" date_value="$2" next_day epoch
  next_day="$(TZ="$timezone" date --date="$date_value + 1 day" +%F)" || return 1
  epoch="$(TZ="$timezone" date --date="$next_day 00:00:00" +%s)" || return 1
  date --date="@$((epoch - 1))" -u +%Y%m%d%H%M%SZ
}

one_year_after_timezone_start_utc() {
  local timezone="$1" date_value="$2" anniversary epoch
  anniversary="$(TZ="$timezone" date --date="$date_value + 1 year" +%F)" || return 1
  epoch="$(TZ="$timezone" date --date="$anniversary 00:00:00" +%s)" || return 1
  date --date="@$((epoch - 1))" -u +%Y%m%d%H%M%SZ
}

date_in_timezone() {
  local timezone="$1" epoch="$2"
  TZ="$timezone" date --date="@$epoch" +%F
}

cert_time_utc() {
  local cert="$1" field="$2" value
  value="$(openssl x509 -in "$cert" -noout -"$field" | sed 's/^[^=]*=//')" || return 1
  date --date="$value" -u +%Y%m%d%H%M%SZ
}

# Return 0 when the stored DB date pair matches, 1 when no snapshot exists,
# and 2 when a snapshot exists but is unreadable, malformed, or changed.
certificate_limit_snapshot_status() {
  local snapshot="$1" start_date="$2" end_date="$3" stored_start stored_end
  [ -e "$snapshot" ] || return 1
  [ -r "$snapshot" ] || return 2
  IFS=$'\t' read -r stored_start stored_end < "$snapshot" || return 2
  [ "$stored_start" = "${start_date:-NULL}" ] \
    && [ "$stored_end" = "${end_date:-NULL}" ] || return 2
  return 0
}

write_certificate_limit_snapshot() {
  local snapshot="$1" start_date="$2" end_date="$3" directory temporary
  directory="${snapshot%/*}"
  install -d -m 0700 "$directory" || return 1
  temporary="$(mktemp "${directory}/.limits.XXXXXX")" || return 1
  if ! printf '%s\t%s\n' "${start_date:-NULL}" "${end_date:-NULL}" > "$temporary"; then
    rm -f -- "$temporary"
    return 1
  fi
  chmod 0600 "$temporary" || { rm -f -- "$temporary"; return 1; }
  mv -f -- "$temporary" "$snapshot"
}

CONFIG_FILE="${SCRIPT_DIR}/config.sh"
LANG_DIR="${SCRIPT_DIR}/lang"

LOG_FILE=""
OS_ID=""
OS_VERSION_ID=""
OS_CODENAME=""
LANG_CHOICE=""

COL_NC='\033[0m'
COL_BLUE='\033[1;34m'
COL_GREEN='\033[1;32m'
COL_RED='\033[1;31m'
COL_YELLOW='\033[1;33m'

TICK="[${COL_GREEN}OK${COL_NC}]"
CROSS="[${COL_RED}!!${COL_NC}]"
INFO_TAG="[${COL_BLUE}..${COL_NC}]"
WARN_TAG="[${COL_YELLOW}??${COL_NC}]"
WHIPTAIL_BIN="${WHIPTAIL_BIN:-$(command -v whiptail || true)}"
WHIPTAIL_HEIGHT=16
WHIPTAIL_WIDTH=84
WHIPTAIL_MENU_HEIGHT=10

init_log() {
  LOG_FILE="${LOG_FILE:-${LOG_FILE_DEFAULT:-/var/log/openvpn-webadmin-setup.log}}"
  mkdir -p "$(dirname "${LOG_FILE}")"
  touch "${LOG_FILE}"
}

log_line() {
  local level="$1"
  shift
  local msg="$*"
  printf '%s [%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "${level}" "${msg}" >> "${LOG_FILE}"
}

info() {
  echo -e " ${INFO_TAG} $*"
  log_line INFO "$*"
}

warn() {
  echo -e " ${WARN_TAG} $*"
  log_line WARN "$*"
}

ok() {
  echo -e " ${TICK} $*"
  log_line INFO "$*"
}

error() {
  echo -e " ${CROSS} $*"
  log_line ERROR "$*"
}

fatal() {
  error "$*"
  exit 1
}

abort_by_user() {
  fatal "${MSG_ABORTED_BY_USER:-Aborted by user.}"
}

print_line() {
  printf '%*s\n' "${1:-78}" '' | tr ' ' '-'
}

show_header() {
  clear
  print_line 78
  echo " OpenVPN-WebAdmin Setup"
  echo " $(date '+%Y-%m-%d %H:%M:%S')"
  print_line 78
}

show_section() {
  echo
  print_line 78
  echo " $*"
  print_line 78
  log_line INFO "SECTION: $*"
}

prompt_continue() {
  local message="${1:-Press ENTER to continue.}"
  "${WHIPTAIL_BIN}" \
    --title "OpenVPN-WebAdmin Setup" \
    --msgbox "${message}" \
    "${WHIPTAIL_HEIGHT}" "${WHIPTAIL_WIDTH}"
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
  local auto_lang="" lang_file lang_code lang_label lang_num="" i
  local -a language_args=() language_codes=()
  local locale="${LANG%%.*}"

  shopt -s nullglob
  local -a language_files=("${LANG_DIR}"/*)
  shopt -u nullglob
  for lang_file in "${language_files[@]}"; do
    [ -f "${lang_file}" ] || continue
    lang_code="${lang_file##*/}"
    [[ "${lang_code}" =~ ^[a-z]{2}_[A-Z]{2}$ ]] || continue
    # shellcheck disable=SC1090
    lang_label="$(unset LANGUAGE; source "${lang_file}"; printf '%s' "${LANGUAGE:-${lang_code}}")"
    language_codes+=("${lang_code}")
    language_args+=("${lang_code}" "${lang_label} (${lang_code})")
    [[ "${locale}" == "${lang_code}"* ]] && auto_lang="${lang_code}"
  done
  ((${#language_codes[@]} > 0)) || fatal "No supported language files found in ${LANG_DIR}."
  if [ -z "${auto_lang}" ]; then
    for lang_code in "${language_codes[@]}"; do
      [[ "${lang_code}" == "en_GB" ]] && auto_lang="${lang_code}" && break
    done
  fi
  [ -n "${auto_lang}" ] || auto_lang="${language_codes[0]}"

  # Establish a fallback translation set before rendering prompts. Languages
  # may not yet contain every message key; the selected file overrides these.
  load_language "${auto_lang}"

  local lang_num=""
  show_section "${MSG_SECTION_LANGUAGE:-Language}"

  if [ -n "${WHIPTAIL_BIN}" ]; then
    lang_num="$("${WHIPTAIL_BIN}" \
      --title "OpenVPN-WebAdmin Setup" \
      --menu "${MSG_LANGUAGE_SELECT:-Select language}" \
      "${WHIPTAIL_HEIGHT}" "${WHIPTAIL_WIDTH}" "${WHIPTAIL_MENU_HEIGHT}" \
      "auto" "Auto (${auto_lang})" "${language_args[@]}" \
      3>&1 1>&2 2>&3)" || abort_by_user
  else
    printf '%s\n' "${MSG_LANGUAGE_SELECT:-Select language}"
    printf '  auto) Auto (%s)\n' "${auto_lang}"
    for ((i=0; i<${#language_codes[@]}; i++)); do
      printf '  %s) %s\n' "${language_codes[i]}" "${language_args[i*2+1]}"
    done
    read -r -p "${MSG_LANGUAGE_SELECT:-Select language} [auto]: " lang_num
    lang_num="${lang_num:-auto}"
  fi

  LANG_CHOICE="${auto_lang}"
  if [[ " ${language_codes[*]} " == *" ${lang_num} "* ]]; then LANG_CHOICE="${lang_num}"; fi

  load_language "${LANG_CHOICE}"
  ok "${MSG_LANGUAGE_SET:-Language set}: ${LANG_CHOICE}"
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
  ok "${MSG_OS_DETECTED:-Detected OS}: ${OS_ID} ${OS_VERSION_ID} ${OS_CODENAME}"
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

  value="$("${WHIPTAIL_BIN}" \
    --title "OpenVPN-WebAdmin Setup" \
    --inputbox "${prompt}" \
    "${WHIPTAIL_HEIGHT}" "${WHIPTAIL_WIDTH}" "${default_value}" \
    3>&1 1>&2 2>&3)" || abort_by_user

  if [ -n "${default_value}" ] && [ -z "${value}" ]; then
    value="${default_value}"
  fi

  printf -v "${var_name}" '%s' "${value}"
}

ask_secret() {
  local var_name="$1"
  local prompt="$2"
  local value=""

  while true; do
    value="$("${WHIPTAIL_BIN}" \
      --title "OpenVPN-WebAdmin Setup" \
      --passwordbox "${prompt}" \
      "${WHIPTAIL_HEIGHT}" "${WHIPTAIL_WIDTH}" \
      3>&1 1>&2 2>&3)" || abort_by_user
    [ -n "${value}" ] && break
    warn "${MSG_EMPTY_NOT_ALLOWED:-This value must not be empty.}"
  done

  printf -v "${var_name}" '%s' "${value}"
}

ask_yes_no() {
  local var_name="$1"
  local prompt="$2"
  local default_value="${3:-yes}"

  local status=1
  local -a yesno_args=(--yesno)
  case "${default_value}" in
    no|NO|n|N) yesno_args=(--defaultno --yesno) ;;
  esac

  if "${WHIPTAIL_BIN}" \
    --title "OpenVPN-WebAdmin Setup" \
    "${yesno_args[@]}" "${prompt}" \
    "${WHIPTAIL_HEIGHT}" "${WHIPTAIL_WIDTH}"; then
    status=0
  else
    status=$?
  fi

  case "${status}" in
    0) printf -v "${var_name}" '%s' "yes" ;;
    1) printf -v "${var_name}" '%s' "no" ;;
    *) abort_by_user ;;
  esac
}

ask_choice() {
  local var_name="$1"
  local prompt="$2"
  local default_value="$3"
  shift 3

  local value=""
  local -a options=("$@")
  local default_tag="${default_value}"
  local has_default=0
  local i=0

  if [ "${#options[@]}" -lt 2 ] || [ $(( ${#options[@]} % 2 )) -ne 0 ]; then
    fatal "ask_choice requires tag/label option pairs."
  fi

  for ((i = 0; i < ${#options[@]}; i += 2)); do
    if [ "${options[$i]}" = "${default_tag}" ]; then
      has_default=1
      break
    fi
  done

  if [ "${has_default}" -eq 0 ]; then
    default_tag="${options[0]}"
  fi

  value="$("${WHIPTAIL_BIN}" \
    --title "OpenVPN-WebAdmin Setup" \
    --menu "${prompt}" \
    "${WHIPTAIL_HEIGHT}" "${WHIPTAIL_WIDTH}" "${WHIPTAIL_MENU_HEIGHT}" \
    "${options[@]}" \
    3>&1 1>&2 2>&3)" || abort_by_user

  if [ -z "${value}" ]; then
    value="${default_tag}"
  fi

  printf -v "${var_name}" '%s' "${value}"
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
  ok "${MSG_BACKUP_CREATED:-Backup created}: ${backup}"
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

render_progress_bar() {
  local current="$1"
  local total="$2"
  local label="${3:-}"
  local width=40
  local percent=0
  local filled=0
  local empty=0

  if [ "${total}" -le 0 ]; then
    total=1
  fi
  if [ "${current}" -lt 0 ]; then
    current=0
  fi
  if [ "${current}" -gt "${total}" ]; then
    current="${total}"
  fi

  percent=$(( current * 100 / total ))
  filled=$(( current * width / total ))
  empty=$(( width - filled ))

  printf "\r %s [" "${label}"
  printf "%${filled}s" '' | tr ' ' '#'
  printf "%${empty}s" '' | tr ' ' '-'
  printf "] %3d%% (%d/%d)" "${percent}" "${current}" "${total}"
}

ask_prefill() {
  local prompt default answer
  if [ "$#" -ge 3 ]; then
    prompt="$2"
    default="$3"
  else
    prompt="$1"
    default="$2"
  fi
  read -r -e -i "$default" -p "${prompt}: " answer
  printf '%s' "${answer:-$default}"
}
