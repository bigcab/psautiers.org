<?php
require_once("include/auth.php");
require_once('include/page.php');
require_once('include/mysql.php');
require_once("include/log.php");

ob_start();
$title=_("Connexion");


if ( (isset($_GET['action']))&&($_GET['action'] == "login"))
{

	$r = requete("SELECT * FROM users WHERE pseudo = '{$_POST['pseudo']}' AND password = '".md5($_POST['password'])."'");
	if ($data = fetch_array($r))
	{
		//requete pour les paranos => j'adore !!!!!!!!!!!
		requete("UPDATE users SET ip='".$_SERVER['REMOTE_ADDR']."' WHERE id_user='".$data["id_user"]."'");
		auth_log("USER ".$_POST["pseudo"]." logged in with address ".$_SERVER["REMOTE_ADDR"]);
		$_SESSION['authorized'] = "authorized";
		$_SESSION['pseudo'] = $data['pseudo'];
		$_SESSION['id'] = $data['id_user'];
		$_SESSION['admin'] = false;
		switch ($data['permissions'])
		{
			case 777: //compatibilité ascendante :)
			case 1: //administrer
			$_SESSION['admin'] = true;
			
		}
	}
}


if ($_SESSION['authorized'] == "authorized")
{
	//on redirige vers la page d'administration si l'utilisateur est loggué
        header("Location: index.php");
        return 0;
	
}
else if (isset($_GET['action']))
{
        begin_box(_("Mot de passe incorrect"));
        msg(_("Mot de passe incorrect"));
        auth_log("A client with ip address ".$_SERVER["REMOTE_ADDR"]." failed to log in with user ".$_POST["pseudo"]);
        end_box();
}




if (!isset($_SESSION['authorized']) || !$_SESSION['authorized'] || ($_SESSION["authorized"] != "authorized"))
{
        echo '<form action="?action=login" method="post">';
        begin_box(_("Identification requise"),"identification_box");
	?>
	
	<tr>
	        <td>Identifiant : </td>
	        <td><input type="text" name="pseudo"  value=""/></td>
	</tr>
	<tr>
	        <td>Mot de passe : </td>
	        <td><input type="password" name="password" /></td>
	</tr>
	<tr>
	        <td></td><td align="right"><input type="submit" value="OK" /></td>
	</tr>
	
	<?php
	end_box();
	echo '</form>';
}



$contenu =  ob_get_contents(); 
ob_end_clean(); 
afficher_page($title,$contenu);
?>
