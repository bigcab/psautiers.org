<?php
require_once("include/note.php");
require_once("include/config.php");
DEFINE("COPYRIGHT","Music Engraving By Lilypond, Generated by PPIV.");

/**********************************************************************************************************************************************
							ici on met répertorie toute les fonctions
				
				
	classes:
	        -music_xml_class
	        -part_class
	        -measure_class
	        -note_class
	        -lyric_class
	        						
	openload_xml($fichier)
	get_nb_part($collection_of_part_nodes)
	
	fonction pour les ambitus : get_ambitus dans chaque classe
	les var sont min_note et max_note à chaque fois
	
	
	//les fonctions les plus importantes
	
	certaines fonctions ne serviront peut-être jamais

**********************************************************************************************************************************************/

class music_xml_class
{
	var $fichier="";
	var $nb_parts=0;	//le nombre de voix
	var $parts=array();	//tableau de classe part donc avec un s à la fin
	var $beats="";	//balise <beat>
	var $beat_type="";	//correspond à <beat-type>
	var $note_finale=""; //last note 
	var $ambitus="";
	var $min_note="";
	var $max_note="";
	var $clef="";
	
	var $max_nb_measures=0; // gives the maximum number of measures in music
	
	var $first_note="" ;// useful for analyse.php
	
	//	Cette fonction a le même nom que la classe
	//	Ca ira plus vite pour la déclaration de la classe
	//	et son initialisation
	//	on fera juste $xml=new music_xml_class($fichier);
	function music_xml_class($filename)
	{
	    if(empty($filename))
	    {
	        return ;
	    }
		$this->fichier=$filename;
		$xml_doc=openload_xml($this->fichier);
		
		
		
		if($xml_doc->getElementsByTagName("identification")->length!=0)
		{
		
		
		   $identification=$xml_doc->getElementsByTagName("identification")->item(0);
		   $encoding=$identification->getElementsByTagName("encoding");
		   $rights=$identification->getElementsByTagName("rights"); 
		   
		  if(($encoding->length!=0)&&($rights->length!=0))
		  {
		    $rights=$identification->getElementsByTagName("rights")->item(0);
		    $encoding=$identification->getElementsByTagName("encoding")->item(0);
		   
		if($encoding!=NULL)
		{
		
			$identification->removeChild($encoding);
		}
		if($rights!=NULL)
		{	
			$identification->removeChild($rights);
		}
		$nencoding=$xml_doc->createElement("encoding");
		$nrights=$xml_doc->createElement("rights");
		$software=$xml_doc->createElement("software");
		$copyright=$xml_doc->createTextNode(COPYRIGHT);
		$software->appendChild($copyright);
		$nrights->appendChild($copyright);
		$identification->appendChild($nrights);
		$nencoding->appendChild($software);
		$identification->appendChild($nencoding);
		}
		}
		$xml_doc->save($this->fichier);
		$part_nodes=$xml_doc->getElementsByTagName("part");
                $part_list_node=$xml_doc->getElementsByTagName("part-list")->item(0);
                
        $this->max_nb_measures=0;        
                //      initialisation du tableau parts
                $this->parts=array();
                foreach($part_nodes as $part_node)
                {
                        $part=new part_class($part_node,$part_list_node);
                        
                        //ajout dans le tableau
                        array_push($this->parts,$part);
                }
                
                //      les variables qui pourraient servir
                $this->nb_parts=count($this->parts);
                $this->get_ambitus();
                $this->get_last_note();
                foreach($this->parts as $part)
                {
                        $this->clef.=$part->clef;
                        $this->max_nb_measures=max($part->nb_measures, $this->max_nb_measures);
                }
	}
	
	
	function get_ambitus()
	{
	        global $correspondances;
	        if ($this->nb_parts !=0)
	        {
	            $this->min_note=$this->parts[0]->min_note;
	            $this->max_note=$this->parts[0]->max_note;
	            foreach($this->parts as $part)
	            {
	                    $this->min_note=min_note($this->min_note,$part->min_note);
	                    $this->max_note=max_note($this->max_note,$part->max_note);
	            }
	            $this->ambitus=$this->min_note->note_info->step.$this->min_note->note_info->octave."-".$this->max_note->note_info->step.$this->max_note->note_info->octave;
	        }
	        
	}
	
