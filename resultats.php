<?php
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/note.php");
require_once("include/global.php");
//titre
$title=_("Résultats");
ob_start();


$info=array("recherche","recueil","piece","titre_uniforme","imprimeur","auteur","groupe_texte",
"adresse_biblio","lieu","compositeur","date_impression",
"texte","entre_année","fin_entre_année","id_base","nom_base",
"output_titre_uniforme","output_imprimeur","output_commentaire",
"output_adresse_biblio","output_lieu","output_compositeur",
"output_date_impression","output_note_finale","output_ambitus",
"output_armure","output_cles","output_nombre_parties");

$struct_sql=array("titre_uniforme"=>"r","imprimeur"=>"r","adresse_biblio"=>"r","lieu"=>"r","date_impression"=>"r","compositeur"=>"r",

				"note_finale"=>"p","ambitus"=>"p","nombre_parties"=>"p","armure"=>"p","cles"=>"p");
$titre_correspondance=array("base"=>"Base","titre_recueil"=>"Titre Du recueil","titre_piece"=>"Titre de la pièce","titre_uniforme"=>"Titre Uniforme","note_finale"=>"Note Finale","nombre_parties"=>"Nombre de Parties","armure"=>"Armure","cles"=>"Clés","titre_uniforme"=>"Titre uniforme","imprimeur"=>"Imprimeur","adresse_biblio"=>"Adresse bibliographique","lieu"=>"Lieu","date_impression"=>"Date d'Impression","commentaire"=>"Commentaire","ambitus"=>"Ambitus","groupe_texte"=>"Source du texte","compositeur"=>"Compositeur");

$checkbox_field=array();

