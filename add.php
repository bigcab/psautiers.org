<?php
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/lilypond.php");
require_once("include/log.php");
require_once("include/check_serv_dev.php");
require_once("include/mots.php");
//titre
$title=_("Ajout");
ob_start();
#DEFINE('TOLY',"musicxml2ly ");
#DEFINE('LILY',"lilypond --png ");
#DEFINE('LY_DIR',"ly/");
#DEFINE('PNG_DIR',"png/");
#DEFINE('MIDI_DIR','midi/');


/*
Add.php to add files
5 functions 
	-delete ($file_path) to delete a file
	-delete_all_files () used in add_piece() to delete uploaded files if a score already exists
	-add_recueil() adds a book collection
	-add_piece() adds a score or songbook
	-add_base() adds a base
	
*/


function delete($fp)
{
	unlink($fp);
}

function delete_all_files()
{
	if(isset($_POST["continue"]))
		{
			if (!empty($_SESSION["musicxml_file"]))
			{

					delete($_SESSION["musicxml_file"]);
					unset($_SESSION["musicxml_file"]);
			}
			if (!empty($_SESSION["incipit_jpg"]))
			{

					delete($_SESSION["incipit_jpg"]);
					unset($_SESSION["incipit_jpg"]);
			}
			if (!empty($_SESSION["image_jpg"]))
			{

					delete($_SESSION["image_jpg"]);
					unset($_SESSION["image_jpg"]);
			}
			if (!empty($_SESSION["fichier_finale"]))
			{

					delete($_SESSION["fichier_finale"]);
					unset($_SESSION["fichier_finale"]);
			}
		}
		else
		{
			// On enlève les fichiers puisqu'ils sont dejà été uploadé
			if(!empty($musicxml_file))
			{
				delete($musicxml_file);
			}
			if(!empty($image_jpg))
			{
				delete($image_jpg);
			}
			if(!empty($fichier_finale))
			{
				delete($fichier_finale);
			}
			if(!empty($incipit_jpg))
			{
				delete($incipit_jpg);
			}
		}
}


