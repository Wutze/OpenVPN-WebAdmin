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
# @version		1.4.1
# @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues


### Set Vars

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

### Important notice ###
# The @pos[nnn] indicates the sequence number of the functions or additional
# descriptions to make them easier to find.
###

#
# set static vars
#
config="installation/config.conf"
COLTABLE=/opt/install/COL_TABLE
# Set the path from which you started your installation
CURRENT_PATH=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
VERSION="1.4.1"

#
# init screen
# Find the rows and columns will default to 80x24 if it can not be detected
#
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
if [[ -f "${COLTABLE}" ]]; then
  # source it
  source ${COLTABLE}
# Otherwise,
else
  # Set these values so the installer can still run in color
  COL_NC='\e[0m' # No Color
  COL_LIGHT_GREEN='\e[1;32m'
  COL_LIGHT_RED='\e[1;31m'
  COL_BLUE='\e[94m'
  COL_YELLOW='\e[1;33m'
  INF0="[${COL_YELLOW}▸"
  INF1="◂${COL_NC}]"
  TICK="[${COL_LIGHT_GREEN}✓${COL_NC}]"
  CROSS="[${COL_LIGHT_RED}✗${COL_NC}]"
  DONE="${COL_LIGHT_GREEN} done!${COL_NC}"
	OVER="\\r\\033[K"
fi


##### System Functions #####

#
# If the entries are left with "Cancel", then create a corresponding error message
# @param Exitstatus $?
# @return Message OK or Exit Script
# @see additional description @pos100
# @pos001
#  
control_box(){
  exitstatus=$?
  if [ ${exitstatus} = 0 ]; then
      print_out 1 "Execution Ok: ${2}"
  else
      print_out 0 "Execution break: ${2}"
      exit
  fi
}

#
# Errors in the script are intercepted and displayed here
# @Call After executing a command: control_script "description"
# @param $? + Description
# @return continue script or or exit when error with exit 100
# @see additional description @pos100
# @pos002
#  
control_script(){
  if [ ! $? -eq 0 ]
  then
  print_out 0 "Error ${1} "
  exit 100
  fi
}

#
# formats the notes and messages in an appealing form
# @param [1|0|i|d|r] [Text]
# @example: print_out 1 "your text"
# @return formated Text with "0" red cross, "1" green tick, "i"nfo, "d"one Message or need input with "r"ead
# @see additional description @pos100
# @pos003
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
    echo -e " ${INF0} ${2} ${INF1}"
    ;;
    d)
    echo -e " ${DONE} ${2}"
    ;;
    r)	read -rsp " ${2}"
	    echo "【ツ】"
    ;;
  esac
  datum=$(date '+%Y-%m-%d:%H.%M.%S')
  echo ${datum}": "${2} >> loginstall.log
}

### Additional Description
# @pos100
# The two functions should be used in combination.
# "print_out" should indicate where the script is and what it wants to do,
# "control_script" or "control_box" should then indicate the completion of the action,
# whether it was successful or no
###

#
# my Intro with colored Logo
# @pos004
#
intro(){
  clear
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
datum=$(date '+%Y-%m-%d:%H.%M.%S')
echo ${datum}": Start Install" > loginstall.log
}

#
# you have define database?
# looks at define local or remote database
# you can take only one database, else error message
# @return $installsql [1|0]
# @return $mysqlserver for install
# @callfrom function start_install
# @pos005
#
collect_param_mysql(){
  print_out i "define selectet SQL-Server|SQL-Client"
  # If the variable xxx already contains a value, it means this function
  # has been called before. A double installation is not allowed
  # exit script
  if [ ${mysqlserver} ]; then
    print_out 0 "${FEHLER01} ${SELECT03} ${FEHLER03} ${SELECT04}. ${ONEONLY}"
    print_out 0 ${BREAK}
    exit
  fi
  if [ ${1} = 3 ]; then
    mysqlserver="mariadb-server"
    # Definition whether local server [1] or remote [0]
    installsql="1"
    print_out 1 "Install Server on ${OS}: ${mysqlserver}"
  elif [ ${1} = 4 ]; then
    if [ "${OS}" = "centos" ]; then
      mysqlserver="mysql"
      installsql="0"
    else
      mysqlserver="default-mysql-client"
      installsql="0"
    fi
    print_out 1 "Install Client on ${OS}: ${mysqlserver}"
  fi
}

