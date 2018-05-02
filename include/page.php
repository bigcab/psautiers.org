<?php

require_once("include/mysql.php");
require_once("include/config.php");
init_db();

#default timezone paris 
date_default_timezone_set("Europe/Paris");

#initialisation des variables:
$jquery="";

/* Utilise l'encodage interne UTF-8 */
mb_internal_encoding("UTF-8");


if(empty($_SESSION['id']))
{
	$_SESSION["id"]="unknown";
}

if(empty($_SESSION['pseudo']))
{
	$_SESSION["pseudo"]="";
}

if(isset($_GET['lang']))
{
	$_SESSION['lang']=$_GET['lang'];
}
else if(empty($_SESSION['lang']))
{
	$_SESSION['lang']="fr";
}
$langage = $_SESSION['lang'];
if($langage=="en")
{
	$langage="en_GB.UTF-8";
	putenv("LANG=$langage"); 
    setlocale(LC_ALL, $langage); 
}
else
{
    $langage="fr_FR.UTF-8";
    putenv("LANG=$langage"); 
    setlocale(LC_ALL, $langage);
}





$modes=list_modes();
if(!empty($_GET["mode"]))
{
	if (array_search($_GET["mode"],$modes) !== FALSE )
	{
		$_SESSION["mode"]=$_GET["mode"];
	}
}
else if(empty($_SESSION["mode"]))
{
	$_SESSION["mode"]="default";
}
if(isset($_GET["isolated"]))
{
    if($_GET["isolated"]=="true")
    {
        if(isset($_GET["id_base"]))
        {
            $_SESSION["isolated"]=true;
            $_SESSION["isolated_base"]=$_GET["id_base"];
            //select the mode
            $req=requete("SELECT mode FROM bases WHERE id_base='{$_GET["id_base"]}'");
            if(num_rows($req)!=0)
            {
                $response=fetch_array($req);
                $_SESSION["mode"]=$response["mode"];
            }
        }
        
    }
    else
    {
        $_SESSION["isolated"]=false;
    }
}
else if(empty($_SESSION["isolated"]))
{
	$_SESSION["isolated"]=false;
}
if (empty($_SESSION['mode']))
{
	$_SESSION["mode"]="default";
}


if ($_SESSION["mode"]!="default")
{
    $traduction_dir=get_traduction_directory($_SESSION["mode"]);
    bindtextdomain("traduction","./locale/traductions/".$traduction_dir);
    textdomain("traduction");
}
else
{
    bindtextdomain("traduction", "./locale"); 
    textdomain("traduction"); 
}
$default_body_background_color="rgb(204,220,255)";
$default_banner="./images/design/ban5.jpg";


function check_skins($tableau)
{
	foreach ($tableau as $key=>$default)
	{
		if(!isset($_SESSION[$key]))
		{
			$_SESSION[$key]=$default;
		}
	}
	
}

$skins=array(
			"body_background_color" 	=> $default_body_background_color,
			"banner" => $default_banner
			);
check_skins($skins);



#maintenant on s'occupe des histoires de customisations
if ((isset($_GET["custom"]))&& ($_GET["custom"]!="default"))
{
#    echo $_GET["custom"];
	# $_GET["custom"] contient l'id d'une base
	# dans la base body_background_color est sous la forme rgb(a,b,c)
	$req=requete("SELECT banner,body_background_color FROM bases WHERE id_base='{$_GET['custom']}'");
	if(num_rows($req)!= 0)
	{
		$response=fetch_array($req);
		$_SESSION["body_background_color"]=$response["body_background_color"];
		$_SESSION["banner"] = "".$response["banner"];
		$_SESSION["custom"]=$_GET['custom'];
	}
	else
	{
		$_SESSION["custom"]="default";
		$_SESSION["body_background_color"]=$default_body_background_color;
		$_SESSION["banner"] = $default_banner;
	}
}


if(!isset($_SESSION["custom"]))
{
	#valeur par default
	$_SESSION["custom"]="default";
	$_SESSION["body_background_color"]=$default_body_background_color;
	$_SESSION["banner"] = $default_banner;
}



$debut=microtime(true);



require_once("include/auth.php");
require_once("include/config.php");



