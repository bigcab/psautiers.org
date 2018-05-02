<?php
require_once("include/xml.php");
require_once("include/auth.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/bbcode.php");
require_once("include/mots.php");
require_once("include/virga_statistics.php");
$title=_("Statistiques sur virga");
ob_start();

if (!$_SESSION["admin"])
{
    begin_box("Non authorisé","forbidden_box");
    msg("Vous n'êtes pas autorisé à consulter cette page'");
    msg_return_to("show.php?id_base=".$_GET['id_base']."&amp;id_recueil=".$_GET["id_recueil"]."&amp;id_piece=".$_GET['id_piece']);
    end_box();
    dump_page("","default");
}

if(!empty($_GET["recherche"]))
{
#    here searching for informations about any word
    
}
else if (!empty($_GET["id_piece"]))
{

#--------------------------------------------statistiques sur les accents--------------------------------------------------------------
    begin_box_js("Statistiques sur les accents","accents_statistics_box");
#    here finding all the informations about any word
    ?>
    <tr><td>
    
    <table >
    <tr>
        <th>Syllabe</th>
        <th>Mot</th>
        <th>Accent</th>
        <th>Accent Musique</th>
    </tr>
    <?php
#    $db=new db_mots_class($_GET['id_piece']);
    $piece_statistics=new piece_statistics_class($_GET['id_piece']);
    $db=$piece_statistics->db_mots;
    $vers_n=0;
    foreach($db->texte->vers as $vers)
    {
        $syllabe_n=0;
        foreach($vers->syllabes as $syllabe)
        {
            $pattern=$syllabe->text;
            $word=$db->get_mot($vers_n,$syllabe_n);
#            $pattern_class=new pattern_class(convert_mot($pattern),convert_mot($word));
            ?>
            <tr>
                <td><?php echo $pattern?></td>
                <td><?php echo $word?></td>
                <td>
                    <?php echo (!isset($piece_statistics->freqs_accents[$vers_n][$syllabe_n]))?("Aucun"):(intval($piece_statistics->freqs_accents[$vers_n][$syllabe_n])."%")?>
                </td>
                <td><?php echo (!isset($piece_statistics->freqs_accents_mus[$vers_n][$syllabe_n]))?("Aucun"):(intval($piece_statistics->freqs_accents_mus[$vers_n][$syllabe_n])."%")?></td>
            </tr>
            <?php
            $syllabe_n++;
        }
        $vers_n++;
    }
    
    ?>
    </table>
    </td></tr>
    <?php
    end_box();
#----------------------------------------------------------------------------------------------------------------------------------------    
#    ---------------------------------------------statistiques sur les h aspirés----------------------------------------------------------
#---------------------------------------------------------------------------------------------------------------------------------------
    begin_box_js("Statistiques sur les 'H' aspirés","h_statistics_box");
    ?>
    <tr>
        <td>
        <table>
            <tr>
                <th>Syllabe</th>
                <th>Mot</th>
                <th>H aspiré</th>
                <th>H aspiré dans la musique</th>
            </tr>
            <?php
            foreach($piece_statistics->freqs_h as $vers_n => $tableau)
            {

                foreach($tableau as $syllabe_n => $value)
                {
#                    if($db->texte->vers[$vers_n]->syllabes[$syllabe_n]->is_h)
#                    {
                        $pattern=$db->texte->vers[$vers_n]->syllabes[$syllabe_n]->text;
                        $word=$db->get_mot($vers_n,$syllabe_n);
#                        echo $syllabe_n . " " . $vers_n . '<br/>';
            #            $pattern_class=new pattern_class(convert_mot($pattern),convert_mot($word));
                        ?>
                        <tr>
                            <td><?php echo $pattern?></td>
                            <td><?php echo $word?></td>
                            <td>
                                <?php echo (!isset($piece_statistics->freqs_h[$vers_n][$syllabe_n]))?("Aucun"):(intval($piece_statistics->freqs_h[$vers_n][$syllabe_n])."%")?>
                            </td>
                            <td><?php echo (!isset($piece_statistics->freqs_h_mus[$vers_n][$syllabe_n]))?("Aucun"):(intval($piece_statistics->freqs_h_mus[$vers_n][$syllabe_n])."%")?></td>
                        </tr>
                        <?php
#                    }
                    
                    
                }
                
            }       
            ?>
            
        </table>
        </td>
    </tr>
    <?php
    
    end_box();
    
#    -----------------------------------------------------------------------------------------------------------------------------
#--------------------------------Affichage des statistiques pour les ent-------------------------------------------------------------
#----------------------------------------------------------------------------------------------------------------------------------
    begin_box_js("Statistiques sur les ent","ent_stats_box");
    ?>
    <tr>
        <td>
        <table>
            <tr>
                <th>Syllabe</th>
                <th>Mot</th>
                <th>Ent grave</th>
                <th>Ent grave dans la musique</th>
            </tr>
            <?php
            foreach($piece_statistics->freqs_ent as $vers_n => $tableau)
            {

                foreach($tableau as $syllabe_n => $value)
                {
#                    if($db->texte->vers[$vers_n]->syllabes[$syllabe_n]->is_h)
#                    {
                        $pattern=$db->texte->vers[$vers_n]->syllabes[$syllabe_n]->text;
                        $word=$db->get_mot($vers_n,$syllabe_n);
#                        echo $syllabe_n . " " . $vers_n . '<br/>';
            #            $pattern_class=new pattern_class(convert_mot($pattern),convert_mot($word));
                        ?>
                        <tr>
                            <td><?php echo $pattern?></td>
                            <td><?php echo $word?></td>
                            <td>
                                <?php echo (!isset($piece_statistics->freqs_ent[$vers_n][$syllabe_n]))?("Aucun"):(intval($piece_statistics->freqs_ent[$vers_n][$syllabe_n])."%")?>
                            </td>
                            <td><?php echo (!isset($piece_statistics->freqs_ent_mus[$vers_n][$syllabe_n]))?("Aucun"):(intval($piece_statistics->freqs_ent_mus[$vers_n][$syllabe_n])."%")?></td>
                        </tr>
                        <?php
#                    }
                    
                    
                }
                
            }       
            ?>
            
        </table>
        </td>
    </tr>
    <?php
   
    end_box();    
    
    
    
    
#---------------------------------------------------------------------------------------------------------------------------------    
#-----------------------------------------------------Statistiques pour les hiatus-------------------------------------------------
#----------------------------------------------------------------------------------------------------------------------------------    
    begin_box_js("Statistiques pour les hiatus","hiatus_statistics_box");
    
    ?>
    <tr>
        <td>
            <table>
                <tr>
                    <th>Mot</th>
                    <th>Type du hiatus</th>
                    <th>Fréquence</th>
                </tr>
                
                <?php
#                print_r($piece_statistics->nb_hiatus_db_stats);
#                print_r($piece_statistics->hiatus_db_stats);
                foreach ($piece_statistics->freqs_hiatus as $mot=> $tableau)
                {
                    foreach ($tableau as $hiatus_form=> $value)
                    {
                        ?>
                        <tr>
                            <td><?=htmlspecialchars($mot)?></td>
                            <td><?=htmlspecialchars($hiatus_form)?></td>
                            <td><?=(!isset($piece_statistics->freqs_hiatus[$mot][$hiatus_form]))?("Aucun"):intval($value)."%"?></td>
                        </tr>
                        <?php
                        
                    }
                }
                ?>
            </table>
        </td>
    </tr>
    <?php
    
    
    end_box();



    msg_return_to("virga.php?id_base=".$_GET['id_base']."&amp;id_recueil=".$_GET["id_recueil"]."&amp;id_piece=".$_GET['id_piece']);
}
else if((!empty($_GET["pattern"])) &&( !empty($_GET["word"])))
{
    
    begin_box("Recherche de motifs","pattern_search_box");
    msg_return_to("show.php?id_base=".$_GET['id_base']."&amp;id_recueil=".$_GET["id_recueil"]."&amp;id_piece=".$_GET['id_piece']);        
    // maybe we will do some little treatment here on $pattern and $word
    $word=$_GET["word"];
    $pattern=$_GET["pattern"];
    $pattern_class=new pattern_class(convert_mot($pattern),convert_mot($word));
    ?>
    <tr>
        <th>Syllabe</th>
        <td><?= $pattern?></td>
    </tr>
    <tr>
        <th>Mot</th>
        <td><?=$word?></td>
    </tr>
    <tr>
        <th>Accent</th>
        <td><?php echo (!isset($pattern_class->freqs['accent_db']))?("Aucun"):(intval($pattern_class->freqs['accent_db'])."%"); ?></td>
    </tr>
    <tr>
        <th>Accent de la musique</th>
        <td><?php echo (!isset($pattern_class->freqs['accent_mus_db']))?("Aucun"):(intval($pattern_class->freqs['accent_mus_db'])."%"); ?></td>
    </tr>
    <?php


    end_box();
    
    search_pattern_form();
}
else
{
    search_pattern_form("no_js");
}


function search_pattern_form($option="js")
{
    ?>
    <form action="?" method="GET">
    <?php
    if($option=="js")
    {
        begin_box_js("Recherche de motifs","pattern_form_box");
    }
    else
    {
        begin_box("Recherche de motifs", "pattern_form_box");
    }
    
    ?>
    <tr>
        <th>Syllabe</th>
        <td><input type="text" name="pattern" value=""/></td>
    </tr>
    <tr>
        <th>Mot</th>
        <td><input type="text" name="word" value=""/></td>
    </tr>
    <tr>
        <td colspan="2" align="right"><input type="submit"  value="Valider"/></td>
    </tr>
    <?php
    if($option=="no_js")
    {
        msg_return_to("show.php?id_base=".$_GET['id_base']."&amp;id_recueil=".$_GET["id_recueil"]."&amp;id_piece=".$_GET['id_piece']);    
    }
    end_box();
    ?>
    </form>
    <?php  
}


dump_page("","default");


?>
