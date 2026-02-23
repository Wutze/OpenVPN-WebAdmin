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
# @fork Original Idea and parts in this script from: https://github.com/Chocobozzz/OpenVPN-Admin
#
# @author    Wutze
# @copyright 2026 OpenVPN-WebAdmin
# @link			https://github.com/Wutze/OpenVPN-WebAdmin
# @see				Internal Documentation ~/doc/
# @version		2.0.0
# @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# shellcheck disable=SC1091
source "${SCRIPT_DIR}/functions.sh"

SOURCE_DIR=""
DEPLOY_DIR=""
UPDATE_SOURCE="yes"
SOURCE_BRANCH=""

collect_update_inputs() {
  show_section "${MSG_SECTION_INPUT:-Update parameters}"

  ask_input SOURCE_DIR "${MSG_SOURCE_DIR:-Local source directory}" "${INSTALL_SOURCE_DIR}"
  ask_input DEPLOY_DIR "${MSG_DEPLOY_DIR:-WebAdmin target directory}" "${DEFAULT_DEPLOY_DIR}"
  ask_yes_no UPDATE_SOURCE "${MSG_UPDATE_SOURCE:-Update source repository before deploy}" "yes"

  if [ "${UPDATE_SOURCE}" = "yes" ]; then
    ask_input SOURCE_BRANCH "${MSG_REPO_BRANCH:-Repository branch}" "${INSTALL_REPO_BRANCH}"
  fi
}

validate_update_inputs() {
  show_section "${MSG_SECTION_VALIDATE:-Validation}"

  [ -d "${SOURCE_DIR}" ] || fatal "${MSG_MISSING_DIR:-Missing required directory}: ${SOURCE_DIR}"
  [ -d "${DEPLOY_DIR}" ] || fatal "${MSG_MISSING_DIR:-Missing required directory}: ${DEPLOY_DIR}"

  if [ "${UPDATE_SOURCE}" = "yes" ] && [ ! -d "${SOURCE_DIR}/.git" ]; then
    fatal "${MSG_SOURCE_NOT_GIT:-Source directory is not a git repository}: ${SOURCE_DIR}"
  fi

  ok "${MSG_INPUT_VALID:-All input values are valid.}"
}

confirm_update() {
  show_section "${MSG_SECTION_SUMMARY:-Summary}"
  echo " ${MSG_SOURCE_DIR:-Local source directory}: ${SOURCE_DIR}"
  echo " ${MSG_DEPLOY_DIR:-WebAdmin target directory}: ${DEPLOY_DIR}"
  echo " ${MSG_UPDATE_SOURCE:-Update source repository before deploy}: ${UPDATE_SOURCE}"
  if [ "${UPDATE_SOURCE}" = "yes" ]; then
    echo " ${MSG_REPO_BRANCH:-Repository branch}: ${SOURCE_BRANCH}"
  fi
  echo " ${MSG_UPDATE_ONLY_PHP:-Only PHP files will be updated. config/config.php and .env stay unchanged.}"

  ask_yes_no CONFIRM_UPDATE "${MSG_CONFIRM_UPDATE:-Start update with these settings}" "yes"
  [ "${CONFIRM_UPDATE}" = "yes" ] || fatal "${MSG_ABORTED_BY_USER:-Aborted by user.}"
}

update_source_repo() {
  [ "${UPDATE_SOURCE}" = "yes" ] || return 0

  show_section "${MSG_SECTION_REPO:-Source repository}"
  info "${MSG_REPO_UPDATE:-Updating existing source repository}"

  git -C "${SOURCE_DIR}" fetch --all --prune >>"${LOG_FILE}" 2>&1
  git -C "${SOURCE_DIR}" checkout "${SOURCE_BRANCH}" >>"${LOG_FILE}" 2>&1
  git -C "${SOURCE_DIR}" pull --ff-only origin "${SOURCE_BRANCH}" >>"${LOG_FILE}" 2>&1

  ok "${MSG_REPO_READY:-Source repository is ready.}"
}

deploy_php_update() {
  show_section "${MSG_SECTION_DEPLOY:-Deploy files}"

  local deploy_uid deploy_gid
  deploy_uid="$(stat -c '%u' "${DEPLOY_DIR}")"
  deploy_gid="$(stat -c '%g' "${DEPLOY_DIR}")"

  rsync -rt \
    --include='*/' \
    --include='*.php' \
    --exclude='config/config.php' \
    --exclude='.env' \
    --exclude='*' \
    "${SOURCE_DIR}/" "${DEPLOY_DIR}/" >>"${LOG_FILE}" 2>&1

  find "${DEPLOY_DIR}" -type f -name '*.php' ! -path "${DEPLOY_DIR}/config/config.php" -exec chown "${deploy_uid}:${deploy_gid}" {} + >>"${LOG_FILE}" 2>&1

  ok "${MSG_UPDATE_DONE:-PHP update completed.}"
}

main() {
  load_install_config
  require_root
  LOG_FILE="${LOG_FILE:-/var/log/openvpn-webadmin-update.log}"
  init_log
  show_header

  ensure_cmd whiptail
  ensure_cmd git
  ensure_cmd rsync
  ensure_cmd find
  choose_language

  collect_update_inputs
  validate_update_inputs
  confirm_update
  update_source_repo
  deploy_php_update

  show_section "${MSG_SECTION_DONE:-Finished}"
  ok "${MSG_DONE:-Update finished successfully.}"
  info "${MSG_LOG_HINT:-Log file}: ${LOG_FILE}"
}

main "$@"
