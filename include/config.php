<?php









// Définitions des id's pour la BDD (à remplacer par des identifiants corrects, bien sûr)
define('MYSQL_HOST','name of the host');
define('MYSQL_USER','name of the user');
define('MYSQL_PASSWORD','password of the user');
define('MYSQL_DB','ppiv'); // database name


define('XML_CFG','include/cfg_fields.xml');
//	Cette fonction ouvre un fichier xml et le loade
//	elle nous évite de refaire à chaque fois la même chose 
//	(open et apres load_xml);
//	fonction marche à 100%




#variable importante pour vérifier les pages de developpements



#fonction importantes pour initialiser les vars et éviter les erreur dans les logs
function init_post_var($name)
{
	if(!isset($_POST[$name]))
	{
		$_POST[$name]="";
	}
}
function init_get_var($name)
{
	if(!isset($_GET[$name]))
	{
		$_GET[$name]="";
	}
}


#$dev_server_bool=FALSE;
$dev_server_bool=TRUE;
$xml_doc=openload_xml(XML_CFG);
	


function openload_xml($fichier)
{
	$xml_doc= new DomDocument();
	$xml_doc->load($fichier);
	return $xml_doc;
}


function list_modes()
{
    global $xml_doc;
	$array=array();
	foreach($xml_doc->getElementsByTagName("mode") as $mode)
	{
		array_push($array,$mode->getAttribute("id"));
	}
	return $array;
}

function get_traduction_directory($mode_name)
{
    global $xml_doc;
    $mode_node=$xml_doc->getElementById($mode_name);
    if ($mode_node===NULL)
    {
        return NULL;
    }
    return $mode_node->getAttribute("traduction_directory");
}



#we don't use these functions anymore
function get_traduction($mode_name,$option /*base or recueil or piece*/,$name,$default="" )
{
    global $xml_doc;
	/*j'y vais directement pour trouver les champs à afficher*/
	$mode_node=$xml_doc->getElementById($mode_name);
	$op=$mode_node->getElementsByTagName($option)->item(0);
	switch ($name)
	{
	    case "base":
	        $out=$op->getAttribute("traduction");
	        return (empty($out))?(_("base")):($out);
	    break;
	    
	    case "recueil":
	        $out=$op->getAttribute("traduction");
	        return (empty($out))?(_("recueil")):($out);
	    break;
	    
	    case "piece":
	        $out=$op->getAttribute("traduction");
	        return (empty($out))?(_("pièce")):($out);
	    break;
	    
	    default :
	        $fields=$op->getElementsByTagName("field");
	        foreach($fields as $field)
	        {
	            if($field->getAttribute("name")==$name)
	            {
	                $trad=$field->getAttribute("traduction");
	                return (empty($trad))?($default):($trad);
	            }
	        }
	        return $default;        
	}
	
}

//callback is in form: "name"=> array("name_of_callback","single_argument")

function output_show_form($args/*table containing argument (from sql req)*/,$mode,$option /*add or update or info*/,$field /*base/recueil/piece*/)
{
	global $add_base_form_show_callback;
	global $update_base_form_show_callback;
	global $add_recueil_form_show_callback;
	global $update_recueil_form_show_callback;
	global $add_piece_form_show_callback;
	global $update_piece_form_show_callback;
	
	global $info_base_show_callback;
	global $info_recueil_show_callback;
	global $info_piece_show_callback;
	
	
	global $xml_doc;
	$mode_node=$xml_doc->getElementById($mode);
	
	switch ($option)
	{
		case 'add':
			switch ($field)
			{
				case 'base':
					$node=$mode_node->getElementsByTagName("base")->item(0);
					$array_callback=$add_base_form_show_callback;
				break;
				
				case 'recueil':
					$node=$mode_node->getElementsByTagName("recueil")->item(0);
					$array_callback=$add_recueil_form_show_callback;
				break;
				
				case 'piece':
					$node=$mode_node->getElementsByTagName("piece")->item(0);
					$array_callback=$add_piece_form_show_callback;
				break;
			}
		break;
		
		case 'update':
			switch ($field)
			{
				case 'base':
					$node=$mode_node->getElementsByTagName("base")->item(0);
					$array_callback=$update_base_form_show_callback;
				break;
				
				case 'recueil':
					$node=$mode_node->getElementsByTagName("recueil")->item(0);
					$array_callback=$update_recueil_form_show_callback;
				break;
				
				case 'piece':
					$node=$mode_node->getElementsByTagName("piece")->item(0);
					$array_callback=$update_piece_form_show_callback;
				break;
			}
		break;
		
		case "info":
			switch ($field)
			{
				case 'base':
					$node=$mode_node->getElementsByTagName("base")->item(0);
					$array_callback=$info_base_show_callback;
				break;
				
				case 'recueil':
					$node=$mode_node->getElementsByTagName("recueil")->item(0);
					$array_callback=$info_recueil_show_callback;
				break;
				
				case 'piece':
					$node=$mode_node->getElementsByTagName("piece")->item(0);
					$array_callback=$info_piece_show_callback;
				break;
			}
		break;
	}
	
	$champs_node=$node->getElementsByTagName("field");
	$champs=array();
	$trads=array();

	foreach($champs_node as $champ)
	{
		$name=$champ->getAttribute("name");
		array_push($champs,$name);
        #	on update les traductions pour chaque modes 
        #	syntaxe : <field name="titre" traduction="sourate"/>
        $trad=$champ->getAttribute("traduction");
        $trads[$name]=$trad;
	}
	
	foreach($champs as $elem)
	{
		if (isset($array_callback[$elem]))
		{
			$func=$array_callback[$elem][0];
			if(isset($array_callback[$elem][1]))
			{
				$arg=$array_callback[$elem][1];
			}
			else
			{
				$arg="";
			}
			$trad=$trads[$elem];
			if(!is_array($arg))
			{
#				echo $args[$arg];
				if(isset($args[$arg]))
				{
					$arg_value= $args[$arg];
				}
				else
				{
					$arg_value="";
				}
				call_user_func($func,$arg_value /*,$trad*/);
			}
			else
			{
				$arg_array=array();
				foreach($arg as $elem)
				{
					array_push($arg_array,$args[$elem]);
				}
#				array_push($arg_array,$trad);
				call_user_func_array($func,$arg_array);
			}
		}
		
	}
}


$add_base_form_show_callback=
array(
	"nom_base"=> array("add_base_form_show_nom_base",""),
	"description"=>  array("add_base_form_show_description",""),
	"references"=> array("add_base_form_show_references",""),
	"body_background_color" => array("add_base_form_show_body_background_color","body_background_color"),
	"banner" => array("add_base_form_show_banner"),
	"mode" => array("add_base_form_show_mode")
);


$update_base_form_show_callback=
array(
	"nom_base"=> array("add_base_form_show_nom_base","nom_base"),
	"description"=>  array("add_base_form_show_description","description"),
	"references"=> array("add_base_form_show_references","references"),
	"body_background_color" => array("add_base_form_show_body_background_color","body_background_color"),
	"banner" => array("update_base_form_show_banner"),
	"mode" => array("add_base_form_show_mode","mode")
);

$info_base_show_callback=
array(
	"nom_base"=> array("info_base_show_nom_base","nom_base"),
	"description"=>  array("info_base_show_description","description"),
	"references"=> array("info_base_show_references","references"),
	"mode" =>array("info_base_show_mode","mode")
);



