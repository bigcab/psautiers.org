
function update_champ(step)
{
	document.getElementById('melodie').value +='' + step + '/';
	update_partition();
}
function update_partition()
{
	// Actualisation en temps réel de la partition, pour que l'utilisateur
	// voie ce qu'il tape
	image = document.getElementById('part_image');
	data = document.getElementById('melodie').value;
        
        clef_line=document.getElementById('clef_line').value;
        clef_sign=document.getElementById('clef_sign').value;
	image.src="afficher_partition.php?clef_sign="+ clef_sign +"&clef_line="+ clef_line +"&data="+data;
}
function effacer()
{
	champ = document.getElementById('melodie');
	value=champ.value;
	value=value.substr(0,value.length-1);
	value=value.substr(0,value.lastIndexOf("/")+1);
	champ.value = value;
	
	
	update_partition();
}
