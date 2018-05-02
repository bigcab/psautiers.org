<?php


require_once("include/auth.php");
require_once("include/global.php"); // for ALLOW_DOWNLOAD_XML
require_once("include/xml.php");
require_once("include/note.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/lilypond.php");

require_once("include/analyse.php");
require_once("include/histogram.php");
is_authorized();
ob_start();



$title="Analyse";

?>

<?php

#if(!$_SESSION["admin"])
#{
#	begin_box("Problème de droit","auth");
#	msg("Vous n'êtes pas autorisé à voir cette page");
#	end_box();
#	dump_page();
#	return;
#}
init_get_var("id_piece");
if(empty($_GET["id_piece"]))
{
	begin_box("Veuillez spécifier une pièce","piece");
	msg("Aucune pièce n'a été spécifiée");
	end_box();
	dump_page();
	return;
}

$req=requete("SELECT fichier_xml FROM pieces WHERE id_piece='{$_GET['id_piece']}'");
$res=fetch_array($req);
if(empty($res["fichier_xml"]))
{
	begin_box("Fichier XML","no_xml");
	msg("Le fichier xml n'existe pas pour cette pièce");
	end_box();
	dump_page();
	return ;
}



$analyse=new analyse_class($res["fichier_xml"]);


init_get_var("option");

switch($_GET["option"])
{
	case "gamme":
		?><script language="Javascript" type="text/javascript" src="js/analyse.js"></script><?php
		init_post_var("melodie");
		$gamme_custom=parse_gamme($_POST["melodie"]);
		$string=convert_note_to_absolute_interval($_POST["melodie"]); // gamme in interval example 0/2/4/7/9
		//print_r($gamme_custom->notes);
		show_identify_gamme($gamme_custom,$analyse,"Gamme entrée 0/".$string,"default","Monika pondérée");
		show_identify_gamme($gamme_custom,$analyse,"Gamme 0/".$string,"monika","Monika");
	break;
	
	case "successions_note_note":
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
           "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
        <head>
	        <meta name="google-site-verification" content="2PFLfOBaeKcXvldl5brrFdep8PPxXL_jTF7mHA0v3nM" />
	        <meta name="Author" content="Yoann Desmouceaux, Nguyen Bac Dang, Alice Tacaille, Daniel Morel,Pierre Boivin"/>
	        <meta name="Keywords" content="Recherche de psaumes, Psaumes, Psautiers, psautiers.org , psautiers.fr, www.psautiers.org, www.psautiers.fr, Recherche musicale, recherche mélodique,rechercher une melodie, find a melody, score finder "/>
	        <meta name="Description" content="This site provides a powerful script to search for any melody in the database, rechercher une melodie, un psaume en entrant la mélodie"/>
	        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	        <title>Psautiers : Recherche mélodique dans les psaumes -- <?php echo $titre;  if (!empty($_SESSION['pseudo'])) echo _(" -- Identifié en tant que")." ".$_SESSION['pseudo'];?></title>
	        <link href='css/style.css' rel='stylesheet' type='text/css' /> 
	
            
        </head>
        <body style="background-color: <?=$_SESSION['body_background_color']?>;">

        <div onclick="window.close()">
		<?php
		$analyse->output_successions_note_note();
		?>
		</div>
		<?php
		$contenu =  ob_get_contents(); 
		ob_end_clean(); 
		echo $contenu;
		?>
		</body>
		</html>
		<?php
		return ;
	break;
	
	case "fragment":
        ?><script language="Javascript" type="text/javascript" src="js/analyse.js"></script><?php    
		init_post_var("measure_start");
		init_post_var("measure_end");
		init_post_var("beat_start");
		init_post_var("beat_end");		
		if(($_SESSION["pseudo"]=="root")||($_SESSION["pseudo"]=='alice'))
		{
			$analyse->fragment($_POST["measure_start"],$_POST["measure_end"],$_POST["beat_start"],$_POST["beat_end"]);
			$analyse->output_analyse_fragment();
		}
	break;
}

