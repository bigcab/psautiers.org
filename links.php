<?php
require_once("include/xml.php");
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/bbcode.php");
require_once("include/message_box.php");
$title=_("Carnets de Notes");
ob_start();


function dump_base_links($base,$id_base,$msg)
{
    if (write_in_base($id_base))
    {
        begin_box(_("Liens de la base")." ".$base ,"links".$id_base,"<a href='links.php?id_base=$id_base&amp;update_links_form=true'><img src=\"images/design/edit.png\" alt='Editer' title='Editer' width='20' height='20'/></a>");
    }
    else
    {
        begin_box(_("Liens de la base")." ".$base ,"links".$id_base,"");    
    }
    ?> 
    <tr>
        <td>   
    	<p>
    	<?php
    	// On enlève les éventuels antislash PUIS on crée les entrées en HTML (<br />)
    	$contenu = bbcode(nl2br(stripslashes($msg)));
    	echo $contenu;
    	?>
    	</p>
    	</td>
	</tr>
	<?php
	end_box();
}
function show_links()
{
    if($_SESSION["isolated"])
    {
        $id_base=$_SESSION["isolated_base"];
        $req=requete("SELECT nom_base,liens,id_base FROM bases WHERE id_base='$id_base'");
        if(num_rows($req)==0)
        {
            begin_box(_("Erreur"),"error_box");
            msg(_("La base demandée n'existe pas"));
            end_box();
            dump_page();
            return;
        }
        
        while($response=fetch_array($req))
        {
            dump_base_links($response["nom_base"],$response["id_base"],$response["liens"]);
        }
    }
    else
    {
        $req=requete("SELECT nom_base,liens,id_base FROM bases WHERE 1");
        if(num_rows($req)==0)
        {
            begin_box(_("Erreur"),"error_box");
            msg(_("Aucune base existante"));
            end_box();
            dump_page();
            return;
        }
        while($response=fetch_array($req))
        {
            dump_base_links($response["nom_base"],$response["id_base"],$response["liens"]);
        }
    }

}


function update_links_form()
{
    if (!isset($_GET["id_base"]))
    {
        begin_box(_("Erreur"),"error_box");
        msg(_("Aucune base spécifiée"));
        end_box();
        dump_page();
        return;
        
    }
    $req=requete("SELECT nom_base,id_base,liens,owner FROM bases WHERE id_base='{$_GET["id_base"]}'");
    if(num_rows($req)==0)
    {
        begin_box(_("Erreur"),"error_box");
        msg(_("La base n'existe pas"));
        end_box();
        return;
    }
    $response=fetch_array($req);
    if(($response["owner"]!=$_SESSION["id"])&&(!$_SESSION["admin"]))
    {
        begin_box(_("Problème de droit"),"error_box");
        msg(_("Vous n'avez pas les droits nécessaires"));
        end_box();
        return; 
    }
    ?>
    <form method="post" action="links.php?update_links=true&amp;id_base=<?=$_GET['id_base']?>">
    <?php
    message_box($response["liens"]);
    ?>
    <input type="submit" name="ok" value="<?=_('Valider')?>"/>
    </form>
    <?php
}


function update_links()
{
    if (!isset($_GET["id_base"]))
    {
        begin_box(_("Erreur"),"error_box");
        msg(_("Aucune base spécifiée"));
        end_box();
        dump_page();
        return;
        
    }
    $req=requete("SELECT nom_base,id_base,liens,owner FROM bases WHERE id_base='{$_GET["id_base"]}'");
    if(num_rows($req)==0)
    {
        begin_box(_("Erreur"),"error_box");
        msg(_("La base n'existe pas"));
        end_box();
        return;
    }
    $response=fetch_array($req);
    if(($response["owner"]!=$_SESSION["id"])&&(!$_SESSION["admin"]))
    {
        begin_box(_("Problème de droit"),"error_box");
        msg(_("Vous n'avez pas les droits nécessaires"));
        end_box();
        return; 
    }
    
    requete("UPDATE bases SET 
            liens='{$_POST["contenu"]}'
            WHERE id_base='{$_GET['id_base']}'");
    begin_box(_("Modification des liens"),"update_links");
    msg(_("Les liens ont été modifiés"));
    end_box();
}

if(isset($_GET["update_links"]))
{
    update_links();
}
else if(isset($_GET["update_links_form"]))
{
    update_links_form();
}
else
{
    show_links();
}
dump_page();
?>
