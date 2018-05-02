<?php
require_once("include/xml.php");
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/bbcode.php");
require_once("include/virga_texte.php");
require_once("include/mots.php");

require_once("include/texte.php");
$title=_("Analyse de la langue française du XVIième siècle");
ob_start();

#echo $title

#print_r(hiatus_mot("cestuyla"));



#marquage des h

function select_piece()
{
    if(empty($_GET["id_piece"]))
    {
        begin_box(_("Erreur"),"error_box");
        msg(_("Aucune pièce sélectionnée"));
        end_box();
        return;
    }
    $req=requete("SELECT fichier_xml FROM pieces WHERE id_piece={$_GET["id_piece"]}");
    if(num_rows($req)==0)
    {
        begin_box(_("Erreur"),"error_box");
        msg(_("La pièce n'existe pas"));
        end_box();
        return ;
    }
    $response=fetch_array($req);
    return $response["fichier_xml"];
}

// conversion des \n en retour à la ligne visible sur un navigateur => <br/>
function affichage_texte($texte /*version virga */)
{
    $texte_modif=$texte_modifie=preg_replace("/\n/","<br/>",$texte);
    return $texte_modif;
}









function show_text_with_accent_from_database()
{
    global $db_mots;
#    $req=requete("SELECT fichier_xml FROM pieces WHERE id_piece='{$_GET["id_piece"]}'");

#    if(num_rows($req)==0)
#    {
#        begin_box(_("Erreur","error_box"));
#        msg(_("La pièce n'existe pas"));
#        end_box();
#        dump_page();
#        return ;
#    }
#    $response=fetch_array($req);
#    
#    

    
    ?>
    
    
    <?=html_output($db_mots->texte)?>
        

    <?php
}

function show_text_with_accent_from_music()
{
#    $req=requete("SELECT fichier_xml FROM pieces WHERE id_piece='{$_GET["id_piece"]}'");

#    if(num_rows($req)==0)
#    {
#        begin_box(_("Erreur","error_box"));
#        msg(_("La pièce n'existe pas"));
#        end_box();
#        dump_page();
#        return ;
#    }

#    $response=fetch_array($req);
#    $filename=$response['fichier_xml'];

    global $db_mots_mus;

#    $db_class=new db_mots_class($_GET["id_piece"],"musique");

    //$db_class->guess_all();
#    print_r($db_class);
    ?>
    
            <?=html_output($db_mots_mus->texte)?>
       

    <?php
}



#par défaut  : fonction pour les accents parlés
function update_accent_form()
{
    global $db_mots, $db_mots_mus;
#    $req=requete("SELECT fichier_xml FROM pieces WHERE id_piece='{$_GET["id_piece"]}'");

#    if(num_rows($req)==0)
#    {
#        begin_box(_("Erreur","error_box"));
#        msg(_("La pièce n'existe pas"));
#        end_box();
#        dump_page();
#        return ;
#    }

#    $response=fetch_array($req);


#    $db_class=new db_mots_class($_GET["id_piece"]);
        
    //$db_class->guess_all();
    $texte=$db_mots->texte;
#    $tableau=html_form_output($texte);
    
    ?>
    <form method='post' action='?update=1&amp;id_piece=<?=$_GET["id_piece"]?>&amp;id_recueil=<?=$_GET["id_recueil"]?>&amp;id_base=<?=$_GET["id_base"]?>'>
    <?php
    
        begin_box("Edition des accents","edit_form_box");
        ?>
        <tr>
            <td><?=html_form_output($texte)?></td>
        </tr>
        <tr>
            <td><input type='submit' name='ok' value='ok'/></td>
        </tr>
        <?php
        end_box();
    ?>
    </form>
    <?php
    global $css_virga,$jquery_virga,$dialog_box_virga;
    dump_page($css_virga.$jquery_virga,"default",$dialog_box_virga);
    return ;
}




