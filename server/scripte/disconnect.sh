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
bytes_received=$(echap "$bytes_received")
bytes_sent=$(echap "$bytes_sent")
trusted_ip=$(echap "$trusted_ip")
trusted_port=$(echap "$trusted_port")

# We specify the user is offline
mysql -h$DBHOST -P$DBPORT -u$DBUSER -p$DBPASS $DBNAME -e "UPDATE user SET user_online=0 WHERE user_name='$common_name'"

# We insert the deconnection datetime
mysql -h$DBHOST -P$DBPORT -u$DBUSER -p$DBPASS $DBNAME -e "UPDATE log SET log_end_time=now(), log_received='$bytes_received', log_send='$bytes_sent' WHERE log_trusted_ip='$trusted_ip' AND log_trusted_port='$trusted_port' AND user_name='$common_name' AND log_end_time IS NULL"

echo $(date '+%a %b %d %H:%M:%S %Y')" [ovpn] disconnect $common_name: [QUIT]"