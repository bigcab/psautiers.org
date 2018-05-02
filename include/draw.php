<?php
/*
							Draw.php
							
	toutes les fonctions :
	- draw_portee($image,$x_zero,$x_max,$y_zero,$noir)
	- add_note(    $image,$x,$y_zero,$noir,$clef_sign,$clef_line,
                        $step,$octave,$stem,$accidental,$rythm,$nb_dots)
        - draw_clef($image,$x,$y_zero,$clef_sign,$clef_line)
        - get_y($y_zero,$clef_sign,$clef_line,$step,$octave)
*/

// FIXME : désolé si tu as un conflit, je peux te passer l'ancienne version de ton fichier si tu veux

require_once("include/note.php");

define('POLICE',"./include/DejaVuSans.ttf");
//distance minimale entre les notes
define('D_MIN',15);

// armure Fa Do Sol Ré La Mi Si
$armure_ordre="FCGDAEB";
//tableau qui renvoie les octaves correspondantes pour l'armure
$armure["G"][2]["diese"]=array(
	"F"	=>	5,
	"C"	=>	5,
	"G"	=>	5,
	"D"	=>	5,
	"A"	=>	4,
	"E"	=>	5,
	"B"	=>	4);
	
$armure["G"][2]["bemol"]=array(
	"F"	=>	4,
	"C"	=>	5,
	"G"	=>	4,
	"D"	=>	5,
	"A"	=>	4,
	"E"	=>	5,
	"B"	=>	4);
	


	
$armure["F"][4]["diese"]=array(
	"F"	=>	3,
	"C"	=>	3,
        "G"     =>      3,
        "D"     =>      3,
        "A"     =>      3,
        "E"     =>      3,
        "B"     =>      3);

$armure["F"][4]["bemol"]=array(
	"F"	=>	2,
	"C"	=>	3,
        "G"     =>      2,
        "D"     =>      3,
        "A"     =>      2,
        "E"     =>      3,
        "B"     =>      2);


$armure["C"][4]["diese"]=array(
	"F"	=>	4,
	"C"	=>	4,
        "G"     =>      3,
        "D"     =>      4,
        "A"     =>      3,
        "E"     =>      4,
        "B"     =>      3);
        
        

$armure["C"][4]["bemol"]=array(
	"F"	=>	3,
	"C"	=>	4,
        "G"     =>      3,
        "D"     =>      4,
        "A"     =>      3,
        "E"     =>      4,
        "B"     =>      3);
        
$armure["C"][3]["diese"]=array(
	"F"	=>	4,
	"C"	=>	4,
        "G"     =>      4,
        "D"     =>      4,
        "A"     =>      3,
        "E"     =>      4,
        "B"     =>      3);

$armure["C"][3]["bemol"]=array(
	"F"	=>	3,
	"C"	=>	4,
        "G"     =>      3,
        "D"     =>      4,
        "A"     =>      3,
        "E"     =>      4,
        "B"     =>      3);

        
$armure["C"][1]["diese"]=array(
	"F"	=>	4,
	"C"	=>	4,
        "G"     =>      4,
        "D"     =>      4,
        "A"     =>      4,
        "E"     =>      4,
        "B"     =>      4);
        
$armure["C"][1]["bemol"]=array(
	"F"	=>	4,
	"C"	=>	4,
        "G"     =>      4,
        "D"     =>      4,
        "A"     =>      4,
        "E"     =>      4,
        "B"     =>      4);
        

        
// pour ecrire sur la portée
$ordre_note=array(
        "C" => 0,
        "D" => 1,
        "E" => 2,
        "F" => 3,
        "G" => 4,
        "A" => 5,
        "B" => 6);
        

//correspondance (C4)
$clef=array();
//clef de sol le do 4 se trouve à la quatrième interligne
//      on se repere par rapport au do de la clé de fa qui se trouve sur la 
//      6eme ligne
$clef["G"][2]=48;
$clef["F"][4]=0;
$clef["C"][3]=24;
$clef["C"][4]=16;
$clef["C"][1]=40;


