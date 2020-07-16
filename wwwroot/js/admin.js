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

$(function () {
  "use strict";

  //var gridsUrl = '/';

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


/* was cooles zum testen
var timeoutId;
$('#save-form').on('input propertychange change', function() {
    console.log('Textarea Change');

    clearTimeout(timeoutId);
    timeoutId = setTimeout(function() {
        // Runs 1 second (1000 ms) after the last change
        saveToDB();
    }, 1000);
});

function saveToDB()
{
    console.log('Saving to the db');

    // Now show them we saved and when we did
    var d = new Date();
    $('.form-status-holder').html('Saved! Last: ' + d.toLocaleTimeString());
}
*/


  // watch the config textareas for changes an persist them if a change was made
  $('textarea').keyup(function(){
     $('#save-config-btn').removeClass('saved-success hidden').addClass('get-attention');
  }).change(function(){
      updateConfig($(this).data('config-file'), $(this).val());
      $('#save-config-btn').removeClass('get-attention').addClass('saved-success');
  });




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
         document.getElementById("message-stage").innerHTML = "<p class=\"bg-success text-white\">File Saved</p>";
      },
      dataType : 'json',
      method: 'POST',
      error: function(res){
        document.getElementById("message-stage").innerHTML = "<p class=\"bg-danger text-white\">Error</p>";
      },
    });
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
    }
  };
  xmlhttp.open("GET", "/?op=live&go=load", true);
  xmlhttp.send(); 
}, 5000);


/**
 * create Code for Userchanges in Admin Section "List User"
 * ! not function - start/end dates !
 * ! I don't know, I can't find the error of thinking
 */
function userdetails(index, row) {
  var session_id = ""
  var usersmail = '<div class="form-group">' +
                '<div class="custom-control custom-switch">'+
                  '<input type="text" class="form-control is-warning" name="mail" value="' + ((row['mail']) ? row['mail']: '') + '" placeholder="Mail">'+
                  '<input type="password" class="form-control is-warning" name="pass" value="" placeholder="new Password">'+
                '</div>'+
              '</div>'
  var usersmailbox = '<div class="col-lg-3 col-12">'+
              '<div class="small-box bg">'+
                '<div class="inner">'+
                  '<h5>Userdata</h5>'+
                  '' + usersmail + '' +
                '</div>'+
              '</div>'+
            '</div>'
  var usersdate = '<div class="form-group">' +
                '<div class="custom-control custom-switch">'+
                  '<input type="text" class="form-control is-warning" name="fromdate" id="datepickerA-' + row['uuid'] + '" name="fromdate" value="' + ((row['user_start_date']) ? row['user_start_date']: '') + '" disabled="" placeholder="from ...">'+
                  '<input type="text" class="form-control is-warning" name="todate" id="datepickerB-' + row['uuid'] + '" name="todate" value="' + ((row['user_end_date']) ? row['user_end_date']: '') + '" disabled="" placeholder="to ...">'+
                '</div>'+
              '</div>'
  var usersdatebox = '<div class="col-lg-3 col-12">'+
              '<div class="small-box bg">'+
                '<div class="inner">'+
                  '<h5>Time Limit</h5>'+
                  '' + usersdate + '' +
                '</div>'+
              '</div>'+
            '</div>'

  var isuser = '<div class="form-group">' +
                    '<div class="custom-control custom-switch">'+
                      '<input type="checkbox" ' + ((row['enable']==="1")?'checked':'') + ' class="custom-control-input" name="isuser" id="isuserSwitch-' + row['uuid'] + '">'+
                      '<label class="custom-control-label" for="isuserSwitch-' + row['uuid'] + '"></label>'+
                    '</div>'+
                  '</div>'
  var isuserbox = '<div class="col-lg-3 col-12">'+
                  '<div class="small-box bg">'+
                    '<div class="inner">'+
                      '<h5>User enable</h5>'+
                      '' + isuser + '' +
                    '</div>'+
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
                  '<div class="inner">'+
                    '<h5>User: ' + row['uname'] + '</h5>'+
                    'IsAdmin?: ' + isadmin + '' +
                  '</div>'+
                '</div>'+
              '</div>'

  var html = []
    html.push('<form role="form" action="/" method="post" data-index="uu-' + row['uuid'] + '"><div class="row">' + 
              '' + userinfobox + ''+
              '' + isuserbox + ''+
              '' + usersdatebox + ''+
              '' + usersmailbox + ''+
              '</div>' +
              '<input type="hidden" name="uid" value="' + row['uuid'] + '">' +
              '<input type="hidden" name="uname" value="' + row['uname'] + '">' +
              '<input type="hidden" name="session" value="' + session_id + '">' +
              '<input type="hidden" name="op" value="saveuserchanges">' +
              '<button type="submit" class="btn btn-app bg-warning" name="make" value="update"><i class="fa fa-save"></i>Update</button>' +
              '<button type="submit" class="btn btn-app bg-danger" name="make" value="delete"><i class="fa fa-trash"></i>Delete</button>' +
              '<button type="submit" class="btn btn-app bg-info" name="make" value="sendmail"><i class="fa fa-envelope"></i>Mail</button>' +
              '</form>'
              )
  return html.join('')
}

