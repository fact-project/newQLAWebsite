<!DOCTYPE html>
<html>
<head>
	<title>Chart Menu</title>

	  <!--for sending data via email-->
	<script type="text/javascript" src="scripts/send_email.js"></script>
	<!--for Menu behavior-->
	<script type="text/javascript" src="scripts/chart_menu.js"></script>

	<!--creating forms so they can be used by jQuery in the Java Script file-->
	<script type="text/javascript">
		$(function()
		{
			$("form").form();
		});

		$(function()
		{
			$("send_email_form").form();
		});	
	</script>

</head>
<body>
	<div style="text-indent:10px;" id="container">


		<!-- Form will be send when clicking on update
		The update Button calls function sendInBackground() in Scpipt file -->		
		<form action="#" id="form">
		<!--This creats bottons, dopdown menus and checkboxes for the menu of the plot  -->
			</br>
			<div>
				<label for="enable_beg">From</label>
				<input type="checkbox" class="checkbox" id="enable_beg" title="Enable a lower date limit">
				<input type="text"  class="date" id="date_beg" name="date_beg" />

		   		 <label for="enable_end">To</label>
   				 <input type="checkbox" class="checkbox" id="enable_end" title="Enable an upper date limit">
				<input type="text" class="date" id="date_end" name="date_end" />

				<label for="one_night">One Night</label>
				<input type="text" class="date" id="one_night" name="one_night" />		

			</div>


			<!--Dynamically loaded multiselect dropdown menu for displaying sources. The user can check sources with a checkbox in the menu-->
			<div  style=" display: inline-block" >
			    <label for="sources" title="Select Sources for plotting">Sources</label>

			    <select name="sources[]" multiple id="sources" >

			      <optgroup label="Sources" >
			      	<?php 
			      	 include 'php-files/database_functions.php'; 
			  		connect_database("factdata");
			  		$sourceoptions = get_sourceoptions();
			      	 echo $sourceoptions; ?>
			      </optgroup>

			    </select>
			</div>




		
			<!--Select menus for x and y axis-->
			<div style=" display: inline-block">
				
 				<label for="x" >X-Axis</label></br>
				<select class="drop_down_menu" name="x" id="x" style="width:10em;display:inline-block" title="Select X Axis for plotting">
				    <option value="time">Time</option>
				  	<option value="zenith_distance" hidden>Zenith Distance</option>
				  	<option value="threshold" hidden>Threshold</option>

				</select>
			</div>
			<div style=" display: inline-block">
	

			    <label for="y" >Y-Axis</label></br>	
			    <select class="drop_down_menu" name="y" id="y" style="width:10em"  title="Select Y Axis for plotting">
				  	<option value="excess_rate">Excess Rate</option>
				  	<option value="background_rate" hidden>Background Rate</option>
				  	<option value="zenith_distance" hidden>Zenith Distance</option>
				  	<option value="threshold" hidden>Threshold</option>
				  	<option value="significance" hidden>Significance</option>
				  	<option value="signal_rate" hidden>Signal Rate</option>

				</select>
			</div>
			<div style=" display: inline-block" >

				<div name="time_menu" id="time_menu" >

				<!--Dropdown menu to select time binning. Is disabled when manual time binning is enabled-->				
				<div  style=" display: inline-block">
				    <label for="time_binning" >Time Binning</label></br>
				    <select class="drop_down_menu" name="time_binning" id="time_binning" style="width:10em"  title="Select Time Binning">
				        <option value='select' hidden >Select option</option>
				        <option value='None' hidden></option>
				        <option value="20" selected>20 min</option>   
				      	<option value="night">nightly</option>
				      	<option value="month" hidden>monthly</option>
				      	<option value="year" hidden>yearly </option>
				    	<option value="season" hidden>seasonal (only for single Sources)</option>
				    	<option value="period" hidden>Periodically</option>
				    	<option value="run" hidden>Run (only for short intervals)</option>

				    </select>
				 </div>   

				<div  style=" display: inline-block">
					<!--Inuts for manual time binning and checkbox. only enabled if checkbox is checked-->
					<div id="manual_time_binning_inputs" name="manual_time_binning_inputs"  hidden>
						<div>
					    <label for="free_time_binning" title="Enable free Time Binning">Manual Time Binning </label></br>
					    <input type="checkbox"  class="checkbox" id="enable_time_binning" name="enable_time_binning" title="Enable free Time Binning">


					   	<label for="free_time_binning_days">Days</label>
					   	<input type="number"  class="input_field" name="free_time_binning_days" min="0" max="29"id="free_time_binning_days" style="width:3em" title="Select Daily Time Bins">

	   					<label for="free_time_binning_hours">Hours</label>
					   	<input type="number"  class="input_field" name="free_time_binning_hours" min="0" max="23"id="free_time_binning_hours" style="width:3em" title="Select Time Bins in Hours Range">

					   	<label for="free_time_binning_minutes">Minutes</label>
					   	<input type="number"  class="input_field" name="free_time_binning_minutes" min="0" max="59" id="free_time_binning_minutes" style="width:3em" step="5" title="Select Time Bins in 5 Minutes Steps. If less than 60 minutes the Time Binning directly transferes to the Ontime. ">
					 
					 	</div>
					 </div>
				 </div>

				 <div  style=" display: inline-block">
					<!--Input to shit the offset of data . Unit: days-->
					<div id="offset_input" name="offset_input" hidden>
					    <label for="offset" >Bin Shift in Days:</label>
					    <input type="number" class="input_field" id="offset" name="offset" min="0" style="width:3em" title="Normally binning starts at 0. This allows an artificial shift of the bins.">
					  
					</div>
 				</div>
			 </div>
			 </div>


			<div  id="more_options_div" name="more_options_div" hidden >
				<label for="moreOptions" >More Options</label>
				<input type="checkbox"  class="checkbox" id="moreOptions" name="moreOptions" >
			</div>
	


			<!--Select menu to let user choose analysis table-->
		 	<div id="various_inputs" name="various_inputs" hidden>


				<!-- Selectmenu for Databases (not dynamic)-->				
				<div style="display:inline-block" >
					<label for="different_database" title="Set Database that will be used. Default: factdata.">Set Database</label ></br>
					<select  lass="drop_down_menu"  name="different_database" id="different_database" style="width:15em" >

						<option value="factdata" selected>factdata</option>
						<option value=" factdata.test.2014.05.13" > factdata.test.2014.05.13</option>
						<option value="factdata.test.2014.05.13_2" >factdata.test.2014.05.13_2</option>
						<option value="factdata.test.2014.05.13_3" >factdata.test.2014.05.13_3</option>
						<option value="factdata.test.2015.02.18" >factdata.test.2015.02.18</option>
						<option value="factdata_check20140820" >factdata_check20140820</option>
						<option value="factdata_test20160505" >factdata_test20160505</option>


				    </select>
				</div>
			


				  <div style="display:inline-block;">
				    <label for="usedTable">Using Table: </label></br>
				    <select class="drop_down_menu" name="usedTable" id="usedTable" style="width:10em" title="Set Table. Default: LaPalma">
				        <option value="LP" selected >LaPalma</option>
				        <option value="ISDC">ISDC</option>
				        </select>

				     <label for="enable_bin_width" title="Width of the range in which data is grouped together, for other binning than time binning.">Bin Width </label>
				    <input type="checkbox"  class="checkbox" id="enable_bin_width" name="enable_bin_width" title="Enable Bin Width">
				    <input type="number"  class="input_field" id="bin_width" name="bin_width" min="1" style="width:3em" hidden>
		
				
				 </div>

				<!--checkboxes for No Errorbars and Numeric Input for Ontime-->
				 <div style="display:inline-block;">
		
				    <label for="noerr">No Errors:</label>
				    <input type="checkbox" class="checkbox" id="noerr" name="noerr" title="Disable error bars">
				  </div>

				  <div style="display:inline-block;">
				    <label for="ontime">Ontime in Minutes: </label>
				    <input type="number"  class="input_field" min="0" id="ontime" name="ontime" title="Set Ontime" style="width:3em">
				  </div>
			
				<!--Limits for Threshold and Zenith distance-->
				<div style="display:inline-block;">
					<label for="threshold_min">Threshold (Minimum): </label>
				    <input type="number"  class="input_field" min="0" id="threshold_min" name="threshold_min" title="Set Threshold lower level. Good Value : 290"  style="width:4em">

				    <label for="threshold_max">Threshold (Maximum): </label>
				    <input type="number"  class="input_field" min="0" id="threshold_max" name="threshold_max" title="Set Threshold upper level. Good Value: 550"  style="width:4em">
				 </div>
				 
				 <div style="display:inline-block;">
		    		<label for="zenith_distace_min">Zenith Distance (Minimum): </label>
				    <input type="number"  class="input_field" min="0" id="zenith_distace_min" name="zenith_distace_min" title="Set Zenith Distanze lower level. Good Value:0"  style="width:4em">


		    		<label for="zenith_distace_max">Zenith Distance (Maximum): </label>
				    <input type="number"  class="input_field" min="0" id="zenith_distace_max" name="zenith_distace_max" title="Set Zenith Distanze upper level. Good Value: 45" style="width:4em">

				</div>
	
				<!--Select menu for datachecks with Mathjax explenations written in Latex Syntax-->	
				  <div >
				    <label for="good">Data Check: </label>
				    <select class="drop_down_menu" id="good" name="good" title="Enable Data Check / Good Data" style="width:97%">
				    	<option value="0" selected> Select Datacheck \( \) </option>

				    	<option value="1"> 1  (for newer databases) \( \frac{(fnum_{th750} / cos(zd_{mean}\cdot\frac{\Pi}{180})+0.178\cdot sin(1.89 \cdot zd_{mean} \cdot \frac{\Pi}{180})^5)) \cdot effOntime }{(t_{stop}-t_{start})}> 3.7 \) </option>


				    	<option value="2"> 2  (for newer databases) \( \frac{((fnum_{aftercleaning}-fnum_{27sec}) / cos(zd_{mean}\cdot\frac{\Pi}{180}+0.178\cdot sin(1.89 \cdot zd_{mean} \cdot \frac{\Pi}{180})^5)) \cdot effOntime }{((a_1-a_2\cdot log_{10}(th_{min})+a_3 \cdot log_{10}(th_{min})^2-a_4 \cdot log_{10}(th_{min})^3+a_5 \cdot log_{10}(th_{min})^4)/21.5 ) \cdot (t_{stop}-t_{start})}> 16.2 \) </option>


				    	<option value="4"> 4  (for newer databases) \( \frac{(fnum_{qualcuts} / cos(zd_{mean}\cdot\frac{\Pi}{180}+0.178\cdot sin(1.89 \cdot zd_{mean} \cdot \frac{\Pi}{180})^5)) \cdot effOntime }{((a_1-a_2\cdot log_{10}(th_{min})+a_3 \cdot log_{10}(th_{min})^2-a_4 \cdot log_{10}(th_{min})^3+a_5 \cdot log_{10}(th_{min})^4)/9.2 ) \cdot (t_{stop}-t_{start})}> 6.7 \)	 </option>


				    	<option value="8"> 8 (for newer databases) \( \frac{fnum_{bgcuts} \cdot (-7.53e-12 \cdot th_{min} \cdot 369 + 1.035) \cdot effOntime}{(1.41\cdot(zd_{mean}\frac{\Pi}{180})^2+0.975) \cdot (t_{stop}-t_{start})} >0.6 \)</option>

				    	<option value="current" > current (for factdata) <!--
