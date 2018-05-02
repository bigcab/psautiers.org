<?php

$notes_blanches=array("C","D","E","F","G","A","B");
	$notes_noires=array("+1C"=>27,"-1D"=>43,"+1D"=>72,"-1E"=>88,"+1F"=>162,"-1G"=>178,"+1G"=>207,"-1A"=>223,"+1A"=>252,"-1B"=>268);

function octave($x,$octave,$octave_number)
{
	global $notes_blanches;
	global $notes_noires;
	$s=$octave_number;
	for ($i=0;$i<count($notes_blanches);$i++)
	{
		?>
		<div  
		onmousedown="this.style.backgroundColor = 'red';play('<?php echo $notes_blanches[$i].$s ?>.mp3');"  
		onmouseup="this.style.backgroundColor = 'white';" 
		onmouseover="this.style.backgroundColor = 'blue';" 
		onmouseout="this.style.backgroundColor = 'white';" 
		style="position: relative;  left:<?php echo $x+$i*45; ?>px; top:-<?php echo $i*200+$octave*2900 ?>px;" 
		class="blanche" onclick="affiche_note('<?php echo $notes_blanches[$i].$s ?>')">
			&nbsp;
		</div>
		<?php
	}
	$i=0;
	while($note=current($notes_noires))
	{
		$key=key($notes_noires);
		
		?>
		<div  onmousedown="this.style.backgroundColor = 'red';play('<?php echo $key.$s ?>.mp3');"  
		onmouseup="this.style.backgroundColor = 'black';" 
		onmouseover="this.style.backgroundColor = 'blue';" 
		onmouseout="this.style.backgroundColor = 'black';" 
		style="position: relative;  left:<?php echo $note+$x; ?>px; top:-<?php echo 1400+$i*150+$octave*2900 ?>px;" 
		class="noire" onclick="affiche_note('<?php echo $key.$s ?>')">
			&nbsp;
		</div>
		<?php
		next($notes_noires);
		$i+=1;
	}
	reset($notes_noires);
}


// nombre d'octaves et aussi l'octave par laquelle on commence
function clavier($nb_octaves=2,$begin=4)
{
	?>
	<script  type="text/javascript"><!--
	function affiche_note(note)
	{
		parent.update_champ(note);
	}
	--> </script>
	<?php
	$x=$nb_octaves+$begin;
	for($i=0;$i<$nb_octaves;$i++)
	{
		$octave_number=$begin+$i;
		octave($i*315,$i,$octave_number);
	}
}
?>