$add_recueil_form_show_callback=array(
	"titre"			=> array("add_recueil_form_show_titre",""),
	"titre_uniforme"	=> array("add_recueil_form_show_titre_uniforme",""),
	"abreviation"		=> array("add_recueil_form_show_abreviation",""),
	"image_titre_recueil_jpg"=> array("add_recueil_form_show_image_titre_recueil_jpg",""),
	"image_table_matieres"	=> array("add_recueil_form_show_image_table_matieres",""),
	"imprimeur"		=> array("add_recueil_form_show_imprimeur",""),
	"editeur"		=> array("add_recueil_form_show_editeur",""),
	"adresse_biblio"	=>array( "add_recueil_form_show_adresse_biblio",""),
	"auteur"		=> array("add_recueil_form_show_auteur",""),
	"compositeur"		=> array("add_recueil_form_show_compositeur",""),
	"lieu"			=> array("add_recueil_form_show_lieu",""),
	"solmisation"		=> array("add_recueil_form_show_solmisation",""),
	"timbre"		=> array("add_recueil_form_show_timbre",""),
	"date_impression"	=> array("add_recueil_form_show_date_impression",""),
	"description_materielle"=> array("add_recueil_form_show_description_materielle",""),
	"sources_bibliographiques"=> array("add_recueil_form_show_sources_bibliographiques",""),
	"litterature_secondaire"=> array("add_recueil_form_show_litterature_secondaire",""),
	"comment_public"	=> array("add_recueil_form_show_comment_public",""),
	"comment_reserve"	=> array("add_recueil_form_show_comment_reserve",""),
	"bibliotheque"		=> array("add_recueil_form_show_bibliotheque",""),
	"cote"			=> array("add_recueil_form_show_cote","")
);

$update_recueil_form_show_callback=array(
	"titre"			=> array("add_recueil_form_show_titre","titre"),
	"titre_uniforme"	=> array("add_recueil_form_show_titre_uniforme","titre_uniforme"),
	"abreviation"		=> array("add_recueil_form_show_abreviation","abreviation"),
	"image_titre_recueil_jpg"=> array("update_recueil_form_show_image_titre_recueil_jpg",""),
	"image_table_matieres"	=> array("update_recueil_form_show_image_table_matieres",""),
	"imprimeur"		=> array("add_recueil_form_show_imprimeur","imprimeur"),
	"editeur"		=> array("add_recueil_form_show_editeur","editeur"),
	"adresse_biblio"	=>array( "add_recueil_form_show_adresse_biblio","adresse_biblio"),
	"auteur"		=> array("add_recueil_form_show_auteur","auteur"),
	"compositeur"		=> array("add_recueil_form_show_compositeur","compositeur"),
	"lieu"			=> array("add_recueil_form_show_lieu","lieu"),
	"solmisation"		=> array("add_recueil_form_show_solmisation","solmisation"),
	"timbre"		=> array("add_recueil_form_show_timbre","timbre"),
	"date_impression"	=> array("add_recueil_form_show_date_impression","date_impression"),
	"description_materielle"=> array("add_recueil_form_show_description_materielle","description_materielle"),
	"sources_bibliographiques"=> array("add_recueil_form_show_sources_bibliographiques","sources_bibliographiques"),
	"litterature_secondaire"=> array("add_recueil_form_show_litterature_secondaire","litterature_secondaire"),
	"comment_public"	=> array("add_recueil_form_show_comment_public","comment_public"),
	"comment_reserve"	=> array("add_recueil_form_show_comment_reserve","comment_reserve"),
	"bibliotheque"		=> array("add_recueil_form_show_bibliotheque","bibliotheque"),
	"cote"			=> array("add_recueil_form_show_cote","cote")
);



$info_recueil_show_callback=array(
	"titre"			=> array("info_recueil_show_titre","titre"),
	"titre_uniforme"	=> array("info_recueil_show_titre_uniforme","titre_uniforme"),
	"abreviation"		=> array("info_recueil_show_abreviation","abreviation"),
	"image_titre_recueil_jpg"=> array("info_recueil_show_image_titre_recueil_jpg","id_recueil"),
	"image_table_matieres"	=> array("info_recueil_show_image_table_matieres","id_recueil"),
	"imprimeur"		=> array("info_recueil_show_imprimeur","imprimeur"),
	"editeur"		=> array("info_recueil_show_editeur","editeur"),
	"adresse_biblio"	=>array( "info_recueil_show_adresse_biblio","adresse_biblio"),
	"auteur"		=> array("info_recueil_show_auteur","auteur"),
	"compositeur"		=> array("info_recueil_show_compositeur","compositeur"),
	"lieu"			=> array("info_recueil_show_lieu","lieu"),
	"solmisation"		=> array("info_recueil_show_solmisation","solmisation"),
	"timbre"		=> array("info_recueil_show_timbre","timbre"),
	"date_impression"	=> array("info_recueil_show_date_impression","date_impression"),
	"description_materielle"=> array("info_recueil_show_description_materielle","description_materielle"),
	"sources_bibliographiques"=> array("info_recueil_show_sources_bibliographiques","sources_bibliographiques"),
	"litterature_secondaire"=> array("info_recueil_show_litterature_secondaire","litterature_secondaire"),
	"comment_public"	=> array("info_recueil_show_comment_public","comment_public"),
	"comment_reserve"	=> array("info_recueil_show_comment_reserve","comment_reserve"),
	"bibliotheque"		=> array("info_recueil_show_bibliotheque","bibliotheque"),
	"cote"			=> array("info_recueil_show_cote","cote")
);



$add_piece_form_show_callback=array(
	"page"			=> array("add_piece_form_show_page",""),
	"pagination_ancienne"	=> array("add_piece_form_show_pagination_ancienne",""),
	"titre_piece"		=> array("add_piece_form_show_titre_piece",""),
	"fichier_finale"	=> array("add_piece_form_show_fichier_finale",""),
	"image_jpg"		=> array("add_piece_form_show_image_jpg",""),
	"incipit_jpg"		=> array("add_piece_form_show_incipit_jpg",""),
	"rubrique"		=> array("add_piece_form_show_rubrique",""),
	"musicxml"		=> array("add_piece_form_show_musicxml",""),
	"note_finale"		=> array("add_piece_form_show_note_finale",""),
	"ambitus"		=> array("add_piece_form_show_ambitus",""),
	"armure"		=> array("add_piece_form_show_armure",""),
	"cles"			=> array("add_piece_form_show_cles",""),
	"nombre_parties"	=> array("add_piece_form_show_nombre_parties",""),
	"codage_incipit"	=> array("add_piece_form_show_codage_incipit",""),
	"compositeur"		=> array("add_piece_form_show_compositeur",""),
	"timbre"		=> array("add_piece_form_show_timbre",""),
	"auteur"		=> array("add_piece_form_show_auteur","auteur"),
	"concordances"		=> array("add_piece_form_show_concordances",""),
	"texte_additionnel"	=> array("add_piece_form_show_texte_additionnel",""),
	"groupes_textes"	=> array("add_piece_form_show_groupes_textes",""),
	"biblio_texte"		=> array("add_piece_form_show_biblio_texte",""),
	"comment_public"	=> array("add_piece_form_show_comment_public",""),
	"comment_reserve"	=> array("add_piece_form_show_comment_reserve",""),
	"psaume"            => array("add_piece_form_show_psaume","") 
);


