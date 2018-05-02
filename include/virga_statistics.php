<?php
#here we have all we need to do some statistics
require_once("include/mots.php");


class piece_statistics_class
{
    var $id_piece;
    var $db_mots;
    
    
    var $freqs_accents=array(array());
    var $freqs_accents_mus=array(array());
    var $freqs_h=array(array());
    var $freqs_h_mus=array(array());
    var $freqs_ent=array(array());
    var $freqs_ent_mus=array(array());
    
    
    var $freqs_hiatus=array(array());
    
    var $accents=array(array()); // array with accent (default accent_db) containing all info (number of real accent)
    var $accents_mus=array(array()); // from accent_mus_db
    var $nb_accents=array(array());
    var $nb_accents_mus=array(array());
    var $h_stats=array(array());
    var $nb_h_stats=array(array());
    var $h_mus_stats=array(array());
    var $nb_h_mus_stats=array(array());
    var $ent_stats=array(array());
    var $nb_ent_stats=array(array());
    var $hiatus_db_stats=array(array());
    var $nb_hiatus_db_stats=array();
    function piece_statistics_class($id_piece)
    {
        
        $this->db_mots=new db_mots_class($id_piece);
        $this->id_piece=$id_piece;
        
        $array=$this->get_statistics("accent_db","accent");
        $this->accents=$array[0];
        $this->nb_accents=$array[1];
        $this->freqs_accents=$this->get_freqs($this->accents, $this->nb_accents);
        
        
        $array=$this->get_statistics("accent_mus_db","accent");
        $this->accents_mus=$array[0];
        $this->nb_accents_mus=$array[1];
        $this->freqs_accents_mus=$this->get_freqs($this->accents_mus, $this->nb_accents_mus);
        
        $array=$this->get_statistics("h_db","aspire");
        $this->h_stats=$array[0];
        $this->nb_h_stats=$array[1];
        $this->freqs_h=$this->get_freqs($this->h_stats, $this->nb_h_stats);
        
        
#       nettoyage des champs qui ne servent à rien ( liés à php)        
        foreach ($this->freqs_h as $vers_n => $tableau)
        {
            foreach($tableau as $syllabe_n => $value)
            {
                if (!$this->db_mots->texte->vers[$vers_n]->syllabes[$syllabe_n]->is_h)
                {
                    unset($this->freqs_h[$vers_n][$syllabe_n]);
                }
            }
        }
        
        $array=$this->get_statistics("h_mus_db","aspire");
        $this->h_mus_stats=$array[0];
        $this->nb_h_mus_stats=$array[1];
        $this->freqs_h_mus=$this->get_freqs($this->h_mus_stats, $this->nb_h_mus_stats);       
        foreach ($this->freqs_h_mus as $vers_n => $tableau)
        {
            foreach($tableau as $syllabe_n => $value)
            {
                if (!$this->db_mots->texte->vers[$vers_n]->syllabes[$syllabe_n]->is_h)
                {
                    unset($this->freqs_h_mus[$vers_n][$syllabe_n]);
                }
            }
        }
        
        $array=$this->get_statistics("ent_db","grave");
        $this->ent_stats=$array[0];
        $this->nb_ent_stats=$array[1];
        $this->freqs_ent=$this->get_freqs($this->ent_stats, $this->nb_ent_stats);
        foreach ($this->freqs_ent as $vers_n => $tableau)
        {
            foreach($tableau as $syllabe_n => $value)
            {
                if (!$this->db_mots->texte->vers[$vers_n]->syllabes[$syllabe_n]->is_ent)
                {
                    unset($this->freqs_ent[$vers_n][$syllabe_n]);
                }
            }
        } 
         
            
        $array=$this->get_statistics("ent_mus_db","grave");
        $this->ent_mus_stats=$array[0];
        $this->nb_ent_mus_stats=$array[1];        
        $this->freqs_ent_mus=$this->get_freqs($this->ent_mus_stats, $this->nb_ent_mus_stats);          
        foreach ($this->freqs_ent_mus as $vers_n => $tableau)
        {
            foreach($tableau as $syllabe_n => $value)
            {
                if (!$this->db_mots->texte->vers[$vers_n]->syllabes[$syllabe_n]->is_ent)
                {
                    unset($this->freqs_ent_mus[$vers_n][$syllabe_n]);
                }
            }
        } 
        
        
        
#        now taking care of hiatus problems 
             
        $this->hiatus_statistics();
                
        
        
        return $this;      
    }
    
    
    
