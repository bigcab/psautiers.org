<?php
#require_once("include/auth.php");
#require_once("include/xml.php");
#require_once("include/mysql.php");
#require_once("include/page.php");
#require_once("include/upload.php");
#require_once("include/check.php");
#require_once("include/lilypond.php");
#require_once("include/infos_db.php");
#require_once("include/upload.php");


#//function import_base ok import_recueil ok
#//not ok import_piece


#class zip_import_class
#{
#        // xml parser
#        var $doc;
#        
#        //zip archive
#        //var $zip;
#        
#        //directory to extract to
#        var $dir;
#        
#        var $zip_file;
#        //upload to the right directory
#        function import_file($file,$directory)
#        {
#                if(empty($file))
#                {
#                        return ;
#                }
#                $extension=get_extension($file);
#                //creer nouveau nom
#                $out=preg_replace(array("/ /","/\./"),"",microtime()).".".$extension;
#                if(!copy($this->dir.$file,$directory."/".$out))
#                {
#                        msg("Erreur lors de l'import d'un fichier");
#                }
#                
#                return $out;
#        }
#        
#        function free()
#        {
#                exec("rm -drf ".$this->dir);
#                unlink($this->zip_file);
#        }
#        
#        
#        function zip_import_class($zip_file)
#        {
#                $this->zip_file=$zip_file;
#                //$this->zip=new ZipArchive();
#                $this->doc=new DOMDocument("1.0","UTF-8");
#                
#                /*if($this->zip->open($zip_file)!==TRUE)
#                {
#                        msg("erreur lors de l'ouverture du fichier export");
#                        return ;
#                        
#                }*/
#                //msg("Ouverture du fichier export : succès");
#                $this->dir="temp/".preg_replace(array("/ /","/\./"),"",microtime());
#                mkdir($this->dir,0755);
#                $this->dir.="/";
#                msg($this->dir);
#                //$this->zip->extractTo($this->dir,array('export.xml'));
#                //extracting dangerous but function extractTo does not work
#                exec("unzip ".$this->zip_file." -d ".$this->dir);
#                
#                /*if($this->zip->extractTo($this->dir)===FALSE)
#                {
#                        msg("L'extraction du fichier export a échoué");
#                        return ;
#                }*/
#                
#                $this->doc->load($this->dir."export.xml");
#                
#             
#                
#        }
#        
#        function import_zip($id=0 /*id is either id_base or id_recueil*/)
#        {
#        	foreach($this->doc->childNodes as $node)
#                {
#                        switch ($node->nodeName)
#                        {
#                                case "base":
#                                	msg("importing base ");
#                                        $this->import_base($node);
#                                break;
#                                
#                                
#                                case "recueil":
#                                	$id_base=$id;
#                                        if(!isset($id_base))
#                                        {
#                                                msg("pas sélectionné de base");
#                                                return ;
#                                        }
#                                        $this->import_recueil($node,$id_base);
#                                break;
#                                
#                                case "piece":
#                                	$id_recueil=$id;
#                                	if(!isset($id_recueil))
#                                	{
#                                		msg("pas de recueil");
#                                		return;
#                                	}
#                                	$this->import_piece($node,$id_recueil);
#                                	
#                                break;
#                        }
#                }
#        }
#        
#        function import_base($node)
#        {
#                global $bases_table;
#                //does the base already exist
#                $table=array();
#                $i=0;
#                //first sql to check if the base already exists
#                $sql="SELECT id_base FROM bases WHERE ";
#                //second to insert base
#                $sql2="INSERT INTO bases (";
#                $end_sql2="VALUES (";
#                foreach($bases_table as $attr)
#                {
#                        
#                        $table[$attr]=$node->getAttribute($attr);
#                        if($i==0)
#                        {
#                                $sql.="`".$attr."`='".$table[$attr]."'";
#                                $sql2.="`".$attr."`";
#                                $end_sql2.="'".$table[$attr]."'";
#                        }
#                        else
#                        {
#                                $sql.=" AND `".$attr."`='".$table[$attr]."'";
#                                $sql2.=",`".$attr."`";
#                                $end_sql2.=",'".$table[$attr]."'";
#                        }
#                        $i++;
#                }
#                $sql2.=")";
#                $end_sql2.=")";
#                $req=requete($sql);
#           
#           	
#           	
#                if(num_rows($req)!=0)
#                {
#                	msg("La base existe déjà");
#                        $response=fetch_array($req);
#                        $id_base=$response["id_base"];
#                }
#                else
#                {
#                        msg("La base n'existe pas elle sera créée.");
#                        requete($sql2." ".$end_sql2);
#                        
#                        $id_base=mysql_insert_id();
#                }
#                
#                msg("Importation des recueils de la base");
#                foreach($node->childNodes as $child)
#                {
#                        if($child->nodeName=="recueil")
#                        {
#                                $this->import_recueil($child,$id_base);
#                        }
#                }
#                
#        }
#        
#        function import_recueil($node,$id_base)
#        {
#                global $recueils_table;
#                //does the recueil already exist
#                $table=array();
#                $i=0;
#                //first sql to check if the recueil already exists
#                $sql="SELECT id_recueil FROM recueils WHERE ";
#                //second to insert recueil
#                $sql2="INSERT INTO recueils (`id_base`";
#                $end_sql2="VALUES ('".$id_base."'";
#                foreach($recueils_table as $attr)
#                {
#                        
#                        $table[$attr]=$node->getAttribute($attr);
#                        if($i==0)
#                        {
#                                $sql.="`".$attr."`='".$table[$attr]."'";
#                                
#                        }
#                        else
#                        {
#                                $sql.=" AND `".$attr."`='".$table[$attr]."'";
#                                
#                        }
#                        $sql2.=",`".$attr."`";
#                        $end_sql2.=",'".$table[$attr]."'";
#                        $i++;
#                }
#                $sql2.=")";
#                $end_sql2.=")";
#                //checking if the recueil already exists
#                $req=requete($sql);
#                
#                if(num_rows($req)!=0)
#                {
#                	//the recueil already exist we don't do anything, just add piece
#                	msg(_("Le recueil existe déjà dans la base"));
#                        $response=fetch_array($req);
#                        $id_recueil=$response["id_recueil"];
#                }
#                else
#                {
#                        //inserting recueil
#                        msg(_("Le recueil")." ".$table["titre"]." "._("n'existe pas, il va être importé"));
#                        requete($sql2." ".$end_sql2);
#                        
#                        $id_recueil=mysql_insert_id();
#                	
#                	/*We update the files*/
#		        // image_titre_recueil_jpg  	 image_table_matieres
#		        if(!empty($table["image_titre_recueil_jpg"]))
#		        {
#		                $image_titre_recueil="images_titres/".$this->import_file($table["image_titre_recueil_jpg"],"images_titres");
#		        }
#		        if(!empty($table["image_table_matieres"]))
#		        {
#		                $image_table_matieres="images_table_matieres/".$this->import_file($table["image_table_matieres"],"images_table_matieres");
#		        }
#		        requete("UPDATE recueils
#		        SET image_titre_recueil_jpg='$image_titre_recueil',
#		        image_table_matieres='$image_table_matieres'
#		        WHERE id_recueil='".$id_recueil."'");
#                
#                }
#                
#                
#                foreach($node->childNodes as $child)
#                {
#                        if($child->nodeName=="piece")
#                        {
#                                $this->import_piece($child,$id_recueil);
#                        }
#                }
#                
#        }
#        
#        function import_piece($node,$id_recueil)
#        {
#                global $import_pieces_table;
#                global $import_table_matieres_table;
#                //does the piece already exist
#                $table=array();
#                $i=0;
#                //first sql to check if the piece already exists
#                $sql="SELECT id_piece FROM pieces WHERE ";
#                //second to insert piece
#                $sql2="INSERT INTO pieces (";
#                $end_sql2="VALUES (";
#                foreach($import_pieces_table as $attr)
#                {
#                        
#                        $table[$attr]=$node->getAttribute($attr);
#                        if($i==0)
#                        {
#                                $sql.="`".$attr."`='".$table[$attr]."'";
#                                $sql2.="`".$attr."`";
#                                $end_sql2.="'".$table[$attr]."'";
#                        }
#                        else
#                        {
#                                $sql.=" AND `".$attr."`='".$table[$attr]."'";
#                                $sql2.=",`".$attr."`";
#                                $end_sql2.=",'".$table[$attr]."'";
#                        }
#                        $i++;
#                }
#                $sql2.=")";
#                $end_sql2.=")";
#                //check if the piece already exists
#                $req=requete($sql);
#                
#                if(num_rows($req)!=0)
#                {
#                	msg(_("La pièce")." ".$table["titre"]._("existe déjà"));
#                        $response=fetch_array($req);
#                        $id_piece=$response["id_piece"];
#                        
#                        //check if piece is already in recueil
#                        $req2=requete("SELECT id_piece FROM table_matieres WHERE id_piece='$id_piece' AND id_recueil='$id_recueil'");
#                        if(num_rows($req2)==0)
#                        {
#                        	//if not in recueil
#		                //inserting to table matierses
#				$sql3="INSERT INTO table_matieres (`id_recueil`,`id_piece`";
#				$end_sql3="VALUES ('".$id_recueil."','".$id_piece."'";
#				foreach($import_table_matieres_table as $attr)
#				{
#				        $table[$attr]=$node->getAttribute($attr);
#				        $sql3.=",`".$attr."`";
#				        $end_sql3.=",'".$table[$attr]."'";
#				}
#				$sql3.=")";
#				$end_sql3.=")";
#				requete($sql3." ".$end_sql3);
#                        }
#                        //check if text is in recueil
#                        
#                        //msg("not finished this condition yet");
#                        //then return stop
#                        return;
#                }
#                
#                //insert the piece
#                msg(_("Import de la pièce")." ".$table["titre"]);
#                requete($sql2." ".$end_sql2);
#                $id_piece=mysql_insert_id();
#                
#                
#                
#                /*now import files*/
#	        if(!empty($table["fichier_finale"]))
#	        {
#	        	msg("&nbsp&nbsp&nbsp&nbsp -Import fichier finale");
#	                $fichier_finale="fichiers_finale/".$this->import_file($table["fichier_finale"],"fichiers_finale");
#	        }
#	        if(!empty($table["fichier_xml"]))
#	        {
#	        	msg("&nbsp&nbsp&nbsp&nbsp -Import fichier xml");
#	                $fichier_xml="xml/".$this->import_file($table["fichier_xml"],"xml");
#	        }
#	       
#	        if(!empty($table["mp3"]))
#	        {
#	        	msg("&nbsp&nbsp&nbsp&nbsp -Import fichier mp3");
#	                $mp3=$this->import_file("mp3/".$table["mp3"],"mp3");
#	        }
#	        if(!empty($table["fichier_jpg"]))
#	        {
#	        	msg("&nbsp&nbsp&nbsp&nbsp -Import fichier jpg");
#	                $fichier_jpg="images_jpg/".$this->import_file($table["fichier_jpg"],"images_jpg");
#	        }
#	        if(!empty($table["image_incipit_jpg"]))
#	        {
#	        	msg("&nbsp&nbsp&nbsp&nbsp -Import fichier image incipit jpg");
#	                $image_incipit_jpg="incipits_jpg/".$this->import_file($table["image_incipit_jpg"],"incipits_jpg");
#	        }
#	        //msg($table["png_lilypond"]);
#	        //png now
#	        if(!empty($table["png_lilypond"]))
#	        {
#	        	msg("&nbsp&nbsp&nbsp&nbsp -Import fichiers png lilypond");
#	        	//msg($this->dir."png/".$table["png_lilypond"]."*.png");
#	                $pages=glob($this->dir.$table["png_lilypond"]."*.png");
#	                //print_r($pages);
#	                $n=count($pages);
#	                $png_lilypond="png/".preg_replace(array("/ /","/\./"),"",microtime());
#	                if($n==1)
#	                {
#	                	//msg($pages[0]);
#	                        copy($pages[0],$png_lilypond.".png");
#	                }
#	                else
#	                {
#	                        for($i=1;$i<=$n;$i++)
#	                        {
#	                        	//msg($pages[$i-1]);
#	                                copy($pages[$i-1],$png_lilypond."-page".$i.".png");
#	                        }
#	                }
#		                
#	        }
#	        
#	        requete(
#	                "UPDATE pieces 
#	                SET png_lilypond='$png_lilypond',
#	                mp3='$mp3',
#	                fichier_xml='$fichier_xml',
#	                fichier_finale='$fichier_finale',
#	                fichier_jpg='$fichier_jpg',
#	                image_incipit_jpg='$image_incipit_jpg'
#	                WHERE id_piece='".$id_piece."'");
#	                

