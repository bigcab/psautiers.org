<?php
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/mots.php");
#Ce fichier contient les classes pour s'occuper correctement des
DEFINE("BREVE","BREVE");
DEFINE("LONGUE","LONGUE");







#Fonctions de traitement pour les hiatus
#cas à considérer : vi/eu/



#on découpe les mots en enlevant les consonnes
#pb : hair

$exception_hiatus=array("queu","Queu","qui","Qui","que","Que","qu","Qu","gue","Gue");
#à compléter plus tard
# fléau , haïr , Léo , aorte, aérer , oasis ,vieux, vieille
$hiatus_list=array("ien","ieu","iei","éau","ian",
    "ae","aé","ao","aï",
    "ea","éa","éi","eo","éo",
    "ia","ie", "io",
    "oa","oé","oe","oi","oui","ouy","oy","oué","ouez",
    "ue","ui","uy");


function get_preg_string($tableau)
{
    $string="";
    $n=0;
    foreach($tableau as $exception)
    {
        if($n==0)
        {
            $string.="(".$exception."";
        }
        else
        {
            $string.="|".$exception."";
        }
        $n++;
    }
    $string.=")";
    return $string;
}

$exception_string=get_preg_string($exception_hiatus);
$preg="/".$exception_string."|[^(en)(an)aeiouy]/";

$hiatus_preg=get_preg_string($hiatus_list);

function decoupe_mot($mot)
{
    global $preg;
    return preg_split($preg,$mot);
}





function reconnait_hiatus($hiatus_potentiel)
{
    global $hiatus_list;
    return in_array($hiatus_potentiel,$hiatus_list);
}

function exception_hiatus($hiatus_potentiel)
{
    global $exception_hiatus;
    return !in_array(strtolower($hiatus_potentiel),$exception_hiatus);
}

function remplacement($word)
{
    $n=mb_strlen($word[0]);
    $string="";
    for ($i=0 ; $i<$n ;$i++)
    {
        $string.="_";
    }
    return $string;
}

function enleve_exception($mot)
{
    global $exception_string;
    return preg_replace_callback("/".$exception_string."/","remplacement",$mot);
}

#cette fonction renvoie un tableau de la forme [ [hiatus , position_debut, position_fin]] 


#version obsolete
#function hiatus_mot($mot)
#{
#    $decoupage=decoupe_mot($mot);
##    print_r($decoupage);
#    $tableau_hiatus=array_filter($decoupage,reconnait_hiatus);
##   on enleve les exceptions du genre qui
##    $tableau_hiatus=array_filter($tableau_hiatus_tmp,exception_hiatus);    
#    #    on met des '_' à la place des consonnes 
#    global $preg;
#    $mot_special=preg_replace($preg,"_",$mot);
##    echo $mot_special;    
#    $output=array();
##    print_r($mot);
##    print_r($tableau_hiatus_tmp);
#    foreach($tableau_hiatus as $hiatus_form)
#    {
#        $form1="_".$hiatus_form."_";
##        forme 2 début d'un mot
#        $form2=$hiatus_form."_";
##        forme 3 en fin de mot
#        $form3="_".$hiatus_form;

#        if(strstr($mot_special,$form1))
#        {
#            array_push($output,array( 0 => $hiatus_form , 1 => strpos($mot_special,$form1)+1 , 2 => strpos($mot_special,$form1)  + mb_strlen($hiatus_form) )) ;
#        }
#        else if(strstr($mot_special,$form2))
#        {
#            array_push($output,array( 0 => $hiatus_form , 1 => strpos($mot_special,$form2) , 2 => strpos($mot_special,$form2)  + mb_strlen($hiatus_form) )) ;
#        }
#        else
#        {
#            array_push($output,array( 0 => $hiatus_form , 1 => strpos($mot_special,$form3)+1 , 2 => strpos($mot_special,$form3)  + mb_strlen($hiatus_form) )) ;
#        }
#    }
#    return $output;    
#}


#nouvelle version
function hiatus_mot($texte)
{
    global $hiatus_preg;
    $texte=enleve_exception($texte);
    #echo $hiatus_preg;
    #echo $texte;
    preg_match_all("/".$hiatus_preg."/",$texte,$matches);
#    print_r($matches);
    if(empty($matches))
    {
        return NULL;
    }
    $output=array(array());
    unset($output[0]);
    foreach($matches[0] as $hiatus_form)
    {
        $pos=mb_strpos($texte,$hiatus_form);
        $len=mb_strlen($hiatus_form);
        array_push($output,array( 0 => $hiatus_form , 1 => $pos , 2 => $pos+ $len-1)) ;
        
    }
    return $output;
    
}


#on recopie get_mot 


