<?php


/*Sets axis properties like unit, titel, minimum for ontime, groupby - clause, 
and the what will be written as x in the sql statement.
groupby for x is always usefull unless manual time binning or 20 min time binning 
was enabled*/
function get_X_Axis_Properties($X_Axis, $Timebin , $Bin_Width){

	$properties="";
	switch ($X_Axis)
	{
		case "time":
				//gets properties in case time was choosen as x-axis
				$properties=get_X_Time_Properties($Timebin);
			break;

		case "zenith_distance":
			$bin = ($Bin_Width!="") ? $Bin_Width : 5;
		    $selectx="FLOOR(fZenithDistanceMean/".$bin.")*".$bin."+".$bin."/2";
		    $titlex="Zenith Distance";
		    $unitx="Degrees [deg]";
		    $mintime=0.5;
		    $groupby="x";
			$properties=[$selectx, $titlex, $unitx, $mintime, $groupby];
			break;

		case "threshold":
			$bin = ($Bin_Width!="") ? $Bin_Width : 50;
		    $selectx="fThresholdAvgMean";
		    $titlex="Threshold";
		    $unitx="DAC counts"; 
		    $mintime=0.5;
			$groupby="x";
			$properties=[$selectx, $titlex, $unitx, $mintime, $groupby];

			break;

		default:
		    $selectx="default";
		    $titlex="default";
		    $unitx="default";
		    $mintime=0;
			$groupby="x";
			$properties=[$selectx, $titlex, $unitx,  $mintime, $groupby];
			break;	

	}
	return $properties;

}

/*Sets axis properties like unit, titel, minimum for ontime, groupby - clause, 
and the what will be written as y in the sql statement.
Groupby for y-axis is only needed if the x and y axis are not functionally depended
*/
function get_Y_Axis_Properties($Y_Axis, $Bin_Width){
	
	$selecterr="1";
	$isnull="";
	$ontimeif="IF(ISNULL(fEffectiveOn), fOnTimeAfterCuts, TIME_TO_SEC(TIMEDIFF(fRunStop,fRunStart))*fEffectiveOn)";
	switch ($Y_Axis) {

		case "excess_rate":  
		    $selecty="SUM(fNumExcEvts)*3600/SUM($ontimeif)";
    		$titley="Excess Rate";
    		$unity="[1/h]";
    		$isnull="AND NOT ISNULL(fNumExcEvts)";
   			$selecterr="EXCERR(SUM(fNumSigEvts), SUM(fNumBgEvts))/SUM($ontimeif)*3600";
    		$groupby="";

			break;

		case "background_rate":
			$selecty="SUM(fNumBgEvts)/SUM($ontimeif)";
    		$titley="Background Rate";
    		$unity="events/sec²";  
    		$isnull="AND NOT ISNULL(fNumBgEvts)";
    		$groupby="";
			break;

		case "zenith_distance": 
			$bin = ($Bin_Width!="") ? $Bin_Width : 5;
		    $selecty="FLOOR(fZenithDistanceMean/".$bin.")*".$bin."+".$bin."/2";
    		$titley="Zenith Distance";
    		$unity="Degrees [deg]";
    		$groupby=",y";
			break;

		case "threshold":
			$bin = ($Bin_Width!="") ? $Bin_Width : 50;
		    $selecty="fThresholdAvgMean";
    		$titley="Threshold";
    		$unity="DAC counts";
    		$groupby=", y";
			break;

		case "significance": 
			 $selecty="(LIMA(SUM(fNumSigEvts), SUM(fNumBgEvts)))";
    		$titley="Significance";
    		$unity="%"; //to do: Stimmt so?
		   	$groupby="";

			break;

		case "signal_rate":
		    $selecty="SUM(fNumSigEvts)/SUM($ontimeif)";

    		$isnull="AND NOT ISNULL(fNumSigEvts)";
    		$titley="Signal Rate";
    		$unity="events/sec²"; 
    		$groupby="";

    		break;

		default:
			$selecty="default";
			$titley="default";
			$unity="default";	
			$groupby="";

			break;
	}

	$properties=[$selecty, $titley, $unity, $isnull, $selecterr, $groupby];
	return $properties;

}

/*returns axis properties if time was selected as x- axis*/
function get_X_Time_Properties($Timebin){
	$titlex="Time";
	switch ($Timebin) { 
				case "20min":
					$selectx="DATE_ADD( MIN(fRunStart), INTERVAL Round( TIME_TO_SEC(TIMEDIFF(MAX(fRunStop),MIN(fRunStart)))/2) SECOND  ) ";
					$unitx=" $Timebin min Bins ";
					$mintime=1;
					$groupby="";
					break;
				
				case "night":
					$selectx="STR_TO_DATE(fNight, '%Y%m%d')";
		    		$unitx="Nightly Binning";
		   	 		$mintime=20;
					$groupby="x";
					break;

				case "month":
					$selectx="STR_TO_DATE(fNight, '%Y%m')";
		    		$unitx="Monthly Binning";
		    		$mintime=60;
					$groupby="x";
					break;

				case "year":
					$selectx="YEAR(fNight)";
		    		$unitx="Yearly Binning";
		    		$mintime=60;
					$groupby="x";
					break;
				
				case "season":

				$selectx="ADDDATE(DATE(CONCAT(YEAR(ADDDATE(DATE(fNight), INTERVAL - (fRightAscension/24*365+80) DAY)), '-01-01')), INTERVAL + (fRightAscension/24*365+80+365/2) DAY)"; 
		    		$unitx="Seasonal Binning";
		    		$mintime=60;
					$groupby="x";
					break;

				case "period":    
					$selectx="fPeriod";
    				$unitx="Periods";
    				$mintime=60;
					$groupby="x";
					break;

				case "run":
				    $selectx="DATE_ADD( fRunStart, INTERVAL Round( TIME_TO_SEC(TIMEDIFF(fRunStop,fRunStart))/2) SECOND  )" ;
   					$unitx="Runs";
    				$mintime=1;
					$groupby="x";
					break;
				
				//if manual time binning is enabled
				default:
					$selectx="DATE_ADD( MIN(fRunStart), INTERVAL Round( TIME_TO_SEC(TIMEDIFF(MAX(fRunStop),MIN(fRunStart)))/2) SECOND  ) ";
					$titlex="Manual Binning";
					$unitx=" $Timebin min Bins ";
					$mintime=1;
					$groupby="";
					break;
			}

			$properties=[$selectx, $titlex, $unitx, $mintime, $groupby];

			return $properties;
}

?>