$update_piece_form_show_callback=array(
	"page"			=> array("add_piece_form_show_page","rang"),
	"pagination_ancienne"	=> array("add_piece_form_show_pagination_ancienne","pagination_ancienne"),
	"titre_piece"		=> array("add_piece_form_show_titre_piece","titre_piece"),
	"fichier_finale"	=> array("update_piece_form_show_fichier_finale",""),
	"image_jpg"		=> array("update_piece_form_show_image_jpg",""),
	"incipit_jpg"		=> array("update_piece_form_show_incipit_jpg",""),
	"rubrique"		=> array("add_piece_form_show_rubrique","rubrique"),
	"musicxml"		=> array("update_piece_form_show_musicxml",""),
	"note_finale"		=> array("add_piece_form_show_note_finale","note_finale"),
	"ambitus"		=> array("add_piece_form_show_ambitus","ambitus"),
	"armure"		=> array("add_piece_form_show_armure","armure"),
	"cles"			=> array("add_piece_form_show_cles","cles"),
	"nombre_parties"	=> array("add_piece_form_show_nombre_parties","nombre_parties"),
	"codage_incipit"	=> array("add_piece_form_show_codage_incipit","codage_incipit"),
	"compositeur"		=> array("add_piece_form_show_compositeur","compositeur"),
	"timbre"		=> array("add_piece_form_show_timbre","compositeur"),
	"auteur"		=> array("add_piece_form_show_auteur","auteur"),
	"concordances"		=> array("add_piece_form_show_concordances","concordances"),
	"texte_additionnel"	=> array("add_piece_form_show_texte_additionnel","texte_additionnel"),
	"groupes_textes"	=> array("update_piece_form_show_groupes_textes","id_groupe_texte"),
	"biblio_texte"		=> array("add_piece_form_show_biblio_texte","biblio_texte"),
	"comment_public"	=> array("add_piece_form_show_comment_public","comment_public")
#	"comment_reserve"	=> array("add_piece_form_show_comment_reserve","comment_reserve"),
#    "psaume"                => array("add_piece_form_show_psaume","psaume") 
);




$info_piece_show_callback=array(
	"page"			=> array("info_piece_show_page","rang"),
	"titre_piece"		=> array("info_piece_show_titre_piece","titre_piece"),
	"pagination_ancienne"	=> array("info_piece_show_pagination_ancienne","pagination_ancienne"),
	//"fichier_finale"	=> array("info_piece_show_fichier_finale",""),
	"fichier_finale"	=> array("nothing",""),
	//"image_jpg"		=> array("info_piece_show_image_jpg",""),
	"image_jpg"		=> array("nothing",""),
	//"incipit_jpg"		=> array("info_piece_show_incipit_jpg",""),
	"incipit_jpg"		=> array("nothing",""),
	"rubrique"		=> array("info_piece_show_rubrique","rubrique"),
	//"musicxml"		=> array("info_piece_show_musicxml",""),
	"musicxml"	=> array("nothing",""),
	"note_finale"		=> array("info_piece_show_note_finale","note_finale"),
	"ambitus"		=> array("info_piece_show_ambitus","ambitus"),
	"armure"		=> array("info_piece_show_armure","armure"),
	"cles"			=> array("info_piece_show_cles","cles"),
	"nombre_parties"	=> array("info_piece_show_nombre_parties","nombre_parties"),
	//"codage_incipit"	=> array("info_piece_show_codage_incipit","codage_incipit"),
	"codage_incipit"	=> array("nothing",""),
	"compositeur"		=> array("info_piece_show_compositeur","compositeur"),
	"timbre"		=> array("info_piece_show_timbre","timbre"),
	"auteur"		=> array("info_piece_show_auteur","auteur"),
	"concordances"		=> array("info_piece_show_concordances","concordances"),
	"texte_additionnel"	=> array("info_piece_show_texte_additionnel","texte_additionnel"),
	"groupes_textes"	=> array("info_piece_show_groupes_textes",array("nom_groupe_texte","fichier_xml","req")),
	"biblio_texte"		=> array("info_piece_show_biblio_texte",array("biblio_texte","fichier_xml","req")),
	"comment_public"	=> array("info_piece_show_comment_public","comment_public"),
	"comment_reserve"	=> array("info_piece_show_comment_reserve",array("comment_reserve","nom_auteur_fiche")),
	"psaume"            => array("info_piece_show_psaume","psaume")
);


function nothing($test="")
{	
	return ;
}
/**********************************Begin of functions ****************************/




/*Function for add_base_form in update and in show.php and for people who want to filter some fields*/

function add_base_form_show_nom_base($nom="",$trad="")
{
	?>
	<tr>
                <td><?=(empty($trad))?(_("Nom")):(ucfirst($trad)) ?></td>
                <td><input type="text" size="75" name="nom_base" value="<?=$nom?>"/></td>
        </tr>
        <?php
}
function add_base_form_show_description($description="",$trad="")
{
	?>
	<tr>
                <td><?=(empty($trad))?(_("Description")):(ucfirst($trad))?></td>
                <td><input type="text" size="75" name="description" value="<?=$description?>"/></td>
        </tr>   
	<?php
}

function add_base_form_show_references($ref="",$trad="")
{
	?>
	<tr>
                <td><?=(empty($trad))?(_("Références")):(ucfirst($trad))?></td>
                <td><input type="text" size="75" name="references" value="<?=$ref?>"/></td>
        </tr>
	<?php
}

function int_selector($selected,$name)
{
	?>
	<select name="<?=$name?>">
		<?php
		for ($i = 0 ; $i< 256 ; $i++ )
		{
			if( $i == $selected )
			{
				?>
				<option selected='selected' value="<?=$selected?>"><?=$i?></option>
				<?php
			}
			else
			{
				?>
				<option value="<?=$i?>" ><?=$i?></option>
				<?php
			}
		}
		?>
	</select>
	<?php
}

function add_base_form_show_body_background_color($color="rgb(204,220,255)",$trad="")
{
	preg_match_all("/rgb\(([0-9]*),([0-9]*),([0-9]*)\)/",$color,$match);
	//print_r($match);
	$r=$match[1][0];
	$g=$match[2][0];
	$b=$match[3][0];
	?>
	<tr>
                <td><?=(empty($trad))?(_("Couleur de fond")):(ucfirst($trad))?></td>
                <td>
                        R : <? int_selector($r,"r") ?>
                        G : <? int_selector($g,"g") ?>
                        B : <? int_selector($b,"b") ?>
                </td>
                
    </tr>
	<?php
}

function add_base_form_show_banner($arg="",$trad="")
{


	?>
	<tr>
            <td><?=(empty($trad))?(_("Banner")):(ucfirst($trad))?></td>
            <td>
           		<!--
				Script pour cacher/afficher les champs pour uploader les fichiers
				images
				This script will hide/show the field to reupload the files
				-->
				<script language="Javascript" type="text/javascript"><!--
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
				
				--> </script>
            
            
            
                    <select onchange="update_file_field(this,'banner')" name="reup_banner">
                            <option value="true"><?=_("Nouveau banner")?></option>
                            <option selected='selected' value="false"><?=_("Par défaut")?></option>
                    </select>
                    <input id="banner" type="hidden" name="banner"/>
            </td>
    </tr>
    <?php
}


function add_base_form_show_mode($mode_selected="default",$trad="")
{
        
    ?>
    <tr>
        <td><?=_("Mode")?></td>
        <td>
            <select name="mode">
                <?php
                global $xml_doc;
                $modes=$xml_doc->getElementsByTagName("mode");
                foreach($modes as $mode)
                {
                    $name=$mode->getAttribute("id");
                    ?>
                    <option value="<?=$name?>" <?=($mode_selected==$name)?("selected='selected'"):("")?>><?=$name?></option>
                    <?php
                }
                ?>
            </select>
        </td>
    </tr>
    <?php
}

