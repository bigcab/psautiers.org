





//----------------------------------------------------------
//for list_groupes_textes function 

function update_groupe_texte(select)
{
	var nom_groupe_texte=document.getElementById('nom_groupe_texte');
	if (select.value!='other')
	{
		
		nom_groupe_texte.setAttribute("type","hidden");
	}
	else
	{
		nom_groupe_texte.setAttribute("type","text");
	}
}


//--------------------------------------------------------
//these function are used in function add_piece_form 

function update_champ()
{
        var has_music_xml_true=document.getElementById('has_musicxml_true').checked;

        
        if (has_music_xml_true == true)
        {
                show_row('fichier_musicxml');
                hide_row('note_finale');
                hide_row('ambitus');
                hide_row('armure');
                hide_row('cles');
                hide_row('nombre_parties');
                hide_row('incipit');
        }
        if (has_music_xml_true == false)
        {
                hide_row('fichier_musicxml');
                show_row('note_finale');
                show_row('ambitus');
                show_row('armure');
                show_row('cles');
                show_row('nombre_parties');  
                show_row('incipit');        
        }
}

document.onload=function (){update_champ()};


function check_page()
{
	var x=document.getElementById('page');
	if(x.value!='')
	{
		document.forms[0].submit();
	}
	else
	{
		alert("Veuillez remplir le champ page (il est obligatoire)");
	}
}




//-----------------------------------------------------------------------------------
//function for show_piece


function change_page(value,id_piece)
{
	var img=document.getElementById('image_piece');
	img.src="image.php?image_type=png_lilypond&id_piece="+id_piece+"&page="+value;
};
