<?php




#require_once("include/auth.php");
#require_once("include/xml.php");
#require_once("include/mysql.php");
#require_once("include/page.php");
#require_once("include/upload.php");
#require_once("include/check.php");
#require_once("include/lilypond.php");
#require_once("include/log.php");
#require_once("include/config.php");
#require_once("include/clavier.php");
#require_once("include/texte.php");
#require_once("include/mots.php");
#require_once("include/virga_statistics.php");
#is_authorized();

#init_db();
#ob_start();
#$title="Page de test";
#$id_piece="185";

#$req=requete("SELECT mot FROM accent_db WHERE id_piece='$id_piece'");
#while($response=fetch_array($req))
#{
#    $v = new virga_statistics_class($response['mot']);
#    print_r($v);
#    echo "<br>";
#}
$language='en_GB.UTF-8';
putenv("LANG=$language"); 
setlocale(LC_ALL, $language);

bindtextdomain("traduction", "./locale"); 
textdomain("traduction"); 
echo _("test traduction");
?>

