<?php

require_once("include/auth.php");
require_once("include/global.php"); // for ALLOW_DOWNLOAD_XML
require_once("include/xml.php");
require_once("include/note.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/draw.php");

require_once("include/analyse.php");
require_once("include/histogram.php");
//is_authorized();


header("Content-type: image/png");
#if(!$_SESSION["admin"])
#{
#	draw_error_msg("non autorise",200,100);
#	return ;
#}





$hist=new histogram_class(450,450,15,40,5);

$tableau=array();
for ($i=0 ; $i< 12 ; $i++ )
{
    $tableau[$i]=$_GET["note_".$i];
}
$hist->results($tableau);


$hist->affich();


?>
