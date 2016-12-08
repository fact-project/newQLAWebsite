///-----GLOBALE VARIABLES-------------------------------------------
var date_end;
var date_beg;
var sources;
var x;
var y;
var time_binning;
var offset;
var free_time_binning_days;
var free_time_binning_hours;
var free_time_binning_minutes;
var usedTable;
var bin_width;
var noerr;
var ontime;
var good;
var database;
var used_query;
var data_from_server;

//---------BEHAVIOR OF INPUT FIELDS IN FORM----------------
/*
	function that will called upon element identity will,
	process that element and add custom css classes to that element,
	and generate newly themed form elements.
*/
$.widget("ui.form",
{

	/*
		widget initialization
	*/
    _init: function ()
	{


		/*
			object hold the current instance
		*/
        var object = this;
		/*
			form will hold form from current instance
			do console.log(this); to see object
		*/
        var form = this.element;
		/*
			finds the form fields and store as array
		*/
        var inputs = form.find("input , select ,textarea");
		/*
			add ui-widget class to our form
		*/
        form.addClass("ui-widget");
		
		/*
			loop through each input element,
			which we have created above
		*/
        $.each(inputs, function ()
		{
			
			/*
				add class to each fields
			*/
            $(this).addClass('ui-state-default ui-corner-all');
			
			/*
				wrap them inside label
			*/
            $(this).wrap("<label />");
			
			/*
				checking if element is
				button,checkbox,input,radio etc.
				after identify element call appropriate class method
			*/

			/*if element is submit button: calls function sendInBackground
			when clicked*/
			if($(this).is("#update")){
				$(document).ready(function(){
					$('#update').on('click', function() {
							var ok=check_save_Values();
							if(ok){
								document.getElementById("chart_div").innerHTML= "";
								$('#loading_animation').show();
						 		postInBackgroung("php-files/run_sql_query.php", new FormData (document.getElementById("form")));
					  		}
					    });
					});	


			} 


			/*Resets form if clicked*/
			if($(this).is("#reset")){
				$(document).ready(function(){
					$('#reset').on('click', function() {
							reset_inputs();
					    });
					});	


			} 

           if ($(this).is(":checkbox")){
				/*
					sets checkbox behavior
				*/

				//if enable_beg is checked, date_beg will be enabled
				$( "#enable_beg" ).change(function(event,data) { 
					$("#date_beg").datepicker('option', 'disabled', !$(this).prop('checked') );  
					if($(this).prop('checked')){
						$("#one_night").datepicker('option', 'disabled', true );  
					}else{
						if(!$("#enable_end").prop('checked')){
							$("#one_night").datepicker('option', 'disabled', false );  
						}
					}
				} );

				//IF enable_end is checked, date_end will be enabled
 				$( "#enable_end" ).change(function(event,data) { 

 					$("#date_end").datepicker('option', 'disabled', !$(this).prop('checked') ); 

						if($(this).prop('checked')){
							$("#one_night").datepicker('option', 'disabled', true );  
						}else{
							if(!$("#enable_beg").prop('checked')){
								$("#one_night").datepicker('option', 'disabled', false );  
							}
						}

 					 } );
 				
 				/*If enable_time binning is checked: free_time_binning will be enabled, normal time_binning will be diabled and 
 				set on defaut value.
 				If enable_time_binning is unchecked: free_time binning diabled and set to default, normal time_binning enabled 
 				*/

 				$( "#enable_time_binning" ).change(function(event,data) {
 					enable_free_time_binning(this);
 					$("#time_binning").val('None');
 					$("#time_binning").prop('disabled', $(this).prop('checked') );
 					if( !$(this).prop('checked')){
 						clear_free_time_binning_fields();
 					}
 				});

 				 $("#moreOptions" ).prop('checked', false);
 				 /*Displays options in various inputs div if checked*/
 				$( "#moreOptions" ).change(function(event,data) { 		

					if( $(this).prop('checked')){
 						 $("#various_inputs").show();
 					}else{
 						 $("#various_inputs").hide();
 					}
				} );

 				$( "#enable_bin_width" ).change(function(event,data) {
 					$("#bin_width").prop('hidden', !$(this).prop('checked') );
 					if( !$(this).prop('checked')){
 						$("#bin_width").val('');
 					}
 				} );
             }   
            else if ($(this).is("input[type='text']")||$(this).is("input[type='number']") || $(this).is("textarea") || $(this).is("input[type='password']")){
				/*
					calling class textelements method if element is input fields
				*/

                if($(this).is("#free_time_binning")){
                	$(this).prop('disabled', true );
                }
    			$( "#cut" ).autocomplete( {source: acSource, minLength:1, select: acSelect, focus: acFocus });
    			$( "#having"  ).autocomplete( {source: acSource, minLength:1, select: acSelect, focus: acFocus });


            }    

       
            else if ($(this).is("select")){

            	if($(this).is("#sources")){

	            	$('#sources').multiselect({
					    columns: 1,
					    placeholder: 'Select Sources',
					    search: true,
					    selectAll: true
					}); 

		            $(document).ready(function(){
					    $('#sources').on('change', function() {
				          var count = $("#sources :selected").length;	
/*					      if ( count == '1' && document.getElementById("logged").value=="yes"){

					        $("#time_binning option[value='season']").show();
					      }
					      else
					      {
					         $("#time_binning option[value='season']").hide();
					      }*/
					    });
					});	

				}
				$(document).ready(function(){
					$('#x').on('change', function() {
					    if ( $('#x').val()=="time"){
					      	$("#time_menu").prop('hidden', false );
					     }
					    else
					     {
					      	 $("#time_menu").prop('hidden', true );
					      }
					    });
					});


			}
			
			/*
				element has class date then it will create date-picker.
			*/
             if ($(this).hasClass("date")){
               	
            	    var date_opt =
				    {
				       dateFormat: 'yy-mm-dd',
				       minDate: '2011-10-10',
				       maxDate: '+0',
				       showButtonPanel: true,
				       showOtherMonths: true,
				       selectOtherMonths: true,
				       changeYear: true,
				       autoSize: true,
				       /*inline: true,*/
				    }
            
                $(this).datepicker();

            	$(this).datepicker('option', 'disabled', true /*, "$begin"==""*/);
			
            	//sets options from above for datepicker
            	$(this).datepicker('option', date_opt);

            	//Sets displayed Date for Datepickers
            	$( "#date_beg" ).datepicker("setDate", "2011-10-10" );
            	$( "#date_end" ).datepicker("setDate", "+1d");

            	$( "#one_night" ).datepicker("setDate", "-1d");
            	$( "#one_night" ).datepicker('option', 'disabled', false /*, "$begin"==""*/);

    		}
    		

        });
    },


});