/*-------------------------------end add_base_form_shows-------------------*/


/*-------------------------------begin update_base_form_shows---------------*/


function update_base_form_show_banner($arg="",$trad="")
{
	?>
	<tr>
            <td><?=(empty($trad))?(_("Banner")):(ucfirst($trad))?></td>
            <td>
                    <select onchange="update_file_field(this,'banner')" name="reup_banner">
                            <option value="true"><?=_("Nouveau banner")?></option>
                            <option selected='selected' value="false"><?=_("Garder le banner")?></option>
                    </select>
                    <input id="banner" type="hidden" name="banner"/>
            </td>
    </tr>
    <?php
}

/*--------------------------------end update_base_form_shows ---------------*/

/*--------------------------------begin info_base_shows---------------------*/


function info_base_show_nom_base ($nom_base,$trad="")
{
	?>
	<tr>
		<th><?=(empty($trad))?(_("Nom")):(ucfirst($trad))?></th>
		<td><?=$nom_base?></td>
	</tr>
	<?php
}

function info_base_show_description ($description,$trad="")
{
	?>
	<tr>
		<th><?=(empty($trad))?(_("Description")):(ucfirst($trad))?></th>
		<td><?=$description?></td>
	</tr>
	<?php
}


function info_base_show_references ($references,$trad="")
{
	?>
	<tr>
		<th><?=(empty($trad))?(_("References")):(ucfirst($trad))?></th>
		<td><?=$references?></td>
	</tr>
	<?php
}

function info_base_show_mode ($mode,$trad="")
{
	?>
	<tr>
		<th><?=(empty($trad))?(_("Mode")):(ucfirst($trad))?></th>
		<td><?=$mode?></td>
	</tr>
	<?php
}

/*--------------------------------end info_base_shows-------------------------*/


/*---------------------------------add_recueil_form_shows begin*/

function add_recueil_form_show_titre($titre="",$trad="")
{
	?>
	<tr>
                <td><?=(empty($trad))?(_("Titre")):(ucfirst($trad))?>*</td>
                <td><input type="text" size="75" name="titre" value="<?=$titre?>"/></td>
        </tr>
	<?php
}
function add_recueil_form_show_titre_uniforme ($titre_uniforme="",$trad="")
{
	?>
 	<tr>
                <td><?=(empty($trad))?(_("Titre uniforme")):(ucfirst($trad))?></td>
                <td><input type="text" size="75" name="titre_uniforme" value="<?=$titre_uniforme?>"/></td>
        </tr>
        <?php
}
function add_recueil_form_show_abreviation ($abreviation="",$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Abréviation")):(ucfirst($trad))?></td>
                <td><input type="text" size="75" name="abreviation" value="<?=$abreviation?>"/></td>
        </tr>
        <?php
}
function add_recueil_form_show_image_titre_recueil_jpg ($arg="",$trad="")
{
	?>      
        <tr>
                <td><?=(empty($trad))?(_("Image de la page de titre")):(ucfirst($trad))?></td>
                <td><input type="file" name="image_titre_recueil_jpg"/></td>
        </tr>
        <?php
}
function add_recueil_form_show_image_table_matieres ($arg="",$trad="")
{
	?>      
        <tr>
                <td><?=(empty($trad))?(_("Image de la table des matières")):(ucfirst($trad))?></td>
                <td><input type="file" name="image_table_matieres"/></td>
        </tr>
        <?php
}


function add_recueil_form_show_imprimeur($imprimeur="",$trad="")
{
	?>
	<tr>
                <td><?=(empty($trad))?(_("Imprimeur")):(ucfirst($trad))?></td>
                <td><input type="text" size="75" name="imprimeur" value="<?=$imprimeur?>"/></td>
        </tr>
        <?php
}

function add_recueil_form_show_editeur ($editeur="",$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Editeur")):(ucfirst($trad))?></td>
        	<td><input type="text" size="75" name="editeur" value="<?=$editeur?>"/></td>
        </tr>
        <?php
}
function add_recueil_form_show_adresse_biblio ($adresse_biblio="",$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Adresse Bibliographique")):(ucfirst($trad))?></td>
        	<td><input type="text"  size="75" name="adresse_biblio" value="<?=$adresse_biblio?>"/></td>
        </tr>
        <?php
}
function add_recueil_form_show_auteur ($auteur="",$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Auteur")):(ucfirst($trad))?></td>
        	<td><input type="text" size="75"  name="auteur" value="<?=$auteur?>"/></td>
        </tr>
        <?php
}


function add_recueil_form_show_compositeur ($compositeur="",$trad="")
{       
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Compositeur")):(ucfirst($trad))?></td>
        	<td><input type="text"  size="75" name="compositeur" value="<?=$compositeur?>"/></td>
        </tr>
        <?php
}

function add_recueil_form_show_lieu ($lieu="",$trad="")
{       
	?>        
        <tr>
                <td><?=(empty($trad))?(_("Lieu")):(ucfirst($trad))?></td>
                <td><input type="text" size="75" name="lieu" value="<?=$lieu?>"/></td>
        </tr>
        <?php
}



function add_recueil_form_show_solmisation ($solmisation="",$trad="")
{       
	?>       
        <tr>
                <td><?=(empty($trad))?(_("Solmisation")):(ucfirst($trad))?></td>
                <td><input type="checkbox" name="solmisation" value="<?=$solmisation;?>"/></td>
        </tr>
        <?php
}
function add_recueil_form_show_timbre ($timbre="",$trad="")
{       
	?>
        <tr>
                <td><?=(empty($trad))?(_("Timbres")):(ucfirst($trad))?></td>
                <td><input type="checkbox"  name="timbre" value="<?=$timbre?>"/></td>
        </tr>
        <?php
}
function add_recueil_form_show_date_impression ($date_impression="",$trad="")
{       
	?>        
        <tr>
                <td><?=(empty($trad))?(_("Date d'impression")):(ucfirst($trad))?></td>
                <td><input type="text" size="75" name="date_impression" value="<?=$date_impression?>"/></td>
        </tr>
        <?php
}
function add_recueil_form_show_description_materielle ($description_materielle="",$trad="")
{       
	?> 
        <tr>
        	<td><?=(empty($trad))?(_("Description matérielle")):(ucfirst($trad))?></td>
        	<td><input type="text"  size="75" name="description_materielle" value="<?=$description_materielle?>"/></td>
        </tr>
        <?php
}
function add_recueil_form_show_sources_bibliographiques ($sources_bibliographiques="",$trad="")
{       
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Sources Bibliographiques")):(ucfirst($trad))?></td>
        	<td><input type="text"  size="75" name="sources_bibliographiques"  value="<?=$sources_bibliographiques?>"/></td>
        </tr>
        <?php
}
function add_recueil_form_show_litterature_secondaire ($litterature_secondaire="",$trad="")
{       
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Litterature secondaire")):(ucfirst($trad))?></td>
        	<td><input type="text" size="75"  name="litterature_secondaire" value="<?=$litterature_secondaire?>"/></td>
        </tr>
        <?php
}


function add_recueil_form_show_comment_public ($comment_public="",$trad="")
{       
	?>        
        <tr>
                <td><?=(empty($trad))?(_("Commentaire public")):(ucfirst($trad))?></td>
                <td><textarea cols="60" rows="5" name="comment_public" ><?=$comment_public?></textarea></td>
        </tr>
        <?php
}

