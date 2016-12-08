<!DOCTYPE html>

<?php


		error_reporting(E_ALL);
		ini_set('display_errors', 'on');
?>

<html >
<head>
<title>FACT Quick Look Analysis</title>

  <meta name="Author" content="Leonie Reichert" />
  <!--For automatic scaling of Website-->	
  <meta name="viewport" content="width=device-width,initial-scale=1">
 
  <!--language and alphabet-->
  <meta http-equiv="Content-Language" content="en,ge">
  <meta charset="UTF-8">

  <!--Layout-->
  <link rel="stylesheet" href="stylesheets/style.css" type="text/css">
  <link rel="stylesheet" href="stylesheets/datepicker.css" type="text/css">

   <!-- for the mysqli extension-->
  <script type="text/javascript" src="scripts/jquery-3.1.0.js"></script>

  <!-- for behavior of jQuery forms-->
  <script src="jquery-ui-1.12.1.custom/jquery-ui.js"></script>

  <!--For Login-->
  <script type="text/javascript" src="scripts/login.js"></script>
  

  <!---for multiselect dropdown menu with checkboxes -->
  <link href="multiselectmenu_with_checkbox/jquery.multiselect.css" rel="stylesheet" type="text/css">
  <script src="multiselectmenu_with_checkbox/jquery.multiselect.js"></script>

  <!--for displaying formulas-->
  <script type="text/javascript" async
  src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-MML-AM_CHTML"></script>

  <!-- for plot-->
  <script type="text/javascript" src="https://cdn.plot.ly/plotly-latest.min.js"></script>
   <script type="text/javascript" src="scripts/plot.js"></script>

<!--For displaying Equations in the data check menu-->
	<script type="text/x-mathjax-config">
	MathJax.Hub.Config({
	  CommonHTML: {
	    scale: 50
	  }
	});
	</script>

	<!--For displaying the plot from last night on page load-->
	<script type="text/javascript">
     window.onload =  function load() {
        console.log("load event detected!");
		document.getElementById("update").click();      
	}
	</script>


 <!-- for reCaptcha-->
 <script src='https://www.google.com/recaptcha/api.js'></script>
<body>

<header>

  <!--Logged-in Field (will be yes if logged in) and  Login Button. Button will be hidden and text will be displayed if user logged in-->	
  <input type="hidden" id="logged" value="no">
  <nav> 
  		<div id="login_button" name="login_button">
  		<a href="#" onclick='login_fact("yes")' >Login</a></div>
		<div id="logged_in_text" name="logged_in_text" hidden></div>

   <!--Important Links to FACt Forum and Uni Wü, Astronomy main page-->
  <!--  <div><div> <a href ="http://www.fact-project.org">FACT Forum</a> <a href="http://www.astro.uni-wuerzburg.de/en?set_language">Astronomy Würzburg</a> </div><div>
  --></nav>
  <h1> FACT Q<span>uick</span>L<span>ook</span>A<span>nalysis</span> </h1>
 <!-- <span>FACT Telescope</span> --></header>

<!--Horizontal Menu creating tab like behaviour by displaying hidden sections and hiding othe ones-->
<nav>
<div id="menucase">
  <div id="menu_horizontal">
    <ul>
      <li><a href="#" onclick="opentab('plots')" >QLA</a></li>
      <li><a href="#" onclick="opentab('about')" >About</a></li>
      <li><a href="#" onclick="opentab('license')" >License</a></li>
      <li><a href="#" onclick="opentab('contact')" >Contact</a></li>
      
    </ul>
  </div>
</div>
</nav>

<!--MAIN TAB: Display of the menu, plot, mailing function and SQL Query-->

<section  >
	 <div id="plots" class="tab" style="text-indent:10px;">
		<!--<h3> Quick Look Analysis Plots </h3>-->
		<?php
			include "php-files/chart_menu.php";
		?>
	  </div>
 
</section>


<!--Explenations about FACT with Picture of Telescope and futher reading recomendations-->
<section  >
 
	<div id="about" class="tab"  style="text-indent:10px;" ></br>
	<h2> About FACT </h2>
	<center>
	<img src="images/fact-telescope.jpg" alt="FACT Telescope" height="90%" width="90%" >
	</center>

	<p>
	 The <b>F</b>irst G-<b>A</b>PD <b>C</b>herenkov <b>T</b>elescope (FACT) is the first imaging atmospheric Cherenkov telescope using Geiger-mode avalanche photodiods (G-APDs) as photo sensors. The rather small, low-cost telescope will not only serve as a test bench for this technology in Cherenkov astronomy, but also monitor bright active galactic nuclei (AGN) in the TeV energy range.
	</p>
	<p> 
	 (http://www.isdc.unige.ch/fact/ on 9.10.2016 16:33)
	</p>
	<p>
		<h4>More Information: </h4>
		<ul>
		
		<li> <b>
			FACT Design Paper </b>:
			 H. Anderhub et al. JINST 8 P6008 
			<div class="box">
			<a target='_blank' href='http://adsabs.harvard.edu/abs/2013JInst...8P6008A'>ADS</a></div>

			<div class="box">
			<a target='_blank' href='http://iopscience.iop.org/1748-0221/8/06/P06008'>open access</a><div></li>

		<li> <b>
		FACT Performance Paper</b>: 
		A. Biland et al. JINST 9 P10012 

		<div class="box">
		<a target='_blank' href='http://adsabs.harvard.edu/abs/2014JInst...9P0012B'>ADS</a> </div>

		<div class="box">
		<a target='_blank' href='http://iopscience.iop.org/1748-0221/9/10/P10012/'> open access</a></div></li>
		</ul>
	</p>
	</br>

	</div>
</section>

<!--Display of the global license file-->
<section  >
  
	<div id="license" class="tab" style="text-indent:10px;" ></br>
		<h2> License </h2>
	
		<!--pre makes sure that /n in texts gets interpreted as new line-->
		<pre>
			<?php 
				include "license_file.php";
				echo license();
			 ?>
 		</pre>
	
	
	  </div>
  
</section>

<!--Contact tab with obscurefied mail addressed, with picture of FACT Logo-->
<section  >
  
	<div id="contact" class="tab" style="text-indent:10px;" ></br>
		<h2> Contact </h2>

		<p>
		At University of Würzburg, Chair for Astronomy: </br>
		Dr. Daniela Dorner 
		 <i>							
		 <span class="obfuscated" name="renrod" domain="ed.grubzreuw-inu.ortsa"></span>
		 </i></br>
		 </p>
		 <p>
		Feedback concerning the Website and Bugreports: </br>
		Leonie Reichert

		<i>	<span class="obfuscated" name="trehcier.einoel" domain="ed.grubzreuw-inu.liam-duts"></span> </i>
		 </p>
		<center>

	</center>
		
	  </div>
  
</section>



<footer>
  <center>
<p>&copy; Copyright 2016 - fact-project.org  </p>
<p>This Website was optimized for Firefox.</p>
</center>


</footer>
<!-- script for Mainpage functions like tabs. DO NOT MOVE! -->
<script src="scripts/page_behavior.js"></script>

</body>
</html>
