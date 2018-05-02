<?php
require_once("include/auth.php");
require_once('include/page.php');
require_once('include/mysql.php');
require_once("include/log.php");
require_once("include/check_serv_dev.php");
is_authorized();
ob_start();
$title=_("Administration");

init_post_var("pseudo");
init_post_var("group_add");
init_post_var("del_pseudo");
init_get_var("del_from_group");
init_post_var("gerer_base");
init_post_var("actuel");
init_post_var("new1");
init_post_var("new2");
init_post_var("pass1");
init_post_var("pass2");
init_post_var("perm_group");
init_post_var("perm_others");

if ($_SESSION["admin"])
{
	?>
	<a href="liste_news.php?view=users"><?=_("Gestion des nouvelles")?></a> 
	<a href="rediger_news.php?view=users"><?=_("Rédiger une nouvelle")?></a> 
	<a href="users_groups.php?view=users"><?=_("Utilisateurs")?></a> 
	<a href="users_groups.php?view=groupes"><?=_("Groupes")?></a>
	<a href="modes.php"><?=_("Gestion des modes")?></a>
	<a href="export.php"><?=_("Téléchargement des fichiers d'export")?></a>
	<a href="virga.php" ><?=_("virga")?></a>
	<br /><br />
	<?php
}
	?>
	<a href="bugs.php"><?=_("Signaler un bug")?></a> 
	
	<?php
if ($_POST['actuel'] && $_POST['new1'] && $_POST['new2'])
{
	$r = requete("SELECT * FROM users WHERE pseudo = '".$_SESSION['pseudo']."' AND password = '".md5($_POST['actuel'])."'");

	if ($_POST['new1'] != $_POST['new2'])
	{
		begin_box(_("Erreur"),"erreur");
		msg(_("Les deux mots de passe ne correspondent pas !"));
		end_box();
	}
	else if (num_rows($r))
	{
		requete("UPDATE users SET password = '".md5($_POST['new1'])."' WHERE pseudo = '".$_SESSION['pseudo']."'");
		auth_log("USER ".$_SESSION['pseudo']." changed his password");
		begin_box(_("Mot de passe changé"),"pass_changed");
		end_box();
	}
	else 
	{
		begin_box(_("Erreur"),"error_box");
		msg(_("Mot de passe erroné !"));
		end_box();
	}

}


if ($_POST['pseudo'] && $_POST['pass1'] && $_POST['pass2'])
{
	if ($_SESSION['admin'])
	{
		if ($_POST['pass1'] != $_POST['pass2'])
		{
			begin_box(_("Erreur"),"error_box");
			msg(_("Les deux mots de passe ne correspondent pas !"));
			end_box();	
		}
		else
		{
			$r = requete("SELECT pseudo FROM users WHERE pseudo = '".$_POST['pseudo']."'");
			if (num_rows($r)>0)
			{
				begin_box(_("Cet identifiant est déja utilisé"),"user_exist");
				auth_log("USER ".$_SESSION["pseudo"]." tried to add an existant user {$_POST['pseudo']} with permissions {$_POST['permissions']}");
				end_box();
			}
			else
			{
				requete("INSERT INTO users(pseudo,password,permissions) VALUES('{$_POST['pseudo']}','".md5($_POST['pass1'])."','{$_POST['permissions']}')");
				begin_box(_("Utilisateur")." ".$_POST['pseudo']._(" ajouté!"),"user_added");
				auth_log("USER ".$_SESSION["pseudo"]." added USER {$_POST['pseudo']} with permissions {$_POST['permissions']}");
				end_box();
			}
		}
	}
	else
	{
	    if ($_POST['pass1'] != $_POST['pass2'])
		{
			begin_box(_("Erreur"),"error_box");
			msg(_("Les deux mots de passe ne correspondent pas !"));
			end_box();	
		}
		else
		{
			$r = requete("SELECT pseudo FROM users WHERE pseudo = '".$_POST['pseudo']."'");
			if (num_rows($r)>0)
			{
				begin_box(_("Cet identifiant est déja utilisé"),"user_exist");
				auth_log("USER ".$_SESSION["pseudo"]." tried to add an existant user {$_POST['pseudo']} with permissions 2");
				end_box();
			}
			else
			{
				requete("INSERT INTO users(pseudo,password,permissions) VALUES('{$_POST['pseudo']}','".md5($_POST['pass1'])."','2')");
				begin_box(_("Utilisateur")." ".$_POST['pseudo']._(" ajouté!"),"user_added");
				auth_log("USER ".$_SESSION["pseudo"]." added USER {$_POST['pseudo']} with permissions 2");
				end_box();
			}
		}
		
	}
}