function add_recueil_form_show_comment_reserve ($comment_reserve="",$trad="")
{       
	?>
        <tr>
                <td><?=(empty($trad))?(_("Commentaire réservé")):(ucfirst($trad))?></td>
                <td><textarea cols="60" rows="5" name="comment_reserve" ><?=$comment_reserve?></textarea></td>
        </tr>
        <?php
}
function add_recueil_form_show_bibliotheque ($bibliotheque="",$trad="")
{       
	?>
        <tr>
       		<td><?=(empty($trad))?(_("Bibliothèque")):(ucfirst($trad))?></td>
       		<td><input type="text" size="75"  name="bibliotheque" value="<?=$bibliotheque?>"/></td>
	</tr>
	<?php
}
function add_recueil_form_show_cote ($cote="",$trad="")
{       
	?>
	<tr>
       		<td><?=(empty($trad))?(_("Cote")):(ucfirst($trad))?></td>
       		<td><input type="text" size="75"  name="cote" value="<?=$cote?>"/></td>
	</tr>
	<?php
}

/*---------------------------------------------end  add_recueil_form_shows ----------------------------------------
EXCEPTION two functions are used for update.php different from these functions 
*/
function update_recueil_form_show_image_titre_recueil_jpg($arg="",$trad="")
{
	?>
	<tr>
                <td><?=(empty($trad))?(_("Titre en image")):(ucfirst($trad))?></td>
                <td>
                        <select onchange="update_file_field(this,'image_titre_recueil_jpg')" name="reup_image_titre_recueil_jpg">
                                <option value="true"><?=_("Oui")?></option>
                                <option selected='selected' value="false"><?=_("Non")?></option>
                        </select>
                        <input id="image_titre_recueil_jpg" type="hidden" name="image_titre_recueil_jpg"/>
                </td>
        </tr>
        <?php
}
function update_recueil_form_show_image_table_matieres($arg="",$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Image de la table des matières")):(ucfirst($trad))?></td>
                <td>
                        <select onchange="update_file_field(this,'image_table_matieres')" name="reup_image_titre_recueil_jpg">
                                <option value="true"><?=_("Oui")?></option>
                                <option selected='selected' value="false"><?=_("Non")?></option>
                        </select>
                        <input id="image_table_matieres" type="hidden" name="image_table_matieres"/>
                </td>
        </tr>
        <?php
}

/*---------------------------------------------end special update_recueil_form_shows special-----------------------*/


/*---------------------------------------------begin special info_recueil_shows special-----------------------*/

function info_recueil_show_titre ($titre,$trad="")
{
	?>
 	<tr>
                <td><?=(empty($trad))?(_("Titre")):(ucfirst($trad))?></td>
                <td><?=$titre?></td>
        </tr>
        <?php
}
function info_recueil_show_titre_uniforme ($titre_uniforme,$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Titre uniforme")):(ucfirst($trad))?></td>
                <td><?=$titre_uniforme?></td>
        </tr>
        <?php
}
function info_recueil_show_abreviation ($abreviation,$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Abréviation")):(ucfirst($trad))?></td>
                <td><?=$abreviation?></td>
        </tr>
        <?php
}
function info_recueil_show_image_titre_recueil_jpg ($id_recueil,$trad="")
{
	?>
       
        <tr>
                <td><?=(empty($trad))?(_("Titre en image")):(ucfirst($trad))?></td>
                <td>
                        <a href="javascript:;" onclick='popup_image("image.php?image_type=image_titre_recueil_jpg&amp;id_recueil=<?=$id_recueil?>",500,500)' ><img alt='' width='100' height='100' src='image.php?image_type=image_titre_recueil_jpg&amp;id_recueil=<?=$id_recueil?>' /></a>
                </td>
        </tr>
        <?php
}
function info_recueil_show_image_table_matieres ($id_recueil,$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Image de la table des matières")):(ucfirst($trad))?></td>
                <td>
                        <a href="javascript:;" onclick='popup_image("image.php?image_type=image_table_matieres&amp;id_recueil=<?=$id_recueil?>",500,500)' ><img alt='' width='100' height='100' src='image.php?image_type=image_table_matieres&amp;id_recueil=<?=$id_recueil?>' /></a>
                </td>
        </tr>
        <?php
}
function info_recueil_show_imprimeur ($imprimeur,$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Imprimeur")):(ucfirst($trad))?></td>
                <td><?=$imprimeur?></td>
        </tr>
        <?php
}
function info_recueil_show_editeur ($editeur,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Editeur")):(ucfirst($trad))?></td>
        	<td><?=$editeur?></td>
        </tr>
        <?php
}
function info_recueil_show_adresse_biblio ($adresse_biblio,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Adresse Bibliographique")):(ucfirst($trad))?></td>
        	<td><?=$adresse_biblio?></td>
        </tr>
        <?php
}
function info_recueil_show_auteur ($auteur,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Auteur")):(ucfirst($trad))?></td>
        	<td><?=$auteur?></td>
        </tr>
        <?php
}
function info_recueil_show_compositeur ($compositeur,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Compositeur")):(ucfirst($trad))?></td>
        	<td><?=$compositeur?></td>
        </tr>
        <?php
}
function info_recueil_show_lieu ($lieu,$trad="")
{
	?>
        
        <tr>
                <td><?=(empty($trad))?(_("Lieu")):(ucfirst($trad))?></td>
                <td><?=$lieu?></td>
        </tr>
        <?php
}
function info_recueil_show_solmisation ($solmisation,$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Solmisation")):(ucfirst($trad))?></td>
                <td><?=$solmisation?></td>
        </tr>
        <?php
}
function info_recueil_show_timbre ($timbre,$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Timbres")):(ucfirst($trad))?></td>
                <td><?=$timbre?></td>
        </tr>
        <?php
}
function info_recueil_show_date_impression ($date_impression,$trad="")
{
	?>
        
        <tr>
                <td><?=(empty($trad))?(_("Date d'impression")):(ucfirst($trad))?></td>
                <td><?=$date_impression?></td>
        </tr>
        <?php
}
function info_recueil_show_description_materielle ($description_materielle,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Description matérielle")):(ucfirst($trad))?></td>
        	<td><?=$description_materielle?></td>
        </tr>
        <?php
}
function info_recueil_show_sources_bibliographiques ($sources_bibliographiques,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Sources Bibliographiques")):(ucfirst($trad))?></td>
        	<td><?=$sources_bibliographiques?></td>
        </tr>
        <?php
}
function info_recueil_show_litterature_secondaire ($litterature_secondaire,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Litterature secondaire")):(ucfirst($trad))?></td>
        	<td><?=$litterature_secondaire?></td>
        </tr>
        <?php
}
function info_recueil_show_comment_public ($comment_public,$trad="")
{
	?>
        
        <tr>
                <td><?=(empty($trad))?(_("Commentaire public")):(ucfirst($trad))?></td>
                <td><?=$comment_public?></td>
        </tr>
        <?php
}
function info_recueil_show_comment_reserve ($comment_reserve,$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Commentaire réservé")):(ucfirst($trad))?></td>
                <td><?=$comment_reserve?></td>
        </tr>
        <?php
}
function info_recueil_show_bibliotheque ($bibliotheque,$trad="")
{
	?>
        <tr>
       		<td><?=(empty($trad))?(_("Bibliothèque")):(ucfirst($trad))?></td>
       		<td><?=$bibliotheque?></td>
	</tr>
	<?php
}
function info_recueil_show_cote ($cote,$trad="")
{
	?>
	<tr>
       		<td><?=(empty($trad))?(_("Cote")):(ucfirst($trad))?></td>
       		<td><?=$cote?></td>
	</tr>
        <?php
}



