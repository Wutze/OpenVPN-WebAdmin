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

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# shellcheck disable=SC1091
source "${SCRIPT_DIR}/functions.sh"

SOURCE_DIR=""
DEPLOY_DIR=""
UPDATE_SOURCE="yes"
SOURCE_BRANCH=""
OPENVPN_SCRIPTS_DIR=""
UPDATE_MODE="latest"
RELEASE_VERSION=""
TARGET_VERSION=""
START_VERSION=""

collect_update_inputs() {
  show_section "${MSG_SECTION_INPUT:-Update parameters}"

  ask_input SOURCE_DIR "${MSG_SOURCE_DIR:-Local source directory}" "${INSTALL_SOURCE_DIR}"
  ask_input DEPLOY_DIR "${MSG_DEPLOY_DIR:-WebAdmin target directory}" "${DEFAULT_DEPLOY_DIR}"
  ask_input OPENVPN_SCRIPTS_DIR "${MSG_OPENVPN_SCRIPTS_DIR:-OpenVPN scripts directory}" "${DEFAULT_OPENVPN_SCRIPTS_DIR}"
  ask_yes_no UPDATE_SOURCE "${MSG_UPDATE_SOURCE:-Update source repository before deploy}" "yes"

  if [ "${UPDATE_SOURCE}" = "yes" ]; then
    ask_choice UPDATE_MODE "${MSG_UPDATE_MODE:-Choose the update source}" "latest" \
      "release" "${MSG_UPDATE_RELEASE:-Cached release only}" \
      "latest" "${MSG_UPDATE_LATEST:-Latest branch commits}"
    if [ "${UPDATE_MODE}" = "release" ]; then
      local cache_result=0
      RELEASE_VERSION="$(php "${SCRIPT_DIR}/helpers/release-version.php" "${DEPLOY_DIR}/config/config.php")" || cache_result=$?
      case "${cache_result}" in
        0) ;;
        2)
          warn "${MSG_RELEASE_CACHE_FALLBACK:-No valid cached release; switching to the latest branch.}"
          UPDATE_MODE="latest"
          ;;
        *) fatal "${MSG_RELEASE_CACHE_ERROR:-Could not read the release cache.}" ;;
      esac
    fi
    if [ "${UPDATE_MODE}" = "latest" ]; then
      ask_input SOURCE_BRANCH "${MSG_REPO_BRANCH:-Repository branch}" "${INSTALL_REPO_BRANCH}"
    fi
  fi

}

