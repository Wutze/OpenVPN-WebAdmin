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
# @author     Wutze
# @copyright  2020 OpenVPN-WebAdmin
# @link       https://github.com/Wutze/OpenVPN-WebAdmin
# @see        Internal Documentation ~/doc/
# @version    1.4.2
# @todo       new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues


### Set Vars
# debug
# If you want to debug the script, start it with this call
## DEBUG=1 ./install.sh
test -z "$DEBUG" || set -x

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
source installation/functions.sh
config="installation/config.conf"
BACKTITLE="OVPN-Admin [INSTALLATION]"
# Set the path from which you started your installation
CURRENT_PATH=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
VERSION="1.4.1"

##### System Functions #####


### Additional Description
# @pos100
# The two functions should be used in combination.
# "message_print_out" should indicate where the script is and what it wants to do,
# "control_script_message" or "control_box" should then indicate the completion of the action,
# whether it was successful or no
###


#
# you have define database?
# looks at define local or remote database
# you can take only one database, else error message
# @return $installsql [1|0]
# @return $mysqlserver for install
# @callfrom function do_select_start_install
# @pos005
#
collect_param_mysql(){
  message_print_out i "define selectet SQL-Server|SQL-Client"
  # If the variable xxx already contains a value, it means this function
  # has been called before. A double installation is not allowed
  # exit script
  if [ ${mysqlserver} ]; then
    message_print_out 0 "${FEHLER01} ${SELECT03} ${FEHLER03} ${SELECT04}. ${ONEONLY}"
    message_print_out 0 ${BREAK}
    exit
  fi
  if [ ${1} = 3 ]; then
    mysqlserver="mariadb-server"
    # Definition whether local server [1] or remote [0]
    installsql="1"
    message_print_out 1 "Install Server on ${OS}: ${mysqlserver}"
  elif [ ${1} = 4 ]; then
    if [ "${OS}" = "centos" ]; then
      mysqlserver="mysql"
      setsebool -P httpd_can_network_connect_db on
      installsql="0"
    else
      mysqlserver="default-mysql-client"
      installsql="0"
    fi
    message_print_out 1 "Install Client on ${OS}: ${mysqlserver}"
  fi
}

#
# all collect Functions collect the script options
# @callfrom function do_select_start_install
# @pos006
#
collect_param_webserver(){
  message_print_out i "define selectet Webserver"
  if [ ${webserver} ]; then
    message_print_out 0 "${FEHLER01} ${SELECT04} ${FEHLER03} ${SELECT05}. ${ONEONLY}"
    message_print_out 0 ${BREAK}
    exit
  fi
  if [ ${1} = 5 ]; then
    if [ "${OS}" = "centos" ]; then
      webserver="httpd"
    else
      webserver="apache2"
    fi
    message_print_out 1 "Install on ${OS}: ${webserver}"
  elif [ ${1} = 6 ]; then
    webserver="nginx"
    message_print_out 1 "Install on ${OS}: ${webserver}"
  fi
}

#
# set var webroot
# @callfrom function do_select_start_install
# @pos007
#
collect_param_webroot(){
  message_print_out i "Set Parameters Webserver"
  WWWROOT="/srv/www"
  OVPNROOT="/openvpn-admin"
  OVPN_FULL_PATH=$WWWROOT$OVPNROOT
}

#
# set the php-script owner
# @callfrom function do_select_start_install
# @pos008
#
collect_param_owner(){
  message_print_out i "define Owner for permissions"
  if [ "${OS}" == "debian" ]; then
    OWNER="www-data"
    GROUPOWNER="www-data"
  elif [ "${OS}" == "centos" ]; then
    OWNER="apache"
    GROUPOWNER="apache"
  fi
  message_print_out 1 "define permissions on ${OS} : ${OWNER}:${GROUPOWNER}"
}

#
# copy install.conf, when you call it
# @callfrom function do_select_start_install
# @pos009
#
copy_config(){
  message_print_out i "copy install.config"
  cp installation/config.conf.sample installation/config.conf
  control_box $0 "copy config.conf"
}