#structure de output vers_n => syllabe_n => array(mot, index_syllabe_debut_mot)
function get_array_info_syllabe_mot($texte_class)
{
    $output=array(array());
    $vers_n=0;
    $syllabe_n=0;
    $indice_debut=0;
    $indice_fin=0;
    $mot_tmp="";
    $index_syllabe_debut_mot=0;
    foreach($texte_class->vers as $vers)
    {
        $syllabe_n=0;
        foreach($vers->syllabes as $syllabe)
        {
            if(!empty($syllabe->syllabic))
            {
                if($syllabe->syllabic=="single")
                {
                    $output[$vers_n][$syllabe_n]=array($syllabe->text,$syllabe_n);
                    $indice_debut=$syllabe_n +1 ;
                    $indice_fin=$syllabe_n + 1;
                    $mot_tmp="";
                }
                else if ($syllabe->syllabic=="begin")
                {
                    $mot_tmp.=$syllabe->text;
                    $index_syllabe_debut_mot=$syllabe_n;
                    $indice_fin=$syllabe_n +1;
                    $indice_debut=$syllabe_n  ;
                }
                else if ($syllabe->syllabic=="end")
                {
                    $mot_tmp.=$syllabe->text;    
                     for($i=$indice_debut ; $i <=$indice_fin ; $i++ )
                     {
                        $output[$vers_n][$i]=array($mot_tmp,$index_syllabe_debut_mot);
                     } 
                     $mot_tmp="";
                     $index_syllabe_debut_mot=$syllabe_n+1;
                }
                else
                {
                    $mot_tmp.=$syllabe->text;
                    $indice_fin=$syllabe_n +1;
                }
            }
            
            $syllabe_n+=1;
        }
        $vers_n+=1;
    }
    return $output;
}


function get_syllabe($psaume,$vers_n,$syllabe_n)
{
	if(isset($psaume->vers[$vers_n]))
	{
		if(isset($psaume->vers[$vers_n]->syllabes[$syllabe_n]))
		{
			return $psaume->vers[$vers_n]->syllabes[$syllabe_n];
		}
	}
    return "";
}






function count_nb_chars($texte_class,$vers_n,$syllabe_debut,$syllabe_fin)
{
    $count=0;
    for($i=$syllabe_debut;$i<=$syllabe_fin;$i++)
    {
        $syllabe=get_syllabe($texte_class,$vers_n,$i);
        if(!empty($syllabe))
        {
        	$count+=mb_strlen($syllabe->text);
        }
    }
    return $count;
}




function update_hiatus($texte_class)
{
    
    $tableau_syllabe_mot=get_array_info_syllabe_mot($texte_class);
#    print_r($tableau_syllabe_mot);
    $vers_n=0;
    $syllabe_n=0;
    
    foreach($texte_class->vers as $vers)
    {
        $syllabe_n=0;
        foreach($vers->syllabes as $syllabe)
        {

#structure de tableau syllabe_mot vers_n => syllabe_n => array(mot, index_syllabe_debut_mot)
            if(!isset($tableau_syllabe_mot[$vers_n]))
            {
            	$tableau_syllabe_mot[$vers_n]=array(array());
            }
            if(!isset($tableau_syllabe_mot[$vers_n][$syllabe_n]))
            {
            	$tableau_syllabe_mot[$vers_n][$syllabe_n]=array_fill(0,2,"");
            }
            
            $mot=$tableau_syllabe_mot[$vers_n][$syllabe_n][0];
#            echo $mot;
            $syllabe_debut_mot=$tableau_syllabe_mot[$vers_n][$syllabe_n][1];
            $hiatus_tableau = hiatus_mot($mot);
#            avec le tableau c'est presque gagné
#            tableau_hiatus  : [ [hiatus, debut, fin ] ]
            $nb_char=0; //  nombre de caractères à retrancher pour avoir la distance relative à l'intérieur de la syllabe considérée
            if($syllabe_n != 0)
            {
                $nb_char=count_nb_chars($texte_class, $vers_n ,$syllabe_debut_mot, $syllabe_n - 1);
                                         
            }
#            nb_char: le nombre de caractere qu'il y a avant cette syllabe
#            nota bene : le premier caractere de la syllabe a pour indice nb_char
#            et le dernier caractere est a pour indice nb_char_syllabe_b+ nb_char -1
            if (!empty($syllabe->text))
            {
                $nb_char_syllabe_n=mb_strlen($syllabe->text);
            }

#            print_r($hiatus_tableau);
            foreach ($hiatus_tableau as $hiatus ) 
            {
#                hiatus : [forme, debut/ mot , fin / mot]
                $debut_hiatus_mot=$hiatus[1];
                $fin_hiatus_mot = $hiatus[2];
#                si le hiatus(potentiel) est dans une seule syllabe
#                var $is_hiatus;
#                var $start_hiatus;
#                var $end_hiatus;
#                var $start_pos;
#                var $end_pos;

#                ici le hiatus ne déborde pas
                if(($debut_hiatus_mot >= $nb_char ) && ($fin_hiatus_mot <= $nb_char_syllabe_n + $nb_char - 1) && ($fin_hiatus_mot >= $nb_char) && ($debut_hiatus_mot <= $nb_char + $nb_char_syllabe_n - 1))
                {
                    $syllabe->is_hiatus=TRUE;
                    $syllabe->start_hiatus=TRUE;
                    $syllabe->end_hiatus=TRUE;
                    $syllabe->hiatus_string=$hiatus[0];
                    $syllabe->start_pos= $debut_hiatus_mot - $nb_char; // vaut 0 quand debut_hiatus_mot = nb_char 
                    $syllabe->end_pos=$fin_hiatus_mot - $nb_char;
                    $syllabe->hiatus_form=mb_substr($mot, $debut_hiatus_mot, $fin_hiatus_mot - $debut_hiatus_mot+1);
                } 
#                débordement sur la syllabe d'avant
                else if (($debut_hiatus_mot < $nb_char)&& ($fin_hiatus_mot <= $nb_char_syllabe_n + $nb_char - 1) && ($fin_hiatus_mot >= $nb_char))
                {
                    $syllabe->is_hiatus=TRUE;
                    $syllabe->start_hiatus=FALSE;
                    $syllabe->end_hiatus=TRUE;
                    $syllabe->hiatus_string=$hiatus[0];
                    $syllabe->end_pos=$fin_hiatus_mot - $nb_char;
                    $syllabe->hiatus_form=mb_substr($mot, $debut_hiatus_mot, $nb_char  - $debut_hiatus_mot). "/" . mb_substr($mot,$nb_char, $fin_hiatus_mot - $nb_char + 1);
                }
#                débordement sur la syllabe d'après
                else if(($debut_hiatus_mot >= $nb_char ) && ($fin_hiatus_mot > $nb_char_syllabe_n + $nb_char - 1)&& ($debut_hiatus_mot <= $nb_char+ $nb_char_syllabe_n -1) )
                {
                    $syllabe->is_hiatus=TRUE;
                    $syllabe->start_hiatus=TRUE;
                    $syllabe->end_hiatus=FALSE;
                    $syllabe->hiatus_string=$hiatus[0];
                    $syllabe->start_pos= $debut_hiatus_mot - $nb_char; // vaut 0 quand debut_hiatus_mot = nb_char 
                    //$syllabe->end_pos=$fin_hiatus_mot - $nb_char;
                    $syllabe->hiatus_form=mb_substr($mot, $debut_hiatus_mot, $nb_char_syllabe_n+$nb_char - $debut_hiatus_mot). "/" . mb_substr($mot, $nb_char + $nb_char_syllabe_n, $fin_hiatus_mot - $nb_char - $nb_char_syllabe_n + 1);
                }
                
                
            }
            $syllabe_n+=1;
        }
        $vers_n+=1;
    }
}


