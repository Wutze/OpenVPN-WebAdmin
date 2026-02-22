#!/usr/bin/env bash
set -euo pipefail

SELF_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# If the full repository is already present, run installer directly.
if [ -x "${SELF_DIR}/server/installation/setup.sh" ]; then
  exec "${SELF_DIR}/server/installation/setup.sh" "$@"
fi

# Standalone bootstrap mode (script downloaded alone).
REPO_URL="${OVPN_SETUP_REPO_URL:-http://gitlab1.home/micro/openvpn-webadmin-intern.git}"
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
