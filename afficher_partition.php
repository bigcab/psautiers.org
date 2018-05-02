<?php
// Afficher une petite partition qui correspond à ce que l'utilisateur est en train de taper ...

require_once("include/draw.php");
require_once("include/check.php");
header ("Content-type: image/png");



$x_max = 550;
$x_zero = 50;
$y_zero = 50;

//taille de la portee
$height_portee=100;

$x = $x_zero+50;

if(!isset($_GET['data']))
{
	$_GET['data']="";
}

$notes = explode('/',$_GET['data']);

if(count($notes)==0)
{
	$height=$height_portee;
}
else
{
	$height=ceil(count($notes)/9)*$height_portee;
}

$image = imagecreate(600,$y_zero+$height);


$blanc = imagecolorallocate($image,255,255,255);
$noir = imagecolorallocate($image,0,0,0);



$note_int_to_char = array( 0 => "C",
			   2 => "D",
			   4 => "E",
			   5 => "F",
			   7 => "G",
			   9 => "A",
			   11 => "B");

//on regarde la clé
$vars=array("clef_sign","clef_line");
if(!check_get($vars))
{
        draw_msg($image,$x,"aucune clé spécifiée",$noir);
        imagepng($image);
        imagedestroy($image);
        return ;
}
draw_portee($image,$x_zero,$x_max,$y_zero,$noir);
draw_clef($image,$x_zero,$y_zero,$_GET['clef_sign'],$_GET['clef_line']);


foreach($notes as $note)
{
        if ($note==NULL)
        {
                break;       
        }
	if(strlen($note)==2)
	{
	        $step = substr($note,0,1);
	        $octave = substr($note,-1);
	        $accidental=NULL;
	}
	else
	{
	        switch(substr($note,0,2))
	        {
	                case '+1':
	                $accidental="sharp";
	                break;
	                
	                case '-1':
	                $accidental="flat";
	                break;
	        }
	        $step=substr($note,2,1);
	        $octave = substr($note,-1);
	}
	if ($x>= $x_max-20)
	{
	        
	        $y_zero+=100;
	        $x=$x_zero+50;
	        draw_portee($image,50,$x_max,$y_zero,$noir);
		draw_clef($image,$x_zero,$y_zero,$_GET['clef_sign'],$_GET['clef_line']);

	}
	add_note($image,$x,$y_zero,$noir,$_GET['clef_sign'],$_GET['clef_line'],$step,$octave,"up",$accidental,"quarter",0);
	$x+=50;

}

imagepng($image);
imagedestroy($image);
?>
