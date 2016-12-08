//Ensures Tab funktion of Menu
opentab("plots")
function opentab(tabname) {
   var i;
    var x = document.getElementsByClassName("tab");
    for (i = 0; i < x.length; i++) {
       x[i].style.display = "none";  	
    }
    document.getElementById(tabname).style.display = "block";  
};

//chechs for input in Textfield with Id 'id'
function validateInput(id){ 
	var element=document.getElementById(id);
	alert('test');	
	if(element.value ==''){
		
		alert('Field is requiered!');	
		return false;
		
	}
	return true;

};