function info_recueil_show_nom_auteur_fiche ($nom_auteur_fiche,$trad="")
{
	?>
	<tr>
                <td><?=(empty($trad))?(_("Nom de l'auteur de la fiche")):(ucfirst($trad))?></td>
                <td><?=$nom_auteur_fiche?></td>
        </tr>
	<?php
}

/********************************************End info_recueil_shows***********************************************/

/*---------------------------------------------begin add_piece_form_shows-------------------------------------------*/
function add_piece_form_show_page($page="",$trad="")
{
	?>
	<tr>
        	<td><?=(empty($trad))?(_("Page")):(ucfirst($trad))?>*</td>
        	<td><input id="page" type="text" size="3" name="page" value="<?=$page?>"/></td>
        </tr>
        <?php
}
function add_piece_form_show_pagination_ancienne ($pagination_ancienne="",$trad="")
{
	?>
        <tr>
        	<td><acronym title="<?=_("Signature,pagination alternative")?>"><?=(empty($trad))?(_("Pagination ancienne")):(ucfirst($trad))?></acronym></td>
        	<td><input  type="text" size="3" name="pagination_ancienne" value="<?=$pagination_ancienne?>"/></td>
        </tr>
	<?php
}
function add_piece_form_show_titre_piece ($titre_piece="",$trad="")
{
	?>       
        <tr>
        	<td><?=(empty($trad))?(_("Titre de la pièce (ou incipit)")):(ucfirst($trad))?>*</td>
        	<td><input type="text" size="75" name="titre"  value="<?=$titre_piece?>"/></td>
        </tr>
        
        <?php
}
function add_piece_form_show_fichier_finale ($arg="",$trad="")
{
	?>
        <tr>
                <td><acronym title="<?=_("Stocker le fichier d'édition musicale (Finale,Sibelius ...)")?>"><?=(empty($trad))?(_("Fichier Finale")):(ucfirst($trad))?></acronym></td>
                <td><input type="file" name="fichier_finale"/></td>
        </tr>
	<?php
}
function add_piece_form_show_image_jpg ($arg="",$trad="")
{
	?>
        <tr>
                <td><acronym title="<?=_("Image de la partition moderne")?>"><?=(empty($trad))?(_("Image de la page de titre")):(ucfirst($trad))?></acronym></td>
                <td><input id="image_jpg" type="file" name="image_jpg"/></td>
        </tr>
        <?php
}
function add_piece_form_show_incipit_jpg ($arg="",$trad="")
{
	?>
        <tr>
                <td><acronym title="<?=_("Image fac simile")?> "><?=(empty($trad))?(_("Image fac simile")):(ucfirst($trad))?></acronym></td>
                <td><input id="incipit_jpg"  type="file" name="incipit_jpg"/></td>
        </tr>
        <?php
}
function add_piece_form_show_rubrique ($rubrique="",$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Mentions marginales")):(ucfirst($trad))?></td>
                <td><input type="text" size="75" name="rubrique" value="<?=$rubrique?>"/></td>
        </tr>
        <?php
}
function add_piece_form_show_musicxml ($arg="",$trad="")
{
	?>
        
        
                <tr>
                        <td></td>
                        <td>
                        	<input  id="has_musicxml_true" type="radio" name="has_musicxml" value="true" onclick="update_champ()" checked="checked"/><?=_("Vous avez un fichier MusicXML")?>
                        	<input onclick="update_champ()" type="radio" name="has_musicxml" value="false"/><?=_("Pas de MusicXML")?>
                        </td>
                </tr>
                <tr id="fichier_musicxml">
                        <td><?=(empty($trad))?(_("Fichier MusicXML")):(ucfirst($trad))?></td>
                        <td><input type="file" name="fichier_musicxml"/></td>
                </tr>
	<?php
}

function add_piece_form_show_note_finale ($note_finale="",$trad="")
{
	?>                
       <tr class="table_no_show" id="note_finale">
                <td><?=(empty($trad))?(_("Note finale")):(ucfirst($trad))?></td>
                <td><input type="text" size="75" name="note_finale" value="<?=$note_finale?>"/></td>
        </tr>
        <?php
}
function add_piece_form_show_ambitus ($ambitus="",$trad="")
{
	?>
        <tr class="table_no_show" id="ambitus">
                <td><acronym title="<?=_("Ambitus ou tessiture")?>"><?=(empty($trad))?(_("Ambitus")):(ucfirst($trad))?></acronym></td>
                <td><input type="text" size="75" name="ambitus" value="<?=$ambitus?>"/></td>
        </tr>
        <?php
}
function add_piece_form_show_armure ($armure="",$trad="")
{
	?>
        <tr class="table_no_show" id="armure">
                <td><?=(empty($trad))?(_("Armure")):(ucfirst($trad))?></td>
                <td><input type="text" size="75" name="armure" value="<?=$armure?>"/></td>
        </tr>
        <?php
}
function add_piece_form_show_cles ($cles="",$trad="")
{
	?>
        <tr class="table_no_show" id="cles">
                <td><?=(empty($trad))?(_("Clés")):(ucfirst($trad))?></td>
                <td><input type="text" size="75" name="cles" value="<?=$cles?>"/></td>
        </tr>
        <?php
}
function add_piece_form_show_nombre_parties ($nb_parts="",$trad="")
{
	?>
        <tr class="table_no_show" id="nombre_parties">
                <td><?=(empty($trad))?(_("Nombre de parties")):(ucfirst($trad))?></td>
                <td><input type="text" size="75" name="nombre_parties" value="<?=$nb_parts?>"/></td>
        </tr>
        <?php
}
function add_piece_form_show_codage_incipit ($codage_incipit="",$trad="")
{
	?>
        <tr class="table_no_show" id="incipit">
        	<td><?=(empty($trad))?(_("Codage incipit")):(ucfirst($trad))?></td>
        	<td><input type="text" size="75" name="codage_incipit" value="<?=$codage_incipit?>"/></td>
        </tr>
        <?php
}
function add_piece_form_show_compositeur ($compositeur="",$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Compositeur")):(ucfirst($trad))?></td>
        	<td><input type="text" size="75" name="compositeur" value="<?=$compositeur?>"/></td>
        </tr>
        <?php
}
function add_piece_form_show_timbre ($timbre="",$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Timbre")):(ucfirst($trad))?></td>
        	<td><input type="text" size="75" name="timbre" value="<?=$timbre?>"/></td>
        </tr>
        <?php
}


function add_piece_form_show_auteur ($auteur,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Auteur du texte")):(ucfirst($trad))?></td>
        	<td>
        	<?php
        	/*
        	Si le champ auteur est vide dans le recueil auteur
        	=> l'user rentre le nom de l'auteur
        	sinon => on affiche l'auteur du recueil
        	*/
        	if(empty($auteur))
        	{
        		?>
        		<input type="text" size="75" name="auteur_texte"/>
        		<?php
        	}
        	else
        	{
        	        ?>
        	        <?=$auteur?>
        	        
        		<?php
        	}
        	?>
        	</td><!--Numéro dans le catalogue-->
        </tr>
        <?php
}


