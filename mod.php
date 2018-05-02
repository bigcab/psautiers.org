<?php
require_once("include/xml.php");
//require_once("include/page.php");
//require_once("include/auth.php");
//require_once("include/mysql.php");


/*if(!$_SESSION["admin"])
{
	return 0;
}*/

// sum of x_i stored in array $x
function sigma($x)
{
	$sum=0;
	foreach($x as $num)
	{
		$sum+=$num;
	}
	return $sum;
}

function frequency($table)
{
	$f=array();
	$sum=sigma($table);
	foreach($table as $key=>$value)
	{
		$f[$key]=$value/$sum*100;
	}
	return $f;
}

function get_absolute_frequencies()
{
	$f=array();
	$xml=new music_xml_class("xml/psaume.xml");
	
	foreach($xml->parts as $part)
	{
		$measures=$part->measures;
		foreach($measures as $measure)
		{
			foreach($measure->notes as $note)
			{
				$f[$note->note_info->note_value]+=1;	
			}
		}
	}
	
	$f=frequency($f);
	print_r($f);
	return $f;
}

get_absolute_frequencies();
?>