#
# all collect Functions collect the script options
# @callfrom function start_install
# @pos006
#
collect_param_webserver(){
  print_out i "define selectet Webserver"
  if [ ${webserver} ]; then
    print_out 0 "${FEHLER01} ${SELECT04} ${FEHLER03} ${SELECT05}. ${ONEONLY}"
    print_out 0 ${BREAK}
    exit
  fi
  if [ ${1} = 5 ]; then
    if [ "${OS}" = "centos" ]; then
      webserver="httpd"
    else
      webserver="apache2"
    fi
    print_out 1 "Install on ${OS}: ${webserver}"
  elif [ ${1} = 6 ]; then
    webserver="nginx"
    print_out 1 "Install on ${OS}: ${webserver}"
  fi
}

#
# set var webroot
# @callfrom function start_install
# @pos007
#
collect_param_webroot(){
  print_out i "create WWW Root"
  WWWROOT="/srv/www"
  OVPNROOT="/ovpn-admin"
  OVPN_FULL_PATH=$WWWROOT$OVPNROOT
  mkdir $WWWROOT
  print_out 1 "create www-root ${WWWROOT}"
}

#
# set the php-script owner
# @callfrom function start_install
# @pos008
#
collect_param_owner(){
  print_out i "define Owner for permissions"
  OWNER="www-data"
  GROUPOWNER="www-data"
  print_out 1 "Set permissions: ${OWNER} ${GROUPOWNER}"
}

#
# copy install.conf, when you call it
# @callfrom function start_install
# @pos009
#
copy_config(){
  print_out i "copy install.config"
  cp installation/config.conf.sample installation/config.conf
  control_box $0 "copy config.conf"
}

#
# choose your favourite language
# or selected automatically from system settings
# @callfrom function main
# At the moment there are 3 language files
# If no known language is available, English is used as the default
# @pos010
#
set_language(){
  print_out i "Select Language"
  # Split System-Variable $LANG
  var1=${LANG%.*}
  ## Select Language to install
  var2=$(whiptail --title "Select Language" --menu "Select your language" ${r} ${c} ${h} \
    "AUTO" " Automatic" \
    "de_DE" " Deutsch" \
    "en_EN" " Englisch" \
    "fr_FR" " Français" \
    3>&1 1>&2 2>&3)
  RET=$?
  if [ ${RET} -eq 1 ]; then
    print_out 0 "Exit select language"
    exit
  elif [ ${RET} -eq 0 ]; then
    case "${var2}" in
      AUTO)
        if [[ -f "installation/lang/${var1}" ]]; then
          source "installation/lang/${var1}"
        else
          source "installation/lang/en_EN"
          var2="en_EN"
        fi
      ;;
      de_DE) source "installation/lang/${var2}"
      ;;
      en_EN) source "installation/lang/${var2}"
      ;;
      fr_FR) source "installation/lang/${var2}"
      ;;
      *) source "installation/lang/en_EN"
      ;;
    esac
  fi
  if [ $var2 = "AUTO" ]; then
    print_out 1 "Set Language to: ${var1}"
  else
    print_out 1 "Set Language to: ${var2}"
  fi
}

#
# description: you can only install with root privileges, check this
# name: check_user
# @param $?
# @return continue script or or exit when no root user
# @callfrom function main
# @pos011
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

#
# select current linux version
# @callfrom function main
# @pos012
#
set_os_version(){
  if [[ -e /etc/debian_version ]]; then
    OS="debian"
    OSVERSION=$(grep -oE '[0-9]+' /etc/debian_version | head -1)
    print_out i "Install on:  ${OS} ${OSVERSION}"
    # Fix Debian 10 Fehler
    export PATH=$PATH:/usr/sbin:/sbin
  elif [[ -e /etc/centos-release ]]; then
    OS="centos"
    OSVERSION=$(grep -oE '[0-9]+' /etc/centos-release | head -1)
    print_out i "Install on:  ${OS} ${OSVERSION}"
  else
    print_out 0 "No suitable operating system found, sorry"
    print_out 0 ${BREAK}
  fi
}

