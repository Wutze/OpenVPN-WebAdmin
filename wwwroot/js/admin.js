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

$(function () {
  "use strict";

  $("#ExtendUserModal").on("show.bs.modal", function(e) {
    var link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
  });

  window.printStatus = function(msg, alert_type='warning', bootstrap_icon='') {
     $('#message-stage').empty()
         .append(
            $(document.createElement('div'))
            .addClass('alert alert-'+alert_type)
            .html(bootstrap_icon?'<i class="stauts-icon glyphicon glyphicon-'+bootstrap_icon+'"></i>':'')
            .append(msg)
            .hide().fadeIn().delay(2000).fadeOut()
         );
  }

  function onAjaxError (xhr, textStatus, error) {
    console.error(error);
    console.log('Saving to the db' + xhr);
    alert('Error: ' + textStatus);
  }

  function refreshTable($table) {
    $table.bootstrapTable('refresh');
  }

  // watch the config textareas for changes an persist them if a change was made
  $('textarea').keyup(function(){
     $('#save-config-btn').removeClass('saved-success hidden').addClass('get-attention');
  }).change(function(){
      updateConfig($(this).data('config-file'), $(this).val());
      $('#save-config-btn').removeClass('get-attention').addClass('saved-success');
  });

  /** save the config file and post success flying-message */
  function updateConfig(config_file, config_content) {
    $.ajax({
      url: '?op=savefile',
      data: {
        update_config : true,
        config_file: config_file,
        config_content: config_content
      },
      success : function(res){
        console.log('Files saved?: ' + res.config_success);
        filesaveok();
      },
      dataType : 'json',
      method: 'POST',
      error: function(res){
        filesaveer();
      },
    });
  }

  /** flying messages */
  function filesaveok() {
    var x = document.getElementById("messagestage");
    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
  }
  function filesaveer() {
    var x = document.getElementById("messagestageer");
    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
  }
} );

/**
 * Reads json data and displays it
 * ! The script starts too late. Why?
 */
setInterval(function(){
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var myArr = JSON.parse(this.responseText);
      document.getElementById("load").innerHTML = myArr['load'];
      document.getElementById("disk").innerHTML = "total: " + myArr['disk'][0] + " | free: " + myArr['disk'][1] + " | used: " + myArr['disk'][2];
      document.getElementById("users").innerHTML = "Users: " + myArr['user']['user'] + " | Online: " + myArr['user']['online'];
      document.getElementById("error").innerHTML = myArr['error'];
      document.getElementById("cpu2").innerHTML = "CPU: " + myArr['cpu'] + "%";
      cpu.style.width = myArr['cpu'] + "%";
      document.getElementById("ram_free2").innerHTML = "free: " + myArr['ram_free'] + "%";
      ram_free.style.width = myArr['ram_free'] + "%";
      document.getElementById("ram_used2").innerHTML = "used: " + myArr['ram_used'] + "%";
      ram_used.style.width = myArr['ram_used'] + "%";
    }
  };
  xmlhttp.open("GET", "/?op=live&go=load", true);
  xmlhttp.send(); 
}, 500000);


/**
 * create Code for Userchanges in Admin Section "List User"
 * create modal button for extended edit user
 */
function userdetails(index, row) {
  var session_id = ""
  var usersmail = '<div class="form-group row">' +
                    '<div class="custom-control col-md-6">'+
                      '<input type="text" class="form-control is-warning" name="mail" value="' + ((row['mail']) ? row['mail']: '') + '" placeholder="Set Mail">'+
                    '</div>' +
                    '<div class="custom-control col-md-6">' +
                      '<input type="password" class="form-control is-warning" name="pass" value="" placeholder="New Password">'+
                    '</div>'+
                  '</div>'
  var usersmailbox =  '<div class="col-md-6 col-12">'+
                        '' + usersmail + '' +
                      '</div>'
  var isuser = '<div class="form-group">' +
                  '<div class="custom-control custom-switch">'+
                    '<input type="checkbox" ' + ((row['enable']==="1")?'checked':'') + ' class="custom-control-input" name="isuser" id="isuserSwitch-' + row['uuid'] + '">'+
                    '<label class="custom-control-label" for="isuserSwitch-' + row['uuid'] + '"></label>'+
                  '</div>'+
                '</div>'
  var isuserbox = '<div class="col-lg-3 col-12">'+
                    '<div class="small-box bg">'+
                        'User enable: '+ isuser + '' +
                    '</div>'+
                  '</div>'

  var isadmin = '<div class="form-group">' +
                  '<div class="custom-control custom-switch">'+
                    '<input type="checkbox" ' + ((row['gname']==='admin')?'checked':'') + ' class="custom-control-input" name="makeadmin" id="adminSwitch-' + row['uuid'] + '">'+
                    '<label class="custom-control-label" for="adminSwitch-' + row['uuid'] + '"></label>'+
                  '</div>'+
                '</div>'
  var userinfobox = '<div class="col-lg-3 col-12">'+
                      '<div class="small-box bg">'+
                          'Admin? ' + isadmin + '' +
                      '</div>'+
                    '</div>'

  var html = []
    html.push('<form role="form" action="/" method="post" data-index="uu-' + row['uuid'] + '"><div class="row">' + 
              '' + userinfobox + ''+
              '' + isuserbox + ''+
              '' + usersmailbox + ''+
              '</div>' +
              '<input type="hidden" name="uid" value="' + row['uuid'] + '">' +
              '<input type="hidden" name="uname" value="' + row['uname'] + '">' +
              '<input type="hidden" name="session" value="' + session_id + '">' +
              '<input type="hidden" name="op" value="saveuserchanges">' +
              '<button type="submit" class="btn btn-app bg-warning" name="make" value="update"><i class="fa fa-save"></i>Update</button>' +
              '<button type="submit" class="btn btn-app bg-danger" name="make" value="delete"><i class="fa fa-trash"></i>Delete</button>' 
              ),
    html.push(new_userdetails(row['uuid'])),
    html.push('</form>'),
    html.push(getuserdata(row['uuid']))
  return html.join('')
}

/**
 * Set modal button
 * @param {*} row userid
 */
function new_userdetails(uuid) {
  var html = '' +
  '<button type="button" class="btn btn-app bg-cemetery" data-target="#modal" data-toggle="modal" data-uuid="' + uuid + '">' +
    '<i class="fas fa-user-edit"></i>Details' +
  '</button>'
  return html
}

/**
 * get userid from modal button "details" in user table
 * @param {int} uuid
 */
function getuserdata(uuid){
  var xhttp = new XMLHttpRequest();
  xhttp.open("GET", "?op=loadmodul&modname=user&go=getuserdata&uid=" + uuid, true);
  xhttp.send();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var myArr = JSON.parse(this.responseText);
      document.getElementById("uuid").innerHTML = myArr['user']['uid'];
      document.getElementById("username").innerHTML = myArr['user']['user_name'];
      document.getElementById("datepicker3").value = myArr['user']['user_start_date'];
      document.getElementById("datepicker4").value = myArr['user']['user_end_date'];
      document.getElementById("lastlogin").innerHTML = myArr['last']['log_start_time'];
      document.getElementById("logins").innerHTML = myArr['last']['anz'];
    }
  };
};


