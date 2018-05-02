<?php
require_once("include/mysql.php");
//on est obligé d'inclure ce fichier pour toutes les taches d'administration

/*
authorization group

read = 1
write + read = 2
nothing = 3
*/


session_start();
if(empty($_SESSION["authorized"]))
{
	$_SESSION["authorized"]="";
}

if(empty($_SESSION["pseudo"]))
{
	$_SESSION["pseudo"]="";
}

if($_SESSION["pseudo"]=="root")
{
	ini_set('display_errors', 1);
	ini_set('log_errors', 1); 
	error_reporting(E_ALL);
}

function is_authorized()
{
        if (!isset($_SESSION['authorized']) || !$_SESSION['authorized'])
        {
                header("Location: login.php");
        }
}

function is_admin()
{
                if ($_SESSION['authorized']=="authorized")
                {
                        return 1;
                }
                else
                {
                        return 0;
                }
}

// Verifie si l'utilisateur est autorisé à écrire sur la base $id_base
// La fonction marche parfaitement
// FIXME la fonction ne marche pas parfaitement, et même en rajoutant WHERE b.id_base = '$id_base' ça ne marche pas :(
// C'est à cause du fait que la requête ne marche que s'il y a des gens dans le groupe, problème IL ME SEMBLE résolu, en mettant un LEFT JOIN
function write_in_base($id_base)
{
    global $dev_server_bool;
    if(!$dev_server_bool)
    {
        return false;
    }
	if($_SESSION["admin"])
	{
		return true;
	}
	
	$req=requete( "SELECT owner FROM bases b LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner WHERE b.id_base = '$id_base'  AND ( b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND b.permissions_groupe='2') OR (b.permissions_others='2') )");
	if(num_rows($req)==0)
	{
		return false;
	}
	else
	{
		return true;
	}
}


// Verifie si l'utilisateur a le droit de consulter la base
// FIXME idem
function read_in_base($id_base)
{
    global $dev_server_bool;
    if(!$dev_server_bool)
    {
        return true;
    }
	if($_SESSION["admin"])
	{
		return true;
	}
	
	$req=requete("SELECT owner FROM bases b LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner WHERE  (b.id_base = '$id_base' AND  ( b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND (b.permissions_groupe='1' OR b.permissions_groupe='2')) OR (b.permissions_others='2' OR b.permissions_others='1') ) ) ");
	if(num_rows($req)==0)
	{
		return false;
	}
	else
	{
		return true;
	}
}

// Fonction qui prend l'id de la base et regarde les autorisations
// de l'user sur la base
function read_in_recueil($id_recueil)
{
    global $dev_server_bool;
    if(!$dev_server_bool)
    {
        return true;
    }
	if($_SESSION["admin"])
	{
		return true;
	}
	
	$req=requete("SELECT owner FROM bases b LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner INNER JOIN recueils r ON r.id_base=b.id_base WHERE r.id_recueil='".$id_recueil."' AND (b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND (b.permissions_groupe='1' OR b.permissions_groupe='2')) OR (b.permissions_others='2' OR b.permissions_others='1'))");
	if(num_rows($req)==0)
	{
		return false;
	}
	else
	{
		return true;
	}
}

function write_in_recueil($id_recueil)
{
    global $dev_server_bool;
    if(!$dev_server_bool)
    {
        return false;
    }
	if($_SESSION["admin"])
	{
		return true;
	}
	
	$req=requete("SELECT owner FROM bases b LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner INNER JOIN recueils r ON r.id_base=b.id_base WHERE r.id_recueil='".$id_recueil."' AND (b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND (b.permissions_groupe='2')) OR (b.permissions_others='2') )");
	if(num_rows($req)==0)
	{
		return false;
	}
	else
	{
		return true;
	}
}


function write_in_piece($id_piece)
{
    global $dev_server_bool;
    if(!$dev_server_bool)
    {
        return false;
    }
	if($_SESSION["admin"])
	{
		return true;
	}
	
	$req=requete("SELECT owner FROM bases b LEFT OUTER JOIN groupes g ON g.id_groupe=b.owner INNER JOIN recueils r ON r.id_base=b.id_base INNER JOIN table_matieres tm ON tm.id_recueil=r.id_recueil INNER JOIN pieces p ON p.id_piece=tm.id_piece WHERE p.id_piece='".$id_piece."' AND (b.owner='".$_SESSION["id"]."' OR  (g.id_user='".$_SESSION["id"]."' AND (b.permissions_groupe='2')) OR (b.permissions_others='2') )");
	if(num_rows($req)==0)
	{
		return false;
	}
	else
	{
		return true;
	}
}


?>
