	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
           "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
	<meta name="google-site-verification" content="2PFLfOBaeKcXvldl5brrFdep8PPxXL_jTF7mHA0v3nM" />
	<meta name="Author" content="Yoann Desmouceaux, Nguyen Bac Dang, Alice Tacaille, Daniel Morel,Pierre Boivin"/>
	<meta name="Keywords" content="Recherche de psaumes, Psaumes, Psautiers, psautiers.org , psautiers.fr, www.psautiers.org, www.psautiers.fr, Recherche musicale, recherche mélodique,rechercher une melodie, find a melody, score finder "/>
	<meta name="Description" content="This site provides a powerful script to search for any melody in the database, rechercher une melodie, un psaume en entrant la mélodie"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Psautiers : Recherche mélodique dans les psaumes -- Analyse de la langue française du XVIième siècle -- Identifié en tant que root</title>
	<link href='css/style.css' rel='stylesheet' type='text/css' /> 
	
    <link href='./jquery/themes/base/jquery.ui.all.css' rel='stylesheet' type='text/css' /> <script type='text/javascript' src='./jquery/jquery.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.core.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.widget.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.draggable.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.mouse.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.sortable.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.position.js'></script>
        	<script type='text/javascript'  src='./jquery/ui/jquery.ui.dialog.js'></script>
        <script type='text/javascript' ><!--
        	        $(function() {
        $('.dialog_form').dialog({
            title: 'Modification',
            autoOpen: false,
            width: 400,
            buttons: {
                'Valider': function (event,ui){
                    validate($(this));
                }
            }
            });
        $('.syllabic_special').click(function() 
        {
            open_dialog(this);
        });    



function open_dialog($span)
{
    var name=$('input', $span)[0].attr('id');
    var tableau=name.split('_');
    var vers=tableau[1];
    var syllabe=tableau[2];
    
    $('#dialog_form_'+vers+'_'+syllabe).dialog('open');
}


function validate($dialog_box)
{
    var l=$('input[type=radio]:checked', $dialog_box).length;
    var name=$dialog_box.attr('id');

    var tableau=name.split('_');
    var vers=tableau[2];
    var syllabe=tableau[3];

    for(i=0 ; i< l ; i++)
    {
        var elem=$('input[type=radio]:checked',$dialog_box)[i];
        var elem_name=$(elem).attr('name');
        var value=$(elem).attr('value');
        $('#'+elem_name+'_'+vers+'_'+syllabe).attr('value',value);

    }
    $dialog_box.dialog('close');    
}
});
	


});--></script></head>
<body style="background-color: rgb(204,220,255);">


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
<area alt="Aller au site score-catcher.org" shape="rect" coords="708,75,900,100" href="http://www.score-catcher.org" />
</map>
	<div id="header">
		<img alt="Banner" usemap="#map" src="./images/design/ban5.jpg" width="900" height="100" />
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
	<div id="article">
	<div id="texte">
		<div id="menu">
		
			<a href="index.php"><img alt="Page principale"  src="./images/design/home.png" /> Page principale</a>
			<a href="recherche.php"><img alt="Recherche" src="./images/design/find.png" /> Recherche</a>
			<a href="show.php?custom=default"><img alt="Consultation" src="./images/design/consulter.png" /> Consultation</a>
			<!--<a href="http://forum.localhost"><img alt="Forum" 
