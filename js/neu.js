
$(function () {
  "use strict";



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
        document.getElementById("message-stage").innerHTML = "<p class=\"bg-danger text-white\">Fehler</p>";
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

