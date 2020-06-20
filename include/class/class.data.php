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
        echo session::getvar('isuser');
    #debug($_SESSION,$this->isuser);
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



/*




  if(!isset($_SESSION['admin_id']))
    exit -1;

#  require(dirname(__FILE__) . '/connect.php');
#  require(dirname(__FILE__) . '/functions.php');


  // ---------------- SELECT ----------------
  if(isset($_GET['select'])){

    // Select the users
    if($_GET['select'] == "user"){
      $req = $bdd->prepare('SELECT * FROM user');
      $req->execute();

      if($data = $req->fetch()) {
        do {
          $list[] = array("user_id" => $data['user_id'],
                          "user_pass" => $data['user_pass'],
                          "user_mail" => $data['user_mail'],
                          "user_phone" => $data['user_phone'],
                          "user_online" => $data['user_online'],
                          "user_enable" => $data['user_enable'],
                          "user_start_date" => $data['user_start_date'],
                          "user_end_date" => $data['user_end_date']);
        } while($data = $req->fetch());

        echo json_encode($list);
      }
      // If it is an empty answer, we need to encore an empty json object
      else{
        $list = array();
        echo json_encode($list);
      }
    }

    // Select the logs
    else if($_GET['select'] == "log" && isset($_GET['offset'], $_GET['limit'])){
      $offset = intval($_GET['offset']);
      $limit = intval($_GET['limit']);

      // Creation of the LIMIT for build different pages
      $page = "LIMIT $offset, $limit";

      // ... filtering by the bootstrap table plugin
      $filter = isset($_GET['filter']) ? json_decode($_GET['filter'],true) : []; // this is passed by the bootstrap table filter plugin (if a filter was chosen by the user): these are the concrete set filters with their value
      $where = !empty($filter)?'WHERE TRUE':'';
      $allowed_query_filters = ['user_id', 'log_trusted_ip','log_trusted_port','log_remote_ip','log_remote_port']; // these are valid filters that could be used (defined here for sql security reason)
      $query_filters_existing = [];
      foreach($filter as $unsanitized_filter_key => $unsanitized_filter_val) {
         if(in_array($unsanitized_filter_key, $allowed_query_filters)) { // if this condition does not match: ignore it, because this parameter should not be passed
            // if $unsanitized_filter_key is in array $allowed_query_filters its a valid key and can not be harmful, so it can be considered sanitized
            $where .= " AND $unsanitized_filter_key = ?";
            $query_filters_existing[] = $unsanitized_filter_key;
         }
      }

      // Select the logs
      $req_string = "SELECT *, (SELECT COUNT(*) FROM log $where) AS nb FROM log $where ORDER BY log_id DESC $page";
      $req = $bdd->prepare($req_string);

      // dynamically bind the params
      foreach(array_merge($query_filters_existing,$query_filters_existing) as $i => $query_filter) // array_merge -> duplicated the array contents; this is needed because our where clause is bound two times (in subquery + the outer query)
         $req->bindValue($i+1, $filter[$query_filter]);

      $req->execute();

      $list = array();

      $data = $req->fetch();

      if($data) {
        $nb = $data['nb'];

        do {
          // Better in Kb or Mb
          $received = ($data['log_received'] > 1000000) ? $data['log_received']/1000000 . " Mo" : $data['log_received']/1000 . " Ko";
          $sent = ($data['log_send'] > 1000000) ? $data['log_send']/1000000 . " Mo" : $data['log_send']/1000 . " Ko";

          // We add to the array the new line of logs
          array_push($list, array(
                                  "log_id" => $data['log_id'],
                                  "user_id" => $data['user_id'],
                                  "log_trusted_ip" => $data['log_trusted_ip'],
                                  "log_trusted_port" => $data['log_trusted_port'],
                                  "log_remote_ip" => $data['log_remote_ip'],
                                  "log_remote_port" => $data['log_remote_port'],
                                  "log_start_time" => $data['log_start_time'],
                                  "log_end_time" => $data['log_end_time'],
                                  "log_received" => $received,
                                  "log_send" => $sent));

        } while ($data = $req->fetch());
      }
      else {
        $nb = 0;
      }

      // We finally print the result
      $result = array('total' => intval($nb), 'rows' => $list);

      echo json_encode($result);
    }

    // Select the admins
    else if($_GET['select'] == "admin"){
      $req = $bdd->prepare('SELECT * FROM admin');
      $req->execute();

      if($data = $req->fetch()) {
        do{
          $list[] = array(
                          "admin_id" => $data['admin_id'],
                          "admin_pass" => $data['admin_pass']
                          );
        } while($data = $req->fetch());

        echo json_encode($list);
      }
      else{
        $list = array();
        echo json_encode($list);
      }
    }
  }

  // ---------------- ADD USER ----------------
  else if(isset($_POST['add_user'], $_POST['user_id'], $_POST['user_pass'])){
    // Put some default values
    $id = $_POST['user_id'];
    $pass = hashPass($_POST['user_pass']);
    $mail = "";
    $phone = "";
    $online = 0;
    $enable = 1;
    $start = "null";
    $end = "null";

    $req = $bdd->prepare('INSERT INTO user (user_id, user_pass, user_mail, user_phone, user_online, user_enable, user_start_date, user_end_date)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $req->execute(array($id, $pass, $mail, $phone, $online, $enable, $start, $end));

    $res = array("user_id" => $id,
      "user_pass" => $pass,
      "user_mail" => $mail ,
      "user_phone" => $phone,
      "user_online" => $online,
      "user_enable" => $enable,
      "user_start_date" => $start,
      "user_end_date" => $end
    );

    echo json_encode($res);
  }

  // ---------------- UPDATE USER ----------------
  else if(isset($_POST['set_user'])){
    $valid = array("user_id", "user_pass", "user_mail", "user_phone", "user_enable", "user_start_date", "user_end_date");

    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk = $_POST['pk'];

    if (!isset($field) || !isset($pk) || !in_array($field, $valid)) {
      return;
    }

    if ($field === 'user_pass') {
      $value = hashPass($value);
    }
    else if (($field === 'user_start_date' || $field === 'user_end_date') && $value === '') {
      $value = "null";
    }

    // /!\ SQL injection: field was checked with in_array function
    $req_string = 'UPDATE user SET ' . $field . ' = ? WHERE user_id = ?';
    $req = $bdd->prepare($req_string);
    $req->execute(array($value, $pk));
  }

  // ---------------- REMOVE USER ----------------
  else if(isset($_POST['del_user'], $_POST['del_user_id'])){
    $req = $bdd->prepare('DELETE FROM user WHERE user_id = ?');
    $req->execute(array($_POST['del_user_id']));
  }

  // ---------------- ADD ADMIN ----------------
  else if(isset($_POST['add_admin'], $_POST['admin_id'], $_POST['admin_pass'])){
    $req = $bdd->prepare('INSERT INTO admin(admin_id, admin_pass) VALUES (?, ?)');
    $req->execute(array($_POST['admin_id'], hashPass($_POST['admin_pass'])));
  }

  // ---------------- UPDATE ADMIN ----------------
  else if(isset($_POST['set_admin'])){
    $valid = array("admin_id", "admin_pass");

    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk = $_POST['pk'];

    if (!isset($field) || !isset($pk) || !in_array($field, $valid)) {
      return;
    }

    if ($field === 'admin_pass') {
      $value = hashPass($value);
    }

    $req_string = 'UPDATE admin SET ' . $field . ' = ? WHERE admin_id = ?';
    $req = $bdd->prepare($req_string);
    $req->execute(array($value, $pk));
  }

  // ---------------- REMOVE ADMIN ----------------
  else if(isset($_POST['del_admin'], $_POST['del_admin_id'])){
    $req = $bdd->prepare('DELETE FROM admin WHERE admin_id = ?');
    $req->execute(array($_POST['del_admin_id']));
  }

  // ---------------- UPDATE CONFIG ----------------
  else if(isset($_POST['update_config'])){

      $pathinfo = pathinfo($_POST['config_file']);

      $config_full_uri = $_POST['config_file']; // the complete path to the file, including the file (name) its self and the fully qualified path
      $config_full_path = $pathinfo['dirname']; // path to file (without filename its self)
      $config_name = basename($_POST['config_file']); // config file name only (without path)
      $config_parent_dir = basename($config_full_path); // name of the dir that contains the config file (without path)

      /*
       * create backup for history
       */
/*      if (!file_exists($dir="../$config_full_path/history"))
         mkdir($dir, 0777, true);
      $ts = time();
      copy("../$config_full_uri", "../$config_full_path/history/${ts}_${config_name}");

      /*
       *  write config
       */
/*      $conf_success = file_put_contents('../'.$_POST['config_file'], $_POST['config_content']);

      echo json_encode([
        'debug' => [
            'config_file' => $_POST['config_file'],
            'config_content' => $_POST['config_content']
        ],
        'config_success' => $conf_success !== false,
      ]);
  }

*/

?>
