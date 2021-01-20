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
#set -x

###############
# Only for Functions to use setup AND update
###############

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
echo ${datum}": Start Install" >> ${CURRENT_PATH}/loginstall.log
}

#
# init screen
# Find the rows and columns will default to 80x24 if it can not be detected
#
set_screen_vars(){
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
}

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
      message_print_out 1 "Execution Ok: ${2}"
  else
      message_print_out 0 "Execution break: ${2}"
      exit
  fi
}

#
# Errors in the script are intercepted and displayed here
# @Call After executing a command: control_script_message "description"
# @param $? + Description
# @return continue script or or exit when error with exit 100
# @see additional description @pos100
# @pos002
#  
control_script_message(){
  if [ ! $? -eq 0 ]
  then
  message_print_out 0 "Error ${1} "
  exit 100
  fi
}

#
# formats the notes and messages in an appealing form
# @param [1|0|i|d|r] [Text]
# @example: message_print_out 1 "your text"
# @return formated Text with "0" red cross, "1" green tick, "i"nfo, "d"one Message or need input with "r"ead
# @see additional description @pos100
# @pos003
#  
message_print_out(){
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
    r)  read -rsp " ${2}"
    echo "【ツ】"
    ;;
  esac
  datum=$(date '+%Y-%m-%d:%H.%M.%S')
  echo ${datum}": "${2} >> ${CURRENT_PATH}/loginstall.log
}

### Additional Description
# @pos100
# The two functions should be used in combination.
# "message_print_out" should indicate where the script is and what it wants to do,
# "control_script_message" or "control_box" should then indicate the completion of the action,
# whether it was successful or no
###

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
    message_print_out 1 "${str}"
  else
    message_print_out 0 "${str}"
    message_print_out i "${COL_LIGHT_RED}${USER01}${COL_NC}"
    message_print_out i "${USER02}"
    message_print_out 0 "${USER03}"
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
    message_print_out i "Install on:  ${OS} ${OSVERSION}"
    # Fix Debian 10 Fehler
    export PATH=$PATH:/usr/sbin:/sbin
  elif [[ -e /etc/centos-release ]]; then
    OS="centos"
    OSVERSION=$(grep -oE '[0-9]+' /etc/centos-release | head -1)
    message_print_out i "Install on:  ${OS} ${OSVERSION}"
  else
    message_print_out 0 "No suitable operating system found, sorry"
    message_print_out 0 ${BREAK}
    exit
  fi
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
  message_print_out i "Select Language"
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
    message_print_out 0 "Exit select language"
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
    message_print_out 1 "Set Language to: ${var1}"
  else
    message_print_out 1 "Set Language to: ${var2}"
  fi
}
