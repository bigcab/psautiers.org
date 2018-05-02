<?php
require_once("include/xml.php");
require_once("include/auth.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/bbcode.php");
$title=_("Carnets de Notes");
ob_start();




$retour = mysql_query('SELECT * FROM news ORDER BY id DESC LIMIT 0, 5');
while ($donnees = mysql_fetch_array($retour))
{


        $date=date('d/m/Y à H\hi', $donnees['timestamp']); 
        begin_box(stripslashes($donnees['titre'])." <em>le $date</em>","news".$donnees['timestamp']); ?> 
        <tr>
        <td>   
    	<p>
    	<?php
    	// On enlève les éventuels antislash PUIS on crée les entrées en HTML (<br />)
    	$contenu = bbcode(nl2br(stripslashes($donnees['contenu'])));
    	echo $contenu;
    	?>
    	</p>
    	</td>
    	</tr>
    	<?php
    	end_box();
}

dump_page("","default");


?>
