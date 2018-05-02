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
if(empty($_GET["id_piece"]))
{
	draw_error_msg("aucune piece spécifiee",300,100);
	return ;
}
$req=requete("SELECT fichier_xml FROM pieces WHERE id_piece='{$_GET['id_piece']}'");
$res=fetch_array($req);
if(empty($res["fichier_xml"]))
{

	draw_error_msg("Le fichier xml n'existe pas pour cette pièce",500,100);
	return ;
}


#echo $res["fichier_xml"];


#two options : all or measure
#third argument : indicates the real measure number we have to substract 1 to get the index in array
#output is of form number => value in percent
function fast_analyse_histogram($filename, $option= "all",$measure_nb="0")
{
    global $correspondances;
    $xml=new music_xml_class($filename);
    $output=array_fill(0,12,0);
    $duree_totale=0;
    if($option == "measure" )
    {
        $measure_nb =$measure_nb-1;
        foreach($xml->parts as $part)
        {
            $measure=$part->measures[$measure_nb];
            foreach($measure->notes as $note)
            {
                $note_info=$note->note_info;
                $value=$note->duration;

				if(!empty($note_info->step) )
				{
				    $output[modulo_spe($correspondances[$note_info->step]+
							$note_info->alter,12)]+=$value;
    				$duree_totale+=$value;
				}
            }
        }        
    }
    else
    {
        foreach($xml->parts as $part)
        {
            foreach($part->measures as $measure)
            {
                foreach($measure->notes as $note)
                {
                    $note_info=$note->note_info;
                    $value=$note->duration;

				    if(!empty($note_info->step) )
				    {
				        $output[modulo_spe($correspondances[$note_info->step]+
							    $note_info->alter,12)]+=$value;
        				$duree_totale+=$value;
				    }
                }
            }
        }  
    }
    
    $output=renormalize_without_silence($output);
    return $output;
}


$hist=new histogram_class(450,450,15,40,5);

if(!empty($_GET["measure"]))
{
    $measure=intval($_GET["measure"]);
    $tableau=fast_analyse_histogram($res["fichier_xml"],"measure",$measure);
}
else
{
    $tableau=fast_analyse_histogram($res["fichier_xml"],"all");
}
#print_r($tableau);
$hist->results($tableau);
$hist->affich();


?>