function add_piece_form_show_concordances ($concordances="",$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Concordances")):(ucfirst($trad))?></td>
        	<td><textarea cols="60" rows="2" name="concordances"><?=$concordances?></textarea></td>
        </tr>
        <?php
}
function add_piece_form_show_texte_additionnel ($texte_additionnel="",$trad="")
{
	?>
        <tr id="champ_texte">
        	<td><?=(empty($trad))?(_("Texte additionnel")):(ucfirst($trad))?></td>
        	<td><textarea cols="60" rows="2" name="texte_additionnel"><?=$texte_additionnel?></textarea></td>
        </tr>
        <?php
}
function add_piece_form_show_groupes_textes ($arg="",$trad="")
{
	?>
        <tr>
        	<td><acronym title="<?=("Catalogue,Collection (exemple : BWV)")?>"><?=(empty($trad))?(_("Source du texte")):(ucfirst($trad))?></acronym></td>
        	<td><?php list_groupes_textes();?></td>
        </tr>
        <?php
}
function add_piece_form_show_biblio_texte ($biblio_texte="",$trad="")
{
	?>
        <!--
        <tr>
        	<td><acronym title="<?=("Catalogue,Collection (exemple : BWV)")?>"><?=("Commentaire")?></acronym></td>
        	<td><input type="text" size="75" name="commentaire_groupe_textes"/></td>
        </tr>
        -->
        
        <tr>
        	<td><?=(empty($trad))?(_("Bibliographie spécifique du texte")):(ucfirst($trad))?></td>
        	<td><textarea cols="60" rows="2" name="biblio_texte"><?=$biblio_texte?></textarea></td>
        </tr>
        <?php
}
function add_piece_form_show_comment_public ($comment_public="",$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Commentaire public")):(ucfirst($trad))?></td>
                <td><textarea cols="60" rows="5" name="comment_public"><?=$comment_public?></textarea></td>
        </tr>
        <?php
}
function add_piece_form_show_comment_reserve ($comment_reserve="",$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Commentaire réservé")):(ucfirst($trad))?></td>
                <td><textarea cols="60" rows="5" name="comment_reserve"><?=$comment_reserve?></textarea></td>
        </tr>
        <?php
}
function add_piece_form_show_comment_revision ($comment_revision="",$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Commentaire révision")):(ucfirst($trad))?></td>
                <td><textarea cols="60" rows="5" name="comment_revision"><?=$comment_revision?></textarea></td>
        </tr>
        <?php
}



function add_piece_form_show_psaume ($psaume=0,$trad="")
{
	?>
    <tr>
            <td><?=_("Psaume")?></td>
            <td>
                    <input type="radio" name="psaume" value="true" <?=($psaume==1)?("checked='checked'"):("")?> /><?=_("Oui")?>
                    <input type="radio" name="psaume" value="false" <?=($psaume==0)?("checked='checked'"):("")?>/><?=_("Non")?>
            </td>
    </tr>
    <?php
}
/*---------------------------------------------------------end add_piece_form_shows-------------------------------------*/
/*---------------------------------------------------------begin update_piece_form_shows--------------------------------*/


function update_piece_form_show_recueil($arg="",$trad="")
{
	?>
	<tr>
        	<td><?=(empty($trad))?(_("Recueil")):(ucfirst($trad))?></td>
        	<td><input type="hidden" name="old_recueil" id="old_recueil" value="<?=$_GET['id_recueil']?>"/><?php list_recueils();?></td>
        </tr>
        <?php
}


function update_piece_form_show_fichier_finale($arg="",$trad="")
{
	?>
	<tr>
                <td><acronym title="<?=_("Stocker le fichier d'édition musicale (Finale,Sibelius ...)")?>"><?=(empty($trad))?(_("Envoyer un nouveau Fichier Finale")):(ucfirst($trad))?></acronym></td>
                <td>
                        <select onchange="update_file_field(this,'fichier_finale')" name="reup_fichier_finale">
                                <option value="true"><?=_("Oui")?></option>
                                <option selected='selected' value="false"><?=_("Non")?></option>
                        </select>
                        <input id="fichier_finale" type="hidden" name="fichier_finale"/>
                </td>
        </tr>
	<?php
}


function update_piece_form_show_musicxml($arg="",$trad="")
{
	?>
	<tr>
                <td><acronym title="<?=_("MusicXML")?>"><?=(empty($trad))?(_("Envoyer un nouveau Fichier MusicXML")):(ucfirst($trad))?></acronym></td>
                <td>
                        <select onchange="update_file_field(this,'fichier_musicxml')" name="reup_fichier_musicxml">
                                <option value="true"><?=_("Oui")?></option>
                                <option selected='selected' value="false"><?=_("Non")?></option>
                        </select>
                        <input id="fichier_musicxml" type="hidden" name="fichier_musicxml"/>
                        <!--<input type="checkbox" name="want_mp3" />
                        Tempo <input type="text" name="nb_beats" value="60"/>&nbsp
                        à la <select name="reference_tempo">
                        	<option value="1"><?=_("Ronde")?></option>
                        	<option value="2"><?=_("Blanche")?></option>
                        	<option value="3"><?=_("Blanche pointée")?></option>
                        	<option value="4" selected=true><?=_("Noire")?></option>
                        	<option value="8"><?=_("Croche")?></option>
                        	<option value="16"><?=_("Double croche")?></option>
                        </select>-->
                </td>
        </tr>
	<?php
}

function update_piece_form_show_image_jpg($arg="",$trad="")
{
	?>
	<tr>
                <td><acronym title="<?=_("Reupload de l'image de la partition moderne")?>"><?=(empty($trad))?(_("Envoyer une nouvelle image de la page de titre")):(ucfirst($trad))?></acronym></td>
                <td>
                        <select onchange="update_file_field(this,'image_jpg')" name="reup_image_jpg">
                                <option value="true"><?=_("Oui")?></option>
                                <option selected='selected' value="false"><?=_("Non")?></option>
                        </select>
                        <input id="image_jpg" type="hidden" name="image_jpg"/>
                </td>
        </tr>
        <?php
}
function update_piece_form_show_incipit_jpg($arg="",$trad="")
{
	?>
        <tr>
                <td><acronym title="<?=_("Reupload de l'image fac simile")?> "><?=(empty($trad))?(_("Renvoyer une image fac simile")):(ucfirst($trad))?></acronym></td>
                <td>
                        <select onchange="update_file_field(this,'incipit_jpg')" name="reup_incipit_jpg">
                                <option value="true"><?=_("Oui")?></option>
                                <option selected='selected' value="false"><?=_("Non")?></option>
                        </select>
                        <input id="incipit_jpg"  type="hidden" name="incipit_jpg"/>
                </td>
        </tr>
	<?php
}

/*
function update_piece_form_show_groupes_textes($response)
{
	?>
	<tr>
        	<td><acronym title="<?=("Catalogue,Collection (exemple : BWV)")?>"><?=("Source du texte")?></acronym></td>
        	<td>
        		<?php
        		$response2=$response;
        		//problem when id_groupe_texte is null
        		while(empty($response2["id_groupe_texte"])&&($response2!=FALSE) )
        		{
        			$response2=fetch_array($req);
        		} 
        		list_groupes_textes($response2["id_groupe_texte"]);
        		?>
        	</td>
        </tr>
        <?php
}
*/

function update_piece_form_show_groupes_textes($id_groupe_texte,$trad="")
{
	?>
	<tr>
        	<td><acronym title="<?=("Catalogue,Collection (exemple : BWV)")?>"><?=(empty($trad))?(_("Source du texte")):(ucfirst($trad))?></acronym></td>
        	<td>
        		<?php
        		
        		list_groupes_textes($id_groupe_texte);
        		?>
        	</td>
        </tr>
        <?php
}




