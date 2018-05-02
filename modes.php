<?php
require_once("include/auth.php");
require_once('include/page.php');
require_once('include/mysql.php');
require_once("include/log.php");
require_once("include/config.php");
require_once("include/upload.php");
require_once("include/check.php");
is_authorized();
ob_start();
$title=_("Gestion des modes");


DEFINE('WORK_DIR','/home/ovh/www/locale/traductions/');

function add_mode_form()
{
	begin_box_js(_("Ajout d'un mode"),"add_mode_form");
	?>
	<form method="post" action="?add=true" >
	<tr>
	<td>
		
		<table style="border: 1px solid grey; border-collapse: collapse" border="1">
		<tr>
			<th><?=_("Nom du mode")?></th>
			<td colspan="3"><input type="text" name="mode"/></td>
			
		</tr>
		<tr>
			<th><?=_("Champ de la base")?></th>
				
			<td style="vertical-align: top;"><input disabled="disabled" checked="checked"
			name=""  type="checkbox"/><input name="base_nom_base"  type="hidden" value="on"/><?=_("Nom de la base")?></td>
			<td style="vertical-align: top;"><input checked="checked"
			name="base_description"  type="checkbox"/><?=_("Description")?></td>
			<td style="vertical-align: top;"><input checked="checked"
			name="base_references"  type="checkbox"/><?=_("Références")?></td>
		</tr>
				<tr>
			<th rowspan="7"><?=_("Champ de chaque recueil")?></th>

			<td><input disabled="disabled" checked="checked" type="checkbox" name="" /><input   type="hidden" name="recueil_titre" value="on"/><?=_("Titre")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_titre_uniforme" /><?=_("Titre uniforme")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_abreviation" /><?=_("Abréviation")?>
			<input type="hidden" name="base_mode" value="on" />
			<input type="hidden" name="base_banner" value="on" />
			</td>
		
					
		</tr>
		<tr>
			<td><input checked="checked" type="checkbox" name="recueil_image_titre_recueil_jpg" /><?=_("Image de la page de titre")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_image_table_matieres" /><?=_("Image de la table des matières")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_imprimeur" /><?=_("Imprimeur")?></td>
		
		</tr>
		<tr>
		
			<td><input checked="checked" type="checkbox" name="recueil_editeur" /><?=_("Editeur")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_adresse_biblio" /><?=_("Adresse Bibliographique")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_auteur" /><?=_("Auteur")?></td>
		
		</tr>
		<tr>
		
			<td><input checked="checked" type="checkbox" name="recueil_compositeur" /><?=_("Compositeur")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_lieu" /><?=_("Lieu")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_solmisation" /><?=_("Solmisation")?></td>
		
		</tr>
		<tr>
		
			<td><input checked="checked" type="checkbox" name="recueil_timbre" /><?=_("Timbres")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_date_impression" /><?=_("Date d'impression")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_description_materielle" /><?=_("Description matérielle")?></td>
		
		</tr>
		<tr>
			<td><input checked="checked" type="checkbox" name="recueil_sources_bibliographiques" /><?=_("Sources Bibliographiques")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_litterature_secondaire" /><?=_("Litterature secondaire")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_comment_public" /><?=_("Commentaire public")?></td>
		</tr>
		<tr>
			<td><input checked="checked" type="checkbox" name="recueil_comment_reserve" /><?=_("Commentaire réservé")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_bibliotheque" /><?=_("Bibliothèque")?></td>
			<td><input checked="checked" type="checkbox" name="recueil_cote" /><?=_("Cote")?></td>
		</tr>
				
	
		<tr>
			<th rowspan="8"><?=_("Champ de chaque pièce")?></th>
			
			<td><input disabled="disabled" checked="checked" type="checkbox" name="" /><input   type="hidden" value="on" name="piece_page" /><?=_("Page")?></td>
			<td><input disabled="disabled" checked="checked" type="checkbox" name="" /><input   type="hidden" value="on" name="piece_titre_piece" /><?=_("Titre")?></td>
			<td><input checked="checked" type="checkbox" name="piece_pagination_ancienne" /><?=_("Pagination ancienne")?></td>
					
		</tr>
		<tr>
			<td><input checked="checked" type="checkbox" name="piece_fichier_finale" /><?=_("Fichier Finale")?></td>
			<td><input checked="checked" type="checkbox" name="piece_image_jpg" /><?=_("Image de la page de titre")?></td>
			<td><input checked="checked" type="checkbox" name="piece_incipit_jpg" /><?=_("Image fac simile")?></td>
		</tr>
		


		<tr>
			<td><input checked="checked" type="checkbox" name="piece_rubrique" /><?=_("Mentions marginales")?></td>
			<td><input checked="checked" type="checkbox" name="piece_musicxml" /><?=_("Fichier MusicXML")?></td>
			<td><input checked="checked" type="checkbox" name="piece_note_finale" /><?=_("Note finale")?></td>
		</tr>
		<tr>
			<td><input checked="checked" type="checkbox" name="piece_ambitus" /><?=_("Ambitus")?></td>
			<td><input checked="checked" type="checkbox" name="piece_armure" /><?=_("Armure")?></td>
			<td><input checked="checked" type="checkbox" name="piece_cles" /><?=_("Clés")?></td>
		</tr>
		<tr>
			<td><input checked="checked" type="checkbox" name="piece_nombre_parties" /><?=_("Nombre de parties")?></td>
			<td><input checked="checked" type="checkbox" name="piece_texte_additionnel" /><?=_("Texte additionnel")?></td>
			<td><input checked="checked" type="checkbox" name="piece_concordances" /><?=_("Concordances")?></td>
		</tr>
	
		<tr>
			<td><input checked="checked" type="checkbox" name="piece_auteur" /><?=_("Auteur du texte")?></td>
			<td><input checked="checked" type="checkbox" name="piece_compositeur" /><?=_("Compositeur")?></td>
			<td><input checked="checked" type="checkbox" name="piece_codage_incipit" /><?=_("Codage incipit")?></td>
		</tr>
		

		<tr>
			<td><input checked="checked" type="checkbox" name="piece_timbre" /><?=_("Timbre")?></td>
			<td><input checked="checked" type="checkbox" name="piece_groupes_textes" /><?=_("Source du texte")?></td>
			<td><input checked="checked" type="checkbox" name="piece_biblio_texte" /><?=_("Bibliographie spécifique du texte")?></td>
		</tr>
		<tr>
			<td><input checked="checked" type="checkbox" name="piece_comment_public" /><?=_("Commentaire public")?></td>
			<td><input checked="checked" type="checkbox" name="piece_comment_reserve" /><?=_("Commentaire réservé")?></td>

		</tr>
				

	</table>
	</td>
	</tr>
	
	<tr>


		<td colspan="2" align="right"><input type="reset" value="<?=_("Réinitialiser")?>"/><input type="submit" value="<?=_("Valider")?>" /></td>

	</tr>
		
	
	</table>
	
	</form>
	<?php
	end_box();
}

