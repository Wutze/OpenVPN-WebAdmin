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

# debug
#set -x

## Fix Debian 10 Fehler
export PATH=$PATH:/usr/sbin:/sbin
## load main functions
source installation/functions.sh

## set static vars
config="installation/config.conf"
BACKTITLE="OVPN-Admin [UPDATE]"
updpath="/var/lib/ovpn-admin/"
updfile="config.ovpn-admin.upd"
CURRENT_PATH=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
THIS_NEW_VERSION=$(php -r "include('wwwroot/version.php'); echo version;" )


bs_version_update(){
  if [[ -e /etc/debian_version ]]; then
    os="debian"
    os_version=$(grep -oE '[0-9]+' /etc/debian_version | head -1)
    print_out i "Update on:  $os $os_version"
    apt-get update && apt-get upgrade -y
  elif [[ -e /etc/centos-release ]]; then
    os="centos"
    os_version=$(grep -oE '[0-9]+' /etc/centos-release | head -1)
    print_out i "Update on:  $os $os_version"
    yum update
  fi
}

if_updatefile_exist(){
  if [[ -f "${updpath}${updfile}" ]]; then
    # go vars from install.files if exist
    source ${updpath}${updfile}
    # load database pw if exist
    # These variables certainly work
    if [[ -f "/etc/openvpn/scripts/config.sh" ]]; then
      source /etc/openvpn/scripts/config.sh
    fi
    print_out 1 "Setup config loaded"
  else
    # when the update file not exist, you have a older version
    print_out i "Version older than 1.1.0"
    print_out i "load only /etc/openvpn/scripts/config.sh"
    if [[ -f "/etc/openvpn/scripts/config.sh" ]]; then
      source /etc/openvpn/scripts/config.sh
      print_out i "Openvpn Script Config loaded"
    else
      print_out 0 "No openvpn script config found. Is there even a working installation?"
      print_out i "Please read the update.info.md in doc folder!"
      exit;
    fi
  fi
}

# The machine_id is only stored to detect if the system has been
# fundamentally changed at any time. This can be a move to new hardware,
# which usually involves a new installation of the operating system.
# Since usually only the webroot is copied, often no nodejs and yarn are
# installed on the new server. However, since these two packages are
# required, any errors that may occur will be fixed at the same time.

verify_setup(){
  LOCALMACHINEID=$( cat /etc/machine-id )
  if [ -n "$PASS" ]; then
    DBPASS=$PASS
    WEBROOT="/srv/www/"
    BASEPATH="openvpn-admin"
  fi
  if [ -n "$HOST" ]; then DBHOST=$HOST; fi
  if [ -n "$DB" ]; then DBNAME=$DB; fi
  if [ -n "$INSTALLEDVERSION" ]; then VERSION=$INSTALLEDVERSION; fi

  UPDATEINFSUM="
${UPDATEINF02} ↠ ${LOCALMACHINEID}

${UPVERSIO}: ${VERSION}
${NEVERSIO}: ${THIS_NEW_VERSION}
${UPDBHOST}: ${DBHOST}
${UPDBUSER}: ${DBUSER}
${UPDBNAME}: ${DBNAME}
${UPDBPASS}: ${DBPASS}
${UPWEBDIR}: ${BASEPATH}
${UPWEBROO}: ${WEBROOT}
${UPPATHOW}: ${WWWOWNER}
${UPMASHID}: ${MACHINEID}
${INSTALLD}: ${INSTALLDATE}

${UPDATAOK}
"
  
  sel=$(whiptail --backtitle "${BACKTITLE}" --title "${UPSEL00}" --yesno "${UPDATEINFSUM}" ${r} ${c} 3>&1 1>&2 2>&3)
  
  if [ $? = 0 ]; then
      print_out 1 "Update: Inputs ok"
      fix_error_1
  else
      print_out i "Update: get inputs"
      setup_questions
  fi
}