function add_piece()
{
        begin_box(_("Ajout d'une pièce"),"ajout_piece");
        
        $incipit_jpg="";
		$image_jpg="";
		$fichier_finale="";
        $musicxml_file="";
        $id_groupe_texte="";
        init_post_var("auteur_texte");
        if(!isset($_GET["id_recueil"]))
        {
                msg(_("Aucun recueil sélectionné"));
                msg_return_to("show.php?");
                end_box();
                return;
        }
        if( !write_in_recueil($_GET["id_recueil"]) )
        {
                msg(_("Vous n'avez pas les permissions nécessaires pour ajouter une pièce à ce recueil"));
                msg_return_to("show.php");
                end_box();
                return;
        }
        $req=requete("SELECT titre FROM recueils WHERE id_recueil='".$_GET["id_recueil"]."'");
        $response=fetch_array($req); 
        $titre_recueil=$response["titre"];
        
        $vars=array("titre","page");
        if (!check_post($vars))
        {
                msg(_("Veuillez renseigner tous les champs"));
                msg_return_to("show.php?id_base=".$_GET["id_base"]."&id_recueil=".$_GET["id_recueil"]);
                end_box();
                return ;
        }
        
        /*
        We find the id of the group of the text (for example BMW)
        First if it does not exist
        */
        if($_POST["id_groupe_texte"]=="other")
	{
		if(!empty($_POST['nom_groupe_texte']))
		{
			/*groupe texte*/
			/*Check if the group text exists*/
			$req=requete("SELECT id_groupe_texte FROM groupe_textes WHERE nom_groupe_texte='".$_POST['nom_groupe_texte']."'");
			if(num_rows($req)==0)
			{
				requete(
				"
				INSERT INTO groupe_textes 
				(
					id_groupe_texte,
					nom_groupe_texte
				)
				VALUES
				(
					NULL,
					'".$_POST['nom_groupe_texte']."'
				)
				"
				);
				$req=requete("SELECT id_groupe_texte FROM groupe_textes WHERE nom_groupe_texte='".$_POST['nom_groupe_texte']."'");
			
			}
			$response=fetch_array($req);
			$id_groupe_texte=$response["id_groupe_texte"];
		}
	}
	/*Else if it exists*/
	else
	{
		$id_groupe_texte=$_POST["id_groupe_texte"];
	}
    //Check if the musicxml has been submitted
	if($_POST["has_musicxml"]=="true")
	{
	
		//continue : est ce que la piece existe deja dans la base
		// continue n'existe pas : on fait le checking car il n'a pas été fait
		if (!isset($_POST["continue"])) 
		{
				//variables non obligatoires
		  		// image_jpg,fichier_finale,incipit_jpg
				if (check_file("image_jpg"))
		  		{
						$extensions=array("jpg","jpeg","png","gif","tif","tiff");
						$image_jpg=upload("image_jpg",$extensions,"images_jpg");
						if ($image_jpg==NULL)
						{
								msg(_("Une erreur est survenue lors de l'upload de l'image jpeg"));
								msg_return();
								end_box();
								return;
						}
			
		  		}
				if (check_file("fichier_finale"))
		  		{
						$extensions=array("mus");
						$fichier_finale=upload("fichier_finale",$extensions,"fichiers_finale");
						if ($fichier_finale==NULL)
						{
								msg(_("Une erreur est survenue lors de l'upload du fichier finale"));
								msg_return();
								end_box();
								return;
						}
		  		}
				if (check_file("incipit_jpg"))
		  		{
						$extensions=array("jpg","jpeg","png","gif","tif","tiff");
						$incipit_jpg=upload("incipit_jpg",$extensions,"incipits_jpg");
						if ($incipit_jpg==NULL)
						{
								msg(_("Une erreur est survenue lors de l'upload de l'incipit en jpeg"));
								msg_return();
								end_box();
								return;
						}
				 }
				$valides=array("xml"); 
		        	$vars_musicxml=array("fichier_musicxml");
		       		if (!check_files($vars_musicxml))
				{                
		        		msg(_("Vous n'avez pas entré de fichier musicXML"));
		        		msg_return_to("show.php?id_base=".$_GET["id_base"]."&id_recueil=".$_GET["id_recueil"]);
		        		end_box();
		        		return ;
				}
		       		$musicxml_file=upload("fichier_musicxml",$valides,"xml");
		        	//ha ha j'adore utiliser ma classe
		        	if ($musicxml_file==NULL)
		        	{
		                	msg(_("Erreur lors de l'ouverture du fichier xml"));
		                	msg_return();
		                	end_box();
		                	return ;
		        	}
				//on verifie si la piece n'existe pas deja
				$expr=preg_replace("#( +)#","%",$_POST["titre"]);
				$expr=preg_replace("#([A-Z]+)#","_",$expr);
				$req=requete("SELECT titre FROM pieces WHERE titre LIKE '%".$expr."%'");
				if(num_rows($req)!=0)
				{
				
					msg(_("Certains titres sont très proches de celui que vous avez entré."));
					msg(_("Veuillez vérifier si la pièce n'existe pas déjà."));
					msg(_("Les titres correspondants sont :")." ");
					?>
			
					<tr><td>
					<form action="?id_recueil=<?php echo $_GET["id_recueil"]?>&add=piece&action=add&id_base=<?php echo $_GET["id_base"]?>" method="post" enctype="multipart/form-data">
					<table>
					<?php
					while($response=fetch_array($req))
					{
							msg($response["titre"]);		
					}
					//on écrit toutes les variables rentrées
					foreach(array_keys($_POST) as $name)
					{
							echo "<input type=\"hidden\" name=\"".$name."\" value='".$_POST[$name]."' />";
					}
					$_SESSION["musicxml_file"]=$musicxml_file;
					$_SESSION["incipit_jpg"]=$incipit_jpg;
					$_SESSION["image_jpg"]=$image_jpg;
					$_SESSION["fichier_finale"]=$fichier_finale;
					?>
				
					<tr>
						<td><?=("Voulez-vous poursuivre la procédure?")?></td>
					</tr>
					<tr>
						<td><input type="radio" name="continue" value="oui"/><?=("Oui")?></td>
						<td><input type="radio" name="continue" value="non"/><?=("Non")?></td>
					</tr>
					</table>
					</td></tr>
					<tr>
						<td><input type="submit" name="valider" value="<?=('Valider')?>"/></td>
					</tr>
					</form>
					<?php
					end_box();
					return;				
				}
		        
		}
		//continue=non; la piece existe deja et l'utilisateur ne veut pas poursuivre
		//on doit aussi enlever les fichiers uploadés
		else if ($_POST["continue"]=="non")
		{
					delete_all_files();
					msg(_("La pièce n'a pas été ajoutée dans la base de données"));
					msg_return_to("show.php?id_base=".$_GET["id_base"]."&id_recueil=".$_GET["id_recueil"]);
					end_box();
					return;
		}
		// continue= oui : on ne verifie rien, on ajoute à la base de donnée
		//tous les fichiers et les variables sont dans le tableau $_POST
		else if($_POST["continue"]=="oui")
		{
					//on met toutes les variables post
				
					//on réinitialise les variables comme $musicxml_file,$incipit_jpg,$image_jpg,$fichier_finale
					$musicxml_file=$_SESSION["musicxml_file"];
					$incipit_jpg=$_SESSION["incipit_jpg"];
					$image_jpg=$_SESSION["image_jpg"];
					$fichier_finale=$_SESSION["fichier_finale"];
		}
		//else if ($_POST[""])
		
		
#		echo $musicxml_file;
		//parsing instantanné du musicxml
		$music_xml=new music_xml_class($musicxml_file);
		
		$clef=$music_xml->clef;
		//ambitus
		$ambitus=$music_xml->ambitus;
		//nombre de parties
		$nb_parts=$music_xml->nb_parts;
		//armure
		$fifths=$music_xml->parts[0]->fifths;
		
		// last note
		$note_finale=$music_xml->last_note;
		
		//on s'occupe de la mélodie
		$melodies=get_melodies($music_xml->parts);
		$lyrics=get_lyrics($music_xml->parts);
		
		//$lyrics=array_map('utf8_decode',$lyrics);
		$lyrics=htmlspecialchars_array($lyrics);
		$lyrics=array_map('mysql_real_escape_string',$lyrics);
	
	
		// On regarde si la piece n'existe pas déja
		// la boucle permet de joindre plusieurs fois la table part
		// On le fait autant de fois qu'il y a de parties
		// puis on fait le filtrage avec le WHERE 
		$sql="SELECT r.id_recueil,r.titre AS titre_recueil,p.id_piece,p.titre AS titre_piece FROM parts pts1 "
			 ."INNER JOIN pieces p ON pts1.id_piece=p.id_piece "
			 ."INNER JOIN table_matieres tm ON  tm.id_piece=pts1.id_piece "
			 ."INNER JOIN recueils r ON r.id_recueil=tm.id_recueil "
			 ."INNER JOIN melodies m1 ON m1.id_melodie=pts1.id_melodie ";
	
		$i=1;
		$sql_where="WHERE p.titre='{$_POST["titre"]}' ";
		$sql_where.="AND m1.melodie='".$melodies[0]."' ";
		if(!empty($lyrics[0]))
		{
				$sql.="INNER JOIN textes t1 ON t1.id_text=pts1.id_text ";
				$sql_where.="AND (t1.texte='".$lyrics[0]."' )";
		}
		for($i=2;$i<=$nb_parts;$i++)
		{
				$sql.=" INNER JOIN parts pts".$i." ON pts".$i.".id_piece=p.id_piece "
					."INNER JOIN melodies m".$i." ON m".$i.".id_melodie=pts".$i.".id_melodie ";
				$sql_where.="AND (m".$i.".melodie='".$melodies[$i-1]."' )";
				if(!empty($lyrics[$i-1]))
				{
						$sql.="INNER JOIN textes t".$i." ON t".$i.".id_text=pts".$i.".id_text ";
						$sql_where.="AND (t".$i.".texte='".$lyrics[$i-1]."' )";
				}
				
		}
	
		$sql.=$sql_where/*.$sql_recueils*/;
	
		$req=requete($sql);
		// La piece existe t-elle deja
		if (num_rows($req)!=0)
		{
			msg(_("La pièce a déja été enregistrée"));
			// Premiere fois,on le fait pour ne pas à refaire $id_piece=response["..."] en boucle 
			$response=fetch_array($req);
			// On regarde si les recueils correspondent
			if(strcmp($response["id_recueil"],$_GET["id_recueil"])==0)
			{
				msg(_("La pièce existe déjà dans le recueil")." $titre_recueil");
				msg_return_to("show.php?id_base=".$_GET["id_base"]."&id_recueil=".$_GET["id_recueil"]);
				delete_all_files();
				end_box();
				return;
			}
		
			$id_piece=$response["id_piece"];
			while($response=fetch_array($req))
			{
				
				
				if(strcmp($response["id_recueil"],$_GET["id_recueil"])==0)
				{
				        msg(_("La pièce existe déjà dans le recueil")." $titre_recueil");
				        msg_return_to("show.php?id_base=".$_GET["id_base"]."&id_recueil=".$_GET["id_recueil"]);
				        delete_all_files();
				        end_box();
				        return;
				}
					
			}
		
			delete_all_files();
		
			// il nous suffit d'ajouter à la table des matières
			$r_groupe = requete("SELECT id_user, id_groupe FROM groupes WHERE id_user = '".$_SESSION['id']."'");
		
			// On recherche l'id de la base correspondant au recueil
			$req=requete("SELECT bases.nom_base,bases.id_base,recueils.titre FROM recueils "
					."INNER JOIN bases ON bases.id_base=recueils.id_base "
					."WHERE id_recueil='".$_GET["id_recueil"]."'");
			$response=fetch_array($req);
				
		
			$sql="
			INSERT INTO table_matieres 
			(
				id_recueil,
				pagination_ancienne,
				rang,
				id_piece
			) VALUES 
			(
				'".$_GET["id_recueil"]."',
				'".$_POST["pagination_ancienne"]."',
				'".$_POST["page"]."',
				'".$id_piece."'
			)";
			requete($sql);
			//now update updated variables
			requete("UPDATE recueils SET updated='0' WHERE id_recueil='".$_GET['id_recueil']."'");
			requete("UPDATE bases SET updated='0' WHERE id_base='".$_GET['id_base']."'");
			msg(_("La pièce a été ajoutée au recueil")." ".$response["titre"]." "._("de la base")." ".$response["nom_base"]);
			msg(_("La procédure a été exécutée avec succès"));
			msg_return_to("show.php?id_base=".$_GET["id_base"]."&id_recueil=".$_GET["id_recueil"]);
			end_box();
			return;
		
		}
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
		
		
		//tout a été fait on nettoie
		//voir dans lilypond.php
		clean_files($out);
		
		$psaume=0;
		if($_POST['psaume']=="true")
		{
			$psaume=1;
		}

				
				
		// Insertion de la pièce
		$sql="INSERT INTO pieces (
				id_piece,
				titre,
				auteur,
				fichier_xml,
				png_lilypond,
				mp3,
				fichier_jpg,
				image_incipit_jpg,
				fichier_finale,
				ambitus,
				armure,
				cles,
				note_finale,
				concordances,
				texte_additionnel,
				rubrique,
				nombre_parties,
				comment_public,
				comment_reserve,
				compositeur,
				timbre,
				psaume,
				nom_auteur_fiche
			) 
			VALUES (
				NULL,
				'".$_POST["titre"]."',
				'".$_POST["auteur_texte"]."',
				'".$musicxml_file."', 
				'".$png."',
				'".$mp3."',
				'".$image_jpg."',
				'".$incipit_jpg."',
				'".$fichier_finale."',
				'".$ambitus."',
				'".$fifths."',
				'".$clef."',
				'".$note_finale."',
				'".$_POST["concordances"]."',
				'".$_POST["texte_additionnel"]."',
				'".$_POST["rubrique"]."',
				'".$nb_parts."',
				'".$_POST["comment_public"]."',
				'".$_POST["comment_reserve"]."',
				'".$_POST["compositeur"]."',
				'".$_POST["timbre"]."',
				'".$psaume."',
				'".$_SESSION["pseudo"]."')";
#		echo $sql;
		requete($sql);
#		echo "insertpieces";
		// Recupération de l'id de la pièce'
		// Ici BUGBUGBUGBUG
		
		$id_piece=mysql_insert_id();
	
		// On insère la pièce à la TdM, et on vérifie au passage que l'user
		// courant à bien le droit d'ajouter la pièce aux recueils sélectionnés.
	
		// On recherche l'id de la base correspondant au recueil
		// We find the id of the base
		$req=requete("SELECT bases.nom_base,recueils.titre FROM recueils "
		."INNER JOIN bases ON bases.id_base=recueils.id_base "
		."WHERE id_recueil='".$_GET["id_recueil"]."'");
		$response=fetch_array($req);
		
		$sql="
			INSERT INTO table_matieres 
			(
				id_recueil,
				pagination_ancienne,
				rang,
				id_piece
			) VALUES 
			(
				'".$_GET["id_recueil"]."',
				'".$_POST["pagination_ancienne"]."',
				'".$_POST["page"]."',
				'".$id_piece."'
			)";
		requete($sql);
		
		//now update updated variables
		requete("UPDATE recueils SET updated='0' WHERE id_recueil='".$_GET['id_recueil']."'");
		requete("UPDATE bases SET updated='0' WHERE id_base='".$_GET['id_base']."'");
		msg(_("La pièce a été ajoutée au recueil")." ".$response["titre"]." "._("de la base")." ".$response["nom_base"]);
	
		
		#		maintenant si la pièce est un psaume , on va mettre à jour la base de donnée des mots
#		la classe db_mots_class s'occupe de tout
		if($_POST['psaume']=="true")
		{
			$db_class=new db_mots_class($id_piece);
			$db_class->update_db();
			$db_class=new db_mots_class($id_piece,"musique");
			$db_class->update_db();	
		}
	
	
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
				$req=requete("SELECT id_text FROM textes WHERE  texte = '".$lyrics[$i]."'");
				init_post_var("references");
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
		
			if(!empty($id_melodie)||!empty($id_text))
			{
				// On insere à la partie
				requete("INSERT INTO parts (id_part,id_piece,id_text,id_melodie,indice_partie) VALUES ("
					."NULL,"
					."'".$id_piece."',"
					."'".$id_text."',"
					."'".$id_melodie."',
					'".($i+1)."'
					)");
			}
		}
	}
	//If there is no musicxml
	else if($_POST["has_musicxml"]=="false")
	{
		//continue : est ce que la piece existe deja dans la base
		// continue n'existe pas : on fait le checking car il n'a pas été fait
		if (!isset($_POST["continue"])) 
		{
				//variables non obligatoires
		  		// image_jpg,fichier_finale,incipit_jpg
				if (check_file("image_jpg"))
		  		{
						$extensions=array("jpg","jpeg","png","gif","tif","tiff");
						$image_jpg=upload("image_jpg",$extensions,"images_jpg");
						if ($image_jpg==NULL)
						{
								msg(_("Une erreur est survenue lors de l'upload de l'image jpeg"));
								msg_return();
								end_box();
								return;
						}
			
		  		}
				if (check_file("fichier_finale"))
		  		{
						$extensions=array("mus");
						$fichier_finale=upload("fichier_finale",$extensions,"fichiers_finale");
						if ($fichier_finale==NULL)
						{
								msg(_("Une erreur est survenue lors de l'upload du fichier finale"));
								msg_return();
								end_box();
								return;
						}
		  		}
				if (check_file("incipit_jpg"))
		  		{
						$extensions=array("jpg","jpeg","png","gif","tif","tiff");
						$incipit_jpg=upload("incipit_jpg",$extensions,"incipits_jpg");
						if ($incipit_jpg==NULL)
						{
								msg(_("Une erreur est survenue lors de l'upload de l'incipit en jpeg"));
								msg_return();
								end_box();
								return;
						}
				 }
				
				//on verifie si la piece n'existe pas deja
				$expr=preg_replace("#( +)#","%",$_POST["titre"]);
				$expr=preg_replace("#([A-Z]+)#","_",$expr);
				$req=requete("SELECT titre FROM pieces WHERE titre LIKE '%".$expr."%'");
				if(num_rows($req)!=0)
				{
				
					msg(_("Certains titres sont très proches de celui que vous avez entré."));
					msg(_("Veuillez vérifier si la pièce n'existe pas déjà."));
					msg(_("Les titres correspondants sont :")." ");
					?>
			
					<tr><td>
					<form action="?id_recueil=<?php echo $_GET["id_recueil"]?>&add=piece&action=add&id_base=<?php echo $_GET["id_base"]?>" method="post" enctype="multipart/form-data">
					<table>
					<?php
					while($response=fetch_array($req))
					{
							msg($response["titre"]);		
					}
					//on écrit toutes les variables rentrées
					foreach(array_keys($_POST) as $name)
					{
							echo "<input type=\"hidden\" name=\"".$name."\" value='".$_POST[$name]."' />";
					}
					$_SESSION["incipit_jpg"]=$incipit_jpg;
					$_SESSION["image_jpg"]=$image_jpg;
					$_SESSION["fichier_finale"]=$fichier_finale;
					?>
				
					<tr>
						<td><?=("Voulez-vous poursuivre la procédure?")?></td>
					</tr>
					<tr>
						<td><input type="radio" name="continue" value="oui"/><?=("Oui")?></td>
						<td><input type="radio" name="continue" value="non"/><?=("Non")?></td>
					</tr>
					</table>
					</td></tr>
					<tr>
						<td><input type="submit" name="valider" value="<?=('Valider')?>"/></td>
					</tr>
					</form>
					<?php
					end_box();
					return;				
				}      
		}
		//continue=non; la piece existe deja et l'utilisateur ne veut pas poursuivre
		//on doit aussi enlever les fichiers uploadés
		else if ($_POST["continue"]=="non")
		{
					delete_all_files();
					msg(_("Aucun recueil n'a été entré dans la base de données"));
					msg_return();
					end_box();
					return;
		}
		// continue= oui : on ne verifie rien, on ajoute à la base de donnée
		//tous les fichiers et les variables sont dans le tableau $_POST
		else if($_POST["continue"]=="oui")
		{
					//on met toutes les variables post
				
					//on réinitialise les variables comme $musicxml_file,$incipit_jpg,$image_jpg,$fichier_finale
					$incipit_jpg=$_SESSION["incipit_jpg"];
					$image_jpg=$_SESSION["image_jpg"];
					$fichier_finale=$_SESSION["fichier_finale"];
		}
		
		// On ajoute le tout dans la base
		// We add everything in the database
		
		requete("INSERT INTO pieces (
				id_piece,
				titre,
				auteur,
				fichier_jpg,
				image_incipit_jpg,
				fichier_finale,
				note_finale,
				concordances,
				texte_additionnel,
				ambitus,
				armure,
				cles,
				rubrique,
				nombre_parties,
				comment_public,
				comment_reserve,
				compositeur,
				timbre,
				psaume,
				nom_auteur_fiche
			) 
			VALUES (
				NULL,
				'".$_POST["titre"]."',
				'".$_POST["auteur_texte"]."',
				'".$image_jpg."',
				'".$incipit_jpg."',
				'".$fichier_finale."',
				'".$_POST["note_finale"]."',
				'".$_POST["concordances"]."',
				'".$_POST["texte_additionnel"]."',
				'".$_POST["ambitus"]."',
				'".$_POST["armure"]."',
				'".$_POST["cles"]."',
				'".$_POST["rubrique"]."',
				'".$_POST["nombre_parties"]."',
				'".$_POST["comment_public"]."',
				'".$_POST["comment_reserve"]."',
				'".$_POST["compositeur"]."',
				'".$_POST["timbre"]."',
				'".($_POST['psaume']=="true")?(1):(0)."',
				'".$_SESSION["pseudo"]."'
				)");
		
		/* We find the id of the piece which has just been added*/
		
		$id_piece=mysql_insert_id();
		
		
		//now update updated variables
		requete("UPDATE recueils SET updated='0' WHERE id_recueil='".$_GET['id_recueil']."'");
		requete("UPDATE bases SET updated='0' WHERE id_base='".$_GET['id_base']."'");
		$id_melodie="";
		if(!empty($_POST['codage_incipit']))
		{
			requete("
			INSERT INTO melodies
			(
				id_melodie,
				melodie,
				indice_partie
			)
			VALUES
			(
				NULL,
				'".$_POST['codage_incipit']."',
				0
			) 
			");
			$req=requete("SELECT id_melodie FROM melodies WHERE melodie='".$_POST['codage_incipit']."'");
			$response=fetch_array($req);
			$id_melodie=$response['id_melodie'];
			
		}
		$id_text="";
		if(!empty($_POST['texte']))
		{
			/*Securing the text*/
			$_POST["texte"]=htmlspecialchars($_POST['texte']);
			$_POST["texte"]=mysql_real_escape_string($_POST['texte']);
			/*Add the text to the base*/
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
				'".$_POST['texte']."',
				'".$_POST["auteur_texte"]."',
				'".$_POST['biblio_texte']."',
				'$id_groupe_texte',
				'".$_POST["references"]."'
			)
			");
			/*Finding id_text*/
			$req=requete("SELECT id_text FROM textes WHERE texte='".$_POST['texte']."'");
			$response=fetch_array($req);
			$id_text=$response["id_text"];
		}
		requete("
		INSERT INTO parts 
		(
			id_piece,
			id_part,
			id_melodie,
			id_text
		)
		VALUES
		(
			'$id_piece',
			NULL,
			'$id_melodie',
			'$id_text'
		)
		");
		
		// On recherche l'id de la base correspondant au recueil
		// We find the id of the base
		$req=requete("SELECT bases.nom_base,recueils.titre FROM recueils "
		."INNER JOIN bases ON bases.id_base=recueils.id_base "
		."WHERE id_recueil='".$_GET["id_recueil"]."'");
		$response=fetch_array($req);
		
		$sql="
		INSERT INTO table_matieres 
		(
			id_recueil,
			pagination_ancienne,
			rang,
			id_piece
		) VALUES 
		(
			'".$_GET["id_recueil"]."',
			'".$_POST["pagination_ancienne"]."',
			'".$_POST["page"]."',
			'".$id_piece."'
		)";
		requete($sql);
		msg(_("La pièce a été ajoutée au recueil")." ".$response["titre"]." "._("de la base")." ".$response["nom_base"]);
	
		
	}
	msg(_("La procédure a été exécutée avec succès"));
        msg_return_to("show.php?id_base=".$_GET["id_base"]."&id_recueil=".$_GET["id_recueil"]."");
        end_box();     
}


