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
  exit 0
else
  echo $(date '+%a %b %d %H:%M:%S %Y')" [ovpn] $username: authentication: [ERROR] failed"
  exit 1
fi