#: fonction pour les accents de la musique
function update_accent_musique_form()
{
    global $db_mots_mus;
#    $req=requete("SELECT fichier_xml FROM pieces WHERE id_piece='{$_GET["id_piece"]}'");

#    if(num_rows($req)==0)
#    {
#        begin_box(_("Erreur","error_box"));
#        msg(_("La pièce n'existe pas"));
#        end_box();
#        dump_page();
#        return ;
#    }

#    $response=fetch_array($req);


#    $db_class=new db_mots_class($_GET["id_piece"],"musique");
        
    //$db_class->guess_all();
    $texte=$db_mots_mus->texte;
#    $tableau=html_form_output($texte);

    ?>
    <form method='post' action='?update=1&amp;id_piece=<?=$_GET["id_piece"]?>&amp;id_recueil=<?=$_GET["id_recueil"]?>&amp;id_base=<?=$_GET["id_base"]?>&amp;option=musique'>
    <?php
        begin_box("Edition des accents de la musique","edit_form_box");
        ?>
        <tr>
            <td><?=html_form_output($texte)?></td>
        </tr>
        <tr>
            <td><input type='submit' name='ok' value='ok'/></td>
        </tr>
        <?php
        end_box();
    ?>
    </form>
    <?php
        global $css_virga,$jquery_virga,$dialog_box_virga;
    dump_page($css_virga.$jquery_virga,"default",$dialog_box_virga);
    return ;
}

function update_accent()
{
    global $db_mots;
#    $req=requete("SELECT fichier_xml FROM pieces WHERE id_piece='{$_GET["id_piece"]}'");

#    if(num_rows($req)==0)
#    {
#        begin_box(_("Erreur","error_box"));
#        msg(_("La pièce n'existe pas"));
#        end_box();
#        dump_page();
#        return ;
#    }


#    $db_class=new db_mots_class($_GET["id_piece"]);
        
    //$db_class->guess_all();
    $texte=$db_mots->texte;
    begin_box(_("Edition des accents"),"update_accent_box");
    $vers_n=0;
    $syllabe_n=0;
    foreach($texte->vers as $vers)
    {
        foreach($vers->syllabes as $syllabe)
        {
            $syllabic=$syllabe->syllabic;
            if(isset($_POST["accent_".$vers_n."_".$syllabe_n])&&($_POST["accent_".$vers_n."_".$syllabe_n]=="longue"))
            {
                $accent=TRUE;
            }
            else
            {
                $accent=FALSE;
            }
            
            if(preg_match("/^[h|H]/",$syllabe->text)&&(($syllabic=="single")||($syllabic!="end")))
            {
                $syllabe->is_h=TRUE;
            }
            else
            {
                $syllabe->is_h=FALSE;
            }
            if((isset($_POST["aspire_".$vers_n."_".$syllabe_n]))&&($_POST["aspire_".$vers_n."_".$syllabe_n]=="aspire"))
            {
                $h=TRUE;
            }
            else
            {
                $h=FALSE;
            }
            
            
            if(preg_match("/(ent)$/",$syllabe->text)&&(($syllabic=="single")||($syllabic=="end")))
            {
                $syllabe->is_ent=TRUE;
            }
            else
            {
                $syllabe->is_ent=FALSE;
            }
            if((isset($_POST["ent_{$vers_n}_{$syllabe_n}"]))&&($_POST["ent_{$vers_n}_{$syllabe_n}"]=="grave"))
            {
                $ent=TRUE;
            }
            else
            {
                $ent=FALSE;
            }
            $syllabe->ent=$ent;
            $syllabe->h=$h;
            $syllabe->accent=$accent;
            $syllabe_n+=1;
        }
        $vers_n+=1;
        $syllabe_n=0;
    }
    ?>
    <?php

    $db_mots->texte=$texte;
    
    $db_mots->update_db();
    
    msg(_("Le texte a été modifié"));
    msg_return_to("?id_piece={$_GET["id_piece"]}&amp;id_recueil={$_GET['id_recueil']}&amp;id_base={$_GET['id_base']}");
    end_box();
}




