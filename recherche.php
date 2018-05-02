<?php
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/clavier.php");
ob_start();

function begin_box_js_focus($title,$id)
{
	?>
	                                
	<h2><a href="#recherche" onclick="change_table('<?php echo $id?>');"><img alt="" width='12' height='12' id='<?php echo "img".$id ?>' src='images/design/plus.png'/><?php echo $title ?></a></h2>
	                               
	                        
	               			<!--box table js -->
	               			<a name="recherche"></a>
	                                <table id="<?php echo $id ?>" class="box_table_js_ferme" >
	                                
	
	<?php
}


function search_in_results()
{
    if (!empty($_GET["search_in_results"]))
    {
        ?>
        <input type="hidden" name="search_in_results" value="<?=$_GET['search_in_results']?>"/>
        <?php
    }
}

$default_values=array();
/*This function returns default value or value of $_POST variable
(because the user has hit the button return )
*/
function set_checked($name)
{
        if($name=="true")
        {
                return "checked";
        }
}

$title=_("Recherche");
?>
<script type="text/javascript" language="Javascript" src="js/clavier.js"></script>
<script type="text/javascript" language="Javascript" src="js/recherche.js"></script>
<script type="text/javascript" language="Javascript" src="js/ajax_listing.js"></script>
<?php



if (count($_POST)==0)
{
?>
<form method="post" action="resultats.php?recherche=titre">
<?php
begin_box_js(_("Recherche titre/texte"),"recherche_titre");
?>


		<tr>
				<th><?=_("Votre recherche :")." "?></th>
				<td><input type="text" name="recherche" />
				<?php
#				    provide support to search in specific pieces (from another search)
				    search_in_results();
				?>
				
				</td>
				
		</tr>

		<tr>
			<td>
				<table  style="border: 1px solid grey; border-collapse: collapse" border="1">
						<tr>
						
								<th colspan="2"><?=_("Rechercher la chaîne dans")?></th>
						</tr>
								
						<tr>
							<td><input checked="checked" type="checkbox" name="recueil" onclick="date_recueil(this)"/><?=_("Titre des Recueils")?></td>
						
							<td><input checked="checked" type="checkbox" name="piece" /><?=_("Titre des Pièces")?></td>
						</tr>
						
						
						<tr>
							<td><input checked="checked" type="checkbox" name="titre_uniforme" /><?=_("Titre uniforme")?></td>
						
							<td><input checked="checked" type="checkbox" name="imprimeur" /><?=_("Imprimeur")?></td>
						</tr>
						<tr>
						
							<td><input checked="checked" type="checkbox" name="adresse_biblio" /><?=_("Adresse Bibliographique")?></td>
							<td><input checked="checked" type="checkbox" name="groupe_texte" /><?=_("Source_du_texte")?></td>
						</tr>
						
						<tr>
							<td><input checked="checked" type="checkbox" name="lieu" /><?=_("Lieu")?></td>
					
							<td><input checked="checked" type="checkbox" name="compositeur" /><?=_("Compositeur")?></td>
						</tr>
						<tr>
							<td><input checked="checked" type="checkbox" name="date_impression" /><?=_("Date d'impression")?></td>
					
							<td><input checked="checked" type="checkbox" name="texte" /><?=_("Textes")?></td>
						</tr>
						<tr>
							<td><input checked="checked" type="checkbox" name="auteur" /><?=_("Auteur")?></td>
						</tr>
						
						
						<tr style="display:none;" id="row_date_recueil">
							<td><?=_("Date du recueil: entre")?></td>
							<td colspan="2"><input type="text" name="entre_année" size="4" /><?=" "._("et")." "?><input type="text" name="fin_entre_année" size="4"/></td>
						</tr>
						<?php
						if(!$_SESSION["isolated"])
						{
						    ?>
						    <tr>
							    <td><?=_("Rechercher dans une base")?></td>
							    <td>
								    <select name="id_base" onchange="update_recueil(this)">
									    <option value="toutes"><?=_("Toutes")?></option>
									    <?php
									    // listing de toutes les bases avec l'option toutes
									    $sql="SELECT id_base,nom_base FROM bases WHERE 1";
									    $req=requete($sql);
									    while($response=fetch_array($req))
									    {
											    ?>
											    <option value="<?php echo $response["id_base"] ?>">
											    <?php echo $response["nom_base"] ?>
											    </option>
											    <?php
									    }
									    ?>
								    </select>
							    <input type="hidden" name="id_recueil" id="id_recueil" />
							    </td>
						    </tr>
						    
						    <tr id="tr_select_recueil">
							    <td></td>
							    <td></td>
						    </tr>
						    <?php
						}
						else
						{
						    ?>
						    <tr>
						        <td><?=_("Rechercher dans un recueil")?></td>
						        <td>
						            <input type="hidden" name="id_base" value="<?=$_SESSION['isolated_base']?>" />
						            <select name="id_recueil" style="width:100px">
									    <option value="toutes"><?=_("Toutes")?></option>
									    <?php
									    // listing de toutes les bases avec l'option toutes
									    $sql="SELECT id_recueil,titre FROM recueils WHERE 1";
									    $req=requete($sql);
									    while($response=fetch_array($req))
									    {
											    ?>
											    <option value="<?php echo $response['id_recueil'] ?>" >
											    <?php echo $response["titre"] ?>
											    </option>
											    <?php
									    }
									    ?>
								    </select>
						        </td>
						    </tr>
						    <?php
						}
						?>
				</table>
			</td>
			
			<td>
				<table  style="border: 1px solid grey; border-collapse: collapse" border="1">
					
						<tr>
							<th colspan="2"><?=_("Sortie")?></th>
						</tr>
						<tr>
							<td><input checked="checked" type="checkbox" name="nom_base" /><?=_("Nom de la Base")?></td>
						
							<td><input checked="checked" type="checkbox" name="output_titre_uniforme" /><?=_("Titre uniforme")?></td>
						</tr>
						<tr>
							<td><input checked="checked" type="checkbox" name="output_imprimeur" /><?=_("Imprimeur")?></td>
						
							<td><input checked="checked" type="checkbox" name="output_commentaire" /><?=_("Commentaire")?></td>
						</tr>
						<tr>
							<td><input checked="checked" type="checkbox" name="output_adresse_biblio" /><?=_("Adresse Bibliographique")?></td>
						
							<td><input checked="checked" type="checkbox" name="output_lieu" /><?=_("Lieu")?></td>
						</tr>
						<tr>
							<td><input checked="checked" type="checkbox" name="output_compositeur" /><?=_("Compositeur")?></td>
						
							<td><input checked="checked" type="checkbox" name="output_date_impression" /><?=_("Date d'impression")?></td>
						</tr>
						<tr>
							<td><input checked="checked" type="checkbox" name="output_note_finale" /><?=_("Note Finale")?></td>
						
							<td><input checked="checked" type="checkbox" name="output_ambitus" /><?=_("Ambitus")?></td>
						</tr>
						<tr>
							<td><input checked="checked" type="checkbox" name="output_armure" /><?=_("Armure")?></td>
						
							<td><input checked="checked" type="checkbox" name="output_cles" /><?=_("Clé")?></td>
						</tr>
						<tr>
							<td><input checked="checked" type="checkbox" name="output_nombre_parties" /><?=_("Nombre de Parties")?></td>
						</tr>
						
						
				</table>
			</td>
		</tr>
		<tr>
			<td/><td/>	
				
		</tr>
		<tr>
				<td></td>
				<td align="right"><input type="reset" value="<?=_("Réinitialiser")?>"/><input type="submit" value="<?=_("Valider")?>" /></td>
		</tr>
		

<?php
//end of the if (count ($_POST) ==0)
}
else
{

?>
<form method="post" action="resultats.php?recherche=titre">
<?php
begin_box_js_opened(_("Recherche titre/texte"),"recherche_titre");
?>


		<tr>
				<th><?=_("Votre recherche :")." "?></th>
				<td><input  value="<?=$_POST['recherche']?>" type="text" name="recherche" />
				<?php
#				    provide support to search in specific pieces (from another search)
				    search_in_results();
				?>
				</td>
				
		</tr>

		<tr>
			<td>
				<table  style="border: 1px solid grey; border-collapse: collapse" border="1">
						<tr>
						
								<th colspan="2"><?=_("Rechercher la chaîne dans")?></th>
						</tr>
								
						<tr>
							<td><input <?=set_checked($_POST["recueil"])?> type="checkbox" name="recueil" onclick="date_recueil(this)"/><?=_("Titre des Recueils")?></td>
						
							<td><input <?=set_checked($_POST["piece"])?> type="checkbox" name="piece" /><?=_("Titre des Pièces")?></td>
						</tr>
						
						<tr>
							<td><input <?=set_checked($_POST["titre_uniforme"])?> type="checkbox" name="titre_uniforme" /><?=_("Titre uniforme")?></td>
						
							<td><input <?=set_checked($_POST["imprimeur"])?> type="checkbox" name="imprimeur" /><?=_("Imprimeur")?></td>
						</tr>
						<tr>
							<td><input <?=set_checked($_POST["commentaire"])?> type="checkbox" name="commentaire" /><?=_("Commentaire")?></td>
						
							<td><input <?=set_checked($_POST["adresse_biblio"])?> type="checkbox" name="adresse_biblio" /><?=_("Adresse Bibliographique")?></td>
						</tr>
						<tr>
							<td><input <?=set_checked($_POST["lieu"])?> type="checkbox" name="lieu" /><?=_("Lieu")?></td>
					
							<td><input <?=set_checked($_POST["compositeur"])?> type="checkbox" name="compositeur" /><?=_("Compositeur")?></td>
						</tr>
						<tr>
							<td><input <?=set_checked($_POST["date_impression"])?> type="checkbox" name="date_impression" /><?=_("Date d'impression")?></td>
					
							<td><input <?=set_checked($_POST["texte"])?> type="checkbox" name="texte" /><?=_("Textes")?></td>
						</tr>
						<tr>
							<td><input <?=set_checked($_POST["auteur"])?> type="checkbox" name="auteur" /><?=_("Auteur")?></td>
						</tr>
						
						
						<tr style="display:none;" id="row_date_recueil">
							<td><?=_("Date du recueil: entre")?></td>
							<td colspan="2"><input type="text" value="<?=$_POST["entre_année"]?> name="entre_année" size="4" /><?=" "._("et")." "?><input type="text" name="fin_entre_année" value="<?=$_POST["fin_entre_année"]?> size="4"/></td>
						</tr>
						<?php
						if(!$_SESSION["isolated"])
						{
						    ?>
						    <tr>
							    <td><?=_("Rechercher dans une base")?></td>
							    <td>
								    <select name="id_base" onchange="update_recueil(this)">
									    <option value="toutes"><?=_("Toutes")?></option>
									    <?php
									    // listing de toutes les bases avec l'option toutes
									    $sql="SELECT id_base,nom_base FROM bases WHERE 1";
									    $req=requete($sql);
									    while($response=fetch_array($req))
									    {
											    ?>
											    <option value="<?php echo $response["id_base"] ?>">
											    <?php echo $response["nom_base"] ?>
											    </option>
											    <?php
									    }
									    ?>
								    </select>
							    <input type="hidden" name="id_recueil" id="id_recueil" />
							    </td>
						    </tr>
						    
						    <tr id="tr_select_recueil">
							    <td></td>
							    <td></td>
						    </tr>
						    <?php
						}
						else
						{
						    ?>
						    <tr>
						        <td><?=_("Rechercher dans un recueil")?></td>
						        <td>
						            <input type="hidden" name="id_base" value="<?=$_SESSION['isolated_base']?>" />
						            <select name="id_recueil" style="width:100px">
									    <option value="toutes"><?=_("Toutes")?></option>
									    <?php
									    // listing de toutes les bases avec l'option toutes
									    $sql="SELECT id_recueil,titre FROM recueils WHERE 1";
									    $req=requete($sql);
									    while($response=fetch_array($req))
									    {
											    ?>
											    <option value="<?php echo $response['id_recueil'] ?>" >
											    <?php echo $response["titre"] ?>
											    </option>
											    <?php
									    }
									    ?>
								    </select>
						        </td>
						    </tr>
						    <?php
						}
						?>
						
						<tr>
						</tr>
				</table>
			</td>
			
			<td>
				<table  style="border: 1px solid grey; border-collapse: collapse" border="1">
					
						<tr>
							<th colspan="2"><?=_("Sortie")?></th>
						</tr>
						<tr>
							<td><input <?=set_checked($_POST["nom_base"])?> type="checkbox" name="nom_base" /><?=_("Nom de la Base")?></td>
						
							<td><input <?=set_checked($_POST["output_titre_uniforme"])?> type="checkbox" name="output_titre_uniforme" /><?=_("Titre uniforme")?></td>
						</tr>
						<tr>
							<td><input <?=set_checked($_POST["output_imprimeur"])?> type="checkbox" name="output_imprimeur" /><?=_("Imprimeur")?></td>
						
							<td><input <?=set_checked($_POST["output_commentaire"])?> type="checkbox" name="output_commentaire" /><?=_("Commentaire")?></td>
						</tr>
						<tr>
							<td><input <?=set_checked($_POST["output_adresse_biblio"])?> type="checkbox" name="output_adresse_biblio" /><?=_("Adresse Bibliographique")?></td>
						
							<td><input <?=set_checked($_POST["output_lieu"])?> type="checkbox" name="output_lieu" /><?=_("Lieu")?></td>
						</tr>
						<tr>
							<td><input <?=set_checked($_POST["output_compositeur"])?> type="checkbox" name="output_compositeur" /><?=_("Compositeur")?></td>
						
							<td><input <?=set_checked($_POST["output_date_impression"])?> type="checkbox" name="output_date_impression" /><?=_("Date d'impression")?></td>
						</tr>
						<tr>
							<td><input <?=set_checked($_POST["output_note_finale"])?> type="checkbox" name="output_note_finale" /><?=_("Note Finale")?></td>
						
							<td><input <?=set_checked($_POST["output_ambitus"])?> type="checkbox" name="output_ambitus" /><?=_("Ambitus")?></td>
						</tr>
						<tr>
							<td><input <?=set_checked($_POST["output_armure"])?> type="checkbox" name="output_armure" /><?=_("Armure")?></td>
						
							<td><input <?=set_checked($_POST["output_cles"])?> type="checkbox" name="output_cles" /><?=_("Clé")?></td>
						</tr>
						<tr>
							<td><input <?=set_checked($_POST["output_nombre_parties"])?> type="checkbox" name="output_nombre_parties" /><?=_("Nombre de Parties")?></td>
						</tr>
						
						
				</table>
			</td>
		</tr>
		<tr>
				
				
		</tr>
		<tr>
				<td></td>
				<td align="right"><input type="reset" value="<?=_("Réinitialiser")?>"/><input type="submit" value="<?=_("Valider")?>" /></td>
		</tr>
		

<?php

        

        


//end else condition
}

