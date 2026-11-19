#!/usr/bin/env bash
set -Eeuo pipefail

root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
if [ "$(id -u)" -ne 0 ]; then
  echo "Run as root to test www-data permissions in an isolated temporary directory." >&2
  exit 1
fi
for command in rsync setfacl getfacl su; do
  command -v "$command" >/dev/null || { echo "Missing command: $command" >&2; exit 1; }
done

test_dir="$(mktemp -d /tmp/webadmin-storage-test.XXXXXXXX)"
trap 'find "$test_dir" -depth -delete' EXIT
chmod 0755 "$test_dir"
source_dir="${test_dir}/source"
deploy_dir="${test_dir}/web root"
mkdir -p "$source_dir/server/installation" "$source_dir/server/scripte" "$source_dir/config" \
  "$deploy_dir/storage/conf/linux/history"
touch "$source_dir/server/installation/database.sql" "$source_dir/config/config.conf"
for system in linux osx windows; do
  mkdir -p "$source_dir/storage/conf/$system"
  printf 'template %s\n' "$system" > "$source_dir/storage/conf/$system/client.ovpn"
  printf 'script %s\n' "$system" > "$source_dir/storage/conf/$system/update-resolv.sh"
done
printf 'existing configuration\n' > "$deploy_dir/storage/conf/linux/client.ovpn"
printf 'existing history\n' > "$deploy_dir/storage/conf/linux/history/old.ovpn"

(
  source "$root/server/installation/setup.sh"
  SOURCE_DIR="$source_dir"
  DEPLOY_DIR="$deploy_dir"
  WEB_OWNER=www-data
  WEB_GROUP=www-data
  WEBSERVER_CONFIGURE=no
  LOG_FILE="$test_dir/setup.log"
  deploy_application_files
  finalize_permissions
  check_runtime_permissions
  test "$(cat "$deploy_dir/storage/conf/linux/client.ovpn")" = 'existing configuration'
  test "$(cat "$deploy_dir/storage/conf/linux/history/old.ovpn")" = 'existing history'
  deploy_application_files
  test "$(cat "$deploy_dir/storage/conf/linux/client.ovpn")" = 'existing configuration'
  for system in linux osx windows; do
    test -f "$deploy_dir/storage/conf/$system/client.ovpn"
    test -f "$deploy_dir/storage/conf/$system/update-resolv.sh"
    test -d "$deploy_dir/storage/conf/$system/history"
    test "$(stat -c '%U:%G %a' "$deploy_dir/storage/conf/$system/history")" = 'www-data:www-data 2770'
  done
  umask 077
  touch "$deploy_dir/storage/conf/linux/root-created"
  mkdir "$deploy_dir/storage/conf/linux/root-created-dir"
  su -s /bin/sh -c 'test -r "$1" && test -w "$1" && test -w "$2"' \
    www-data -- sh "$deploy_dir/storage/conf/linux/root-created" "$deploy_dir/storage/conf/linux/root-created-dir"
)

(
  source "$root/server/installation/openvpn-setup-easyrsa.sh"
  LOG_FILE="$test_dir/easyrsa.log"
  OPENVPN_DIR="$test_dir/openvpn"
  WEBADMIN_DEPLOY_DIR="$deploy_dir"
  mkdir -p "$OPENVPN_DIR" "$deploy_dir/storage/conf/server"
  printf 'vpn config\n' > "$OPENVPN_DIR/server.conf"
  printf 'existing web config\n' > "$deploy_dir/storage/conf/server/server.conf"
  link_server_conf
  test "$(readlink "$deploy_dir/storage/conf/server/server.conf")" = "$OPENVPN_DIR/server.conf"
  test "$(stat -c '%U:%G %a' "$OPENVPN_DIR/server.conf")" = 'root:www-data 640'
  backups=("$deploy_dir"/storage/conf/server/server.conf.bak.*)
  test "${#backups[@]}" -eq 1
  test "$(cat "${backups[0]}")" = 'existing web config'
  link_server_conf
  backups=("$deploy_dir"/storage/conf/server/server.conf.bak.*)
  test "${#backups[@]}" -eq 1
)

echo "Storage setup and server configuration link: OK"