function update_accent_musique()
{
    global $db_mots_mus;
    $req=requete("SELECT fichier_xml FROM pieces WHERE id_piece='{$_GET["id_piece"]}'");

    if(num_rows($req)==0)
    {
        begin_box(_("Erreur","error_box"));
        msg(_("La pièce n'existe pas"));
        end_box();
        dump_page();
        return ;
    }


#    $db_class=new db_mots_class($_GET["id_piece"],"musique");
        
    //$db_class->guess_all();
    $texte=$db_mots_mus->texte;
    begin_box(_("Edition des accents de la musique"),"update_accent_box");
    $vers_n=0;
    $syllabe_n=0;
    foreach($texte->vers as $vers)
    {
        foreach($vers->syllabes as $syllabe)
        {
            $syllabic=$syllabe->syllabic;
            if(isset($_POST["accent_".$vers_n."_".$syllabe_n])&&($_POST["accent_".$vers_n."_".$syllabe_n]=="longue"))
            {
                $accent=TRUE;
            }
            else
            {
                $accent=FALSE;
            }
            
            if(preg_match("/^[h|H]/",$syllabe->text)&&(($syllabic=="single")||($syllabic!="end")))
            {
                $syllabe->is_h=TRUE;
            }
            else
            {
                $syllabe->is_h=FALSE;
            }
            if((isset($_POST["aspire_".$vers_n."_".$syllabe_n]))&&($_POST["aspire_".$vers_n."_".$syllabe_n]=="aspire"))
            {
                $h=TRUE;
            }
            else
            {
                $h=FALSE;
            }
            
            
            if(preg_match("/(ent)$/",$syllabe->text)&&(($syllabic=="single")||($syllabic=="end")))
            {
                $syllabe->is_ent=TRUE;
            }
            else
            {
                $syllabe->is_ent=FALSE;
            }
            if((isset($_POST["ent_{$vers_n}_{$syllabe_n}"]))&&($_POST["ent_{$vers_n}_{$syllabe_n}"]=="grave"))
            {
                $ent=TRUE;
            }
            else
            {
                $ent=FALSE;
            }
            $syllabe->ent=$ent;
            $syllabe->h=$h;
            $syllabe->accent=$accent;
            $syllabe_n+=1;
        }
        $vers_n+=1;
        $syllabe_n=0;
    }
    ?>
    <?php
#    $serialized=mysql_escape_string(serialize($texte));
#    requete("UPDATE pieces SET
#            texte_class='$serialized'
#            WHERE id_piece='{$_GET['id_piece']}'
#            ");
    $db_mots_mus->texte=$texte;
    $db_mots_mus->update_db();
    
    msg(_("Le texte a été modifié"));
    msg_return_to("?id_piece={$_GET["id_piece"]}&amp;id_recueil={$_GET['id_recueil']}&amp;id_base={$_GET['id_base']}");
    end_box();
}

function compare_box()
{
    global $db_mots,$db_mots_mus;
    $req=requete("SELECT fichier_xml FROM pieces WHERE id_piece='{$_GET["id_piece"]}'");

    if(num_rows($req)==0)
    {
        begin_box(_("Erreur"),"error_box");
        msg(_("La pièce n'existe pas"));
        end_box();
        dump_page();
        return ;
    }
    $response=fetch_array($req);
    $filename=$response['fichier_xml'];
    
#    $db_class_musique=new db_mots_class($_GET["id_piece"],"musique");
#    $db_class=new db_mots_class($_GET["id_piece"]);
        
    //$db_class->guess_all();
    //$db_class_musique->guess_all();
    $texte_class=$db_mots->texte;
    $texte_xml=$db_mots_mus->texte;
#    print_r(hiatus_index($texte_class,0,3));
#    update_hiatus($texte_class);
#    print_r($texte_class);
    $nb_vers=count($texte_class->vers);
    for($i=0;$i<$nb_vers;$i++)
    {
        ?>
        <tr >
            <td>
                <img src='images/design/audio_input.png' alt='"<?=_("Base")?>"' title='"<?=_("Base")?>"' width='20' height='20'/>
                <?=output_vers($texte_class,$i)?>
                <br/>
                <img src='images/design/music_input.png' alt='"<?=_("Musique")?>"' title='"<?=_("Musique")?>"' width='20' height='20'/>
                <?=output_vers($texte_xml,$i)?>
                
            </td>
        </tr>
        <tr>
        <td></td>
        </tr>
        <?php
    }
    ?>
    

    <?php
}



