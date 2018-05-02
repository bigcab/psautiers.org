<?php
require_once("include/auth.php");
require_once('include/page.php');
require_once('include/mysql.php');
require_once("include/log.php");
require_once("include/global.php");
is_authorized();
ob_start();
$title=_("Utilisateurs et groupes");

//pour voir tous les groupes
function show_groupes()
{
	begin_box(_("Affichage des groupes existants"));
	
	$sql="SELECT  DISTINCT  id_groupe,pseudo FROM groupes INNER JOIN users u ON id_groupe=u.id_user";
	
	$req=requete($sql);
	if(num_rows($req)==0)
	{
		msg(_("Aucun groupe existant"));
		end_box();
		return;
	}	
	while($response=fetch_array($req))
	{
		msg("<a href='?view=groupes&id_groupe=".$response["id_groupe"]."'>".$response["pseudo"]."</a>");
	}
	end_box();
}


// pour voir un groupe en particulier
function show_groupe($id_groupe)
{
	global $users_table_correspond;
	$req=requete("SELECT pseudo FROM users WHERE id_user='".$id_groupe."'");
	$response=fetch_array($req);
	begin_box(_("Affichage du groupe")." ".$response["pseudo"]);
	if($_SESSION["admin"])
	{
		$vars=array("ip","permissions");
		$sql="SELECT DISTINCT  g.id_groupe,u.id_user,u.pseudo,ip,permissions  FROM groupes g "
			."INNER JOIN users u ON g.id_user=u.id_user "
			."WHERE id_groupe='".$id_groupe."'";
	}
	else
	{
		$vars=array();
		$sql="SELECT DISTINCT g.id_groupe,u.id_user,u.pseudo FROM groupes g "
			."INNER JOIN users u ON g.id_user=u.id_user "
			."WHERE id_groupe='".$id_groupe."'";
	}
	$req=requete($sql);
	?>
	<tr>
		<td>
			<table style="border: 2px solid rgb(207,222,250); border-collapse: collapse" border="1">
				<th><?=_("Utilisateur")?></th>
				<?php
				foreach($vars as $var)
				{
					?>
					<th><?php echo $users_table_correspond[$var]?></th>
					<?php
				}
				?>
				<th><?=_("Supprimer")?></th>
				<?php
				if($_SESSION["admin"])
				{
					while($response=fetch_array($req))
					{
						?>
						<tr>
							<td><a href="?view=users&id_user=<?php echo $response["id_user"]?>"><?php echo $response["pseudo"]?></a></td>
							<?php
							foreach($vars as $var)
							{
								?>
								<td><?php echo $response[$var]?></th>
								<?php
							}
							?>
							<td><a href="update.php?del_user=<?php echo $response["id_user"]?>"><img src='./images/design/remove.png'  alt='Supprimer' title='Supprimer' width='20' heigth='20'/></a></td>
						</tr>
						<?php
					}
				}
				else
				{
					while($response=fetch_array($req))
					{
						?>
						<tr>
							<td><a href="?view=users&id_user=<?php echo $response["id_user"]?>"><?php echo $response["pseudo"]?></a></td>
							<?php
							foreach($vars as $var)
							{
								?>
								<td><?php echo $response[$var]?></th>
								<?php
							}
							?>
						</tr>
						<?php
					}
				}
				?>
		
			</table>
		</td>
	</tr>
	<?php
	end_box();
}