function add_child($xml_doc,$node,$name,$attrs)
{
	
	$new=$xml_doc->createElement($name);
	$node->appendChild($new);
	foreach($attrs as $attr=> $value)
	{
	    $new_attr=$xml_doc->createAttribute($attr);
		$new->appendChild($new_attr);
		$new->setAttribute($attr,$value);
	}
	return $new;
}

function add_mode()
{
	global $xml_doc;
	$document=$xml_doc->getElementsByTagName("document")->item(0);
	$default=$xml_doc->getElementById("default");
	$base=$default->getElementsByTagName("base")->item(0);
	$recueil=$default->getElementsByTagName("recueil")->item(0);
	$piece=$default->getElementsByTagName("piece")->item(0);
	
	$mode_name=$_POST["mode"];
	$traduction_dir=str_replace(" ","",microtime());
	$traduction_dir=str_replace(".","",$traduction_dir);
	$new_mode=add_child($xml_doc,$document,"mode",array("id"=>$mode_name,"traduction_directory"=>$traduction_dir));
	mkdir("./locale/traductions/".$traduction_dir."/fr/LC_MESSAGES",0777,true);
	mkdir("./locale/traductions/".$traduction_dir."/en/LC_MESSAGES",0777,true);
	copy("./locale/traductions/orig.po","./locale/traductions/".$traduction_dir."/fr/LC_MESSAGES/traduction.po");
	copy("./locale/en/LC_MESSAGES/traduction.po","./locale/traductions/".$traduction_dir."/en/LC_MESSAGES/traduction.po");
	$log_msg=system("./convert_to_mo.py");
	command_log("convert_to_mo : ".$log_msg);
	$new_base=add_child($xml_doc,$new_mode,"base",array());
	$new_recueil=add_child($xml_doc,$new_mode,"recueil",array());
	$new_piece=add_child($xml_doc,$new_mode,"piece",array());
	
	
	$fields=$base->getElementsByTagName("field");
	foreach($fields as $field)
	{
		$name=$field->getAttribute("name");
		if($_POST["base_".$name]=="on")
		{
			add_child($xml_doc,$new_base,"field",array("name"=>$name));
		}
	}
	
	$fields=$recueil->getElementsByTagName("field");
	foreach($fields as $field)
	{
		$name=$field->getAttribute("name");
		if($_POST["recueil_".$name]=="on")
		{
			add_child($xml_doc,$new_recueil,"field",array("name"=>$name));
		}
	}
	
	
	$fields=$piece->getElementsByTagName("field");
	foreach($fields as $field)
	{
		$name=$field->getAttribute("name");
		if($_POST["piece_".$name]=="on")
		{
			add_child($xml_doc,$new_piece,"field",array("name"=>$name));
		}
	}
	$xml_doc->save(XML_CFG);
}

