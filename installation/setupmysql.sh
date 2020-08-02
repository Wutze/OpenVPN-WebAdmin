#!/bin/bash
# this File is part of OpenVPN-WebAdmin - (c) 2020 OpenVPN-WebAdmin
#
# NOTICE OF LICENSE
#
# GNU AFFERO GENERAL PUBLIC LICENSE V3
# that is bundled with this package in the file LICENSE.md.
# It is also available through the world-wide-web at this URL:
# https://www.gnu.org/licenses/agpl-3.0.en.html
#
# @fork Original Idea and parts in this script from: https://github.com/Chocobozzz/OpenVPN-Admin
# 
# @author    Wutze
# @copyright 2020 OpenVPN-WebAdmin
# @link			https://github.com/Wutze/OpenVPN-WebAdmin
# @see				Internal Documentation ~/doc/
# @version		1.0.0
# @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues

#  
#  name: set_mysql_rootpw
#  @param
#  @return
#  
set_mysql_rootpw(){
	dbpw=$(whiptail --inputbox "Insert your new database password" 8 78 --title "Create DB-PW" 3>&1 1>&2 2>&3)
	echo "grant all on *.* to root@localhost identified by '$dbpw' with grant option;" | mysql -u root --password="$dbpw"
	echo "flush privileges;" | mysql -u root --password="$dbpw"
}

set_mysql_rootpw