function msg_consultation_titre_recueil($string)
{
	msg("<div class=''>"._("Titre du recueil :")." ".$string."</div>");
}

function msg_consultation_titre_piece($string)
{
	msg("<div class=''>"._("Titre de la pièce :")." ".$string."</div>");
}

function msg_consultation_nom_base($string)
{
	msg("<div class=''>"._("Appartient à la base :")." ".$string."</div>");
}

function link_to($msg,$href)
{
	return "<a href='$href'>$msg</a>";
}

//Correspondance
$menu=array(
				"index.php"=>_("Page Principale"),
				"recherche.php"=> _("Recherche"),
				"show.php"=>_("Consultation"),
				"login.php"=>_("Connexion"),
				"add.php"=>_("Ajout"),
				"admin.php"=>_("Administration"),
				"logout.php"=>_("Déconnexion")
				);
				
//pour les onglets
function menu_onglets($onglets)
{
	global $menu;
	foreach($onglets as $onglet)
			{
				if("/".$onglet==$_SERVER['SCRIPT_NAME'])
				{
					echo "<li onclick='change_current_menu(this)' id='current'><a class='navigation' href='".$onglet."'>".$menu[$onglet]."</a></li>";
				}
				else
				{
					echo "<li onclick='change_current_menu(this)' ><a class='navigation' href='".$onglet."'>".$menu[$onglet]."</a></li>";
				}
			}
}

function dump_page($jquery="",$doctype="default",$before="")
{
		global $title;
		
		$contenu =  ob_get_contents(); 
		ob_end_clean(); 
		afficher_page($title,$contenu,$doctype,$jquery,$before);
		return ;
}

//fonction pour afficher un truc joli et bien placé
//cette fonction crée un tableau
//on doit donc ecrire des tr et des td après cette balise
//cette fonction doit se terminer par end_box
//sinon problèmes
// id non obligatoire => gestion des ancres
function begin_box($title ,$id="",$message_after="")
{
	$id=str_replace(" ","_",$id);
	$id=str_replace("/","_",$id);
	$id=str_replace("(","_",$id);
	$id=str_replace(")","_",$id);
	
        ?>
                                        
        <h2 id="<?php echo $id ?>"><?php echo $title ?><?php echo $message_after?></h2>
                                       
                                
                       
                                        <table class="box_table_contenu">
        
        <?php
}




//Connaître le navigateur du client

if ((preg_match("/Nav/", getenv("HTTP_USER_AGENT"))) || (preg_match("/Gold/", getenv("HTTP_USER_AGENT"))) ||
(preg_match("/X11/", getenv("HTTP_USER_AGENT"))) || (preg_match("/Mozilla/", getenv("HTTP_USER_AGENT"))) ||
(preg_match("/Netscape/", getenv("HTTP_USER_AGENT")))
AND (!preg_match("/MSIE/", getenv("HTTP_USER_AGENT"))) AND (!preg_match("/Konqueror/", getenv("HTTP_USER_AGENT"))))
  $navigateur = "Netscape";
elseif (preg_match("/Opera/", getenv("HTTP_USER_AGENT")))
  $navigateur = "Opera";
elseif (preg_match("/MSIE/", getenv("HTTP_USER_AGENT")))
  $navigateur = "MSIE";
elseif (preg_match("/Lynx/", getenv("HTTP_USER_AGENT")))
  $navigateur = "Lynx";
elseif (preg_match("/WebTV/", getenv("HTTP_USER_AGENT")))
  $navigateur = "WebTV";
elseif (preg_match("/Konqueror/", getenv("HTTP_USER_AGENT")))
  $navigateur = "Konqueror";
elseif ((preg_match("/bot/", getenv("HTTP_USER_AGENT"))) || (preg_match("/Google/", getenv("HTTP_USER_AGENT"))) ||
(preg_match("/Slurp/", getenv("HTTP_USER_AGENT"))) || (preg_match("/Scooter/", getenv("HTTP_USER_AGENT"))) ||
(preg_match("/Spider/", getenv("HTTP_USER_AGENT"))) || (preg_match("/Infoseek/", getenv("HTTP_USER_AGENT"))))
  $navigateur = "Bot";