//fonction qui prend une note avec une clé et qui ressort l'ordonnée où l'on doit l'écrire
function get_y($y_zero,$clef_sign,$clef_line,$step,$octave)
{
	global $clef;
	global $ordre_note;
	$y=$y_zero+$clef[$clef_sign][$clef_line];
	
        //      délicat:
        //      si $y augmente, plus on descend dans le grave
        //      donc on met - pour l'opération
        //      28=4*7 
        //      par exemple pour passer de do 4 à ré4
        //      ré-do=1 donc on monte de 4 pixels soit $y descend de 4
        //      meme raisonnement pour l'octave
        $y -= ($ordre_note[$step]-$ordre_note["C"])*4+($octave-4)*28;
	return $y;        
}

        
//      fonction pour dessiner une portée
//      location_y : ordonnée de la première ligne


function draw_portee($image,$x_zero,$x_max,$y_zero,$noir)
{
        for ($i=1;$i<6;$i++)
        {
                $y=$i*8+$y_zero;
                ImageLine($image,$x_zero,$y,$x_max,$y,$noir);
        }
}

function draw_armure($image,&$x,$y_zero,$y_min,$y_max,$noir,$fifths,$clef_sign,$clef_line)
{
	global $armure_ordre;
	global $armure;
	$espacement_armure=8;
	if ($fifths>0)
	{
		//dièse à mettre
		for ($i=0;$i<$fifths;$i++)
		{
			$step=$armure_ordre[$i];
			$octave=$armure[$clef_sign][$clef_line]["diese"][$step];
			$y=get_y($y_zero,$clef_sign,$clef_line,$step,$octave);
			imagettftext($image,16,0,$x,$y+9,$noir,POLICE,"♯");
			$x+=$espacement_armure;
		}
	}
	else
	{
		//bémols
		for ($i=0;$i>$fifths;$i--)
		{
			$step=$armure_ordre[6+$i];
			$octave=$armure[$clef_sign][$clef_line]["bemol"][$step];
			$y=get_y($y_zero,$clef_sign,$clef_line,$step,$octave);
			imagettftext($image,16,0,$x,$y+5,$noir,POLICE,"♭");
			$x+=$espacement_armure;
		}
	}
	$x+=D_MIN*2;
	return $x;
}



function draw_clef($image,&$x,$y_zero,$clef_sign,$clef_line)
{
        switch($clef_sign)
        {
        	//clé de sol
        	case "G":
        	$clef_image=imagecreatefrompng("./pics/cle_de_sol.png");
        	$size_x=imagesx($clef_image);
        	$size_y=imagesy($clef_image);   
        	$y=$y_zero-11;//la clé de sol est placée sur le sol cad la 2ème ligne
        	$y+=16;//on le place sur la première ligne supplémentaire    
        	break;
        	
        	case "F":
        	$clef_image=imagecreatefrompng("./pics/cle_de_fa.png");
        	$size_x=imagesx($clef_image);
        	$size_y=imagesy($clef_image);       
        	$y=$y_zero+31;//sur la premiere ligne 
        	$y+=8;
        	break;
        	
        	//clé d'ut
        	case "C":
        	$clef_image=imagecreatefrompng("./pics/cle_d_ut.png");
        	$size_x=imagesx($clef_image);
        	$size_y=imagesy($clef_image);  
        	$y=$y_zero+25;//on place sur la premiere ligne (Ut1)
        	$y+=8;//même chose la on le met sur la premiere ligne sup (Do 4 de la clé de sol)     
        	break;
        }
        $y-=$clef_line*8;
        // imagecopymerge  (  resource $dst_im  ,  resource $src_im  ,  int $dst_x  ,  int $dst_y  ,  int $src_x  ,  int $src_y  ,  int $src_w  ,  int $src_h  ,  int $pct  )
        imagecopymerge($image,$clef_image,$x,$y,0,0,$size_x,$size_y,60);
        return $x;
}

function add_measure($image,$x,$y_zero,$noir)
{
	ImageLine($image,$x,$y_zero+8,$x,$y_zero+40,$noir);
}

