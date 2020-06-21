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

# debug
#set -x

## short Description install file
## script explained step by step
# in the first define variables, load config files
# setting up the functions
# then start the script
# read the arguments, check this, set the install path
# check installation path and whether group and user exist
# Inputs all vars with Message Boxes
# setup mysql > databases, tables, access and first admin to login webfrontend
# read vars and setting up to create ca-certs
# create system services
# create and copy bash scripts for server
# create and copy files for webfrontend
# in the last install third party components, setting access rights
# finnish the script with message

## Fix Debian 10 Fehler
export PATH=$PATH:/usr/sbin:/sbin

## set static vars
config="config.conf"
coltable=/opt/install/COL_TABLE

## init screen
# Find the rows and columns will default to 80x24 if it can not be detected
screen_size=$(stty size 2>/dev/null || echo 24 80)
rows=$(echo "${screen_size}" | awk '{print $1}')
columns=$(echo "${screen_size}" | awk '{print $2}')

# Divide by two so the dialogs take up half of the screen, which looks nice.
r=$(( rows / 2 ))
c=$(( columns / 2 ))
# Unless the screen is tiny
r=$(( r < 20 ? 20 : r ))
c=$(( c < 70 ? 70 : c ))
h=$(( r - 7 ))

# The script is part of a larger script collection, so this entry exists.
# If the color table file exists
if [[ -f "${coltable}" ]]; then
  # source it
  source ${coltable}
# Otherwise,
else
  # Set these values so the installer can still run in color
  COL_NC='\e[0m' # No Color
  COL_LIGHT_GREEN='\e[1;32m'
  COL_LIGHT_RED='\e[1;31m'
  COL_BLUE='\e[94m'
  TICK="[${COL_LIGHT_GREEN}✓${COL_NC}]"
  CROSS="[${COL_LIGHT_RED}✗${COL_NC}]"
  INFO="[i]"
  # shellcheck disable=SC2034
  DONE="${COL_LIGHT_GREEN} done!${COL_NC}"
	OVER="\\r\\033[K"
fi

#
#  description: intercepts errors and displays them as messages
#  name: control_box
#  @param $? + Description
#  @return Message OK or Exit Script
#  
control_box(){
  exitstatus=$?
  if [ $exitstatus = 0 ]; then
      print_out 1 "Input Ok: ${2}"
  else
      print_out 0 "input break: ${2}"
      exit
  fi
}
#
#  description: Intercept and display errors
#  name: control_script
#  @param $?
#  @return continue script or or exit when error with exit 100
#  
control_script(){
  if [ ! $? -eq 0 ]
  then
  print_out 0 "Error ${1}"
  exit 100
  fi
}
#
#  description: formats the notes and messages in an appealing form
#  name: print_out
#  @param [1|0|i|d|r] [Text]]
#  @return formated Text with red cross, green tick, "i"nfo, "d"one Message or need input with "r"
#  
print_out(){
  case "${1}" in
    1)
    echo -e " ${TICK} ${2}"
    ;;
    0)
    echo -e " ${CROSS} ${2}"
    ;;
    i)
    echo -e " ${INFO} ${2}"
    ;;
    d)
    echo -e " ${DONE} ${2}"
    ;;
    r)	read -rsp " ${2}"
	    echo "\n"
    ;;
  esac
}
# Split System-Variable $LANG
var1=${LANG%.*}
## Select Language to install
var2=$(whiptail --title "Select Language" --menu "Select your language" ${r} ${c} ${h} \
  "AUTO" " Automatic" \
  "de_DE" " Deutsch" \
  "en_EN" " Englisch" \
  3>&1 1>&2 2>&3)
RET=$?
if [ $RET -eq 1 ]; then
  print_out 0 "Exit select language"
  exit
elif [ $RET -eq 0 ]; then
  case "$var2" in
    AUTO) source lang/"$var1"
    ;;
    de_DE) source "lang/$var2"
    ;;
    en_EN) source "lang/$var2"
    ;;
    *) source "lang/de_DE"
    ;;
  esac
fi

## Intro with colored Logo
intro(){
  NOW=$(date +"%Y")
	echo -e "${COL_LIGHT_RED}
■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
${COL_BLUE}        ◢■◤
      ◢■◤
    ◢■◤  ${COL_LIGHT_RED} O P E N V P N - ${COL_NC}W E B A D M I N${COL_LIGHT_RED} - S E R V E R${COL_BLUE}
  ◢■◤                         【ツ】 © 10.000BC - ${NOW}
◢■■■■■■■■■■■■■■■■■■■■◤             ${COL_LIGHT_RED}L   I   N   U   X
■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■${COL_NC}
"
}