#
# tests if required programs are installed
# @callfrom function main
# @pos013
#
test_system(){
  print_out i "checks if all required programs are installed"
  for i in openvpn mysql php yarn node unzip wget sed route tar; do
    which $i > /dev/null
    if [ $? -ne 0 ]; then
      print_out 0 "${MISSING} ${COL_LIGHT_RED}${i}${COL_NC}! ${INSTALL}"
      print_out 0 "${BREAK}"
      exit
    fi
  done
}

#
# Selection of installation options
# @callfrom function start_install
# @pos014
#
do_select(){
  # nginx fehlt noch
  print_out i "give me inputs"
	sel=$(whiptail --title "${SELECT_A}" --checklist --separate-output "${SELECT_B}:" ${r} ${c} ${h} \
    "1" "${SELECT01} " on \
    "2" "${SELECT02} " on \
    "3" "${SELECT03} " on \
    "4" "${SELECT04} " off \
    "5" "${SELECT05} " on \
    "11" "${SELECT11} " off \
    "12" "${SELECT12} " off \
    "13" "${SELECT13} " off \
    "20" "${SELECT20} " off \
    3>&1 1>&2 2>&3)
#  RET=$?
  control_box $? "do_select"
}

#
# execute the settings from do_select
# @callfrom function main
# @pos015
#
start_install(){
  print_out 1 "Intro Attention"
  #### Start Script with Out- and Inputs
  ## first call funcions
  echo "${ATTENTION}" > README.FIRST.txt
  whiptail --textbox README.FIRST.txt --title "Information" ${r} ${c}

  print_out i "${BEFOR}"
  print_out r
  print_out 1 "@pos015 | Collect Informations from your Selections"
  do_select

  while read -r line;
  do #echo "${line}";
      case ${line} in
          1) copy_config
          ;;
          2) collect_param_install_programs ${line}
          ;;
          3|4) collect_param_mysql ${line}
          ;;
          5|6) collect_param_webserver ${line}
          ;;
          9|10) collect_param_owner ${line}
          ;;
          11) modules_dev="1"
              MOD_ENABLE="1"
          ;;
          12) modules_firewall="1"
              MOD_ENABLE="1"
          ;;
          13) modules_clientload="1"
              MOD_ENABLE="1"
          ;;
          20) modules_all="1"
              MOD_ENABLE="1"
          ;;
          *)
          ;;
      esac
  done < <(echo "${sel}")
  print_out 1 "pos@015 | fin collection"
}


#
# read config.conf
# you must copy config.conf.example to config.conf and edit this file
# @callfrom function main
# @pos016
#
check_config(){
  if [[ -f "${config}" ]]; then
    # source it
    source ${config}
  # Otherwise,
  else
    echo -e ${COL_LIGHT_RED}${CONFIG01}${COL_NC}
    echo -e ${CONFIG02}
    echo -e ${COL_LIGHT_GREEN}${CONFIG03}${COL_NC}
    echo -e ${CONFIG04}
    echo -e ${CROSS}" Script "${BREAK}
    exit
  fi
}

#
# you need this programs
# Here it is defined which operating system needs which programs
# @callfrom function start_install
# @pos017
#
collect_param_install_programs(){
  if [ "${OS}" == "debian" ]; then
    autoinstall="openvpn php-mysql php-zip php unzip git wget sed curl git net-tools npm nodejs"
  elif [ "${OS}" == "centos" ]; then
    autoinstall="openvpn php php-mysqlnd php-zip unzip git wget sed curl git net-tools tar npm"
  fi
}