#
# tests if required programs are installed
# @callfrom function main
# @pos013
#
test_system(){
  message_print_out i "checks if all required programs are installed"
  for i in openvpn mysql php yarn node unzip wget sed route tar; do
    which $i > /dev/null
    if [ $? -ne 0 ]; then
      message_print_out 0 "${MISSING} ${COL_LIGHT_RED}${i}${COL_NC}! ${INSTALL}"
      message_print_out 0 "${BREAK}"
      exit
    fi
  done
}

#
# Selection of installation options
# @callfrom function do_select_start_install
# @pos014
#
do_select(){
  # nginx fehlt noch
  message_print_out i "give me inputs"
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
do_select_start_install(){
  message_print_out 1 "Intro Attention"
  #### Start Script with Out- and Inputs
  ## first call funcions
  ## creates a readme first file with installation information
  echo "${ATTENTION}" > README.FIRST.txt
  whiptail --textbox README.FIRST.txt --title "Information" ${r} ${c}

  message_print_out i "${BEFOR}"
  message_print_out r
  message_print_out 1 "Select the installationoptions:"
  ## go to @pos014, select install options
  do_select
  ## execute the previously selected options
  while read -r line;
  do #echo "${line}";
      case ${line} in
          1) copy_config ## @pos009
          ;;
          2) collect_param_install_programs ${line} ## @pos017
          ;;
          3|4) collect_param_mysql ${line} ## @pos005
          ;;
          5|6) collect_param_webserver ${line} ## @pos006
          ;;
          9|10) collect_param_owner ${line} ## @pos008
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
  message_print_out 1 "Fin selection"
}


#
# read config.conf
# you must copy config.conf.example to config.conf and edit this file
# @callfrom function main
# @pos016
#
check_config(){
  message_print_out i "check install-config"
  if [[ -f "${config}" ]]; then
    # source it
    source ${config}
  # Otherwise,
  else
    echo -e ${COL_LIGHT_RED}${CONFIG01}${COL_NC}
    echo -e ${CONFIG02}
    echo -e ${COL_LIGHT_GREEN}${CONFIG03}${COL_NC}
    echo -e ${CONFIG04}
    echo -e ${CROSS}" "${BREAK}
    message_print_out 0 "error check install-config"
    exit
  fi
  message_print_out 1 "read install-config"
}

#
# Security updates come in too late from the distributions.
# Therefore, the openvpn repo will be used from now on.
# @callfrom function collect_param_install_programs
#
set_openvpn_repo(){
  message_print_out i "set openvpn repo"
  if [ "${OS}" == "debian" ]; then
    apt-get update && apt-get -y install ca-certificates wget net-tools gnupg >> ${CURRENT_PATH}/loginstall.log
    wget -O - https://swupdate.openvpn.net/repos/repo-public.gpg | apt-key add -
    echo "deb http://build.openvpn.net/debian/openvpn/stable/ ${CODENAME} main" > /etc/apt/sources.list.d/openvpn-as-repo.list
    apt-get update >> ${CURRENT_PATH}/loginstall.log
  elif [ "${OS}" == "centos" ]; then
    yum copr enable dsommers/openvpn-git -y >> ${CURRENT_PATH}/loginstall.log
  fi
  message_print_out 1 "set openvpn repo ${OS}"
}

#
# you need this programs
# Here it is defined which operating system needs which programs
# @callfrom function do_select_start_install
# @pos017
#
collect_param_install_programs(){
  set_openvpn_repo
  message_print_out i "collect install programms"
  if [ "${OS}" == "debian" ]; then
    autoinstall="openvpn unzip git wget sed curl git net-tools nodejs"
  elif [ "${OS}" == "centos" ]; then
    autoinstall="openvpn unzip git wget sed curl git net-tools tar npm"
  fi
  message_print_out 1 "collect install programms for ${OS}"
}

