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
 * @version		1.4.1
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */


$(window).on('load',function(){
  $('#error-message').modal('show');
});

/**
 * Load Username if exists and print inputfield red (exists) or green (not exist)
 * @param {*} str from Input field
 * @return background color style
 */
function showuser(str) {
  if (str.length == 0) {
    document.getElementById("InputUsername").innerHTML = "";
    return;
  } else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var isok = JSON.parse(this.responseText);
        if (isok['isuser']){
          document.getElementById("InputUsername").style.backgroundColor = "#ea7782"; // username not found
          document.getElementById("Plonk").disabled = true;
        }else{
          document.getElementById("InputUsername").style.backgroundColor = "#90f7a7"; // username found
          document.getElementById("Plonk").disabled = false;
        }
      }
    };
    xmlhttp.open("GET", "?op=live&go=user&uname=" + str, true);
    xmlhttp.send();
  }
}