	function get_last_note()
	{
		$nb=$this->nb_parts;
		$i=0;
		$note=$this->parts[$i]->last_note;
		// Find the first last-note (the note maybe null)
		while(($i<$nb)&&(!is_note($note)))
		{
			$i++;
			$note=$this->parts[$i]->last_note;
		}
		//we found it
		$this->last_note=$note;
		$i++;
		if($i<$nb)
		{
			$note=$this->parts[$i]->last_note;
			while($i<$nb)
			{
				$note=$this->parts[$i]->last_note;
				if(is_note($note))
				{
					$this->last_note=min_note($this->last_note,$note);
				}
				$i++;
			}
			$octave=$this->last_note->note_info->octave;
			$step=$this->last_note->note_info->step;
			$this->last_note=$step.$octave;
		}
		else
		{
#			we did not find the last note
			$this->last_note="";
#			hence null
		}
	}
	
	function get_first_note()
	{
		foreach($this->parts as $part)
		{
			$note=$part->get_first_note();
			if($note!=NULL)
			{
				return $note;
			}
		}
		return NULL;
	}
	
	function get_max_nb_measures()
	{
		$this->max_nb_measures=0;
		
	}
	
}


function is_note($a)
{
	if((isset($a->note_info))&&(isset($a->note_info->note_value))&&($a->note_info->note_value!=NULL))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function max_note($a,$b)
{
        global $correspondances;
        
        if ($a->note_info->octave > $b->note_info->octave)
        {
                return $a;
        }
        else if ($a->note_info->octave == $b->note_info->octave)
        {
                if ($correspondances[$a->note_info->step] > $correspondances[$b->note_info->step])
                {
                        return $a;
                }
                else
                {
                        return $b;
                }
        }
        else
        {
                return $b;
        }
}


function min_note($a,$b)
{
        global $correspondances;
        if ($a->note_info->octave < $b->note_info->octave)
        {
                return $a;
        }
        else if ($a->note_info->octave == $b->note_info->octave)
        {
                if ($correspondances[$a->note_info->step] < $correspondances[$b->note_info->step])
                {
                        return $a;
                }
                else
                {
                        return $b;
                }
        }
        else
        {
                return $b;
        }
}



class measure_class
{
	/*Handling time
	<time>
		<beats>3</beats>
		<beat-type>4</beat-type>
	</time>*/
	var $beats="";
	var $beat_type="";
	
	var $measure_duration=0;
	
	var $divisions="";
	
        var $measure_number=0;
        var $notes=array();     //tableau de classe note_class
        var $min_note="";
        var $max_note="";
        function measure_class($measure_node,$last_beats=4,$last_beat_type=4,$last_divisions=1)
        {
        	$beats_nodes=$measure_node->getElementsByTagName("beats");
        	$beat_type_nodes=$measure_node->getElementsByTagName("beat-type");
        	$divisions_nodes=$measure_node->getElementsByTagName("divisions");
        	
        	if($divisions_nodes->length==0)
        	{
        		$this->divisions=$last_divisions;
        	}
        	else
        	{
        		$this->divisions=$divisions_nodes->item(0)->textContent;
        	}
        	
                if($beats_nodes->length==0)
                {
                	$this->beats=$last_beats;
                	$this->beat_type=$last_beat_type;
                }
                else
                {
                	$this->beats=$beats_nodes->item(0)->textContent;
                	$this->beat_type=$beat_type_nodes->item(0)->textContent;
                }
                
                $this->measure_duration=measure_duration($this->beats,$this->beat_type);
                
                $this->measure_number=$measure_node->getAttribute("number");
                $note_nodes=$measure_node->getElementsByTagName("note");
                $this->notes=array();
                
                $last_time_location=0;
                $last_duration=0;
                
                foreach ($measure_node->childNodes as $child)
                {
                	switch($child->nodeName)
                	{
                		case "note":
                			$note=new note_class($child,$this->divisions,$last_time_location,$last_duration);
				        $last_time_location=$note->time_location;
				        $last_duration=$note->duration;
				        array_push($this->notes,$note);
                		break;
                		
                		case "backup":
       					$last_time_location+=$last_duration;
                			$last_time_location-=$child->nodeValue/$this->divisions;
                			$last_duration=0;
                		break;
                	}
                }
                
                $this->get_ambitus();
        }
        function get_first_note()
        {
        	foreach($this->notes as $note)
                {
                        if(is_note($note))
                        {
                        	return $note;
                        }
                }
                return NULL;
        }
        
        function get_ambitus()
        {
                global $correspondances;
                $this->min_note=$this->get_first_note();
                $this->max_note=$this->get_first_note();
                if($this->min_note==NULL && $this->max_note==NULL)
                {
                	return ;
                }
                
                foreach($this->notes as $note)
                {
                	if(is_note($note))
                	{
                		$this->min_note=min_note($this->min_note,$note);
                		$this->max_note=max_note($this->max_note,$note);
                	}

                }
        }
        function get_last_note()
        {
        	$nb=count($this->notes);
        	if($nb==0)
        	{
        		return NULL;
        	}
        	$i=$nb-1;
        	while(($i>=0)&&(!is_note($this->notes[$i])))
        	{
        		$i--;
        	}
        	
        	if(($i!=-1)&&(is_note($this->notes[$i])))
        	{
        		return $this->notes[$i];
        	}
        	else
        	{
        		return NULL;
        	}
        }
        
}


class lyric_class
{
        var $text="";
        var $syllabic="";
        
        
        //      Cette fonction prend une balise lyric en argument
        //      et retourne une classe lyric_class
        function lyric_class($lyric_node)
        {
                $lyric_child_nodes=$lyric_node->childNodes;
                foreach($lyric_child_nodes as $child)
                {
                        switch($child->nodeName)
                        {
                                case "text":
                                $this->text=$child->nodeValue;
                                break;
                                
                                case "syllabic":
                                $this->syllabic=$child->nodeValue;
                                break;
                        }
                }
        }       


}

//      Cette classe servira quand on voudra représenter les
//      notes précisément
//      par exemple si on a une hauteur de 10
//      cela correspond à soit un si bémol ou un la dièse
//      elle correspond à la balise pitch et prend tout ce qui
//      a d'interessant dedans
class note_info_class
{
        var $note_value=NULL;
        var $step;
        var $octave;
        var $alter;

        
        function note_info_class($pitch_node)
        {
                global $correspondances;
	        $note = 0;
	        $child_nodes=$pitch_node->childNodes;
	
	        foreach($child_nodes as $child)
	        {
		        switch ($child->nodeName)
		        {
		                case 'step':
                                $this->step=$child->nodeValue;
		                break;
		                
		                case 'octave':
                                $this->octave=$child->nodeValue;
		                break;
		        
		                case 'alter':
		                $this->alter=$child->nodeValue;
		                break;
		                
		                
	
		        }	
	        }
	        if(empty($this->step)&&empty($this->octave))
	        {
	        	$this->note_value=NULL;
	        }
	        else
	        {
	        	$this->note_value=$correspondances[$this->step]+12*$this->octave+$this->alter;
	        }
        }
        
}






//	Classe note : toutes les infos pour pouvoir chanter ou jouer correctement
//	ajout des informations sur les accords, is_chord TRUE si accord, false sinon
class note_class
{
	var $note_info;        //classe note_info_class (une classe ou "rest" pour un silence)
	var $rythm;     //rythme
	var $nb_dots=0;   //pour le rythme (pointées)
	var $lyric;     //parole
	var $tie;       //liaison : start ou stop ou null
    var $accidental;        //sert pour la représentation	
	var $stem;      //up ou down : positionnement de la barre 
	var $is_chord=FALSE;
	
	
	var $time_location; // in duration value : 0.5 means it is an eigth (une croche après début de la mesure)
	var $duration; // we don't use printout settings of musicxml but their midi features
			// the <duration> tag hold the duration of the note
			// but we have to know the value of <divisions> which holds the value of the smallest duration
			// it is the ratio of the duration of a quarter divided by divisions
			/*
			if divisions is 1 then duration=4 is a whole
			if divisions is 2 then duration=1 is a eigth croche
			(two for a quarter)
			*/
	    
    function is_rest()
    {
        return ($this->note_info=="rest");
    }
        //	Cette fonction prend une balise <note>
        //	en argument et renvoie une classe note_class
        function note_class($note_node,$divisions,$last_time_location,$last_duration)
        {
                $note_child_nodes=$note_node->childNodes;
	        foreach($note_child_nodes as $child)
	        {
	                switch($child->nodeName)
	                {
	                        case "pitch":   //pitch node
	                        $this->note_info=new note_info_class($child);
	                        break;
	                
	                        case "type":    //type node (rythme)
	                        $this->rythm = $child->nodeValue;
	                        break;
	                
	                        case "lyric":   //lyric node
	                        $this->lyric=new lyric_class($child);
	                        break;
	                
	                        case "dot":     //pointée : on rajoute un point
	                        $this->nb_dots +=1;
	                        break;
	                
	                        case "tie":
	                        $this->tie=$child->getAttribute("type");
	                        break;
	                        
	                        case "accidental":
		                $this->accidental=$child->nodeValue;
		                break;
		                
		                case "stem":
		                $this->stem=$child->nodeValue;
		                break;
		                
		                case "chord":
		                $this->is_chord=TRUE;
		                break;
		                
		                case "duration":
		                $this->duration=$child->nodeValue/$divisions;
		                break;
		                
		                case "rest":
		                $this->note_info="rest";
		                break;
	                }
	        }
	        // now finished parsing
	        if($this->is_chord==TRUE)
	        {
	        	$this->time_location=$last_time_location;
	        	
	        }
	        else
	        {
	        	//not a chord so we have to move time_location
	        	$this->time_location=$last_time_location+$last_duration;
	        }
	        
        }


}

//      Note pour le champ $instrument name, la complexité est tres grande
//      mais bon c'est la seule facon que j'ai trouvé
//      surtout que l'on a pas dix mille instruments
//      donc ça pourra aller

class part_class
{
	var $instrument_name;
	var $part_id;
	var $nb_measures;
	var $measures;      //tableau de classe measure
	var $clef_sign;
	var $clef_line;
	var $clef;
	var $fifths;    //pour l'armure
	var $min_note;
	var $max_note;
	var $last_note;
	var $first_note;
	
	//var $divisions; //how we divided a whole in time
	function get_first_note()
	{
		foreach($this->measures as $measure)
		{
			if($measure->get_first_note()!=NULL)
			{
				return $measure->get_first_note();
			}
		}
		return NULL;
	}
	function get_first_max_note()
	{
		foreach($this->measures as $measure)
		{
			if(is_note($measure->max_note))
			{
				return $measure->max_note;
			}
			
		}
		return NULL;
	}
	
	function get_first_min_note()
	{
		foreach($this->measures as $measure)
		{
			if(is_note($measure->min_note))
			{
				return $measure->min_note;
			}
			
		}
		return NULL;
	}
	
	
	function get_ambitus()
	{
	        global $correspondances;
	        $this->min_note=$this->get_first_min_note();
	        $this->max_note=$this->get_first_max_note();
	        foreach($this->measures as $measure)
	        {
	        	if(is_note($measure->min_note))
	        	{
	        		$this->min_note=min_note($measure->min_note,$this->min_note);
	        	}
	        	if(is_note($measure->max_note))
	        	{
	                	$this->max_note=max_note($measure->max_note,$this->max_note);
	                }	                
	        }
	}
	
	function part_class($part_node,$part_list_node)
	{
	        
	        //      on prend l'id de la partie
	        $this->part_id=$part_node->getAttribute("id");  
	        
	        //      balise measure on compte le nb de mesures
	        $this->nb_measures=$part_node->getElementsByTagName("measure")->length;
	        
	        
	        //      On prend le nom de l'instrument
	        $score_part_nodes=$part_list_node->getElementsByTagName("score-part");
	        foreach($score_part_nodes as $score_part_node)
	        {
	                //      si on trouve le bon id
	                if ($score_part_node->getAttribute("id")==$this->part_id)
	                {
	                         $this->instrument_name=$score_part_node->getElementsByTagName("part-name")->item(0)->nodeValue;
	                }
	        }
	        
	        //      les choses sérieuses
	        //      sont quand même facilitées par l'utilisation des 
	        //      classes
	        //      on prend les balises measure
	        $measure_nodes=$part_node->getElementsByTagName("measure");
	        
	        //      declaration du tableau
	        $this->measures=array();
	    	$last_beats=4;
	        $last_beat_type=4;
	        $last_divisions=1;
	        foreach($measure_nodes as $measure_node)
	        {
	                //      super simple
	                //      déclaration de la classe note
	                $measure=new measure_class($measure_node,$last_beats,$last_beat_type,$last_divisions);
	                if(!empty($measure->divisions))
	                {
	                	$last_divisions=$measure->divisions;
	                }
	                if(!empty($measure->beats))
	                {
	                	$last_beats=$measure->beats;
	                	$last_beat_type=$measure->beat_type;
	                }
	                
	                //      ajout dans le tableau de classe
	                array_push($this->measures,$measure);
	        }
	        
	        // ici il faut prendre toutes 
	        for ($i=0;$i<$part_node->getElementsByTagName("sign")->length;$i++)
	        {
	        	$this->clef_sign=$part_node->getElementsByTagName("sign")->item($i)->nodeValue;
	        	$this->clef_line=$part_node->getElementsByTagName("line")->item($i)->nodeValue;
	        	$this->clef=$this->clef_sign.$this->clef_line;
	        }
	        
	        $this->fifths = $part_node->getElementsByTagName("fifths")->item(0)->nodeValue;
	        $this->get_ambitus();
	        $this->last_note=$this->get_last_note();
	}
	
	
	function get_last_note()
	{
		$nb=count($this->measures);
		$i=$nb-1;
		$note=$this->measures[$i]->get_last_note();
		while((!is_note($note))&&($i>=0))
		{
			$i--;
			$note=$this->measures[$i]->get_last_note();
		}
		if(is_note($note))
		{
			return $note;
		}
		else
		{
			return NULL;
		}
	}
}





//	La fonction de recherche ne sert plus à rien 
//	mais on la laisse ici au cas où.
/*function recherche($fichier,$chaine_a_chercher)
{
	$xml_doc=openload_xml($fichier);
	//On prend toutes les balises <pitch>
	$pitches = $xml_doc->getElementsByTagName('pitch');
	$notes = array();
	foreach($pitches as $pitch)
	{
		$enfants = $pitch->childNodes; // Il y a donc les balises <note>, <alter>, et <octave>
		array_push($notes,parse($enfants)); // On rajoute la note dans le tableau
	}
	$melodie_str = NULL;
	for ($i=1;$i<sizeof($notes);$i++)
	{
		$melodie_str .= $notes[$i] - $notes[$i-1]; // .= pour concaténer
		$melodie_str .= '/';
	}
	echo 'Le fichier contient : '.$melodie_str."\n";
	echo 'On recherche : '.$chaine_a_chercher."\n";
	if ((strpos($melodie_str,$chaine_a_chercher) === false)) return false;	else return true;
}
*/





//	La fonction prend en argument un parseur xml
//	et renvoie le nombre de voix de la partition
//	en regardant la balise <part></part>
//	Elle équivaut à un
//	document.getElementsByTagName('part')[0].childNodes.length en javascript
//	La fonction marche à 100%
function get_nb_part($collection_of_part_nodes)
{
	return $collection_of_part_nodes->length;//document.getElementsByTagName('part').length en javascript
}










//prend une variable parts : $music_xml->parts
function get_melodies($parts)
{
        $melodies=array();      //tableau de classes melody_class
        foreach($parts as $part)
        {
                //on prend la melodie de la partie
                $melody=get_melody($part);
                //on la met dans le tableau
                array_push($melodies,$melody);
        }
        return $melodies;
}

//on ressort par intervalle
//sauf pour la premiere note ou l'on a 
//la vraie valeur
//la forme est: intervalle/intervalle_suivant ...
function get_melody($part)
{
        $last_note=0;   //correspond à rien il permet d'avoir la valeur de la
                        //premiere note
        // on s'interesse à la valeur des notes seulement, rien d'autres
        $melody="/";
        foreach($part->measures as $measure)
        {
                //on prend les note_class
                foreach($measure->notes as $note)
                {
                		if((isset($note->note_info))&&(isset($note->note_info->note_value)))
                		{
                        	$note_value=$note->note_info->note_value;
                        	$melody .= $note_value-$last_note."/";
                        	//last_note est reinitialisé à cette note
                        	//ainsi pour la premiere note, on a sa valeur absolue
                        	//puisque l'intervalle entre $note_value et 0 
                        	//est $note_value
                        	$last_note=$note_value;
                        }
#                        else
#                        {
#                        	$note_value=NULL;
#                        }
#                       
                        
                }
        }
        return $melody;
}


//meme chose pour les paroles, je ne détaille pas cf get_melodies
function get_lyrics($parts)
{
        $lyrics=array();
        foreach($parts as $part)
        {
                $lyric=get_lyric($part);
                array_push($lyrics,$lyric);
        }
        return $lyrics;
}
//similaire à get_melody
function get_lyric($part)
{
	$lyric="";
        foreach($part->measures as $measure)
        {
                //note_class
                foreach($measure->notes as $note)
                {
                	if(isset($note->lyric))
                    {
                        $syllabic=$note->lyric->syllabic;
                        $text=$note->lyric->text;
                        $lyric .= $text;
                        if (($syllabic == "single") || ($syllabic=="end"))
                        {
                                $lyric .= " ";
                        }
                    }
                }
        }
        return $lyric;
}


function get_rythms($parts)
{
        $rythms=array();
        foreach($parts as $part)
        {
                $rythm=get_rythm($part);
                array_push($rythms,$rythm);
        }
        return $rythms;
}
//similaire à get_melody
function get_rythm($part)
{
        //var globale pour les rythmes
        global $rythm_correspond;
        foreach($part->measures as $measure)
        {
                //note_class
                foreach($measure->notes as $note)
                {
                        $rythm .= $rythm_correspond[$note->rythm]."/";
                }
        }
        return $rythm;
}

function get_sylbacs($xml)
{
    $tab=array();
    foreach($xml->parts as $part)
    {
        array_push($tab,get_sylbac_in_part($part));
    }
    return $tab;
}

// on prend les paroles au format de syllabation à la bac
//exemple : qui/au/con#seil/des/ma#lins/
function get_sylbac_in_part($part)
{
    $text="";
    $i=0;
    foreach($part->measures as $measure)
    {
            //note_class
            foreach($measure->notes as $note)
            {
                    $syllabic=$note->lyric->syllabic;
                    $text .= $note->lyric->text;
                    if (($syllabic == "single") || ($syllabic=="end"))
                    {
                            $text .= "/";
                    }
                    else
                    {
                        $text.="#";
                    }
                   
            }
    }
    return $text;  
}

?>