function data_box($id_piece)
{
    global $db_mots, $db_mots_mus;
    begin_box(_("Données numériques"),"data_box");
    
    //compute number of syllabes
#    $req=requete("SELECT count(syllabe_n) FROM accent_db WHERE id_piece='$id_piece'");
#    $response=fetch_array($req);
#    $nb_syllabes=$response["count(syllabe_n)"];
    $nb_syllabes=$db_mots->texte->nb_syllabes;
    $nb_syllabes_poly=0;
#    $db_mots=new db_mots_class($id_piece);
#    $db_mots_mus=new db_mots_class($id_piece,"musique");
    
    $correlation=0;
    $correlation_poly=0;
    $vers_n=0;
    $syllabe_n=0;
    $texte=$db_mots->texte;
    $texte_mus=$db_mots_mus->texte;
    while (($vers_n < count($texte->vers))&&($vers_n < count($texte_mus->vers)))
    {
        $syllabe_n=0;
        while( ($syllabe_n < count($texte->vers[$vers_n]->syllabes)) && ($syllabe_n < count($texte_mus->vers[$vers_n]->syllabes)))
        {
            if( $texte->vers[$vers_n]->syllabes[$syllabe_n]->accent == $texte_mus->vers[$vers_n]->syllabes[$syllabe_n]->accent )
            {
                if(($texte->vers[$vers_n]->syllabes[$syllabe_n]->syllabic!= "single")&& ($texte_mus->vers[$vers_n]->syllabes[$syllabe_n]->syllabic!= "single"))
                {
                    $correlation_poly++;
                }
                $correlation++;
            }
             if(($texte->vers[$vers_n]->syllabes[$syllabe_n]->syllabic!= "single")&& ($texte_mus->vers[$vers_n]->syllabes[$syllabe_n]->syllabic!= "single"))
             {
                $nb_syllabes_poly++;
             }
            $syllabe_n++;
        }
        $vers_n++;
    }
    
    if($nb_syllabes!= 0)
    {
        $correlation=$correlation/$nb_syllabes*100;
    }
    if($nb_syllabes_poly!= 0)
    {
        $correlation_poly=$correlation_poly/$nb_syllabes_poly*100;
    }
    ?>
    <tr>
        <th>Nombre de syllabes</th>
        <td><?=$db_mots->texte->nb_syllabes?></td>
    </tr>
    <tr>
        <th>Corrélation langue-musique</th>
        <td><?=number_format($correlation,2)?>%</td>
    </tr>
    <tr>
        <th>Corrélation langue-musique polysyllabes</th>
        <td><?=number_format($correlation_poly,2)?>%</td>
    </tr>
    <?php
    end_box();
}

?>

<?php


if(!$_SESSION["admin"])
{
    begin_box(_("Problème de droit"),"auth_box");
    msg(_("Non authorisé"));
    end_box();
    dump_page();
    return ;
}
if(empty($_GET["id_piece"]))
{
    begin_box(_("Erreur"),"error_box");
    msg(_("Aucune pièce spécifiée"));
    end_box();
    dump_page();
    return ;
}

#verification des permissions pour pouvoir executer les algorithmes sur les psaumes 


$req=requete("SELECT psaume FROM pieces WHERE id_piece='{$_GET["id_piece"]}'");
if(num_rows($req)==0)
{
    begin_box(_("Erreur","error_box"));
    msg(_("La pièce n'existe pas"));
    end_box();
    dump_page();
    return ;
}
$response=fetch_array($req);
if($response["psaume"]==0)
{
    begin_box(_("Erreur"),"error_box");
    msg(_("La pièce sélectionnée n'est pas un psaume"));
    msg_return_to("show.php?id_base=".$_GET['id_base']."&amp;id_recueil=".$_GET["id_recueil"]."&amp;id_piece=".$_GET['id_piece']);
    end_box();
    dump_page();
    return ;
}

