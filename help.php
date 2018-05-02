<?php
require_once("include/xml.php");
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/bbcode.php");
$title=_("Aide");
ob_start();



begin_box(_("Aide"),"help_box");

msg("Si vous voulez reporter un bug qui vous semble urgent à corriger, veuillez m'envoyer un email à l'adresse suivante : ");
msg("www.bigcab(remplacer par at)free.fr");
msg("L'objet du mail devrait commencer par 'Support psautiers.org :'");
msg("Si c'est un problème qui survient lorsque vous faites telle ou telle manipulations,");
msg("détaillez ce que vous faites (quelles pages vous consultez etc ...) ");
msg("Merci pour votre aide");

msg("");
msg("Dang Nguyen Bac");
end_box();


dump_page();
?>
