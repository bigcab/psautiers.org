<?php

/*
 function to convert from $a=C4 / $b=D4 to 2M (seconde majeure)
 convertit en "vrai" intervalle musical pour trouver une seconde
	function step_to_interval_name($a,$b,$oct1,$oct2)


convertit en demi ton :

	function interval_to_value($a)

convertit chaine F4# en valeur
	function note_to_value_bis($note)
	
compare les intervalles :
	function cmp_interval($a,$b) 
compare les notes:
	function cmp_note($a,$b)
	
donne la prochaine note:	
	function next_note($step,$octave,$alter,$interval,$sign)
	// prend une note en chaine, ressort une chaine
	function next_note_string_string($note,$interval,$sign)
donne le vrai intervalle:	
	function convert_to_full_interval($step1,$step2,$octave1,$octave2,$value1,$value2)
	
	
	
convertit une classe note en valeur:	
	function note_to_value($note)
convert note_class_to_rythm:	
	note_class_to_rythm_value($note_class)	
	
find measure length example 3/4 is 3 3/8 is 1.5
	measure_duration($beats,$beat_type)

*/

// tableau qui sera utile pour les transpositions voir dans analyse
$tableau_correspond_int_note=array(
	"C" => 0,
	"D" => 1,
	"E" => 2,
	"F" => 3,
	"G" => 4,
	"A" => 5,
	"B" => 	6,
	0 => "C",
	1 => "D",
	2 => "E",
	3 => "F",
	4 => "G",
	5 => "A",
	6 => "B"	
	);


$alterations=array(
	0 => "",
	1 => '#',
	-1 => '♭',
	-2=> '♭♭',
	+2=> '##',
	""=> 0,
	"#"=> 1,
	"♭"=> -1,
	"##"=> 2,
	"♭♭"=> -2
	
);

// function to convert from $a=C4 / $b=D4 to 2M (seconde majeure)
// convertit en "vrai" intervalle musical pour trouver une seconde
function step_to_interval_name($a,$b,$oct1,$oct2)
{
	global $tableau_correspond_int_note;
	$x=$tableau_correspond_int_note[$a]; // distance avec le C oct1
	$y=$tableau_correspond_int_note[$b];
	$delta=abs($y-$x); // si les notes sont dans la meme tonalité
	//pb : quelle est l'octave sol3 mi3 ou sol3 mi4  
	$r_delta= $y-$x + ($oct2-$oct1)*7; //distance algébrique
	$r_delta%=7;
	$r_delta=abs($r_delta); 
	return ($r_delta!=0)?($r_delta+1):(0);
	
}

function interval_to_value($a)
{
	global $correspondances_interval_value;
	$x=explode("o",$a);
	//print_r($x);
	if(count($x)==2)
	{
		$octave_jump=$x[1];
	}
	else
	{
		$octave_jump=0;
	}
	return ($correspondances_interval_value[$x[0]]+$octave_jump*12);
}

//useful if of form F4#
function note_to_value_bis($note)
{
	$a=substr($note,0,2);
	$b=substr_count($note,"#");
	$c=substr_count($note,'♭');
	return note_to_value($a)+$b - $c;
}

function cmp_interval($a,$b)
{
	global $correspondances_interval_value;
	$x=interval_to_value($a);
	$y=interval_to_value($b);
	if($x<$y)
	{
		return -1;
	}
	else if($x>$y)
	{
		return 1;
	}
	else 
	{
		return 0;
	}
}

function cmp_note($a,$b)
{
	$x=note_to_value_bis($a);
	$y=note_to_value_bis($b);
	if($a=="silence")
	{
		return 1;
	}
	else if($b=="silence")
	{
		return -1;
	}
	
	if($x<$y)
	{
		return -1;
	}
	else if($x>$y)
	{
		return 1;
	}
	else 
	{
		return 0;
	}
}

$correspondances_interval_value=array(
	"0" => 0,
	"2d" => 0,
	"2m" => 1,
	"2M" => 2,
	"2a" => 3,
	"2sa" => 4,
	"3sd" => 1,
	"3d" => 2,
	"3m" => 3,
	"3M" => 4,
	"3a" => 5,
	"3sa" => 6,
	"4sd" => 3,
	"4d" => 4,
	"4j" => 5,
	"4a" => 6,
	"4sa" => 7,
	"5sd" => 5,
	"5d" => 6,
	"5j" => 7,
	"5a" => 8,
	"5sa" => 9,
	"6sd" => 6,
	"6d" => 7,
	"6m" => 8,
	"6M" => 9,
	"6a" => 10,
	"6sa" => 11,
	"7sd" => 9,
	"7d" => 10,
	"7j" => 11,
	"7a" => 12
);