#
# Start Program Installation
# @callfrom function main
# @pos018
#
install_programs_now(){
  if [ ! ${mysqlserver} ]; then
    message_print_out 0 "${INSTMESS}"
    message_print_out 0 "${BREAK}"
    exit
  fi
  message_print_out i "${INFO001}"
  message_print_out i "${INFO002}"  
  message_print_out i "${INFO003}"
  if [ "${OS}" == "debian" ]; then
    message_print_out i "Update ${OS}"
    apt-get update -y >> ${CURRENT_PATH}/loginstall.log
    message_print_out i "Upgrade ${OS}"
    apt-get upgrade -y >> ${CURRENT_PATH}/loginstall.log
    control_box $? "${OS}-Update"
    message_print_out i "Install Nodejs 12.x ${OS}"
    wget https://deb.nodesource.com/setup_12.x -O node-setup.sh >> ${CURRENT_PATH}/loginstall.log
    chmod 700 node-setup.sh >> ${CURRENT_PATH}/loginstall.log
    ./node-setup.sh >> ${CURRENT_PATH}/loginstall.log
    message_print_out i "Install Packages ${OS}"
    echo "apt-get install ${webserver} ${autoinstall} ${mysqlserver}"
    apt-get install ${webserver} ${autoinstall} ${mysqlserver} -y >> ${CURRENT_PATH}/loginstall.log
    control_box $? "${OS}-Install"
    message_print_out i "Install npm/yarn ${OS}"
    npm install -g yarn >> ${CURRENT_PATH}/loginstall.log
    control_box $? "${OS}-npm/yarn Install"
  elif [ "${OS}" == "centos" ]; then
    ## disable the firewall bullshit
    if (whiptail --title "Question" --yesno "${CENTOSME}" ${r} ${c}); then
      message_print_out 1 "Continue."
    else
      message_print_out 0 "You would rather work with a pseudo security. Script end"
      exit
    fi
    
    systemctl stop firewalld >> ${CURRENT_PATH}/loginstall.log
    systemctl disable firewalld >> ${CURRENT_PATH}/loginstall.log
    systemctl mask --now firewalld >> ${CURRENT_PATH}/loginstall.log

    message_print_out i "Install epel-release ${OS}"
    yum install epel-release -y  >> ${CURRENT_PATH}/loginstall.log
    control_box $? "${OS}-enable epel-release"
    message_print_out i "Update ${OS}"
    yum update -y >> ${CURRENT_PATH}/loginstall.log
    control_box $? "${OS}-Update"
    message_print_out i "Install Packages ${OS}"
    yum install ${webserver} ${autoinstall} ${mysqlserver} -y >> ${CURRENT_PATH}/loginstall.log

    if [ $installsql = "1" ]; then
      systemctl enable mariadb >> ${CURRENT_PATH}/loginstall.log
      systemctl start mariadb >> ${CURRENT_PATH}/loginstall.log
    fi

    message_print_out i "Enable OpenVPN-Server"
    mkdir /var/log/openvpn
    # diese Änderung ist notwendig, da sonst die server.conf nicht per Web editiert werden kann
    # bzw. der OpenVPN-Server schlicht nicht starten mag
    sed -i "s/SELINUX=enforcing/SELINUX=disabled/" "/etc/selinux/config"
    systemctl -f enable openvpn-server@server.service >> ${CURRENT_PATH}/loginstall.log
    systemctl start httpd >> ${CURRENT_PATH}/loginstall.log
    systemctl enable httpd >> ${CURRENT_PATH}/loginstall.log
    control_box $? "${OS}-Install"
    message_print_out i "enable/install nodejs ${OS}"
    ## jetzt Version 12 da Version 10 veraltet
    wget -qO- https://raw.githubusercontent.com/nvm-sh/nvm/v0.38.0/install.sh | bash >> ${CURRENT_PATH}/loginstall.log
    yum module reset nodejs:10 -y >> ${CURRENT_PATH}/loginstall.log
    yum module enable nodejs:12 -y >> ${CURRENT_PATH}/loginstall.log
    yum install nodejs -y >> ${CURRENT_PATH}/loginstall.log
    control_box $? "${OS}-enabled/install nodejs"
    message_print_out i "Install yarn ${OS}"
    curl --silent --location https://dl.yarnpkg.com/rpm/yarn.repo | tee /etc/yum.repos.d/yarn.repo >> ${CURRENT_PATH}/loginstall.log
    rpm --import https://dl.yarnpkg.com/rpm/pubkey.gpg >> ${CURRENT_PATH}/loginstall.log
    yum install yarn -y >> ${CURRENT_PATH}/loginstall.log
    control_box $? "${OS}-Install yarn"
  fi
  message_print_out 1 "Installation Ok -> ${OS}"
}

