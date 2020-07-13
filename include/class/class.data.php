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
 * @version		1.0.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');


/**
 * get dynamic Data from Database with bootstrap, further only include/grid.php
 * Calling the class is only possible via index.php?op= in logged in state via class.request.php
 *
 * Call Class with;
 * $getdata = new data;
 * $getdata->set_value('data',$_REQUEST['select']);
 * $getdata->set_value('req',$_REQUEST);
 * $getdata->main();
 *
 * the main function allows only defined calls - see var $go
 *
 * @return json formated data from request log|user|admin for bootstrap, jquery or other
 */

class godata{
  var $go = array(
                  'log'=>'log',
                  'user'=>'user',
                  'admin'=>'admin'
                );
  var $offset = '/[0-9]*/m';
  var $limit = '/[0-9]*/m';
  var $allowedchars = '/^[a-z0-9\_\-]*$/';
  var $allowed_query_filters = ['user_id', 'log_trusted_ip','log_trusted_port','log_remote_ip','log_remote_port'];

  function main(){
    (array_key_exists($this->action,$this->go)) ? $this->gotox = $this->go[$this->action] : $this->gotox = 'ERROR';
    (session::getvar('isuser')) ? '' : $this->gotox = 'ERROR';
    switch($this->gotox){
      case "log";
        $out = self::read_log();
      break;
      case "user";
        $out = self::read_user();

      break;
      case "admin";
        $o = new jsonObject;
        $o->id   = "0";
        $o->text = "deprecated";
        $rows[]  = $o;
        print json_encode($rows);
      break;
      case "ERROR";
        Session::Destroy();
        header("Location: /?op=error");
      break;
    }
  }

  /**
   * make sql query for log and create json data
   * distinguishes between admin and user and displays the corresponding data
   * 
   * @return json Data for bootstrap log table
   */
  function read_log(){
    $data = newAdoConnection(_DB_TYPE);
    $data->connect(_DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB);
    /** set dynamic filters and create query */
    $page = "LIMIT ".$this->req['offset'].",".$this->req['limit']."";
    $filter = isset($this->req['filter']) ? json_decode($this->req['filter'],true) : [];
    ($this->req['search']) ? $where = " WHERE user_id like '%".$this->req['search']."%'" : $where = "";
    /** create sort */
    foreach(array_keys($filter) as $key){
      (array_key_exists($key,$filter)) ? $where .= " AND ".$key." = '".$filter[$key]."'" : "";
    }
    if(!(int)Session::GetVar('isadmin')){
      if(!empty($where)){
        $where = $where.' AND user_id = \''.Session::GetVar('uname').'\'';
      }else{
        $where = 'WHERE user_id = \''.Session::GetVar('uname').'\'';
      }
    }
    /** make query */
    $sql = "SELECT log.*, (SELECT COUNT(*) FROM log AS counter $where) AS nb FROM log $where ORDER BY log_id DESC $page";
    /** execute query */
    $result = $data->execute($sql);


      /** create json from database data for bootstrap table */
      while ($r = $result->fetchRow())
      {
        $o = new jsonObject;
        $o->id   = "0";
        $o->text = $sql;
        $o->log_id   = $r[0];
        $o->user_id = $r[1];
        $o->log_trusted_ip = $r[2];
        $o->log_trusted_port = $r[3];
        $o->log_remote_ip = $r[4];
        $o->log_remote_port = $r[5];
        $o->log_start_time = $r[6];
        $o->log_end_time = $r[7];
        $o->log_received = ($r[8] < 1000000)? round($r[8]/1000,2,PHP_ROUND_HALF_UP) ." KB" : round($r[8]/1000000,2,PHP_ROUND_HALF_UP) ." MB" ;
        $o->log_send = ($r[9] < 1000000)? round($r[9]/1000,2,PHP_ROUND_HALF_UP) ." KB" : round($r[9]/1000000,2,PHP_ROUND_HALF_UP) ." MB" ;
        $o->nb = $r[10];
        $rows['rows'][]  = $o;
      }

    /**
     * fügt dem übergebenen Array eine Zeile oben an mit dem Inhalt der Summe der Zeilen
     * und verschiebt die relevanten Inhalte eine Ebene höher
     * total: nn
     * rows:[]
     *    0:objects ....
     */
    $js = array('total' => intval($o->nb), 'rows' => $rows );
    $ergebnis = array_merge($js, $rows);
    print json_encode($ergebnis);

  }

  /**
   * Anzeige der User im Adminbereich
   * @return json Data for bootstrap user table
   */
  function read_user(){
    $data = newAdoConnection(_DB_TYPE);
    $page = "LIMIT ".$this->req['offset'].",".$this->req['limit']."";
    ($this->req['search']) ? $where = " WHERE user_id like '%".$this->req['search']."%'" : $where = "";
    $data->connect(_DB_SERVER, _DB_UNAME, _DB_PW, _DB_DB);
    $sql = "SELECT COUNT( uid ) AS nb FROM user AS user $where";
    $nbv = $data->execute($sql);
    $nb = $nbv->fetchRow();
    $sql = "SELECT user.uid AS uid,
            user.user_id AS uname,
            groupnames.name AS gname,
            user.user_online,
            user.user_enable,
            user.user_start_date,
            user.user_end_date,
            user.user_mail
            FROM { oj groupnames AS groupnames
            LEFT OUTER JOIN user AS user
            ON groupnames.gname = user.gid } $where $page" ;
    $result = $data->execute($sql);

    while ($r = $result->fetchRow()){
        $o = new jsonObject;
        $o->id   = "0";
        $o->text = $sql;
        $o->uid   = '<a class="" id="'. $r[0] .'" href="#"><i class="fas fa-edit"></i></a>';
        $o->uuid = $r[0];
        $o->uname = $r[1];
        $o->gname = $r[2];
        #$o->user_online = $r[3];
        ($r[3]) ? $o->user_online='<div class="mini-led-green-blink"></div>' : $o->user_online='<div class="mini-led-gray"></div>';
        ($r[4]) ? $o->user_enable='<div class="mini-led-green"></div>' : $o->user_enable='<div class="mini-led-red"></div>';
        $o->enable = $r[4];
        $o->user_start_date = $r[5];
        $o->user_end_date = $r[6];
        $o->mail = $r[7];
        $rows['rows'][]  = $o;
    }

    $js = array('total' => intval($nb[0]), 'rows' => $rows );
    $ergebnis = array_merge($js, $rows);
    print json_encode($ergebnis);

  }




  /**
  * set value
  * @return defined vars for this class
  */
  function set_value($key, $val){
      $this->$key = $val;
  }


}


?>
