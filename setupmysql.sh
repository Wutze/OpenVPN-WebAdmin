#!/bin/bash
#
# this File is part of OpenVPN-Admin
#
# GNU AFFERO GENERAL PUBLIC LICENSE V3
# Original Script from: https://github.com/Chocobozzz/OpenVPN-Admin
# Parts of the programming from pi-hole were used as templates.
#
# changes (c) by Wutze 2020 Version 0.6
#
# Twitter -> @HuWutze

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
