<?php
function do_all_markov($tableau)
{
	$info=parse_data_markov_chaine($tableau);
	if($info == FALSE)
	{
		return FALSE;
	}
	$taille= $info[0];
	if($taille == 0)
	{
	
		return FALSE;
	}
	$vect = markov_measure($taille,$info[1]);
	if($vect ==FALSE)
	{
		return FALSE;
	}
	//print_r($vect);
	$output=array();
	for ($i = 0 ; $i<$taille; $i++)
	{
		$key = $info[2][$i];
		$output[$key] = $vect[$i];
	}
	unset($output[0]);
	unset($info);
	unset($vect);
	return $output;
}

function parse_data_markov_chaine($tableau)
{
	$i=0;
	$keys = array();
	$mat=array();
	foreach($tableau as $key => $elem)
	{
		if(!isset($keys[$key]))
		{
			$keys[$i] = $key;
			$keys[$key] = $i;
			$i++;
		}
		foreach($elem as $key2 => $value)
		{
			if(!isset($keys[$key2]))
			{
				$keys[$i] = $key2;
				$keys[$key2] = $i;
				$i++;
			}
		}
	}	
	$taille= $i;
	if($taille ==0)
	{
		return FALSE;
	}
	for($i = 0; $i < $taille; $i++)
	{
		$sum=0;
		for ($j=0; $j < $taille; $j++)
		{
			$mat[$taille  * $i + $j] = 0.0;
			if (isset($tableau[$keys[$j]]))
			{
				if(isset($tableau[$keys[$j]][$keys[$i]]))
				{
					$mat[ $taille * $i + $j]=$tableau[$keys[$j]][$keys[$i]];
					$sum += $mat[$taille * $i + $j];
				}
			}
			
		}
		if($sum!=0)
		{
			for ($j = 0 ; $j < $taille ; $j ++)
			{
				
				$mat[$taille * $i + $j] /= $sum;
				
				
			}
		}
		
	}
	
	return array($taille,$mat, $keys);
}

function markov_measure($taille, $array)
{
	if($taille==0)
	{
		return FALSE;
	}
	
	return markov_measure_eigen_lib($taille , $array);	
}
?>