#
# Collect all variables here to be able to perform the installation
# @callfrom function main
# @pos019
#
give_me_input(){
  message_print_out i "Setup the variables"
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
  
  message_print_out 1 "the setup have all variables now"
}

#
# after install mysql-server create mysql-root-pw
# @callfrom function main
# @pos020
#
set_mysql_rootpw(){
  DBROOTPW=$(whiptail --inputbox "${MYSQL01}" ${r} ${c} --title "${MYSQL02}" 3>&1 1>&2 2>&3)

  message_print_out i "Insert/Set MySQL Root PW"
  control_box $? "input mysql root pw"
  return


  if [ "${OS}" == "centos" ]; then
    echo "CALL: mysql_secure_installation (centos)"
    mysql_secure_installation >> ${CURRENT_PATH}/loginstall.log 2>&1 <<EOF

y
${DBROOTPW}
${DBROOTPW}
y
y
y
y
EOF
  elif [ "${OS}" == "debian" ]; then
    echo "CALL: mysql -u root --password=\"${DBROOTPW}\" (debian)"
    echo "grant all on *.* to root@localhost identified by '${DBROOTPW}' with grant option;" | mysql -u root --password="${DBROOTPW}"
    echo "flush privileges;" | mysql -u root --password="${DBROOTPW}"
  fi
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
  message_print_out i "Setup User Password for OpenVPN-WebAdmin on your DB-Server"
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
  echo "CALL: $MYSQL -h ${db_host} -uroot --password=${DBROOTPW} -e \"${SQL}\""
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
  message_print_out i "Setup Database"
  # current only new install
  mysql -h ${db_host} -u ${mysql_user} --password=${mysql_user_pass} ${db_name} < installation/sql/vpnadmin-${VERSION}.dump
  control_script_message "Insert Database Dump"
  mysql -h ${db_host} -u ${mysql_user} --password=${mysql_user_pass} --database=${db_name} -e "INSERT INTO user (user_name, user_pass, gid, user_enable) VALUES ('${admin_user}', encrypt('${admin_user_pass}'),'1','1');"
  control_script_message "Insert Webadmin User"
  mysql -h ${db_host} -u ${mysql_user} --password=${mysql_user_pass} --database=${db_name} -e "INSERT INTO user (user_name, user_pass, gid, user_enable) VALUES ('${admin_user}-user', encrypt('${admin_user_pass}'),'2','1');"
  control_script_message "Insert first User"
  message_print_out 1 "setting up MySQL OK"
}

