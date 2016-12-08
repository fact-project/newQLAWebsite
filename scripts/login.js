//no usdeclared variables
"use strict";

var data = [];
var options = {};


function login_fact(button)
{
  
    var xmlLoad = new XMLHttpRequest();
    xmlLoad.open('POST', "php-files/login.php", false);
    xmlLoad.setRequestHeader("Cache-Control", "no-cache");
    xmlLoad.setRequestHeader("If-Match", "*");
    xmlLoad.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlLoad.onload = function()
    {
       
        if (xmlLoad.status!=200)   // "OK"
        {
            //to debug xml http request:
            //alert("ERROR[0] - HTTP request '"+xmlLoad.statusText+" ["+xmlLoad.status+"]");
            return;
        }

        if (xmlLoad.status==200)
        {
            document.getElementById("logged").value = 'yes';
            //alert(this.responseText);

		    document.getElementById("login_button").innerHTML="Logged in!";

		    $("#x option[value='zenith_distance']").show();
		    $("#x option[value='threshold']").show();

            $("#y option[value='background_rate']").show();
		    $("#y option[value='zenith_distance']").show();
		    $("#y option[value='threshold']").show();
		    $("#y option[value='significance']").show();
		    $("#y option[value='signal_rate']").show();

            $("#time_binning option[value='month']").show();
            $("#time_binning option[value='year']").show();
            $("#time_binning option[value='period']").show();
            $("#time_binning option[value='run']").show();


		    $("#offset_input").show();
		    $("#manual_time_binning_inputs").show();
            $("#more_options_div").show();

            $("#show_sql_query").show();

            //$("#various_inputs").show();
            $("#responseTextButton").show();

            return;
        };
    };
    xmlLoad.send("button="+button);
};