//in any case we end the appropriate box
end_box();
//then end the form
?>
</form>




<!-- recherche melodique-->


<form method="post" action="resultats.php?recherche=melodique">
<?php
begin_box_js_focus(_("Recherche mélodique"),"recherche_melodique");
?>


<tr >
        <td width="300px">Clef</td>
        <td>
                <?php
#				    provide support to search in specific pieces (from another search)
				    search_in_results();
				?>
                <select id="clef_sign" onchange="update_partition()">
                        <option value="F"><?=_("Fa")?></option>
                        <option value="C"><?=_("Do")?></option>
                        <option value="G" selected="selected"><?=_("Sol")?></option>
                </select>
                <select id="clef_line" onchange="update_partition()">
                        <option value="1">1</option>
                        <option value="2" selected="selected">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                </select>
        </td>
</tr>

<tr>
		<td><?=_("Reconnaissance")?></td>
		<td><input type="radio" name="reconnaissance" value="absolue"/><?=_("Absolue")?>
			<input type="radio" checked="checked" name="reconnaissance" value="relative"/><?=_("Relative")?>
		</td>
		
</tr>
<tr>
		<td><?=_("Recherche uniquement au début des pièces")?></td>
		<td> <input type="checkbox" name="debut" /></td>
		
		
</tr>
<tr>
	<td>
			<table  style="border: 1px solid grey; border-collapse: collapse" border="1">
					<tr>
							
							<th colspan="2"><?=_("Afficher les champs suivants")?></th>
					</tr>
					<tr>
	
						
						<td>
						<input type="checkbox" name="out_ambitus" checked="checked"/><?=_("Ambitus")?>
						</td>
						<td>
						<input type="checkbox" name="out_armure" checked="checked"/><?=_("Armure")?>
						</td>
					</tr>
					<?php
			        if(!$_SESSION["isolated"])
			        {
			            ?>
			            <tr>
				            <td><?=_("Rechercher dans une base")?></td>
				            <td>
				          
					            <select name="id_base" onchange="update_recueil2(this)">
						            <option value="toutes"><?=_("Toutes")?></option>
						            <?php
						            // listing de toutes les bases avec l'option toutes
						            $sql="SELECT id_base,nom_base FROM bases WHERE 1";
						            $req=requete($sql);
						            while($response=fetch_array($req))
						            {
								            ?>
								            <option value="<?php echo $response["id_base"] ?>">
								            <?php echo $response["nom_base"] ?>
								            </option>
								            <?php
						            }
						            ?>
					            </select>
				            <input type="hidden" name="id_recueil" id="id_recueil2" />
				            </td>
			            </tr>
			            
			            <tr id="tr_select_recueil2">
				            <td></td>
				            <td></td>
			            </tr>
			            <?php
			        }
			        else
			        {
			            ?>
			            <tr>
			                <td><?=_("Rechercher dans un recueil")?></td>
			                <td>
			                    <input type="hidden" name="id_base" value="<?=$_SESSION['isolated_base']?>" />
			                    <select name="id_recueil" style="width:100px">
						            <option value="tous"><?=_("Tous")?></option>
						            <?php
						            // listing de toutes les bases avec l'option toutes
						            $sql="SELECT id_recueil,titre FROM recueils WHERE id_base='{$_SESSION["isolated_base"]}'";
						            $req=requete($sql);
						            while($response=fetch_array($req))
						            {
								            ?>
								            <option value="<?php echo $response['id_recueil'] ?>" >
								            <?php echo $response["titre"] ?>
								            </option>
								            <?php
						            }
						            ?>
					            </select>
			                </td>
			            </tr>
			            <?php
			        }
			        ?>
				
			</table>
	</td>
</tr>

<tr>
        <td colspan="2">
                <div id="partition_temporaire">
	        <img alt="" id="part_image" src="afficher_partition.php?clef_sign=G&amp;clef_line=2" />
                </div>
        </td>
</tr>
<tr>
        <td colspan="2">
        <table class="note_table" border="0" style="border-collapse: collapse">
	<tr>
		<td>
			
                        <iframe src="clavier.php?nb_octave=2&amp;begin=4" 
scrolling="no" frameborder="0" width="850px" 
height="235px" style="border:0; overflow:hidden;"></iframe>
		</td>
        </tr>
        </table>
	</td>
</tr>



	
		
<tr>
	<td colspan="2" align="right"><input type="hidden" id="melodie" name="melodie" value=""/><input type="button" value="<?=_("Effacer")?>" onclick="effacer()" /><input type="submit" value="<?=_("Recherche")?>!" /></td>
</tr>


<?php

end_box();
?>
</form>
<?php
dump_page();
?>
