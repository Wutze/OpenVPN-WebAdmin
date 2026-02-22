#!/usr/bin/env bash

# Git source for the application code.
# Replace these values to switch to a different internal repository.
INSTALL_REPO_URL="${INSTALL_REPO_URL:-http://gitlab1.home/micro/openvpn-webadmin-intern.git}"
INSTALL_REPO_BRANCH="${INSTALL_REPO_BRANCH:-main}"
INSTALL_SOURCE_DIR="${INSTALL_SOURCE_DIR:-/opt/openvpn-webadmin-src}"

# Deployment target (web application path)
DEFAULT_DEPLOY_DIR="${DEFAULT_DEPLOY_DIR:-/srv/www/openvpn-webadmin}"
DEFAULT_WEB_OWNER="${DEFAULT_WEB_OWNER:-www-data}"
DEFAULT_WEB_GROUP="${DEFAULT_WEB_GROUP:-www-data}"
DEFAULT_LOGIN_THEME="${DEFAULT_LOGIN_THEME:-login1}"

# Must stay local/private. External URLs are blocked in setup.
DEFAULT_SITETOOLS="${DEFAULT_SITETOOLS:-/tools}"

# Database defaults
DEFAULT_DB_HOST="${DEFAULT_DB_HOST:-localhost}"
DEFAULT_DB_PORT="${DEFAULT_DB_PORT:-3306}"
DEFAULT_DB_NAME="${DEFAULT_DB_NAME:-ovpn_admin}"
DEFAULT_DB_USER="${DEFAULT_DB_USER:-ovpn_admin}"

# OpenVPN integration defaults
DEFAULT_OPENVPN_SERVER_CONF="${DEFAULT_OPENVPN_SERVER_CONF:-/etc/openvpn/server.conf}"
ALT_OPENVPN_SERVER_CONF="${ALT_OPENVPN_SERVER_CONF:-/etc/openvpn/server.conf}"
DEFAULT_OPENVPN_SCRIPTS_DIR="${DEFAULT_OPENVPN_SCRIPTS_DIR:-/etc/openvpn/scripts}"

# Supported platforms only
SUPPORTED_DEBIAN_VERSIONS=("12" "13")
SUPPORTED_UBUNTU_LTS_VERSIONS=("22.04" "24.04")

LOG_FILE_DEFAULT="${LOG_FILE_DEFAULT:-/var/log/openvpn-webadmin-setup.log}"