function show_users()
{
	global $users_table_correspond;
	begin_box(_("Affichage de tous les utilisateurs")."")." ";
	if($_SESSION["admin"])
	{
		$vars=array("ip","permissions");
		$sql="SELECT DISTINCT  id_user,pseudo,ip,permissions FROM users ";
	}
	else
	{
		$vars=array();
		$sql="SELECT DISTINCT  id_user,pseudo FROM users ";
	}
	
	$req=requete($sql);
	?>
	<tr>
		<td>
			<table style="border: 2px solid rgb(207,222,250); border-collapse: collapse" border="1">
				<tr>
					<th><?=_("Pseudo")?></th>
					<?php
					foreach($vars as $var)
					{
						?>
						<th><?php echo $users_table_correspond[$var]?></th>
						<?php
					}
					?>
					<th><?=_("Supprimer")?></th>
					<th><?=_("Changer les permissions")?></th>
				</tr>
				<?php
				if($_SESSION["admin"])
				{
					while($response=fetch_array($req))
					{
						?>
						<tr>
							<td><a href="?view=users&id_user=<?php echo $response["id_user"]?>"><?php echo $response["pseudo"]?></a></td>
							<?php
							foreach($vars as $var)
							{
								?>
								<td><?php echo $response[$var]?></th>
								<?php
							}
							?>
							<td><a href="update.php?del_user=<?php echo $response["id_user"]?>"><img src='./images/design/remove.png'  alt='Supprimer' title='Supprimer' width='20' heigth='20'/></a></td>
							<td><a href="update.php?perm=<?php echo ($response['permissions'] == 1)?2:1 ?>&change_permissions_user=<?php echo $response["id_user"]?>"><img src='./images/design/edit.png' /></a></td>
						</tr>
						<?php
					}
				}
				else
				{
					while($response=fetch_array($req))
					{
						?>
						<tr>
							<td><a href="?view=users&id_user=<?php echo $response["id_user"]?>"><?php echo $response["pseudo"]?></a></td>
							<?php
							foreach($vars as $var)
							{
								?>
								<td><?php echo $response[$var]?></th>
								<?php
							}
							?>
						</tr>
						<?php
					}
				}
				?>
		
			</table>
		</td>
	</tr>
	<?php
	end_box();
}


function show_user($id_user)
{
	global $users_table_correspond;
	$req=requete("SELECT pseudo FROM users WHERE id_user='".$id_user."'");
	$response=fetch_array($req);
	begin_box(_("Affichage de l'utilisateur")." ".$response["pseudo"]);
	if($_SESSION["admin"])
	{
		$vars=array("pseudo","ip","permissions");
		$sql="SELECT DISTINCT id_user,pseudo,ip,permissions FROM users WHERE id_user='$id_user'";
	}
	else
	{
		$vars=array("pseudo");
		$sql="SELECT DISTINCT  id_user,pseudo FROM users WHERE id_user='$id_user'";
	}
	
	$req=requete($sql);
	?>
	<tr>
		<td>
			<table style="border: 2px solid rgb(207,222,250); border-collapse: collapse" border="1">

				<?php
				foreach($vars as $var)
				{
					?>
					<th><?php echo $users_table_correspond[$var]?></th>
					<?php
				}
				?>
					<th><?=_("Supprimer")?></th>
					<th><?=_("Changer les permissions")?></th>
				<?php
				if($_SESSION["admin"])
				{
					while($response=fetch_array($req))
					{
						?>
						<tr>
							<?php
							foreach($vars as $var)
							{
								?>
								<td><?php echo $response[$var]?></th>
								<?php
							}
							?>
							<td><a href="update.php?del_user=<?php echo $response["id_user"]?>"><img src='./images/design/remove.png'  alt='Supprimer' title='Supprimer' width='20' heigth='20'/></a></td>
							<td><a href="update.php?perm=<?php echo ($response['permissions'] == 1)?2:1 ?>&change_permissions_user=<?php echo $response["id_user"]?>"><img src='./images/design/edit.png' /></a></td>
						</tr>
						<?php
					}
				}
				else
				{
					while($response=fetch_array($req))
					{
						?>
						<tr>
							<?php
							foreach($vars as $var)
							{
								?>
								<td><?php echo $response[$var]?></th>
								<?php
							}
							?>
						</tr>
						<?php
					}
				}
				?>
		
			</table>
		</td>
	</tr>
	<?php
	end_box();
}

if($_SESSION["admin"])
{
        echo "<a href='?view=users'>"._("Utilisateurs")."</a> ";
        echo "<a href='?view=groupes'>"._("Groupes")."</a>"; 
	switch($_GET["view"])
	{
		case "users":
			if(isset($_GET["id_user"]))
			{
				show_user($_GET["id_user"]);
			}
			else
			{
				show_users();
			}
		break;
		case "groupes":
			if(isset($_GET["id_groupe"]))
			{
				show_groupe($_GET["id_groupe"]);
			}
			else
			{
				show_groupes();
			}
		break; 
	}

}
dump_page();
?>
