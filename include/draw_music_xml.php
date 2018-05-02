<?php
require_once("include/draw.php");
require_once("include/xml.php");

/*
		fonctions importantes
		draw_parts($parts);
*/


// On va trouver le rythme le plus rapide par mesure
function smallest_measures($parts)
{
        $smallest_measures=array();
        $nb_parts=count($parts);
        for($i=0;$i<$nb_parts;$i++)
        {
                for($j=0;$j<$parts[$i]->nb_measures;$j++)
                {
                        $smallest_measures[$j]=4;
                }
        }
        
        for($i=0;$i<$nb_parts;$i++)
        {
                for($j=0;$j<$parts[$i]->nb_measures;$j++)
                {
                        $smallest_in_measure=get_smallest_in_measure($parts[$i]->measures[$j]);
                        $smallest_measures[$j]=min($smallest_in_measure,$smallest_measures[$j]);
                }
        }
        return $smallest_measures;
}


//      Cette fonction ressort le plus petit élément de deux
//      voies différentes à la meme mesure
function get_smallest_in_measure($measure)
{
        global $rythm_correspond;
        // On recherche la plus petite unité de rythme
	$smallest = 4.0; // whole
	foreach($measure->notes as $note)
	{
	        $rythm = $rythm_correspond[$note->rythm];
	        if ($rythm < $smallest && $rythm != 0)
	        {
	                $smallest = $rythm;
	        }
	
        }
        return $smallest;
}



function draw_parts($parts)
{
	global $rythm_correspond;
	header ("Content-type: image/png");
	$x_max=1000;
	$x_zero=50;
	$y_zero=100;
	$y_max=600;
	$x=$x_zero+20;

	
        
        //On fait plutôt ça :
        $smallest_array=smallest_measures($parts);
	
	$image=imagecreate($x_max,$y_max);
	
	$blanc=imagecolorallocate($image,255,255,255);
	$noir=imagecolorallocate($image,0,0,0);	
	
	$nb_parts = count($parts);
	for ($i=0; $i < $nb_parts; $i++)
	{
		$part = $parts[$i];
		$clef_sign = $part->clef_sign;
		$clef_line = $part->clef_line;
		//echo "part clef sign: ".$clef_sign." ".$clef_line." <br>";
		$fifths = $part->fifths;
		$x=$x_zero+D_MIN; 
		$y_zero = 100*($i+1); // on décale y_zero pour ne pas chevaucher les parties déjà dessinées...
		draw_portee($image,$x_zero,$x_max,$y_zero,$noir);
		
		draw_clef( $image ,$x,$y_zero,$clef_sign,$clef_line);
		$x+=2*D_MIN;
		draw_armure($image,$x,$y_zero,$y_zero,$y_zero+48,$noir,$fifths,$clef_sign,$clef_line);
		$x+=D_MIN;
		//$x+=abs($fifths)*15+20;//on ajoute un espacement
                
	

		
		//on regarde chaque mesure de la partie
		$measure_num=0;
		foreach($part->measures as $measure)
		//for($j=0;$j<$part->nb_measures;$j++)
		{
		        //$measure=$part->measures[$j];
		        //$smallest=get_smallest_in_measure($parts,$measure_num);        		                
		        //pour chaque mesure on prend la valeur minimale
		        $smallest=$smallest_array[$measure_num];
			foreach($measure->notes as $note)
			{
				$rythm=$note->rythm;
				$step=$note->note_info->step;
				$octave=$note->note_info->octave;
				$accidental=$note->accidental;		
				$stem=$note->stem;	
				    
				// On décale de $bonus si le décalage était censé s'effectuer à la fin de 
				// la dernière ligne (cela permet de resynchroniser les voix, si ce n'est pas 
				// convaincant, essaye d'enlever ces 4 lignes!	        
    	                        if ($bonus != 0)
    	                        {
    	        	                $x += $bonus;
    	        	                $bonus = 0;    	        	
    	                        }       
    	        
    	        
			        add_note(    $image,$x,$y_zero,$noir,$clef_sign,$clef_line,
		    	                        $step,$octave,$stem,$accidental,$rythm,$nb_dots);
		    	                
    	                        $x += D_MIN * ($rythm_correspond[$rythm]/$smallest); //proportionnalité   	        

    	        

    	                        if ($x>=$x_max-30)
    	                        {
    	        	                $bonus = $x -$x_max + 30;
    	        	                $y_zero+=100*$nb_parts; // on laisse du blanc qui sera rempli par les autres parts
    	        	                $x=$x_zero+D_MIN;
    	        	                draw_portee($image,$x_zero,$x_max,$y_zero,$noir);
				        draw_clef($image,$x,$y_zero,$clef_sign,$clef_line);
					draw_armure($image,$x,$y_zero,$y_zero,$y_zero+48,$noir,$fifths,$clef_sign,$clef_line);
    	                        }			

    	        		if ($x>=$x_max-30)
    	        		{
    	        			$bonus = $x -$x_max + 30;
    	        			$y_zero+=100*$nb_parts; // on laisse du blanc qui sera rempli par les autres parts
    	        			$x=$x_zero+D_MIN;
    	        			draw_portee($image,$x_zero,$x_max,$y_zero,$noir);
					draw_clef($image,$x,$y_zero,$clef_sign,$clef_line);
					draw_armure($image,$x,$y_zero,$y_zero,$y_zero+48,$noir,$fifths,$clef_sign,$clef_line);	
    	        		}			


		        }
			add_measure($image,$x,$y_zero,$noir);
			$measure_num++;
			$x+=D_MIN;
		}

	}
	imagepng($image);
	imagedestroy($image);
	
}



?>
