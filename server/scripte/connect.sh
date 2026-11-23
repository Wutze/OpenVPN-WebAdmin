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

common_name=$(echap "$common_name")
trusted_ip=$(echap "$trusted_ip")
trusted_port=$(echap "$trusted_port")
ifconfig_pool_remote_ip=$(echap "$ifconfig_pool_remote_ip")
remote_port_1=$(echap "$remote_port_1")
bytes_received=$(echap "$bytes_received")
bytes_sent=$(echap "$bytes_sent")

session_id=$(dbus-uuidgen)

if [ -z "$bytes_received" ]
then
      bytes_received=0
fi

if [ -z "$bytes_sent" ]
then
      bytes_sent=0
fi


# We insert data in the log table
mysql -h$DBHOST -P$DBPORT -u$DBUSER -p$DBPASS $DBNAME -e "INSERT INTO log (log_id, user_name, log_trusted_ip, log_trusted_port, log_remote_ip, log_remote_port, log_start_time, log_end_time, log_received, log_send) VALUES(NULL, '$common_name','$trusted_ip', '$trusted_port','$ifconfig_pool_remote_ip', '$remote_port_1', now(),NULL, '$bytes_received', '$bytes_sent')"

# We specify that the user is online
mysql -h$DBHOST -P$DBPORT -u$DBUSER -p$DBPASS $DBNAME -e "UPDATE user SET user_online=1 WHERE user_name='$common_name'"

# Save the UUID so that the details of the operating system and client used can be read from the log file.
mysql -h$DBHOST -P$DBPORT -u$DBUSER -p$DBPASS $DBNAME -e "UPDATE user SET session_id='$session_id' WHERE user_name='$common_name'"
# write Session ID to logfile /var/log/openvpn/openvpn.log
echo $(date '+%a %b %d %H:%M:%S %Y')" [OVPN] $username: OVPN_SSID=$session_id"