function absolu($m1/* melodie entrée */,$melodie /* melodie musicXML */,$first_note)
{
	//string strstr  ( string $haystack  , mixed $needle  [, bool $before_needle  ] )
	$pos=strpos($melodie,$m1);
	if($pos===false)
	{
		msg("absolu() function failed");
		return;
	}
	$reste=substr($melodie,0,$pos-1);
	$data=explode("/",$reste);
	foreach($data as $val)
	{
		$valeur+=$val;
	}
	//si on voit que l'on a les meme notes (do4 = do5)
	if(($valeur%12)==($first_note%12))
	{
		return true;
	}
	else
	{
		return false;
	}
}
function recherche_melodique()
{
        global $dev_server_bool;

		begin_box(_("Recherche mélodique"),"recherche_melodique");

		$vars=array("melodie");
		if (!check_post($vars))
		{
				msg(_("Erreur , aucune mélodie entrée"));
				msg_return_to("recherche.php");
				
				end_box();
				return;
		}
		
		//On prend les intervalles de la melodie entrée par l'utilisateur
		$melodie=convert_note_to_values($_POST["melodie"]);
		
		// On prend la premiere note
		// pour savoir si c'est absolu
		$first_pos=strpos($_POST["melodie"],"/");
		$first_note=substr($_POST["melodie"],0,$first_pos-1);
		$first_note=note_to_value($first_note);
		
		$sql="SELECT DISTINCT r.titre AS titre_recueil,r.id_recueil,p.titre AS titre_piece,p.id_piece,m.melodie,p.fichier_xml,r.id_base,b.owner, b.permissions_groupe, b.permissions_others FROM melodies m "
			."INNER JOIN parts pts ON pts.id_melodie=m.id_melodie " 
			."INNER JOIN pieces p ON p.id_piece=pts.id_piece "
			."INNER JOIN table_matieres tm ON p.id_piece=tm.id_piece "
			."INNER JOIN recueils r ON r.id_recueil=tm.id_recueil "
			."INNER JOIN bases b ON r.id_base=b.id_base "
			."LEFT OUTER JOIN users u ON u.id_user='".$_SESSION["id"]."' "
			."LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner ";
		//si debut est selectionne c'est à dire qu'on ne recherche que dans l'incipit
		$where="WHERE ";
		init_post_var("debut");
		if ($_POST['debut'] == "on")
		{
			$where.=" ( (m.melodie LIKE '/__/".$melodie."%') OR (m.melodie LIKE '/_/".$melodie."%') OR (m.melodie LIKE '/___/".$melodie."%') ) ";	
		}
		else
		{
			$where.="(m.melodie LIKE '%/".$melodie."%') ";
		}
		//echo $_POST["id_recueil"];
		if($_POST["id_base"]!="toutes")
		{
		    $where.="AND (b.id_base='{$_POST["id_base"]}') ";
		    if($_POST["id_recueil"]!="tous")
		    {
		        $where.="AND (r.id_recueil='{$_POST["id_recueil"]}') ";
		    }
		}
		if ($dev_server_bool)
		{
		    $where.="AND (u.permissions='1' OR b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND (b.permissions_groupe='1' OR b.permissions_groupe='2')) OR (b.permissions_others='2' OR b.permissions_others='1') )  ";
		}
		
#		recherche particulière dans certaines bases
        if(!empty($_POST["search_in_results"]))
        {
            $where.="AND (";
            $ids_piece=explode("/",$_POST["search_in_results"]);
            $count=0;
            foreach($ids_piece as $id_piece)
            {
                if(!empty($id_piece))
                {
                    if($count==0)
                    {
                        $where.=" (p.id_piece='$id_piece') ";
                    }
                    else
                    {
                        $where.="OR (p.id_piece='$id_piece') ";
                    }
                }
                
                $count++;
            }
            $where.=") ";
        }
        
        
		$req=requete($sql.$where); //echo $melodie;
		if(num_rows($req)==0)
		{
			msg(_("La recherche n'a retourné aucun résultat"));
			msg_return_to("recherche.php");
			end_box();
			return;
		}
		
		$search_in_results_string="";
		if(ALLOW_DOWNLOAD_XML==TRUE)
		{
			?>
			<tr>
				<td>
				<table style="border: 2px solid rgb(207,222,250); border-collapse: collapse">
				<tr>
					<th>Recueil</th>
					<th>Piece</th>
				
					<th>Fichier MusicXML</th>
				</tr>
			<?php
			if($_POST["reconnaissance"]=="relative")
			{
				while($response=fetch_array($req))
				{
                    $search_in_results_string.=$response["id_piece"]."/";
				
			
				
					?>
					<tr>
						<td><a href="show.php?id_base=<?php echo $response["id_base"] ?>&amp;id_recueil=<?php echo $response["id_recueil"]?>"><?php echo $response["titre_recueil"] ?></a></td>
						<td><a href="show.php?id_base=<?php echo $response["id_base"] ?>&amp;id_recueil=<?php echo $response["id_recueil"]?>&amp;id_piece=<?php echo $response["id_piece"] ?>"><?php echo $response["titre_piece"] ?></a></td>
				
						<td>
							<a href="download.php?id_piece=<?php echo $response['id_piece']; ?>"><img alt='download' src='images/design/download.png' /></a>
						</td>
					</tr>
					<?php
				
				
				}
			}
			else
			{
				while($response=fetch_array($req))
				{
				        $search_in_results_string.=$response["id_piece"]."/";
				
						if(absolu($melodie,$response["melodie"],$first_note)==true)
						{
							?>
							<tr>
								<td><a href="show.php?id_base=<?php echo $response["id_base"] ?>&amp;id_recueil=<?php echo $response["id_recueil"]?>"><?php echo $response["titre_recueil"] ?></a></td>
								<td><a href="show.php?id_base=<?php echo $response["id_base"] ?>&amp;id_recueil=<?php echo $response["id_recueil"]?>&amp;id_piece=<?php echo $response["id_piece"] ?>"><?php echo $response["titre_piece"] ?></a></td>
								<a href="download.php?id_piece=<?php echo $response['id_piece']; ?>"><img alt='download' src='images/design/download.png' /></a>
						
							</tr>
							<?php	
						}
				
				}
			}
		}
		else
		{
			?>
			<tr>
				<td>
				<table style="border: 2px solid rgb(207,222,250); border-collapse: collapse">
				<tr>
					<th>Recueil</th>
					<th>Piece</th>
				
				</tr>
			<?php
			if($_POST["reconnaissance"]=="relative")
			{
				
				while($response=fetch_array($req))
				{

				    $search_in_results_string.=$response["id_piece"]."/";
			
				
					?>
					<tr>
						<td><a href="show.php?id_base=<?php echo $response["id_base"] ?>&amp;id_recueil=<?php echo $response["id_recueil"]?>"><?php echo $response["titre_recueil"] ?></a></td>
						<td><a href="show.php?id_base=<?php echo $response["id_base"] ?>&amp;id_recueil=<?php echo $response["id_recueil"]?>&amp;id_piece=<?php echo $response["id_piece"] ?>"><?php echo $response["titre_piece"] ?></a></td>
				
						
					</tr>
					<?php
				
				
				}
			}
			else
			{
				while($response=fetch_array($req))
				{
				
				        $search_in_results_string.=$response["id_piece"]."/";
						if(absolu($melodie,$response["melodie"],$first_note)==true)
						{
							?>
							<tr>
								<td><a href="show.php?id_base=<?php echo $response["id_base"] ?>&amp;id_recueil=<?php echo $response["id_recueil"]?>"><?php echo $response["titre_recueil"] ?></a></td>
								<td><a href="show.php?id_base=<?php echo $response["id_base"] ?>&amp;id_recueil=<?php echo $response["id_recueil"]?>&amp;id_piece=<?php echo $response["id_piece"] ?>"><?php echo $response["titre_piece"] ?></a></td>
								
						
							</tr>
							<?php	
						}
				
				}
			}
		}
		?>
			</table>
			</td>
		</tr>
		<tr>
		    <td><?php 
		        search_in_results_button($search_in_results_string);
		        ?>
		    </td>
		</tr>
		<?php
		msg_return_to("recherche.php");
		end_box();
}