create_setup_new_user(){
  ADMIN=$(whiptail --backtitle "${BACKTITLE}" --inputbox "${SETVPN08}" ${r} ${c} --title "${SETVPN08}" 3>&1 1>&2 2>&3)
  control_box $? "new Admin"
  ADMINPW=$(whiptail --backtitle "${BACKTITLE}" --inputbox "${SETVPN09}" ${r} ${c} --title "${SETVPN09}" 3>&1 1>&2 2>&3)
  control_box $? "new Admin Password"
  ## all Users now User, not Admin!
  mysql -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME -e "UPDATE user SET gid = '2'; "
  control_script "set all Users to Group User"
  print_out 1 "All Users now Group User"
  mysql -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME -e "INSERT INTO user (user_name, user_pass, gid, user_enable) VALUES ('${ADMIN}', encrypt('${ADMINPW}'),'1','1');"
  control_script "Insert new Webadmin"
  mysql -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME -e "INSERT INTO user (user_name, user_pass, gid, user_enable) VALUES ('${ADMIN}-user', encrypt('${ADMINPW}'),'2','1');"
  control_script "Insert new User"
  print_out 1 "setting up MySQL OK"
  print_out i "Admin-Login now with $ADMIN and our new Password!"
  print_out i "Control and reconfigure all users after the update!"
}

#
# create the backup from database and webfiles
#
make_backup(){
  if [[ -d "/opt/ovpn-backup/" ]]; then
    print_out 1 "backup path exist"
  else
    mkdir /opt/ovpn-backup/
    control_script "mkdir backup path"
    print_out 1 "backup path created"
  fi

  date=$(date '+%Y-%m-%d')
  tar cfz /opt/ovpn-backup/$date-archiv.tar.gz --exclude=node_modules --exclude=ADOdb $WEBROOT$BASEPATH
  control_script "create tar"
  print_out 1 "Backup Webfolder ok"
  cp $WEBROOT$BASEPATH/include/config.php /opt/ovpn-backup/$date-config.php
  cp -R $WEBROOT$BASEPATH/data /opt/ovpn-backup/$date-data
  
  print_out i "Insert Password MySQL Database!"
  mysqldump --opt -Q -u $DBUSER -p$DBPASS -h $DBHOST $DBNAME > /opt/ovpn-backup/$date-dump.sql
  control_script "create db dump"
  print_out 1 "Backup Database ok"
  
}

## Fixed a bug in the installation script that saved the wrong BASEPATH of the Webroot (up to version 1.1.1)
fix_error_1(){
  if [[ ! -d "$WEBROOT$BASEPATH" ]]; then
    BASEPATH=$(whiptail --backtitle "${BACKTITLE}" --inputbox "${SETVPN12}" ${r} ${c} openvpn-admin --title "${SETVPN12}" 3>&1 1>&2 2>&3)
    control_box $? "fix error Web-Basepath to $BASEPATH"
  fi
}

setup_questions(){

  DBHOST=$(whiptail --backtitle "${BACKTITLE}" --inputbox "${SETVPN04}" ${r} ${c} ${DBHOST} --title "DB Host" 3>&1 1>&2 2>&3)
  control_box $? "DB-Host"
  DBNAME=$(whiptail --backtitle "${BACKTITLE}" --inputbox "${SETVPN10}" ${r} ${c} ${DBNAME} --title "DB Name" 3>&1 1>&2 2>&3)
  control_box $? "DB-Name"
  DBUSER=$(whiptail --backtitle "${BACKTITLE}" --inputbox "${SETVPN06}" ${r} ${c} ${DBUSER} --title "DB-User" 3>&1 1>&2 2>&3)
  control_box $? "MySQL Username"
  DBPASS=$(whiptail --backtitle "${BACKTITLE}" --inputbox "${SETVPN07}" ${r} ${c} ${DBPASS} --title "DB Password" 3>&1 1>&2 2>&3)
  control_box $? "MySQL User PW"
  WEBROOT=$(whiptail --backtitle "${BACKTITLE}" --inputbox "${SETVPN11}" ${r} ${c} ${WEBROOT} --title "${SETVPN11}" 3>&1 1>&2 2>&3)
  control_box $? "Web-Root"
  BASEPATH=$(whiptail --backtitle "${BACKTITLE}" --inputbox "${SETVPN12}" ${r} ${c} ${BASEPATH} --title "${SETVPN12}" 3>&1 1>&2 2>&3)
  control_box $? "Web-Basepath"

  verify_setup
  
  if [[ $0 == 0 ]]; then
    setup_options
  fi
}