/*******************************************end update_piece_form_shows****************************************************/

/*------------------------------------------begin info_piece_shows-------------------------------------------------------*/





function info_piece_show_page ($rang,$trad="")
{
	?>
	<tr>
        	<td><?=(empty($trad))?(_("Page")):(ucfirst($trad))?></td>
        	<td><?=$rang?></td>
        </tr>
        <?php
}

function info_piece_show_titre_piece ($titre_piece,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Titre de la pièce (ou incipit)")):(ucfirst($trad))?></td>
        	<td><?=$titre_piece?></td>
        </tr>      
        <?php
}

function info_piece_show_pagination_ancienne ($pagination_ancienne,$trad="")
{
	?>
        <tr>
        	<td><acronym title="<?=_("Signature,pagination alternative")?>"><?=(empty($trad))?(_("Pagination ancienne")):(ucfirst($trad))?></acronym></td>
        	<td><?=$pagination_ancienne?></td>
        </tr>
	<?php
}

function info_piece_show_rubrique ($rubrique,$trad="")
{
	?>
	<tr>
                <td><?=(empty($trad))?(_("Mentions marginales")):(ucfirst($trad))?></td>
                <td><?=$rubrique?></td>
        </tr>
        <?php
}

function info_piece_show_note_finale ($note_finale,$trad="")
{
	?>
	<tr  id="note_finale">
                <td><?=(empty($trad))?(_("Note finale")):(ucfirst($trad))?></td>
                <td><?=$note_finale?></td>
        </tr>
        <?php
}

function info_piece_show_ambitus ($ambitus,$trad="")
{
	?>
        <tr  id="ambitus">
                <td><acronym title="<?=("Ambitus ou tessiture")?>"><?=(empty($trad))?(_("Ambitus")):(ucfirst($trad))?></acronym></td>
                <td><?=$ambitus?></td>
        </tr>
        <?php
}

function info_piece_show_armure ($armure,$trad="")
{
	?>
        <tr  id="armure">
                <td><?=(empty($trad))?(_("Armure")):(ucfirst($trad))?></td>
                <td><?=$armure?></td>
        </tr>
        <?php
}

function info_piece_show_cles ($cles,$trad="")
{
	?>
        <tr  id="cles">
                <td><?=(empty($trad))?(_("Clés")):(ucfirst($trad))?></td>
                <td><?=$cles?></td>
        </tr>
        <?php
}

function info_piece_show_nombre_parties ($nombre_parties,$trad="")
{
	?>
        <tr  id="nombre_parties">
                <td><?=(empty($trad))?(_("Nombre de parties")):(ucfirst($trad))?></td>
                <td><?=$nombre_parties?></td>
        </tr>
        <?php
}

function info_piece_show_texte_additionnel ($texte_additionnel,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Texte additionnel")):(ucfirst($trad))?></td>
        	<td><?=$texte_additionnel?></td>
        </tr>
        <?php
}

function info_piece_show_concordances ($concordances,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Concordances")):(ucfirst($trad))?></td>
        	<td><?=$concordances?></td>
        </tr>
        <?php
}

function info_piece_show_auteur ($auteur,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Auteur")):(ucfirst($trad))?></td>
        	<td><?=$auteur?></td>
        </tr>
        <?php
}

function info_piece_show_compositeur ($compositeur,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Compositeur")):(ucfirst($trad))?></td>
        	<td><?=$compositeur?></td>
        </tr>
        <?php
}

function info_piece_show_timbre ($timbre,$trad="")
{
	?>
        <tr>
        	<td><?=(empty($trad))?(_("Timbre")):(ucfirst($trad))?></td>
        	<td><?=$timbre?></td>
        </tr>
        <?php
}

 
		
		
function info_piece_show_biblio_texte ($biblio_texte,$fichier_xml,$req,$trad="")
{
	if(empty($fichier_xml))//no xml
	{
		?>
		
        	<tr>
        		<td><?=(empty($trad))?(_("Bibliographie spécifique du texte")):(ucfirst($trad))?></td>
        		<td><?=_("Aucune (champ impossible à remplir sans musicxml)")?></td>
        	</tr>
		<?php	
	}
	else
	{
		?>
		<tr>
			<td><?=(empty($trad))?(_("Bibliographie spécifique du texte")):(ucfirst($trad))?></td>
			<td>	
				<?php
				echo $biblio_texte;
				if(num_rows($req)!=1)
				{
					mysql_data_seek($req,1);
					while($response2=fetch_array($req))
					{
						if(!empty($response2["biblio_texte"]))
						{
							echo " ".$response2["biblio_texte"];
						}
					}
				}
				?>
			</td>
		</tr>
		<?php
	}
}

function info_piece_show_groupes_textes ($nom_groupe_texte,$fichier_xml,$req /*need a little trick to pass req as argument, req must be at second row (if it exists)*/,$trad="")
{
	if(empty($fichier_xml))//no xml
	{
		?>
		<tr>
        		<td><acronym title="<?=("Catalogue,Collection (exemple : BWV)")?>"><?=(empty($trad))?(_("Source du texte")):(ucfirst($trad))?></acronym></td>
        		<td><?=_("Aucune (champ impossible à remplir sans musicxml)")?></td>
        	</tr>
        	<tr>
        		<td><?=_("Bibliographie spécifique du texte")?></td>
        		<td><?=_("Aucune (champ impossible à remplir sans musicxml)")?></td>
        	</tr>
		<?php	
	}
	else
	{
		?>
		<tr>
			<td><acronym title="<?=("Catalogue,Collection (exemple : BWV)")?>"><?=(empty($trad))?(_("Source du texte")):(ucfirst($trad))?></acronym></td>
			<td>
        		
        		<?php
        		echo $nom_groupe_texte;
        		if(num_rows($req)!=1)
			{
				mysql_data_seek($req,1);
				while($response2=fetch_array($req))
				{
					if(!empty($response2["nom_groupe_texte"]))
					{
						echo " ".$response2["nom_groupe_texte"];
					}
				}
			}
        		?>
        		</td>
		</tr>
		
		<?php
	}
}
	
function info_piece_show_comment_public ($comment_public,$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Commentaire public")):(ucfirst($trad))?></td>
                <td><?=$comment_public?></td>
        </tr>
        <?php
}

function info_piece_show_comment_reserve ($comment_reserve,$nom_auteur_fiche,$trad="")
{
        if( ($_SESSION['pseudo']==$nom_auteur_fiche) || $_SESSION["admin"])
        {
                ?>
                <tr>
                        <td><?=(empty($trad))?(_("Commentaire réservé")):(ucfirst($trad))?></td>
                        <td><?=$comment_reserve?></td>
                </tr>
                <?php
        }
}

function info_piece_show_comment_revision($comment_revision,$id_piece,$trad="")
{
        if(write_in_piece($id_piece))
        {
        	?>
		<tr>
		        <td><?=(empty($trad))?(_("Commentaire de la révision")):(ucfirst($trad))?></td>
		        <td><?=$comment_revision?></td>
		</tr>
		<?php
	}
}



function info_piece_show_psaume ($psaume="0",$trad="")
{
	?>
        <tr>
                <td><?=(empty($trad))?(_("Psaume")):(ucfirst($trad))?></td>
                <td><?=($psaume==0)?("<input type='checkbox' disabled='disabled'/>"):("<input type='checkbox' disabled='disabled' checked='checked'/>")?></td>
        </tr>
        <?php
}
/**********************************end info_piece_shows**********************************************************/
?>