// use this to convert to real interval
$correspondances_value_interval=array(
	0 => array(
		0 => "",
		1=> "a",
		2=> "sa"),
	1 => 	array(
			0 => "j",
			1 => "a",
			2 => "sa"
			
		),
	2 =>  	array(
			0 => "d",
			1 => "m",
			2 => "M",
			3 => "a",
			4 => "sa"
		),
	3 => 	array(
			1 => "sd",//sur dimminuée
			2 => "d",
			3 => "m",
			4 => "M",
			5 => "a",
			6 => "sa"
		),
	4 => 	array(
			3 => "sd",//sur dimminuée
			4 => "d",
			5 => "j",
			6 => "a",
			7 => "sa"
		),
	5 => 	array(
			5 => "sd",//sur dimminuée
			6 => "d",
			7 => "j",
			8 => "a",
			9 => "sa"
		),
	6 => 	array(
			6 => "sd",//sur dimminuée
			7 => "d",
			8 => "m",
			9 =>  "M",
			10 => "a" ,
			11 => "sa"
		),
	7 =>  	array(
			9 => "sd",//sur dimminuée
			10 => "d",
			11 => "j",
			12 => "a"
		)
);


function next_note_string_string($note,$interval,$sign)
{
	$step=substr($note,0,1);
	$octave=substr($note,1,1);
	global $alterations;
	$b=substr_count($note,"#");
	$c=substr_count($note,'♭');
	$alter=$b-$c;
	return next_note($step,$octave,$alter,$interval,$sign);
}

function next_note_string_string_special($note,$interval,$sign)
{
	$step=substr($note,0,1);
	global $alterations;
	$b=substr_count($note,"#");
	$c=substr_count($note,'♭');
	$alter=$b-$c;
	return next_note_special($step,$alter,$interval,$sign);
}

// find next note  to C4# for example, with corresponding interval
// interval is of form 2M   
// return is a note of form string : C5b
function next_note($step,$octave,$alter,$interval,$sign)
{
	//alter is of type int 1 -1 
	global $tableau_correspond_int_note;
	global $alterations;
	// first we take the first chararcter which is a number and we don't forget to substract 1
	$array=explode("o",$interval);
	$dist=substr($interval,0,1) - 1; // real distance
	$index=$tableau_correspond_int_note[$step];
	$new_octave=$octave;
	if(count($array)==2)
	{
		$octave_jump=$array[1];
	}
	else
	{
		$octave_jump=0;
	}
	//now we take care of alterations
	
	global $correspondances_interval_value;
	$interval_value=interval_to_value($interval);
	
	$value=note_to_value_bis($step.$octave)+$alter;
	
	if($sign=="+")
	{
		
		$new_index=$index+$dist;
		if($new_index>=7)
		{
			$new_octave++;
		}
		$new_octave+=$octave_jump;
		
		//we find the new_value
		$new_value=$value+$interval_value;
		
	}
	else
	{
		// sign is '-'
		$new_index=$index-$dist;
		if($new_index<0)
		{
			$new_octave--;
		}
		$new_octave-=$octave_jump;
		
		$new_value=$value-$interval_value;
	}
	$new_step=$tableau_correspond_int_note[modulo_spe($new_index,7)];
	
	$wrong_value=note_to_value_bis($new_step.$new_octave);
	$alteration=$alterations[$new_value-$wrong_value];
	return $new_step.$new_octave.$alteration;
}