#
# make the TLS Certs for your OpenVPN-Server
# @param looks like the config.conf
# @callfrom function main
# @pos023
#
make_certs(){
  message_print_out i "Creating the certificates"

  # Get the rsa keys
  # mal austauschen gegen "neu"
  # https://github.com/OpenVPN/easy-rsa/archive/master.zip
  cd /opt/
  wget "https://github.com/OpenVPN/easy-rsa/releases/download/v3.0.6/EasyRSA-unix-v3.0.6.tgz"
  tar -xaf "EasyRSA-unix-v3.0.6.tgz"
  mv "EasyRSA-v3.0.6" /etc/openvpn/easy-rsa
  rm "EasyRSA-unix-v3.0.6.tgz"

  cd /etc/openvpn/easy-rsa

  message_print_out i "Setup OpenVPN"
  message_print_out i "Init PKI dirs and build CA certs"
  ./easyrsa init-pki
  ./easyrsa build-ca nopass
  message_print_out i "Generate Diffie-Hellman parameters"
  ./easyrsa gen-dh
  message_print_out i "Genrate server keypair for "$ip_server
  ./easyrsa build-server-full $ip_server nopass
  message_print_out i "Generate shared-secret for TLS Authentication"
  openvpn --genkey --secret pki/ta.key
  message_print_out 1 "setting up EasyRSA Ok"
  message_print_out 1 "Creating the certificates"
  
  # Copy certificates and the server configuration in the openvpn directory
  if [ "${OS}" == "centos" ]; then
    # CentOS is unfortunately somewhat special here.
    # Originally the path should only be a different one,
    # on "install_programs_now" now the system start script is changed
    OVPNSERVERPATH="/etc/openvpn"
  else
    OVPNSERVERPATH="/etc/openvpn"
  fi

  cp /etc/openvpn/easy-rsa/pki/{ca.crt,ta.key,issued/${ip_server}.crt,private/${ip_server}.key,dh.pem} ${OVPNSERVERPATH}
  message_print_out 1 "Copy Certifikates ${OVPNSERVERPATH}"
  cp "${CURRENT_PATH}/installation/server.conf" ${OVPNSERVERPATH}
  message_print_out 1 "Copy Server Conf"
  # ccd dir
  # The folder is entered directly in the server configuration and
  # should not be changed, otherwise the login scripts may not work properly
  mkdir "/etc/openvpn/ccd"
  message_print_out 1 "make ccd dir"
  sed -i "s/port 443/port ${server_port}/" "${OVPNSERVERPATH}/server.conf"
  message_print_out 1 "Set Openvpn Proto"
  if [ ${openvpn_proto} = "udp" ]; then
    sed -i "s/proto tcp/proto ${openvpn_proto}/" "${OVPNSERVERPATH}/server.conf"
  fi

  nobody_group=$(id -ng nobody)
  sed -i "s/group nogroup/group ${nobody_group}/" "${OVPNSERVERPATH}/server.conf"
  message_print_out 1 "Change Access OpenVPN Group"
  message_print_out 1 "Setup OpenVPN Finish"
}

#
# copy now all config file
# copy keys, scripts and make server.conf
# @callfrom function main
# @pos024
#
create_openvpn_config_files(){
  # Replace in the client configurations with the ip of the server and openvpn protocol
  message_print_out i "make config-files for vpn"
  cd ${CURRENT_PATH}
  for file in $(find ../ -name client.ovpn); do
    sed -i "s/remote xxx\.xxx\.xxx\.xxx 443/remote ${ip_server} ${server_port}/" ${file}
    echo "<ca>" >> ${file}
    cat "${OVPNSERVERPATH}/ca.crt" >> ${file}
    echo "</ca>" >> ${file}
    echo "<tls-auth>" >> ${file}
    cat "${OVPNSERVERPATH}/ta.key" >> ${file}
    echo "</tls-auth>" >> ${file}
    if [ ${openvpn_proto} = "udp" ]; then
      sed -i "s/proto tcp-client/proto udp/" ${file}
    fi
  done

  mkdir -p $WWWROOT/vpn/{conf,history}/{server,osx,windows,gnu-linux,firewall}

  # Copy ta.key inside the client-conf directory
  for directory in "${WWWROOT}/vpn/conf/gnu-linux/" "${WWWROOT}/vpn/conf/osx/" "${WWWROOT}/vpn/conf/windows/"; do
    cp "${OVPNSERVERPATH}/"{ca.crt,ta.key} $directory
  done

  #mkdir -p $WWWROOT/{vpn}/{history}/{server,osx,win,gnu-linux,firewall}
  
  #mkdir -p {$WWWROOT"/vpn",$WWWROOT"/vpn/history",$WWWROOT"/vpn/history/server",$WWWROOT"/vpn/history/osx",$WWWROOT"/vpn/history/gnu-linux",$WWWROOT"/vpn/history/win"}
  cp -r ${CURRENT_PATH}"/installation/conf" ${WWWROOT}"/vpn/"
  ln -s ${OVPNSERVERPATH}/server.conf ${WWWROOT}"/vpn/conf/server/server.conf"

  message_print_out i "adjust key and cert for the server for ${ip_server}"
  sed -i "s/cert server.crt/cert ${ip_server}.crt/" /etc/openvpn/server.conf
  sed -i "s/key server.key/key ${ip_server}.key/" /etc/openvpn/server.conf

  ## Copy bash scripts (which will insert row in MySQL)
  cp -r ${CURRENT_PATH}"/installation/scripts" ${OVPNSERVERPATH}
  chmod +x "${OVPNSERVERPATH}/scripts/"*

  message_print_out 1 "make config-files vpn"

}

