function hide_div(div)
{
	div.setAttribute("style","display : none;");
}


function show_div(div)
{
	div.setAttribute("style","display: block;");
}

function change_div(sign,id,id_piece)
{
	//var measure_number=document.getElementById(id+'_measure_number').value;
	var measure_number=document.getElementById('measure_number').value;
	hide_div(document.getElementById(id+'_measure_'+measure_number));
	measure_number=parseInt(measure_number);
	measure_number+= sign ;
	show_div(document.getElementById(id+'_measure_'+measure_number));
	
	var hist=document.getElementById('hist').value;
	
	if(hist==1)
	{
	    
	    var img=document.getElementById('preload');
	    img.src='affich_histogram.php?id_piece='+id_piece+'&measure='+ measure_number;
	    
	}
	else
	{
	    
	    var img=document.getElementById('preload');
	    var string=tableau_measure_strings[measure_number-1];
	    img.src='fast_histogram.php?'+string;
	    
	}
	
	//document.getElementById(id+'_measure_number').value=measure_number;
	//alert(measure_number);
}

function change_value(sign)
{
	var measure_number=document.getElementById('measure_number').value;
	measure_number=parseInt(measure_number);
	measure_number+= sign ;
	document.getElementById('measure_number').value=measure_number;
}



//used in fun choose_fragment_form()


function update_measure_end()
{
	var measure_start=document.getElementById("measure_start");
	var measure_end=document.getElementById("measure_end");
	var start=measure_start.value;
	
}
function update_measure_start()
{
	var measure_start=document.getElementById("measure_start");
	var measure_end=document.getElementById("measure_end");
	
}