if ($_POST['del_pseudo'])
{
	if ($_SESSION['admin'])
	{

		if ($_POST['del_pseudo'] == $_SESSION['pseudo'])
		{
			begin_box(_("Vous ne pouvez pas vous supprimer vous-même. Si vous souhaitez vraiment le faire, merci de contacter l'administrateur."),"self_destruct");
			end_box();
		}
		else
		{
			
			requete("DELETE FROM users WHERE pseudo = '".$_POST['del_pseudo']."'");
			auth_log("USER ".$_SESSION["pseudo"]." removed USER {$_POST['del_pseudo']}");
			begin_box(_("Utilisateur supprimé!"),"user_deleted");
			end_box();
		}
	}
	else
	{
		begin_box(_("Vous n'êtes pas autorisé à  supprimer un utilisateur!"),"unauthorized_action");
		auth_log("USER ".$_SESSION["pseudo"]." tried to remove USER {$_POST['del_pseudo']} without authorization");
		end_box();
	}
}


if ($_POST['group_add'])
{
	requete("INSERT INTO groupes (`id_user`, `id_groupe`) VALUES ( '".$_POST['group_add']."', '".$_SESSION['id']."')");
	$req=requete("SELECT pseudo FROM users WHERE id_user='{$_POST["group_add"]}'");
	$response=fetch_array($req);
	auth_log("USER ".$_SESSION["pseudo"]." added {$response["pseudo"]} into his group");

}

if ($_GET['del_from_group'])
{
	requete("DELETE FROM groupes WHERE id_user = '".$_GET['del_from_group']."' AND id_groupe = '".$_SESSION['id']."'");
	$req=requete("SELECT pseudo FROM users WHERE id_user='{$_GET["del_from_group"]}'");
	$response=fetch_array($req);
	auth_log("USER ".$_SESSION["pseudo"]." removed {$response["pseudo"]} from his group");
}


if ($_POST['gerer_base'] && $_POST['perm_group'] && $_POST['perm_others'])
{

	requete("UPDATE bases SET permissions_groupe = '".$_POST['perm_group']."', permissions_others = '".$_POST['perm_others']."' WHERE owner = '".$_SESSION['id']
	."' AND id_base = '".$_POST['gerer_base']."'");
	auth_log("USER ".$_SESSION["pseudo"]." set the permissions in his base {$_POST['gerer_base']}: group {$_POST['perm_group']} and other {$_POST['perm_others']}");
}


////// AFFICHAGE PAGE ///////
begin_box(_("Vous êtes identifié en tant que")." ".$_SESSION['pseudo'],"logged_in");
	if ($_SESSION['admin']) msg(_("Vous possédez les droits d'administration."));
end_box();

?>
<form action="" method="post">
	<?php		
	begin_box_js(_("Modifier votre mot de passe"),"mdp");
	?>
	 
	<tr><td><?=_("Mot de passe actuel")?> : </td><td><input type="password" name="actuel" /></td></tr>
	<tr><td><?=_("Mot de passe souhaité")?> : </td><td><input type="password" name="new1" /></td></tr> 
	<tr><td><?=_("Mot de passe souhaité (vérification)")." "?></td><td><input type="password" name="new2" /></td></tr>
	<tr><td></td><td><input type="submit" /></td></tr>
	<?php
	end_box();
	?>
</form>

<form action="admin.php#gerer_groupe" method="post">
	<?php
	begin_box_js(_("Gérer votre groupe"),"gerer_groupe");
	$r = requete("SELECT u2.pseudo,u2.id_user FROM users u INNER JOIN groupes g ON g.id_groupe=u.id_user INNER JOIN users u2 ON u2.id_user=g.id_user WHERE u.pseudo='".$_SESSION["pseudo"]."'");
	while ($data = fetch_array($r))
	{
		msg("".$data['pseudo']." : <a href='?del_from_group=".$data['id_user']."#gerer_groupe'>Retirer de votre groupe</a>");
	}
	?>
	 
	<tr><td><?=_("Ajouter l'utilisateur")?> :
	<select name="group_add">
	<?php
	$r = requete("SELECT id_user,pseudo FROM users");
	while ($data = fetch_array($r))
	{
		echo "<option value='".$data['id_user']."'>".$data['pseudo']."</option>";
	}
	?>
	</select> <?=_("à votre groupe")?>
	</td></tr>
	<tr><td><input type="submit" value="OK" /></td></tr>

	<?php
	end_box();
	?>