function dump_mode_node($node,$msg)
{
    ?>
    <table>
        <tr>
            <th><?=$msg?></th>
        </tr>
	    <?php
	    foreach($node->getElementsByTagName("field") as $field)
	    {
	        ?>
	        <tr>
	            <td><?=$field->getAttribute("name")?></td>
	        </tr>
	        <?php
	    }
	    ?>
	</table>
	<?php
}

function affich_mode($node)
{
	$name=$node->getAttribute("id");
	if($_SESSION["admin"])
	{
		if($name!="default")
		{
	    	begin_box_js($name,$name,"<a href='download.php?file=traduction&amp;mode=$name'><img src='images/design/save_as.png' alt='Télécharger les fichiers de traduction' title='Télécharger les fichiers de traduction' width='20' height='20'/></a><a href='modes.php?traduction_form=true&amp;mode=$name'><img src='images/design/edit.png' alt='Envoyer un nouveau fichier de traduction' title='Envoyer un nouveau fichier de traduction' width='20' height='20'/></a><a href='?delete=$name'><img src='./images/design/remove.png'  alt='Supprimer' title='Supprimer' width='20' height='20'/></a>");
	    }
	    else
	    {
	    	begin_box_js($name,$name,"<a href='download.php?file=traduction&amp;mode=$name'><img src='images/design/save_as.png' alt='Télécharger les fichiers de traduction' title='Télécharger les fichiers de traduction' width='20' height='20'/></a><a href='modes.php?traduction_form=true&amp;mode=$name'><img src='images/design/edit.png' alt='Envoyer un nouveau fichier de traduction' title='Envoyer un nouveau fichier de traduction' width='20' height='20'/></a>");
	    }
	}
	else
	{
	    begin_box_js($name,$name,"<a href='download.php?file=traduction&amp;mode=$name'><img src='images/design/save_as.png' alt='Télécharger les fichiers de traduction' title='Télécharger les fichiers de traduction' width='20' height='20'/></a><a href='?delete=$name'><img src='./images/design/remove.png'  alt='Supprimer' title='Supprimer' width='20' height='20'/></a>");
	}
	$base=$node->getElementsByTagName("base")->item(0);
	$recueil=$node->getElementsByTagName("recueil")->item(0);
	$piece=$node->getElementsByTagName("piece")->item(0);
	
	?>
	<tr>
	    <td><?php dump_mode_node($base,_("Champs de la base"))?></td>
	    <td><?php dump_mode_node($recueil,_("Champs du recueil"))?></td>
	    <td><?php dump_mode_node($piece,_("Champs de la pièce"))?></td>
	</tr>
	<?php
	end_box();
}