\( ((0.8 < check_8 < 1.7) AND (10.5.2014 < fNight < 31.1.2015))\\ 
 OR ((0.4 < check_8 < 1.6) AND (1.2.2015 < fNight < 15.7.2015))\\
 OR ((0.7 < check_8 < 1.4) AND (16.7.2015 < fNight < 18.2.2016))\\
OR ((0.5 < check_8 < 1.0) AND (10.2.2016 < fNight))
 OR (-0.85 < param < 0.25) \\ \)
 with  \(check_8\): see above, 
 \(param=\frac{fnum_{bgcuts}/5-fnum_{sigevents}}{Ontime_{aftercuts}}-(th_{min}-329.42)^2 \cdot -2.04 \cdot 10^{-6}-0.754 \cdot cos(zd_{mean}^{7.65}\cdot e^{-5.7 \cdot zd_{mean}^{2.08}})\) --> </option>
				    </select> 
				</div>



				


				<!--Inputs field for the user to change the SQL Statement. Autocorrection is enabled-->
				<div>
				
					<label for="cut" title="Altering the Where - Part of the used SQL Statement">Cut</label ></br>
					<input type="text"  class="input_field" name="cut" id="cut" style="width:97%" />
				</div>

				<div>
					<label for="having" title="Altering the Having - Part of the SQL Statement">Having</label></br>
					<input type="text"  class="input_field" name="having" id="having"  style="width:97%"/>
				</div>
			
			</div>
			<div>
				<input  type="button" class= "button" class="button" id="update" value="update" title="Sends Selected Information and loads Data"/>
				<input  type="button" class= "button" class="button" id="reset" value="reset" title="Resets Menu Inputs"/>

			</div>

		</form>
	</div>

	<!--Will be displayed when XMLHttpRequest is send to run_sql_query.php-->
	<div id="loading_animation" hidden >
		<center>
			<img src="images/loading_animation.gif" alt="Loading Animation"/>
		</center>

	</div>


	
	<!--chart will be drawn here-->
	<div id="chart_div" style='position:relative'></div>
	<div>