else
  $navigateur = "Autre";
// Affichage par exemple du navigateur
// ou insertion dans base de données pour statistiques

//affiche le navigateur
/*if($navigateur=="MSIE")
{
	function begin_box_js($title ,$id="")
	{
		begin_box($title ,$id="");
	}
}
else
{*/

	/*
	function begin box js
	creates a swapping menu
	you must call end_box() after each begin_box_js
	Title is the title of the box (the title does not disappear and you click on it to show the hidden contents)
	the var $id is whatever you want , it has to be unique if you want the function to work 
	the var $message_after is there if you want to put a message after the title which is outside the link <a></a>
	this variable is used in show.php 
	*/
function begin_box_js($title ,$id,$message_after="")
{
	$id=str_replace(" ","_",$id);
	$id=str_replace("/","_",$id);
	$id=str_replace("(","_",$id);
	$id=str_replace(")","_",$id);
	?>
	                                
	<h2><a href="javascript:;" onclick="change_table('<?php echo $id?>');"><img alt="" width='12' height='12' id='<?php echo "img".$id ?>' src='images/design/plus.png'/><?php echo $title ?></a><?php echo $message_after?></h2>
	                               
	                        
	               			<!--box table js -->
	                                <table id="<?php echo $id ?>" class="box_table_js_ferme" >
	
	<?php
}


function begin_box_js_opened($title ,$id,$message_after="")
{
	$id=str_replace(" ","_",$id);
	$id=str_replace("/","_",$id);
	$id=str_replace("(","_",$id);
	$id=str_replace(")","_",$id);
	?>
	                                
	<h2><a href="javascript:;" onclick="change_table('<?php echo $id?>');"><img width='12' height='12' id='<?php echo "img".$id ?>' src='images/design/moins.png' alt=''/><?php echo $title ?></a><?php echo $message_after?></h2>
	                               
	                        
	               			<!--box table js -->
	                                <table id="<?php echo $id ?>" class="box_table_js_ouvert" >
	
	<?php
}
	
//}
function end_box()
{
        ?>         
                                        </table>
                                       <!-- Fin du tableau box_table_contenu -->    
    
        <?php
}

//fonction qui m'évite à chaque fois d'ecrire les lignes et colonnes
function msg($msg)
{
        ?>
        <tr>
                <td><?php echo $msg?></td>
        </tr>
        <?php
}

//delivers terminal like messages
function special_msg($head,$msg)
{
	msg($head." : ".$msg);
}

function msg_return_to($href)
{
	// pour les liens
	//$href=str_replace("&","&amp;",$href);
	msg("<a href=\"$href\"><img alt='' width='16' height='16' src='images/retour.png' /> "._("Retour")."</a>");
}

function msg_return()
{
	msg("<a href=\"#\"><img alt='' width='16' height='16' src='images/retour.png ' /> "._("Retour")."</a>");
}


/* Cette fonction permet d'uniformiser l'affichage des pages HTML, et du design quand il y en aura un.
 * Toutes les pages qui seront affichées à l'utilisateur (recherche.php, administration.php ...) doivent inclure ce fichier.
 *
 * Le jour où on aura un bon design, on aura plus qu'à ajouter une balise du type
 * <link href="style.css" type="text/css" rel="stylesheet" />
 *
 * Pour utiliser facilement cette fonction, il faut utiliser les fonction de bufferisation de sortie (cf doc de PHP).
 * Ex :
 * <?php
 * require_once('include/page.php');
 * ob_start();
 * // Code qui utilise des echo 
 * $contenu =  ob_get_contents(); // on récupère tout ce qui a été echo'ed dans $contenu
 * ob_end_clean(); // on n'affiche pas tout ce qui a été echo'ed
 * afficher_page('Titre ici',$contenu); // on affiche $contenu "à notre sauce"
 * ?>
 before se met avant le tableau pour les divs flottantes
 */