// C4 correspond au do à la 4eme interligne
// L'altération est ajoutée
function add_note(    $image,$x,$y_zero,$noir,$clef_sign,$clef_line,
                        $step,$octave,$stem,$accidental,$rythm,$nb_dots)
{

        //variables crées pour  les lignes supplémentaires
        //      hauteur aigue
        //      y petit
        $y_min=$y_zero;
        
        //      hauteur grave
        //      y grand
        $y_max=$y_zero+48;
        
        $y=get_y($y_zero,$clef_sign,$clef_line,$step,$octave);
        
        //on dessine l'altération
        switch ($accidental)
        {
                case "flat":
                //imagestring($image,4,$x-15,$y-9,"♭",$noir);  
                imagettftext($image,16,0,$x-15,$y+5,$noir,POLICE,"♭");
                break;
                
                case "sharp": 
                imagettftext($image,16,0,$x-15,$y+9,$noir,POLICE,"♯");
                break;
                
                case "natural":
                imagettftext($image,16,0,$x-15,$y+9,$noir,POLICE,"♮");
                
                break;
                
                default:
                break;
        }
        
        if ($step!=NULL)
        {
        	//      traitement des lignes supplémentaires
        	//      <= car pour le la aigu en clé de sol
        	//      $y=0 et y_min=0 mais on doit écrire une ligne
        
        	//      si c'est plus haut que le la aigu(on prend compte de 
        	//      l'inversion: plus c'est haut plus y bas)
             
       
        	 if($y<=$y_min)
       		{
        	        $i=$y_min;
        	        while($i>=$y)
        	        {
        	                ImageLine($image,$x-8,$i,$x+8,$i,$noir);
        	                $i-=8;
        	        }               
        	}
        
        	//      si c'est grave
        	if($y>=$y_max)
        	{
        	        $i=$y_max;
        	        //tant que c'est pas assez grave
        	        while($i<=$y)
        	        {
        	                ImageLine($image,$x-8,$i,$x+8,$i,$noir);
        	                $i+=8;
        	        }               
        	}
        	//      on dessine la note
  	      	//      c'est soit une blanche ou une ronde donc on fait un cercle
 		if ( ($rythm=="whole") || ($rythm == "half") )
	      	{
        	        ImageEllipse($image,$x,$y,8,8,$noir);                
       		}
        	//      sinon on dessine un disque
        	else
        	{
        	        ImageFilledEllipse($image,$x,$y,8,8,$noir);
        	}
        }
        else
        {
        	//on a affaire à un silence
        	
        }
        
        //      Dessin des pointées s'il y a
        while($nb_dots!=0)
        {
                ImageFilledEllipse($image,$x+4+4*$nb_dots,$y,4,4,$noir);
                $nb_dots-=1;
        }
        
        //      dessin de la barre
        //      pratique on n'a pas à savoir le
        //      rythme utilisé blanche ronde ou noire ou autre
        //      la variable stem nous donne une partie de l'info
		global $rythm_correspond;
        switch($stem)
        {
                case "up":
                ImageLine($image,$x+4,$y,$x+4,$y-28,$noir);


                $nb_crochet = log(1/$rythm_correspond[$rythm])/log(2);
				$crochet=imagecreatefrompng("pics/flag.png");
				$size_x=imagesx($crochet);
				$size_y=imagesy($crochet);  
				for ($i = 0; $i < $nb_crochet; $i++)
				{
	   				imagecopymerge($image,$crochet,$x+4,$y-28-$i*5,0,0,$size_x,$size_y,60);
				}
                break;
                
                case "down":
                ImageLine($image,$x-4,$y,$x-4,$y+28,$noir);
                 $nb_crochet = log(1/$rythm_correspond[$rythm])/log(2);
            	 $crochet=imagecreatefrompng("pics/flagdown.png");

                $nb_crochet = log(1/$rythm_correspond[$rythm])/log(2);
				$crochet=imagecreatefrompng("pics/flagdown.png");

				$size_x=imagesx($crochet);
				$size_y=imagesy($crochet);  
				for ($i = 0; $i < $nb_crochet; $i++)
				{
	   				imagecopymerge($image,$crochet,$x-4,$y+7+$i*5,0,0,$size_x,$size_y,60);
                		}
                break;
                
        }      
        
}


/*utilise cette fonction que si on a un message d'erreur à afficher*/
function draw_error_msg($string,$x_max,$y_max)
{
	$image=imagecreate($x_max,$y_max);
	header ("Content-type: image/png");
	$blanc=imagecolorallocate($image,255,255,255);
	$noir = imagecolorallocate($image,0,0,0);
	draw_msg($image,$x,$string,$noir);
	imagepng($image);
	imagedestroy($image);
}

function draw_msg($image,&$x,$msg,$noir)
{
        imagestring($image, 5 , $x, 0, $msg, $noir);
}

?>
