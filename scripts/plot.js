function drawChart(data_from_Server, x_axis_info, y_axis_info, noerr) {



	//sets title, layout and data if serverresonse positiv. Else Shows SQL Error.
	if(!(data_from_Server[0][1] instanceof Array)){

		$error="<font color='red'>There has been an SQL Error: Please check your SQL Request.</br>"+ data_from_Server[0][1]+"</font>";
		document.getElementById("chart_div").innerHTML= $error;

	}else{
		var data=get_plottable_data(data_from_Server,x_axis_info,y_axis_info, noerr);

		var title=  y_axis_info[0]+ " vs "+ x_axis_info[0];



		//Sets layout for behavior and axis of chart
		var layout = {
		hovermode:'closest',	
		  title:title,
		  xaxis: {
		    title: x_axis_info[0] + " in "+x_axis_info[1] ,
		    titlefont: {
		      size: 14,
		      color: '#7f7f7f'
		    }
		  },
		  yaxis: {
		    title: y_axis_info[0] + " in "+y_axis_info[1] ,
		    titlefont: {
		      size: 14,
		      color: '#7f7f7f'
		    }
		  }
		};

		//removes options from built in Plotly Modebar
		var remove_from_Modebar={
			modeBarButtonsToRemove: ['sendDataToCloud',
									'select2d',
									'lasso2d',
									]
		}

		//clears plot area of errors before plotting
		document.getElementById("chart_div").innerHTML= "";

		//plots the data in the html part called 'chart_div'
		Plotly.newPlot('chart_div', data, layout,remove_from_Modebar);
	}

}


//returns a data object usable by plotly 
function get_plottable_data(data_from_Server,x_axis_info,y_axis_info, noerr){

	var data=[];

	//for each source creats a thisdata object, that will be added to data
	//enables plotting of different series in same chart
	for (var i=0; i<data_from_Server.length; i++) {

			var x=[];
			var y=[];

			var err_x_min=[];
			var err_x_max=[];

			var err_y_min=[];
			var err_y_max=[];

			//will hold additinal information
			var datapoint_info=[];

			//will draw a empty graph if failed
			if (data_from_Server[i][1] instanceof Array){


					//identifies which collum of data_from_Server[i][1][j] has to be used for errorbars
					var err_x_index=get_err_x_index(x_axis_info);
					var err_y_index=get_err_y_index(y_axis_info);

					//formats data from the server to an array and splitting up x and y for each source
					//because the dataformat of plotly demands two seperate arrays
					for (var j=0; j<data_from_Server[i][1].length; j++) {

					
						x.push(					data_from_Server[i][1][j][0] 	);
					
						y.push(					data_from_Server[i][1][j][1]	);

						//if Noerr ==1 , so "No Erros" selected in menu no errorbars will be displayed because err_y/x_min/max will be empty 
						if(noerr !=1){
							if(y_axis_info[0]=="Time"){
								err_x_min.push(			x[j]-	data_from_Server[i][1][j][err_x_index[0]]	);
								err_x_max.push(			data_from_Server[i][1][j][err_x_index[1]]	- x[j]	);	
							}	
							//The error of excess events is different from other errors as it is symmetric and already a differenz 
                            if(y_axis_info[0]=="Excess Rate"){

                                err_y_min.push(                 data_from_Server[i][1][j][13]       ); 
                               	err_y_max.push(                 data_from_Server[i][1][j][13]       );  
                               	    
                            }else if(y_axis_info[0]!= "Significance" || y_axis_info[0]!= "Signal Rate" ){
                                err_y_min.push(                 y[j]-   data_from_Server[i][1][j][err_y_index[0]]       );
                                err_y_max.push(                 data_from_Server[i][1][j][err_y_index[1]]       -y[j]   );
                            }

						}

						datapoint_info.push(get_datapoint_info(		data_from_Server[i][1][j])		);
					}
			}
			var thisdata = 
				  {
	    			x: x,
	    			y: y,
	    			error_x:{
	    				type:"data",
	    				symmetric:false,
	    				array: err_x_max,
	    				arrayminus: err_x_min,

	    			},
	    			error_y:{
	    				type:"data",
	    				symmetric:false,
	    				array: err_y_max,
	    				arrayminus: err_y_min,

	    			},
	    			text: datapoint_info,
	  				mode: 'markers',
				    type: 'scatter',
	   				name: data_from_Server[i][0],

				  }

			data.push(thisdata);
	}
	return data;
}


//sets the information to be displayed when hovering over a datapoint, returns a string
function get_datapoint_info(dataset){
	var logged=document.getElementById("logged").value;
	var info="\n";
	var i=2;

	info= info+"Start="+dataset[i++];
	info= info+"\nStop="+dataset[i++];

	if(logged=="yes"){
		info= info+"\nS="+dataset[i++]+"  Lima(sig events/bg events) ";
		info= info+"\nSr="+dataset[i++]+"  Lima(sig events/bg events)/s^(1/2) ";
		info= info+"\nontime="+dataset[i++];
		info= info+"\nZd="+dataset[i++]+" - "+dataset[i++];
		info= info+"\nAz="+dataset[i++]+" - "+dataset[i++];
		info= info+"\nTh="+dataset[i++]+" - "+dataset[i++];
	}
	return info;
}


function get_err_x_index(x_axis_info){

	if(x_axis_info[0]=="Time"){

		return [2,3]; //refers to values $start and $stop in get_data_for_source()

	}else if(x_axis_info[0]=="Zenith Distance"){

		return [7,8]; //refers to $zdmin and $zdmax in get_data_for_source()

	}else if(x_axis_info[0]=="Threshold"){

		return [11,12]; //refers to $thmin and $thmax in get_data_for_source()

	}else{
		return [0,0]; //No Errors
	}


}



function get_err_y_index(y_axis_info){


	if(y_axis_info[0]=="Background events"){

		return [1,1]; 
	}else if(y_axis_info[0]=="Threshold"){

		return [11,12]; //refers to $thmin and $thmax in get_data_for_source()

	}else if(y_axis_info[0]=="Zenith Distance"){

			return [7,8]; //refers to $zdmin and $zdmax in get_data_for_source()

	}else{

		return [1,1]; //No errors
	}



}