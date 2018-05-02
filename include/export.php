<?php
#require_once("include/mysql.php");
#require_once("include/infos_db.php");


#DEFINE("TEMP_DIR","temp");
#class zip_export_class
#{
#	var $doc;
#	var $zip;
#	var $filename;
#	function zip_export_class()
#	{
#		$this->doc=new DOMDocument('1.0', 'UTF-8');
#		$this->filename=TEMP_DIR."/".preg_replace(array("/ /","/\./"),"", microtime()).".bac";
#		
#		$this->zip=new ZipArchive();
#		if($this->zip->open($this->filename, ZipArchive::CREATE)!==TRUE)
#		{
#			error_log("An Error occured while creating the zip file");
#			echo "An Error occured while creating the zip file";
#			return ;
#		}
#		$this->zip->addEmptyDir('fichiers_finale');
#		$this->zip->addEmptyDir('images_jpg');
#		$this->zip->addEmptyDir('images_table_matieres');
#		$this->zip->addEmptyDir('images_titres');
#		$this->zip->addEmptyDir('incipits_jpg');
#		$this->zip->addEmptyDir('mp3');
#		$this->zip->addEmptyDir('png');
#		$this->zip->addEmptyDir('xml');
#		$this->zip->addEmptyDir('pdf');
#		
#		
#	}
#	
#	function export_base($id_base)
#	{
#		export_base($this,$id_base);

#		
#	}
#	
#	function export_recueil($id_recueil)
#	{
#		export_recueil($this,$id_recueil);

#		
#	}
#	
#	
#	function export_piece($id_piece)
#	{
#		export_piece($this,$id_piece);
#	}
#	
#	function save_file($file)
#	{
#	        if(empty($file))
#	        {
#	                return;
#	        }
#		$this->zip->addFile($file,$file);
#	}
#	
#	//for mp3 files in the database only their names is registered*
#	//we need to specify the dir
#	function save_file_in_directory($dir,$file)
#	{
#	        if(empty($file))
#	        {
#	                return;
#	        }
#		$this->zip->addFile($dir."/".$file,$dir."/".$file);
#	}
#	function export_xml($file)
#	{
#	        $this->zip->addFromString($file,$this->doc->saveXML());
#	}
#	
#	function save_all()
#	{
#	       $this->export_xml("export.xml");
#	       $this->zip->close();
#	}
#	
#	
#	function dump()
#	{
#	        $this->save_all();
#	        $fp=fopen($this->filename,"r+");
#	        if ($fp===FALSE)
#	        {
#	                return ;
#	        }
#	        header("Content-Type: application/binary");
#	        header("Content-Length: ".filesize($this->filename));
#	        if(empty($_GET["outfile"]))
#	        {
#	                header("Content-Disposition: attachment;  filename=export.bac" );
#	        }
#	        else
#	        {
#	                header("Content-Disposition: attachment;  filename=".basename(preg_replace("/ /","",$_GET["outfile"]) ));
#	        }
#	        fpassthru($fp);
#	        fclose($fp);
#	}
#	
#	
#	function free()
#	{
#	        unlink($this->filename);
#	}
#}




#// Function creates an Element with name $name in node $node and with attributes $table
#function create_element_in_node($zip,$name,$node,$table,$table_info)
#{
#	$elem=$zip->doc->createElement($name);
#	$node->appendChild($elem);
#	foreach($table_info as $in)
#	{
#		$elem->setAttribute($in,$table[$in]);
#	}
#	return $elem;
#}


#function export_recueil($zip,$id_recueil)
#{
#	$zip->doc=new DOMDocument('1.0', 'UTF-8');
#	export_recueil_in_node($zip,$zip->doc,$id_recueil);
#	return $zip->doc;
#}

#function export_piece($zip,$id_piece)
#{
#	$zip->doc=new DOMDocument('1.0', 'UTF-8');
#	export_piece_in_node($zip,$zip->doc,$id_piece);
#	return $zip->doc;
#}



