<?php

require_once("include/xml.php");
require_once("include/mysql.php");
#require_once("include/page.php");
#require_once("include/upload.php");
#require_once("include/check.php");
require_once("include/log.php");
#require_once("include/lilypond.php");
#require_once("include/check_serv_dev.php");
require_once("include/mots.php");

init_db();
$req=requete("SELECT DISTINCT id_piece FROM accent_db WHERE mot LIKE '%ouez%'");

while($response=fetch_array($req))
{
    
	$id_piece=$response["id_piece"];
	echo $id_piece."\n";
	requete("DELETE FROM hiatus_db WHERE id_piece='$id_piece'");
	$db=new db_mots_class($id_piece);
#	if(!$db->has_entries())
#	{
	    $db->update_db();
#	}
	
	$db= new db_mots_class($id_piece,"musique");
#	if(!$db->has_entries())
#	{
	    $db->update_db();
#	}
}

#$db=new db_mots_class(288);
#$db->update_db();


#ici on update les problemes d'accents sans tout purger dans la base 

function traite($mot)
{
    return mysql_escape_string(preg_replace("/:/","",$mot));
}




#print_r($db);
?>