function unchecked($var)
{
	if(is_array($var))
	{
		foreach($var as $x)
		{
			if(checked($x))
			{
				return FALSE;
			}
		}
		return TRUE;
	}
	return (!checked($var));
}

//fonction pour verifier une variable post en checkbox
function checked($var)
{
	if($_POST[$var]=="on")
	{
		return TRUE;
	}
	return FALSE;
}

//fonction pour regarder les checkbox qui commencent par output_ c'est le filtrage de la sortie
function checked_output($vars)
{
	$out=array();
	foreach($vars as $var)
	{
		if(checked("output_".$var))
		{
			array_push($out,$var);
		}
	}
	return $out;
}


/*
exemple of response

Array ( [recherche] => test [recueil] => on [piece] => on [titre_uniforme] => on [imprimeur] => on [commentaire] => on [adresse_biblio] => on [lieu] => on [compositeur] => on [date_impression] => on [texte] => on [entre_année] => [fin_entre_année] => [id_base] => 20 [id_recueil] => 41 [nom_base] => on [output_titre_uniforme] => on [output_imprimeur] => on [output_commentaire] => on [output_adresse_biblio] => on [output_lieu] => on [output_compositeur] => on [output_date_impression] => on [output_note_finale] => on [output_ambitus] => on [output_armure] => on [output_cles] => on [output_nombre_parties] => on )
*/
function recherche_titre()
{
        global $dev_server_bool;
		global $struct_sql;
		global $titre_correspondance;
		begin_box(_("Recherche titre/texte"),"recherche_chaine");
		$vars=array("recherche");
		if(!check_post($vars))
		{
				msg(_("Veuillez renseigner tous les champs"));
				msg_return_to("recherche.php");
				end_box();
				return;
		}
		msg(_("Vous recherchez la chaîne")." <b>".$_POST["recherche"]."</b>");
		$expr=preg_replace("#( +)#","%",$_POST["recherche"]);
		$expr=preg_replace("#([A-Z]+)#","_",$expr);
		
		$select="SELECT DISTINCT ";
		$from="FROM parts pts ";
		$where="WHERE (";
		$join="LEFT OUTER JOIN textes t ON t.id_text=pts.id_text "
			."INNER JOIN pieces p ON p.id_piece=pts.id_piece "
			."INNER JOIN table_matieres tm ON p.id_piece=tm.id_piece "
			."INNER JOIN recueils r ON r.id_recueil=tm.id_recueil "
			."INNER JOIN bases b ON r.id_base=b.id_base "
			."LEFT OUTER JOIN users u ON u.id_user='".$_SESSION["id"]."' "
			."LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner ";
		if($_POST["piece"]!="on"&&$_POST["recueil"]!="on")
		{
				msg(_("Veuillez choisir au moins une option dans le champ"). " \"<em>"._("Recherche dans")."</em>\"");
				msg_return_to("recherche.php");
				end_box();
				return;
		}
		
		/*
		
		table melodie : -commentaire


		table piece
		-titre
		-auteur
		-compositeur
		
		table recueils:
		titre_uniforme
		titre
		imprimeur
		adresse bibliographique
		lieu
		date d'impression
		compasiteur 
		
		pour l'entrée on s'occupe des input de nom r_*
		*/
		$vars=array("recueil","titre_uniforme","imprimeur","adresse_biblio","lieu","date_impression","compositeur");
		//on regarde pour la table recueils
		
		$vars=array
                ("titre_uniforme"=>"r","imprimeur"=>"r",
                "adresse_biblio"=>"r","lieu"=>"r",
		"date_impression"=>"r","compositeur"=>"r",
		"auteur"=>"p");
		$i=0;
		foreach($vars as $key=>$var)
		{
			
			if(checked($key))
			{
				if($i!=0)
				{
					$where.="OR ";
				}
				$where.="($var.$key LIKE '%".$_POST["recherche"]."%') ";
				$i++;
			}
		}
		
		//texte spécial on regarde à la fois dans p.texte_additionnel et la table texte => t.texte
		if(checked("texte"))
		{
			if($i!=0)
			{
				$where.="OR ";
			}
			$where.="(p.texte_additionnel LIKE '%".$_POST["recherche"]."%') OR (t.texte LIKE '%".$_POST["recherche"]."%') ";
			$i++;
		}
		
		//special  groupe_texte
		if(checked("groupe_texte"))
		{
			$select.="grp_t.nom_groupe_texte,";
			$join.="LEFT OUTER JOIN groupe_textes grp_t ON grp_t.id_groupe_texte=t.id_groupe_texte ";
			if($i!=0)
			{
				$where.="OR ";
			}
			$where.="(grp_t.nom_groupe_texte LIKE '%".$_POST["recherche"]."%') ";
			$i++;
		}
		
		if(checked("recueil"))
		{
			if($i!=0)
			{
					$where.="OR ";
			}
			$where.="(r.titre LIKE '%".$_POST["recherche"]."%') ";
			$i++;
		}
		
		//table titre de la piece
		if(checked("piece"))
		{
			if($i!=0)
			{
					$where.="OR ";
			}
			$where.="(p.titre LIKE '%".$_POST["recherche"]."%') ";
			$i++;
		}
		$where.=") ";
		
		//recherche dans une base précise 
		if($_POST["id_base"]!="toutes")
		{
			$where.="AND (b.id_base='{$_POST["id_base"]}') ";
			//recherche dans un recueil precis
			if($_POST["id_recueil"]!="tous")
			{
				$where.="AND (r.id_recueil='{$_POST["id_recueil"]}') ";
			}
		}
		
		//permissions 
		if ($dev_server_bool)
		{
		    $where.="AND (u.permissions='1' OR b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND (b.permissions_groupe='1' OR b.permissions_groupe='2')) OR (b.permissions_others='2' OR b.permissions_others='1') )  ";
		}
		//on s'occupe du select maintenant
		
		$select.="p.id_piece,p.titre AS titre_piece,r.titre AS titre_recueil,r.id_recueil,b.id_base,b.nom_base ";
		
		
		/*
		a afficher
		ambitus
		armure cles nombre_parties
		*/
		$vars=array("titre_uniforme","imprimeur","adresse_biblio","lieu","date_impression","compositeur","note_finale","ambitus","armure","cles","nombre_parties");
		
		$output=checked_output($vars);
		
		foreach($output as $out)
		{
			$select.=",".$struct_sql[$out].".".$out." ";
		}
		
#		recherche particulière dans certaines bases
        if(!empty($_POST["search_in_results"]))
        {
            $where.="AND (";
            $ids_piece=explode("/",$_POST["search_in_results"]);
            $count=0;
            foreach($ids_piece as $id_piece)
            {
                if(!empty($id_piece))
                {
                    if($count==0)
                    {
                        $where.=" (p.id_piece='$id_piece') ";
                    }
                    else
                    {
                        $where.="OR (p.id_piece='$id_piece') ";
                    }
                }
                
                $count++;
            }
            $where.=") ";
        }
        
		$sql=$select.$from.$join.$where;
		$req=requete( $sql );
		if(num_rows($req)==0)
		{
			msg(_("Aucun resultat"));

			hidden_form_return();
			end_box();
			return;
		}
		$table_output=array("base","titre_recueil","titre_piece");
		
		
		?>
		<tr><td>
		<table style="border: 2px solid rgb(207,222,250); border-collapse: collapse">
		<tr>
			
		<?php
		foreach($table_output as $out)
		{
			?>
			<th><?php echo $titre_correspondance[$out] ?></th>
			<?php
		}
		foreach($output as $out)
		{
			?>
			<th><?php echo $titre_correspondance[$out] ?></th>
			<?php
		}
		?>
		</tr>
		<?php
		$search_in_results_string="";
		while($response=fetch_array($req))
		{
		    $search_in_results_string.=$response["id_piece"]."/";
			?>
			<tr>
				<td>
					<a href="show.php?id_base=<?php echo $response["id_base"]?>"><?php echo $response["nom_base"]?></a>
				</td>
				<td>
					<a href="show.php?id_base=<?php echo $response["id_base"]?>&amp;id_recueil=<?php echo $response["id_recueil"]?>"><?php echo $response["titre_recueil"]?></a>
				</td>
				<td>
					<a href="show.php?id_base=<?php echo $response["id_base"]?>&amp;id_recueil=<?php echo $response["id_recueil"]?>&amp;id_piece=<?php echo $response["id_piece"]?>"><?php echo $response["titre_piece"]?></a>
				</td>
				<?php
				foreach($output as $out)
				{
					?>
					<td><?php echo $response[$out] ?></td>
					<?php
				}
				?>
			</tr>
			<?php
		}
		?>
		</table>
		</td></tr>
		<tr>
		    <td><?php 
		        search_in_results_button($search_in_results_string);
		        ?>
		    </td>
		</tr>
		<?php
		hidden_form_return();
		
		end_box();
}