    function hiatus_statistics()
    {
        $id=$this->id_piece;
        $req=requete("SELECT DISTINCT a.mot,a.vers_n, a.syllabe_n , a.mot,a.hiatus_form,b.hiatus_form AS hiatus_form2, b.id_piece, 
                    b.vers_n AS vers2, b.syllabe_n AS syll2 
                    FROM hiatus_db a
                    LEFT OUTER JOIN hiatus_db b ON a.mot=b.mot
                    WHERE a.id_piece=$id
                    AND a.hiatus_string=b.hiatus_string
                    ORDER by a.vers_n, a.syllabe_n");
        $this->nb_hiatus_db_stats=array();
        $this->hiatus_db_stats=array();
        while($response=fetch_array($req))
        {
            $vers_n=$response['vers_n'];
            $syllabe_n=$response['syllabe_n'];
            $hiatus_form=$response['hiatus_form'];
            $hiatus_form2=$response['hiatus_form2'];
            $mot=$response['mot'];
            
            if(!isset($this->hiatus_db_stats[$mot]))
            {
                $this->hiatus_db_stats[$mot]=array();
                if(!isset($this->hiatus_db_stats[$mot][$hiatus_form]))
                {
                    $this->hiatus_db_stats[$mot][$hiatus_form]=0;
                }
                unset($this->hiatus_db_stats[$mot][0]);
                
            }
            if ( !isset($this->nb_hiatus_db_stats[$mot]) )
            {
                $this->nb_hiatus_db_stats[$mot]=0;
            }
            if(is_split_hiatus($hiatus_form2))
            {
                $this->nb_hiatus_db_stats[$mot]+=1/2;
            }
            else
            {
                $this->nb_hiatus_db_stats[$mot]+=1;
            }
            if ($hiatus_form==$hiatus_form2)
            {
                if(is_split_hiatus($hiatus_form))
                {
                    $this->hiatus_db_stats[$mot][$hiatus_form]+=1/2;
                }
                else
                {
                    $this->hiatus_db_stats[$mot][$hiatus_form]+=1;
                }
            }
        }
        foreach($this->hiatus_db_stats as $mot=> $tableau)
        {
            if (!isset($this->freqs_hiatus[$mot]))
            {
                $this->freqs_hiatus[$mot]=array();
            }
            if ($this->nb_hiatus_db_stats[$mot]!=0)
            {
                foreach($tableau as $hiatus_form=>$value)
                {
                    if (!isset($this->freqs_hiatus[$mot][$hiatus_form]))
                    {
                        $total_value=$this->nb_hiatus_db_stats[$mot];
                        $this->freqs_hiatus[$mot][$hiatus_form]=$value/$total_value * 100;
                    }
                }
            }
            
        }
        unset($this->freqs_hiatus[0]);
        unset($this->nb_hiatus_db_stats[0]); 
    }
    
    
    function get_freqs($array, $nb)
    {
        $output=array(array());
        $vers_n=0;
        $syllabe_n=0;
        while(($vers_n < count($array)) && ($vers_n < count($nb)) )
        {
            $syllabe_n=0;
            if(!isset($array[$vers_n]))
            {
                $array[$vers_n]=array();
            }
            if(!isset($nb[$vers_n]))
            {
                $nb[$vers_n]=array();
            }

            if(!isset($output[$vers_n]))
            {
                $output[$vers_n]=array();
            }
            while (( $syllabe_n < count($array[$vers_n]) ) && ($syllabe_n < count($nb[$vers_n])) )
            {
                if(!isset($array[$vers_n][$syllabe_n]))
                {
                    $array[$vers_n][$syllabe_n]=0;
                }
                if(!isset($nb[$vers_n][$syllabe_n]))
                {
                    $nb[$vers_n][$syllabe_n]=0;
                }
                if(!isset($output[$vers_n][$syllabe_n]))
                {
                    $output[$vers_n][$syllabe_n]="None";
                }
                if($nb[$vers_n][$syllabe_n] != 0)
                {
                    $output[$vers_n][$syllabe_n]= 100 * $array[$vers_n][$syllabe_n] / $nb[$vers_n][$syllabe_n];
                }
                $syllabe_n++;
            }
            $vers_n++;
        }
        return $output;
    }
    
    function get_statistics($db,$field_name)
    {
        
        
        $id_piece=$this->id_piece;
#        echo $db;
        
        $output=array(array());
        $nbs=array(array());
        $req=requete("SELECT a.vers_n, a.syllabe_n, b.$field_name  
                    FROM $db a
                    LEFT OUTER JOIN $db b ON b.mot=a.mot
                    WHERE a.id_piece='$id_piece'
                    AND b.syllabe=a.syllabe
                    ORDER BY a.vers_n, a.syllabe_n
                    ");
        while($response=fetch_array($req))
        {
            $real_vers_n=$response['vers_n'];
            $real_syllabe_n=$response['syllabe_n'];
            $vers_n=$real_vers_n-1;
            $syllabe_n=$real_syllabe_n -1;
            $value=$response[$field_name];
#            echo $vers_n . " ". $syllabe_n . '<br/>';
            if(!isset($output[$vers_n]))
            {
                $output[$vers_n]=array();
                
            }
            if(!isset($output[$vers_n][$syllabe_n]))
            {
                $output[$vers_n][$syllabe_n]=0;
            }
            if(!isset($nbs[$vers_n]))
            {
                $nbs[$vers_n]=array();
                
            }
            if(!isset($nbs[$vers_n][$syllabe_n]))
            {
                $nbs[$vers_n][$syllabe_n]=0;
            }
#            echo $vers_n . " " . $syllabe_n ."<br/>";
            $nbs[$vers_n][$syllabe_n]+=1;
            $output[$vers_n][$syllabe_n]+=$value;
        }
        return array($output,$nbs);
    }
}


#function recognize if the hiatus has a slash (cut) ou/ez
function is_split_hiatus($hiatus_form)
{
    if(mb_strpos($hiatus_form,'/')==FALSE)
    {
        return FALSE;
    }
    else
    {
        return TRUE;
    }
}

class word_statistics_class
{
    var $word;
    var $nb; 
    var $patterns=array();
    function word_statistics_class($word)
    {
        $this->word=$word;
        $this->patterns=array();
        if(get_occurences($word)==0)
        {
            // not found in db
            return $this;
        }
        $syllabes=do_syllabation($word);
        foreach($syllabes as $pattern)
        {
            $pattern_class=new pattern_class($pattern,$word);
            array_push($this->patterns,$pattern_class);
        }

        return $this;
    }
}

class pattern_class
{
    var $pattern; // syllabe string
    var $word;
    var $nbs; // total number of occurences of pattern in word in each database
    var $freqs; // frequencies of positive matches (example of accent in accent_db , aspire in h_db)
    var $is_h=FALSE;
    var $is_hiatus=FALSE;
    var $accent_freq=0;
    var $accent_mus_freq=0;
    
    var $is_ent=FALSE;
    var $h_mus=FALSE;
    function pattern_class($pattern, $word)
    {
        $this->pattern=$pattern;
        $this->word= $word;
        $dbs=array("accent_db", "accent_mus_db", "h_db", "h_mus_db", "ent_db", "ent_mus_db");
        
        foreach($dbs as $db)
        {
            $this->nbs[$db]= get_pattern_occurences($pattern,$word, $db);
        }
        if($this->nbs["h_db"]!= 0)
        {
            $this->is_h=TRUE;
        }
        
        unset($this->nbs[0]);
        foreach ($this->nbs as $db=> $nb)
        {
            if($nb!=0)
            {
                $this->freqs[$db]= 100* (get_info($pattern,$word,$db,TRUE )) / $nb;
            }
        }
        return $this;
    }
}



#using the database it is much easier
function do_syllabation($word)
{
    $out=array();
    $req=requete("SELECT DISTINCT syllabe FROM accent_db WHERE mot='$word'");
    while($response=fetch_array($req))  
    {
        array_push($out,$response['syllabe']);
    }
    return $out;
}

function get_info($pattern , $word, $db="accent_db", $value=FALSE)
{
    $int_value=0;
    if($value==TRUE)
    {
        $int_value=1;
    }
    $tableau=array( "accent_db" => "accent" , "accent_mus_db" => "accent",
                    "h_db" =>"aspire", "h_mus_db" => "aspire",
                    "ent_db" =>"grave", "ent_mus_db"=> "grave");
    $req=requete("SELECT count(mot) FROM $db WHERE syllabe='$pattern' AND mot='$word' AND {$tableau[$db]}='$int_value'");
    $response=fetch_array($req);
    
    return $response['count(mot)'];
}


function get_pattern_occurences($pattern, $word,$db="accent_db")
{
    $req=requete("SELECT count(mot) FROM $db WHERE mot='$word' AND syllabe='$pattern'");
    $response=fetch_array($req);
    return $response['count(mot)'];
}

function get_occurences($word,$db="accent_db")
{
    $req=requete("SELECT count(mot) FROM $db WHERE mot='$word'");
    $response=fetch_array($req);
    return $response['count(mot)'];
}
?>