$correspondances_entier_forme=array( 1 => "monosyllabe",
                                    2 => "disyllabe",
                                    3 => "trisyllabe",
                                    4 => "tetrasyllabe",
                                    5 => "pentasyllabe",
                                    6 => "hexasyllabe",
                                    7 => "heptasyllabe",
                                    8 => "octasyllabe",
                                    9 => "ennéasyllabe",
                                    10=> "distique", 11=> "hendécasyllabe",12 => "alexandrin" );
                                    

function update_updated_vars($id)
{
    $req=requete("SELECT * FROM table_matieres WHERE id_piece='".$id."'");
        
    while($response=fetch_array($req))
    {
        $id_recueil=$response["id_recueil"];    	
       //now update updated variables
		requete("UPDATE recueils SET updated='0' WHERE id_recueil='".$id_recueil."'");
		//update all the bases
		$req2=requete("SELECT tm.id_recueil,r.id_base FROM table_matieres tm 
				INNER JOIN recueils r ON r.id_recueil=tm.id_recueil 
				WHERE tm.id_piece='$id'");
		while($response2=fetch_array($req2))
		{
			$id_rec=$response2["id_recueil"];
			$id_b=$response2["id_base"];
			requete("UPDATE recueils SET updated='0' WHERE id_recueil='".$id_rec."'");
			requete("UPDATE bases SET updated='0' WHERE id_base='".$id_b."'");
		}
		
	}	
		
}



class psaume_texte_class
{
    var $vers ; // tableau contenant des vers_class
    var $forme; // taille des vers : alexandrin /distique / ...
    var $nb_syllabes=0;
    function psaume_texte_class($filename)
    {
       
        $xml=new music_xml_class($filename);
        $this->vers=array();
        $vers_courant=new vers_class();
        $count=0;
        $last_duration=0; // duration of the last syllabe
        $duration=0; // duration of the current syllabe
        foreach($xml->parts as $part)
        {
            foreach($part->measures as $measure)
            {
                    //note_class
                    foreach($measure->notes as $note)
                    {
                        if(!$note->is_rest())
                        {
                            $duration_note=$note->duration;
                            if(isset($note->lyric))
                            {
#                                we passed on another note                                
                                $last_duration=$duration;
                            	$syllabic=$note->lyric->syllabic;
                            	$text=$note->lyric->text;
                            	
                            	if((preg_match("/^([0-9]*\.)/",$text)))
                            	{
                            	    if(($count!=0)&&(count($vers_courant->syllabes)!=0))
                            	    {
                            	        $laxt_duration=0;
                                	    array_push($this->vers,$vers_courant);
                                        $vers_courant=new vers_class();
                            	    }
                            	    
                                    $count=$count+1;
                            	}
#                                 echo "<! --";
    #                            echo $last_duration." \n";
    #                            echo $duration;
                                if($duration_note>$last_duration)
                                {
    #                                echo "\nlongue-->";
                                    $vers_courant->ajouter_syllabe($text,$syllabic,TRUE,FALSE,FALSE);
                                }
                                else
                                {
    #                                echo "\n breve -->";
                                    $vers_courant->ajouter_syllabe($text,$syllabic,FALSE,FALSE,FALSE);
                                }
#                                $last_duration=$duration_note;
                                $duration=$duration_note;
                            } 
    #                       now if the last syllabe is held on multiple notes
                            else
                            {
                                $duration+=$duration_note;
                                if($duration>$last_duration)
                                {
#                                    change the accent
                                    if (count($vers_courant->syllabes)!=0)
                                    {
                                        $vers_courant->syllabes[count($vers_courant->syllabes) -1]->accent=TRUE;                                    
                                    }

                                }
                            }
#                           
                        }
                        else 
                        {
                            $last_duration=0;
                            $duration=0;
                            if(count($vers_courant->syllabes)!=0)
                            {
                                array_push($this->vers,$vers_courant);
                                $vers_courant=new vers_class();
                            }
                            
                        }
                    }
            }
            
            
            // indique la taille des vers
            global $correspondances_entier_forme;
            $nb=count($this->vers[0]->syllabes);
            if ($nb <= 12)
            {
                $this->forme=$correspondances_entier_forme[$nb];
            }
            else
            {
                $this->forme=$nb." syllabes";
            }
            
        }
        if(count($vers_courant->syllabes)!=0)
        {
        array_push($this->vers,$vers_courant);
        }
        
        // now counting the total number of syllabes
        foreach($this->vers as $vers)
        {
            $this->nb_syllabes+=count($vers->syllabes);
        }
        update_hiatus($this);
    }
    
    
    function output_raw_text()
    {
        $lyric="";
        foreach($this->vers as $vers)
        {
            foreach($vers->syllabes as $syllabe)
            {
                $lyric.=$syllabe->text.$syllabe->ponctuation;
                if(($syllabe->syllabic=="single")||($syllabe->syllabic=="end"))
                {
                    $lyric.=" ";
                }
            }
            $lyric.="\n";
        }
        return $lyric;
    }
    
    
    
    
    
    
    
    
    
#    
#    function update_syllabe($vers_n,$syllabe_n,$accent)
#    {
#        $syllabe=$this->vers[$vers_n]->syllabes[$syllabe_n];
#        if($accent=="longue")
#        {
#            $accent=TRUE;
#        }
#        else
#        {
#            $accent=FALSE;
#        }
#        $syllabe->accent=$accent;
#    }
}


class vers_class
{
    var $syllabes;
    function vers_class()
    {
        $this->syllabes=array();
    }
    
    function ajouter_syllabe($text,$syllabic,$accent=FALSE,$h=FALSE,$ent=FALSE)
    {
        array_push($this->syllabes,new syllabe_class($text,$syllabic,$accent,$h,$ent));
    }
    
}

class syllabe_class
{
    var $text;
    var $ponctuation;
    var $accent;
    var $is_h;
    var $is_ent;
    var $h;
    var $ent; // dit si le e final est grave ou non
    var $syllabic=""; // vient du musicxml single ou end
    var $is_hiatus=FALSE;
    var $start_hiatus=FALSE;
    var $end_hiatus=FALSE;
    var $hiatus_form="";
    var $hiatus_string=""; // the string is uy for cestuyla but the form might be u/y or uy/ (the form specifies how it is cut)
    var $start_pos; // these variables are like in strings (not real number ) starting from 0 to n-1 where n is the strlen
    var $end_pos;
    
    
    function syllabe_class($text='',$syllabic="single",$accent=FALSE,$h=FALSE,$ent=FALSE)
    {
        $this->text=preg_replace("/[0-9]*\./","",$text);
        $this->ponctuation=preg_replace("/^[a-zA-Z0-9&\'éçàâôêè]*/","",$this->text);
        
        $this->text=preg_replace("/[\.\?\!,:]/","",$this->text);
        $this->accent=$accent;
        $this->h=$h;
        
        $this->ent=$ent;
        $this->syllabic=$syllabic;
        if(preg_match("/^[H|h]/",$text)&&(($syllabic=="single")||($syllabic!="end")))
        {
            $this->is_h=TRUE;
        }
        else
        {
            $this->is_h=FALSE;
        }
        if(preg_match("/(ent)$/",$text)&&(($syllabic=="single")||($syllabic=="end")))
        {
            $this->is_ent=TRUE;
        }
        else
        {
            $this->is_ent=FALSE;
        }
    }
    
   
}

function html_output_hiatus_preprocessing($syllabe)
{
	$debut="";
	$fin="";
	$milieu="";
    if($syllabe->is_hiatus)
    {
        if($syllabe->start_hiatus && $syllabe->end_hiatus)
        {
            $start=$syllabe->start_pos;
            $end=$syllabe->end_pos;
            $len=$end -$start +1;
            if($start!=0)
            {
                $debut=mb_substr($syllabe->text,0,$start);
            }
            if($end!= (mb_strlen($syllabe->text)-1))
            {
                $fin=mb_substr($syllabe->text,$end+1,mb_strlen($syllabe->text)-$end);
            }
            $milieu=mb_substr($syllabe->text,$start,$len);
            return htmlspecialchars($debut)."<i>".htmlspecialchars($milieu)."</i>".htmlspecialchars($fin);
            
        }
        else if(!$syllabe->start_hiatus && $syllabe->end_hiatus)
        {
            $start=0;
            $end=$syllabe->end_pos;
            $len=$end -$start +1;
            if($start!=0)
            {
                $debut=mb_substr($syllabe->text,0,$start);
            }
            if($end!= (mb_strlen($syllabe->text)-1))
            {
                $fin=mb_substr($syllabe->text,$end+1,mb_strlen($syllabe->text)-$end);
            }
            $milieu=mb_substr($syllabe->text,$start,$len);
            return htmlspecialchars($debut)."<b>".htmlspecialchars($milieu)."</b>".htmlspecialchars($fin);
            
        }
        else if($syllabe->start_hiatus && !$syllabe->end_hiatus)
        {
            $start=$syllabe->start_pos;
            $end=mb_strlen($syllabe->text)-1;
            $len=$end -$start +1;
            if($start!=0)
            {
                $debut=mb_substr($syllabe->text,0,$start);
            }
            if($end!= (mb_strlen($syllabe->text)-1))
            {
                $fin=mb_substr($syllabe->text,$end+1,mb_strlen($syllabe->text)-$end);
            }
            $milieu=mb_substr($syllabe->text,$start,$len);
            return htmlspecialchars($debut)."<i>".htmlspecialchars($milieu)."</i>".htmlspecialchars($fin);
           
        }
        
    }
    return $syllabe->text;
}

//attention au mots entrainent , enthousiasme
function html_output_syllabe($syllabe)
{
    $text=html_output_hiatus_preprocessing($syllabe);
    $lyric="";
    if($syllabe->ent)
    {
        $ent_class="ent_grave";
        $ent_bulle="Grave";
    }
    else
    {
        $ent_class="ent_non_grave";
        $ent_bulle="Non Grave";
    }
    if($syllabe->accent)
    {

        if(!$syllabe->is_h)
        {
            $lyric.="<span class='longue'>";
            $lyric.=$text;
        }
        else
        {
            if($syllabe->h)
            {
                $lyric.=preg_replace("/^([H|h])/","<span class='h_aspire'>$1<span class='bulle'>Aspiré</span></span><span class='longue'>",$text);
            }
            else
            {
                $lyric.=preg_replace("/^([H|h])/","<span class='h_non_aspire'>$1<span class='bulle'>Non Aspiré</span></span><span class='longue'>",$text);    
            }
        }
        if(!$syllabe->is_ent)
        {
            $lyric.="<span class='bulle'>Longue</span></span>";
        }
        else
        {
            
            $lyric=preg_replace("/(ent)$/","<span class='bulle'>Longue</span></span><span class='$ent_class'>$1<span class='bulle'>$ent_bulle</span></span>",$lyric);
        }
        
    }
    else
    {

        if(!$syllabe->is_h)
        {
            $lyric.="<span class='breve'>";
            $lyric.=$text;
        }
        else
        {
            if($syllabe->h)
            {
                $lyric.=preg_replace("/^([H|h])/","<span class='h_aspire'>$1<span class='bulle'>Aspiré</span></span><span class='breve'>",$text);
            }
            else
            {
                $lyric.=preg_replace("/^([H|h])/","<span class='h_non_aspire'>$1<span class='bulle'>Non Aspiré</span></span><span class='breve'>",$text);    
            }
        }
        if(!$syllabe->is_ent)
        {
            $lyric.="<span class='bulle'>Brève</span></span>";
        }
        else
        {
            
            $lyric=preg_replace("/(ent)$/","<span class='bulle'>Brève</span></span><span class='$ent_class'>$1<span class='bulle'>$ent_bulle</span></span>",$lyric);
        }
    }
    
    if(($syllabe->syllabic=="single")||($syllabe->syllabic=="end"))
    {
        $lyric.=" ";
    }  
    return $lyric.$syllabe->ponctuation;
}



function html_output_form_syllabe($syllabe,$vers_n,$syllabe_n)
{
    // this variable counts number of things (h aspire , ent , etc ...) if count is equal to one , we do nothing
    // but do the usual thing 
    // if count is > 1 then we have to ouput a box
#    $dialog_form="";
    $count=1;
	$lyric="";
	if($syllabe->is_h)
	{
        $count+=1;
	}
	if($syllabe->is_ent)
	{
	    $count+=1;
	}
    if($syllabe->h)
    {
        $h_class="h_aspire";
        $h_bulle="Aspiré";
        $h_value="aspire";
    }
    else
    {
        $h_class="h_non_aspire";
        $h_bulle="Non Aspiré";
        $h_value="non_aspire";
    }
    
    
    if($syllabe->ent)
    {
        $ent_class="ent_grave";
        $ent_value="grave";
        $ent_bulle="Grave";
       
    }
    else
    {
        $ent_class="ent_non_grave";
        $ent_value="non_grave";
        $ent_bulle="Non Grave";
    }
    // the good case
    if ($count==1)
    {
        if($syllabe->accent)
        {
            $lyric.="<span class='longue' id='accent_help_".$vers_n.",".$syllabe_n."' onclick='change_accent(this,".$vers_n.",".$syllabe_n.")'><input type='hidden' value='{$syllabe->text}'/><input type='hidden' id='accent_".$vers_n."_".$syllabe_n."' name='accent_".$vers_n."_".$syllabe_n."' value='longue'/>";
            $lyric.=htmlspecialchars($syllabe->text);
            $lyric.="<span class='bulle' id='help_{$vers_n}_{$syllabe_n}'>Longue</span></span>".$syllabe->ponctuation;
        }
        else
        {
            $lyric.="<span class='breve' onclick='change_accent(this,".$vers_n.",".$syllabe_n.")'><input type='hidden' value='{$syllabe->text}'/><input type='hidden' id='accent_".$vers_n."_".$syllabe_n."' name='accent_".$vers_n."_".$syllabe_n."' value='breve'/>";
            $lyric.=htmlspecialchars($syllabe->text);
            $lyric.="<span class='bulle' id='help_{$vers_n}_{$syllabe_n}'>Brève</span></span>".$syllabe->ponctuation;
        }
    }
    else
    {
#        here computing the bad case
        if($syllabe->accent)
        {
            $lyric.="<span class='longue syllabic_special' id='accent_help_".$vers_n."_".$syllabe_n."'><input type='hidden' name='syllabe_".$vers_n."_".$syllabe_n."' value='{$syllabe->text}'/><input type='hidden' id='accent_".$vers_n."_".$syllabe_n."' name='accent_".$vers_n."_".$syllabe_n."' value='longue'/>";
            $lyric.="<span class='bulle' id='help_{$vers_n}_{$syllabe_n}'>Longue</span>";
        }
        else
        {
            $lyric.="<span class='breve syllabic_special' id='accent_help_".$vers_n."_".$syllabe_n."'><input type='hidden' name='syllabe_".$vers_n."_".$syllabe_n."'  value='{$syllabe->text}'/><input type='hidden' id='accent_".$vers_n."_".$syllabe_n."' name='accent_".$vers_n."_".$syllabe_n."' value='breve'/>";
            $lyric.="<span class='bulle' id='help_{$vers_n}_{$syllabe_n}'>Brève</span>";
            
        }
        $string="";
#        we reinit the dialog form (so that it is non empty on this case only)
#         $dialog_form="
#         <div class='dialog_form' id='dialog_form_{$vers_n}_{$syllabe_n}'>

#                <table>
#                <tr>
#                    <th>Syllabe :</th>
#                    <td>".htmlspecialchars($syllabe->text)."</td>
#                </tr>
#                <tr>
#                    <th>Accent :</th>
#                    <td>
#                        <input type='radio' name='accent_{$vers_n}_{$syllabe_n}' value='longue' ".(($syllabe->accent)?("checked"):(""))."/> Long      
#                        <input type='radio' name='accent' value='breve' ".((!$syllabe->accent)?("checked"):(""))."/> Bref
#                    </td>
#               </tr>
#                ";
         if($syllabe->is_h)
         {
#            $dialog_form.="<tr>
#                    <th>H aspiré :</th>
#                    <td>
#                        <input type='radio' name='aspire' value='aspire' ".(($syllabe->h)?("checked"):(""))."/> Aspiré      
#                        <input type='radio' name='aspire' value='non_aspire' ".((!$syllabe->h)?("checked"):(""))."/> Non Aspiré
#                    </td>
#               </tr>";
            $lyric.="<input type='hidden' id='aspire_".$vers_n."_".$syllabe_n."' name='aspire_".$vers_n."_".$syllabe_n."' value='$h_value'/>";
            $string=preg_replace("/^([H|h])/","<span id='aspire_first_class_{$vers_n}_{$syllabe_n}' class='$h_class'>$1<span class='bulle' id='aspire_help_{$vers_n}_{$syllabe_n}'>$h_bulle</span></span>",htmlspecialchars($syllabe->text));;
            
            
         }
         else
         {
            $string=htmlspecialchars($syllabe->text);
         }
         if($syllabe->is_ent)
         {       
#               $dialog_form.="
#               <tr>     
#                    <th>Ent :</th>
#                    <td>
#                        <input type='radio' name='ent' value='grave' ".(($syllabe->ent)?("checked"):(""))."/> Grave     
#                        <input type='radio' name='ent' value='non_grave' ".((!$syllabe->ent)?("checked"):(""))."/> Non Grave
#                    </td>
#               </tr>";
               $lyric.="<input type='hidden' id='ent_".$vers_n."_".$syllabe_n."' name='ent_".$vers_n."_".$syllabe_n."' value='$ent_value'/>";
               $string=preg_replace("/(ent)$/","<span id='ent_first_class_{$vers_n}_{$syllabe_n}' class='$ent_class'>$1<span id='ent_help_{$vers_n}_{$syllabe_n}' class='bulle'>$ent_bulle</span></span>",$string);    
         }
                
                
#        $dialog_form.="

#            </table>

#        </div>";
        $lyric.=$string."</span>".$syllabe->ponctuation;
                   
    }
    if(($syllabe->syllabic=="single")||($syllabe->syllabic=="end"))
    {
        $lyric.=" ";
    }
    
#    return array($lyric,$dialog_form);
    return $lyric;
}


function html_form_output($texte)
{
    $lyric="";
    $vers_n=0;
    $syllabe_n=0;
    ?>
    <script language="Javascript" type="text/javascript"><!--
    
    function change_ent(span,vers,syllabe)
    {
        var classe=span.getAttribute('class');
        if(classe=='ent_grave')
        {
            span.setAttribute('class','ent_non_grave');   
        }
        else
        {
            span.setAttribute('class','ent_grave');
        }
        var input=document.getElementById('ent_'+vers+'_'+syllabe);
        if(input.value=='grave')
        {
            input.value='non_grave';
        }
        else
        {
            input.value='grave';
        }
        
        var help=document.getElementById('ent_help_'+vers+'_'+syllabe);
        if(help.innerHTML=='grave')
        {

            help.innerHTML='non grave';
        }
        else
        {
            help.innerHTML='grave';
        }
    }
    
    
    function change_h(span,vers,syllabe)
    {
        var classe=span.getAttribute('class');
        if(classe=='h_aspire')
        {
            span.setAttribute('class','h_non_aspire');   
        }
        else
        {
            span.setAttribute('class','h_aspire');
        }
        var input=document.getElementById('aspire_'+vers+'_'+syllabe);
        if(input.value=='aspire')
        {
            input.value='non_aspire';
        }
        else
        {
            input.value='aspire';
        }
        
        var help=document.getElementById('aspire_help_'+vers+'_'+syllabe);
        if(help.innerHTML=='aspire')
        {

            help.innerHTML='non aspire';
        }
        else
        {
            help.innerHTML='aspire';
        }
    }
    
    
    
    function change_accent(span,vers,syllabe)
    {
        
        var classe=span.getAttribute('class');
        var help=document.getElementById('help_'+vers+'_'+syllabe);
        if(classe=='longue')
        {
            span.setAttribute('class','breve');
            help.innerHTML='Brève';
        }
        else
        {
            span.setAttribute('class','longue');
            help.innerHTML='Longue';
        }
        
        var input=document.getElementById('accent_'+vers+'_'+syllabe);
        if(input.value=='longue')
        {
            input.value='breve';
        }
        else
        {
            input.value='longue';
        }
        

        if(help.innerHTML=='Longue')
        {

            
        }
        else
        {

        }
    }
    --></script>
    <?php
#    $dialog_form="";
    foreach($texte->vers as $vers)
    {
        foreach($vers->syllabes as $syllabe)
        {
#            $tableau=html_output_form_syllabe($syllabe,$vers_n,$syllabe_n);
#            $lyric.=$tableau[0];
#            $dialog_form.=$tableau[1];
            $lyric.=html_output_form_syllabe($syllabe,$vers_n,$syllabe_n);
            $syllabe_n+=1;
        }
        $lyric.="<br/>";
        $vers_n+=1;
        $syllabe_n=0;
    }
#    return array($lyric,$dialog_form);
    return $lyric;
}


function html_output($texte)
{
    $lyric="";
    foreach($texte->vers as $vers)
    {
        foreach($vers->syllabes as $syllabe)
        {
            $lyric.=html_output_syllabe($syllabe);
        }
        $lyric.="<br/>";
    }
    return $lyric;
}


function output_vers($texte,$nb)
{
    $lyric="";
    $vers=$texte->vers[$nb];
    foreach($vers->syllabes as $syllabe)
    {
        
        
        $lyric.=html_output_syllabe($syllabe);
    }
    return $lyric;
}

$css_virga="<link href='./jquery/themes/base/jquery.ui.all.css' rel='stylesheet' type='text/css' /> ";

$jquery_virga="<script type='text/javascript' src='./jquery/jquery.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.core.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.widget.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.draggable.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.mouse.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.sortable.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.position.js'></script>
        	<script type='text/javascript'  src='./jquery/ui/jquery.ui.dialog.js'></script>
        <script type='text/javascript' ><!--
        	        \$(function() {
        \$('#dialog_box').dialog({
            title: 'Modification',
            autoOpen: false,
            width: 400,
            modal: true,
            buttons: {
                'Valider': function (event,ui){
                    validate(\$(this));
                }
            }
            });
        \$('.syllabic_special').click(function() 
        {
            open_dialog(this);
        });    

function open_dialog(\$span)
{
    var l=\$('input[type=hidden]', \$span).length;
    if(l!=0)
    {
    var elem=\$('input[type=hidden]', \$span)[0];
    var name=\$(elem).attr('name');   
    var tableau=name.split('_');
    var vers=tableau[1];
    var syllabe=tableau[2];
    var dialog_box=\$('#dialog_box');
    \$('input[name=localisation]',dialog_box).removeAttr('value').attr('value',vers+'_'+syllabe);


    
    var syllabe_text=\$(elem).attr('value');
        
    \$('#syllabe',dialog_box).empty().text(syllabe_text);
    
    var accent=\$('#accent_'+vers+'_'+syllabe);
    var aspire=\$('#aspire_'+vers+'_'+syllabe);
    var ent=\$('#ent_'+vers+'_'+syllabe);
    
    var accent_value=\$('#accent_'+vers+'_'+syllabe).attr('value');  
    
    
    var ent_row=\$('#ent_row');
    var aspire_row=\$('#aspire_row');
    var accent_row=\$('#accent_row');
    
    set_value(accent_row,accent_value);
    
    
    if (\$('#aspire_'+vers+'_'+syllabe).length !=0)
    {
        show_row('aspire_row');
        var aspire_value=\$(\$('#aspire_'+vers+'_'+syllabe)[0]).attr('value');
        set_value(aspire_row, aspire_value);
    }
    else
    {
        hide_row('aspire_row');
    }
    
    
    if (\$('#ent_'+vers+'_'+syllabe).length !=0)
    {
        show_row('ent_row');
        var ent_value=\$(\$('#ent_'+vers+'_'+syllabe)[0]).attr('value');
        set_value(ent_row,ent_value)
    }
    else
    {
        hide_row('ent_row');
    }
    
    \$('#dialog_box').dialog('open');
    }
}

function set_value(row,value)
{
    var tableau=\$('input',row);
    for (i=0 ; i< tableau.length; i++)
    {
        var input=tableau[i];
        var input_value=input.getAttribute('value');
        if(input_value== value)
        {
            if(!input.hasAttribute('checked'))
            {
            input.setAttribute('checked','checked');
            }
           
        }
        else
        {
            if(input.hasAttribute('checked'))
            {
            input.removeAttribute('checked');
            }
        }
    }
}


function validate(\$dialog_box)
{
    var l=\$('input[type=radio]:checked', \$dialog_box).length;
    var name=\$('input[name=localisation]',\$dialog_box).attr('value');
    
    
    var tableau=name.split('_');
    var vers=tableau[0];
    var syllabe=tableau[1];
    var span=\$('#accent_'+vers+'_'+syllabe).parent;
    
    for(i=0 ; i< l ; i++)
    {
        var elem=\$('input[type=radio]:checked',\$dialog_box)[i];
        var elem_name=\$(elem).attr('name');
        var value=\$(elem).attr('value');
        if(\$('#'+elem_name+'_'+vers+'_'+syllabe).length!=0)
        {
            \$('#'+elem_name+'_'+vers+'_'+syllabe).attr('value',value);
            if(elem_name =='aspire')
            {
                var aspire_class='h_non_aspire';
                var aspire_text='Non Aspiré'
                if(value=='aspire')
                {
                    aspire_class='h_aspire';
                    aspire_text='Aspiré';
                }
                \$('#aspire_help_'+vers+'_'+syllabe).empty().text(aspire_text);
                \$('#aspire_first_class_'+vers+'_'+syllabe).attr('class',aspire_class);
            }
            else if (elem_name=='accent')
            {
                var accent_text='Longue';
                if(value=='longue')
                {
                    accent_text='Brève';
                }
                \$('#accent_help_'+vers+'_'+syllabe).attr('class','syllabic_special '+value);
                \$('#help_'+vers+'_'+syllabe).empty().text(accent_text);
            }
            else if (elem_name=='ent')
            {
                var ent_text='Non Grave';
                if(value=='grave')
                {
                    ent_text='Grave';
                }
                \$('#ent_first_class_'+vers+'_'+syllabe).attr('class','ent_'+value);
                \$('#ent_help_'+vers+'_'+syllabe).empty().text(ent_text);
            }
        }
        

    }
    
    \$dialog_box.dialog('close');    
}

});
	


--></script>";



$dialog_box_virga="

<div id='dialog_box'>
<input type='hidden' name='localisation' value=''/>
<table>

    <tr>
        <th>Syllabe :</th>
        <td id='syllabe'></td>
        </tr>
    <tr id='accent_row'>
        <th>Accent :</th>
        <td>
            <input checked type='radio' name='accent' value='longue'/> Long      
            <input type='radio' name='accent' value='breve'/> Bref
        </td>
   </tr>
    <tr id='aspire_row'>
        <th>H aspire :</th>
        <td>
            <input checked type='radio' name='aspire' value='aspire'/> Oui      
            <input type='radio' name='aspire' value='non_aspire'/> Non
        </td>
   </tr>
   <tr id='ent_row'>     
        <th>Ent :</th>
        <td>
            <input type='radio' name='ent' value='grave'/> Grave     
            <input type='radio' name='ent' value='non_grave'/> Non Grave
        </td>
    </tr>
    </table>
    
</div>";

?>