# read config.conf
# you must copy config.conf.example to config.conf and edit this file
check_config(){
  if [[ -f "${config}" ]]; then
    # source it
    source ${config}
  # Otherwise,
  else
    echo -e ${COL_LIGHT_RED}$CONFIG01${COL_NC}
    echo -e $CONFIG02
    echo -e ${COL_LIGHT_GREEN}$CONFIG03${COL_NC}
    echo -e $CONFIG04
    echo -e ${CROSS}" Script "$BREAK
    exit
  fi
}

#
#  description: you can only install with root privileges, check this
#  name: check_user
#  @param $?
#  @return continue script or or exit when no root user
#  
check_user(){
  # Must be root to install
  local str="Root user check"
  if [[ "${EUID}" -eq 0 ]]; then
    # they are root and all is good
    print_out 1 "${str}"
  else
    print_out 0 "${str}"
    print_out i "${COL_LIGHT_RED}${USER01}${COL_NC}"
    print_out i "${USER02}"
    print_out 0 "${USER03}"
    exit 1
  fi
}

do_select(){

#value=("0" "${SELECT00}" on "2" "${SELECT01}" on "3" "${SELECT02}" off)
#whiptail --title "xx" --checklist "choose" 16 78 10 "${value[@]}"
# nginx fehlt noch
	sel=$(whiptail --title "${SELECT0}" --checklist --separate-output "${SELECT1}:" ${r} ${c} ${h} \
    "1" "${SELECT01} " on \
    "2" "${SELECT02} " on \
    "3" "${SELECT03} " on \
    "4" "${SELECT04} " off \
    "5" "${SELECT05} " on \
    "7" "${SELECT07} " on \
    "9" "${SELECT09} " on \
    3>&1 1>&2 2>&3)
#  RET=$?
  control_box $? "select"
}

set_autoinstall(){
  autoinstall1="openvpn php-mysql php-zip php unzip git wget sed curl git net-tools npm nodejs"
  autoinstall2="npm install -g yarn"
}

# Future for the progressbar
go_progress(){
  apt-get $1
}

make_mysql(){
  if [ $mysqlserver ]; then
    print_out 0 "$FEHLER01 $SELECT02 $FEHLER03 $SELECT03. $ONEONLY"
    print_out 0 $BREAK
    exit
  fi
  if [ ${1} = 3 ]; then
    mysqlserver="mariadb-server"
    sqltest="mysql"
    installsql="1"
    echo -e " ${TICK} ${1}" $mysqlserver
  elif [ ${1} = 4 ]; then
    mysqlserver="default-mysql-client"
    sqltest="mysql"
    installsql=""
    echo -e " ${TICK} ${1}" $mysqlserver
  fi
  #apt-get install mariadb-server -y
  #echo -e " ${TICK} ${1}"
}

make_webserver(){
  if [ $webserver ]; then
    print_out 0 "$FEHLER01 $SELECT04 $FEHLER03 $SELECT05. $ONEONLY"
    print_out 0 $BREAK
    exit
  fi
  if [ ${1} = 5 ]; then
    webserver="apache2"
    echo -e " ${TICK} ${1}" $webserver
  elif [ ${1} = 6 ]; then
    webserver="nginx"
    echo -e " ${TICK} ${1}" $webserver
  fi
    
}

make_webroot(){
  www="/srv/www/"
  echo -e " ${TICK} ${1}" "Set www-root "$www
}

make_owner(){
  user="www-data"
  group="www-data"
  echo -e " ${TICK} ${1}" "Set permissions: "$user $group
}

set_config(){
  cp config.conf.sample config.conf
}
#### Start Script with Out- and Inputs
## first call funcions
echo "${ATTENTION}" > info.text
whiptail --textbox info.text --title "Information" ${r} ${c}
clear
intro
print_out i "${BEFOR}"
print_out r 
check_user
do_select

while read -r line;
do #echo "${line}";
    case $line in
        1) set_config
        ;;
        2) set_autoinstall
        ;;
        3|4) make_mysql $line
        ;;
        5|6) make_webserver $line
        ;;
        7|8) make_webroot $line
        ;;
        9|10) make_owner $line
        ;;
        *)
        ;;
    esac
done < <(echo "$sel")

check_config

#echo "$webserver $autoinstall1 $mysqlserver -y"
#echo "$autoinstall2"
go_progress "update"
go_progress "install $webserver $autoinstall1 $mysqlserver -y"
$autoinstall2

