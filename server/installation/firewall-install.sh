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
# @version		2.0.1
# @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues

# shellcheck disable=SC1091
# shellcheck disable=SC2034

set -euo pipefail

git clone --branch ovpn2 --single-branch https://github.com/Wutze/bifroest2.git /opt/firewall

echo ""
echo "Firewall clone completed."
echo "now:"
echo "cd /opt/firewall"
echo "./setup-ovpn-fw.sh"
echo "./setup-system.sh"
echo ""
echo "Follow the above steps to complete the firewall setup."
echo ""