//---------------------FUNCTIONS------------------------------------------------------

/*when free time binning is enabled, the numeric inputs year,day and minute 
 will be enabled and each menu will be set 0*/
function enable_free_time_binning(element){


 		$("#free_time_binning_days").prop('disabled', !$(element).prop('checked') ); 
 		$("#free_time_binning_hours").prop('disabled', !$(element).prop('checked') );   
 		$("#free_time_binning_minutes").prop('disabled', !$(element).prop('checked') );  
 

 		$("#free_time_binning_days").val('0');
 		$("#free_time_binning_hours").val('0');
 		$("#free_time_binning_minutes").val('0');
 };

/*clears manual binning fields and set 'select' in time binning,
called when enable_manual_time_binning is unchecked */
function clear_free_time_binning_fields(){

	$("#free_time_binning_days").val('');
	$("#free_time_binning_hours").val('');
	$("#free_time_binning_minutes").val('');

	$("#time_binning").val('select');
 };


 ///-----------------------FOR AUTOCOMPLETE for cut and having-------------------------




	var availableTags = [
      "ABS(","ACOS(","ADDDATE(","ADDTIME(","AND ","ASIN(","ATAN(","ATAN2(","AVG(",
      "CEIL(","CONCAT(","COS(","COUNT(","DATE(","DAY(","DEGREES(","EXCERR(","EXP(",
      "FLOOR(","FROM_UNIXTIME(","INTERVAL ","ISNULL(","LIMA(","LN(","LOG(","LOG10(",
      "LOG2(","MAX(","MIN(","MINUTE(","MONTH(","OR ","PI()","POW(","RADIANS(","RAND(",
      "ROUND(","SIGN(","SIN(","SUM(","SQRT(","TAN(","TIME_TO_SEC(","TIMEDIFF(","TRUNCATE(",
      "UNIX_TIMESTAMP(","YEAR(",
];

  function acSource( request, response )
 {
 	getTags();
     var pos = this.element[0].selectionStart;
     var t = request.term.substr(0, pos);
     var l = t.match(/\w*$/);
     response( $.ui.autocomplete.filter(availableTags, String(l)));
    }

function acSelect ( event, ui )
{

       
   var pos = this.selectionStart;
   var idx = this.value.substr(0, pos).match(/\w*$/).index;
   this.value = this.value.substr(0, idx)+ui.item.value+this.value.substr(pos);
   this.selectionStart = idx+ui.item.value.length;
   this.selectionEnd   = idx+ui.item.value.length;
   return false;
}

function acFocus(event, ui) { event.preventDefault(); return false; }





//--------------------For SENDING THE FORM DATA---------------------------------