<!--For displaying response text, normally hidden-->
<input type="button" class= "button" id="responseTextButton" onclick="$('#responseText').toggle(300);" value="responseText" hidden />
	<div id=responseText style="display:none" hidden> </div>


<hr>

<!-- For sending data via email, using a form. sending will be handled by send_email.js and send_email.php -->
<input type="button" class= "button" onclick="$('#spoiler0').toggle(300);" value="Send Data via Email"/>
<div id="spoiler0" style="display:none">

	<form action="#" id="send_email_form" method="post" title="Open to access ">
		<center>
			<div>
				<label for="name_for_email"> Name </label></br>
				<input type="text" name="name_for_email" id="name_for_email">
			</div>
			<div>
				<label for="uni"> Institution /University  </label></br>
				<input type="text" name="uni" id="uni">
			</div>
			<div>	
				<label for="email"> E-Mail Address </label></br>
				<input type="email" name="email" id="email">
			</div>
			<div>	
				<label for="motivation">This Data will be used for..</label></br>
				<textarea name="motivation" id="motivation" cols="40" rows="5"></textarea>
				
			</div>

			<div>

			<div class="g-recaptcha" data-sitekey="6LfNnwoUAAAAACXP6o1zKEzGz3FDnNT63qUtW5fB"></div>

			<div>	
				<input type="button" class= "button" name="send_email" value="Submit" onclick="check_and_send_Email()" > </br>
				<i>By clicking on the 'Submit', you agree to our </i> <a onclick="alert(document.getElementById('license').textContent);" > <i>data usage policy</i></a>. 

			</div>

		</center>
	</form>

</div>

<!--for displaying an example  for a used sql query and the measured round trip time -->

<div id ="show_sql_query" hidden>
	<hr>
	<!--- filed for used query, will be send from run_sql_query-->
	<input type="button" class= "button" onclick="$('#spoiler1').toggle(300);" value="SQL Query"  />
	<div id="spoiler1" style="display:none">
		<b> Sql Query used:</b>
		<!--pre makes sure that /n in texts gets interpreted as new line-->
		<pre>  
			<div id=query></div>
		</pre>	
		<tt><font color="red">	(Sourcekey will be changed for each source.) </font> </tt> </br>
		<tt><font color="blue"><b>Roundtrip query time in ms:</B></font> 
		<div id=timeMeasured>time measured </div> </tt>
		
	</div>
</div>

<hr>
</body>
</html>
