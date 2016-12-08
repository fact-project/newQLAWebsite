
<?php
//------------CONNECTS to DATABASE--------------------------
include 'database_functions.php';
$Database=isset($_POST["different_database"]) ? trim($_POST["different_database"]): "factdata";


//connects to the database with the name DATABASE using the user factweb
connect_database($Database);


//----------------------------------SETTING VARIABLES----------------------------------------------
/* checks if variable with name ".." is set, if yes it will use this value 
for the local variable, else sets empty or different value */
$Begin  = isset($_POST["date_beg"]) ? $_POST["date_beg"] : "";
$End    = isset($_POST["date_end"]) ? $_POST["date_end"] : "";
$oneNight    = isset($_POST["one_night"]) ? $_POST["one_night"] : "";

$Sources = isset($_POST["sources"]) ? $_POST["sources"] : "[1]";

$X_Axis  =$_POST["x"];
$Y_Axis = $_POST["y"];
$Bin_Shift=isset($_POST["offset"])? $_POST["offset"] :0;
$Bin_Width=isset($_POST["bin_width"])? $_POST["bin_width"] : "";
$Table= $_POST["usedTable"];
$Ontime= isset($_POST["ontime"]) ? intval($_POST["ontime"]) : "";
$Threshold_min= isset($_POST["threshold_min"]) ? intval($_POST["threshold_min"]) : "";
$Threshold_max= isset($_POST["threshold_max"]) ? intval($_POST["threshold_max"]) : "";
$Zenith_distace_min= isset($_POST["zenith_distace_min"]) ? intval($_POST["zenith_distace_min"]) : "";
$Zenith_distace_max= isset($_POST["zenith_distace_max"]) ? intval($_POST["zenith_distace_max"]) : "";
$Good	= isset($_POST["good"]) ? $_POST["good"] : "0";
$Cut    = isset($_POST["cut"]) ? trim($_POST["cut"]) : "";
$Having    = isset($_POST["having"]) ? trim($_POST["having"]) : "";

//Manages Timebinning. One timebin from several inputs is created
$Manual_Time_binning_enabled= isset($_POST["enable_time_binning"])? true : false;

if ($Manual_Time_binning_enabled){
	$Time_bin= calc_time_bin_from_manual(); //in Minutes

}else{
	$Time_bin=get_time_bin_from_Dropdown(); //is a string, exeption if nothing is selected or 20min
}


/*gets a string neccessary for the sql statement in the WHERE part. 
Defines Zenith Distance and Threshold limits.
*/
$sqlLimit=get_SqlLimit($Zenith_distace_min, $Zenith_distace_max, $Threshold_min, $Threshold_max);




//------------SQL-QUERY--------------------------------------------------------
if($Good!="0"){
	$sqlgood= get_SqlGood($Good);
}




//Setting the to be selected column from the database according to the Axis, Axistitle, Axistype and Mintime, etc.
include "axis_properties.php";
$X_Axis_Properties=get_X_Axis_Properties($X_Axis, $Time_bin, $Bin_Width);
$Y_Axis_Properties=get_Y_Axis_Properties($Y_Axis,$Bin_Width);

//Runs the sql-query with to get the data that will be plotted
$Data=get_Data_From_Database($X_Axis_Properties, $Y_Axis_Properties, $Table, $Begin, $End, $sqlgood, $Cut, $Having, $Noerr, $Sources, $Ontime, $Time_bin, $sqlLimit, $oneNight);

//Encoding Data with Json that will be send back to Client  for easier decoding
$json_Data=json_encode($Data);
$X_Axis_Info=get_X_Axis_Info($X_Axis_Properties);
$Y_Axis_Info=get_Y_Axis_Info($Y_Axis_Properties);
$json_X_Axis_Info=json_encode($X_Axis_Info);
$json_Y_Axis_Info=json_encode($Y_Axis_Info);


// "|" is used as delimiter for Client, so data can be told apart from overhead
echo $GLOBALS['themp_query'];
echo " | ";
echo $GLOBALS['timeMeasured'];
echo " | ";
echo $json_X_Axis_Info;
echo " | ";
echo $json_Y_Axis_Info;
echo " | ";
echo $json_Data;




//------------FUNCTIONS--------------------------------------

//calculates input from Manual Time binning into minutes but only when manual time binning is enabled
function calc_time_bin_from_manual(){
	$d= isset($_POST["free_time_binning_days"]) ? ($_POST["free_time_binning_days"]) : 0;
	$h= isset($_POST["free_time_binning_hours"]) ? ($_POST["free_time_binning_hours"]) : 0;
	$min=isset($_POST["free_time_binning_minutes"]) ? ($_POST["free_time_binning_minutes"]) : 0;

	$result=$d*1440+$h*60+$min;
	return $result;
}

//recives the selected Timebin from the dropdown menu if it was used. If nothing choosen then 20min timebinning will be applied
function get_time_bin_from_Dropdown(){
	$result  = ($_POST["time_binning"]);
	if($result=="select" || $result=="20"){
			$result=intval(20);
	}
	return $result;
}