//hidden form which contains info to return to search page
//with all the infos submitted
function hidden_form_return()
{
        global $info;
        ?>
        <tr><td>
        <form name="hidden_form_return" action="recherche.php" method="post">
        <?php
        foreach($info as $in)
		{
		        
		        if(checked($in))
		        {
		               
		                        ?>
		                        <input type="hidden" name="<?=$in?>" value="true"/>
		                        <?php
		        }
		        else if(isset($_POST[$in]))
		        {
		                 ?>
		                        
		                 <input type="hidden" name="<?=$in?>" value="<?=$_POST[$in]?>"/>
		                 <?php
		        }
		        else
		        {
		                ?>
		                <input type="hidden" name="<?=$in?>" value="false"/>
		                <?php
		        }    
		                
		               
		        
		}
		
		?>
       
		<a  onclick="document.hidden_form_return.submit();"><img alt='retour' width='16' height='16' src='images/retour.png' />Retour</a>
		</form>
		</td></tr>
		<?php
}

function is_checkbox($var)
{
        if($var=="on"||$var=="off")
        {
                return TRUE;
        }
        return FALSE;
}

function search_in_results_button($string)
{
    ?>
    <a href="recherche.php?search_in_results=<?=$string?>"><?=_("Rechercher dans ces résultats")?></a>
    <?php
}

switch($_GET["recherche"])
{
		case "melodique":
		recherche_melodique();
		break;
		
		case "titre":
		recherche_titre();
		break;
}

dump_page();


?>
