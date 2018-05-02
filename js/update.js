//----------------------------------------------------------------

//used in update_forms functions 



function hide_field(id)
{
        var file=document.getElementById(id);
        file.setAttribute('type','hidden');
}
function show_name_field(id)
{
	var name_field=document.getElementById(id);
    name_field.setAttribute('type','name');
}
function show_file_field(id)
{
        var file=document.getElementById(id);
        file.setAttribute('type','file');
}
function update_name_field(select,name_id)
{
        if(select.value=='other')
        {
                show_name_field(name_id);
        }
        else
        {
                hide_field(name_id);
        }
}
function update_file_field(select,file_id)
{
        if(select.value=='true')
        {
                show_file_field(file_id);
        }
        else
        {
                hide_field(file_id);
        }
}



//------------------------------------------------------


//function function list_groupes_textes($selected)
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

//used in update_piece_form

function hide_file_field(id)
{
        var file=document.getElementById(id);
        file.setAttribute('type','hidden');
}
function show_file_field(id)
{
        var file=document.getElementById(id);
        file.setAttribute('type','file');
}
function update_file_field(select,file_id)
{
        if(select.value=='true')
        {
                show_file_field(file_id);
        }
        else
        {
                hide_file_field(file_id);
        }
}
function verify()
{
    var id_recueil=document.getElementById('id_recueil').value;
    var old_recueil=document.getElementById('old_recueil').value;
    if(id_recueil != old_recueil)
    {
	    if(confirm("Vous êtes sur le point de déplacer la pièce, êtes-vous sûr de vouloir continuer?"))
	    {
		    document.forms[0].submit();
	    }
	    else
	    {
		    document.location='show.php?id_base=<?=$_GET["id_base"]?>&amp;id_recueil=<?=$_GET["id_recueil"]?>';
	    }
    }
    else
    {
	    document.forms[0].submit();
    }
}






//-----------------------------------------------------------------------------



