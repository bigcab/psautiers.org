<?php
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/auth.php");
require_once("include/log.php");
session_destroy();
$title=_("Déconnexion");
ob_start();
$_SESSION["authorized"]="no";


begin_box(_("Déconnexion"));
msg(_("Déconnexion réussie"));
end_box();

$contenu =  ob_get_contents(); 
ob_end_clean(); 
afficher_page($title,$contenu);
?>
