<?php
require_once("include/texte.php");
require_once("include/mysql.php");
#on suppose que les droits ont déjà été vérifiés






function convert_mot($string)
{
    return mysql_escape_string(preg_replace("/[0-9]*\./","",mb_strtolower($string,"UTF-8")));
}


class db_mots_class
{
    var $texte; // texte class
    var $id_piece;

    
    
    var $accent_db="accent_db";
    var $ent_db="ent_db";
    var $h_db="h_db";
    var $empty_xml=TRUE;
    function db_mots_class($id_piece,$option="texte")
    {
        if($option=="musique")
        {
            $this->accent_db="accent_mus_db";
            $this->ent_db="ent_mus_db";
            $this->h_db="h_mus_db";
        }
        $this->id_piece=$id_piece;
        $req=requete("SELECT psaume,fichier_xml FROM pieces WHERE id_piece='$id_piece'");
        $response=fetch_array($req);
        if($response["psaume"]==0)
        {
            return;
        }
        if(empty($response['fichier_xml']))
        {
            
            return $this;
        }
        $this->empty_xml=FALSE;
        $this->texte=new psaume_texte_class($response["fichier_xml"]);
        //print_r($this);

        $this->guess_all();
#        $req=requete("SELECT count(id_piece) FROM {$this->accent_db} WHERE id_piece='$id_piece'");
#        if(num_rows($req)==0)
#        {
#        	$this->update_db();
#        }
        
    }
    
    function has_entries()
    {
        $id_piece=$this->id_piece;
        $req=requete("SELECT count(id_piece) FROM {$this->accent_db} WHERE id_piece='$id_piece'");
        $response=fetch_array($req);
        return ($response['count(id_piece)']!=0);
    
    }
    