## deprecated
start_update_new_version(){
  openvpn_admin=$WEBROOT$BASEPATH
  # wenn alte Version - vor 1.1.0 - dann lösche das alte Verzeichnis
  # es wird neu angelegt
  rm -r $openvpn_admin
  print_out 1 "delete old Webfolder"
  mkdir $openvpn_admin
  control_script "create new Webfolder"

  cp -r "$CURRENT_PATH/wwwroot/"{index.php,favicon.ico,package.json,js,include,css,images,data} "$openvpn_admin"
  control_script "install new files"
  print_out i "Install third party module yarn"
  cd $openvpn_admin
  yarn install
  control_script "yarn install"
  print_out i "Install third party module ADOdb"
  git clone https://github.com/ADOdb/ADOdb ./include/ADOdb
  control_script "ADODb install"
  chown -R www-data $openvpn_admin
  control_script "Set access rights webfolder"
  print_out 1 "Set access rights webfolder"
  if [[ -f "$CURRENT_PATH/installation/sql/$THIS_NEW_VERSION-ovpnadmin.update.sql" ]]; then
    mysql -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME < $CURRENT_PATH/sql/$THIS_NEW_VERSION-ovpnadmin.update.sql
    control_script "execute Database Updates"
    mysql -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME < $CURRENT_PATH/sql/adodb.sql
    print_out 1 "Update Database ok"
    create_setup_new_user
  else
    print_out i "no changes to the database necessary"
  fi

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
 * @version		$THIS_NEW_VERSION
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos(\$_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');"
  echo ""
  echo ""
  echo "\$dbhost=\"$DBHOST\";"
  echo "\$dbuser=\"$DBUSER\";"
  echo "\$dbname=\"$DBNAME\";"
  echo "\$dbport=\"3306\";"
  echo "\$dbpass=\"$DBPASS\";"
  echo "\$dbtype=\"mysqli\";"
  echo "\$dbdebug=FALSE;"
  echo "\$sessdebug=FALSE;"

  echo "/* Site-Name */
define('_SITE_NAME',\"OVPN-WebAdmin\");
define('HOME_URL',\"vpn.home\");
define('_DEFAULT_LANGUAGE','en_EN');

/** Login Site */
define('_LOGINSITE','login1');

/** 
 * only for development!
 * please comment out if no longer needed!
 * comment out the \"define function\" to enable
 */
#define('dev','dev/dev.php');
if (defined('dev')){
	include_once('dev/class.dev.php');
}"

  }> $WEBROOT$BASEPATH"/include/config.php"
  control_script "create new config.php"
  print_out 1 "create new config.php"

}

start_update_normal(){
  openvpn_admin=$WEBROOT$BASEPATH
  # simply delete the web directory to keep it clean
  rm -r $openvpn_admin
  print_out 1 "delete old Webfolder"
  mkdir $openvpn_admin
  control_script "create new Webfolder"

  if [ -n "$modules_dev" ] || [ -n "$modules_all" ]; then
    cp -r "$CURRENT_PATH/wwwroot/"{index.php,version.php,favicon.ico,js,include,css,images,data,dev} "$openvpn_admin"
  else
    cp -r "$CURRENT_PATH/wwwroot/"{index.php,version.php,favicon.ico,js,include,css,images,data} "$openvpn_admin"
  fi
  if [[ ! -d  $WEBROOT"ovpn_modules/" ]]; then
    mkdir $WEBROOT"ovpn_modules/"
  fi
  cp "$CURRENT_PATH/wwwroot/package.json" $WEBROOT"ovpn_modules/"
  cp -r "$CURRENT_PATH/installation/scripts/"{connect.sh,disconnect.sh,login.sh} "/etc/openvpn/scripts/"
  ## move all history folders and osx folder
  cd $WEBROOT
  if [[ ! -d  "vpn/history/osx" ]]; then
    ## rename osx folder
    mv vpn/history/osx-viscosity/ vpn/history/osx
    mv vpn/conf/osx-viscosity/ vpn/conf/osx
    ## move history files
    if [[ -d  "vpn/history/osx" ]]; then
    cp vpn/history/osx/history/* vpn/history/osx/
    rm -r vpn/history/osx/history/
    fi
    if [[ ! -d  "vpn/history/windows" ]]; then
    cp vpn/history/windows/history/* vpn/history/windows/
    rm -r vpn/history/windows/history/
    fi
    if [[ ! -d  "vpn/history/gnu-linux" ]]; then
    cp vpn/history/gnu-linux/history/* vpn/history/gnu-linux/
    rm -r vpn/history/gnu-linux/history/
    fi
    if [[ ! -d  "vpn/history/server" ]]; then
    cp vpn/history/server/history/* vpn/history/server/
    rm -r vpn/history/server/history/
    fi
  fi
  
  if [[ ! -d  "vpn/history/firewall" ]]; then
    mkdir vpn/history/firewall
  fi

  control_script "renew Files"
  print_out i "Update third party module yarn"
  cd $WEBROOT"ovpn_modules/"
  yarn install
  control_script "yarn update"
  print_out i "Update third party module ADOdb"
  if [[ ! -d  $WEBROOT"ovpn_modules/ADOdb" ]]; then
    git clone https://github.com/ADOdb/ADOdb ADOdb
  else
    cd ADOdb/
    git pull
  fi
  control_script "ADODb update"

  ln -s $WEBROOT"ovpn_modules/ADOdb" $openvpn_admin"/include/ADOdb"
  ln -s $WEBROOT"ovpn_modules/node_modules" $openvpn_admin"/node_modules"

  print_out i "Update SQL"
  if [[ -f "$CURRENT_PATH/installation/sql/$THIS_NEW_VERSION-ovpnadmin.update.sql" ]]; then
    mysql -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME < $CURRENT_PATH/installation/sql/$THIS_NEW_VERSION-ovpnadmin.update.sql
    control_script "execute Database Updates" 
  else
    print_out i "no changes to the database necessary"
  fi
}

check_version(){
  if [ -n "$INSTALLEDVERSION" ]; then VERSION=$INSTALLEDVERSION; fi
  if [ "$(printf '%s\n' "$THIS_NEW_VERSION" "$VERSION" | sort -V | head -n1)" = "$THIS_NEW_VERSION" ]; then 
    print_out i "Installed Version $VERSION greater than or equal to $THIS_NEW_VERSION"
    print_out d "Update is not required"
    exit
  else
    ## Special version due to renaming of several variables
    if [ "$(printf '%s\n' "$THIS_NEW_VERSION" "$VERSION" | sort -V | head -n1)" = "1.1.0"  ]; then
      print_out i "Installed Version $VERSION, this should be installed: $THIS_NEW_VERSION"
      print_out i "Update is required"
      V2=2
      do_select
      #control_box "Set Development"
      return
    fi
    do_select
    return
  fi
}

startDialog(){
  sel=$(whiptail --backtitle "${BACKTITLE}" --title "${UPSEL00}" --yesno "${UPDATEINF01}" ${r} ${c} 3>&1 1>&2 2>&3)
  control_box $? "${UPSEL00}"
}

write_config(){
  if [[ ! -d  "${updpath}" ]]; then
    mkdir $updpath
  fi

  {
  echo "VERSION=\"$THIS_NEW_VERSION\""
  echo "DBHOST=\"$DBHOST\""
  echo "DBUSER=\"$DBUSER\""
  echo "DBNAME=\"$DBNAME\""
  echo "BASEPATH=\"$BASEPATH\""
  echo "WEBROOT=\"$WEBROOT\""
  echo "WWWOWNER=\"www-data\""
  echo "### Is it still the original installed system?"
  echo "MACHINEID=$LOCALMACHINEID"
  echo "INSTALLDATE=\"$(date '+%Y-%m-%d %H:%M:%S')\""
  }> $updpath$updfile

  control_box $? "write config"
  chmod -R 700 $updpath
}

## Since version 1.1.1 new naming convention
function rename_vars(){
  sed -i "s/\$host/\$dbhost/" "./include/config.php"
  sed -i "s/\$port/\$dbport/" "./include/config.php"
  sed -i "s/\$db/\$dbname/" "./include/config.php"
  sed -i "s/\$user/\$dbuser/" "./include/config.php"
  sed -i "s/\$pass/\$dbpass/" "./include/config.php"


  mv vpn/history/osx-viscosity/ vpn/history/osx

}

install_version_2(){
  V2="YES"

}


do_select(){
	sel=$(whiptail --title "${SELECT_A}" --checklist --separate-output "${SELECT_B}:" ${r} ${c} ${h} \
    "11" "${SELECT11} " off \
    "12" "${SELECT12} " off \
    "13" "${SELECT13} " off \
    "20" "${SELECT20} " off \
    3>&1 1>&2 2>&3)
  control_box $? "select"

  while read -r line;
  do
      case $line in
          11) modules_dev="1"
              MOD_ENABLE="1"
          ;;
          12) modules_firewall="1"
              MOD_ENABLE="1"
          ;;
          13) modules_clientdownload="1"
              MOD_ENABLE="1"
          ;;
          20) modules_all="1"
              MOD_ENABLE="1"
          ;;
          *)
          ;;
      esac
  done < <(echo "$sel")
}

write_webconfig(){

cp /opt/ovpn-backup/$date-config.php $WEBROOT$BASEPATH/include/config.php
control_script "Copy web.config.php"
cp -R /opt/ovpn-backup/$date-data $WEBROOT$BASEPATH/data
control_script "Copy data folder"

if [ -n "$modules_dev" ] || [ -n "$modules_all" ]; then
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
" >> $WEBROOT$BASEPATH"/include/module.config.php"
MOD_ENABLE="1"
fi

if [ -n "$modules_firewall" ] || [ -n "$modules_all" ]; then
  echo "
define('firewall',TRUE);
" >> $WEBROOT$BASEPATH"/include/module.config.php"
MOD_ENABLE="1"
fi

print_out i "Config and Module Config written"
}



## first information to update
# you must say yes to continue!
# all other inputs will break this script
main(){

  # select language german or english
  sel_lang  
  # main logo
  intro
  if_updatefile_exist
  check_version

  # first dialog for informations
  startDialog

  verify_setup

  ## create backup files and database
  print_out i "Backup - this may take a little moment"
  make_backup
  
  # System Update
  bs_version_update

  ## make update files and database
  if [ -n "$VERSION" ]; then
    start_update_normal
  else
    start_update_new_version
  fi

  write_config
  write_webconfig
  print_out 1 "Configs written"
  chown -R "$WWWOWNER:$WWWOWNER" "$WEBROOT$BASEPATH"
  chown -R "$WWWOWNER:$WWWOWNER" $WEBROOT/vpn
  print_out 1 "set file rights"
}

### Start Script

main


### finish script and call messages
print_out d "Yeahh! Update ready. 【ツ】"
print_out i "Have Fun!"
print_out i "${SETFIN04}"
print_out i "${AUPDATE01}"

if [ -n "$MOD_ENABLE" ]; then
  print_out i "${MOENABLE0}"
  print_out i "${MOENABLE1}"
fi


exit


### Hinweise
## 
