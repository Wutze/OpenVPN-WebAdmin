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

REPO_URL="${OVPN_SETUP_REPO_URL:-https://github.com/Wutze/OpenVPN-WebAdmin.git}"
REPO_BRANCH="${OVPN_SETUP_REPO_BRANCH:-main}"
WORK_DIR="${OVPN_SETUP_WORKDIR:-/tmp/openvpn-webadmin-setup-src}"

command -v git >/dev/null 2>&1 || {
  echo "git is required but not installed."
  exit 1
}

if [ -d "${WORK_DIR}/.git" ]; then
  git -C "${WORK_DIR}" fetch --all --prune
  git -C "${WORK_DIR}" checkout "${REPO_BRANCH}"
  git -C "${WORK_DIR}" pull --ff-only origin "${REPO_BRANCH}"
elif [ -d "${WORK_DIR}" ] && [ -n "$(ls -A "${WORK_DIR}" 2>/dev/null)" ]; then
  echo "Work directory exists and is not empty: ${WORK_DIR}"
  exit 1
else
  mkdir -p "$(dirname "${WORK_DIR}")"
  git clone --branch "${REPO_BRANCH}" "${REPO_URL}" "${WORK_DIR}"
fi

exec "${WORK_DIR}/server/installation/setup.sh" "$@"