choose_gamme_form();

if(($_SESSION["pseudo"]=="root")||($_SESSION["pseudo"]=='alice'))
{
	choose_fragment_form($analyse->xml->parts[0]->nb_measures);
}

begin_box("Histogramme","histogram_box");
?>
<tr><td align="center">
    <?php
    $tableau=array();
    for($i=0; $i<12 ; $i++) 
    {
    
        if(!empty($analyse->frequence_notes_absolues[$i]))
        {
            $tableau[$i]=number_format($analyse->frequence_notes_absolues[$i],0);
        }
        else
        {
            $tableau[$i]=0;
        }
    }
    jquery_histogram($tableau,"partial");
    ?>
</td></tr>
<?php
end_box();
?>
<br/><br/>
<?php


$analyse->output("js");

if ($_SESSION["admin"])
{
	$analyse->output_mesure_invariante();
}

?>
<br/>
<br/>

<script language="Javascript" type="text/javascript" src="js/analyse.js"></script>

<?php 



//pour commenter et debugger sans que ça se voit
#if(($_SESSION["pseudo"]=="root")||($_SESSION["pseudo"]=='alice') || ($_SESSION["pseudo"]=='dam'))
#{
	
	
	$analyse->output_by_measures();
#	$jquery="
#	        <script type='text/javascript' src='../jquery/jquery.js'></script>
#	        ";
	//print_r($analyse->get_fragment(0,2,0,1.5));
	//if(!empty($_GET["measure_start"]))
	
	
	//choose_fragment_form($analyse->xml->parts[0]->nb_measures);
	
	//$analyse->fragment(0,2,0,1.5);
	//print_r($analyse->majeure_fragment);
	//$analyse->output_analyse_fragment();
	echo "<!--";
	//print_r($analyse->intervals);
	echo "<br/>";
	//print_r($analyse->phrases);
	?>
	<?php
	
	
	//$analyse->output_tableau($analyse->majeure_monika_interval_by_measures,"","Majeure monika par mesures");
	//debug($analyse->majeure_monika_interval_by_measures,"maj");
	//print_r($analyse->frequence_notes_detailed_by_measures);
	/*echo "majeure";
	print_r($analyse->majeure_monika_interval_by_measures);
	echo "mineure";
	print_r($analyse->mineure_monika_interval_by_measures);
	echo "Bilan:";
	print_r($analyse->max);
	echo "Synthese";
	print_r($analyse->best);*/
	//print_r($analyse->frequence_notes_detailed);
	//print_r($analyse->penta_interval);

	echo "-->";
#	$tab=$analyse->frequence_notes_detailed_by_measures[15];
#	print_r($tab);
#	$ntab=$majeure_interval->correct_notes($tab,"D♭");
#	echo "<br>";
#	print_r($majeure_interval->get_notes("D♭"));
#	echo "<br>";
#	$gamme=normalize($majeure_interval->get_notes("D♭"));
#	print_r( $gamme);
#	echo "<br>";
#	echo scalar_product(array("C" => 1),array("C" => 2 , "D" => 1));
#	echo "<br>";
#	print_r($ntab);
#	echo "<br>";
#	$s=scalar_product($gamme,normalize($ntab));
#	echo $s;
#	echo "<br>";
#	$theta=acos($s);
#	echo $theta;
#	echo "<br>";
#	$theta_zero=acos(1/sqrt($majeure_interval->nb_notes));
#	echo $theta_zero;
#	echo "<br>";
#	echo (1 - ($theta / $theta_zero) )*100;
#	echo "<br>";
#	echo $majeure_interval->coef_in_gamme($analyse->frequence_notes_detailed_by_measures[14],"C");


#}
dump_page($css_histogram.$jquery_histogram.$jquery);



?>