function delete_if_possible($file)
{
	if(!empty($file))
	{
		@unlink($file);
	}
}







function add_recueil()
{
        begin_box(_("Ajout d'un recueil"),"recueil");
        //variable à checker pour voir si NULL ou vides
        /*
        	variable à ne pas checker
        	-titre uniforme
        	-abreviation
        	-imprimeur
        	-lieu
        	
        	il faut juste le champ titre et id base
        */
        $vars=array("titre");       
        if ( !check_post($vars) )
        {
                msg(_("Veuillez renseigner tous les champs"));
                msg_return();
                end_box();
                return ;
        }
        if ( !isset($_GET["id_base"]) )
        {
                msg(_("Erreur aucune base spécifiée"));
                msg_return();
                end_box();
                return ;
        }
        
        //on verifie seulement si la base existe
         $sql="SELECT nom_base FROM bases WHERE id_base='".$_GET["id_base"]."'";
        $req=requete($sql);
        if(num_rows($req)==0)
        {
                msg(_("La base choisie n'existe pas"));
                msg_return_to("show.php");
                end_box();
                return ;
        }
        $response=fetch_array($req);
        $nom_base=$response["nom_base"];
        // On vérifie si le propriétaire de la base est bien l'utilisateur en cours


        //on regarde si les entrées existent déja 
        //il s'agit de verifier le champ titre et titre uniforme
        // on regarde si l'entrée n'a pas déja été entré
        // dans les bases sélectionnées 
        // example :
        // 
        //      SELECT id_base,titre,titre_uniforme FROM recueils       
        //      WHERE       
        //      titre='sdf' AND titre_uniforme='sdf' AND (id_base='sqdfs' OR 
        //      id_base='sdfs' OR id_base='ok')
        $sql="SELECT nom_base,recueils.id_base,titre,titre_uniforme 
        FROM recueils 
        INNER JOIN bases ON bases.id_base = recueils.id_base 
        WHERE titre='".$_POST['titre']."' 
        AND titre_uniforme='".$_POST['titre_uniforme']."' 
        AND recueils.id_base='".$_GET["id_base"]."'";
        

        
        $req=requete($sql);
        if ( num_rows($req)!=0 )
        {        
                msg(_("Ce recueil existe déjà dans la base de donnée")." ".$response["nom_base"]);
                msg_return_to("show.php?id_base=".$_GET["id_base"]);
		end_box();
		return;
	        
        }
        
        $filename="";
        //les variables on déja été checkées
        $extensions_valides=array("jpeg","jpg","gif","png","bmp","tif","tiff");
        $vars_file=array("image_titre_recueil_jpg");
        if (check_files($vars_file))
        {
            $filename=upload("image_titre_recueil_jpg",$extensions_valides,"images_titres");
        	if($filename==NULL)
        	{
                	msg(_("Erreur lors de l'upload du fichier image_titre"));
                	msg_return_to("add.php?add=recueil&id_base=".$_GET["id_base"]);
                	end_box();
                	return;
        	}
                
        }
        $image_table_matieres="";
        //les variables on déja été checkées
        $extensions_valides=array("jpeg","jpg","gif","png","bmp","tif","tiff");
        $vars_file=array("image_table_matieres");
        if (check_files($vars_file))
        {
                $image_table_matieres=upload("image_table_matieres",$extensions_valides,"images_table_matieres");
        	if($image_table_matieres==NULL)
        	{
                	msg(_("Erreur lors de l'upload du fichier image_titre"));
                	msg_return_to("add.php?add=recueil&id_base=".$_GET["id_base"]);
                	end_box();
                	return;
        	}
                
        }
        
        
	//insertion du recueil dans toutes les bases ou elle n'est pas 
	//déjà présente
	$r_groupe = requete("SELECT id_user, id_groupe FROM groupes WHERE id_user = '".$_SESSION['id']."'");
	
	
	// L'utilisateur en cours doit être administrateur, ou bien propriétaire de  la base
	$r = requete("SELECT b.nom_base, b.permissions_groupe, b.permissions_others , b.owner ,u.pseudo FROM bases b INNER JOIN users u ON u.id_user=b.owner WHERE id_base = '".$_GET["id_base"]."'");
	$data = fetch_array($r);
	// groupe ok ?
	$ok = 0;
	while ($data_g = fetch_array($r_groupe))
	{
		if($data_g['id_groupe'] == $data['owner'] && $data['permissions_groupe'] == 2) 
			$ok = 1;
	}
	mysql_field_seek($r_groupe,0);
	if ($data['pseudo'] != $_SESSION['pseudo'] && !$_SESSION['admin'] && $ok==0 && $data['permissions_others'] != 2) // si l'user n'est pas admin, et qu'il n'est pas le proprio de la base
	{
		delete_if_possible($filename);
		delete_if_possible($image_table_matieres);
                msg(_("Vous n'êtes pas le propriétaire de la base")." ".$data['nom_base']." ("._("dont le propriétaire est")." ".$data['pseudo'].").<br />".
                _("Le programme n'a donc pas ajouté le recueil à cette base."));
                end_box();
                msg_return_to("show.php");
                return;
        }
        init_post_var("solmisation");
        init_post_var("timbre");
        $req=requete("INSERT INTO recueils(
					`id_recueil`,
					`id_base`,
					`titre`,
					`titre_uniforme`,
					`abreviation`,
					`image_titre_recueil_jpg`,
					`image_table_matieres`,
					`imprimeur`,
					`lieu`,
					`solmisation`,
					`timbre`,
					`date_impression` ,
					`date_revision`, 
					`nom_auteur_fiche`,
					`nom_auteur_revision`,
					`comment_public`, 
					`comment_reserve`,
					`editeur`,
					`adresse_biblio`,
					`auteur`,
					`compositeur`,
					`description_materielle`,
					`sources_bibliographiques`,
					`litterature_secondaire`,
					`bibliotheque`,
					`cote`
				) 
				VALUES (
					NULL,
					'".$_GET["id_base"]."',
					'".$_POST["titre"]."',
					'".$_POST["titre_uniforme"]."',
					'".$_POST["abreviation"]."',
					'".$filename."',
					'".$image_table_matieres."',
					'".$_POST["imprimeur"]."',
					'".$_POST["lieu"]."',
					'".convert_checkbox($_POST["solmisation"])."',
					'".convert_checkbox($_POST["timbre"])."',
					'"./*convert_checkbox(*/$_POST["date_impression"]/*)*/."',
					'".time()."' ,
					'".$_SESSION['pseudo']."',
					'".$_SESSION['pseudo']."',
					'".$_POST['comment_public']."',
					'".$_POST['comment_reserve']."',
					'".$_POST["editeur"]."',
					'".$_POST["adresse_biblio"]."',
					'".$_POST["auteur"]."',
					'".$_POST["compositeur"]."',
					'".$_POST["description_materielle"]."',
					'".$_POST["sources_bibliographiques"]."',
					'".$_POST["litterature_secondaire"]."',
					'".$_POST["bibliotheque"]."',
					'".$_POST["cote"]."'
				)");
	// for export			
	requete("UPDATE bases SET updated='0' WHERE id_base='".$_GET['id_base']."'");
	
        msg(_("Ajout du recueil dans la base")." ".$nom_base);
        msg(_("La procédure a été exécutée avec succès"));
        msg_return_to("show.php?id_base=".$_GET["id_base"]);
        end_box();
}

