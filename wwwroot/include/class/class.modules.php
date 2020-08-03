<?php
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
 * @version		1.2.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

 (stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');


/** This class is for language modules and modules */
class get_modules{
  var $path_modules=REAL_BASE_DIR."/include/html/modules/";
  var $path_languages=REAL_BASE_DIR."/include/lang/";


  /** function read modules dir and load modules */
  function search_mod_dir() {
    $this->loaddir = array();
    $moddir = scandir($this->path_modules);
    foreach ($moddir as $key => $value){
      if (!in_array($value,array(".",".."))){
        if (is_dir($this->path_modules . DIRECTORY_SEPARATOR . $value)){
          $this->loaddir[$value] = $value;
        }
      }
    }
    return;
  }

  /** function read languages dir and load language */
  function search_lang_dir() {
    $this->loaddir = array();
    $langdir = scandir($this->path_languages);
    foreach ($langdir as $key => $value){
      if (!in_array($value,array(".",".."))){
        if (is_dir($this->path_languages . DIRECTORY_SEPARATOR . $value)){
          $this->loaddir[$value] = $value;
        }
      }
    }
    return;
  }

  /**
  * set value
  * @return defined vars for this class
  */
  function set_value($key, $val){
    $this->$key = $val;
  }

  /** search and print module folder */
  function load_modules(){
    $this->search_mod_dir();
    return $this;
  }

  /** search and print language folder */
  function load_languages(){
    $this->search_lang_dir();
    return $this;
  }
}
