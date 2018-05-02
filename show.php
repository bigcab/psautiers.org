<?php



/*
Count number of sql request
function show_info_base($id_base,$write_in_base) : 1 req
show_info_recueil() 1
list_groupes_textes 1
add_piece_form() = list_groupes_textes + 1
show_recueil= 
*/
require_once("include/auth.php");
require_once("include/global.php"); // for ALLOW_DOWNLOAD_XML
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/lilypond.php");
require_once("include/config.php");
ob_start();

$title=_("Consultation de la base");

$users_table_correspond=array("pseudo"=>"Pseudo","ip"=>"IP","permissions"=>"Permissions");




function list_groupes_textes()
{
	$req=requete("SELECT id_groupe_texte,nom_groupe_texte FROM groupe_textes ");
	?>
	<select onchange="update_groupe_texte(this)" name="id_groupe_texte">
	<?php
	while($response=fetch_array($req))
	{
		?>
		<option value="<?=$response["id_groupe_texte"]?>"><?=$response["nom_groupe_texte"]?></option>
		<?php
	}
	?>
	<option 
	<?php
	if(num_rows($req)==0)
	{
		echo "selected='true'";
	}
	?>
	 value="other"><?=_("Autre")?></option>
	</select>
	<input id="nom_groupe_texte" 
	<?php
	if(num_rows($req)==0)
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

function add_piece_form()
{
	?>
	<form action="add.php?id_recueil=<?=$_GET['id_recueil']; ?>&amp;add=piece&amp;action=add&amp;id_base=<?=$_GET['id_base']?>"  method="post" enctype="multipart/form-data" >
	<?php
        begin_box_js(_("Ajout d'une pièce"),"piece");
        
        $req=requete("SELECT titre,auteur FROM recueils WHERE id_recueil='".$_GET["id_recueil"]."'");
        $response=fetch_array($req); 
        $titre_recueil=$response["titre"];
        $auteur=$response["auteur"];
        ?>
	<tr>
	<td>
        <table >
		<tr>
		        <td><?=_("Créer un MP3 (plus long)")?></td>
		        <td>
		                <input type="checkbox" name="want_mp3" />
		                Tempo <input type="text" name="nb_beats" value="60"/>&nbsp;
		                à la <select name="reference_tempo">
		                	<option value="1"><?=_("Ronde")?></option>
		                	<option value="2"><?=_("Blanche")?></option>
		                	<option value="3"><?=_("Blanche pointée")?></option>
		                	<option value="4" selected="selected"><?=_("Noire")?></option>
		                	<option value="8"><?=_("Croche")?></option>
		                	<option value="16"><?=_("Double croche")?></option>
		                </select>
		        </td>
		</tr>
		
		

		
		
		<?php
        
		info_recueil_show_titre($titre_recueil);
		
		$response[""]="";
		output_show_form($response,$_SESSION["mode"],"add","piece");
		?>
		
		<tr>
	       		<td></td><td><input type="reset" name="Réinitialiser" value="<?=_('Réinitialiser')?>"/><input type="button" onclick='check_page()' name="Valider" value="<?=_('Valider')?>"/></td>
		</tr>
	</table>
	
	
	</td>
        </tr>
      
        
        <?php
        end_box();
        ?>
        </form>
        <?php
}






function add_recueil_form()
{
	?>
	<form action="add.php?id_base=<?=$_GET['id_base']; ?>&amp;add=recueil&amp;action=add&amp;etape=2" method="post" enctype="multipart/form-data">	
	
	
	
	
	<?php
        begin_box_js(_("Ajout d'un recueil"),"recueil");
        
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
        ?>
        <tr>
        <td>
        <table>
        <tr>        
                <td><?=_("Base sélectionnée")?></td>
                <td>
                        <?php echo $nom_base;?>
                </td>
        </tr>
        <?php
	
	output_show_form($response,$_SESSION["mode"],"add","recueil");
        ?>
   
        
       <tr> 
	        <td></td>
	        <td>
                	<input type="reset" value="<?=_('Réinitialiser')?>"/>
                	<input type="submit" name="submit" value="<?=_('Valider')?>"/>
                </td>
        </tr>
        </table>
        </td>
        </tr>

        <?php
        end_box();
        ?>
        </form>
        <?php
}


//      debut fonction add_base_form
//      (je fais ça car on a fait mis les balise de fin de php )
function add_base_form()
{
        $owners = null;
        $r = requete("SELECT id_user,pseudo FROM users");
        while ($data = fetch_array($r))
        {
        	$owners .= "<option value='".$data['id_user']."'>".$data['pseudo']."</option>\n";
        }
        ?>
        <form action="add.php?add=base&amp;action=add&amp;etape=2" method="post">
        <?php
        begin_box_js(_("Ajout d'une base"),"base");
        ?>
                <tr>
                        <td colspan="2" align="center"><?=_("Veuillez entrer les informations sur la base")?></td>
                </tr>
                <tr>
                        <td><acronym title="<?=_("Sélectionnez ici le propriétaire de la base.Si vous n'avez pas encore créé l'utilisateur dont vous souhaitez qu'il soit le propriétaire de cette base, merci de le faire auparavant en vous rendant dans l'administration.")?>"><?=_("Propriétaire de la base")?></acronym></td>
                        <td>
                        <select name="owner">
                        <?php echo $owners; ?>
                        </select>
                        </td>
                </tr>
                <?php
                /*
                add_base_form_show_nom();
                add_base_form_show_description();
                add_base_form_show_references();
                */
                global $default_body_background_color;
                output_show_form(array("body_background_color" => $default_body_background_color,
                					"mode"=>"default"),
                				$_SESSION["mode"],
                				"add",
                				"base");
                ?>
                <tr>
                        <td></td><td><input type="reset" value="Réinitialiser" name="Réinitialiser"/><input type="submit" name="Valider" value="Valider"/></td>
                </tr>
        
        <?php
        end_box();
        ?>
        </form>
        <?php
}
//      fin fonction add_base_form




function listing_bases()
{
    global $dev_server_bool;
        if($_SESSION["admin"]&& ($dev_server_bool))
        {
               
               add_base_form();
        }
	begin_box(_("Consulter une base"),"listing_bases");
	if($_SESSION["admin"]==true)
	{
		$sql="SELECT DISTINCT `nom_base`,`id_base`,`description`,`references`,`mode` FROM bases b WHERE 1";
	}
	else
	{
		$sql="SELECT DISTINCT `nom_base`,`id_base`,`description`,`references`,`mode` FROM bases b LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner WHERE  b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND (b.permissions_groupe='1' OR b.permissions_groupe='2')) OR (b.permissions_others='2' OR b.permissions_others='1')   ";
	}
	$req=requete($sql);
	if(num_rows($req)==0)
	{
		msg("Aucune base existante");
		end_box();
		return;
	}
	?>
	
	<tr>
	<td>
	<table style="border: 2px solid rgb(207,222,250); border-collapse: collapse" border="1">
	<tr>
		
		<th><?=_("Nom de la base")?></th>
		<th><?=_("Description")?></th>
		<th><?=_("Références")?></th>
		<th><?=_("Guide Pdf")?></th>
		<th><?=_("Editer")?></th>
		<th><?=_("Supprimer")?></th>
	</tr>
	<?php
	if($_SESSION["admin"]&& ($dev_server_bool==TRUE))
	{
		while($response=fetch_array($req))
		{
			?>
			<tr>
			<td>
			<a href="?id_base=<?=$response['id_base']?>&amp;mode=<?=$response['mode']?>" ><?php echo $response["nom_base"] ?></a>
			<a href="?id_base=<?=$response['id_base']?>&amp;mode=<?=$response['mode']?>&amp;custom=<?=$response['id_base']?>" ><img alt='' src='images/design/skin.png' width='20' height='20'/></a>
			</td>
			<td>
				<?php echo $response["description"]?>
			</td>
			<td>
				<?php echo $response["references"]?>
			</td>
			<td>
				<a href="download.php?file=guide_pdf&amp;id_base=<?php echo $response["id_base"]?>"><img alt='' src='images/design/download.png' width='20' height='20'/></a>
			</td>
			<td>
			<a href="update.php?id_base=<?php echo $response["id_base"] ?>"><img src='./images/design/edit.png' alt='<?=_("Editer")?>' title='<?=_("Editer")?>' width='20' height='20'/></a>
			</td>
			<td>
			<a href="update.php?del_base=<?php echo $response["id_base"] ?>"><img src='./images/design/remove.png'  alt='<?=_("Supprimer")?>' title='<?=_("Supprimer")?>' width='20' height='20'/></a></td>
					
			</tr>
			<?php	
		}
	}
	else
	{
		while($response=fetch_array($req))
		{
			?>
			<tr>
			<td>
			<a href="?id_base=<?=$response['id_base']?>&amp;mode=<?=$response['mode']?>" ><?php echo $response["nom_base"] ?></a>
			<a href="?id_base=<?=$response['id_base']?>&amp;mode=<?=$response['mode']?>&amp;custom=<?=$response['id_base']?>" ><img alt='' src='images/design/skin.png' width='20' height='20'/></a>
			</td>
			<td>
				<?php echo $response["description"]?>
			</td>
			<td>
				<?php echo $response["references"]?>
			</td>
			<td>
			        <img title='Non Autorisé' alt='Non Autorisé' src='images/design/not_authorized.png' />
			</td>
			<td>
			        <img title='Non Auhorisé' alt='Non Autorisé' src='images/design/not_authorized.png' />
			</td>
			<td>
			        <img title='Non Auhorisé' alt='Non Autorisé' src='images/design/not_authorized.png' />
			</td>
			</tr>
			<?php	
		}
	}
	?>
	</table>
	</td>
	</tr>
	<?php
	end_box();
	return;
}


function show_info_piece($id_piece,$write)
{
	$req=requete("
	SELECT 
	p.titre as titre_piece,
	p.*,
	tm.rang,
	tm.pagination_ancienne ,
	t.biblio_texte,
	grp_t.nom_groupe_texte
	FROM pieces p
	INNER JOIN table_matieres tm ON tm.id_piece=p.id_piece
	LEFT OUTER JOIN parts pts ON pts.id_piece=p.id_piece
	LEFT OUTER JOIN textes t ON t.id_text=pts.id_text
	LEFT OUTER JOIN groupe_textes grp_t ON grp_t.id_groupe_texte=t.id_groupe_texte
	WHERE 
	p.id_piece='".$id_piece."'");
			$response=fetch_array($req);
			//Print info about score
			if($write)
			{
			        begin_box_js(_("Pièce")." ","info_piece","<i><a href='show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]."&amp;id_piece=".$_GET["id_piece"]."'>".$response["titre"]."</a><a href='update.php?id_piece=".$_GET["id_piece"]."&amp;id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]."'><img src='images/design/edit.png' width='20' height='20' alt='"._("Editer")."' title='"._("Editer")."'/></a></i>");
			}
			else
			{
			        begin_box_js(_("Pièce")." ","info_piece","<i><a href='show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]."&amp;id_piece=".$_GET["id_piece"]."'>".$response["titre"]."</a></i>");
			}
			$titre_piece=$response["titre"];
			?>
			
<tr>
	<td>
        <table class="tableau_info">
        <?php
        $response["req"]=$req;
        output_show_form($response,$_SESSION["mode"],"info","piece");
        info_piece_show_comment_revision($response["comment_revision"],$_GET["id_piece"]);
        ?>
        </table>
        </td>
        </tr>
			
	<?php
			
	end_box();
}

function show_piece()
{
		$read=FALSE;
		$write=FALSE;
		global $dev_server_bool;
		if($_SESSION["admin"]&&($dev_server_bool))
		{
			$read=TRUE;
			$write=TRUE;
			
		}
		else
		{
			$req=requete("SELECT b.owner, b.permissions_others, b.permissions_groupe,g.id_user
			FROM table_matieres tm 
			INNER JOIN recueils r ON r.id_recueil=tm.id_recueil
			INNER JOIN bases b ON b.id_base=r.id_base
			LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner
			WHERE tm.id_piece='".$_GET["id_piece"]."'
			AND (b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND (b.permissions_groupe='1' OR b.permissions_groupe='2')) OR (b.permissions_others='2' OR b.permissions_others='1') ) 
			");
			if(num_rows($req)==0)
			{
				begin_box(_("Erreur"),"error_box");
				msg("Soit la pièce n'existe pas ou 
				soit vous n'avez pas les droit nécessaires pour consulter la base");
				msg_return_to("show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]);
				end_box();
				return;
			}
			$response=fetch_array($req);
			
			//if others can write or group can write or if it is the owner
			if
			  (
			   	($response["permissions_others"]=="2")
			   	||(	($response["permissions_groupe"]=="2")&&	($response["id_user"]==$_SESSION["id"]) )
			   	||($response["owner"]==$_SESSION["id"])
			  )   
			{
				$write=TRUE;
				$read=TRUE;
			}
		
		
			//if others can read
			if(
				($response["permissions_others"]=="1")
				||( ($response["permissions_groupe"]=="1") && ($response["id_user"]==$_SESSION["id"]) )
			  )
			{
				$read=TRUE;
			}	
		
		}
		if(!$dev_server_bool)
		{
		
		    $write=FALSE;
			$read=TRUE;
		}
		echo "<a href=\"show.php?id_base={$_GET["id_base"]}&amp;id_recueil={$_GET["id_recueil"]}\"><img alt='' width='16' height='16' src='images/retour.png' /> "._("Retour")."</a>";
		
#		if($_SESSION["admin"]==true)
#		{
			?>
			<a href="analyse.php?id_base=<?=$_GET['id_base']?>&amp;id_recueil=<?=$_GET['id_recueil']?>&amp;id_piece=<?=$_GET['id_piece']?>"><img alt='' src="images/design/analyse.png" width='20' height='20'/></a>
			<?php
#		}
        if($_SESSION["admin"]==true)
        {
            ?>
            <a href="virga.php?id_recueil=<?=$_GET['id_recueil']?>&amp;id_base=<?=$_GET['id_base']?>&amp;id_piece=<?=$_GET['id_piece']?>"><img alt='virga' title='virga' src="images/design/book.png" width='20' height='20'/></a>
            <?php
        }
		?>
		<div style="  margin-left: auto;
  margin-right: auto;width: 70%;">
			<?php
			// taking  piece id of the preceding piece in recueil
			$sql="SELECT tm2.id_piece
			FROM table_matieres tm
			INNER JOIN table_matieres tm2 ON tm2.id_recueil = '{$_GET["id_recueil"]}'
			WHERE tm.id_recueil = '{$_GET["id_recueil"]}'
			AND tm.id_piece = '{$_GET["id_piece"]}'
			AND tm2.rang < tm.rang
			ORDER BY  tm2.rang DESC
			LIMIT 1";
			$req=requete($sql);
			if(num_rows($req)!=0)
			{
				$response=fetch_array($req);
				$id_piece_before=$response["id_piece"];
				?>
			
				<a style="text-align: center;" href="show.php?id_base=<?=$_GET["id_base"]?>&amp;id_recueil=<?=$_GET["id_recueil"]?>&amp;id_piece=<?=$id_piece_before?>"><img src="./images/design/previous.png" alt="<?=_("Précédent")?>" title="<?=_("Précédent")?>"/></a>
			
				<?php
			}
		
		
			$sql="SELECT tm2.id_piece
			FROM table_matieres tm
			INNER JOIN table_matieres tm2 ON tm2.id_recueil = '{$_GET["id_recueil"]}'
			WHERE tm.id_recueil = '{$_GET["id_recueil"]}'
			AND tm.id_piece = '{$_GET["id_piece"]}'
			AND tm2.rang > tm.rang
			ORDER BY  tm2.rang ASC
			LIMIT 1";
			$req=requete($sql);
			if(num_rows($req)!=0)
			{
				$response=fetch_array($req);
				$id_piece_next=$response["id_piece"];
				?>
			
				<a  style="text-align: center;" href="show.php?id_base=<?=$_GET["id_base"]?>&amp;id_recueil=<?=$_GET["id_recueil"]?>&amp;id_piece=<?=$id_piece_next?>"><img src="./images/design/next.png" alt="<?=_("Suivant")?>" title="<?=_("Suivant")?>"/></a>

				<?php
			}
		
			?>
		</div>
		<?php
		
		show_info_base($_GET['id_base'],$write);
		show_info_recueil($_GET['id_recueil'],$write);
		show_info_piece($_GET["id_piece"],$write);
		
		$sql="SELECT r.titre AS titre_recueil,p.id_piece,p.titre AS titre_piece,p.fichier_xml,b.id_base,b.nom_base,p.png_lilypond,p.mp3,p.fichier_jpg,p.image_incipit_jpg FROM table_matieres tm "
				."INNER JOIN recueils r ON r.id_recueil=tm.id_recueil "
				."INNER JOIN pieces p ON p.id_piece=tm.id_piece "
				."INNER JOIN bases b ON b.id_base=r.id_base "
				."WHERE tm.id_recueil='".$_GET["id_recueil"]."' AND p.id_piece = '".$_GET['id_piece']."'  "
				."ORDER BY tm.rang";
				
		$r = requete($sql);
		$data = fetch_array($r);
		global $title;
		
		$title = $data['titre_piece'];
		$titre_piece=$title;
		$titre_recueil=$data['titre_recueil'];
		$nom_base=$data['nom_base'];
		
		
		if ($data['mp3'] != NULL && $data['mp3'] != '.mp3')
		{
			?>
			<script language="Javascript" type="text/javascript" src="js/player_show.js"></script>
			<?php
			begin_box(_("Player"),"player_box");
			?>
			
			<tr>
			<td>
				<!--url's used in the movie-->
				<!--text used in the movie-->
				<!-- saved from url=(0013)about:internet -->
				<object id="player" type="application/x-shockwave-flash" data="Player.swf?fichier=<?=$data['mp3']?>" width="300" height="100">
				  <param name="movie" value="Player.swf?fichier=<?=$data['mp3']?>" />
				</object>
				<!--
				<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="300" height="100" id="player" data="Player.swf?fichier=<?=$data['mp3']?>&amp;name=<?=$title?>" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="Player.swf?fichier=<?=$data['mp3']?>&amp;name=<?=$title?>" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="Player.swf?fichier=<?=$data['mp3']?>&amp;name=<?=$title?>" quality="high" bgcolor="#ffffff" width="300" height="100" name="player" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />-->
				<input type="hidden" value="<?php echo $data["mp3"]?>" id="box" name="box"/>
				
			</td>
			</tr>
			<?php
			end_box();
		}
		if(!empty($data["fichier_jpg"]))
		{
			begin_box_js("Image Jpeg","image_jpg");
			?>
			<tr><td>
			<a href="image.php?image_type=fichier_jpg&amp;id_piece=<?php echo $_GET["id_piece"]?>"><?php echo _("Voir l'image en plein écran");?></a>
			<img alt='' style='width:800px;' src="image.php?image_type=fichier_jpg&amp;id_piece=<?php echo $_GET["id_piece"]?>"/>
			</td>
			</tr>
			<?php
			end_box();
		}
		if(!empty($data["image_incipit_jpg"]))
		{
			begin_box_js(_("Image Incipit")." ","image_incipit_jpg");
			?>
			<tr><td>
			<a href="image.php?image_type=image_incipit_jpg&amp;id_piece=<?php echo $_GET["id_piece"]?>"><?php echo _("Voir l'image en plein écran");?></a>
			<img alt='' style='width:800px;' src="image.php?image_type=image_incipit_jpg&amp;id_piece=<?php echo $_GET["id_piece"]?>"/>
			</td>
			</tr>
			<?php
			end_box();
		}	
		if(!empty($data['png_lilypond']))
		{
			// This box is opened by default
		        begin_box_js_opened("Affichage de la partition","affich_partition");
		        ?>
		        <tr>
		        <td>
		        <?php
		
		
		        	
			$file = $data['png_lilypond'];
			$pages=glob($file."*.png");
			if(count($pages)==1)
			{
				echo "<img alt='' src='image.php?image_type=png_lilypond&amp;id_piece=".$_GET["id_piece"]."' />";
			}
			else
			{
				?>
				Page 1 <input checked="true" type='radio' name='page' onclick="change_page(this.value,<?=$_GET['id_piece']?>)" value="1"/>
				<?php
				for($i=1;$i<count($pages);$i++)
				{
					?>
					Page <?php echo $i+1 ?><input type='radio' name="page" onclick="change_page(this.value,<?=$_GET['id_piece']?>)" value="<?php echo $i+1 ?>"/>
					<?php
				}
				?>
				
				<img alt='' id='image_piece' src="image.php?image_type=png_lilypond&amp;page=1&amp;id_piece=<?php echo $_GET["id_piece"]?>"/>
				<?php
			}
			?>
			</td>
			</tr>
			<?php
			end_box();
		}
	        
		echo "<a href=\"show.php?id_base={$_GET["id_base"]}&amp;id_recueil={$_GET["id_recueil"]}\"><img alt='' width='16' height='16' src='images/retour.png' /> "._("Retour")."</a>";

		
		
}

//Same as show_info_base
function show_info_recueil($id_recueil,$write_in_recueil)
{
	$req=requete("SELECT * FROM recueils
	WHERE id_recueil='".$id_recueil."'");
	$response=fetch_array($req);
	$titre_recueil=$response["titre"];

	if($write_in_recueil)
	{
	        begin_box_js(_("Recueil")." ","info_recueil","<i><a href='show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]."'>".$titre_recueil."</a><a href='update.php?id_recueil=".$_GET["id_recueil"]."&amp;id_base=".$_GET["id_base"]."'><img src='images/design/edit.png' alt='"._("Editer")."' title='"._("Editer")."' width='20' height='20'/></a></i>");
	}
	else
	{
	        begin_box_js(_("Recueil")." ","info_recueil","<i><a href='show.php?id_base=".$_GET["id_base"]."&amp;id_recueil=".$_GET["id_recueil"]."'>".$titre_recueil."</a></i>");
	}
	?>
	 <tr>
        <td>
        <table class="tableau_info">
        
	<?php
	output_show_form($response,$_SESSION["mode"],"info","recueil");
	?>
        
	
	
	<?php
	//special , apparait dans toutes les configs
	//info_recueil_show_nom_auteur_fiche($response["nom_auteur_fiche"]);
	?>
        <tr>
                <td><?=_("Nom de l'auteur de la fiche")?></td>
                <td><?php echo $response["nom_auteur_fiche"]?></td>
        </tr>
        </table>
        </td>
        </tr>
	<?php
	
	
	end_box();	
}

function show_recueil()
{
        $write=FALSE;
	$read=FALSE;
	/*Checking authorization only-*/
	global $dev_server_bool;
	if($_SESSION["admin"]==true)
	{
		$r = requete("SELECT titre FROM recueils WHERE  id_recueil = '".$_GET['id_recueil']."'");
		$data = fetch_array($r);
		$titre_recueil=$data["titre"];
		
		$read=TRUE;
		$write=TRUE;
	}
	else
	{
		//Big sql command we also check permissions
		$sql="SELECT r.titre,b.owner, b.permissions_others, b.permissions_groupe,g.id_user FROM recueils r "
			."INNER JOIN bases b ON b.id_base=r.id_base "
			."LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner "
			."WHERE r.id_recueil='".$_GET["id_recueil"]."' "
			."AND (b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND (b.permissions_groupe='1' OR b.permissions_groupe='2')) OR (b.permissions_others='2' OR b.permissions_others='1') ) ";
		$req=requete($sql);
		if(num_rows($req)==0)
		{
			msg("Soit le recueil n'existe pas ou 
			soit vous n'avez pas les droit nécessaires pour consulter la base");
			msg_return_to("show.php?id_base=".$_GET["id_base"]);
			return;
		}
		while($response=fetch_array($req))
		{
		    $titre_recueil=$response["titre"];
		
		    //if others can write or group can write or if it is the owner
		    if (($response["permissions_others"]=="2")||
		        (($response["permissions_groupe"]=="2")&&($response["id_user"]==$_SESSION["id"]) )
		       	||($response["owner"]==$_SESSION["id"]))   
		    {
			    $write=TRUE;
			    $read=TRUE;
		    }
		    //if others can read
		    if(($response["permissions_others"]=="1")||
			    ( ($response["permissions_groupe"]=="1") && ($response["id_user"]==$_SESSION["id"]) ))
		    {
			    $read=TRUE;
		    }	
		}
		
		
				
	}
	
	if(!$dev_server_bool)
	{
	    $read=TRUE;
	    $write=FALSE;
	}
	//print info on base
	show_info_base($_GET["id_base"],$write);
	show_info_recueil($_GET["id_recueil"],$write);
	if($write==TRUE)
	{
		add_piece_form();
	}
	if(isset($_GET['order']))
	{
		if (($_GET['order'] == 'page')) 
		{
			$order = 'ORDER BY tm.rang ASC';
		}
		else if ($_GET['order'] == 'alphabetique')
		{
			$order = 'ORDER BY p.titre ASC';
		}
	}
	else 
	{
		$order = 'ORDER BY tm.rang ASC';
	}
	$sql="SELECT DISTINCT p.compositeur,p.armure,p.note_finale,p.ambitus, p.id_piece,p.titre AS titre_piece,tm.rang,p.fichier_xml FROM table_matieres tm "
			."INNER JOIN recueils r ON r.id_recueil=tm.id_recueil "
			."INNER JOIN pieces p ON p.id_piece=tm.id_piece "
			."INNER JOIN bases b ON b.id_base=r.id_base "
			."LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner "
			."WHERE tm.id_recueil='".$_GET["id_recueil"]."' "
			.$order;	
	$req=requete($sql);
	
	if(num_rows($req)==0)
	{
				
			
			msg(_("Aucune pièce dans ce recueil ."));
			msg_return_to("show.php?id_base=".$_GET['id_base']);
			
			return;
	}
	
	begin_box(_("Affichage des pièces du recueil")." ".$titre_recueil,"affich_piece_from_recueil");
	
	?>
	<?php
#	this is for handling carroussel matters
	$array=array();
    $count=0;
	//condition on ALLOW_DOWNLOAD_XML see include/global.php
	if (ALLOW_DOWNLOAD_XML==TRUE)
	{
	
	    	
		$tableau="<table id='tableau_haut'>
		<tr>
			<th><a href='show.php?id_base={$_GET['id_base']}&amp;id_recueil={$_GET['id_recueil']}&amp;order=alphabetique'>"._("Pièce")."</a></th>
			<th>MusicXML</th>
			<th><a href='show.php?id_base={$_GET['id_base']}&amp;id_recueil={$_GET['id_recueil']}&amp;order=page'>Page </a></th>
			<th>"._("Editer")."</th><th>"._("Supprimer")."</th>
		</tr>";
		while($response=fetch_array($req))
		{
		    
		    $array[$count]=array($response['id_piece'],$response["titre_piece"]);
			$tableau.="<tr><td><a  href='?id_base={$_GET["id_base"]}&amp;id_recueil={$_GET["id_recueil"]}&amp;id_piece={$response["id_piece"]}'>{$response["titre_piece"]}</a></td>";
			
			if(!$response["fichier_xml"])
			{
			        $tableau.="<td>Non</td>";
			}
			else
			{
			        $tableau.="<td><a href='download.php?id_piece={$response["id_piece"]}'><img alt='' src='images/design/download.png' width='20' height='20'/></a></td>";
			}
			
			$tableau .= "<td>{$response['rang']}</td>";
			if($write==TRUE)
			{
			        $tableau.="<td><a href='update.php?id_piece={$response["id_piece"]}&amp;id_base={$_GET["id_base"]}&amp;id_recueil={$_GET["id_recueil"]}'><img src='images/design/edit.png' alt='"._("Editer")."' title='Editer' width='20' height='20' /></a></td><td><a href='update.php?del_piece={$response["id_piece"]}&amp;id_base={$_GET["id_base"]}&amp;id_recueil={$_GET["id_recueil"]}'><img alt='"._("Supprimer")."' title='"._("Supprimer")."' src='images/design/remove.png' width='20' height='20'/></a></td>";
			}
			else
			{
			        $tableau.="<td><img title='Non autorisé' alt='Non Autorisé' src='images/design/not_authorized.png' /></td><td><img title='Non Autorisé' alt='Non Autorisé' src='images/design/not_authorized.png' /></td>";
			}
			$tableau.="</tr>";
            $count+=1;
	    }
	}
	else //if download of xml is not allowed
	{
		$tableau="<table id='tableau_haut'>
		<tr>
			<th><a href='show.php?id_base={$_GET['id_base']}&amp;id_recueil={$_GET['id_recueil']}&amp;order=alphabetique'>"._("Pièce")."</a></th>
			<th><a href='show.php?id_base={$_GET['id_base']}&amp;id_recueil={$_GET['id_recueil']}&amp;order=page'>Page </a></th>
			<th>"._("Editer")."</th><th>"._("Supprimer")."</th>
		</tr>";
		while($response=fetch_array($req))
		{
		    $array[$count]=array($response['id_piece'],$response["titre_piece"]);
		    
			$tableau.="<tr><td><a  href='?id_base={$_GET["id_base"]}&amp;id_recueil={$_GET["id_recueil"]}&amp;id_piece={$response["id_piece"]}'>{$response["titre_piece"]}</a></td>";
			
			// NO DOWNLOAD HERE do not uncomment
			/*if(!$response["fichier_xml"])
			{
			        $tableau.="<td>Non</td>";
			}
			else
			{
			        $tableau.="<td><a href='download.php?id_piece={$response["id_piece"]}'><img alt='' src='images/design/download.png' width='20' height='20'/></a></td>";
			}*/ 
			
			$tableau .= "<td>{$response['rang']}</td>";
			if($write==TRUE)
			{
			        $tableau.="<td><a href='update.php?id_piece={$response["id_piece"]}&amp;id_base={$_GET["id_base"]}&amp;id_recueil={$_GET["id_recueil"]}'><img src='images/design/edit.png' alt='"._("Editer")."' title='"._("Editer")."' width='20' height='20' /></a></td><td><a href='update.php?del_piece={$response["id_piece"]}&amp;id_base={$_GET["id_base"]}&amp;id_recueil={$_GET["id_recueil"]}'><img alt='"._("Supprimer")."' title='"._("Supprimer")."' src='images/design/remove.png' width='20' height='20'/></a></td>";
			}
			else
			{
			        $tableau.="<td><img title='Non autorisé' alt='Non Autorisé' src='images/design/not_authorized.png' /></td><td><img title='Non Autorisé' alt='Non Autorisé' src='images/design/not_authorized.png' /></td>";
			}
			$tableau.="</tr>";
            $count+=1;
		}
	}
	$tableau.="</table>";
	?>
	<tr> <!-- Here is the carroussel-->
	    <td>
	        
            <?php
            $jquery="<link rel='stylesheet' type='text/css' href='./jquery/jcarousel_skin.css' />";
	        $jquery.="<script type='text/javascript' src='./jquery/jquery.js'></script>";

    
            $jquery.="<script type='text/javascript' src='./jquery/jquery.jcarousel.min.js'></script>";
            $jquery.="<script type='text/javascript' language='Javascript'>\n var id_base='{$_GET["id_base"]}';\n var id_recueil='{$_GET["id_recueil"]}';";
            $jquery.="var mycarousel_itemList=[";
            for($i=0;$i<(count($array)-1);$i++)
            {
                $jquery.= "{url: 'affich_histogram.php?id_base={$_GET["id_base"]}&{$_GET["id_recueil"]}&id_piece={$array[$i][0]}',title: '{$array[$i][1]}',id: '{$array[$i][0]}'},";
            }
            $jquery.= "{url: 'affich_histogram.php?id_base={$_GET["id_base"]}&{$_GET["id_recueil"]}&id_piece={$array[$i][0]}',title: '{$array[$i][1]}',id: '{$array[$i][0]}'}";
            $jquery.= "];";
            $jquery.= "</script>";
            $jquery.="<script type='text/javascript' language='Javascript' src='./jquery/jcarousel.js'></script>";
            ?>
	        <div id="wrap">

                  <ul id="mycarousel" class="jcarousel-skin-ie7">
                    <!-- The content will be dynamically loaded in here -->
                  </ul>

            </div>
	    </td>
	</tr>
	<tr>
	    <td width='800px'>
	        <?=$tableau?>
	    </td>
	</tr>
	<?php
	

	//echo $tableau_info;
	
	msg_return_to("show.php?id_base={$_GET["id_base"]}");
    end_box(); 
    return $jquery;
}

/*Function shows information of a base
Carefull , the user must be authorized
this function does not check anything

In id_base
write_in_base : boolean
*/
function show_info_base($id_base,$write_in_base)
{
    global $dev_server_bool;
	$req=requete("SELECT nom_base,`description`,`references`,owner FROM bases WHERE id_base='".$id_base."'");
	$response=fetch_array($req);
	
	//Keeping for later (print info base)
	$nom_base=$response["nom_base"];
	$description=$response["description"];
	$references=$response["references"];
	$owner=$response["owner"];
	/*Printing info about the base*/
	if (( ($owner==$_SESSION['id']) || ($_SESSION['admin'])) && ($dev_server_bool))
	{
	        begin_box_js(_("Base")." ","info_base","<i><a href='show.php?id_base=".$_GET["id_base"]."'>".$nom_base."</a><a href='update.php?id_base=".$_GET["id_base"]."'><img  src='images/design/edit.png' alt='"._("Editer")."' title='"._("Editer")."' width='20' height='20'/></a></i>");         
	}
	else
	{
	        begin_box_js(_("Base")." ","info_base","<i><a href='show.php?id_base=".$_GET["id_base"]."'>".$nom_base."</a></i>");
	}
	?>
	<tr>
	<td>
	<table class='tableau_info'>
	<?php
	output_show_form($response,$_SESSION["mode"],"info","base");
	?>
	</table>
	</td>
	</tr>
	<?php
	
	end_box();
}

function show_base()
{
		// init boolean values to know if we can read or write
		$write=FALSE;
		$read=FALSE;
		global $dev_server_bool;
#		echo $_GET["id_base"];
		//checking admin
#		echo "admin ? ".$_SESSION["admin"];
		if($_SESSION["admin"]==true)
		{
			$write=TRUE;
			$read=TRUE;
			$req=requete("SELECT nom_base,mode FROM bases WHERE id_base='".$_GET["id_base"]."'");
			$response=fetch_array($req);
	
			$nom_base=$response["nom_base"];
		}
		else
		{
			$req=requete("SELECT nom_base,b.owner,b.permissions_groupe,b.permissions_others,g.id_user,b.mode
			FROM bases b 
			LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner 
			WHERE 
			b.id_base = '".$_GET["id_base"]."'  
			AND ( 
				b.owner='".$_SESSION["id"]."' 
				OR  (
					g.id_user='".$_SESSION["id"]."' 
					AND (
						b.permissions_groupe='2' OR b.permissions_groupe='1'
					)
				)
				OR (b.permissions_others='2' OR b.permissions_others='1') 
			)");
			if(num_rows($req)==0)
			{
				msg("Soit la base n'existe pas ou 
				soit vous n'avez pas les droit nécessaires pour consulter la base");
				msg_return();
				return;
			}
			while ($response=fetch_array($req))
			{
			
				$nom_base=$response["nom_base"];
			
			
				//if others can write or group can write or if it is the owner
				if
				  (
				   	($response["permissions_others"]=="2") || (($response["permissions_groupe"]=="2") && ($response["id_user"]==$_SESSION["id"]) ) || ($response["owner"]==$_SESSION["id"])
				  )   
				{
					$write=TRUE;
					$read=TRUE;
				}
			
			
				//if others can read
				if(
					($response["permissions_others"]=="1")
					||( ($response["permissions_groupe"]=="1") && ($response["id_user"]==$_SESSION["id"]) )
				  )
				{
					$read=TRUE;
				}
			
			}	
				
		}
		
		
		if(!$dev_server_bool)
		{
		    $read=TRUE;
		    $write=FALSE;
		}
		
		show_info_base($_GET["id_base"],$write);
		
		if($write==TRUE)
        {
                add_recueil_form();
        }
		begin_box(_("Affichage des recueils"),"affich_recueil");
		$sql="SELECT DISTINCT * FROM recueils r "
				."WHERE r.id_base='".$_GET["id_base"]."' ";
		$req =requete($sql);
		if(num_rows($req)==0)
		{
				msg("Aucun recueil dans la base ".$nom_base);
				msg_return_to("show.php");
				end_box();
				return;
		}

		
	$tableau="
	<tr><td>
	<table id='tableau_haut'>
        <tr><td>Titre du recueil</td><td>Modifier</td><td>"._("Supprimer")."</td></tr>
	";
	$count=0;
	

if($write==TRUE)
{
        while($response=fetch_array($req))
	{
	        $tableau.="<tr>";
	        $tableau.="<td><a  href='?id_base=".$_GET["id_base"]."&amp;id_recueil=".$response["id_recueil"]."'>".$response["titre"]."</a></td>";
	                 
	         
	                $tableau.=
	                "
	                <td>
	                        <a href='update.php?id_recueil=".$response["id_recueil"] ."&amp;id_base=".$_GET["id_base"]."'><img src=\"images/design/edit.png\" alt='"._("Editer")."' title='"._("Editer")."' width='20' height='20'/></a>
	                        </td>
	                        <td>
	                        <a href='update.php?del_recueil=".$response["id_recueil"] ."&amp;id_base=".$_GET["id_base"]."'><img src=\"./images/design/remove.png\"  alt='"._("Supprimer")."' title='"._("Supprimer")."' width='20' height='20'/></a>
	                </td>
	                
	                
	                ";
	        
	    
	                
	        
	        $tableau.="</tr>";
	       
	}

}
else
{
        while($response=fetch_array($req))
	{
	        $tableau.="<tr>";
	        $tableau.="<td><a  href='?id_base=".$_GET["id_base"]."&amp;id_recueil=".$response["id_recueil"]."'>".$response["titre"]."</a></td>";
	                 
	         
	        $tableau.="<td><img title='Non Authorisé' alt='Non Authorisé' src='images/design/not_authorized.png' /></td><td><img title='Non Authorisé' alt='Non Authorisé' src='images/design/not_authorized.png' /></td>";
	        
	    
	                
	        
	        $tableau.="</tr>";
	}
                
} 
	
	echo $tableau;
	echo "</table>\n</td></tr>\n";
	
	
	msg_return_to("show.php?");
	end_box();
		
}




?>
<!--main script for the whole page--> 
<script type='text/javascript' src='./js/show.js'></script>


<?php
if(!$_SESSION["isolated"])
{
    if(!isset($_GET["id_piece"])&&!isset($_GET["id_recueil"])&&!isset($_GET["id_base"]))
    {
		    listing_bases();
    }
    if(isset($_GET["id_piece"]))
    {
		    show_piece();
    }
    else if (isset($_GET["id_recueil"]))
    {
		    $jquery=show_recueil();
    }
    else if (isset($_GET["id_base"])) 
    {
		    show_base();
    }
}
else
{
    if(!isset($_GET["id_piece"])&&!isset($_GET["id_recueil"]))
    {
            $_GET["id_base"]=$_SESSION["isolated_base"];
            show_base();
    }
    if(isset($_GET["id_piece"]))
    {
		    show_piece();
    }
    else if (isset($_GET["id_recueil"]))
    {
		    $jquery=show_recueil();
    }
}


dump_page($jquery);

?>