#		        
#                
#                
#                //ici à modifier et si la pièce est déja dans le recueil
#                
#                //inserting to table matierses
#                $sql3="INSERT INTO table_matieres (`id_recueil`,`id_piece`";
#                $end_sql3="VALUES ('".$id_recueil."','".$id_piece."'";
#                foreach($import_table_matieres_table as $attr)
#                {
#                        $table[$attr]=$node->getAttribute($attr);
#                        $sql3.=",`".$attr."`";
#                        $end_sql3.=",'".$table[$attr]."'";
#                }
#                $sql3.=")";
#                $end_sql3.=")";
#                msg("&nbsp&nbsp&nbsp&nbsp -ajout dans la table des matières");
#                requete($sql3." ".$end_sql3);
#                
#                //next add parts and text and melodies
#                foreach($node->childNodes as $child)
#                {
#                        if($child->nodeName=="part")
#                        {
#                                
#                                import_part($child,$id_piece);
#                        }
#                }
#                //requete("");
#                
#        }
#}


#function import_part($part_node,$id_piece)
#{
#	$indice_partie=$part_node->getAttribute("indice_partie");
#	msg("&nbsp&nbsp&nbsp&nbsp -Import partie $indice_partie");
#	//next add parts and text and melodies
#        foreach($part_node->childNodes as $child)
#        {
#                switch ($child->nodeName)
#                {
#                        case "text":
#                        	$id_text=check_add_text($child);
#                        break;
#                        
#                        
#                        case "melodie":
#                                $id_melodie=check_add_melodie($child);
#                        break;
#                        
#                }
#        }
#        requete("INSERT INTO parts (`id_piece`,`id_melodie`,`id_text`,`indice_partie`) VALUES ('$id_piece','$id_melodie','$id_text','$indice_partie')");
#}