# after install mysql-server create mysql-root-pw
set_mysql_rootpw(){
	dbpw=$(whiptail --inputbox "${MYSQL01}" ${r} ${c} --title "${MYSQL02}" 3>&1 1>&2 2>&3)
  control_box $? "set mysql root pw"
	echo "grant all on *.* to root@localhost identified by '$dbpw' with grant option;" | mysql -u root --password="$dbpw"
	echo "flush privileges;" | mysql -u root --password="$dbpw"
}

if [ $installsql ]; then
  set_mysql_rootpw
fi

# Ensure there are the prerequisites
for i in openvpn mysql php yarn node unzip wget sed route; do
  which $i > /dev/null
  if [ "$?" -ne 0 ]; then
    if [ "$i" = "mysql" ]; then
      print_out 0 "$INSTMESS"
      print_out 0 "$BREAK"
      exit
    fi
    print_out 0 "$MISSING ${COL_LIGHT_RED}$i${COL_NC}! $INSTALL"
    print_out 0 "$BREAK"
    exit
  fi
done

openvpn_admin="$www/openvpn-admin"

base_path=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

## Message Boxen/Input
## Setup VPN
ip_server=$(whiptail --inputbox "${SETVPN01}" ${r} ${c} --title "Hostname/IP" 3>&1 1>&2 2>&3)
control_box $? "Server IP"
openvpn_proto=$(whiptail --inputbox "${SETVPN02}" ${r} ${c} udp --title "Protokoll" 3>&1 1>&2 2>&3)
control_box $? "VPN Protokoll"
server_port=$(whiptail --inputbox "${SETVPN03}" ${r} ${c} 1194 --title "Server Port" 3>&1 1>&2 2>&3)
control_box $? "OpenVPN Port"

## Setup Database-Server
db_host=$(whiptail --inputbox "${SETVPN04}" ${r} ${c} localhost --title "DB Host" 3>&1 1>&2 2>&3)
control_box $? "DB-Host"
db_name=$(whiptail --inputbox "${SETVPN10}" ${r} ${c} openvpnadmin --title "DB Name" 3>&1 1>&2 2>&3)
control_box $? "DB-Name"

## If you are using an external database server
## configure it previously so that you can enter a user name and password.
if [ "$db_host" == localhost ]; then
  mysql_root_pass=$(whiptail --inputbox "${SETVPN05}" ${r} ${c} --title "DB Root PW" 3>&1 1>&2 2>&3)
  control_box $? "Root PW"
fi
mysql_user=$(whiptail --inputbox "${SETVPN06}" ${r} ${c} --title "User DB Name" 3>&1 1>&2 2>&3)
control_box $? "MySQL Username"
mysql_user_pass=$(whiptail --inputbox "${SETVPN07}" ${r} ${c} --title "User DB PW" 3>&1 1>&2 2>&3)
control_box $? "MySQL User PW"

## Setup Webfrontend
admin_user=$(whiptail --inputbox "${SETVPN08}" ${r} ${c} --title "Web-Admin Name" 3>&1 1>&2 2>&3)
control_box $? "Web Admin User"
admin_user_pass=$(whiptail --inputbox "${SETVPN09}" ${r} ${c} --title "Web-Admin PW" 3>&1 1>&2 2>&3)
control_box $? "Web Admin PW"

