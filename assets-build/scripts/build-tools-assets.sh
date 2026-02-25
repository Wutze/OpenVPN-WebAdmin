#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ASSETS_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
NODE_MODULES_DIR="${ASSETS_DIR}/node_modules"
STAGING_DIR="${ASSETS_DIR}/staging"
OUTPUT_DIR="${ASSETS_DIR}/release"
TOOLS_DIR="${STAGING_DIR}/tools"
ARCHIVE_NAME="tools-assets.tar.gz"
ARCHIVE_PATH="${OUTPUT_DIR}/${ARCHIVE_NAME}"
SHA_PATH="${OUTPUT_DIR}/${ARCHIVE_NAME}.sha256"
MANIFEST_PATH="${TOOLS_DIR}/manifest.json"

need_cmd() {
  command -v "$1" >/dev/null 2>&1 || {
    echo "Missing command: $1" >&2
    exit 1
  }
}

copy_file() {
  local src="$1"
  local dst="$2"
  [ -f "${src}" ] || {
    echo "Missing file: ${src}" >&2
    exit 1
  }
  install -D -m 0644 "${src}" "${dst}"
}

copy_tree() {
  local src="$1"
  local dst="$2"
  [ -d "${src}" ] || {
    echo "Missing directory: ${src}" >&2
    exit 1
  }
  mkdir -p "${dst}"
  cp -a "${src}/." "${dst}/"
}

main() {
  need_cmd npm
  need_cmd node
  need_cmd tar
  need_cmd sha256sum
  need_cmd install

  cd "${ASSETS_DIR}"
  if [ -f "${ASSETS_DIR}/package-lock.json" ]; then
    npm ci
  else
    echo "No package-lock.json found. Generating lock file first..."
    npm install --package-lock-only
    npm ci
  fi

  rm -rf "${STAGING_DIR}" "${OUTPUT_DIR}"
  mkdir -p "${TOOLS_DIR}" "${OUTPUT_DIR}"

  # Direct paths used by the app templates
  copy_tree "${NODE_MODULES_DIR}/admin-lte/dist" "${TOOLS_DIR}/AdminLTE-3.2.0/dist"
  copy_tree "${NODE_MODULES_DIR}/bootstrap/dist" "${TOOLS_DIR}/bootstrap5"
  copy_tree "${NODE_MODULES_DIR}/bootstrap-table/dist" "${TOOLS_DIR}/bootstrap-table/dist"
  copy_tree "${NODE_MODULES_DIR}/bootstrap-datepicker/dist" "${TOOLS_DIR}/int/bootstrap-datepicker/dist"
  copy_tree "${NODE_MODULES_DIR}/flatpickr/dist" "${TOOLS_DIR}/int/flatpickr/dist"
  copy_tree "${NODE_MODULES_DIR}/bootstrap-icons/font" "${TOOLS_DIR}/int/bootstrap-icons/font"
  copy_file "${NODE_MODULES_DIR}/jquery/dist/jquery.min.js" "${TOOLS_DIR}/jquery/jquery-min.js"

  cat > "${MANIFEST_PATH}" <<EOF
{
  "created_at_utc": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
  "source": "assets-build/package.json",
  "packages": {
    "admin-lte": "$(node -p "require('./node_modules/admin-lte/package.json').version")",
    "bootstrap": "$(node -p "require('./node_modules/bootstrap/package.json').version")",
    "bootstrap-table": "$(node -p "require('./node_modules/bootstrap-table/package.json').version")",
    "bootstrap-datepicker": "$(node -p "require('./node_modules/bootstrap-datepicker/package.json').version")",
    "flatpickr": "$(node -p "require('./node_modules/flatpickr/package.json').version")",
    "bootstrap-icons": "$(node -p "require('./node_modules/bootstrap-icons/package.json').version")",
    "jquery": "$(node -p "require('./node_modules/jquery/package.json').version")"
  }
}
EOF

  tar -C "${STAGING_DIR}" -czf "${ARCHIVE_PATH}" tools
  sha256sum "${ARCHIVE_PATH}" > "${SHA_PATH}"

  echo "Built archive: ${ARCHIVE_PATH}"
  echo "SHA256 file : ${SHA_PATH}"
  echo "Staging path: ${TOOLS_DIR}"
}

main "$@"
