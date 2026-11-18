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

. /etc/openvpn/scripts/config.sh
. /etc/openvpn/scripts/functions.sh

username=$(echap "$username")
password=$(echap "$password")
#session_id=$(echap "$sid")
session_id=$(dbus-uuidgen)

#echo "push \"ifconfig 10.8.0.40 255.255.255.0\"" > $1


echo $(date '+%a %b %d %H:%M:%S %Y')" [ovpn] Start Login Process for: $username: [INFO]"

# Authentication
user_pass=$(mysql -h$DBHOST -P$DBPORT -u$DBUSER -p$DBPASS $DBNAME -sN -e "SELECT user_pass FROM user WHERE user_name = '$username' AND user_enable=1 AND (TO_DAYS(now()) >= TO_DAYS(user_start_date) OR user_start_date IS NULL) AND (TO_DAYS(now()) <= TO_DAYS(user_end_date) OR user_end_date IS NULL)")

# Check the user
# write log entry
if [ "$user_pass" == '' ]; then
    echo $(date '+%a %b %d %H:%M:%S %Y')" [ovpn] $username: authentication: [ERROR] bad account"
  exit 1
fi

result=$(php -r "if(password_verify('$password', '$user_pass') == true) { echo 'ok'; } else { echo 'ko'; }")

if [ "$result" == "ok" ]; then

## Set User based IP
#IP=$(mysql -h$DBHOST -P$DBPORT -u$DBUSER -p$DBPASS $DBNAME -sN -e "SELECT user_ip.user_ip FROM { oj user_ip AS user_ip RIGHT OUTER JOIN user AS user ON user_ip.uid = user.uid } WHERE user.user_name = '$username'")
#NETMASK=$(mysql -h$DBHOST -P$DBPORT -u$DBUSER -p$DBPASS $DBNAME -sN -e "SELECT user_ip.netmask FROM { oj user_ip AS user_ip RIGHT OUTER JOIN user AS user ON user_ip.uid = user.uid } WHERE user.user_name = '$username'")

IP=$(mysql -h$DBHOST -P$DBPORT -u$DBUSER -p$DBPASS $DBNAME -sN -e "SELECT user_ip.user_ip, user_ip.server_ip FROM { oj user_ip AS user_ip RIGHT OUTER JOIN user AS user ON user_ip.uid = user.uid } WHERE user.user_name = '$username'")

echo $(date '+%a %b %d %H:%M:%S %Y')" [ovpn] $username: set ip-adresses: $IP [OK]"
echo "ifconfig-push " $IP > /etc/openvpn/ccd/$username

#echo "ifconfig-push $IP $NETMASK" > /etc/openvpn/ccd/$username



  echo $(date '+%a %b %d %H:%M:%S %Y')" [ovpn] $username: authentication: [OK]"
#  echo $(date '+%a %b %d %H:%M:%S %Y')" [ovpn] $username: ID=$session_id"
  exit 0
else
  echo $(date '+%a %b %d %H:%M:%S %Y')" [ovpn] $username: authentication: [ERROR] failed"
  exit 1
fi