function convert_checkbox($str)
{
	return ($str=="on")?1:0;
}
function add_base()
{
	// Les non-admins ne peuvent pas faire ceci! 
        if (!$_SESSION['admin']) return;
        
        begin_box(_("Ajout d'une base"),"base");
        
        $vars=array("nom_base","owner");
        
        if ( !check_post($vars) )
        {
                msg(_("Veuillez renseigner tous les champs\n"));
                msg_return_to("add.php?add=base");
                end_box();
                return ;
        }
        if ( num_rows(requete("SELECT id_base,nom_base FROM bases WHERE nom_base='".$_POST["nom_base"]."'"))!=0)
        {
                msg(_("Erreur : cette base existe déjà"));
                msg_return_to("add.php?add=base");
                end_box();
                return ;
        }
        //tout marche
        $sql_begin="INSERT INTO bases  (  `owner`, `nom_base` , `description` , `references`, `permissions_groupe`, `permissions_others`,`body_background_color`,`mode`";
        $sql_end=" VALUES (  '".$_POST['owner']."', '".$_POST["nom_base"]."' , '".$_POST["description"]."' , '".$_POST["references"]."' , '1' , '1', 'rgb(".$_POST["r"].",".$_POST["g"].",".$_POST["b"].")','".$_POST["mode"]."' ";
        
        if ((check_file("banner"))&& ($_POST["banner"] == "true" ))
  		{
				$extensions=array("jpeg","jpg","png","gif","tif","tiff");
				$banner=upload("banner",$extensions,"banners");
				if ($banner==NULL)
				{
						msg(_("Une erreur est survenue lors de l'upload de fichier banner"));
						msg_return();
						end_box();
						return;
				}
				$sql_begin = $sql_begin . ", `banner`";
				$sql_end = $sql_end ." , './$banner' " ;
  		}
       
        $req=requete($sql_begin . " ) " .$sql_end . " ) ");
        msg("La base ".$_POST["nom_base"] . " a été ajoutée avec succès\n");
        msg_return_to("show.php");
        end_box();
}



