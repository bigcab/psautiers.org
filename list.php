<?php
require_once("include/xml.php");
require_once("include/auth.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");



function create_element_in_node($doc,$node,$element,$attributes)
{
        $elem=$doc->createElement($element);
        foreach($attributes as $key=>$value)
        {
                $elem->setAttribute($key,$value);
        }
        $node->appendChild($elem);
        return $elem;
}

//function list recueil
function xml_base($id_base)
{
        if(!read_in_base($id_base))
        {
                echo "Pas Les Droits";
                return ;
                
        }
        
        $req=requete("SELECT id_recueil,titre FROM recueils WHERE id_base='$id_base'");
        header("Content-Type: text/xml");
        $doc=new DOMDocument('1.0','UTF-8');  
        $recueils=create_element_in_node($doc,$doc,"recueils",array());
              
        if(num_rows($req)==0)
        {
                create_element_in_node($doc,$recueils,"recueil",array("titre"=> "Aucun","id_recueil"=>"tous"));
                echo $doc->saveXML();
                return ;
        }
        create_element_in_node($doc,$recueils,"recueil",array("titre"=>"Tous les recueils","id_recueil"=>"tous"));
        while ($response=fetch_array($req))
        {
                create_element_in_node($doc,$recueils,"recueil",array("titre"=>$response["titre"],"id_recueil"=>$response["id_recueil"]));
        }
        echo $doc->saveXML();
}

if(isset($_GET["id_base"]))
{
        xml_base($_GET["id_base"]);
}

?>