/*saves menu values locally when send for later usage*/
function check_save_Values(){

	one_night=$('#one_night').val();
	if(one_night!=""){
		date_end=one_night;
		date_beg=one_night;

	}else{	
		date_end=$("#date_end").val();
		date_beg=$("#date_beg").val();
	}
	sources=$("#sources").val(); 
	x=$("#x").val();
	y=	$("#y").val();
	time_binning=	$("#time_binning").val();
	offset=$("#offset").val();
	free_time_binning_days=	$("#free_time_binning_days").val();	
	free_time_binning_hours=	$("#free_time_binning_hours").val();
	free_time_binning_minutes=	$("#free_time_binning_minutes").val();
	usedTable=$("#usedTable").val();
	bin_width= $("#bin_width").val();
	noerr=$("#noerr").prop('checked');
	ontime=$("#ontime").val();
	good=$("#good").val();
	database=$("#different_database").val();

	//if nothing is choosen all sources will be selected
	if(sources==""){
		//selects all sources
		$('#sources option').prop('selected', true);
			//selects all checkboxes 
			$("input[id^='ms-opt-']	").each( function(){
		$(this).prop('checked', true);
	});
	}

	var date1=new Date(date_beg);
	var date2= new Date(date_end);
	var timediff=Math.floor((date2 - date1) / (1000*60*60*24)); //in days 

	if( timediff>1068 && time_binning=="20"){  
	//if choosen interval is bigger than three years and 20 min binning: would crash website

		alert("Please select a smaller interval for 20 min binning");
		return false;
	}

	if(timediff>356 && time_binning=="run"){

		alert("Please select a smaller interval to use binning by runs.");
		return false;

	}		
	return true;

}
 
/*uses xmlHttpRequest to post the form information in the background (without reloading the page)
to run_sql_query which will prozess the information and return a response text.	or to send the 
message that will be sent to the given e-MAIL
*/
 "use strict";
function postInBackgroung(file_url, send_data){

	xmlhttp=new XMLHttpRequest();
	xmlhttp.onload = function(){ 

        if (xmlhttp.status!=200){ 
        //if something goes wrong
			$('#loading_animation').hide();

            alert("ERROR[0] - HTTP request '"+xmlhttp.statusText+" ["+xmlhttp.status+"]");
            return;
        }

		if(xmlhttp.status == 200) { 
		//everything is fine, responsetext will be displayed in the div-field "responseText"
			$('#loading_animation').hide();
			document.getElementById("responseText").innerHTML=this.responseText;
			process_responseText(this.responseText);



		}
	}

  	xmlhttp.open ("POST", file_url , true);
  	xmlhttp.send (send_data);
  	return false;
 }

function process_responseText(responseText){

	var responseArray=responseText.split(" | ");  // "|" was used as delimiter for responsetext
	used_query=responseArray[0];
	document.getElementById("query").innerHTML=used_query;

	var timeMeasured=responseArray[1];
	document.getElementById("timeMeasured").innerHTML=timeMeasured;

	var x_axis_info=JSON.parse(responseArray[2]);

	var y_axis_info=JSON.parse(responseArray[3]);

	data_from_server=JSON.parse(responseArray[4]);

	drawChart(data_from_server, x_axis_info, y_axis_info, noerr);

}

//resets all input when the button reset is clicked
function reset_inputs(){

	$('#sources option').prop('selected', false);

	$(":checkbox").each( function(){
		$(this).prop('checked', false);
	});
 	$("input[type='text']").val('');
 	 $("input[type='number']").val(''); 


 	$("#date_beg").val('');
 	$("#date_beg").datepicker('option', 'disabled', true /*, "$begin"==""*/);

	$("#date_end").val('');
 	$("#date_end").datepicker('option', 'disabled', true /*, "$begin"==""*/);

 	$( "#date_beg" ).datepicker("setDate", "2011-10-10" );
    $( "#date_end" ).datepicker("setDate", "+1d");

    $( "#one_night" ).datepicker("setDate", "-1d");
 	$("#one_night").datepicker('option', 'disabled', false /*, "$begin"==""*/);



 	$("#x").val('time');
 	$("#y").val('excess_rate');

	clear_free_time_binning_fields();
 	$("#free_time_binning_days").prop('disabled', true ); 
 	$("#free_time_binning_hours").prop('disabled', true);   
 	$("#free_time_binning_minutes").prop('disabled', true);  

 	$("#time_binning").val('20');
 	$("#time_binning").prop('disabled' , false);  

 	 $("#offset").val('');
 	 $("#different_database").val('factdata');
 	 $("#usedTable").val('LP');
 	 $("#threshold_min").val('');
 	 $("#threshold_max").val('');
 	 $("#zenith_distace_min").val('');
 	 $("#zenith_distace_max").val('');
 	 $("#good").val('0');

 	 $("#various_inputs").hide();



}


