function date_recueil(checkbox)
{
		var row=document.getElementById('row_date_recueil');
		if(checkbox.checked==true)
		{
				row.style.display="table-row";
				
		}
		else
		{
				row.style.display="none";
		}
}