</form>



<?php
$r = requete("SELECT id_base,permissions_others,permissions_groupe,nom_base FROM bases WHERE owner = '".$_SESSION['id']."'");
while ($data = fetch_array($r))
{
    ?>
    <form method="post" action="#gerer_base">
        <?php
	    begin_box_js(_("Gérer la base :")." "." ".$data['nom_base'],$data['id_base']);
	    ?>
	
	    <tr>
		    <td>
			    <input type="hidden" name="gerer_base" value="<?php echo $data['id_base']; ?>" />
			    <?=_("Les utilisateurs de votre groupe peuvent :")." "?>
		    </td>
		    <td>
			    <select name="perm_group">
			    <option value="1" <?php if ($data['permissions_groupe'] == 1) echo 'selected="selected"'; ?>><?=_("Consulter la base")?></option>
			    <option value="2" <?php if ($data['permissions_groupe'] == 2) echo 'selected="selected"'; ?>><?=_("Ajouter des pièces dans la base (et la consulter)")?></option>
			    <option value="3" <?php if ($data['permissions_groupe'] == 3) echo 'selected="selected"'; ?>><?=_("Ne rien faire")?></option>
			    </select>
		    </td>
	    </tr>
	    <tr><td><?=_("Les utilisateurs ne faisant pas partie de votre groupe peuvent :")." "?> </td><td>
	    <select name="perm_others">
	    <option value="1" <?php if ($data['permissions_others'] == 1) echo 'selected="selected"'; ?>><?=_("Consulter la base")?></option>
	    <option value="2" <?php if ($data['permissions_others'] == 2) echo 'selected="selected"'; ?>><?=_("Ajouter des piéšces dans la base (et la consulter)")?></option>
	    <option value="3" <?php if ($data['permissions_others'] == 3) echo 'selected="selected"'; ?>><?=_("Ne rien faire")?></option>
	    </select>
	    </td></tr>
	    <tr><td></td><td><input type="submit" value="OK" /></td></tr>
	    <?php
	    end_box();
	    ?>
	</form>
	<?php
}
?>




<form action="#ajout_user" method="post">
	<?php
	if ($_SESSION['admin'])
	{
		begin_box_js(_("Créer un utilisateur"),"ajout_user");
		?>
	 
	
		<tr><td><?=_("Identifiant :")." "?></td><td><input type="text" name="pseudo" /></td></tr>
		<tr><td><?=_("Mot de passe souhaité :")." "?></td><td><input type="password" name="pass1" value="" /></td></tr> 
		<tr><td><?=_("Mot de passe souhaité (vérification):")?> </td><td><input type="password" name="pass2" /></td></tr>
		<tr><td><acronym title="<?=_("Administrer : l'utilisateur pourra effectuer TOUTES les tâches d'administration (i.e. ajouter des utilisateurs, en supprimer, ajouter des bases, etc.)")?>"><?=_("Permissions")?></acronym></td><td><select name="permissions"><option value="2"><?=_("Utilisateur")?></option><option value="1"><?=_("Administrer")?></option></select></td></tr>
		<tr><td></td><td><input type="submit" value="<?=_("Valider")?>" /></td></tr>

		<?php
		end_box();

	
	} //end if($_SESSION['admin']
	else
	{
	    begin_box_js(_("Créer un utilisateur"),"ajout_user");
		?>
	 
	
		<tr><td><?=_("Identifiant :")." "?></td><td><input type="text" name="pseudo" /></td></tr>
		<tr><td><?=_("Mot de passe souhaité :")." "?></td><td><input type="password" name="pass1" value="" /></td></tr> 
		<tr><td><?=_("Mot de passe souhaité (vérification):")?> </td><td><input type="password" name="pass2" /></td></tr>
		<tr><td></td><td><input type="submit" value="<?=_("Valider")?>" /></td></tr>

		<?php
		end_box();
	}
	?>
</form>
<?php

dump_page();

?>