#//function returns an id_melodie
#// function take melodie_node in argument
#// if melodie already exists
#// then return corresponding id_melodie or add new melodie and return new added id_melodie
#function check_add_melodie($melodie_node)
#{
#	global $melodies_table;
#	$end_sql=" WHERE ";
#	$sql2="INSERT INTO melodies (";
#	$values=" VALUES (";
#	$table=array();
#	$i=0;
#	foreach($melodies_table as $attr)
#	{
#		if($i!=0)
#		{
#			$end_sql.=" AND ";
#			$sql2.=",";
#			$values.=",";
#		}
#		$table[$attr]=$melodie_node->getAttribute($attr);
#		$end_sql.="`$attr`='".$table[$attr]."' ";
#		$sql2.="`$attr`";
#		$values.="'".$table[$attr]."'";
#		$i++;
#	}
#	$sql2.=")";
#	$values.=")";
#	$sql.=")";
#	$req=requete("SELECT id_melodie FROM melodies $end_sql");
#	
#	if(num_rows($req)!=0)
#	{
#		$response=fetch_array($req);
#		$id_melodie=$response["id_melodie"];
#		return $id_melodie;
#	}
#	else
#	{
#		requete($sql2.$values);
#		$id_melodie=mysql_insert_id();
#		return $id_melodie;
#	}
#	
#}


#//function returns an id_text
#// function take texte_node in argument
#// if text already exists
#// then return corresponding id_text or add new text and return new added id_text
#function check_add_text($text_node)
#{
#	global $textes_table;
#	$end_sql=" WHERE ";
#	$sql2="INSERT INTO textes (";
#	$values=" VALUES (";
#	$table=array();
#	$i=0;
#	foreach($textes_table as $attr)
#	{
#		if($i!=0)
#		{
#			$end_sql.=" AND ";
#			$sql2.=",";
#			$values.=",";
#		}
#		$table[$attr]=$text_node->getAttribute($attr);
#		$end_sql.="`$attr`='".$table[$attr]."' ";
#		$sql2.="`$attr`";
#		$values.="'".$table[$attr]."'";
#		$i++;
#	}
#	$sql2.=")";
#	$values.=")";
#	$sql.=")";
#	$req=requete("SELECT id_text FROM textes $end_sql");
#	
#	if(num_rows($req)!=0)
#	{
#		$response=fetch_array($req);
#		$id_text=$response["id_text"];
#		return $id_text;
#	}
#	else
#	{
#		requete($sql2.$values);
#		$id_text=mysql_insert_id();
#		return $id_text;
#	}
#	
#}



?>
