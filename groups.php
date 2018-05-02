<?php
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
//require_once("include/lilypond.php");
//titre
$title=_("Groupes");
ob_start();




if(!isset($_GET["id_groupe"]))
{
	show_groups();
}
else
{
	show_group($_GET["id_groupe"]);
}

dump_page();
?>
