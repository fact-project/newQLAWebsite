
///-------------------------SEND DATA VIA EMAIL------------------------------------------

function check_and_send_Email(){
	name_for_email=$("#name_for_email").val();
	uni=$('#uni').val();
	email=$("#email").val();

	if (grecaptcha.getResponse() == ""){
		return false;
	} else {
	   
	
		if(name_for_email=="" || uni=="" || email=="" ){

			alert("Please fill out all fields.");
			return false;
		}else if( !validateEmail(email)){

			alert("Please fill in a valid e-mail address.");
			return false;
		}else{

			send_Email();
			
		}

	}

}

//sends information to php script that will send email
function send_Email(){
	var msg=set_msg();
	var formdata=  new FormData (document.getElementById("send_email_form"));
	formdata.append('msg',msg);
	postInBackgroungEmail("php-files/send_email.php", formdata);
}


//Validats email by using a set of regular (allowed ) expressions
function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

//writes message that will be send via email
function set_msg(){

	//the license will be added to the message on the server side 
  	var msg="\n# Contact: Daniela Dorner dorner@astro.uni-wuerzburg.de\n";

  	var today=new Date();

  	msg=msg+"\n# This file was created on: "+ today.toUTCString();


	msg= msg+"\n# Start Date: "+date_beg+"\n# End Date: "+date_end;
	msg=msg+"\n# X-Axis: "+x;
	if(x=="time"){
		if(time_binning!="None"){ //time_binning
 			
 			msg=msg+"\n# Time Bin: "+time_binning;
		}else{  //manual_time_binning
			msg=msg+ "\n# Manual Time Binning: "+ free_time_binning_years+ "year(s), "+free_time_binning_days+" day(s), "+ free_time_binning_minutes+ " minute(s)";
		}
	
		if(offset!=""){
			msg=msg+"\n# Bin Shift in Days: "+offset;}
	}

	msg=msg+"\n# Y-Axis: "+y;
	msg=msg+"\n# Used Table: "+usedTable;

	if(bin_width!=""){
		msg=msg+"\t# Bin Width: "+bin_width;}
	if(ontime!=""){
		msg=msg+"\t# Ontime: "+ontime;}
	if(good!=0){
		msg=msg+"\t# Data Check/Good: "+good;}
	if(database!=""){
		msg=msg+"\t# Database: "+database;}
	msg=msg+"\n \n# Data by Sources:"

	for(var i=0; i<data_from_server.length; i++){
		msg=msg+"\n\n# "+data_from_server[i][0]; //sourcename
		msg=msg+"\n# x\ty"; 

		for (var j=0; j<data_from_server[i][1].length;j++) {
			msg=msg+"\n"+data_from_server[i][1][j][0]+"\t"+data_from_server[i][1][j][1];
		}
	}
	return msg;
}

function postInBackgroungEmail(file_url, send_data){

	xmlhttp=new XMLHttpRequest();
	xmlhttp.onload = function(){ 

        if (xmlhttp.status!=200){ 
        //if something goes wrong
            alert("ERROR[0] - HTTP request '"+xmlhttp.statusText+" ["+xmlhttp.status+"]");
            return;
        }

		if(xmlhttp.status == 200) { 

			if(this.responseText!=""){
				//shows response text including the used email address 
				alert(this.responseText);
				//closes the spoiler0 with inputs fields for sending data
				$('#spoiler0').toggle(300);
			}


		}
	}

  	xmlhttp.open ("POST", file_url , true);
  	xmlhttp.send (send_data);
  	return false;
 }


function readTextFile(file)
{
    var rawFile = new XMLHttpRequest();
    rawFile.open("POST", file, false);
    rawFile.onload = function ()
    {
        if(rawFile.readyState === 4)
        {
            if(rawFile.status === 200 || rawFile.status == 0)
            {
                var allText = rawFile.responseText;
                return allText;
            }else{

            	return false;
            }
        }
    }
    rawFile.send(null);
}
