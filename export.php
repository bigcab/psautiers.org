<?php
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/lilypond.php");
require_once("include/infos_db.php");
require_once("include/export.php");
ob_start();

if(!$_SESSION["admin"])
{
        echo _("PAS LES DROITS")."!!!!!";
        return ;    
}


function export_form()
{
    ?>
    <form method="get" action="download.php">
        <?php
        begin_box(_("Export de la base de donnée"),"_box");
        ?>
        <tr>
		    <td><?=_("Sélection de la base")?></td>
		    <td>
		    <script type="text/javascript" language="Javascript"><!--
		    

                function update_id_recueil(select)
                {
                                var id_recueil=document.getElementById('id_recueil');
                                id_recueil.value=select.value;
                }

                function update_id_recueil2(select)
                {
                                var id_recueil=document.getElementById('id_recueil2');
                                id_recueil.value=select.value;
                }

                function create_select_recueil(tr)
                {
                        var td1=document.createElement("td");
                        var td2=document.createElement("td");
                        td1.innerHTML="<?=_("Sélection d'un recueil")?>";
                        var select=document.createElement("select");
                        select.onchange=function (){
                                update_id_recueil(select);
                        };
                        select.setAttribute("name","select_recueil");
                        select.setAttribute("style","width: 200px;");
                        td2.appendChild(select);
                        tr.appendChild(td1);
                        tr.appendChild(td2);
                        return select;
                }

                function create_select_recueil2(tr)
                {
                        var td1=document.createElement("td");
                        var td2=document.createElement("td");
                        td1.innerHTML="Rechercher dans un recueil";
                        var select=document.createElement("select");
                        select.onchange=function (){
                                update_id_recueil2(select);
                        };
                        select.setAttribute("name","select_recueil");
                        select.setAttribute("style","width: 200px;");
                        td2.appendChild(select);
                        tr.appendChild(td1);
                        tr.appendChild(td2);
                        return select;
                }


                function update_recueil(select_base)
                {
	
                        remove_all_children(document.getElementById('tr_select_recueil'));
                        if(select_base.value=="toutes")
                        {
                        	var id_recueil=document.getElementById('id_recueil');
                                id_recueil.value="tous";
                        	return;
                        }
                        var select=create_select_recueil(document.getElementById('tr_select_recueil'));
                        list_recueil_in_base(select,select_base.value);
                }


                function update_recueil2(select_base)
                {
	
                        remove_all_children(document.getElementById('tr_select_recueil2'));
                        if(select_base.value=="toutes")
                        {
                        	var id_recueil=document.getElementById('id_recueil2');
                                id_recueil.value="tous";
                        	return;
                        }
                        var select=create_select_recueil2(document.getElementById('tr_select_recueil2'));
                        list_recueil_in_base2(select,select_base.value);
                }

                function remove_all_children(select)
                {
                        while(select.firstChild)
                        {
                                select.removeChild(select.firstChild);
                        }
                }
                /*
                This function list everything in a base
                and adds the option in the select node
                */
                function list_recueil_in_base(select,id_base)
                {
                        remove_all_children(select);
                        if (window.XMLHttpRequest) { // Mozilla, Safari, ...
                                httpRequest = new XMLHttpRequest();
                        }
                        else if (window.ActiveXObject) { // IE
                                httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                        }
                        httpRequest.overrideMimeType('text/xml');
                        httpRequest.onreadystatechange = function ()
                        {
                                //la reponse est reçue
                                if (httpRequest.readyState == 4) {
                                        // tout va bien, la réponse a été reçue
                                        if (httpRequest.status == 200) {
                                        // parfait !
                                                var doc=httpRequest.responseXML;
                                                var recueils=doc.getElementsByTagName('recueils')[0];
                                                var r=recueils.getElementsByTagName('recueil');
                                                for(i=0;i<r.length;i++)
                                                {
                                                        var titre=r[i].getAttribute("titre");
                                                        var id_recueil=r[i].getAttribute("id_recueil");
                                                        var option=document.createElement('option');
                                                        option.value=id_recueil;
                                                        option.innerHTML=titre;
                                                        select.appendChild(option);

                                                }
                                                update_id_recueil(select);
                                        }
                                } 
                                

                        };
                        httpRequest.open('GET', 'list.php?id_base='+id_base, true);
                        httpRequest.send(null);
                        
                }



                function list_recueil_in_base2(select,id_base)
                {
                        remove_all_children(select);
                        if (window.XMLHttpRequest) { // Mozilla, Safari, ...
                                httpRequest = new XMLHttpRequest();
                        }
                        else if (window.ActiveXObject) { // IE
                                httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                        }
                        httpRequest.overrideMimeType('text/xml');
                        httpRequest.onreadystatechange = function ()
                        {
                                //la reponse est reçue
                                if (httpRequest.readyState == 4) {
                                        // tout va bien, la réponse a été reçue
                                        if (httpRequest.status == 200) {
                                        // parfait !
                                                var doc=httpRequest.responseXML;
                                                var recueils=doc.getElementsByTagName('recueils')[0];
                                                var r=recueils.getElementsByTagName('recueil');
                                                for(i=0;i<r.length;i++)
                                                {
                                                        var titre=r[i].getAttribute("titre");
                                                        var id_recueil=r[i].getAttribute("id_recueil");
                                                        var option=document.createElement('option');
                                                        option.value=id_recueil;
                                                        option.innerHTML=titre;
                                                        select.appendChild(option);

                                                }
                                                update_id_recueil2(select);
                                        }
                                } 
                                

                        };
                        httpRequest.open('GET', 'list.php?id_base='+id_base, true);
                        httpRequest.send(null);
                        
                }
		    
		    </script>
			    <select name="id_base" onchange="update_recueil(this)">
				    <option value="toutes"><?=_("Toutes")?></option>
				    <?php
				    // listing de toutes les bases avec l'option toutes
				    $sql="SELECT id_base,nom_base FROM bases WHERE 1";
				    $req=requete($sql);
				    while($response=fetch_array($req))
				    {
						    ?>
						    <option value="<?php echo $response["id_base"] ?>">
						    <?php echo $response["nom_base"] ?>
						    </option>
						    <?php
				    }
				    ?>
			    </select>
			<input type="hidden" name="file" value="export" />
		    <input type="hidden" name="id_recueil" id="id_recueil" />
		    </td>
	    </tr>
	    <tr id="tr_select_recueil">
	        <td></td>
	        <td></td>
	    </tr>
	    <tr>
	        <td></td>
	        <td>
	            <input type="reset" name="reset" value="<?=_("Réinitialiser")?>"/>
	            <input type="submit" name="submit" value="<?=_("Valider")?>"/>
	        </td>
	    </tr>
	    <?php
        end_box();
        ?>
    </form>
    <?php
}

export_form();
dump_page();
?>