#  
#  name: set_mysql
#  @param dbname dbuser dbpass
#  @return insert new database, user and setup password
#  
set_mysql(){
  EXPECTED_ARGS=3
  MYSQL=`which mysql`
  Q1="CREATE DATABASE IF NOT EXISTS $1;"
  Q2="GRANT ALL ON $1.* TO '$2'@'localhost' IDENTIFIED BY '$3';"
  Q3="FLUSH PRIVILEGES;"
  SQL="${Q1}${Q2}${Q3}"
   
  if [ $# -ne $EXPECTED_ARGS ]
  then
    echo "Usage: $0 dbname dbuser dbpass"
    exit
  fi
   
  $MYSQL -h $db_host -uroot --password=$mysql_root_pass -e "$SQL"
  control_script "Create local Database"
}

if [ "$db_host" == localhost ]; then
  set_mysql $db_name $mysql_user $mysql_user_pass
fi

# current only new install
mysql -h $db_host -u $mysql_user --password=$mysql_user_pass $db_name < sql/vpnadmin.dump
control_script "Insert Database Dump"
#mysql -h $db_host -u $mysql_user --password=$mysql_user_pass $db_name < sql/vpnadmin.sql
#control_script "Insert Userupdate #Fix 102"
#mysql -h $db_host -u $mysql_user --password=$mysql_user_pass $db_name < sql/adodb.sql
#control_script "Insert AdoDB Session Table"
mysql -h $db_host -u $mysql_user --password=$mysql_user_pass --database=$db_name -e "INSERT INTO user (user_id, user_pass, gid, user_enable) VALUES ('${admin_user}', encrypt('${admin_user_pass}'),'1','1');"
control_script "Insert Webadmin User"
mysql -h $db_host -u $mysql_user --password=$mysql_user_pass --database=$db_name -e "INSERT INTO user (user_id, user_pass, gid, user_enable) VALUES ('${admin_user}-user', encrypt('${admin_user_pass}'),'2','1');"
control_script "Insert first User"

print_out 1 "setting up MySQL OK"

print_out i "Creating the certificates"

# Get the rsa keys
# mal austauschen gegen "neu"
# https://github.com/OpenVPN/easy-rsa/archive/master.zip
cd /opt/
wget "https://github.com/OpenVPN/easy-rsa/releases/download/v3.0.6/EasyRSA-unix-v3.0.6.tgz"
tar -xaf "EasyRSA-unix-v3.0.6.tgz"
mv "EasyRSA-v3.0.6" /etc/openvpn/easy-rsa
rm "EasyRSA-unix-v3.0.6.tgz"

cd /etc/openvpn/easy-rsa
## This vars read from config.conf, see above in this script
if [[ ! -z $key_size ]]; then
  export EASYRSA_KEY_SIZE=$key_size
fi
if [[ ! -z $ca_expire ]]; then
  export EASYRSA_CA_EXPIRE=$ca_expire
fi
if [[ ! -z $cert_expire ]]; then
  export EASYRSA_CERT_EXPIRE=$cert_expire
fi
if [[ ! -z $cert_country ]]; then
  export EASYRSA_REQ_COUNTRY=$cert_country
fi
if [[ ! -z $cert_province ]]; then
  export EASYRSA_REQ_PROVINCE=$cert_province
fi
if [[ ! -z $cert_city ]]; then
  export EASYRSA_REQ_CITY=$cert_city
fi
if [[ ! -z $cert_org ]]; then
  export EASYRSA_REQ_ORG=$cert_org
fi
if [[ ! -z $cert_ou ]]; then
  export EASYRSA_REQ_OU=$cert_ou
fi
if [[ ! -z $cert_email ]]; then
  export EASYRSA_REQ_EMAIL=$cert_email
fi
if [[ ! -z $key_cn ]]; then
  export EASYRSA_REQ_CN=$key_cn
fi


print_out i "Setup OpenVPN"
print_out i "Init PKI dirs and build CA certs"
./easyrsa init-pki
./easyrsa build-ca nopass
print_out i "Generate Diffie-Hellman parameters"
./easyrsa gen-dh
print_out i "Genrate server keypair"
./easyrsa build-server-full server nopass
print_out i "Generate shared-secret for TLS Authentication"
openvpn --genkey --secret pki/ta.key
print_out 1 "setting up EasyRSA Ok"

# Copy certificates and the server configuration in the openvpn directory
cp /etc/openvpn/easy-rsa/pki/{ca.crt,ta.key,issued/server.crt,private/server.key,dh.pem} "/etc/openvpn/"
cp "$base_path/installation/server.conf" "/etc/openvpn/"
mkdir "/etc/openvpn/ccd"
sed -i "s/port 443/port $server_port/" "/etc/openvpn/server.conf"

if [ $openvpn_proto = "udp" ]; then
  sed -i "s/proto tcp/proto $openvpn_proto/" "/etc/openvpn/server.conf"
fi

nobody_group=$(id -ng nobody)
sed -i "s/group nogroup/group $nobody_group/" "/etc/openvpn/server.conf"

print_out i "Setup firewall"

## create systemd Service
echo "

[Unit]
Description=Firewall Rules
After=network.target

[Service]
Type=simple
ExecStart=/bin/bash /usr/sbin/firewall.sh
TimeoutStartSec=0

[Install]
WantedBy=default.target

" > /etc/systemd/system/firewall.service

## create simple firewall-script
echo "#/bin/sh
export PATH=$PATH:/usr/sbin:/sbin

echo 1 > "/proc/sys/net/ipv4/ip_forward"

# Get primary NIC device name
primary_nic=`route | grep '^default' | grep -o '[^ ]*$'`

# Iptable rules
iptables -I FORWARD -i tun0 -j ACCEPT
iptables -I FORWARD -o tun0 -j ACCEPT
iptables -I OUTPUT -o tun0 -j ACCEPT

iptables -A FORWARD -i tun0 -o \$primary_nic -j ACCEPT
iptables -t nat -A POSTROUTING -o \$primary_nic -j MASQUERADE
iptables -t nat -A POSTROUTING -s 10.8.0.0/24 -o \$primary_nic -j MASQUERADE
iptables -t nat -A POSTROUTING -s 10.8.0.2/24 -o \$primary_nic -j MASQUERADE

# fixes problems with the persistent transmissions e.g. netflix
iptables -t mangle -o \$primary_nic --insert FORWARD 1 -p tcp --tcp-flags SYN,RST SYN -m tcpmss --mss 1400:65495 -j TCPMSS --clamp-mss-to-pmtu
" > /usr/sbin/firewall.sh

chmod +x /usr/sbin/firewall.sh
systemctl enable firewall.service
systemctl start firewall

print_out i "Setup web application"

## Copy bash scripts (which will insert row in MySQL)
cp -r "$base_path/installation/scripts" "/etc/openvpn/"
chmod +x "/etc/openvpn/scripts/"*

# Configure MySQL in openvpn scripts
sed -i "s/HOST=''/HOST='$db_host'/" "/etc/openvpn/scripts/config.sample.sh"
sed -i "s/USER=''/USER='$mysql_user'/" "/etc/openvpn/scripts/config.sample.sh"
escaped=$(echo -n "$mysql_user_pass" | sed 's#\\#\\\\#g;s#&#\\&#g')
sed -i "s/PASS=''/PASS='${escaped}'/" "/etc/openvpn/scripts/config.sample.sh"
sed -i "s/DB=''/DB='$db_name'/" "/etc/openvpn/scripts/config.sample.sh"
cp /etc/openvpn/scripts/config.sample.sh /etc/openvpn/scripts/config.sh

# Create the directory of the web application
mkdir $www
mkdir "$openvpn_admin"
cp -r "$base_path/"{index.php,package.json,js,include,css,images,data} "$openvpn_admin"
mkdir $www/vpn
#mkdir $www/vpn/conf
cp -r "$base_path/"installation/conf $www/vpn/
ln -s /etc/openvpn/server.conf $www/vpn/conf/server/server.conf

# New workspace
cd "$openvpn_admin"

# Replace config.sample.php variables
cp ./include/config.sample.php ./include/config.php
sed -i "s/\$host = '';/\$host = '$db_host';/" "./include/config.php"
sed -i "s/\$user = '';/\$user = '$mysql_user';/" "./include/config.php"
sed -i "s/\$db   = '';/\$db   = '$db_name';/" "./include/config.php"
sed -i "s/\$pass = '';/\$pass = '${escaped}';/" "./include/config.php"


# Replace in the client configurations with the ip of the server and openvpn protocol
for file in $(find ../ -name client.ovpn); do
  sed -i "s/remote xxx\.xxx\.xxx\.xxx 443/remote $ip_server $server_port/" $file
  echo "<ca>" >> $file
  cat "/etc/openvpn/ca.crt" >> $file
  echo "</ca>" >> $file
  echo "<tls-auth>" >> $file
  cat "/etc/openvpn/ta.key" >> $file
  echo "</tls-auth>" >> $file
  if [ $openvpn_proto = "udp" ]; then
    sed -i "s/proto tcp-client/proto udp/" $file
  fi
done

# Copy ta.key inside the client-conf directory
for directory in "../vpn/conf/gnu-linux/" "../vpn/conf/osx-viscosity/" "../vpn/conf/windows/"; do
  cp "/etc/openvpn/"{ca.crt,ta.key} $directory
done

print_out 1 "Setup Web Application done"

print_out i "Install third party module yarn"
yarn install
# backward compatibility to bower in php scripts
# changed with Version 0.8
#ln -s node_modules vendor

print_out i "Install third party module ADOdb"
git clone https://github.com/ADOdb/ADOdb ./include/ADOdb

chown -R "$user:$group" "$openvpn_admin"
chown -R "$user:$group" $www/vpn
chown "$user:$group" $www/vpn/conf/server/server.conf

print_out 1 "${SETFIN01}"
print_out i "${SETFIN02}"
print_out i "${SETFIN03}"
print_out d "${SETFIN04}"