#
# Start Program Installation
# @callfrom function main
# @pos018
#
install_programs_now(){
  if [ ! ${mysqlserver} ]; then
    print_out 0 "${INSTMESS}"
    print_out 0 "${BREAK}"
    exit
  fi
  print_out i "Please wait, the installation can take up to 10 minutes."
  print_out i "You can view the install progress in /opt/[ovpn-git] via"  
  print_out i "tail -f loginstall.log in another ssh session"
  if [ "${OS}" == "debian" ]; then
    print_out i "Update ${OS}"
    apt-get update -y >> loginstall.log
    print_out i "Upgrade ${OS}"
    apt-get upgrade -y >> loginstall.log
    control_box $? "${OS}-Update"
    print_out i "Install ${OS}"
    apt-get install ${webserver} ${autoinstall} ${mysqlserver} -y >> loginstall.log
    control_box $? "${OS}-Install"
    print_out i "Install npm/yarn ${OS}"
    npm install -g yarn >> loginstall.log
    control_box $? "${OS}-npm/yarn Install"
  elif [ "${OS}" == "centos" ]; then
    print_out i "Install epel-release ${OS}"
    yum install epel-release -y  >> loginstall.log
    control_box $? "${OS}-enable ebel-release"
    print_out i "Update ${OS}"
    yum update -y >> loginstall.log
    control_box $? "${OS}-Update"
    print_out i "Install ${OS}"
    yum install ${webserver} ${autoinstall} ${mysqlserver} -y >> loginstall.log
    control_box $? "${OS}-Install"
    print_out i "enable node.js ${OS}"
    yum module enable nodejs:12 >> loginstall.log
    control_box $? "${OS}-enable njode.js"
    print_out i "Install node.js ${OS}"
    yum install nodejs >> loginstall.log
    control_box $? "${OS}-Install node.js"
    print_out i "Install yarn ${OS}"
    npm install -g yarn >> loginstall.log
    control_box $? "${OS}-Install yarn"
  fi
  print_out 1 "Installation Ok -> ${OS}"
}

#
# Collect all variables here to be able to perform the installation
# @callfrom function main
# @pos019
#
give_me_input(){
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
  if [ "${db_host}" == localhost ]; then
    DBROOTPW=$(whiptail --inputbox "${SETVPN05}" ${r} ${c} ${DBROOTPW} --title "DB Root PW" 3>&1 1>&2 2>&3)
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
}

#
# after install mysql-server create mysql-root-pw
# @callfrom function main
# @pos020
#
set_mysql_rootpw(){
  print_out i "Insert/Set MySQL Root PW"
	DBROOTPW=$(whiptail --inputbox "${MYSQL01}" ${r} ${c} --title "${MYSQL02}" 3>&1 1>&2 2>&3)
  control_box $? "input mysql root pw"

  if [ mysql -u root -p${DBROOTPW} -e exit ]; then
    print_out 1 "root password already set"
    return
  fi

	echo "grant all on *.* to root@localhost identified by '${DBROOTPW}' with grant option;" | mysql -u root --password="${DBROOTPW}"
	echo "flush privileges;" | mysql -u root --password="${DBROOTPW}"
  control_box $? "set mysql root pw"
}

