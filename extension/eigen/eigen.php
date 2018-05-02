<?php
$br = (php_sapi_name() == "cli")? "":"<br>";


$module = 'eigen';
$functions = get_extension_funcs($module);
echo "Functions available in the test extension:$br\n";
foreach($functions as $func) {
    echo $func."$br\n";
}
echo "$br\n";
$function = 'confirm_' . $module . '_compiled';
if (extension_loaded($module)) {
	
	echo "module loaded";
	$a = array( 0.0, 1.0 , 2.0,3.0);
	echo $a[0];
	$b = markov_measure(2,$a);
	
	print_r($b);
}

?>