#
# write Database Instructions to config file for openvpn server
# @callfrom function
# @pos025
#
create_openvpn_setup(){
  # Configure MySQL in openvpn scripts
  message_print_out i "Create Access-Configfile for VPN-Scripts/Server"
  cp "${OVPNSERVERPATH}/scripts/config.sample.sh" "${OVPNSERVERPATH}/scripts/config.sh"
  control_script_message "create script directory"
  sed -i "s/DBHOST=''/DBHOST='${db_host}'/" "${OVPNSERVERPATH}/scripts/config.sh"
  sed -i "s/DBUSER=''/DBUSER='${mysql_user}'/" "${OVPNSERVERPATH}/scripts/config.sh"
  escaped=$(echo -n "${mysql_user_pass}" | sed 's#\\#\\\\#g;s#&#\\&#g')
  sed -i "s/DBPASS=''/DBPASS='${escaped}'/" "${OVPNSERVERPATH}/scripts/config.sh"
  sed -i "s/DBNAME=''/DBNAME='${db_name}'/" "${OVPNSERVERPATH}/scripts/config.sh"
  message_print_out 1 "Access Config for VPN-Scripts/Server created"
}

#
# create webdirectory with the OpenVPN-WebAdmin Files
# @callfrom function
# @pos026
#
create_webdirectory(){
  # Create the directory of the web application
  message_print_out 1 "Create webfolder"
  mkdir $WWWROOT
  message_print_out 1 "Create Rootfolder ${WWWROOT}"

  mkdir $OVPN_FULL_PATH
  control_script_message "Create Webfolder"
  if [ -n "${modules_dev}" ] || [ -n "${modules_all}" ]; then
    cp -r "${CURRENT_PATH}/wwwroot/"{index.php,version.php,favicon.ico,js,include,css,images,data,dev} "${OVPN_FULL_PATH}"
    control_script_message "Copy webfolder with dev"
  else
    cp -r "${CURRENT_PATH}/wwwroot/"{index.php,version.php,favicon.ico,js,include,css,images,data} "${OVPN_FULL_PATH}"
    control_script_message "Copy webfolder"
  fi
}

#
# Install now all third party modules
# node_modules, ADOdb
# @callfrom function main
# @pos027
#
create_third_party(){
  message_print_out i "Create Third Party Module"
  ## node_modules in separate folder
  mkdir $WWWROOT"/ovpn_modules"
  control_script_message "create modules folder"
  cp $CURRENT_PATH"/wwwroot/package.json" $WWWROOT"/ovpn_modules/"
  control_script_message "copy package.json"

  cd $WWWROOT"/ovpn_modules/"
  message_print_out i "Install third party module yarn"
  yarn install
  control_script_message "yarn installed"
  message_print_out i "Install third party module ADOdb"
  git clone https://github.com/ADOdb/ADOdb $WWWROOT"/ovpn_modules/ADOdb"
  control_script_message "ADOdb installed"

  ## link from module folder into webfolder
  ln -s $WWWROOT"/ovpn_modules/ADOdb" $OVPN_FULL_PATH"/include/ADOdb"
  control_script_message "create Link ADOdb"
  ln -s $WWWROOT"/ovpn_modules/node_modules" $OVPN_FULL_PATH"/node_modules"
  control_script_message "create Link node_modules"
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
  echo "\$dbpass=\"PASSWORD\";"
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

  sed -i "s/dbpass=\"PASSWORD\"/dbpass=\"${escaped}\"/" "${OVPN_FULL_PATH}/include/config.php"

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
  
  message_print_out i "Config and Module Config written"

}

