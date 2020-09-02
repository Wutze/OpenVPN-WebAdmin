#!/bin/bash
. /etc/openvpn/scripts/config.sh
. /etc/openvpn/scripts/functions.sh

username=$(echap "$username")
password=$(echap "$password")

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
  echo $(date '+%a %b %d %H:%M:%S %Y')" [ovpn] $username: authentication: [OK]"
  ## Set dynamic User based IP
  IP=$(mysql -h$DBHOST -P$DBPORT -u$DBUSER -p$DBPASS $DBNAME -sN -e "SELECT user_ip.user_ip, user_ip.server_ip FROM { oj user_ip AS user_ip RIGHT OUTER JOIN user AS user ON user_ip.uid = user.uid } WHERE user.user_name = '$username'")

  ## If OpenVPN didn't have such stupid programming, you wouldn't have to constantly rewrite files
  if [ -n "$IP" ]; then
    echo $(date '+%a %b %d %H:%M:%S %Y')" [ovpn] $username: set ip-adresses: $IP [OK]"
    echo "ifconfig-push " $IP > /etc/openvpn/ccd/$username
  fi
  exit 0
else
  echo $(date '+%a %b %d %H:%M:%S %Y')" [ovpn] $username: authentication: [ERROR] failed"
  exit 1
fi