//Combines Datachecks from different websites and returns a string that will be added to the sql Statement to narrow down the data
function get_SqlGood($g){
	$result="";
	/* newer datachecks form db_explorer, do not work on factdata but on newer databases*/
    if ($g=="1"){
	 $result .= " AND fNumThreshold750/(cos(fZenithDistanceMean*PI()/180)+0.179*pow(sin(1.89*fZenithDistanceMean*PI()/180),5))/TIME_TO_SEC(TIMEDIFF(fRunStop,fRunStart))/fEffectiveOn>3.7 ";
	}
    if ($g=="2"){
    	$result .= " AND ((fNumEvtsAfterCleaning*1.0-fNum27Sec*1.0)/((-2381.1+2766.3*LOG10(fThresholdMinSet)-1049.9*POW(LOG10(fThresholdMinSet),2)+131.05*POW(LOG10(fThresholdMinSet),3))/21.15)/(COS(fZenithDistanceMean*PI()/180)+0.179*POW(SIN(1.89*fZenithDistanceMean*PI()/180),5)))/(TIME_TO_SEC(TIMEDIFF(fRunStop,fRunStart))*fEffectiveOn)>16.2 ";
    }
    if ($g=="4"){
		$result .= " AND (fNumEvtsAfterQualCuts/(cos(fZenithDistanceMean*PI()/180)+0.179*pow(sin(1.89*fZenithDistanceMean*PI()/180),5))/((8598.9- 13181*log10(fThresholdMinSet)+7567.4*pow(log10(fThresholdMinSet),2)-1925.2*pow(log10(fThresholdMinSet),3)+183*pow(log10(fThresholdMinSet),4))/9.2 ))/(TIME_TO_SEC(TIMEDIFF(fRunStop,fRunStart))*fEffectiveOn)>6.7 ";
	}	
    if ($g=="8"){ 
    	$result .= " AND (fNumEvtsAfterBgCuts/(1.41*POW(fZenithDistanceMean*PI()/180,2)+0.975)/(-7.53e-12*POW(10, LOG10(fThresholdMinSet)*3.69)+1.035) )/(TIME_TO_SEC(TIMEDIFF(fRunStop,fRunStart))*fEffectiveOn)>0.6 ";
    }
    if($g="current"){

    	/*Current data check as functional in qla.php (oldes qla website)
		Datacheck works on factdata		*/
		$zdparam=" pow(0.753833*cos(Radians(fZenithDistanceMean)), 7.647435)*exp(-5.753686*pow(Radians(fZenithDistanceMean),2.089609))";
		$thparam=" pow((if(isnull(fThresholdMinSet),fThresholdMedian,fThresholdMinSet)-329.4203),2)*(-0.0000002044803) ";
		$param=" (fNumEvtsAfterBgCuts/5-fNumSigEvts)/fOnTimeAfterCuts - $zdparam - $thparam ";
    	$dchold=" -0.085 < ($param) AND 0.25 > ($param) ";
    	$dchval=" fNumEvtsAfterBgCuts/(1.41*POW(fZenithDistanceMean*PI()/180,2)+0.975)/(-7.53e-12*POW(10, LOG10(fThresholdMinSet)*3.69)+1.035)/TIME_TO_SEC(TIMEDIFF(fRunStop,fRunStart))/fEffectiveOn ";

		$result="  AND (($dchval BETWEEN 0.8 AND 1.7 AND fNight BETWEEN 20140520 AND 20150131) OR  ($dchval BETWEEN 0.4 AND 1.6 AND fNight BETWEEN 20150201 AND 20150715)  OR  ($dchval BETWEEN 0.7 AND 1.4 AND fNight BETWEEN 20150716 AND 20160218) OR  ($dchval BETWEEN 0.5 AND 1.0 AND fNight > 20160220) OR  ($dchold AND fNight<20140520)) "; 
    }
    return $result;
}


//takes parts from X_Axis_Properties that need to be send to the client for labeling the chart
function get_X_Axis_Info($X_Axis_Properties){
$X_Axis_Info=array();
$X_Axis_Info[]=$X_Axis_Properties[1];
$X_Axis_Info[]=$X_Axis_Properties[2];
$X_Axis_Info[]=$X_Axis_Properties[3];

return $X_Axis_Info;
}


//takes parts from Y_Axis_Properties that need to be send to the client for labeling the chart
function get_Y_Axis_Info($Y_Axis_Properties){
$Y_Axis_Info=array();
$Y_Axis_Info[]=$Y_Axis_Properties[1];
$Y_Axis_Info[]=$Y_Axis_Properties[2];

return $Y_Axis_Info;
}


//returns the part written in the SQL statement under WHERE that specifies theshold and zenithdistance for the data
function get_SqlLimit($Zenith_distace_min, $Zenith_distace_max, $Threshold_min, $Threshold_max){
	$sqlLimit="";
       if($Zenith_distace_min!=""){ 	
       $sqlLimit=$sqlLimit."AND ( fZenithDistanceMin>$Zenith_distace_min) ";
      }
    if($Zenith_distace_max!=""){
      $sqlLimit=$sqlLimit."AND (fZenithDistanceMax<$Zenith_distace_max)";
	}

    if($Threshold_min!=""){ 	
       $sqlLimit=$sqlLimit."	AND (fThresholdMinSet>$Threshold_min)";
      }
    if($Threshold_max!=""){
      $sqlLimit=$sqlLimit."	AND (fThresholdMax<$Threshold_max)";
	}
	return $sqlLimit;
}


?>
