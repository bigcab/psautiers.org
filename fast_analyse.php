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

$title="Analyse rapide";

function fast_upload_form()
{
        ?>
        <form action="" method="post" enctype="multipart/form-data">
        Fichier MusicXML :<input type="file" name="file" value="Entrez un fichier musicXML"/>
        <input type="submit" name="ok" value="Analyser"/>
        </form>
        <?php
}

if((empty($_FILES["file"]))|| (empty($_FILES['file']['tmp_name'])))
{
    fast_upload_form();
}
else
{
    

#    function upload($file,$extensions_valides,$dossier)
    $fichier=upload("file",array("XML","xml"), "temp/fast_xml/");
    
   
    
    $analyse=new analyse_class($fichier);
    
    begin_box("Histogramme","hist");
    ?>
    <tr><td align="center" width="100%">
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
    <!--<img alt='' align="middle" src="testbac.php?<?=convert_to_get_string($analyse->frequence_notes_absolues)?>&option=partial" width="70%" height="80%" onclick="popup_image('fast_histogram.php?<?=convert_to_get_string($analyse->frequence_notes_absolues)?>',550,600)"/>-->
    </td></tr>
    <?php
    end_box();
    ?>
    <br/><br/>
    <?php
    
    
    //$analyse=new analyse_class($fichier);
    $analyse->output("js","true");
    ?>
    <br/>
    <br/>
    <?php 

    $analyse->output_by_measures("false");
}
dump_page($css_histogram.$jquery_histogram);
?>
