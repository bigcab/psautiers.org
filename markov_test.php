<?php


$matrice = array( 0.5, 0.5 , 0,	1);
$b =markov_measure(2, $matrice);
if($b ===FALSE)
{
	echo "problem";
}
else
{
	print_r($b);
}

$a = array( "A" => array("A" => 4 , "B" => 5 , "C" => 1) , "B" =>array("A" => 1 , "B" => 2), "C" =>array("A" => 1));
#$info=(parse_data_markov_chaine($a));
#print_r($info[1]);
#print_r(markov_measure($info[0],$info[1]));
print_r(do_all_markov($a));

?>
