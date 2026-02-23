#!/usr/bin/env bash
set -euo pipefail

SELF_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

if [ -x "${SELF_DIR}/server/installation/update.sh" ]; then
  exec "${SELF_DIR}/server/installation/update.sh" "$@"
fi

echo "update.sh requires a full repository checkout."
echo "Missing: ${SELF_DIR}/server/installation/update.sh"
exit 1
