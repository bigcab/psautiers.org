<?php
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
//require_once("include/lilypond.php");
//titre
$title=_("Utilisateurs");
ob_start();

//Affiche tous les utilisateurs
function show_users()
{
	if($_SESSION["admin"])
	{
		$sql="";
	}
	else
	{
		$sql="";
	}
}

//Affiche un user en particulier
function show_user($id_user)
{
	if($_SESSION["admin"])
	{
		$sql="";
	}
	else
	{
		$sql="";
	}
}



if(!isset($_GET["id_user"]))
{
	show_users();
}
else
{
	show_user($_GET["id_user"]);
}


dump_page();
?>