#function export_base($zip,$id_base)
#{
#	global $bases_table;
#	$zip->doc=new DOMDocument('1.0', 'UTF-8');
#	$req=requete("SELECT * FROM bases WHERE id_base='$id_base'");
#	if(num_rows($req)==0)
#	{
#		return $zip;
#	}
#	$response=fetch_array($req);
#	$base=create_element_in_node($zip,"base",$zip->doc,$response,$bases_table);
#	$req=requete("SELECT id_recueil FROM recueils WHERE id_base='$id_base'");
#	while($response=fetch_array($req))
#	{
#		export_recueil_in_node($zip,$base,$response["id_recueil"]);
#	}
#	return $zip;
#}



#function export_recueil_in_node($zip,$node,$id_recueil)
#{
#	global $recueils_table;
#	$req=requete("SELECT * FROM recueils WHERE id_recueil='$id_recueil'");
#	if(num_rows($req)==0)
#	{
#		return ;
#	}
#	$response=fetch_array($req);
#	$recueil=create_element_in_node($zip,"recueil",$node,$response,$recueils_table);
#	
#	
#	// save the files
#	$zip->save_file($response["image_titre_recueil_jpg"]);
#	$zip->save_file($response["image_table_matieres"]);
#		
#	$req=requete("SELECT id_piece FROM table_matieres WHERE id_recueil='$id_recueil'");
#	while($response=fetch_array($req))
#	{
#		export_piece_in_node($zip,$recueil,$response["id_piece"]);
#	}
#}





#function export_piece_in_node($zip,$node,$id_piece)
#{
#	global $pieces_table;
#	$req=requete("SELECT * FROM pieces p 
#	INNER JOIN table_matieres tm ON tm.id_piece='$id_piece'
#	WHERE p.id_piece='$id_piece'");
#	if(num_rows($req)==0)
#	{
#		return ;
#	}
#	$response=fetch_array($req);
#	
#	//save the files
#	$zip->save_file($response["fichier_finale"]);
#	$zip->save_file($response["fichier_xml"]);
#	$zip->save_file_in_directory("mp3",$response["mp3"]);	
#	$zip->save_file($response["fichier_jpg"]);
#	$zip->save_file($response["image_incipit_jpg"]);
#	
#	$file = $response['png_lilypond'];
#	$pages=glob($file."*.png");
#	foreach($pages as $page)
#	{
#		$zip->save_file($page);
#	}
#	
#			
#	$piece=create_element_in_node($zip,"piece",$node,$response,$pieces_table);	
#	$req=requete("SELECT id_part FROM parts WHERE id_piece='$id_piece'");
#	while($response=fetch_array($req))
#	{
#		export_part_in_node($zip,$piece,$response["id_part"]);
#	}
#}



#function export_part_in_node($zip,$node,$id_part)
#{
#	global $parts_table;
#	$req=requete("SELECT * FROM parts WHERE id_part='$id_part'");
#	if(num_rows($req)==0)
#	{
#		return ;
#	}
#	$response=fetch_array($req);
#	$part=create_element_in_node($zip,"part",$node,$response,$parts_table);
#	export_melody_in_node($zip,$part,$response["id_melodie"]);
#	export_text_in_node($zip,$part,$response["id_text"]);
#}

#function export_melody_in_node($zip,$node,$id_melodie)
#{
#	global $melodies_table;
#	$req=requete("SELECT * FROM melodies WHERE id_melodie='$id_melodie'");
#	if(num_rows($req)==0)
#	{
#		return ;
#	}
#	$response=fetch_array($req);
#	$melodie=create_element_in_node($zip,"melodie",$node,$response,$melodies_table);
#	
#}

#function export_text_in_node($zip,$node,$id_text)
#{
#	global $textes_table;
#	$req=requete("SELECT * FROM textes WHERE id_text='$id_text'");
#	if(num_rows($req)==0)
#	{
#		return ;
#	}
#	$response=fetch_array($req);
#	$text=create_element_in_node($zip,"text",$node,$response,$textes_table);
#}
?>
