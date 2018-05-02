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
$req=requete("SELECT DISTINCT id_piece FROM hiatus_db WHERE 1");

while($response=fetch_array($req))
{
    
	$id_piece=$response["id_piece"];
	echo $id_piece."\n";
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


$dbs=array("accent_db", "accent_mus_db", "ent_db","ent_mus_db" , "h_db" , "h_mus_db" );
foreach( $dbs as $db )
{
    $req=requete("SELECT * from $db WHERE syllabe LIKE '%:%'");
    while($response=fetch_array($req))
    {
        $old_syllabe=$response['syllabe'];
        $old_mot=$response['mot'];
        $vers_n=$response['vers_n'];
        $syllabe_n=$response['syllabe_n'];
        $syllabe=traite($old_syllabe);
        $mot=traite($old_mot);
        $id_piece=$response['id_piece'];
        
        requete("UPDATE $db SET mot='$mot', syllabe='$syllabe' WHERE id_piece='$id_piece' AND syllabe_n='$syllabe_n' AND vers_n='$vers_n'");
    }
    
}

#now doing for hiatus_db
$db="hiatus_db";
$req=requete("SELECT * from $db WHERE syllabe LIKE '%:%'");
while($response=fetch_array($req))
{
    $old_syllabe=$response['syllabe'];
    $old_hiatus_form=$response['hiatus_form'];
    $old_hiatus_string=$response['hiatus_string'];
    $old_mot=$response['mot'];
    $vers_n=$response['vers_n'];
    $syllabe_n=$response['syllabe_n'];
    $syllabe=traite($old_syllabe);
    $mot=traite($old_mot);
    $hiatus_string=traite($old_hiatus_string);
    $hiatus_form=traite($old_hiatus_form);
    $id_piece=$response['id_piece'];
    
    requete("UPDATE $db SET mot='$mot', syllabe='$syllabe',hiatus_string='$hiatus_string' , hiatus_form='$hiatus_form' WHERE id_piece='$id_piece' AND syllabe_n='$syllabe_n' AND vers_n='$vers_n'");
}

#print_r($db);
?>
