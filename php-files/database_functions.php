<?php

	//libary containing various query templates
	include "query_library.php";



	/*connects to a given database factdata using information from the db_config.php file 
	Database object gets stored globally for later used.
	If not userdefined , the factdata database will be used.
	Returns true if successfull otherwise false.
	*/
	function connect_database($dbname){
			include "db_config.php";
			$GLOBALS["dbname"]=$dbname;
			
			// Create connection
			$GLOBALS['db'] = mysqli_connect($servername, $username, $password, $GLOBALS["dbname"]);

			// Check connection
			if (!$GLOBALS['db']) {
			    die("Connection failed: " . mysqli_connect_error());
			    $GLOBALS['db_connected'] = 0;
			    return false;

			}
			$GLOBALS['db_connected']=true;
			return true;
	};


	/*returns Sourcename, Sourcekey and Rightassection as array of a source using query1. 
	Neccesarry for seasonal binning.*/
	function get_all_sources(){

		if ($GLOBALS['db_connected']==false){return;}

		$error = "";
		$sources = array();
		echo implode($sources);
		$result1 = 	$GLOBALS['db']->query(query1());
		if ($result1){

	    	while ($row = $result1->fetch_assoc()){

	            $sources[$row['k']] = $row;
	    	}
	    	$result1->free();

	    	return $sources;
		}else{

	    	$error .= 	$GLOBALS['db']->error;
	    	return;
		} 

	};

	/* Retruns as string containing HTML code with sourcename and sourcekey. 
	  Query1 was used to get the information. This string will be used to display
	 the sources available to the user in a select menu.
	*/
	function get_sourceoptions(){

		if ($GLOBALS['db_connected']==false){return;}

		$error = "";
		$sourceoptions = "";
		$result1 = 	$GLOBALS['db']->query(query1());
		if ($result1){
	    	while ($row = $result1->fetch_assoc()){

	    		//Creats an HTML string
	        	$sourceoptions .= "<option value='".$row['k']."'>".$row['name']."</option>\n";
		    }
	    	$result1->free();
	    	return $sourceoptions;
		}else{
	    	$error .= 	$GLOBALS['db']->error;
	    	return;
		}

	};

	/* returns a string containing all collum names availabe in the database 
	sparated with a comma.	Can be used for displaying collumnames in auto correction (Not implemented)*/
	function get_column_names($table){
		connect_database();

		if ($GLOBALS['db_connected']==false){return;}
		$database=$GLOBALS["dbname"]; //temporary variable
 
		$error = "";
		$columns = "";

		$result2 = $GLOBALS['db']->query(query2($database,$table));
		if ($result2)
		{
		    while ($row = $result2->fetch_assoc())
		        $columns .= '"'.$row['COLUMN_NAME'].'",'."\n";
		    $result2->free();
		    return $columns;
		}
		return "none";

	};

/* This function will convert given information so it can be used to crate a SQL Data array. The Data entries for a single source are created in  get_data_for_source(). An array with information for all sources will be returned by get_Data_From_Database(). The roundtrip time for the creation of the whole array is measured (equal to the roundtrip time of the query for all sources)*/
function get_Data_From_Database($X_Axis_Properties, $Y_Axis_Properties, $table, $Begin, $End, $good, $Cut, $sqlhaving, $Noerr, $Sources, $Ontime, $Timebin, $sqlLimit, $oneNight){

	$selectx=$X_Axis_Properties[0];
	$titlex=$X_Axis_Properties[1];
	$unitx=$X_Axis_Properties[2];
	$groupbyX=$X_Axis_Properties[4];

	if($Ontime!=""){
		$mintime=$Ontime;
	}else{
		$mintime=$X_Axis_Properties[3];
	}

	$selecty=$Y_Axis_Properties[0];
	$titley=$Y_Axis_Properties[1];
	$unity=$Y_Axis_Properties[2];
	$isnull=$Y_Axis_Properties[3];
	$selecterr=$Y_Axis_Properties[4];
	$groupbyY=$Y_Axis_Properties[5];

	if(!empty($oneNight)){ 
		/*$yesterday=date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $oneNight) ) ));
		$sqlbegin= "AND fRunStart>= ('$yesterday 18:00:00')"; 
		$sqlend="AND fRunStop<=('$oneNight 06:00:00')";*/
		$yesterday=date('Ymd',(strtotime ( $oneNight)  ));
		$sqlbegin="AND fNight='$yesterday'";
		$sqlend="";

	}else{
		$sqlbegin  = !empty($Begin) ? "AND (DATE(fNight)>='".$Begin."')" : "";
		$sqlend    = !empty($End)   ? "AND (DATE(fNight)<='".$End."')" : "";
	}

	$sqlcut    = !empty($Cut)   ? "AND (".$Cut.") " : "";

	

	//Measurs roundtime for sql query
	$timeStart = microtime(true);

	$result= array();

	//Sourcename used so it can be displayed in the chart later
	$sourcenames=get_all_sources();

	//selects datasets for each source, saves each dataset as an entry in an array together with the name of the source 
	foreach ($Sources as $sourcekey){

		$result[]=[$sourcenames[$sourcekey]["name"], get_data_for_source($selectx, $selecty, $selecterr, $table, $sqlbegin, $sqlend, $isnull, $good, $sqlcut, $mintime, $sqlhaving, $sourcekey, $groupbyX, $groupbyY, $Timebin, $titlex, $sqlLimit)];
	}


	$GLOBALS['timeMeasured'] = number_format((microtime(true)-$timeStart)*1000, 2, ".", "");

	return $result;

}