#
# write the config file for updates
# @callfrom function
# @pos029
#
write_config(){
  message_print_out i "write file for future updates"
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
  message_print_out 1 "update informations written (${updpath})"
  chmod -R 600 ${updpath}

}

#
# set all permissions
# @callfrom function main
# @pos031
#
set_permissions(){
  message_print_out i "Set permissions"
  chown -R ${OWNER}:${GROUPOWNER} ${OVPN_FULL_PATH}
  chown -R ${OWNER}:${GROUPOWNER} ${WWWROOT}"/vpn"
  chown ${OWNER}:${GROUPOWNER} ${WWWROOT}"/vpn/conf/server/server.conf"

  chown -R root ${updpath}
  chmod -R 600 ${updpath}
  chown nobody:nobody /etc/openvpn/ccd

  if [ "${OS}" == "centos" ]; then
    sed -i "s/WorkingDirectory=\/etc\/openvpn\/server/WorkingDirectory=\/etc\/openvpn/g" "/usr/lib/systemd/system/openvpn-server@.service"
    systemctl daemon-reload
    chcon -R --reference=/var/www /srv/www
    chcon -t httpd_sys_content_t ${OVPN_FULL_PATH} -R
    chcon -t httpd_sys_rw_content_t ${OVPN_FULL_PATH}/data/ -R
    chcon -t httpd_sys_rw_content_t ${WWWROOT}/vpn/ -R
    chcon -t httpd_sys_rw_content_t ${OVPNSERVERPATH}/server.conf
  fi

  message_print_out d "Setup ready - please read informations!"
}

#
# If the installation was successful
# Displays additional information
# @callfrom function main
# @pos032
#
message_fin(){
  message_print_out 1 "${SETFIN01}"
  message_print_out i "${SETFIN02}"
  message_print_out i "${SETFIN03}"
  message_print_out d "${SETFIN04}"

  if [ -n "${MOD_ENABLE}" ]; then
    message_print_out i "${MOENABLE0}"
    message_print_out i "${MOENABLE1}"
  fi
  datum=$(date '+%Y-%m-%d:%H.%M.%S')
  echo ${datum}": Fin Install - thank you ;o)" >> ${CURRENT_PATH}/loginstall.log
}

#
# create simple firewall-script
# @callfrom function main
# @pos034
#
function create_firewall(){
message_print_out i "create simple firewall"

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
message_print_out 1 "create simple firewall"
}

#
# creates the paths
# @pos35
#
create_dirs(){
  message_print_out i "create directorys, webfolder, files"
  
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
  message_print_out 1 "create directorys, webfolder, files"
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
  # function.sh @pos00
  set_screen_vars

  #
  # functions.sh @pos004
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
  do_select_start_install

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
  # checks if all required programs are installed
  # @pos013
  test_system

  #
  # If MySQL Server is to be installed locally
  # @pos020
  if [[ ${installsql} = "1" ]]; then
    echo "CALL: install.sh set_mysql_rootpw"
    set_mysql_rootpw
  fi

  #
  # Input of all system relevant data
  # @pos019
  give_me_input

  #
  # you take local mysql server, create local database
  # @pos021
  if [[ ${installsql} = "1" ]]; then
    create_database $db_name $mysql_user $mysql_user_pass
  fi
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
  create_openvpn_config_files

  #
  # set Database Permissions
  # @pos025
  create_openvpn_setup

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
  message_fin
}




##
## Main call to setup
## @param none
## @pos000
##
main


## todos for one of the next versions
# replace easy-rsa zip file
#