function afficher_page($titre, $contenu,$doctype="default",$jquery="",$before="")
{
global $dev_server_bool;
global $requetes; // pour afficher le nb de requêtes SQL (tu peux l'enlever si tu veux)
if($doctype=="frames")
{
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
	<?php
}
else
{
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
           "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<?php
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
	<meta name="google-site-verification" content="2PFLfOBaeKcXvldl5brrFdep8PPxXL_jTF7mHA0v3nM" />
	<meta name="Author" content="Yoann Desmouceaux, Nguyen Bac Dang, Alice Tacaille, Daniel Morel,Pierre Boivin"/>
	<meta name="Keywords" content="Recherche de psaumes, Psaumes, Psautiers, psautiers.org , psautiers.fr, www.psautiers.org, www.psautiers.fr, Recherche musicale, recherche mélodique,rechercher une melodie, find a melody, score finder "/>
	<meta name="Description" content="This site provides a powerful script to search for any melody in the database, rechercher une melodie, un psaume en entrant la mélodie"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Carnet de notes -- <?php echo $titre;  if (!empty($_SESSION['pseudo'])) echo _(" -- Identifié en tant que")." ".$_SESSION['pseudo'];?></title>
	<link href='css/style.css' rel='stylesheet' type='text/css' /> 
	
    <?=$jquery?>
</head>
<body style="background-color: <?=$_SESSION['body_background_color']?>;">


<script language="Javascript" type="text/javascript" src="js/main_box.js"></script>

<div id="contenu">

	<div id="haut">
	</div>
	
	<map name="map" id="map" >
<!-- #$-:Image map file created by GIMP Image Map plug-in -->
<!-- #$-:GIMP Image Map plug-in by Maurits Rijk -->
<!-- #$-:Please do not edit lines starting with "#$" -->
<!-- #$VERSION:2.3 -->
<!-- #$AUTHOR:cab -->
<area alt="<?=_("Aller au site score-catcher.org")?>" shape="rect" coords="708,75,900,100" href="http://www.score-catcher.org" />
</map>
	<div id="header">
		<img alt="Banner" usemap="#map" src="<?=$_SESSION['banner']?>" width="900" height="100" />
	</div>

	
	<script language="Javascript" type="text/javascript">
	<!--
	function popup_image(img,width,height) 
	{ 
	    w=window.open("","1","menubar=no, status=no, scrollbars=no, menubar=no, width="+(width+20)+", height="+height+"") ;   
	    w.document.write("<html xmlns='http://www.w3.org/1999/xhtml' lang='fr' xml:lang='fr'><body onclick='window.close();'><img onclick='window.close();' width='"+(width)+"'  height='"+height+"' src='"+img+"'>");
	
	    w.document.write("</body></html>"); 
	    w.document.close(); 
	} 
	
	function change_current_menu(li)
	{
		var before=document.getElementById("current");
		before.removeAttribute("current");
		li.setAttribute("id","current");
	}
	function change_table(id_str)
	{
		var element = document.getElementById(id_str);
		var image=document.getElementById('img'+id_str);
		var plus='images/design/plus.png';
		var moins='images/design/moins.png';
		if(image.getAttribute('src')== moins)
		{
		        image.setAttribute('src',plus);
		}
		else
		{
		        var h2=document.getElementsByTagName('h2');
		        var i;
		        for (i=0;i<h2.length;i++)
		        {
		                var imgs=h2[i].getElementsByTagName('img');
		                if(imgs.length !=0)
		                {
		                        var img=imgs[0];
		                        img.setAttribute('src',plus);
		                }
		                
		        }
		        image.setAttribute('src',moins);
		}
		if (element.getAttribute("class") == 'box_table_js_ouvert')
		{
			element.setAttribute("class",'box_table_js_ferme');
			element.setAttribute("className",'box_table_js_ferme');
			
		}
		else
		{
			var elems = document.getElementsByTagName('table');
			var i;
			for (i=0; i<elems.length; i++)
			{
				if (elems[i].getAttribute("class") == 'box_table_js_ouvert') 
				{
				        elems[i].setAttribute("class",'box_table_js_ferme');
				        elems[i].setAttribute("className",'box_table_js_ferme');
				}
			}
			element.setAttribute("className",'box_table_js_ouvert');
			element.setAttribute("class",'box_table_js_ouvert');
		}
	}
	-->
	</script>
	<?=$before?>
	<div id="article">
	<div id="texte">
		<div id="menu">
		
			<a href="index.php"><img alt="<?=_('Page principale')?>"  src="./images/design/home.png" /> <?=_("Page principale")?></a>
			<a href="recherche.php"><img alt="<?=_('Recherche')?>" src="./images/design/find.png" /> <?=_("Recherche")?></a>
			<a href="show.php?<?=($_SESSION['isolated'])?(''):('custom=default')?>"><img alt="<?=_('Consultation')?>" src="./images/design/consulter.png" /> <?=_("Consultation")?></a>
			<!--<a href="http://forum.<?=preg_replace("/www./","",$_SERVER["SERVER_NAME"])?>"><img alt="<?=_('Forum')?>" 
src="./images/design/forum.png" /> <?=_("Forum")?></a>-->
		<?php
		if(is_admin())
		{
		?>	
			

			<?php
			if ($dev_server_bool)
			{
			    ?>
			    <a href="admin.php"><img alt="<?=_('Administration')?>" src="./images/design/admin.png" /> <?=_("Administration")?></a>
			    <?php
		    }
		    ?>
			<a href="logout.php"><img alt="<?=_('Déconnexion')?>" src="./images/design/deco.png" /> <?=_("Déconnexion")?></a>
		<?php
		}
		else
		{
			?>
			<a href="login.php"><img alt="<?=_('Login')?>" width="19" height="17" src="./images/design/connect.png" /> <?=_("Login")?></a>
			<?php
		}
		if ($_SESSION["isolated"])
		{
		    ?>
		    <a href="download.php?file=guide_pdf&amp;id_base=<?=$_SESSION['isolated_base']?>"><img alt="<?=_('Aide')?>" width="19" height="17" src="./images/design/help.png" /> <?=_("Aide")?></a>
		    <?php
		}
		else
		{
		    ?>
		    <a href="help.php"><img alt="<?=_('Aide')?>" width="19" height="17" src="./images/design/help.png" /> <?=_("Aide")?></a>
		    <?php
		}
		?>
			
			<a href="links.php"><img alt="<?=_('Liens')?>" width="19" height="17" src="./images/design/credits.png" /> <?=_("Liens")?></a>
		</div>
		
		
		<div id="titre">

		<h1><?php echo $titre; ?></h1>
		</div>
	
	<?php 
#	    echo /*str_replace("\n","\n\t\t\t",$contenu)*/ preg_replace("\n#","",$contenu);
        echo $contenu;	     
	?>
	</div> <!-- Fin de id="texte"-->
	</div> <!-- Fin de id="article"-->

	<div id="footer">
		&copy; 2008-<?=date("Y")?> by Nguyen Bac Dang, Yoann Desmouceaux / Design: Pierre Boivin<br />
		Musical Conception : Alice Tacaille, Daniel Morel<br />
		<?=_("Reproduction non autorisée. Tous droits réservés.")?>
		<?php
		if ($_SESSION['lang'] == 'en') echo '<a href="?lang=fr">Version française</a>';
		else echo '<a href="?lang=en">English version</a>';
		echo "<br/>";
		global $debut, $requetes;
		$fin=microtime(true);
		echo _("Temps d'execution")." : ".round($fin - $debut, 10)." s, "._("Requêtes")." : ". $requetes;
		?>
		<br/>
		
		    	<a href="http://validator.w3.org/check?uri=referer"><img
			src="http://www.w3.org/Icons/valid-xhtml10-blue.png"
			alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
			<a href="http://jigsaw.w3.org/css-validator/check/referer">
			    <img style="border:0;width:88px;height:31px"
				src="http://jigsaw.w3.org/css-validator/images/vcss-blue"
				alt="CSS Valide !" />
			</a>

			<a href="http://www.prchecker.info/" title="Page Ranking Tool" target="_blank">
<img src="http://pr.prchecker.info/getpr.php?codex=aHR0cDovL3BzYXV0aWVycy5vcmc=&amp;tag=1" alt="Page Ranking Tool" style="border:0;" /></a>
		 

	</div>

	<div id="bas">
	</div>
	
</div>

	


</body>
</html><?php
}



function newline()
{
        echo "<br />\n";       
}
?>