if($_SESSION["admin"])
{
    ?>
    <a href="virga_statistiques.php?id_base=<?=$_GET['id_base']?>&amp;id_recueil=<?=$_GET['id_recueil']?>&amp;id_base=<?=$_GET['id_base']?>&amp;id_piece=<?=$_GET['id_piece']?>&amp;id_recueil=<?=$_GET['id_recueil']?>">Statistiques détaillées de la pièce</a>
    <?php
}

init_get_var("option");

$db_mots=new db_mots_class($_GET["id_piece"]);


#print_r($db_mots);
#print_r($db_mots->texte);
#echo "<br/> le mot 8,1";
#echo $db_mots->get_mot(7,0);
$db_mots_mus=new db_mots_class($_GET['id_piece'],"musique");
if(!empty($_GET["update_form"]))
{
    if($_GET["option"]=="musique")
    {
        update_accent_musique_form();
        return;
    }
    else
    {
        update_accent_form();
        return ;
    }
}
else if(!empty($_GET["update"]))
{
    if($_GET["option"]=="musique")
    {
        update_accent_musique();
        
    }
    else
    {
        update_accent();
    }
    update_updated_vars($_GET["id_piece"]);
}
else
{
    begin_box(_("Comparaison linguistique"),"compare_box");
    ?>
    <tr>
        <th><?=_("Accent de la musique")?><a href="?update_form=1&amp;id_piece=<?=$_GET['id_piece']?>&amp;id_recueil=<?=$_GET['id_recueil']?>&amp;id_base=<?=$_GET['id_base']?>&amp;option=musique"><img src='./images/design/edit.png' alt='<?=_("Editer les accents")?>' title='<?=_("Editer les accents de la musique")?>' width='20' height='20'/></a></th>
        <th><?=_("Accent de la base")?><a href="?update_form=1&amp;id_piece=<?=$_GET['id_piece']?>&amp;id_recueil=<?=$_GET['id_recueil']?>&amp;id_base=<?=$_GET['id_base']?>"><img src='./images/design/edit.png' alt='<?=_("Editer les accents")?>' title='<?=_("Editer les accents")?>' width='20' height='20'/></a></th>
    </tr>
    <tr>
        <td>
            <?php
            show_text_with_accent_from_music();
            ?>
        </td>
        <td>
            <?php
            show_text_with_accent_from_database();
            ?>
        </td>
        
    </tr>
    
    
    
    <?php
    end_box();
    
    ?>
    <table>
        <tr>
            <td>
                <?php
            
                begin_box(_("Comparaison linguistique "),"compare_box_version_2");
                compare_box();
                
                msg_return_to("show.php?id_base=".$_GET['id_base']."&amp;id_recueil=".$_GET["id_recueil"]."&amp;id_piece=".$_GET['id_piece']);
                end_box();
                ?>
            </td>
        
            <td>
                <?php
                data_box($_GET["id_piece"]);
                ?>
            </td>
        </tr>
   </table><?php
   
}




#echo "hiatus : ";


#print_r(decoupe_mot("violon"));
#print_r(reconnait_hiatus("a"));


#print_r(hiatus_mot("ioviolonoui"));

#function add_slash($word)
#{
#    return "/".$word[0]."/";
#}

#$texte2="Qui au conseil des malins n'a esté,
#Qui n'est au trac des pecheurs arresté,
#Qui des moqueurs au banc place n'a prise,
#Mais nuict & jour la Loy contemple et prise
#De l'Eternel, & en est desireux,
#Certainement cestuyla est heureux ";
#$texte="rien";

#print_r(hiatus_mot("Qui"));
#print_r(preg_split("/(([^e]n)|(e[^n])|([^a][^n])|[^aeiou])/","en an on nuit"));

    
#print_r($matches);

#print_r(hiatus_mot("en an on"));
#impression

#$corpus=file("lanoue/corpus/tristanMariane.txt");
#$tx = new Texte($corpus, $date, "lanoue/corpus/tristanMariane.txt");

#$tx->marque_h_form(true);
#$tx->printTexte();


dump_page($css_virga.$jquery_virga);
?>
