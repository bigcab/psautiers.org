<?php
require_once("include/xml.php");
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/message_box.php");
$title=_("Carnets de Notes");
ob_start();
is_authorized();

//-----------------------------------------------------
// Vérification 1 : est-ce qu'on veut poster une news ?
//-----------------------------------------------------
if ($_SESSION["admin"])
{
    ?>
    <form action="liste_news.php" method="post">
    <?php
    begin_box("Gestion de nouvelles","news_box");
    if (isset($_GET['modifier_news'])) // Si on demande de modifier une news
    {
        // On protège la variable "modifier_news" pour éviter une faille SQL
        $_GET['modifier_news'] = mysql_real_escape_string(htmlspecialchars($_GET['modifier_news']));
        // On récupère les infos de la news correspondante
        $retour = mysql_query('SELECT * FROM news WHERE id=\'' . $_GET['modifier_news'] . '\'');
        $donnees = mysql_fetch_array($retour);
        
        // On place le titre et le contenu dans des variables simples
        $titre = stripslashes($donnees['titre']);
        $contenu = stripslashes($donnees['contenu']);
        $id_news = $donnees['id']; // Cette variable va servir pour se souvenir que c'est une modification
    }
    else // C'est qu'on rédige une nouvelle news
    {
        // Les variables $titre et $contenu sont vides, puisque c'est une nouvelle news
        $titre = '';
        $contenu = '';
        $id_news = 0; // La variable vaut 0, donc on se souviendra que ce n'est pas une modification
    }
    ?>
    <tr><td>
    <p>Titre : <input type="text" size="30" name="titre" value="<?php echo $titre; ?>" /></p>
    <?php
    message_box($contenu);
    ?>
    <input type="hidden" name="id_news" value="<?=$id_news?>" />
    <input type="submit" value="Envoyer" />
    </p>
    </td></tr>
    <?php
    end_box();
    ?>
    </form>
    <?php
}
else
{
	echo _("non autorisé");
}
dump_page();
?>


