<?php
date_default_timezone_set("Europe/Paris");
// Fonctions pour simplifier les requêtes MySQL (gestion des erreurs plus fine possible...)

// Fichier contenant les constantes MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD & MYSQL_DB
// Cela permet une testion plus souple de la BDD : il suffit de changer le fichier config.php en fonction du serveur 
require_once('include/config.php');
require_once("include/auth.php");
require_once('include/log.php');
// Le nombre de requêtes (pour les afficher à la fin ?)
$requetes=0;


function htmlspecialchars_array($arr = array()) {
    $rs =  array();
    while(list($key,$val) = each($arr)) {
        if(is_array($val)) {
            $rs[$key] = htmlspecialchars_array($val);
        }
        else {
            $rs[$key] = htmlentities($val, ENT_QUOTES,'UTF-8');

        }   
    }
    return $rs;
}

$_POST=htmlspecialchars_array($_POST);
$_GET=htmlspecialchars_array($_GET);
function init_db()
{

	@mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD) 
		or erreur(_('Impossible de se connecter à la base MySQL :')." "/*.mysql_error()*/);
		
	@mysql_select_db(MYSQL_DB) 
		or erreur(_('Impossible de sélectionner la base de donées :')." "/*.mysql_error()*/);
	// Pour empêcher les Injections SQL ( injections = le Mal )
	//j'ai modifié ton array map car il n'accepte pas les tableaux
	//je fais donc une boucle sur les $_POST
	//pour voir chaque valeur (c'etait un problème avec les select 
	//multiples)
	


	foreach($_POST as $valeur)
	{
		    if (is_array($valeur))
		    {
		            $valeur=array_map('mysql_real_escape_string',$valeur);
	#	                $valeur=array_map('htmlentities',$valeur);
	#	                $valeur=array_map('htmlspecialchars',$valeur);
		    }
		    else
		    {
		            $valeur=mysql_real_escape_string($valeur);
		            
		    }
	}
	foreach($_SESSION as $valeur)
	{
		    if (is_array($valeur))
		    {
		            $valeur=array_map('mysql_real_escape_string',$valeur);
	#	                $valeur=array_map('htmlentities',$valeur);
	#	                $valeur=array_map('htmlspecialchars',$valeur);
		    }
		    else
		    {
		    	if (is_string($valeur))
		    	{
		            $valeur=mysql_real_escape_string($valeur);
		        }    
		    }
	}
	foreach($_GET as $valeur)
	{
		    if (is_array($valeur))
		    {
		            $valeur=array_map('mysql_real_escape_string',$valeur);
	#	                $valeur=array_map('htmlentities',$valeur);
	#	                $valeur=array_map('htmlspecialchars',$valeur);
		    }
		    else
		    {
		            $valeur=mysql_real_escape_string($valeur);
		    }
	}
	mysql_set_charset("utf8");
	requete("SET NAMES 'utf8';");
}

function requete($q)
{
	global $requetes;
	mysql_log($q);
	// Attention ! Afficher les requêtes en cas d'erreur peut constituer une faille de sécurité ! Ne pas oublier d'enlever cela quand
	// le développement sera fini ! (Pour le moment on garde car ça permet un débuggage facile...)
	$r = @mysql_query($q) 
		or erreur(_('Erreur dans une requête MySql')." ".'<br />La requête était : '.$q.'.<br />MySQL a renvoyé : '.mysql_error()."<br />");
	$requetes++;
	
	return $r;
}

// Ces deux fonctions peuvent être améliorées pour gérer les erreurs notamment.
// Pour le moment, elles permettent surtout de raccourcir le nom (!)
function num_rows($q)
{
	return @mysql_num_rows($q);
}

function fetch_array($q)
{
	return @mysql_fetch_assoc($q);
}	

?>