#on s'en fout de l'octave dans cette fonction
#on veut juste le nom de la note
function next_note_special($step,$alter,$interval,$sign)
{
	//alter is of type int 1 -1 
	global $tableau_correspond_int_note;
	global $alterations;
	// first we take the first chararcter which is a number and we don't forget to substract 1
	$array=explode("o",$interval);
#	on fait des calculs relatifs
	$new_octave=0; 
	$octave=0;
	if(count($array)==2)
	{
		$octave_jump=$array[1];
	}
	else
	{
		$octave_jump=0;
	}
	$dist=substr($interval,0,1) - 1; // real distance
	$index=$tableau_correspond_int_note[$step];
	
	//now we take care of alterations
	
	global $correspondances_interval_value;
	$interval_value=interval_to_value($interval);
	
	$value=note_to_value_bis($step.$octave)+$alter;
	
	if($sign=="+")
	{
		
		$new_index=$index+$dist;
		if($new_index>=7)
		{
			$new_octave++;
		}
		$new_octave+=$octave_jump;
		
		//we find the new_value
		$new_value=$value+$interval_value;
		
	}
	else
	{
		// sign is '-'
		$new_index=$index-$dist;
		if($new_index<0)
		{
			$new_octave--;
		}
		$new_octave-=$octave_jump;
		
		$new_value=$value-$interval_value;
	}
	$new_step=$tableau_correspond_int_note[modulo_spe($new_index,7)];
	
	$wrong_value=note_to_value_bis($new_step.$new_octave);
	$alteration=$alterations[$new_value-$wrong_value];
	return $new_step.$alteration;
}


function convert_to_full_interval($step1,$step2,$octave1,$octave2,$value1,$value2)
{
	global $correspondances_value_interval;
	$name=step_to_interval_name($step1,$step2,$octave1,$octave2);
	$delta=abs($value2-$value1)%12;
	
		$out=$name.$correspondances_value_interval[$name][$delta];
		if ( ( abs($value2-$value1) -$delta) != 0 )
		{
			return $out."o".( (abs($value2-$value1) -$delta)/12);
		}
		else
		{
			return $out;
		}
	
	
}



$correspondances = array(
	'A' => 9,
 	'B' => 11,
	'C' => 0,
	'D' => 2,
	'E' => 4,
	'F' => 5,
	'G' => 7);



//correspondances pour les rythmes
$rythm_correspond= array(
	1        => "ronde",
	2        => "blanche",
	4        => "noire",
	8        => "croche",
	16       => "double",
	"whole"  => 4,
	"half"   => 2,
	"quarter"=> 1,
	"eighth" => 1/2,
	"16th"	 => 1/4,
	"32nd"	 => 1/8,
	"64th"	 => 1/16,
	"128th"	 => 1/32);


/*
whole => ronde
half  => blanche
quarter => noire
eigth => croche
*/
$fraction = array(
	4 => "ronde",
	2 => "blanche",
	1 => "noire",
	0.5=> "croche");



$tie = array(
        "start" => 0,
        "stop"  => 1);



function note_to_value($note)
{
	global $correspondances;
	$value=0;
#	echo "note :".$note."<br/>";
	switch(substr($note,0,2))
	{
		case "+1":
		$value+=1;
		$note=substr($note,2,2);
		
		break;
		
		case "-1":
		$value-=1;
		$note=substr($note,2,2);
		break;
		
		
	}
	if(strlen($note)>=1)
	{
		if(isset($correspondances[$note[0]]))
		{
			$value+=$correspondances[$note[0]];
		}
		if(isset($note[1]))
		{
			$value+=$note[1]*12;
		}
		return $value;
	}
	else
	{
		return 0;
	}
}

// Cette fonction prend une chaine (une série de note) de la forme +1D5/-1D5/C4/
// et elle renvoie les valeurs correspondant
function convert_note_to_values($notes)
{
	$data=explode("/",$notes);
	$last_note=note_to_value($data[0]);
	$string="";
	unset($data[0]);
	foreach($data as $note)
	{
		if(!empty($note))
		{
			$value=note_to_value($note);
			$string.=$value-$last_note."/";
			
		}
		$last_note=$value;
		
	}
	return $string;
}



//convertit en valeur temporelle  la durée d'une note noire => 1 etc...
function note_class_to_rythm_value($note)
{
	global $rythm_correspond;
	$value=$rythm_correspond[$note->rythm];
	$temp=$value;
	for ($i=0;$i<$note->nb_dots; $i++)
	{
		$temp/=2;
		$value+=$temp;
	}
	return $value;
}


function measure_duration($beats,$beat_type)
{
	return ( $beats * ( 4 / $beat_type )  );
}



function modulo_spe($a,$b)
{
	if( ($a%$b) < 0)
	{
		return ($a%$b)+$b;
	}
	else
	{
		return ($a%$b);
	}
}


?>