    function get_syllabe($vers_n,$syllabe_n)
    {
        if(($vers_n < count($this->texte->vers))&&($vers_n >=0))
        {
            if(($syllabe_n< count($this->texte->vers[$vers_n]->syllabes))&&($syllabe_n>=0))
            {
                return $this->texte->vers[$vers_n]->syllabes[$syllabe_n];   
            }    
            else 
            {
                return new syllabe_class();
            }
        }
        else { return new syllabe_class();}
        
    }
    
    
    function get_mot($vers_n,$syllabe_n)
    {
        $max=count($this->texte->vers[$vers_n]->syllabes);
        $syllabe=$this->get_syllabe($vers_n,$syllabe_n);
        if($syllabe==NULL)
        {
            return NULL;
        }
        
        if($syllabe->syllabic=="single")
        {
            return $syllabe->text;
        }
        else if ($syllabe->syllabic=="begin")
        {
            $text=$syllabe->text;
            $syllabe_n+=1;
            $syllabe=$this->get_syllabe($vers_n,$syllabe_n);
            while(($syllabe->syllabic!="end")&&($syllabe_n < $max))
            {
                $text.=$syllabe->text;
                $syllabe_n+=1;
                if($syllabe_n==$max)
                {
                    $syllabe=new syllabe_class('',"end");
                }
                else
                {
                    $syllabe=$this->get_syllabe($vers_n,$syllabe_n);
                }
            }
            if($syllabe_n != $max)
            {
            $text.=$syllabe->text;
            }
            return $text;
        }
        else if ($syllabe->syllabic=="end")
        {
            $debut="";
            $syllabe=$this->get_syllabe($vers_n,$syllabe_n);
            while(($syllabe->syllabic!="begin")&&($syllabe_n >=0))
            {
                $debut=$syllabe->text.$debut;
                $syllabe_n-=1;
                if($syllabe_n==-1)
                {
                    $syllabe=new syllabe_class();
                }
                else
                {
                    $syllabe=$this->get_syllabe($vers_n,$syllabe_n);
                }
                
            }
            $debut=$syllabe->text.$debut;
            return $debut;  
        }
        $syllabe_memoire=$syllabe_n;
#        syllabic=="middle"
        $text=$syllabe->text;
        $syllabe_n+=1;
        $syllabe=$this->get_syllabe($vers_n,$syllabe_n);
        while(($syllabe->syllabic!="end")&&($syllabe_n < $max))
        {
            $text.=$syllabe->text;
            $syllabe_n+=1;
            if($syllabe_n == $max)
            {
                $syllabe=new syllabe_class();
            }
            else
            {
                $syllabe=$this->get_syllabe($vers_n,$syllabe_n);
            }
            
        }
        $text.=$syllabe->text;
        
        $syllabe_n=$syllabe_memoire-1;
        $debut="";
        $syllabe=$this->get_syllabe($vers_n,$syllabe_n);
        while(($syllabe->syllabic!="begin")&&($syllabe_n >=0))
        {
            $debut=$syllabe->text.$debut;
            $syllabe_n-=1;
            if($syllabe_n==-1)
            {
                $syllabe= new syllabe_class();
            }
            else
            {
                $syllabe=$this->get_syllabe($vers_n,$syllabe_n);
            }
            
        }
        $debut=$syllabe->text.$debut;
        return $debut.$text;       
    }
    
    
    function update_db()
    {
        if ($this->empty_xml==TRUE)
        {
            return ;
        }     

        $accent_db=$this->accent_db;
        $h_db=$this->h_db;
        $real_syllabe_n=  1; 
        $real_vers_n =  1;
        $ent_db=$this->ent_db;
        $hiatus_db="hiatus_db";
        $vers_n=0;
        foreach($this->texte->vers as $vers)
        {
            $syllabe_n=0;
            foreach($vers->syllabes as $syllabe)
            {
                $mot=convert_mot($this->get_mot($vers_n,$syllabe_n));
                if (!empty($syllabe->text))
                {
                     $text=convert_mot($syllabe->text);
                }
#                echo $syllabe_n . $vers_n . "\n";
                $h=($syllabe->h)?(1):(0);
                $ent=($syllabe->ent)?(1):(0);
                //echo $mot;
                $accent=($syllabe->accent)?(1):(0);
                $start_hiatus=($syllabe->start_hiatus)?(1):(0);
                $end_hiatus=($syllabe->end_hiatus)?(1):(0);
                $start_pos=($syllabe->start_hiatus);
                $hiatus_form=$syllabe->hiatus_form;
                $hiatus_string=$syllabe->hiatus_string;
                $end_pos=$syllabe->end_pos;
                $real_syllabe_n= $syllabe_n + 1; 
                $real_vers_n = $vers_n + 1;
                $req=requete("SELECT * FROM $accent_db WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}'
                                AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
                if(num_rows($req)==0)
                {
                    requete("INSERT INTO $accent_db 
                            (`mot`,
                            `syllabe`,
                            `accent`,
                            `id_piece`,
                            `syllabe_n`,
                            `vers_n`
                            )
                            VALUES
                            ('$mot',
                            '$text',
                            '$accent',
                            '{$this->id_piece}',
                            '$real_syllabe_n',
                            '$real_vers_n')");
                }
                else
                {
                    requete("UPDATE $accent_db SET accent='$accent' WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}' AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
                } 
                
                if($syllabe->is_h)
                {
                    $req=requete("SELECT * FROM $h_db WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}'
                                AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
                    if(num_rows($req)==0)
                    {
                        requete("INSERT INTO $h_db 
                                (`mot`,
                                `syllabe`,
                                `aspire`,
                                `id_piece`,
		                        `syllabe_n`,
		                        `vers_n`
                                )
                                VALUES
                                ('$mot',
                                '$text',
                                '$h',
                                '{$this->id_piece}',
		                        '$real_syllabe_n',
		                        '$real_vers_n')");
                    }
                    else
                    {
                        requete("UPDATE $h_db SET aspire='$h' WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}'
                        			AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
                    }     
                }
                if($syllabe->is_ent)
                {
                    $req=requete("SELECT * FROM $ent_db WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}'
                                AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
                    if(num_rows($req)==0)
                    {
                        requete("INSERT INTO $ent_db 
                                (`mot`,
                                `syllabe`,
                                `grave`,
                                `id_piece`,
		                        `syllabe_n`,
		                        `vers_n`
                                )
                                VALUES
                                ('$mot',
                                '$text',
                                '$ent',
                                '{$this->id_piece}',
		                        '$real_syllabe_n',
		                        '$real_vers_n')
                        ");
                    }
                    else
                    {
                        requete("UPDATE $ent_db SET grave='$ent' WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}'
                        		AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
                    }
                }
                
                
                if($syllabe->is_hiatus)
                {
                    $req=requete("SELECT * FROM $hiatus_db WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}'
                                AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
                    if(num_rows($req)==0)
                    {
                        requete("INSERT INTO $hiatus_db 
                                (`mot`,
                                `syllabe`,
                                `start_hiatus`,
                                `end_hiatus`,
                                `start_pos`,
                                `end_pos`,
                                `id_piece`,
		                        `syllabe_n`,
		                        `vers_n`,
		                        `hiatus_form`,
		                        `hiatus_string`
                                )
                                VALUES
                                ('$mot',
                                '$text',
                                '$start_hiatus',
                                '$end_hiatus',
                                '$start_pos',
                                '$end_pos',
                                '{$this->id_piece}',
		                        '$real_syllabe_n',
		                        '$real_vers_n',
		                        '$hiatus_form',
		                        '$hiatus_string')
                        ");
                    }
                    else
                    {
                        requete("UPDATE $hiatus_db SET 
                                `start_hiatus`='$start_hiatus',
                                `end_hiatus`='$end_hiatus',
                                `start_pos`='$start_pos',
                                `end_pos`='$end_pos',
                                `hiatus_form`='$hiatus_form',
                                `hiatus_string`='$hiatus_string'
                                WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}'
                                AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
                    }
                }
                
                $syllabe_n++;
            }
            $vers_n++;
        }
        update_updated_vars($this->id_piece);
    }

    
    #fonction de rechange, moins couteuse en ressource sql
    function guess_all()
    {
#        init variables
        $accent_db=$this->accent_db;
        $h_db=$this->h_db;
        $ent_db=$this->ent_db;
        $hiatus_db="hiatus_db";
        
        $req_accent=requete("SELECT  a.mot, a.syllabe ,  a.vers_n ,a.syllabe_n, a.accent 
                            FROM $accent_db a 
                            WHERE  
                            a.id_piece='{$this->id_piece}' 
                            ORDER BY a.vers_n,a.syllabe_n;");        
        while($response=fetch_array($req_accent))
        {
            $real_vers_n=$response["vers_n"];
            $vers_n=$real_vers_n-1;
            $real_syllabe_n=$response["syllabe_n"];
            $syllabe_n=$real_syllabe_n-1;
            if($vers_n <  count($this->texte->vers))
            {
                if($syllabe_n < count($this->texte->vers[$vers_n]->syllabes))
                {
                    $this->texte->vers[$vers_n]->syllabes[$syllabe_n]->accent=($response['accent']==1)?(TRUE):(FALSE);
                                    
                }
            }

        }
        
        
        $req_h=requete("SELECT vers_n , syllabe_n , aspire 
                        FROM $h_db 
                        WHERE id_piece='{$this->id_piece}'
                        ORDER BY vers_n,syllabe_n");
        while($response=fetch_array($req_h))
        {
            $real_vers_n=$response["vers_n"];
            $vers_n=$real_vers_n-1;
            $real_syllabe_n=$response["syllabe_n"];
            $syllabe_n=$real_syllabe_n-1;
            if($vers_n <  count($this->texte->vers))
            {
                if($syllabe_n < count($this->texte->vers[$vers_n]->syllabes))
                {
                    $this->texte->vers[$vers_n]->syllabes[$syllabe_n]->h=($response['aspire']==1)?(TRUE):(FALSE);
                                    
                }
            }
        }                
        
        
        $req_ent=requete("SELECT vers_n , syllabe_n , grave
                        FROM $ent_db 
                        WHERE id_piece='{$this->id_piece}'
                        ORDER BY vers_n,syllabe_n");
        while($response=fetch_array($req_ent))
        {
            $real_vers_n=$response["vers_n"];
            $vers_n=$real_vers_n-1;
            $real_syllabe_n=$response["syllabe_n"];
            $syllabe_n=$real_syllabe_n-1;
            if($vers_n <  count($this->texte->vers))
            {
                if($syllabe_n < count($this->texte->vers[$vers_n]->syllabes))
                {
                    $this->texte->vers[$vers_n]->syllabes[$syllabe_n]->ent=($response['grave']==1)?(TRUE):(FALSE);
                                    
                }
            }
        }
        
        
                 
#        on s'occupe des hiatus
        $req_hiatus=requete("SELECT vers_n, syllabe_n,start_hiatus,end_hiatus,start_pos,end_pos
                            FROM $hiatus_db 
                            WHERE id_piece='{$this->id_piece}'
                            ORDER BY vers_n,syllabe_n");
                
        while($response=fetch_array($req_hiatus))
        {
            $real_vers_n=$response["vers_n"];
            $vers_n=$real_vers_n-1;
            $real_syllabe_n=$response["syllabe_n"];
            $syllabe_n=$real_syllabe_n-1;
            if($vers_n <  count($this->texte->vers))
            {
                if($syllabe_n < count($this->texte->vers[$vers_n]->syllabes))
                {
                    $this->texte->vers[$vers_n]->syllabes[$syllabe_n]->start_hiatus=($response['start_hiatus']==1)?(TRUE):(FALSE);
                    $this->texte->vers[$vers_n]->syllabes[$syllabe_n]->end_hiatus=($response['end_hiatus']==1)?(TRUE):(FALSE);
                    $this->texte->vers[$vers_n]->syllabes[$syllabe_n]->start_pos=($response['start_pos']);
                    $this->texte->vers[$vers_n]->syllabes[$syllabe_n]->end_pos=($response['end_pos']);
                                    
                }
            }
        }
    }
    
#    on essaie de deviner les prononciations des psaumes à partir des mots enregistrés dans la base de donnée

#    function guess_all()
#    {
#        //update_hiatus($this->texte);
#        $accent_db=$this->accent_db;
#        $h_db=$this->h_db;
#        $ent_db=$this->ent_db;
#        $hiatus_db="hiatus_db";
#        $real_syllabe_n=  1; 
#		$real_vers_n =  1;
#        $vers_n=0;
#        foreach($this->texte->vers as $vers)
#        {
#            //echo $vers_n;
#            $syllabe_n=0;
#            
#            foreach($vers->syllabes as $syllabe)
#            {
#                
#                $mot=convert_mot($this->get_mot($vers_n,$syllabe_n));
#                $text=convert_mot($syllabe->text);
#                $real_syllabe_n= $syllabe_n + 1; 
#                $real_vers_n = $vers_n + 1;
#                $h=FALSE;
#                $ent=FALSE;
#                $req=requete("SELECT * FROM $accent_db WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}'
#                                AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
#                if(num_rows($req)!=0)
#                {
#                    $response=fetch_array($req);
#                    $accent=($response["accent"]==1)?(TRUE):(FALSE);
#                } 
#                
#                //echo $mot;
#                if($syllabe->is_h)
#                {
#                    $req=requete("SELECT * FROM $h_db WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}'
#                                AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
#                    if(num_rows($req)!=0)
#                    {
#                        $response=fetch_array($req);
#                        $h=($response["aspire"]==1)?(TRUE):(FALSE);
#                    }     
#                }
#                if($syllabe->is_ent)
#                {
#                    $req=requete("SELECT * FROM $ent_db WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}'
#                                AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
#                    if(num_rows($req)!=0)
#                    {
#                        $response=fetch_array($req);
#                        $ent=($response["grave"]==1)?(TRUE):(FALSE);
##                       requete("UPDATE ent_db SET grave='$ent' WHERE mot='$mot' AND syllabe='$text'");
#                    }
#                }
#                
#                $syllabe->h=$h;
#                $syllabe->ent=$ent;
#                
#                
##                echo ($accent)?(1):2;
#                $this->texte->vers[$vers_n]->syllabes[$syllabe_n]->h = $h;
#                $this->texte->vers[$vers_n]->syllabes[$syllabe_n]->ent = $ent;
#                if(isset($accent))
#                {
#                    $this->texte->vers[$vers_n]->syllabes[$syllabe_n]->accent = $accent;
#                }
#                
#                
##                on s'occupe des hiatus 
#                $start_hiatus=($syllabe->start_hiatus)?(1):(0);
#                $end_hiatus=($syllabe->end_hiatus)?(1):(0);
#                $start_pos=($syllabe->start_hiatus);
#                $end_pos=$syllabe->end_pos;                
#                
#                if($syllabe->is_hiatus)
#                {
#                    $req=requete("SELECT * FROM $hiatus_db WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}'
#                                AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
#                    if(num_rows($req)==0)
#                    {
#                        requete("INSERT INTO $hiatus_db 
#                                (`mot`,
#                                `syllabe`,
#                                `start_hiatus`,
#                                `end_hiatus`,
#                                `start_pos`,
#                                `end_pos`
#                                )
#                                VALUES
#                                ('$mot',
#                                '$text',
#                                '$start_hiatus',
#                                '$end_hiatus',
#                                '$start_pos',
#                                '$end_pos')
#                        ");
#                    }
#                    else
#                    {
#                        requete("UPDATE $hiatus_db SET 
#                                `start_hiatus`='$start_hiatus',
#                                `end_hiatus`='$end_hiatus',
#                                `start_pos`='$start_pos',
#                                `end_pos`='$end_pos'
#                                WHERE mot='$mot' AND syllabe='$text' AND id_piece='{$this->id_piece}'
#                                AND syllabe_n='$real_syllabe_n' AND vers_n='$real_vers_n'");
#                    }
#                }
#                                
#                
#                
#                $syllabe_n++;
#            }
#            $vers_n++;
#        }
#    }
    
}




?>
