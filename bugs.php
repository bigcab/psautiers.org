<?php
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/lilypond.php");
require_once("include/log.php");
require_once("include/global.php");
require_once("include/bbcode.php");
$title=_("Signaler un bug");
ob_start();

//résolu 0 faux
//resolu 1 vrai


if(is_admin()) /* <=> est authorizé*/
{
	
	//si une requete a été demandée
	if (isset($_GET["option"]))
	{
		switch ($_GET["option"])
		{
			case "add_bug":
			
				requete("INSERT INTO bugs VALUES ('','" . time() . "', '0' , '" . $_POST["contenu"] . "', '".$_POST["titre"]."')");
			break;
			
			case "remove_bug":
				requete("DELETE FROM bugs WHERE id='".$_GET["id_bug"]."'");
			break;
			
			
			case "set_solved":
				requete("UPDATE bugs SET resolu='1' WHERE id='".$_GET["id_bug"]."'");
			break;
		}
		
		
	}	
	
	begin_box_js(_("Signaler un bug"),"add_bug_box");
	?>
	<form action="?option=add_bug" method="post" >
	<script src="js/bbcode.js"> </script>
	<tr><td>Titre : </td><td><input type="text" name="titre" /></td></tr>
	<tr><td>Description :</td>
	<td>
	<select onchange="insertTag('[size=' + this.options[this.selectedIndex].value  + ']', '[/size]','textarea', 'textarea');">
		<option value="none" class="selected" selected="selected">Taille</option>
		<option value="5">Très très petit</option>
		<option value="10">Très petit</option>
		<option value="15">Petit</option>
		<option value="20">Gros</option>
		<option value="25">Très gros</option>
		<option value="30">Très très gros</option>
	</select>
	<button type="button" onClick="insertTag('[img]', '[/img]','textarea', 'textarea');" name="img">img</button>
	<button type="button" onClick="insertTag('[i]', '[/i]','textarea', 'textarea');" name="i">italique</button>
	<button type="button" onClick="insertTag('[b]', '[/b]','textarea', 'textarea');" name="b">gras</button>
	<button type="button" onClick="insertTag('[justify]', '[/justify]','textarea', 'textarea');" name="justify">justifier</button>
	<button type="button" onClick="insertTag('[url=]', '[/url]','textarea', 'textarea');" name="url">url</button>
	<button type="button" onClick="insertTag('[center]', '[/center]','textarea', 'textarea');" name="center">centré</button>
	<button type="button" onClick="insertTag('[img]', '[/img]','textarea', 'textarea');" name="img">image</button>
	<button type="button" onClick="insertTag('[right]', '[/right]','textarea', 'textarea');" name="right">droit</button>
	<button type="button" onClick="insertTag('[left]', '[/left]','textarea', 'textarea');" name="left">gauche</button>
	<button type="button" onClick="insertTag('[color=]', '[/color]','textarea', 'textarea');" name="color">couleur</button>
	<button type="button" onClick="insertTag('[email]', '[/email]','textarea', 'textarea');" name="email">email</button>
	<img src="http://users.teledisnet.be/web/mde28256/smiley/smile.gif" alt=":)" onclick="insertTag(':)', '', 'textarea');" />
	<img src="http://users.teledisnet.be/web/mde28256/smiley/unsure2.gif" alt=":euh:" onclick="insertTag(':euh:', '', 'textarea');" />
	<br/>
	<textarea id="textarea" name="contenu" cols="50" rows="10"></textarea></td></tr>
	
	<tr><td></td><td><input type="submit" value="Envoyer" /></td></tr>
	</form>
	<?php
	end_box();
}
?>
<table>
<tr>
	<th>Titre</th>
	<th>Texte</th>
	<th>Date</th>
	<th>Résolu</th>
	<?php
	if($_SESSION["admin"])
	{
		?>
		<th>Supprimer</th>
		<th>Modifier statut</th>
		<?php
	}
	?>
</tr>
<?php
/*
-- 
-- Structure de la table `bugs`
-- 

CREATE TABLE `bugs` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` bigint(20) NOT NULL,
  `resolu` tinyint(1) NOT NULL,
  `texte` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

*/
$req=requete("SELECT * FROM bugs ORDER BY id DESC");
if($_SESSION["admin"])
{
	while($response=fetch_array($req))
	{
		$date=date('d/m/Y à H\hi', $response['timestamp']);
		?>
		<tr>
			<td><?=$response["titre"]?></td>
			<td><?=bbcode($response["texte"])?></td>
			<td width="50"><?=$date?></td>
			<td width="10"><input type="checkbox" DISABLED <? echo ($response["resolu"])?("checked"):("") ?> /></td>
			<td><a href="?option=remove_bug&id_bug=<?php echo $response["id"] ?>"><img src='./images/design/remove.png'  alt='Supprimer' title='Supprimer' width='20' height='20'/></a></td>
			<td><a href="?option=set_solved&id_bug=<?php echo $response["id"] ?>"><img src='./images/design/edit.png' alt='Bug resolu' title='Bug resolu' width='20' height='20'/></a></td>
		</tr>
		<?php
	}
}
else
{
	while($response=fetch_array($req))
	{
		$date=date('d/m/Y à H\hi', $response['timestamp']);
		?>
		<tr>
			<td><?=$response["titre"]?></td>
			<td><?=bbcode($response["texte"])?></td>
			<td width="50"><?=$date?></td>
			<td width="10"><input type="checkbox" DISABLED <? echo ($response["resolu"])?("checked"):("") ?> /></td>
		</tr>
		<?php
	}
}
?>
</table>
<?php

dump_page();
?>