function all_modes()
{
	global $xml_doc;
	$modes=$xml_doc->getElementsByTagName("mode");
	foreach($modes as $mode)
	{
		
			affich_mode($mode);
	}
}


function delete_mode($mode)
{
	if($mode=="default")
	{
		return ;
	}
	global $xml_doc;
	$document=$xml_doc->getElementsByTagName("document")->item(0);
	foreach($xml_doc->getElementsByTagName("mode") as $node)
	{
		if($node->getAttribute("id")==$mode)
		{
			$document->removeChild($node);
#			delete the traduction directory
			$dir=$node->getAttribute("traduction_directory");
			if(!empty($dir))
			{
			    system("rm -drf ./locale/traductions/$dir");
		    }
		}
	}
	$xml_doc->save(XML_CFG);
}

if (!$_SESSION["admin"])
{
	begin_box(_("Erreur de droit"));
	msg(_("Vous n'êtes pas autorisé à voir cette page"));
	end_box();
	dump_page();
	return;
	
}



function update_traduction_form($mode)
{
    if (!$_SESSION["admin"])
    {
        begin_box(_("Problème de droits"),"not_authorized");
        msg("Vous n'avez pas les droits nécessaires pour exécuter cette tâche");
        end_box();
        return;
    }
    global $xml_doc;
    $mode_node=$xml_doc->getElementById($mode);
    if($mode_node===NULL)
    {
        begin_box(_("Erreur"),"error_traduction_form");
        msg(_("Le mode que vous essayez d'éditer n'existe pas"));
        end_box();
        return;
    }
    ?>
    <form method="post" action="modes.php?mode=<?=$mode?>&amp;traduction=true" enctype="multipart/form-data">
    <?php
    begin_box(_("Modifier le fichier de traduction"),"update_traduction_form_box");
    ?>
    <tr>
            <td><?=_("Traduction")?></td>
            <td>
            <?php
            if($mode!="default")
            {
                    ?>
                    Langue :
                    <select name="language">
                        <option selected='selected' value="fr"><?=_("Français")?></option>
                        <option value="en"><?=_("Anglais")?></option>
                        
                    </select>
                    <br/>
                    <?php
            }
            ?>
                    Fichier po :
                    <input  type="file" name="traduction_po" value=""/><br/>
                    Fichier mo :
                    <input  type="file" name="traduction_mo" value=""/>
            </td>
            <td>
                    <input type="reset" name="reset" value="<?=_('Réinitialiser')?>" />
                    <input type="submit" name="submit" value="<?=_('Valider')?>" />
            </td>
    </tr>
    <?php
    end_box();
    ?>
    </form>
    <?php
}