src="./images/design/forum.png" /> Forum</a>-->
			
			

						    <a href="admin.php"><img alt="Administration" src="./images/design/admin.png" /> Administration</a>
			    			<a href="logout.php"><img alt="Déconnexion" src="./images/design/deco.png" /> Déconnexion</a>
				    <a href="help.php"><img alt="Aide" width="19" height="17" src="./images/design/help.png" /> Aide</a>
		    			
			<a href="links.php"><img alt="Liens" width="19" height="17" src="./images/design/credits.png" /> Liens</a>
		</div>
		
		
		<div id="titre">

		<h1>Analyse de la langue française du XVIième siècle</h1>
		</div>
	
	
    <script language="Javascript" type="text/javascript"><!--
    
    function change_ent(span,vers,syllabe)
    {
        var classe=span.getAttribute('class');
        if(classe=='ent_grave')
        {
            span.setAttribute('class','ent_non_grave');   
        }
        else
        {
            span.setAttribute('class','ent_grave');
        }
        var input=document.getElementById('ent_'+vers+'_'+syllabe);
        if(input.value=='grave')
        {
            input.value='non_grave';
        }
        else
        {
            input.value='grave';
        }
        
        var help=document.getElementById('ent_help_'+vers+'_'+syllabe);
        if(help.innerHTML=='grave')
        {

            help.innerHTML='non grave';
        }
        else
        {
            help.innerHTML='grave';
        }
    }
    
    
    function change_h(span,vers,syllabe)
    {
        var classe=span.getAttribute('class');
        if(classe=='h_aspire')
        {
            span.setAttribute('class','h_non_aspire');   
        }
        else
        {
            span.setAttribute('class','h_aspire');
        }
        var input=document.getElementById('aspire_'+vers+'_'+syllabe);
        if(input.value=='aspire')
        {
            input.value='non_aspire';
        }
        else
        {
            input.value='aspire';
        }
        
        var help=document.getElementById('aspire_help_'+vers+'_'+syllabe);
        if(help.innerHTML=='aspire')
        {

            help.innerHTML='non aspire';
        }
        else
        {
            help.innerHTML='aspire';
        }
    }
    
    
    
    function change_accent(span,vers,syllabe)
    {
        
        var classe=span.getAttribute('class');
        if(classe=='longue')
        {
            span.setAttribute('class','breve');
        }
        else
        {
            span.setAttribute('class','longue');
        }
        
        var input=document.getElementById('accent_'+vers+'_'+syllabe);
        if(input.value=='longue')
        {
            input.value='breve';
        }
        else
        {
            input.value='longue';
        }
        
        var help=document.getElementById('help_'+vers+'_'+syllabe);
        if(help.innerHTML=='longue')
        {

            help.innerHTML='breve';
        }
        else
        {
            help.innerHTML='longue';
        }
    }
    --></script>
    <span class='longue' onclick='change_accent(this,0,0)'><input type='hidden' id='accent_0_0' name='accent_0_0' value='longue'/>Qui<span class='bulle' id='help_0_0'>longue</span></span> <span class='longue' onclick='change_accent(this,0,1)'><input type='hidden' id='accent_0_1' name='accent_0_1' value='longue'/>au<span class='bulle' id='help_0_1'>longue</span></span> <span class='longue' onclick='change_accent(this,0,2)'><input type='hidden' id='accent_0_2' name='accent_0_2' value='longue'/>con<span class='bulle' id='help_0_2'>longue</span></span><span class='longue' onclick='change_accent(this,0,3)'><input type='hidden' id='accent_0_3' name='accent_0_3' value='longue'/>seil<span class='bulle' id='help_0_3'>longue</span></span> <span class='breve' onclick='change_accent(this,0,4)'><input type='hidden' id='accent_0_4' name='accent_0_4' value='breve'/>des<span class='bulle' id='help_0_4'>breve</span></span> <span class='breve' onclick='change_accent(this,0,5)'><input type='hidden' id='accent_0_5' name='accent_0_5' value='breve'/>ma<span class='bulle' id='help_0_5'>breve</span></span><span class='breve' onclick='change_accent(this,0,6)'><input type='hidden' id='accent_0_6' name='accent_0_6' value='breve'/>lins<span class='bulle' id='help_0_6'>breve</span></span> <span class='breve' onclick='change_accent(this,0,7)'><input type='hidden' id='accent_0_7' name='accent_0_7' value='breve'/>n'a<span class='bulle' id='help_0_7'>breve</span></span> <span class='longue' onclick='change_accent(this,0,8)'><input type='hidden' id='accent_0_8' name='accent_0_8' value='longue'/>es<span class='bulle' id='help_0_8'>longue</span></span><span class='longue' onclick='change_accent(this,0,9)'><input type='hidden' id='accent_0_9' name='accent_0_9' value='longue'/>té,<span class='bulle' id='help_0_9'>longue</span></span> <br/><span class='longue' onclick='change_accent(this,1,0)'><input type='hidden' id='accent_1_0' name='accent_1_0' value='longue'/>Qui<span class='bulle' id='help_1_0'>longue</span></span> <span class='longue' onclick='change_accent(this,1,1)'><input type='hidden' id='accent_1_1' name='accent_1_1' value='longue'/>n'est<span class='bulle' id='help_1_1'>longue</span></span> <span class='longue' onclick='change_accent(this,1,2)'><input type='hidden' id='accent_1_2' name='accent_1_2' value='longue'/>au<span class='bulle' id='help_1_2'>longue</span></span> <span class='longue' onclick='change_accent(this,1,3)'><input type='hidden' id='accent_1_3' name='accent_1_3' value='longue'/>trac<span class='bulle' id='help_1_3'>longue</span></span> <span class='breve' onclick='change_accent(this,1,4)'><input type='hidden' id='accent_1_4' name='accent_1_4' value='breve'/>des<span class='bulle' id='help_1_4'>breve</span></span> <span class='breve' onclick='change_accent(this,1,5)'><input type='hidden' id='accent_1_5' name='accent_1_5' value='breve'/>pe<span class='bulle' id='help_1_5'>breve</span></span><span class='breve' onclick='change_accent(this,1,6)'><input type='hidden' id='accent_1_6' name='accent_1_6' value='breve'/>cheurs<span class='bulle' id='help_1_6'>breve</span></span> <span class='breve' onclick='change_accent(this,1,7)'><input type='hidden' id='accent_1_7' name='accent_1_7' value='breve'/>ar<span class='bulle' id='help_1_7'>breve</span></span><span class='breve' onclick='change_accent(this,1,8)'><input type='hidden' id='accent_1_8' name='accent_1_8' value='breve'/>res<span class='bulle' id='help_1_8'>breve</span></span><span class='longue' onclick='change_accent(this,1,9)'><input type='hidden' id='accent_1_9' name='accent_1_9' value='longue'/>té,<span class='bulle' id='help_1_9'>longue</span></span> <br/><span class='longue' onclick='change_accent(this,2,0)'><input type='hidden' id='accent_2_0' name='accent_2_0' value='longue'/>Qui<span class='bulle' id='help_2_0'>longue</span></span> <span class='breve' onclick='change_accent(this,2,1)'><input type='hidden' id='accent_2_1' name='accent_2_1' value='breve'/>des<span class='bulle' id='help_2_1'>breve</span></span> <span class='breve' onclick='change_accent(this,2,2)'><input type='hidden' id='accent_2_2' name='accent_2_2' value='breve'/>mo<span class='bulle' id='help_2_2'>breve</span></span><span class='longue' onclick='change_accent(this,2,3)'><input type='hidden' id='accent_2_3' name='accent_2_3' value='longue'/>queurs<span class='bulle' id='help_2_3'>longue</span></span> <span class='longue' onclick='change_accent(this,2,4)'><input type='hidden' id='accent_2_4' name='accent_2_4' value='longue'/>au<span class='bulle' id='help_2_4'>longue</span></span> <span class='breve' onclick='change_accent(this,2,5)'><input type='hidden' id='accent_2_5' name='accent_2_5' value='breve'/>banc<span class='bulle' id='help_2_5'>breve</span></span> <span class='breve' onclick='change_accent(this,2,6)'><input type='hidden' id='accent_2_6' name='accent_2_6' value='breve'/>pla<span class='bulle' id='help_2_6'>breve</span></span><span class='breve' onclick='change_accent(this,2,7)'><input type='hidden' id='accent_2_7' name='accent_2_7' value='breve'/>ce<span class='bulle' id='help_2_7'>breve</span></span> <span class='breve' onclick='change_accent(this,2,8)'><input type='hidden' id='accent_2_8' name='accent_2_8' value='breve'/>n'a<span class='bulle' id='help_2_8'>breve</span></span> <span class='longue' onclick='change_accent(this,2,9)'><input type='hidden' id='accent_2_9' name='accent_2_9' value='longue'/>pri<span class='bulle' id='help_2_9'>longue</span></span><span class='longue' onclick='change_accent(this,2,10)'><input type='hidden' id='accent_2_10' name='accent_2_10' value='longue'/>se,<span class='bulle' id='help_2_10'>longue</span></span> <br/><span class='longue' onclick='change_accent(this,3,0)'><input type='hidden' id='accent_3_0' name='accent_3_0' value='longue'/>Mais<span class='bulle' id='help_3_0'>longue</span></span> <span class='breve' onclick='change_accent(this,3,1)'><input type='hidden' id='accent_3_1' name='accent_3_1' value='breve'/>nuict<span class='bulle' id='help_3_1'>breve</span></span> <span class='longue' onclick='change_accent(this,3,2)'><input type='hidden' id='accent_3_2' name='accent_3_2' value='longue'/>&amp;<span class='bulle' id='help_3_2'>longue</span></span> <span class='longue' onclick='change_accent(this,3,3)'><input type='hidden' id='accent_3_3' name='accent_3_3' value='longue'/>jour<span class='bulle' id='help_3_3'>longue</span></span> <span class='longue' onclick='change_accent(this,3,4)'><input type='hidden' id='accent_3_4' name='accent_3_4' value='longue'/>la<span class='bulle' id='help_3_4'>longue</span></span> <span class='breve' onclick='change_accent(this,3,5)'><input type='hidden' id='accent_3_5' name='accent_3_5' value='breve'/>Loy<span class='bulle' id='help_3_5'>breve</span></span> <span class='breve' onclick='change_accent(this,3,6)'><input type='hidden' id='accent_3_6' name='accent_3_6' value='breve'/>con<span class='bulle' id='help_3_6'>breve</span></span><span class='breve' onclick='change_accent(this,3,7)'><input type='hidden' id='accent_3_7' name='accent_3_7' value='breve'/>temple<span class='bulle' id='help_3_7'>breve</span></span> <span class='breve' onclick='change_accent(this,3,8)'><input type='hidden' id='accent_3_8' name='accent_3_8' value='breve'/>et<span class='bulle' id='help_3_8'>breve</span></span> <span class='longue' onclick='change_accent(this,3,9)'><input type='hidden' id='accent_3_9' name='accent_3_9' value='longue'/>pri<span class='bulle' id='help_3_9'>longue</span></span><span class='longue' onclick='change_accent(this,3,10)'><input type='hidden' id='accent_3_10' name='accent_3_10' value='longue'/>se<span class='bulle' id='help_3_10'>longue</span></span> <br/><span class='breve' onclick='change_accent(this,4,0)'><input type='hidden' id='accent_4_0' name='accent_4_0' value='breve'/>De<span class='bulle' id='help_4_0'>breve</span></span> <span class='longue' onclick='change_accent(this,4,1)'><input type='hidden' id='accent_4_1' name='accent_4_1' value='longue'/>l'E<span class='bulle' id='help_4_1'>longue</span></span><span class='longue' onclick='change_accent(this,4,2)'><input type='hidden' id='accent_4_2' name='accent_4_2' value='longue'/>ter<span class='bulle' id='help_4_2'>longue</span></span><span class='longue' onclick='change_accent(this,4,3)'><input type='hidden' id='accent_4_3' name='accent_4_3' value='longue'/>nel,<span class='bulle' id='help_4_3'>longue</span></span> <span class='longue' onclick='change_accent(this,4,4)'><input type='hidden' id='accent_4_4' name='accent_4_4' value='longue'/>&amp;<span class='bulle' id='help_4_4'>longue</span></span> <span class='breve' onclick='change_accent(this,4,5)'><input type='hidden' id='accent_4_5' name='accent_4_5' value='breve'/>en<span class='bulle' id='help_4_5'>breve</span></span> <span class='longue' onclick='change_accent(this,4,6)'><input type='hidden' id='accent_4_6' name='accent_4_6' value='longue'/>est<span class='bulle' id='help_4_6'>longue</span></span> <span class='longue' onclick='change_accent(this,4,7)'><input type='hidden' id='accent_4_7' name='accent_4_7' value='longue'/>de<span class='bulle' id='help_4_7'>longue</span></span><span class='longue' onclick='change_accent(this,4,8)'><input type='hidden' id='accent_4_8' name='accent_4_8' value='longue'/>si<span class='bulle' id='help_4_8'>longue</span></span><span class='longue' onclick='change_accent(this,4,9)'><input type='hidden' id='accent_4_9' name='accent_4_9' value='longue'/>reux,<span class='bulle' id='help_4_9'>longue</span></span> <br/><span class='longue' onclick='change_accent(this,5,0)'><input type='hidden' id='accent_5_0' name='accent_5_0' value='longue'/>Cer<span class='bulle' id='help_5_0'>longue</span></span><span class='longue' onclick='change_accent(this,5,1)'><input type='hidden' id='accent_5_1' name='accent_5_1' value='longue'/>tai<span class='bulle' id='help_5_1'>longue</span></span><span class='longue' onclick='change_accent(this,5,2)'><input type='hidden' id='accent_5_2' name='accent_5_2' value='longue'/>ne<span class='bulle' id='help_5_2'>longue</span></span><span class='longue syllabic_special' '><input type='hidden' id='accent_5_3' name='accent_5_3' value='longue'/><span class='bulle' id='help_5_3'>longue</span><input type='hidden' id='ent_5_3' name='ent_5_3' value='grave'/>m<span class='ent_grave'>ent<span id='ent_help_5_3' class='bulle'>grave</span></span></span> <span class='longue' onclick='change_accent(this,5,4)'><input type='hidden' id='accent_5_4' name='accent_5_4' value='longue'/>ces<span class='bulle' id='help_5_4'>longue</span></span><span class='breve' onclick='change_accent(this,5,5)'><input type='hidden' id='accent_5_5' name='accent_5_5' value='breve'/>tuy<span class='bulle' id='help_5_5'>breve</span></span><span class='breve' onclick='change_accent(this,5,6)'><input type='hidden' id='accent_5_6' name='accent_5_6' value='breve'/>la<span class='bulle' id='help_5_6'>breve</span></span> <span class='longue' onclick='change_accent(this,5,7)'><input type='hidden' id='accent_5_7' name='accent_5_7' value='longue'/>est<span class='bulle' id='help_5_7'>longue</span></span> <span class='breve syllabic_special' ><input type='hidden' id='accent_5_8' name='accent_5_8' value='breve'/><span class='bulle' id='help_5_8'>breve</span><input type='hidden' id='aspire_5_8' name='aspire_5_8' value='aspire'/><span class='h_aspire'>h<span class='bulle' id='aspire_help_5_8'>aspire</span></span>eu</span><span class='breve' onclick='change_accent(this,5,9)'><input type='hidden' id='accent_5_9' name='accent_5_9' value='breve'/>reux<span class='bulle' id='help_5_9'>breve</span></span> <br/>    <form method='post' action='?update=1&amp;id_piece=4'>
                                            
        <h2 id="edit_form_box">Edition des accents</h2>
                                       
                                
                       
                                        <table class="box_table_contenu">
        
                <tr>
            <td>
         <div class='dialog_form' id='dialog_form_5_3'>

                <table>
                <tr>
                    <th>Syllabe :</th>
                    <td>ment</td>
                </tr>
               <tr>     
                    <th>Ent :</th>
                    <td>
                        <input type='radio' name='ent_' value='grave' checked/> Grave     
                        <input type='radio' name='ent_' value='non_grave' /> Non Grave
                    </td>
               </tr>

            </table>

        </div>
         <div class='dialog_form' id='dialog_form_5_8'>

                <table>
                <tr>
                    <th>Syllabe :</th>
                    <td>heu</td>
                </tr><tr>
                    <th>H aspire :</th>
                    <td>
                        <input type='radio' name='aspire' value='aspire' checked/> Aspiré      
                        <input type='radio' name='aspire' value='non_aspire' /> Non Aspiré
                    </td>
               </tr>

            </table>

        </div></td>
        </tr>
        <tr>
            <td><input type='submit' name='ok' value='ok'/></td>
        </tr>
                 
                                        </table>
                                       <!-- Fin du tableau box_table_contenu -->    
    
            </form>
    	</div> <!-- Fin de id="texte"-->
	</div> <!-- Fin de id="article"-->

	<div id="footer">
		&copy; 2008-2012 by Nguyen Bac Dang, Yoann Desmouceaux / Design: Pierre Boivin<br />
		Musical Conception : Alice Tacaille, Daniel Morel<br />
		Reproduction non autorisée. Tous droits réservés.		<a href="?lang=en">English version</a><br/>Temps d'execution : 0,0871968269 s, Requêtes : 148		<br/>
		
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
</html>