validate_update_inputs() {
  show_section "${MSG_SECTION_VALIDATE:-Validation}"

  [ -d "${SOURCE_DIR}" ] || fatal "${MSG_MISSING_DIR:-Missing required directory}: ${SOURCE_DIR}"
  [ -d "${DEPLOY_DIR}" ] || fatal "${MSG_MISSING_DIR:-Missing required directory}: ${DEPLOY_DIR}"
  ensure_absolute_path "${OPENVPN_SCRIPTS_DIR}" || fatal "${MSG_PATH_ABSOLUTE_REQUIRED:-Path must be absolute}: ${OPENVPN_SCRIPTS_DIR}"
  [ -d "${OPENVPN_SCRIPTS_DIR}" ] || fatal "${MSG_MISSING_DIR:-Missing required directory}: ${OPENVPN_SCRIPTS_DIR}"

  if ! git -C "${SOURCE_DIR}" rev-parse --git-dir >/dev/null 2>&1; then
    fatal "${MSG_SOURCE_NOT_GIT:-Source directory is not a git repository}: ${SOURCE_DIR}"
  fi
  [ ! -L "${DEPLOY_DIR}/storage" ] && [ ! -L "${DEPLOY_DIR}/storage/update" ] && [ ! -L "${DEPLOY_DIR}/storage/updates" ] ||
    fatal "Storage and update directories must not be symlinks."
  local old_status="${DEPLOY_DIR}/storage/update/setup-status.json"
  local status="${DEPLOY_DIR}/storage/updates/setup-status.json"
  if [ -e "${old_status}" ] || [ -L "${old_status}" ]; then
    [ ! -e "${status}" ] && [ ! -L "${status}" ] ||
      fatal "Both migration status files exist; resolve the conflict before updating."
    [ -f "${old_status}" ] && [ ! -L "${old_status}" ] ||
      fatal "The old migration status is not a regular file."
  elif [ -e "${status}" ] || [ -L "${status}" ]; then
    [ -f "${status}" ] && [ ! -L "${status}" ] ||
      fatal "Migration status is not a regular file."
  else
    ask_input START_VERSION "${MSG_START_VERSION:-Installed version before the update (X.Y.Z)}" ""
    [[ "${START_VERSION}" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] ||
      fatal "${MSG_INVALID_START_VERSION:-Invalid installed start version.}"
    local confirm_start_version
    ask_yes_no confirm_start_version "${MSG_CONFIRM_START_VERSION:-Confirm installed start version:} ${START_VERSION}" "no"
    [ "${confirm_start_version}" = "yes" ] || fatal "${MSG_ABORTED_BY_USER:-Aborted by user.}"
  fi

  ok "${MSG_INPUT_VALID:-All input values are valid.}"
}

confirm_update() {
  show_section "${MSG_SECTION_SUMMARY:-Summary}"
  echo " ${MSG_SOURCE_DIR:-Local source directory}: ${SOURCE_DIR}"
  echo " ${MSG_DEPLOY_DIR:-WebAdmin target directory}: ${DEPLOY_DIR}"
  echo " ${MSG_OPENVPN_SCRIPTS_DIR:-OpenVPN scripts directory}: ${OPENVPN_SCRIPTS_DIR}"
  echo " ${MSG_UPDATE_SOURCE:-Update source repository before deploy}: ${UPDATE_SOURCE}"
  if [ "${UPDATE_SOURCE}" = "yes" ]; then
    echo " ${MSG_UPDATE_MODE:-Update source}: ${UPDATE_MODE}"
    if [ "${UPDATE_MODE}" = "release" ]; then
      echo " ${MSG_UPDATE_RELEASE:-Release}: ${RELEASE_VERSION}"
    else
      echo " ${MSG_REPO_BRANCH:-Repository branch}: ${SOURCE_BRANCH}"
    fi
  fi
  [ -z "${START_VERSION}" ] || echo " ${MSG_START_VERSION:-Installed version before the update}: ${START_VERSION}"
  echo " ${MSG_UPDATE_FILES:-Application files and migration status will be updated. Config and other runtime data stay unchanged.}"
  echo " ${MSG_UPDATE_OPENVPN_SCRIPTS:-OpenVPN scripts will be updated; config.sh stays unchanged.}"

  ask_yes_no CONFIRM_UPDATE "${MSG_CONFIRM_UPDATE:-Start update with these settings}" "yes"
  [ "${CONFIRM_UPDATE}" = "yes" ] || fatal "${MSG_ABORTED_BY_USER:-Aborted by user.}"
}

update_source_repo() {
  [ "${UPDATE_SOURCE}" = "yes" ] || return 0

  show_section "${MSG_SECTION_REPO:-Source repository}"
  info "${MSG_REPO_UPDATE:-Updating existing source repository}"

  local worktree_status
  worktree_status="$(git -C "${SOURCE_DIR}" status --porcelain --untracked-files=normal)" ||
    fatal "Cannot inspect the source worktree."
  [ -z "${worktree_status}" ] || fatal "Source worktree has local changes; refusing to deploy an uncertain release."

  git -C "${SOURCE_DIR}" fetch --all --prune >>"${LOG_FILE}" 2>&1
  if [ "${UPDATE_MODE}" = "release" ]; then
    git -C "${SOURCE_DIR}" fetch origin --tags >>"${LOG_FILE}" 2>&1
    git -C "${SOURCE_DIR}" rev-parse --verify "refs/tags/${RELEASE_VERSION}^{commit}" >>"${LOG_FILE}" 2>&1 ||
      fatal "Cached release tag ${RELEASE_VERSION} does not exist in the source repository."
    git -C "${SOURCE_DIR}" checkout --detach "refs/tags/${RELEASE_VERSION}" >>"${LOG_FILE}" 2>&1
  else
    git -C "${SOURCE_DIR}" checkout "${SOURCE_BRANCH}" >>"${LOG_FILE}" 2>&1
    git -C "${SOURCE_DIR}" pull --ff-only origin "${SOURCE_BRANCH}" >>"${LOG_FILE}" 2>&1
  fi

  ok "${MSG_REPO_READY:-Source repository is ready.}"
}

prepare_update_storage() {
  local directory web_owner
  for directory in "${DEPLOY_DIR}/storage" "${DEPLOY_DIR}/storage/updates"; do
    [ ! -L "${directory}" ] || fatal "Storage directories must not be symlinks."
    if [ -e "${directory}" ]; then
      [ -d "${directory}" ] || fatal "Storage path is not a directory: ${directory}"
      continue
    fi
    mkdir -m 2770 -- "${directory}" || fatal "Cannot create storage directory: ${directory}"
    chown www-data:www-data "${directory}" || fatal "Cannot set storage directory owner: ${directory}"
    chmod 2770 "${directory}" || fatal "Cannot set storage directory permissions: ${directory}"
    web_owner="$(stat -c '%U' "${DEPLOY_DIR}")"
    setfacl -m "u:${web_owner}:rwx" \
      -m "d:u::rwx,d:u:${web_owner}:rwx,d:g::rwx,d:m::rwx,d:o::---" "${directory}" ||
      fatal "Cannot set storage directory ACL: ${directory}"
  done
}

migrate_status_location() {
  local old="${DEPLOY_DIR}/storage/update/setup-status.json"
  local directory="${DEPLOY_DIR}/storage/updates"
  local status="${directory}/setup-status.json"
  local old_lock new_lock

  [ -e "${old}" ] || return 0
  [ -f "${old}" ] && [ ! -L "${old}" ] && [ ! -L "${directory}" ] ||
    fatal "Invalid old migration status or new status directory."
  prepare_update_storage
  [ ! -L "${old}.lock" ] && [ ! -L "${status}.lock" ] ||
    fatal "Migration locks must not be symlinks."
  exec {old_lock}>>"${old}.lock"
  flock -n "${old_lock}" || fatal "Another migration is running."
  exec {new_lock}>>"${status}.lock"
  flock -n "${new_lock}" || fatal "Another migration is running."
  [ ! -e "${status}" ] && [ ! -L "${status}" ] ||
    fatal "Both migration status files exist; resolve the conflict before updating."
  mv -n -- "${old}" "${status}" || fatal "Cannot move the migration status."
  [ ! -e "${old}" ] && [ ! -L "${old}" ] && [ -f "${status}" ] && [ ! -L "${status}" ] ||
    fatal "Migration status could not be moved without overwriting."
  chmod 0644 "${status}" || fatal "Cannot set migration status permissions."
  flock -u "${new_lock}"
  exec {new_lock}>&-
  flock -u "${old_lock}"
  exec {old_lock}>&-
}

deploy_site_update() {
  show_section "${MSG_SECTION_DEPLOY:-Deploy files}"

  local deploy_uid deploy_gid
  local script
  local -a openvpn_scripts=(connect.sh disconnect.sh functions.sh login.sh login.v2.sh)
  for script in "${openvpn_scripts[@]}"; do
    [ -f "${SOURCE_DIR}/server/scripte/${script}" ] || fatal "${MSG_MISSING_FILE:-Missing required file}: ${SOURCE_DIR}/server/scripte/${script}"
  done
  [ -d "${OPENVPN_SCRIPTS_DIR}" ] || fatal "${MSG_MISSING_DIR:-Missing required directory}: ${OPENVPN_SCRIPTS_DIR}"
  deploy_uid="$(stat -c '%u' "${DEPLOY_DIR}")"
  deploy_gid="$(stat -c '%g' "${DEPLOY_DIR}")"

  if [ "${UPDATE_SOURCE}" = "yes" ] && [ "${UPDATE_MODE}" = "release" ]; then
    TARGET_VERSION="${RELEASE_VERSION}"
  else
    TARGET_VERSION="$(git_release_version "${SOURCE_DIR}")" || fatal "Cannot determine target version from an accessible Git release tag."
  fi
  [ -f "${SOURCE_DIR}/server/installation/helpers/MigrationRunner.php" ] ||
    fatal "Selected source does not contain the migration runner."
  [ -d "${SOURCE_DIR}/server/installation/migrations" ] ||
    fatal "Selected source does not contain the migrations directory."
  if [ -n "${START_VERSION}" ]; then
    [ ! -e "${DEPLOY_DIR}/storage/update/setup-status.json" ] && [ ! -L "${DEPLOY_DIR}/storage/update/setup-status.json" ] ||
      fatal "An old migration status appeared; restart the update."
    prepare_update_storage
    initialize_migration_status \
      "${SOURCE_DIR}/server/installation/migrate.php" \
      "${START_VERSION}" \
      "${DEPLOY_DIR}/storage/updates/setup-status.json"
  else
    migrate_status_location
  fi
  php "${SOURCE_DIR}/server/installation/migrate.php" --check "${TARGET_VERSION}" "${DEPLOY_DIR}/storage/updates/setup-status.json" ||
    fatal "Migration status is incompatible with the selected target."

  rsync -rt \
    --exclude='/config/config.php' \
    --exclude='/.env' \
    --exclude='/storage/' \
    --exclude='/public/css/self.css' \
    --exclude='*.ovpn' \
    --include='*/' \
    --include='*.php' \
    --include='*.sql' \
    --include='*.css' \
    --include='/update.sh' \
    --include='/server/installation/update.sh' \
    --include='/server/installation/functions.sh' \
    --include='*.js' \
    --include='*.md' \
    --exclude='*' \
    "${SOURCE_DIR}/" "${DEPLOY_DIR}/" >>"${LOG_FILE}" 2>&1

  php "${DEPLOY_DIR}/server/installation/migrate.php" --run "${TARGET_VERSION}" \
    "${DEPLOY_DIR}/storage/updates/setup-status.json" "${SOURCE_DIR}/server/installation/migrations" >>"${LOG_FILE}" 2>&1 ||
    fatal "Database migration failed; see ${LOG_FILE}. Do not repeat non-idempotent SQL without checking the database."

  find "${DEPLOY_DIR}" -type f -name '*.php' ! -path "${DEPLOY_DIR}/config/config.php" -exec chown "${deploy_uid}:${deploy_gid}" {} + >>"${LOG_FILE}" 2>&1

  rsync -rtpogc --chown=root:root --chmod=F750 \
    --exclude='/config.sh' \
    --include='/*.sh' \
    --exclude='*' \
    "${SOURCE_DIR}/server/scripte/" "${OPENVPN_SCRIPTS_DIR}/" >>"${LOG_FILE}" 2>&1

  ok "${MSG_UPDATE_DONE:-Application and help documentation update completed.}"
  ok "${MSG_UPDATE_OPENVPN_DONE:-OpenVPN scripts updated; config.sh preserved.}"
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
  ensure_cmd php
  choose_language

  collect_update_inputs
  validate_update_inputs
  confirm_update
  update_source_repo
  deploy_site_update

  show_section "${MSG_SECTION_DONE:-Finished}"
  ok "${MSG_UPDATE_FINISHED:-Update finished successfully.}"
  info "${MSG_LOG_HINT:-Log file}: ${LOG_FILE}"
}

if [[ "${BASH_SOURCE[0]}" == "$0" ]]; then
  main "$@"
fi
