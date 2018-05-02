<?php
//calcule le produit des éléments d'un tableau
function product($tableau)
{
	$p=1;
	foreach($tableau as $elem)
	{
		$p*=$elem;
	}
	return $p;
}

function sum($tableau)
{
	$p=0;
	foreach($tableau as $key=>$elem)
	{
	    if($key !== "silence")
	    {
		    $p+=$elem;
	    }
	}
	return $p;
}

function scalar_product($a,$b)
{
	//error
#	if(count($a)!=count($b))
#	{
#		echo "error while doing scalar product<br/>\n";
#		return -1;
#	}
#	$n=count($a);
#	$p=0;
#	for($i=0;$i<$n;$i++)
#	{
#		$p+=$a[$i]*$b[$i];
#	}
	$p=0;
	foreach($a as $key=> $value)
	{
	    if($key !== "silence")
	    {
		    $p = $p + $a[$key] * $b[$key];
	    }
	}
	return $p;
}

function norm($a)
{
	$p=scalar_product($a,$a);
	return sqrt($p);
}



function normalize($a)
{
	$norm=norm($a);
	if($norm==0)
	{
		echo "norm is null <br/>\n";
		return -1;
	}
	foreach($a as &$elem)
	{
		$elem/=$norm;
	}
	return $a;
}







//fonction qui remplit un tableau avec 12 entrées (0 ... 11 ) et qui met un zero si l'entrée n'existe pas
function fill_zero($tableau)
{
	for ($i = 0 ; $i < 12 ; $i++)
	{
		if (!isset($tableau[$i]))
		{
			$tableau[$i]=0;
		}
	}
	return $tableau;
}
?>