function update_traduction($mode)
{
    if (!$_SESSION["admin"])
    {
        begin_box(_("Problème de droits"),"not_authorized");
        msg(_("Vous n'avez pas les droits nécessaires pour exécuter cette tâche"));
        end_box();
        return;
    }
    global $xml_doc;
    $mode_node=$xml_doc->getElementById($mode);
    
    if($mode_node===NULL)
    {
        begin_box(_("Erreur"),"error_traduction_form");
        msg(_("Le mode que vous essayez d'éditer n'existe pas"));
        end_box();
        return;
    }
    
    if($mode=="default")
    {
#        default en anglais
        if(check_file("traduction_po"))
        {
            begin_box(_("Upload du fichier de traduction (po)"),"up_traduction_po");
            $extensions=array("po");
            

            $fichier_po=upload("traduction_po",$extensions,"./locale/en/LC_MESSAGES/");
            msg(_("Fichier de traduction uploadé"));
            unlink("./locale/en/LC_MESSAGES/traduction.po");
            msg(_("Suppression de l'ancien fichier de traduction"));
            rename($fichier_po,"./locale/en/LC_MESSAGES/traduction.po");
            msg(_("Déplacement du nouveau fichier de traduction (po)"));
            //$out_r3=exec($command3,$output3=array(),$ret);
            //command_log($command3." was executed by function update_traduction: the function returned ".print_r($output3,true)."and ".$out_r3.$ret);
            //msg(_("Conversion en fichier .mo"));
            //msg(_("La procédure est terminée : le nouveau fichier de traduction est fonctionnel"));
	        end_box();        
        }
        
        
        if(check_file("traduction_mo"))
        {
            begin_box(_("Upload du fichier de traduction (mo)"),"up_traduction_mo");
            $extensions=array("mo");
   

        	//$command3="msgfmt -o ".$path."traduction.mo ".$path."traduction.po";
            $fichier_mo=upload("traduction_mo",$extensions,"locale/en/LC_MESSAGES");
            msg(_("Fichier de traduction uploadé"));
            unlink("./locale/en/LC_MESSAGES/traduction.mo");
            msg(_("Suppression de l'ancien fichier de traduction"));
            rename($fichier_mo,"./locale/en/LC_MESSAGES/traduction.mo");
            msg(_("Déplacement du nouveau fichier de traduction (mo)"));
            //$out_r3=exec($command3,$output3=array(),$ret);
            //command_log($command3." was executed by function update_traduction: the function returned ".print_r($output3,true)."and ".$out_r3.$ret);
            //msg(_("Conversion en fichier .mo"));
            //msg(_("La procédure est terminée : le nouveau fichier de traduction est fonctionnel"));
        
        
    
	        end_box();
              
        }
        return;
    }
    
    //$command3="./convert_to_mo.py";
    $dir=$mode_node->getAttribute("traduction_directory");
    if(check_file("traduction_po"))
    {
        begin_box(_("Upload du fichier de traduction (po)"),"up_traduction_po");
        $extensions=array("po");
        switch($_POST["language"])
        {
            case "en":
            	$path=WORK_DIR.$dir."/en/LC_MESSAGES/";
            	//$command3="msgfmt -o ".$path."traduction.mo ".$path."traduction.po";
                $fichier_po=upload("traduction_po",$extensions,"locale/traductions/$dir/en/LC_MESSAGES");
                msg(_("Fichier de traduction uploadé"));
                unlink("./locale/traductions/$dir/en/LC_MESSAGES/traduction.po");
                msg(_("Suppression de l'ancien fichier de traduction"));
                rename($fichier_po,"./locale/traductions/$dir/en/LC_MESSAGES/traduction.po");
                msg(_("Déplacement du nouveau fichier de traduction (po)"));
                //$out_r3=exec($command3,$output3=array(),$ret);
                //command_log($command3." was executed by function update_traduction: the function returned ".print_r($output3,true)."and ".$out_r3.$ret);
                //msg(_("Conversion en fichier .mo"));
                //msg(_("La procédure est terminée : le nouveau fichier de traduction est fonctionnel"));
            break;
            
            case "fr":
            	$path=WORK_DIR.$dir."/fr/LC_MESSAGES/";
            	$command3="msgfmt -o ".$path."traduction.mo ".$path."traduction.po";
                $fichier_po=upload("traduction_po",$extensions,"./locale/traductions/$dir/fr/LC_MESSAGES/");
                msg(_("Fichier de traduction uploadé"));
                unlink("./locale/traductions/$dir/fr/LC_MESSAGES/traduction.po");
                msg(_("Suppression de l'ancien fichier de traduction"));
                rename($fichier_po,"./locale/traductions/$dir/fr/LC_MESSAGES/traduction.po");
                msg(_("Déplacement du nouveau fichier de traduction po"));
                //$out_r3=exec($command3,$output3=array(),$ret);
                //command_log($command3." was executed by function update_traduction: the function returned ".print_r($output3,true)."and ".$out_r3.$ret);
                //msg(_("Conversion en fichier .mo"));
                //msg(_("La procédure est terminée : le nouveau fichier de traduction est fonctionnel"));
            break;
            
            default:
                msg(_("Aucune langue spécifiée"));
            break;
        }
	end_box();
           
    }
    
    
    if(check_file("traduction_mo"))
    {
        begin_box(_("Upload du fichier de traduction (mo)"),"up_traduction_mo");
        $extensions=array("mo");
        switch($_POST["language"])
        {
            case "en":
            	$path=WORK_DIR.$dir."/en/LC_MESSAGES/";
                $fichier_mo=upload("traduction_mo",$extensions,"locale/traductions/$dir/en/LC_MESSAGES");
                msg(_("Fichier de traduction uploadé"));
                unlink("./locale/traductions/$dir/en/LC_MESSAGES/traduction.mo");
                msg(_("Suppression de l'ancien fichier de traduction"));
                rename($fichier_mo,"./locale/traductions/$dir/en/LC_MESSAGES/traduction.mo");
                msg(_("Déplacement du nouveau fichier de traduction (mo)"));
                //$out_r3=exec($command3,$output3=array(),$ret);
                //command_log($command3." was executed by function update_traduction: the function returned ".print_r($output3,true)."and ".$out_r3.$ret);
                //msg(_("Conversion en fichier .mo"));
                //msg(_("La procédure est terminée : le nouveau fichier de traduction est fonctionnel"));
            break;
            
            case "fr":
            	$path=WORK_DIR.$dir."/fr/LC_MESSAGES/";
            	//$command3="msgfmt -o ".$path."traduction.mo ".$path."traduction.po";
                $fichier_mo=upload("traduction_mo",$extensions,"./locale/traductions/$dir/fr/LC_MESSAGES/");
                msg(_("Fichier de traduction uploadé"));
                unlink("./locale/traductions/$dir/fr/LC_MESSAGES/traduction.mo");
                msg(_("Suppression de l'ancien fichier de traduction"));
                rename($fichier_mo,"./locale/traductions/$dir/fr/LC_MESSAGES/traduction.mo");
                msg(_("Déplacement du nouveau fichier de traduction mo"));
                //$out_r3=exec($command3,$output3=array(),$ret);
                //command_log($command3." was executed by function update_traduction: the function returned ".print_r($output3,true)."and ".$out_r3.$ret);
                //msg(_("Conversion en fichier .mo"));
                //msg(_("La procédure est terminée : le nouveau fichier de traduction est fonctionnel"));
            break;
            
            default:
                msg(_("Aucune langue spécifiée"));
            break;
        }
	end_box();
          
    }
}

?>
<a href="download.php?file=orig"><?=_("Fichier original de traduction")?></a>
<?php
if (isset($_GET["traduction_form"]))
{
    update_traduction_form($_GET["mode"]);
}
else if (isset($_GET["traduction"]))
{
    update_traduction($_GET["mode"]);
}
else
{
    add_mode_form();


    if(isset($_GET["add"]))
    {
	    add_mode();
    }
    if(isset($_GET["delete"]))
    {
	    delete_mode($_GET["delete"]);
    }

    all_modes();

}
//print_r ($_POST);

dump_page();

?>
