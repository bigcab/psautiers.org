<?php
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/log.php");
require_once("include/lilypond.php");
require_once("include/check_serv_dev.php");
require_once("include/mots.php");
$title=_("Modification");
ob_start();

//pour le recueil
function list_bases($id_recueil)
{
	$sql="SELECT b.id_base,b.nom_base,r.id_recueil FROM bases b INNER JOIN recueils r ON r.id_base=b.id_base ";
	$req=requete($sql);
	?>
	<select name="id_base" >	        
	
	<?php
	while($response=fetch_array($req))
	{
		if($id_recueil==$response["id_recueil"])
		{
			?><option selected='selected' value="<?php echo $response["id_base"]?>"><?php echo $response["nom_base"] ?></option><?php
		}
		else
		{
			?><option value="<?php echo $response["id_base"]?>"><?php echo $response["nom_base"] ?></option><?php
		}
	}
	?>
	</select>
	<?php
}

function convert_checkbox($str)
{
	return ($str=="on")?1:0;
}


//update recueil fait bugger
//pour l'instant j'efface tout et je reinsere
//ce qu'il faudra faire c'est prendre tous recueils(doublons)
//et les modifier un par un de la même façon

function update_recueil()
{
	begin_box(_("Modification d'un recueil"),"update_recueil");
	if(!write_in_recueil($_GET["id_recueil"]))
	{
		
		msg(_("Vous n'avez pas les permissions nécessaires"));
		msg_return_to("show.php?id_base=".$_GET["id_base"]);
		end_box();
		return ;
	}
	
	
	$vars=array("titre","id_base");       
        if ( !check_post($vars) )
        {
                msg(_("Veuillez renseigner tous les champs"));
                msg_return_to("update.php?id_base=".$_GET['id_base']."&amp;id_recueil=".$_GET['id_recueil']."");
                end_box();
                return ;
        }
        
        $req=requete("SELECT image_titre_recueil_jpg,image_table_matieres
        FROM recueils
        WHERE id_recueil='".$_GET["id_recueil"]."'
        ");
        $response=fetch_array($req);
        
        
        $extensions_valides=array("jpeg","jpg","gif","png","bmp","tif","tiff");
        if(check_file("image_titre_recueil_jpg"))
        {
                $image_titre_recueil_jpg=upload("image_titre_recueil_jpg",$extensions_valides,"images_titres");
        	if($image_titre_recueil_jpg==NULL)
        	{
                	msg(_("Erreur lors de l'upload de l'image du titre du recueil"));
                	msg_return_to("show.php?id_base=".$_GET["id_base"]);
                	end_box();
                	return;
        	}
                delete_if_possible($response["image_titre_recueil_jpg"]);
        }
        else
        {
                $image_titre_recueil_jpg=$response["image_titre_recueil_jpg"];
        }
        
        $extensions_valides=array("jpeg","jpg","gif","png","bmp","tif","tiff");
        $vars_file=array("image_table_matieres");
        if (check_files($vars_file))
        {
                $image_table_matieres=upload("image_table_matieres",$extensions_valides,"images_table_matieres");
        	if($image_table_matieres==NULL)
        	{
                	msg(_("Erreur lors de l'upload de l'image de la table des matières"));
                	msg_return_to("show.php?id_base=".$_GET["id_base"]);
                	end_box();
                	return;
        	}
                delete_if_possible($response["image_table_matieres"]);
        }
        else
        {
                $image_table_matieres=$response["image_table_matieres"];
        }
        
        
        global $update_recueil_form_show_callback;
        foreach($update_recueil_form_show_callback as $key=> $elem)
        {
        	init_post_var($key);
        }
        
        $sql = "UPDATE recueils SET 
                `id_base` = '{$_POST["id_base"]}',
		`titre_uniforme` = '{$_POST["titre_uniforme"]}',
		`titre` = '{$_POST["titre"]}',
		image_titre_recueil_jpg='".$image_titre_recueil_jpg."',
		image_table_matieres='".$image_table_matieres."',
		`abreviation` = '{$_POST["abreviation"]}',
		`imprimeur` = '{$_POST["imprimeur"]}',
		`lieu` = '{$_POST["lieu"]}',
		`timbre` = '".convert_checkbox($_POST["timbre"])."',
		`solmisation` = '".convert_checkbox($_POST["solmisation"])."',
		`date_impression` = '{$_POST["date_impression"]}',
		`comment_public` = '{$_POST["comment_public"]}',
		`comment_reserve` = '{$_POST["comment_reserve"]}',
		`date_revision` = '".time()."',
		`nom_auteur_revision` = '{$_SESSION["pseudo"]}',
		`commentaire_revision` = '{$_POST["comment_revision"]}',
		`editeur` = '{$_POST["editeur"]}',
		`adresse_biblio` = '{$_POST["adresse_biblio"]}',
		`auteur` = '{$_POST["auteur"]}',
		`compositeur` = '{$_POST["compositeur"]}',
		`description_materielle` = '{$_POST["description_materielle"]}',
		`sources_bibliographiques` = '{$_POST["sources_bibliographiques"]}',
		`litterature_secondaire` = '{$_POST["litterature_secondaire"]}',
		`bibliotheque`='{$_POST["bibliotheque"]}',
		`cote`='{$_POST["cote"]}',
		`updated`='0'
		 WHERE id_recueil='{$_GET["id_recueil"]}'" ;
        requete($sql);
        //now update updated variables
	requete("UPDATE bases SET updated='0' WHERE id_base='".$_GET['id_base']."'");
	msg(_("Modification effectuée avec succès"));
	msg_return_to("show.php?id_base=".$_GET["id_base"]);
	end_box();
}

function update_recueil_form()
{
	?>
	 <form action="?update=recueil&amp;id_recueil=<?php echo $_GET["id_recueil"]?>&amp;id_base=<?php echo $_GET["id_base"] ?>" method='post' enctype="multipart/form-data">
	<?php
	begin_box(_("Modification d'un recueil"),"recueil");
	if(!write_in_recueil($_GET["id_recueil"]))
	{
		msg(_("Vous n'avez pas les permissions nécessaires pour modifier cette base"));
		msg(link_to(_("Retour à la consultation"),"show.php"));
		end_box();
		return;
	}
       	$req=requete("SELECT * FROM recueils WHERE id_recueil='{$_GET["id_recueil"]}'");
       	$vars=fetch_array($req);
        ?>
        <tr>
        <td>
        <!--
        Script pour cacher/afficher les champs pour uploader les fichiers
        images
        This script will hide/show the field to reupload the files
        -->
        <table>
        <tr>        
                <td><?=_("Base sélectionnée")?></td>
                <td>
                <table>
                <tr>
                        <td>
                        <?php
                        list_bases($_GET["id_recueil"]);        
                        ?>
                        </td>
                </tr>
                </table>
                </td>
        </tr>
        
        
        
        <?php
        /*
        add_recueil_form_show_titre($vars["titre"]);
        add_recueil_form_show_titre_uniforme($vars["titre_uniforme"]);
        add_recueil_form_show_abreviation($vars['abreviation']);
        //special for update
        update_recueil_form_show_image_titre_recueil_jpg();
        update_recueil_form_show_image_table_matieres();
        
        add_recueil_form_show_imprimeur($vars['imprimeur']);
        add_recueil_form_show_editeur($vars['editeur']);
        add_recueil_form_show_adresse_biblio($vars['adresse_biblio']);
        add_recueil_form_show_auteur($vars['auteur']);
        add_recueil_form_show_compositeur($vars['compositeur']);
        add_recueil_form_show_lieu($vars['lieu']);
        add_recueil_form_show_solmisation($vars['solmisation']);
        add_recueil_form_show_timbre($vars['timbre']);
        add_recueil_form_show_date_impression($vars['date_impression']);
        add_recueil_form_show_description_materielle($vars['description_materielle']);
        add_recueil_form_show_sources_bibliographiques($vars['sources_bibliographiques']);
        add_recueil_form_show_litterature_secondaire($vars['litterature_secondaire']);
        add_recueil_form_show_comment_public($vars['comment_public']);
        add_recueil_form_show_comment_reserve($vars['comment_reserve']);
        add_recueil_form_show_bibliotheque($vars['bibliotheque']);
        add_recueil_form_show_cote($vars['cote']);
        */
        output_show_form($vars,$_SESSION["mode"],"update","recueil");
        ?>
        
      
        <!--<tr>
                <td>Nom de l'auteur de la fiche</td>
                <td><input type="text" size="80" name="nom_auteur_fiche" value="<?php echo $vars["nom_auteur_fiche"]?>"/></td>
        </tr>-->
        </table>
        </td>
        </tr>
        <tr>
                <td colspan="2" align="right">
                	<input type="hidden" />
                	<input type="reset" value="Réinitialiser"/>
                	<input type="submit" name="submit" value="Valider"/>
                </td>
        </tr>
        <?php
        end_box();
        ?>
        </form>
        <?php
}

/*
Use this function to reupload
$sqlvar is fichier_xml/image_incipit_jpg
it is the name of the field in the sql db
$var_name : variable name $_FILE[$var_name] is the file
                reup_$var_name  is the select form
         
   
upload($file,$extensions_valides,$dossier)
*/
function reupload_if_allowed($sqlvar,$var_name,$extensions_valides,$directory)
{
        // if the user wants to reup the file
        if(check_file($var_name))
        {
                $file=upload($var_name,$extensions_valides,$directory);
                $req=requete("SELECT $sqlvar FROM pieces WHERE id_piece='".$_GET["id_piece"]."'");
                $response=fetch_array($req);
                delete_if_possible($response[$sqlvar]);
                //Update the database
                requete("UPDATE pieces SET
                $sqlvar='".$file."'
                WHERE id_piece='".$_GET["id_piece"]."'");
                return $file;
        }
}


function update_piece()
{
	begin_box(_("Modification d'une pièce"),"modif_piece");
	if(!$_SESSION["admin"])
	{
		$req=requete("SELECT b.id_base FROM bases b LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner WHERE b.owner='".$_SESSION["id"]."' OR (g.id_user='".$_SESSION["id"]."' AND b.permissions_groupe='2') OR (b.permissions_others='2')");
		if(num_rows($req)==0)
		{
			msg(_("Vous n'êtes pas autorisé à modifier cette pièce"));
			msg_return_to("show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]);
			end_box();
			return;
		}
	}
	
	
	$vars=array("titre","page");
	if(!check_post($vars))
	{
		msg(_("Veuillez renseigner tous les champs obligatoires"));
		end_box();
		return;
	}
	//reupload_if_allowed($sqlvar,$var_name,$extensions_valides,$directory)
	$extensions=array("mus");
	reupload_if_allowed("fichier_finale","fichier_finale",$extensions,"fichiers_finale");
	
	$extensions=array("jpg","jpeg","png","gif","tif","tiff");
	
	reupload_if_allowed("image_incipit_jpg","incipit_jpg",$extensions,"incipits_jpg");
	reupload_if_allowed("fichier_jpg","image_jpg",$extensions,"images_jpg");
	
	$extensions=array("xml"); 
	$musicxml_file=reupload_if_allowed("fichier_xml","fichier_musicxml",$extensions,"xml");
	
	if($_POST["valide"]=="true")
	{
	        $valide=1;
	}
	else
	{
	        $valide=0;
	}
	
	
	if($_POST["psaume"]=="true")
	{
	        $psaume=1;
	}
	else
	{
	        $psaume=0;
	}
	global $update_piece_form_show_callback;
	foreach($update_piece_form_show_callback as $key=> $elem)
	{
		init_post_var($key);
	}
	
	if($valide==1)
	{
	        $sql="
	        UPDATE pieces SET 
                titre= '".$_POST["titre"]."',
                auteur= '".$_POST["auteur_texte"]."',
                note_finale= '".$_POST["note_finale"]."',
                ambitus= '".$_POST["ambitus"]."',
                armure= '".$_POST["armure"]."',
                cles= '".$_POST["cles"]."',
                rubrique= '".$_POST["rubrique"]."',
                nombre_parties= '".$_POST["nombre_parties"]."',
                concordances='".$_POST["concordances"]."',
                texte_additionnel= '".$_POST["texte_additionnel"]."',
                code_table_ref_3= '',
                code_table_ref_4= '',
                code_table_ref_5= '',
                comment_public= '".$_POST["comment_public"]."',
                comment_revision= '".$_POST["comment_revision"]."',
                compositeur= '".$_POST["compositeur"]."',
                auteur='".$_POST["auteur_texte"]."',
                timbre= '".$_POST["timbre"]."',
                valide= '".$valide."',
                date_validation='".time()."',
                auteur_validation= '".$_SESSION["pseudo"]."',
                psaume='{$psaume}'
	        WHERE id_piece='".$_GET["id_piece"]."'";
	}
	else
	{
	        $sql="
	        UPDATE pieces SET 
                titre= '".$_POST["titre"]."',
                auteur= '".$_POST["auteur_texte"]."',
                note_finale= '".$_POST["note_finale"]."',
                ambitus= '".$_POST["ambitus"]."',
                armure= '".$_POST["armure"]."',
                cles= '".$_POST["cles"]."',
                rubrique= '".$_POST["rubrique"]."',
                nombre_parties= '".$_POST["nombre_parties"]."',
                concordances='".$_POST["concordances"]."',
                texte_additionnel= '".$_POST["texte_additionnel"]."',
                code_table_ref_3= '',
                code_table_ref_4= '',
                code_table_ref_5= '',
                comment_public= '".$_POST["comment_public"]."',
                comment_revision= '".$_POST["comment_revision"]."',
                compositeur= '".$_POST["compositeur"]."',
                auteur='".$_POST["auteur_texte"]."',
                timbre= '".$_POST["timbre"]."',
                psaume='$psaume'
	        WHERE id_piece='".$_GET["id_piece"]."'";
	}
	requete($sql);
	
	
	//now update updated variables
	requete("UPDATE recueils SET updated='0' WHERE id_recueil='".$_GET['id_recueil']."'");
	requete("UPDATE bases SET updated='0' WHERE id_base='".$_GET['id_base']."'");
	$id=$_GET["id_piece"];
	$nom_groupe_texte=$_POST["nom_groupe_texte"];
	$id_groupe_texte=$_POST["id_groupe_texte"];
	$biblio_texte=$_POST["biblio_texte"];
	$auteur=$_POST["auteur"];
	
	
	// what if xml is reup
	if(check_file("fichier_musicxml"))
	{
		//first we delete everything
		msg("Reupload du fichier xml  : succès");
		msg("Mise à jour des données");
		// first for text
		//we select the id_text in the piece
		$req_id_text=requete("SELECT id_text
			FROM parts pts
			WHERE id_piece='$id'");
		while($response=fetch_array($req_id_text))
		{
			// problem if text is in another score
			// we count first if it is on another one
			$nb=mysql_result(requete("SELECT COUNT( DISTINCT id_piece) 
						FROM parts pts 
						WHERE id_text='{$response["id_text"]}'"),0);
			if($nb==1)
			{
				// only in the piece we are updating
				// => we delete without any worry
				msg("Suppression de textes");
				requete("DELETE FROM textes WHERE id_text='{$response['id_text']}'");
			}
			
						
		}
		
		// we do the same for melodies
		$req_id_melodie=requete("SELECT id_melodie 
					FROM parts pts 
					WHERE id_piece='$id'");
					
		while($response=fetch_array($req_id_melodie))
		{
			// problem if text is in another score
			// we count first if it is on another one
			$nb=mysql_result(requete("SELECT COUNT( DISTINCT id_piece) 
						FROM parts pts 
						WHERE id_melodie='{$response["id_melodie"]}'"),0);
			if($nb==1)
			{
				msg("Suppression d'une mélodie");
				// only in the piece we are updating
				// => we delete without any worry
				requete("DELETE FROM melodies WHERE id_melodie='{$response['id_melodie']}'");
			}
			
						
		}
		
		// now we just have to delete the parts one part is in only one piece
		requete("DELETE FROM parts WHERE id_piece='$id'");
		msg("Suppression des parties");
		
		if($psaume==1)
		{
#			now we have to delete everything related to word stored in database
			$tables=array("ent_db", "ent_mus_db", "hiatus_db", "accent_db", "accent_mus_db","h_db","h_mus_db") ;
			foreach ($tables as  $table)
			{
				requete("DELETE FROM $table WHERE id_piece='$id'");
			}
			msg("Suppression des données dans la base de donnée de mots");
		}

		// now we just have to parse the file and add it
		// copy paste from add.php
		
		msg("Analyse du nouveau fichier xml");
		//parsing instantanné du musicxml
		$music_xml=new music_xml_class($musicxml_file);
		
		$clef=$music_xml->clef;
		//ambitus
		$ambitus=$music_xml->ambitus;
		//nombre de parties
		$nb_parts=$music_xml->nb_parts;
		//armure
		$fifths=$music_xml->parts[0]->fifths;
		$armure="";
		// last note
		$note_finale=$music_xml->last_note;
		
		//on s'occupe de la mélodie
		$melodies=get_melodies($music_xml->parts);
		$lyrics=get_lyrics($music_xml->parts);
		
		//$lyrics=array_map('utf8_decode',$lyrics);
		$lyrics=htmlspecialchars_array($lyrics);
		$lyrics=array_map('mysql_real_escape_string',$lyrics);
	
		
		if ( ( empty($_POST["nb_beats"]) ) || (empty($_POST["reference_tempo"])) ) 
		{
			$nb_beats="60";
			$reference_tempo="4";
		}
		else
		{
			$nb_beats=$_POST["nb_beats"];
			$reference_tempo=$_POST["reference_tempo"];
		}
		msg("Conversion au format jpeg");
		$out=music_xml_to_png($musicxml_file,$nb_beats,$reference_tempo); 
		init_post_var("want_mp3");
		$mp3="";
		if (convert_checkbox($_POST['want_mp3']))$mp3=midi_to_mp3($out);
		if(!empty($mp3))
		{
			$mp3=$mp3.".mp3";
		}
		$png=PNG_DIR.$out;
		$midi=PNG_DIR.$out; 
		
		requete("UPDATE pieces SET
		note_finale= '".$note_finale."',
                ambitus= '".$ambitus."',
                armure= '".$armure."',
                cles= '".$clef."',
                nombre_parties= '".$nb_parts."',
                mp3='$mp3',
                png_lilypond='$png'
                WHERE id_piece='$id'
		");
		
		//tout a été fait on nettoie
		//voir dans lilypond.php
		msg("Nettoyage des fichiers temporaires"); 
		clean_files($out);
		
		msg("Début de l'ajout des nouvelles données : parties , textes et melodies");
		for($i=0;$i<$nb_parts;$i++)
		{
			if(!empty($melodies[$i]))
			{
				$req=requete("SELECT id_melodie FROM melodies WHERE CONVERT( `melodies`.`melodie`
	USING utf8 )='".$melodies[$i]."'");
				if(num_rows($req)==0)
				{
					requete("INSERT INTO melodies (id_melodie,melodie,indice_partie) VALUES ("
					."NULL,"
					."'".$melodies[$i]."',"
					."'".($i+1)."')");
					$req=requete("SELECT id_melodie FROM melodies WHERE CONVERT( `melodies`.`melodie`
	USING utf8 )='".$melodies[$i]."'");
				}
			
			
				$response=fetch_array($req);
				$id_melodie=$response["id_melodie"];
			

			}
			else
			{
				$id_melodie="";
			}
			if(!empty($lyrics[$i]))
			{
				init_post_var("references");
				$req=requete("SELECT id_text FROM textes WHERE  texte = '".$lyrics[$i]."'");
				if(num_rows($req)==0)
				{
				
					requete("
					INSERT INTO textes 
					(
						id_text,
						texte,
						auteur,
						biblio_texte,
						id_groupe_texte,
						references_groupe_texte
					) 
					VALUES 
					(
						NULL,
						'".$lyrics[$i]."',
						'".$_POST["auteur_texte"]."',
						'".$_POST['biblio_texte']."',
						'$id_groupe_texte',
						'".$_POST["references"]."'
					)
					");
					$req=requete("SELECT id_text FROM textes WHERE  texte = '".$lyrics[$i]."'");
				}
			
			
				$response=fetch_array($req);
				$id_text=$response["id_text"];
			}
			else
			{
				$id_text="";
			}
			
			// On a récupéré les id des textes et melodies
			//We found the ids of the lyric and melodies
		
			if(!empty($id_melodie))
			{
				// On insere à la partie
				requete("INSERT INTO parts (id_part,id_piece,id_text,id_melodie,indice_partie) VALUES ("
					."NULL,"
					."'".$id."',"
					."'".$id_text."',"
					."'".$id_melodie."',
					'".($i+1)."'
					)");
			}
		}
		
#		updating the words now
		if($psaume==1)
		{
			$db=new db_mots_class($id);
			$db->update_db();
			$db=new db_mots_class($id,"musique");
			$db->update_db();
			msg("Mise à jour de la base donnée syntaxique");
		}
			
		msg("Fin de la mise à jour des données rattachées au fichier musicxml");
	}
	
	
	//on prend tous les groupes textes rattachés à la pièce
	$req1=requete("SELECT id_text
			FROM parts pts
			WHERE id_piece='$id'");
	
	if($id_groupe_texte=="other")
	{
		if(!empty($nom_groupe_texte))
		{
			requete("INSERT INTO groupe_textes
			(
				`nom_groupe_texte`
			)
			VALUES 
			(
				'$nom_groupe_texte'
			)");
			$id_groupe_texte=mysql_insert_id();
		}
	}
	while($response=fetch_array($req1))
	{
		//take id_text
		$id_text=$response["id_text"];
		requete("UPDATE textes SET `auteur`='{$_POST["auteur_texte"]}',`biblio_texte`='$biblio_texte',`id_groupe_texte`='$id_groupe_texte' WHERE id_text='$id_text'");
	}
	
	
	
	
	requete("UPDATE table_matieres SET pagination_ancienne='".$_POST["pagination_ancienne"]."',rang='".$_POST["page"]."', id_recueil='".$_POST["id_recueil"]."' WHERE id_piece='".$_GET["id_piece"]."' AND id_recueil='".$_GET["id_recueil"]."'");
	
	
	
	
	msg(_("Modification exécutée avec succès"));
	msg_return_to("show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_POST["id_recueil"]);
	end_box();
}

function list_recueils()
{
	
        $req=requete("SELECT id_recueil,titre FROM recueils");
        $nb=num_rows($req);
        if ($nb==0)
        {
                echo _("Aucun recueil enregistré");
                return ;
        }
        ?>
        <select style="width:100%;" name="id_recueil" id="id_recueil">     
        <?php
        while($response=fetch_array($req))
        {
        	if($response["id_recueil"]==$_GET["id_recueil"])
        	{
        		echo "<option selected='selected' value='".$response["id_recueil"]."'>".$response["titre"]."</option>";
        	}
        	else
        	{
        		echo "<option value='".$response["id_recueil"]."'>".$response["titre"]."</option>";
        	}
        }
        ?>
        	
        </select>
        <?php
}


function list_groupes_textes($selected)
{
	$req=requete("SELECT id_groupe_texte,nom_groupe_texte FROM groupe_textes");
        $nb=num_rows($req);
        ?>
        <select onchange="update_groupe_texte(this)" name="id_groupe_texte">     
        <?php
        while($response=fetch_array($req))
        {
        	if($response["id_groupe_texte"]==$selected)
        	{
        		echo "<option selected='selected' value='".$response["id_groupe_texte"]."'>".$response["nom_groupe_texte"]."</option>";
        	}
        	else
        	{
        		echo "<option value='".$response["id_groupe_texte"]."'>".$response["nom_groupe_texte"]."</option>";
        	}
        }
        ?>
        <option value="other"><?=_("Autre")?></option>	
        
        </select>
        <input id="nom_groupe_texte" 
	<?php
	if($nb==0)
	{
		echo "type='text'";
	}
	else
	{
		echo "type='hidden'";
	}
	?>
	 name="nom_groupe_texte" size="30"/>
        <?php
}



function update_piece_form()
{
	$req=requete("SELECT 
		p.titre AS titre_piece,
		r.titre AS titre_recueil,
		tm.rang,
		tm.pagination_ancienne,
		p.comment_public,
		p.comment_revision,
		p.comment_reserve,
		p.compositeur,
		p.auteur,
		p.fichier_finale,
		p.fichier_xml,
		p.rubrique,
		p.note_finale,
		p.concordances,
		p.texte_additionnel,
		p.ambitus,
		p.armure,
		p.cles,
		p.nombre_parties,
		p.comment_public,
		p.comment_revision,
		p.date_validation,
		p.timbre,
		p.valide,
		p.auteur_validation,
		p.nom_auteur_fiche ,
		t.id_groupe_texte,
		t.biblio_texte,
		p.psaume
		FROM pieces p 
		INNER JOIN table_matieres tm ON tm.id_piece=p.id_piece 
		INNER JOIN recueils r ON r.id_recueil=tm.id_recueil 
		LEFT OUTER JOIN parts pts ON pts.id_piece=p.id_piece
		LEFT OUTER JOIN textes t ON t.id_text=pts.id_text
		WHERE p.id_piece='".$_GET["id_piece"]."' AND r.id_recueil='".$_GET["id_recueil"]."'");
	if(num_rows($req)==0)
	{
		begin_box(_("Modification d'une pièce")." ","piece");
		msg(_("La pièce n'existe pas"));
		msg_return_to("show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]);
		end_box();
		return;
	}
	$response=fetch_array($req);
	$titre_piece=$response["titre_piece"];
	$titre_recueil=$response["titre_recueil"];
	?>
	<form action="?update=piece&amp;id_piece=<?= $_GET['id_piece'] ?>&amp;id_base=<?=$_GET['id_base'] ?>&amp;id_recueil=<?= $_GET['id_recueil'] ?>"  method='post' enctype="multipart/form-data" >
	<?php
	begin_box(_("Modification de la pièce")." ".$titre_piece,"piece");
	if(!write_in_piece($_GET["id_piece"]))
	{
		msg(_("Vous n'êtes pas autorisé à modifier cette pièce"));
		msg_return_to("show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]);
		end_box();
		return;
	}
	
        ?>
	<tr>
	<td>
        <!--
        Script pour cacher/afficher les champs pour uploader les fichiers
        images
        This script will hide/show the field to reupload the files
        see in js/update.js
        -->
        
        
        <table>
        
        <tr>
        	<td><?=_("Recueil")?></td>
        	<td><input type="hidden" name="old_recueil" id="old_recueil" value="<?=$_GET['id_recueil']?>"/><?php list_recueils();?></td>
        </tr>
        <?php
        /*update_piece_form_show_recueil();
        add_piece_form_show_page($response['rang']);
        add_piece_form_show_titre_piece($titre_piece);
        
        update_piece_form_show_fichier_finale();
        update_piece_form_show_image_jpg();
        update_piece_form_show_incipit_jpg();
        
        add_piece_form_show_pagination_ancienne($response['pagination_ancienne']);
        add_piece_form_show_rubrique ($response['rubrique']);
        add_piece_form_show_note_finale ($response['note_finale']);
        add_piece_form_show_ambitus ($response['ambitus']);
        add_piece_form_show_armure ($response['armure']);
        add_piece_form_show_cles ($response['cles']);
        add_piece_form_show_nombre_parties ($response['nombre_parties']);
        add_piece_form_show_auteur ($response['auteur']);
        add_piece_form_show_compositeur ($response['compositeur']);
        add_piece_form_show_timbre ($response['timbre']);
        add_piece_form_show_concordances ($response['concordances']);
        add_piece_form_show_texte_additionnel ($response['texte_additionnel']);
        
        update_piece_form_show_groupes_textes ($response["id_groupe_texte"]);
        
        add_piece_form_show_biblio_texte ($response['biblio_texte']);
        add_piece_form_show_comment_public ($response['comment_public']);
        */
        output_show_form($response,$_SESSION["mode"],"update","piece");
        ?>

        
        <?php
        if($_SESSION['pseudo']==$response["nom_auteur_fiche"])
        {
        	add_piece_form_show_comment_reserve ($response['comment_reserve']);
        }
        add_piece_form_show_comment_revision ($response['comment_revision']);
     
        add_piece_form_show_psaume($response['psaume']);
        // Has it been validated
        //yes => print the date 
        if ($response["valide"]==1)
        {
                ?>
                <tr>
                        <td><?=_("Valide")?></td>
                        <td><?=_("Oui")?></td>
                </tr>
                <tr>
                        <td><?=_("Date de validation")?></td>
                        <td><?=date("d/m/Y H:i",$response["date_validation"])?></td>
                </tr>
                <tr>
                        <td><?=_("Auteur de la validation")?></td>
                        <td><?=$response["auteur_validation"]?></td>
                </tr>
                <?php
        }
        // No
        else
        {
                ?>
                <tr>
                        <td><?=_("Valide")?></td>
                        <td>
                                <input type="radio" name="valide" value="true"/><?=_("Oui")?>
                                <input type="radio" name="valide" value="false" checked='checked'/><?=_("Non")?>
                        </td>
                </tr>
                <?php
        }
        
        
        
        ?>        
        
	</table>
	</td>
        </tr>
        <tr>
                <td align="right"><input type="reset" /><input type="button" onclick="verify()" name="Valider" value='<?=_("Valider")?>'/>
                <script type="text/javascript"><!--update_champ();--> </script>
                </td>
        </tr>
        
        <?php
        end_box();
        ?>
        </form>
        <?php
}

function update_base_form()
{
	// Les non-admins peuvent aller voir allieurs !
        if (!$_SESSION['admin']) return;
        $owners="";
        ?>
         
        <form action="?update=base&amp;id_base=<?=$_GET['id_base'] ?>" method='post' enctype="multipart/form-data">
        <?php
        begin_box(_("Modification d'une base"),"update_base_form");
        $r = requete("SELECT id_user,pseudo FROM users");
        $r2=requete("SELECT * FROM bases WHERE id_base='".$_GET["id_base"]."'");
        if(num_rows($r2)==0)
        {
        	msg(_("La base demandée n'existe pas"));
        	return;
        }
        $response=fetch_array($r2);
        $id_owner=$response["owner"];
        while ($data = fetch_array($r))
        {
        	if($data["id_user"]!=$id_owner)
        	{
        		$owners .= "<option value='".$data['id_user']."'>".$data['pseudo']."</option>\n";
        	}
        	else
        	{
        		$owners .= "<option selected='selected' value='".$data['id_user']."'>".$data['pseudo']."</option>\n";
        	}
        }
	?>
	
                
                <tr>
                        <td><acronym title=" <?=_("Sélectionnez ici le propriétaire de la base.Si vous n'avez pas encore créé l'utilisateur dont vous souhaitez qu'il soit le propriétaire de cette base, merci de le faire auparavant en vous rendant dans l'administration.")?>">Propriétaire de la base</acronym></td>
                        <td>
                        <select name="owner">
                        <?php echo $owners; ?>
                        </select>
                        </td>
                </tr>
                <?php
               /* add_base_form_show_nom($response["nom_base"]);
                add_base_form_show_description($response["description"]);
                add_base_form_show_references($response["references"]);
                */
                 output_show_form($response,$_SESSION["mode"],"update","base");
                ?>
               
                
                
		
		<tr>
		        <td><?=_("Guide Pdf")?></td>
		        <td>
		                <select onchange="update_file_field(this,'guide_pdf')" name="reup_guide_pdf">
		                        <option value="true"><?=_("Oui")?></option>
		                        <option selected='selected' value="false"><?=_("Non")?></option>
		                </select>
		                <input id="guide_pdf" type="hidden" name="guide_pdf"/>
		        </td>
		</tr>

           
                <tr>
                        <td colspan="2" align="right"><input type="reset" name="Reset"/><input type="submit" name="ok" value='<?=_("Valider")?>'/></td>
                </tr>
	<?php
	end_box();
	?>
	</form>
	<?php
}

function update_base()
{
	if (!$_SESSION['admin']) return;
        begin_box(_("Modification d'une base"),"update_base");
        $r2=requete("SELECT nom_base,guide_pdf,banner FROM bases WHERE id_base='".$_GET["id_base"]."'");
        if(num_rows($r2)==0)
        {
        	msg(_("La base demandée n'existe pas"));
        	return;
        }
        $response=fetch_array($r2);
        $guide_pdf=$response["guide_pdf"];
        $banner=$response["banner"];
        if( ($_POST["reup_guide_pdf"]=="true")&& (check_file("guide_pdf")) )
        {
        	$pdf=upload("guide_pdf",array("pdf","PDF"),"pdf");
        	delete_if_possible($guide_pdf);
        	requete("UPDATE bases SET `guide_pdf`='$pdf' WHERE id_base='".$_GET["id_base"]."'");
        }
        global $default_banner;
        if( ($_POST["reup_banner"]=="true")&& (check_file("banner")) )
        {
        	if($banner != $default_banner)
        	{
        		delete_if_possible($banner);
        	}
        	$extensions=array("jpeg","jpg","gif","png","bmp","tif","tiff");
        	$new_banner=upload("banner",$extensions,"banners");
        	
        	requete("UPDATE bases SET `banner`='$new_banner' WHERE id_base='".$_GET["id_base"]."'");
        }
        if( ($_POST["r"]<256)&& ($_POST["r"]>=0) && ($_POST["g"]<256)&& ($_POST["g"]>=0) && ($_POST["b"]<256)&& ($_POST["b"]>=0))
        {
		    requete("UPDATE bases SET 
		    		`updated`='0',
		    		`owner`='".$_POST["owner"]."',
		    		`nom_base`='".$_POST["nom_base"]."',
		    		`description`='".$_POST["description"]."',
		    		`references`='".$_POST["references"]."', 
		    		`body_background_color`=  'rgb({$_POST["r"]},{$_POST["g"]},{$_POST["b"]})' ,
		    		`mode` ='".$_POST["mode"]."'
		    		WHERE id_base='".$_GET["id_base"]."'");
		    msg(_("Modification effectuée avec succès"));
        }
        else
        {
        	msg("Erreur  : problème avec les valeurs des couleurs de fond");
        }
        msg_return_to("show.php?");
        end_box();
}

function change_permissions_user($id_user,$perm)
{
	if (!$_SESSION['admin']) return; 
	if ($perm != 1 && $perm != 2) return;
	requete("UPDATE users SET permissions = '$perm' WHERE id_user = '$id_user'");
	begin_box(_("Permissions correctement mises à jour"),"update_perm");
	msg(($perm == 1)?_("L'utilisateur est désormais administrateur."):_("L'utilisateur n'est plus administrateur."));
	end_box();
	
}
function delete_user($id_user)
{
	begin_box(_("Suppression d'un utilisateur"),"del_user");
	if($id_user==$_SESSION["id"])
	{
		msg(_("Vous ne pouvez pas vous supprimer vous même."));
		end_box();
		return ;
	}
	if(!$_SESSION["admin"])
	{
		msg(_("Vous n'avez pas les droits nécessaires pour supprimer un utilisateur"));
		end_box();
		return ;
	}
	$req=requete("SELECT pseudo FROM users WHERE id_user = '".$id_user."' ");
	if(num_rows($req)==0)
	{
	        msg_(("Utilisateur inexistant"));
	        msg_return_to("users_groups.php?view=users");
	        end_box();
	        return;
	}
	$response=fetch_array($req);
	$pseudo=$response["pseudo"];
	requete("DELETE FROM users WHERE id_user = '".$id_user."'");
	requete("DELETE FROM groupes WHERE id_groupe = '".$id_user."'");
	requete("DELETE FROM groupes WHERE id_user = '".$id_user."'");
	msg(_("L'utilisateur")." ".$pseudo._(" a bien été enlevé de la base"));
	msg_return_to("show.php?view=users");
	end_box();
}


function verify_del_user($id_user)
{
	begin_box("Attention!","warning");
	$req=requete("SELECT pseudo FROM users WHERE id_user = '".$id_user."' ");
	$response=fetch_array($req);
	$pseudo=$response["pseudo"];
	msg(_("Vous êtes sur le point de supprimer l'utilisateur")." ".$pseudo);
	msg(_("Etes vous sûr de vouloir continuer?"));
	msg("<form action='?del_user=$id_user&amp;id_base=".$_GET["id_base"]."' method='POST'><input type='hidden' name='del_user' value='$id_user' /><input type='submit' name='accept' value='"._("Oui")."'/><input type='submit' name='accept' value='"._("Non")."'/></form>");
	end_box();
}

function delete_base($id_base)
{
        $req=requete("SELECT nom_base,banner FROM bases WHERE id_base='".$id_base."'");
        
        if(num_rows($req)==0)
	{
	        begin_box(_("Suppression d'une base"),"del_base");
	        msg(_("La base que vous voulez supprimer n'existe pas"));
	        msg_return_to("show.php");
	        end_box();
	        return;
	}
	$response=fetch_array($req);
	$nom_base=$response["nom_base"];
	$banner=$response["banner"];
	begin_box(_("Suppression de la base")." ".$nom_base,"del_base");
        if(!$_SESSION["admin"])
        {
                msg(_("Vous n'avez pas les droits nécessaires pour supprimer une base"));
		end_box();echo 'ok1';
		msg_return_to("show.php");
		return ;
        }
        
        $req=requete("SELECT id_recueil FROM recueils r WHERE id_base='$id_base'");
        while($response=fetch_array($req))
        {
        	delete_recueil_fun($response["id_recueil"],$id_base);
        }
        
        requete("DELETE FROM bases WHERE id_base='".$id_base."'");
        delete_if_possible($banner);
        msg(_("La base")." ".$nom_base._(" a été supprimée"));
        msg_return_to("show.php?");
        end_box();
}

function verify_del_base($id_base)
{
        begin_box("Attention!","warning");
	$req=requete("SELECT nom_base FROM bases WHERE id_base = '".$id_base."' ");
	if(num_rows($req)==0)
	{
	        msg(_("La base que vous voulez supprimer n'existe pas"));
	        msg_return_to("show.php");
	        end_box();
	        return;
	}
	$response=fetch_array($req);
	$nom_base=$response["nom_base"];
	msg(_("Vous êtes sur le point de supprimer la base")." ".$nom_base);
	msg(_("Etes vous sûr de vouloir continuer?"));
	msg("<form action='?del_base=$id_base' method='POST'><input type='hidden' name='del_base' value='$id_base' /><input type='submit' name='accept' value='"._("Oui")."'/><input type='submit' name='accept' value='"._("Non")."'/></form>");
	end_box();
}

function verify_del_recueil($id_recueil)
{
         begin_box("Attention!","warning");
	$req=requete("SELECT titre FROM recueils WHERE id_recueil = '".$id_recueil."' ");
	if(num_rows($req)==0)
	{
	        msg(_("Le recueil que vous voulez supprimer n'existe pas"));
	        msg_return_to("show.php?id_base=".$_GET["id_base"]);
	        end_box();
	        
	        return;
	}
	$response=fetch_array($req);
	$titre=$response["titre"];
	msg(_("Vous êtes sur le point de supprimer le recueil")." ".$titre);
	msg(_("Etes vous sûr de vouloir continuer?"));
	msg("<form action='?del_recueil=$id_recueil&amp;id_base=".$_GET["id_base"]."' method='POST'><input type='hidden' name='del_recueil' value='$id_recueil' /><input type='submit' name='accept' value='"._("Oui")."'/><input type='submit' name='accept' value='"._("Non")."'/></form>");
	end_box();
}

function verify_del_piece($id_piece)
{
         begin_box("Attention!","warning");
	$req=requete("SELECT titre FROM pieces WHERE id_piece = '".$id_piece."' ");
	if(num_rows($req)==0)
	{
	        msg(_("La pièce que vous voulez supprimer n'existe pas"));
	        msg_return_to("show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]);
	        end_box();
	        
	        return;
	}
	$response=fetch_array($req);
	$titre=$response["titre"];
	msg(_("Vous êtes sur le point de supprimer la pièce")." ".$titre);
	msg(_("Etes vous sur de vouloir continuer?"));
	msg("<form action='?del_piece=$id_piece&amp;id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]."' method='POST'><input type='hidden' name='del_piece' value='$id_piece' /><input type='submit' name='accept' value='"._("Oui")."'/><input type='submit' name='accept' value='"._("Non")."'/></form>");
	end_box();
}

/*
delete_recueil_fun
we assume that the rights to remove the piece has already been checked
used in recursive mode
*/
function delete_recueil_fun($id,$id_base)
{
        $req=requete("SELECT titre,image_titre_recueil_jpg,image_table_matieres FROM recueils WHERE id_recueil = '".$id."' ");
        
        
	if(num_rows($req)==0)
	{

	        special_msg(_("Suppression d'un recueil"),_("Le recueil que vous voulez supprimer n'existe pas"));
	        
	        
	        return;
	}
	$response=fetch_array($req);
	$titre=$response["titre"];
	delete_if_possible($response["image_titre_recueil_jpg"]);
	delete_if_possible($response["image_table_matieres"]);
	if(!write_in_recueil($id))
        {
                special_msg(_("Suppression du recueil")." ".$titre,_("Vous n'avez pas les droits nécessaires pour supprimer ce recueil"));
		
		return ;
        }
        $req=requete("SELECT id_piece FROM table_matieres tm
        		WHERE id_recueil='$id' 
        ");
        while($response=fetch_array($req))
        {
        	delete_piece_fun($response["id_piece"],$id,$_GET["id_base"]);
        }
        requete("DELETE FROM table_matieres WHERE id_recueil='$id'");
        requete("DELETE FROM recueils WHERE id_recueil='".$id."'");
        special_msg(_("Suppression du recueil")." ".$titre,_("Le recueil")." ".$titre._(" a été supprimé"));
        //now update updated variables
	
	requete("UPDATE bases SET updated='0' WHERE id_base='".$id_base."'");
		
        

               
}


function delete_recueil($id)
{
        $req=requete("SELECT titre FROM recueils WHERE id_recueil = '".$id."' ");
	if(num_rows($req)==0)
	{
	        begin_box(_("Suppression d'un recueil"));
	        msg(_("Le recueil que vous voulez supprimer n'existe pas"));
	        msg_return_to("show.php?id_base=".$_GET["id_base"]);
	        end_box();
	        
	        return;
	}
	$response=fetch_array($req);
	$titre=$response["titre"];
	begin_box(_("Suppression du recueil")." ".$titre);
	if(!write_in_recueil($id))
        {
                special_msg(_("Suppression du recueil")." ".$titre,_("Vous n'avez pas les droits nécessaires pour supprimer ce recueil"));
                msg_return_to("show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=$id");
		end_box();
		return ;
        }
        delete_recueil_fun($id,$_GET["id_base"]);
        
        msg_return_to("show.php?id_base=".$_GET["id_base"]);
        end_box();       
}



/*
delete_piece_fun
we assume that the rights to remove the piece has already been checked
used in recursive mode
Delete piece $id = id_piece
		$id_recueil
*/
function delete_piece_fun($id,$id_recueil,$id_base)
{
        $req=requete("SELECT titre FROM pieces WHERE id_piece = '".$id."' ");
	if(num_rows($req)==0)
	{
	        special_msg(_("Suppression d'une pièce"),_("La pièce que vous voulez supprimer n'existe pas"));
	        
	        return;
	}
	$response=fetch_array($req);
	$titre_piece=$response["titre"];
	$req=requete("SELECT titre FROM recueils WHERE id_recueil='".$id_recueil."'");
	if(num_rows($req)==0)
	{
		special_msg(_("Suppression de la pièce")." ".$titre_piece,_("Le recueil que vous avez spécifié n'existe pas"));
		return;
	}
	$response=fetch_array($req);
	$titre_recueil=$response["titre"];
	
        $req=requete("SELECT * FROM table_matieres WHERE id_piece='".$id."'");
        // La piece existe une fois => on touche à la table pieces
        if(num_rows($req)>1)
        {
        	// On supprime la piece du recueil seulement
        	requete("DELETE FROM table_matieres WHERE id_piece='".$id."' AND id_recueil='".$id_recueil."'");
        	
        	
        	//now update updated variables
		requete("UPDATE recueils SET updated='0' WHERE id_recueil='".$id_recueil."'");
		//update all the bases
		$req2=requete("SELECT tm.id_recueil,r.id_base FROM table_matieres tm 
				INNER JOIN recueils r ON r.id_recueil=tm.id_recueil 
				WHERE tm.id_piece='$id'");
		while($response2=fetch_array($req2))
		{
			$id_rec=$response2["id_recueil"];
			$id_b=$response2["id_base"];
			requete("UPDATE recueils SET updated='0' WHERE id_recueil='".$id_rec."'");
			requete("UPDATE bases SET updated='0' WHERE id_base='".$id_b."'");
		}
		
		
		requete("UPDATE bases SET updated='0' WHERE id_base='".$id_base."'");
		
        	special_msg(_("Suppression de la pièce")." ".$titre_piece,_("La pièce")." ".$titre_piece._(" a été supprimée du recueil")." ".$titre_recueil._(" mais elle est encore présente dans la base"));
        }
        else
        {
        	$req=requete("SELECT mp3,fichier_finale,fichier_xml,png_lilypond,fichier_jpg,image_incipit_jpg FROM pieces WHERE id_piece=$id");
        	
        	//now update updated variables
		requete("UPDATE recueils SET updated='0' WHERE id_recueil='".$id_recueil."'");
		//update all the bases
		
		requete("UPDATE bases SET updated='0' WHERE id_base='".$id_base."'");
		
        	
#        now delete everything in the words databases 	
		$tables=array("h_db", "h_mus_db", "ent_db", "accent_db", "accent_mus_db", "hiatus_db" ,"ent_mus_db" );
		foreach($tables as $table)
		{
			requete("DELETE FROM $table WHERE id_piece='$id'");
		}
		
        	$response=fetch_array($req);
        	delete_if_possible($response["fichier_finale"]);
        	delete_if_possible($response["fichier_xml"]);
        	delete_if_possible("mp3/".$response["mp3"]);
        	$pngs=glob($response["png_lilypond"]."*".".png");
        	foreach($pngs as $png)
        	{
        		delete_if_possible($png);
        	}
        	delete_if_possible($response["fichier_jpg"]);
        	delete_if_possible($response["image_incipit_jpg"]);
        	$req=requete("SELECT id_melodie,id_text FROM parts WHERE id_piece='".$id."'");
        	while($response=fetch_array($req))
        	{
        		requete("DELETE FROM melodies WHERE id_melodie='".$response["id_melodie"]."'");
        		requete("DELETE FROM textes WHERE id_text='".$response["id_text"]."'");
        	}
        	requete("DELETE FROM parts WHERE id_piece='".$id."'");
        	requete("DELETE FROM table_matieres WHERE id_piece='".$id."'");
        	requete("DELETE FROM pieces WHERE id_piece='".$id."'");
        	
        	
        	
        	
        	special_msg(_("Suppression de la pièce")." ".$titre_piece,_("La pièce")." ".$titre_piece._(" a été supprimée de la base"));
        }
        
}


function delete_if_possible($file)
{
	if(!empty($file))
	{
		@unlink($file);
	}
}



function delete_piece($id)
{
        $req=requete("SELECT titre FROM pieces WHERE id_piece = '".$id."' ");
	if(num_rows($req)==0)
	{
	        begin_box(_("Suppression d'une pièce"));
	        msg(_("La pièce que vous voulez supprimer n'existe pas"));
	        msg_return_to("show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]);
	        end_box();
	        
	        return;
	}
	$response=fetch_array($req);
	$titre_piece=$response["titre"];
	$req=requete("SELECT titre FROM recueils WHERE id_recueil='".$_GET["id_recueil"]."'");
	if(num_rows($req)==0)
	{
		msg(_("Le recueil que vous avez spécifié n'existe pas"));
		msg_return_to("show.php?id_base=".$_GET["id_base"]);
		end_box();
		return;
	}
	$response=fetch_array($req);
	$titre_recueil=$response["titre"];
	begin_box(_("Suppression de la pièce")." ".$titre_piece._(" du recueil")." ".$titre_recueil);
	if(!write_in_piece($id))
        {
                msg(_("Vous n'avez pas les droits nécessaires pour supprimer cette pièce"));
                msg_return_to("show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]."&amp;id_piece=$id");
		end_box();
		return ;
        }
        delete_piece_fun($id,$_GET["id_recueil"],$_GET["id_base"]);
        
        msg_return_to("show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]);
        end_box(); 
}


?>
<!--main javascript functions-->
<script language="Javascript" type="text/javascript" src="js/update.js"></script>
<?php

init_get_var("update");
init_get_var("del_user");
init_post_var("accept");
init_get_var("del_base");
init_get_var("del_recueil");
init_get_var("del_piece");
init_get_var("id_base");
init_get_var("id_recueil");
init_get_var("change_permissions_user");
init_get_var("perm");


if(empty($_GET["update"]))
{
	if(!empty($_GET["del_user"]))
	{
		if($_POST["accept"]==_("Oui"))
		{
			delete_user($_GET["del_user"]);
		}
		else if ($_POST['accept']==_("Non"))
		{
			header('Location: users_groups.php');
		}
		else
		{
			verify_del_user($_GET["del_user"]);
		}
	}
	else if(!empty($_GET["del_base"]))
	{
	        if($_POST["accept"]==_("Oui"))
		{
			delete_base($_GET["del_base"]);
		}
		else if ($_POST['accept']==_("Non"))
		{
			header('Location: show.php#');
		}
		else
		{
			verify_del_base($_GET["del_base"]);
		}
	}
	else if(!empty($_GET["del_recueil"]))
	{
	        if($_POST["accept"]==_("Oui"))
		{
			delete_recueil($_GET["del_recueil"]);
		}
		else if ($_POST['accept']==_("Non"))
		{
			header('Location: show.php?id_base='.$_GET['id_base']);
		}
		else
		{
			verify_del_recueil($_GET["del_recueil"]);
		}
	}
	else if(!empty($_GET["del_piece"]))
	{
	        if($_POST["accept"]==_("Oui"))
		{
			delete_piece($_GET["del_piece"]);
		}
		else if ($_POST['accept']==_("Non"))
		{
			header('Location: show.php?id_recueil='.$_GET['id_recueil'].'&amp;id_base='.$_GET['id_base']);
		}
		else
		{
			verify_del_piece($_GET["del_piece"]);
		}
	}
	else if(!empty($_GET["id_piece"]))
	{
		update_piece_form();
	}
	else if(!empty($_GET["id_recueil"]))
	{
		update_recueil_form();
	}
	else if(!empty($_GET["id_base"]))
	{
		update_base_form();
	}
	else if (!empty($_GET["change_permissions_user"]) && isset($_GET['perm']))
	{
		change_permissions_user($_GET['change_permissions_user'],$_GET['perm']); 
	}
}
else
{
	if(!empty($_GET["id_piece"]))
	{
		update_piece();
	}
	else if(!empty($_GET["id_recueil"]))
	{
		update_recueil();
	}
	else if(!empty($_GET["id_base"]))
	{
		update_base();
	}
}
dump_page();
?>