/* This function get the data array for a single source. Depending on the timebin (or no timebinning) a different query is used. The data from the database will then be converted into an array by process_answer(). In case of errors only the error is returned.*/
function get_data_for_source($selectx, $selecty, $selecterr, $table, $sqlbegin, $sqlend, $isnull, $good, $sqlcut, $mintime, $sqlhaving, $sourcekey, $groupbyX, $groupbyY,$Timebin, $titlex, $sqlLimit){

		if((gettype($Timebin)!="string") && ($Timebin<1440) && ($titlex=="Time") ){
			//small timebins
		$GLOBALS['themp_query']= query4($selectx, $selecty, $selecterr, $table, $sqlbegin, $sqlend, $isnull, $good, $sqlcut, $mintime, $sqlhaving, $sourcekey, $groupbyX, $groupbyY, $Timebin, $sqlLimit);

		}elseif((gettype($Timebin)!="string")&& ($Timebin>1440) && ($titlex=="Time")){
			//large timebins
		$GLOBALS['themp_query']=  query5($selectx, $selecty, $selecterr, $table, $sqlbegin, $sqlend, $isnull, $good, $sqlcut, $mintime, $sqlhaving, $sourcekey, $groupbyX, $groupbyY, $Timebin,$sqlLimit);

		}else{
		//normal
		$GLOBALS['themp_query']= query3($selectx, $selecty, $selecterr, $table, $sqlbegin, $sqlend, $isnull, $good, $sqlcut, $mintime, $sqlhaving, $sourcekey, $groupbyX, $groupbyY,$sqlLimit);

		}
		//sending the query to the database and reciving an answer
		$answer = $GLOBALS['db']->query($GLOBALS['themp_query']);


		$thmp=array();

		//If the query diden't creat erros: bringing the answer into a usable shape 
		if ($answer){

		
		    $thmp=process_answer($answer);

		    $answer->free();
			return $thmp;
		}

		$error="";
		$error .= $GLOBALS['db']->error;
		return $error;

}


/* Processes the answer for all three different queries. Creates an array of arrays.*/
function process_answer($answer){
		$thmp=array();

		while ($row = $answer->fetch_assoc()){

		    $x 	  = $row['x'];
			$y    =  number_format($row['y'],2) ;
			$s    = number_format(round($row['s'], 2),2);
			$sr   = number_format(round($row['s']/sqrt($row['ontime']/60), 1),2);
			$time = $t>60 ? round($row['ontime']/60,2).' h' : round($row['ontime'],1).' min';
			$start= $row['start'];
			$stop = $row['stop'];
			$zdmin= number_format($row['zdmin'],2);
			$zdmax= number_format($row['zdmax'],2);
			$azmin= number_format($row['azmin'],2);
			$azmax= number_format($row['azmax'],2);
			$thmin= number_format($row['thmin'],2);
			$thmax= number_format($row['thmax'],2);
			$err_excessevents=number_format($row['e'],2);

			$entry=[$x, $y, $start, $stop, $s,$sr, $time , $zdmin, $zdmax,$azmin, $azmax,$thmin, $thmax, $err_excessevents];
			$thmp[]=$entry;
		          
		        
		   }     
		 return $thmp;
}




?>