#  
# name: create_mysql
# @param dbname dbuser dbpass
# @return insert new database, user and setup password
# @callfrom function main
# @pos021
#  
create_database(){
  print_out i "Setup User Password for OpenVPN-WebAdmin on your DB-Server"
  EXPECTED_ARGS=3
  MYSQL=`which mysql`
  Q1="CREATE DATABASE IF NOT EXISTS $1;"
  Q2="GRANT ALL ON $1.* TO '$2'@'localhost' IDENTIFIED BY '$3';"
  Q3="FLUSH PRIVILEGES;"
  SQL="${Q1}${Q2}${Q3}"
   
  if [ $# -ne ${EXPECTED_ARGS} ]
  then
    echo "Usage: $0 dbname dbuser dbpass"
    exit
  fi
   
  $MYSQL -h ${db_host} -uroot --password=${DBROOTPW} -e "${SQL}"
  control_box $? "Create local Database"
}

#
# install database
# add admin and first user
# @param from do_select
# @callfrom function main
# @pos022
#
install_mysql_database(){
  print_out i "Setup Database"
  # current only new install
  mysql -h ${db_host} -u ${mysql_user} --password=${mysql_user_pass} ${db_name} < installation/sql/vpnadmin-1.4.0.dump
  control_script "Insert Database Dump"
  mysql -h ${db_host} -u ${mysql_user} --password=${mysql_user_pass} --database=${db_name} -e "INSERT INTO user (user_name, user_pass, gid, user_enable) VALUES ('${admin_user}', encrypt('${admin_user_pass}'),'1','1');"
  control_script "Insert Webadmin User"
  mysql -h ${db_host} -u ${mysql_user} --password=${mysql_user_pass} --database=${db_name} -e "INSERT INTO user (user_name, user_pass, gid, user_enable) VALUES ('${admin_user}-user', encrypt('${admin_user_pass}'),'2','1');"
  control_script "Insert first User"
  print_out 1 "setting up MySQL OK"
}

#
# make the TLS Certs for your OpenVPN-Server
# @param looks like the config.conf
# @callfrom function main
# @pos023
#
make_certs(){
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
  cp "${CURRENT_PATH}/installation/server.conf" "/etc/openvpn/"
  mkdir "/etc/openvpn/ccd"
  sed -i "s/port 443/port ${server_port}/" "/etc/openvpn/server.conf"

  if [ ${openvpn_proto} = "udp" ]; then
    sed -i "s/proto tcp/proto ${openvpn_proto}/" "/etc/openvpn/server.conf"
  fi

  nobody_group=$(id -ng nobody)
  sed -i "s/group nogroup/group ${nobody_group}/" "/etc/openvpn/server.conf"
}

#
# copy now all config file
# copy keys, scripts and make server.conf
# @callfrom function
# @pos024
#
make_openvpn_config_files(){
  # Replace in the client configurations with the ip of the server and openvpn protocol
  for file in $(find ../ -name client.ovpn); do
    sed -i "s/remote xxx\.xxx\.xxx\.xxx 443/remote ${ip_server} ${server_port}/" ${file}
    echo "<ca>" >> ${file}
    cat "/etc/openvpn/ca.crt" >> ${file}
    echo "</ca>" >> ${file}
    echo "<tls-auth>" >> ${file}
    cat "/etc/openvpn/ta.key" >> ${file}
    echo "</tls-auth>" >> ${file}
    if [ ${openvpn_proto} = "udp" ]; then
      sed -i "s/proto tcp-client/proto udp/" ${file}
    fi
  done

  mkdir -p $WWWROOT/vpn/{conf,history}/{server,osx,windows,gnu-linux,firewall}

  # Copy ta.key inside the client-conf directory
  for directory in "${WWWROOT}/vpn/conf/gnu-linux/" "${WWWROOT}/vpn/conf/osx/" "${WWWROOT}/vpn/conf/windows/"; do
    cp "/etc/openvpn/"{ca.crt,ta.key} $directory
  done

  #mkdir -p $WWWROOT/{vpn}/{history}/{server,osx,win,gnu-linux,firewall}
  
  #mkdir -p {$WWWROOT"/vpn",$WWWROOT"/vpn/history",$WWWROOT"/vpn/history/server",$WWWROOT"/vpn/history/osx",$WWWROOT"/vpn/history/gnu-linux",$WWWROOT"/vpn/history/win"}
  cp -r ${CURRENT_PATH}"/installation/conf" ${WWWROOT}"/vpn/"
  ln -s /etc/openvpn/server.conf ${WWWROOT}"/vpn/conf/server/server.conf"

  ## Copy bash scripts (which will insert row in MySQL)
  cp -r ${CURRENT_PATH}"/installation/scripts" "/etc/openvpn/"
  chmod +x "/etc/openvpn/scripts/"*

}

#
# write Database Instructions to config file for openvpn server
# @callfrom function
# @pos025
#
make_openvpn_setup(){
  # Configure MySQL in openvpn scripts
  cp /etc/openvpn/scripts/config.sample.sh /etc/openvpn/scripts/config.sh
  sed -i "s/DBHOST=''/DBHOST='${db_host}'/" "/etc/openvpn/scripts/config.sh"
  sed -i "s/DBUSER=''/DBUSER='${mysql_user}'/" "/etc/openvpn/scripts/config.sh"
  escaped=$(echo -n "${mysql_user_pass}" | sed 's#\\#\\\\#g;s#&#\\&#g')
  sed -i "s/DBPASS=''/DBPASS='${escaped}'/" "/etc/openvpn/scripts/config.sh"
  sed -i "s/DBNAME=''/DBNAME='${db_name}'/" "/etc/openvpn/scripts/config.sh"
}

#
# create webdirectory with the OpenVPN-WebAdmin Files
# @callfrom function
# @pos026
#
create_webdirectory(){
  # Create the directory of the web application
  print_out 1 "create webfolder"
  mkdir $OVPN_FULL_PATH
  control_script "create webfolder"
  if [ -n "${modules_dev}" ] || [ -n "${modules_all}" ]; then
    cp -r "${CURRENT_PATH}/wwwroot/"{index.php,version.php,favicon.ico,js,include,css,images,data,dev} "${OVPN_FULL_PATH}"
    control_script "copy webfolder with dev"
  else
    cp -r "${CURRENT_PATH}/wwwroot/"{index.php,version.php,favicon.ico,js,include,css,images,data} "${OVPN_FULL_PATH}"
    control_script "copy webfolder"
  fi
}

#
# Install now all third party modules
# node_modules, ADOdb
# @callfrom function main
# @pos027
#
create_third_party(){
  print_out i "Create Third Party Module"
  ## node_modules in separate folder
  mkdir $WWWROOT"/ovpn_modules"
  control_script "create modules folder"
  cp $CURRENT_PATH"/wwwroot/package.json" $WWWROOT"/ovpn_modules/"
  control_script "copy package.json"

  cd $WWWROOT"/ovpn_modules/"
  print_out i "Install third party module yarn"
  yarn install
  control_script "yarn installed"
  print_out i "Install third party module ADOdb"
  git clone https://github.com/ADOdb/ADOdb $WWWROOT"/ovpn_modules/ADOdb"
  control_script "ADOdb installed"

  ## link from module folder into webfolder
  ln -s $WWWROOT"/ovpn_modules/ADOdb" $OVPN_FULL_PATH"/include/ADOdb"
  control_script "create Link ADOdb"
  ln -s $WWWROOT"/ovpn_modules/node_modules" $OVPN_FULL_PATH"/node_modules"
  control_script "create Link node_modules"
}

#
# you need config.php file in your OpenVPN-WebAdmin
# Here it is written
# @callfrom function main
# @pos028
#
write_webconfig(){
  {
  echo "<?php
/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020 OpenVPN-WebAdmin
 *
 * NOTICE OF LICENSE
 *
 * GNU AFFERO GENERAL PUBLIC LICENSE V3
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/agpl-3.0.en.html
 *
 * @fork Original Idea and parts in this script from: https://github.com/Chocobozzz/OpenVPN-Admin
 *
 * @author    Wutze
 * @copyright 2020 OpenVPN-WebAdmin
 * @link			https://github.com/Wutze/OpenVPN-WebAdmin
 * @see				Internal Documentation ~/doc/
 * @version		\"${VERSION}\"
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos(\$_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');"
  echo ""
  echo ""
  echo "\$dbhost=\"${db_host}\";"
  echo "\$dbuser=\"${mysql_user}\";"
  echo "\$dbname=\"${db_name}\";"
  echo "\$dbport=\"3306\";"
  echo "\$dbpass=\"${escaped}\";"
  echo "\$dbtype=\"mysqli\";"
  echo "\$dbdebug=FALSE;"
  echo "\$sessdebug=FALSE;"

  echo "
/* Site-Name */
define('_SITE_NAME',\"OVPN-WebAdmin\");
define('HOME_URL',\"vpn.home\");
define('_DEFAULT_LANGUAGE','en_EN');

/** Login Site */
define('_LOGINSITE','login1');"

  }> ${OVPN_FULL_PATH}"/include/config.php"

  if [ -n "${modules_dev}" ] || [ -n "${modules_all}" ]; then
    echo "
/** 
 * only for development!
 * please comment out if no longer needed!
 * comment in the \"define function\" to enable
 */
if(file_exists(\"dev/dev.php\")){
  define('dev','dev/dev.php');
}
if (defined('dev')){
  include('dev/class.dev.php');
}
" >> ${OVPN_FULL_PATH}"/include/module.config.php"
    MOD_ENABLE="1"
  fi

  if [ -n "${modules_firewall}" ] || [ -n "${modules_all}" ]; then
    echo "
define('firewall',TRUE);
" >> ${OVPN_FULL_PATH}"/include/module.config.php"
    MOD_ENABLE="1"
  fi

  if [ -n "${modules_clientload}" ] || [ -n "${modules_all}" ]; then
    echo "
define('clientload',TRUE);
" >> ${OVPN_FULL_PATH}"/include/module.config.php"
    MOD_ENABLE="1"
  fi
  
  print_out i "Config and Module Config written"

}

#
# write the config file for updates
# @callfrom function
# @pos029
#
write_config(){
  print_out i "write file for future updates"
  updpath="/var/lib/ovpn-admin/"
  mkdir $updpath
  updfile="config.ovpn-admin.upd"

  SERVERID=$( cat /etc/machine-id )

  {
  echo "VERSION=\"${VERSION}\""
  echo "DBHOST=\"${db_host}\""
  echo "DBUSER=\"${mysql_user}\""
  echo "DBNAME=\"${db_name}\""
  echo "BASEPATH=\"openvpn-admin\""
  echo "WEBROOT=\"${WWWROOT}\""
  echo "WWWOWNER=\"www-data\""
  echo "### Is it still the original installed system?"
  echo "MACHINEID=$LOCALMACHINEID"
  echo "INSTALLDATE=\"$(date '+%Y-%m-%d %H:%M:%S')\""
  }> ${updpath}${updfile}

#  if [ -n "$installextensions" ]; then
#  {
#    echo "### you have installed modules"
#    echo "MODULES=\"$installextensions\""
#    echo "MODSSL=\"$modssl\""
#    echo "MODDEV=\"$moddev\""
#    }>> $updpath$updfile
#  fi

  control_box $? "write config"
  print_out 1 "update informations written (${updpath})"
  chmod -R 600 ${updpath}

}

#
# set all permissions
# @callfrom function main
# @pos031
#
set_permissions(){

  chown -R ${OWNER}:${GROUPOWNER} ${OVPN_FULL_PATH}
  chown -R ${OWNER}:${GROUPOWNER} ${WWWROOT}"/vpn"
  chown ${OWNER}:${GROUPOWNER} ${WWWROOT}"/vpn/conf/server/server.conf"

  chown -R root ${updpath}
  chmod -R 600 ${updpath}

  print_out d "Setup ready - please read informations!"
}

#
# If the installation was successful
# Displays additional information
# @callfrom function main
# @pos032
#
fin(){
  print_out 1 "${SETFIN01}"
  print_out i "${SETFIN02}"
  print_out i "${SETFIN03}"
  print_out d "${SETFIN04}"

  if [ -n "${MOD_ENABLE}" ]; then
    print_out i "${MOENABLE0}"
    print_out i "${MOENABLE1}"
  fi
  datum=$(date '+%Y-%m-%d:%H.%M.%S')
  echo ${datum}": Fin Install - thank you ;o)" > loginstall.log
}

#
# create simple firewall-script
# @callfrom function main
# @pos034
#
function create_firewall(){
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
FW=\"iptables\"

## reset iptables
\$FW -F
\$FW -X
\$FW -t nat -F
\$FW -t nat -X
\$FW -t mangle -F
\$FW -t mangle -X
\$FW -P INPUT ACCEPT
\$FW -P FORWARD ACCEPT
\$FW -P OUTPUT ACCEPT

# Get primary NIC device name
primary_nic=`route | grep '^default' | grep -o '[^ ]*$'`

# Iptable rules
\$FW -I FORWARD -i tun0 -j ACCEPT
\$FW -I FORWARD -o tun0 -j ACCEPT
\$FW -I OUTPUT -o tun0 -j ACCEPT

\$FW -A FORWARD -i tun0 -o \$primary_nic -j ACCEPT
\$FW -t nat -A POSTROUTING -o \$primary_nic -j MASQUERADE
\$FW -t nat -A POSTROUTING -s 10.8.0.0/24 -o \$primary_nic -j MASQUERADE
\$FW -t nat -A POSTROUTING -s 10.8.0.2/24 -o \$primary_nic -j MASQUERADE

# fixes problems with the persistent transmissions e.g. netflix
\$FW -t mangle -o \$primary_nic --insert FORWARD 1 -p tcp --tcp-flags SYN,RST SYN -m tcpmss --mss 1400:65495 -j TCPMSS --clamp-mss-to-pmtu
" > /usr/sbin/firewall.sh

chmod +x /usr/sbin/firewall.sh
systemctl enable firewall.service
systemctl start firewall
}

#
# creates the paths
# obsolete
function create_dirs(){
  mkdir $WWWROOT
  mkdir $OVPN_FULL_PATH
  if [ -n "$modules_dev" ] || [ -n "$modules_all" ]; then
    cp -r "$CURRENT_PATH/wwwroot/"{index.php,version.php,favicon.ico,js,include,css,images,data,dev} $OVPN_FULL_PATH
  else
    cp -r "$CURRENT_PATH/wwwroot/"{index.php,version.php,favicon.ico,js,include,css,images,data} $OVPN_FULL_PATH
  fi

  ## node_modules in separate folder
  mkdir $WWWROOT/ovpn_modules
  cp "$CURRENT_PATH/wwwroot/package.json" $WWWROOT"/ovpn_modules/"

  mkdir {$WWWROOT/vpn,$WWWROOT/vpn/history,$WWWROOT/vpn/history/server,$WWWROOT/vpn/history/osx,$WWWROOT/vpn/history/gnu-linux,$WWWROOT/vpn/history/win}
  cp -r "$CURRENT_PATH/"installation/conf $WWWROOT"/vpn/"
  ln -s /etc/openvpn/server.conf $WWWROOT"/vpn/conf/server/server.conf"

}

##
## new install routine
## Setup changed, as it has become much too confusing
## @param Nothing. I collect everything in the coming minutes
## @return installed OpenVPN-WebAdmin (i hope so) ,o)
## @callfrom the last line in this script
## @pos000
##
main(){
  #
  # @pos004
  intro

  #
  # As the name says
  # @pos010
  set_language

  #
  # Check root permission
  # @pos011
  check_user

  #
  # Set OS Version and checks if this version is supported
  # @pos012
  set_os_version
  
  #
  # set all web params
  # @pos007, @pos008
  collect_param_webroot
  collect_param_owner

  #
  # start selection of install options
  # set the install options
  # @pos015
  start_install

  #
  # check vars in your install config
  # @pos016
  check_config

  #
  # If all programs are to be installed automatically
  # @pos018
  if [[ ${autoinstall} ]]; then
    install_programs_now
  fi

  #
  # If MySQL Server is to be installed locally
  # @pos020
  if [[ ${installsql} = "1" ]]; then
    set_mysql_rootpw
  fi  

  #
  # checks if all required programs are installed
  # @pos013
  test_system

  #
  # Input of all system relevant data
  # @pos019
  give_me_input

  #
  # As the name says
  # @pos021
  create_database $db_name $mysql_user $mysql_user_pass

  #
  # As the name says
  # @pos022
  install_mysql_database

  #
  # As the name says
  # @pos023
  make_certs

  #
  # As the name says
  # @pos024
  make_openvpn_config_files

  #
  # set Database Permissions
  # @pos025
  make_openvpn_setup

  #
  # As the name says
  # @pos026
  create_webdirectory

  #
  # As the name says
  # @pos027
  create_third_party

  #
  # As the name says
  # @pos028
  write_webconfig

  #
  # As the name says
  # @pos029
  write_config

  #
  # As the name says
  # @pos034
  create_firewall

  #
  # As the name says
  # @pos031
  set_permissions

  #
  # As the name says
  # @pos032
  fin
}




##
## Main call to setup
## @param none
## @pos000
##
main
