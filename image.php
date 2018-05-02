<?php
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/lilypond.php");
require_once("include/draw.php");
// function to show any picture
/*
$response = sql request response
$image_type = png_lilypond or fichier_jpg or image_incipit_jpg
*/
function show_picture($response,$image_type)
{
	
	if(($image_type!="fichier_jpg")&&($image_type!="png_lilypond")&&($image_type!="image_incipit_jpg")&&($image_type!="image_titre_recueil_jpg")&&($image_type!="image_table_matieres"))
	{
		header ("Content-type: image/png");
		draw_error_msg(_("Erreur Type d'image"),200,50);
		return;
	}
	if($image_type=="png_lilypond")
	{
		$pages=glob($response["png_lilypond"]."*.png");
		if(count($pages)==1)
		{
			header ("Content-type: image/png");
			readfile($pages[0]);
		}
		else
		{
			if((isset($_GET["page"])) && ($_GET["page"]-1<count($pages)))
			{
				header ("Content-type: image/png");
				readfile($pages[$_GET["page"]-1]);
			}
			else
			{
				draw_error_msg(_("Spécifiez la page"),150,50);
			}
		}
	}
	else
	{
		if(!empty($response[$image_type]))
		{
			header ("Content-type: image/".(get_extension($response[$image_type])));
			//echo get_extension($response[$image_type]);
			readfile($response[$image_type]);
		}
		else
		{
			header ("Content-type: image/png");
			draw_error_msg(_("Aucune image"),150,50);
			return;
		}
	}
}


if(isset($_GET["id_piece"]))
{
        if($_SESSION["admin"])
        {
        
	        $req=requete("SELECT fichier_jpg,image_incipit_jpg,png_lilypond FROM pieces WHERE id_piece='".$_GET["id_piece"]."'");
	}
	else
	{
	        $req=requete("SELECT fichier_jpg,image_incipit_jpg,png_lilypond FROM pieces p
INNER JOIN table_matieres tm ON  tm.id_piece=p.id_piece
INNER JOIN recueils r ON r.id_recueil=tm.id_recueil
INNER JOIN bases b ON b.id_base=r.id_base
LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner  
WHERE p.id_piece='".$_GET["id_piece"]."'  
AND  ( b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND (b.permissions_groupe='1' OR b.permissions_groupe='2') ) OR (b.permissions_others='2' OR b.permissions_others='1') )");
	}
	
	if(num_rows($req)==0)
        {
                draw_error_msg( _("Soit vous n'avez pas les permissions nécessaires pour voir cette pièce")."\n <br/>"._("Soit la pièce n'existe pas"), 1000, 150);
                return;
        }
        
	$response=fetch_array($req);
	show_picture($response,$_GET['image_type']);
	
}
else if(isset($_GET["id_recueil"]))
{
	if($_SESSION["admin"])
        {
        
	        $req=requete("SELECT image_table_matieres,image_titre_recueil_jpg FROM recueils WHERE id_recueil='".$_GET["id_recueil"]."'");
	}
	else
	{
	        $req=requete("SELECT r.image_titre_recueil_jpg FROM recueils r
INNER JOIN bases b ON b.id_base=r.id_base
LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner  
WHERE r.id_recueil='".$_GET["id_recueil"]."'  
AND  ( b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND (b.permissions_groupe='1' OR b.permissions_groupe='2') ) OR (b.permissions_others='2' OR b.permissions_others='1') )");
	}
	if(num_rows($req)==0)
        {
                draw_error_msg( _("Soit vous n'avez pas les permissions nécessaires pour voir cette pièce")."\n <br/>"._("Soit la pièce n'existe pas"), 1000, 150);
                return;
        }
        
	$response=fetch_array($req);
	show_picture($response,$_GET['image_type']);
}
else
{
	echo _("Aucune piece specifiée");
}
?>