//liste les champs d'une table selon la valeur champ
//ce sera quand on voudra rajouter des recueils à une base
//on aura besoin de cette fonction, on fera une form
//et l'utilisateur a juste à selectionner la bonne base
//et entre les infos de son recueil
function list_bases($pseudo = "")
{
        $req=requete("SELECT id_base,nom_base FROM bases WHERE 1");
        if (num_rows($req)==0)
        {
                return ;
        }
        echo "<select name=\"id_base1\" >\n";
       
        while($reponse=fetch_array($req))
        {
                echo "<option value=\"".$reponse["id_base"]."\">".$reponse["nom_base"]."</option>";
                echo "\n";
        }
        echo "\n</select>\n";
}

if (is_admin())
{
        if ( (isset($_GET['add'])) && (!isset($_GET["action"])) )
        {
                switch($_GET['add'])
                {
                                case "base":
                                add_base_form();
                                break;
                        
                                case "recueil":
                                add_recueil_form();
                                break;
                        
                                case "piece":
                                add_piece_form();
                                break;
                }
        }
        else if ( (isset($_GET['add'])) && (isset($_GET["action"])) )
        {
                if($_GET["action"]=="add")
                {
                        switch($_GET['add'])
                        {
                                case "base":
                                add_base();
                                break;
                        
                                case "recueil":
                                add_recueil();
                                break;
                        
                                case "piece":
                                add_piece();
                                break;
                        }
                }
        }
}
else
{
        begin_box(_("Attention"));
        ?>
        <tr>
                <td><?=_("Vous n'êtes pas autorisé à consulter cette page")?></td>
        </tr>
        <tr>
                <td><?=_("Veuillez vous connecter")." "?><a href="login.php"><?=_("ici")?></a></td>
        </tr>
        <?php
        end_box();
}        

dump_page();

?>






