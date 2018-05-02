<?php
require_once("include/draw_music_xml.php");
require_once("include/draw.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/check.php");
init_db();

if(isset($_GET["id_piece"]))
{
	$sql="SELECT fichier_xml FROM pieces WHERE id_piece='".$_GET["id_piece"]."'";
	$req=requete($sql);
	if(num_rows($req)==0)
	{
		draw_error_msg(_("La pièce n'existe pas"),200,20);
		return;
	}
	$response=fetch_array($req);
	
	$music_xml=new music_xml_class($response["fichier_xml"]);
	draw_parts($music_xml->parts);
}
else
{
	draw_error_msg(_("Pas de pièce specifiée"),50,20);
}
?>
