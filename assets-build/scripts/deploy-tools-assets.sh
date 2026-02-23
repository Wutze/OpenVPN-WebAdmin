#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ASSETS_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
DEFAULT_ARCHIVE="${ASSETS_DIR}/release/tools-assets.tar.gz"
DEFAULT_SHA="${ASSETS_DIR}/release/tools-assets.tar.gz.sha256"
TARGET_DIR="${1:-}"
ARCHIVE_PATH="${2:-${DEFAULT_ARCHIVE}}"
SHA_PATH="${3:-${DEFAULT_SHA}}"
TMP_DIR=""

usage() {
  cat <<EOF
Usage:
  $(basename "$0") <target_public_dir> [archive_path] [sha256_file]

Example:
  $(basename "$0") /srv/www/openvpn-webadmin/public
EOF
}

need_cmd() {
  command -v "$1" >/dev/null 2>&1 || {
    echo "Missing command: $1" >&2
    exit 1
  }
}

cleanup() {
  if [ -n "${TMP_DIR}" ] && [ -d "${TMP_DIR}" ]; then
    rm -rf "${TMP_DIR}"
  fi
}

main() {
  [ -n "${TARGET_DIR}" ] || {
    usage
    exit 1
  }

  need_cmd tar
  need_cmd install
  need_cmd rsync

  [ -f "${ARCHIVE_PATH}" ] || {
    echo "Archive not found: ${ARCHIVE_PATH}" >&2
    exit 1
  }

  if [ -n "${SHA_PATH}" ] && [ -f "${SHA_PATH}" ]; then
    need_cmd sha256sum
    local expected_hash actual_hash
    expected_hash="$(awk 'NR==1 {print $1}' "${SHA_PATH}")"
    [ -n "${expected_hash}" ] || {
      echo "Invalid SHA256 file: ${SHA_PATH}" >&2
      exit 1
    }
    actual_hash="$(sha256sum "${ARCHIVE_PATH}" | awk '{print $1}')"
    if [ "${expected_hash}" != "${actual_hash}" ]; then
      echo "SHA256 mismatch for ${ARCHIVE_PATH}" >&2
      echo "expected: ${expected_hash}" >&2
      echo "actual  : ${actual_hash}" >&2
      exit 1
    fi
    echo "SHA256 verified: ${ARCHIVE_PATH}"
  else
    echo "Warning: no SHA256 file found, skipping integrity check." >&2
  fi

  TMP_DIR="$(mktemp -d)"
  trap cleanup EXIT

  tar -C "${TMP_DIR}" -xzf "${ARCHIVE_PATH}"
  [ -d "${TMP_DIR}/tools" ] || {
    echo "Archive content invalid: tools/ directory missing." >&2
    exit 1
  }

  install -d -m 0755 "${TARGET_DIR}/tools"
  rsync -a --delete "${TMP_DIR}/tools/" "${TARGET_DIR}/tools/"

  echo "Deployed tools assets to: ${TARGET_DIR}/tools"
}

main "$@"
