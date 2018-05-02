<?php
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/lilypond.php");
require_once("include/log.php");
require_once("include/global.php");
require_once("include/config.php");

switch ($_GET["file"])
{
	case "guide_pdf":
		if(!isset($_GET["id_base"]))
		{
			echo _("Erreur vous devez spécifier une base");
			return;
		}
		$req=requete("SELECT nom_base,guide_pdf FROM bases WHERE id_base='".$_GET["id_base"]."'");
		$response=fetch_array($req);
		$pdf=$response["guide_pdf"];
		if(empty($pdf))
		{
			echo _("Le fichier pdf n'existe pas");
			return;
		}
		$fichier=fopen($pdf,"r");
		header("Content-Type: application/pdf");
		header("Content-Length: ".filesize($pdf));
		header("Content-Disposition: attachment; filename=\"".$response["nom_base"].".pdf\"");

		fpassthru($fichier);
		fclose($fichier);	
	break;
	
	case "xml":
		//pour le tel du fichier musicxml
		if((ALLOW_DOWNLOAD_XML==TRUE) || ($_SESSION["admin"]) )
		{
			if(!isset($_GET["id_piece"]))
			{
				echo _("Erreur: vous devez spécifier un fichier");
				return ;
			}

			if($_SESSION["admin"])
			{
				$req=requete("SELECT fichier_xml,titre FROM pieces p WHERE id_piece='".$_GET["id_piece"]."'");
			}
			else
			{
				$req=requete("SELECT fichier_xml,p.titre FROM pieces p
			INNER JOIN table_matieres tm ON  tm.id_piece=p.id_piece
			INNER JOIN recueils r ON r.id_recueil=tm.id_recueil
			INNER JOIN bases b ON b.id_base=r.id_base
			LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner  
			WHERE p.id_piece='".$_GET["id_piece"]."'  
			AND  ( b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND (b.permissions_groupe='1' OR b.permissions_groupe='2') ) OR (b.permissions_others='2' OR b.permissions_others='1') )
			");
			}
			if(num_rows($req)==0)
			{
				echo _("Soit vous n'avez pas les permissions nécessaires pour voir cette pièce")." \n<br/>"._("Soit la pièce n'existe pas");
				return;
			}

			$response=fetch_array($req);
			$fichier=fopen($response["fichier_xml"],"r");
			header("Content-Type: text/xml");
			header("Content-Length: ".filesize($response["fichier_xml"]));
			header("Content-Disposition: attachment; filename=\"".$response["titre"].".xml\"");

			fpassthru($fichier);
			fclose($fichier);
		}
	break;
	
	case "export":
		if($_SESSION["admin"]&& ( ($_SESSION["pseudo"]=="root") || ($_SESSION["pseudo"]=="alice") ) )
		{
			if(empty($_GET["id_base"])&&empty($_GET["id_recueil"]))
			{
				echo _("Spécifiez une base ou un recueil");
				return ;
			}
			if ((!empty($_GET["id_recueil"]))&&($_GET["id_recueil"]!="tous"))
			{
				$req=requete("SELECT export,titre FROM recueils WHERE id_recueil='{$_GET['id_recueil']}'");
				$response=fetch_array($req);
#				$fichier=fopen("/home/ovh/www/export/".$response["export"],"r");
#				header("Content-Type: application/zip");
#				header("Content-Length: ".filesize("/home/ovh/www/export/".$response["export"]));
#				header("Content-Disposition: attachment; filename=\"".$response["titre"].".zip\"");

#				fpassthru($fichier);
#				fclose($fichier);
                header("Location: "."./export/".$response["export"]);
				return ;
			}
			if ((!empty($_GET["id_base"]))&&($_GET["id_base"]!="toutes"))
			{
				$req=requete("SELECT export,nom_base FROM bases WHERE id_base='{$_GET['id_base']}'");
				$response=fetch_array($req);
#				$fichier=fopen("/home/ovh/www/export/".$response["export"],"r");
#				header("Content-Type: application/zip");
#				header("Content-Length: ".filesize("/home/ovh/www/export/".$response["export"]));
#				header("Content-Disposition: attachment; filename=\"".$response["nom_base"].".zip\"");

#				fpassthru($fichier);
#				fclose($fichier);
                header("Location: "."./export/".$response["export"]);
				return ;
			}
			
			
		}
	break;
	
	case "orig":
		$filename="./locale/traductions/orig.pot";
       	$fichier=fopen($filename,"r");
       	header("Content-Type: text/plain");
       	header("Content-Length: ".filesize($filename));
        header("Content-Disposition: attachment; filename=\"orig.pot\"");
        fpassthru($fichier);
        fclose($fichier);
	break;
	
	case "traduction":
	    if(isset($_GET["mode"]))
	    {
	        if($_GET["mode"]=="default")
	        {
           		$filename="./locale/en/LC_MESSAGES/traduction.po";
               	$fichier=fopen($filename,"r");
               	header("Content-Type: text/plain");
		       	header("Content-Length: ".filesize($filename));
		        header("Content-Disposition: attachment; filename=\"traduction_en.po\"");
                fpassthru($fichier);
		        fclose($fichier);
	        }
	        else
	        {
	            $dir=get_traduction_directory($_GET["mode"]);
	            switch($_GET["lang"])
	            {
	                case "en":
	                    $filename="./locale/traductions/$dir/en/LC_MESSAGES/traduction.po";
	                    $fichier=fopen($filename,"r");
	                    header("Content-Type: text/plain");
		                header("Content-Length: ".filesize($filename));
		                header("Content-Disposition: attachment; filename=\"traduction_en.po\"");

		                fpassthru($fichier);
		                fclose($fichier);	

	                break;
	                
	                case "fr":
	                    $filename="./locale/traductions/$dir/fr/LC_MESSAGES/traduction.po";
	                    $fichier=fopen($filename,"r");
	                    header("Content-Type: text/plain");
		                header("Content-Length: ".filesize($filename));
		                header("Content-Disposition: attachment; filename=\"traduction_fr.po\"");
                        
                        fpassthru($fichier);
		                fclose($fichier);	

	                break;
	                
	                default :
	                    ob_start();

                        $title=_("Téléchargement d'un fichier de traduction");
                        ?>
                        <form method="get" action="download.php">
                        <?php
	                    begin_box(_("Choix de la langue"));
	                    msg("<input type='hidden' name='file' value='traduction'/>
	                        <input type='hidden' name='mode' value='".$_GET["mode"]."'/>
	                        <select name='lang'>
	                            <option value='en'>"._("Anglais")."</option>
	                            <option selected='selected' value='fr'>"._("Français")."</option>
	                         </select>
	                         <input type='submit' name='ok' value='ok'/>");
	                    end_box(); 
	                    ?>
	                    </form>
	                    <?php
	                    dump_page();
	                break;
	            }
	            
	        }
	    }
	break;
}


?>
