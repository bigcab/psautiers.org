<?php
require_once("include/xml.php");
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");

$title=_("Carnets de Notes");
ob_start();
is_authorized();

//-----------------------------------------------------
// Vérification 1 : est-ce qu'on veut poster une news ?
//-----------------------------------------------------
if ($_SESSION["admin"])
{
	begin_box("Gestion de nouvelles","news_box");
	if (isset($_POST['titre']) && isset($_POST['contenu']))
	{
	    $titre = addslashes($_POST['titre']);
	    $contenu = addslashes($_POST['contenu']);
	    // On vérifie si c'est une modification de news ou pas
	    if ($_POST['id_news'] == 0)
	    {
		// Ce n'est pas une modification, on crée une nouvelle entrée dans la table
		mysql_query("INSERT INTO news VALUES('', '" . $titre . "', '" . $contenu . "', '" . time() . "')");
	    }
	    else
	    {
		// On protège la variable "id_news" pour éviter une faille SQL
		$_POST['id_news'] = addslashes($_POST['id_news']);
		// C'est une modification, on met juste à jour le titre et le contenu
		mysql_query("UPDATE news SET titre='" . $titre . "', contenu='" . $contenu . "' WHERE id='" . $_POST['id_news'] . "'");
	    }
	}
	 
	//--------------------------------------------------------
	// Vérification 2 : est-ce qu'on veut supprimer une news ?
	//--------------------------------------------------------
	if (isset($_GET['supprimer_news'])) // Si on demande de supprimer une news
	{
	    // Alors on supprime la news correspondante
	    // On protège la variable "id_news" pour éviter une faille SQL
	    $_GET['supprimer_news'] = addslashes($_GET['supprimer_news']);
	    mysql_query('DELETE FROM news WHERE id=\'' . $_GET['supprimer_news'] . '\'');
	}
	?>
	<tr>
	<td>
	<table><tr>
	<th>Modifier</th>
	<th>Supprimer</th>
	<th>Titre</th>
	<th>Date</th>
	</tr>
	<?php
	$retour = mysql_query('SELECT * FROM news ORDER BY id DESC');
	while ($donnees = mysql_fetch_array($retour)) // On fait une boucle pour lister les news
	{
	?>
	<tr>
	<td><?php echo '<a href="rediger_news.php?modifier_news=' . $donnees['id'] . '">'; ?>Modifier</a></td>
	<td><?php echo '<a href="liste_news.php?supprimer_news=' . $donnees['id'] . '">'; ?>Supprimer</a></td>
	<td><?php echo stripslashes($donnees['titre']); ?></td>
	<td><?php echo date('d/m/Y', $donnees['timestamp']); ?></td>
	</tr>
	<?php
	} 
	?>
	</table>
	</td>
	</tr>
	<?php
	end_box();
	// Fin de la boucle qui liste les news
}
else
{
	echo "non autorisé";
}
dump_page();
?>


