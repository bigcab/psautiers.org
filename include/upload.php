<?php


function get_extension($filename)
{
        return strtolower(  substr(  strrchr($filename, '.')  ,1)  );
}


//début de la fonction upload_form()
function upload_form()
{
        ?>
        <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="file" value="Entrez un fichier musicXML"/>
        <input type="submit" name="ok" value="Enregistrer"/>
        </form>
        <?php
}
//fin de la fonction upload_form();

function upload($file,$extensions_valides,$dossier)
{
        if ( !isset($_FILES[$file])  )
        {
                echo "<tr><td>"._("Aucun fichier selectionné")."</td></tr>";
                newline();
                return NULL;
        }
        if ($_FILES[$file]['error'] > 0) 
        {
                $erreur = "<tr><td>"._("Erreur lors du transfert du fichier")." $file</td></tr>";
                echo $erreur;
                return NULL;
        }
        $filename=$_FILES[$file]["name"];
        $filetype=$_FILES[$file]["type"];
        $filetmp=$_FILES[$file]["tmp_name"];
        //regarde l'extension du fichier
        $extension_upload = strtolower(  substr(  strrchr($_FILES[$file]['name'], '.')  ,1)  );
        if ( !in_array($extension_upload,$extensions_valides) )
        {
                echo _("Le fichier n'a pas une bonne extension");
                newline();
                upload_form();
                return NULL;
        }
        //pour l'extension, on prend la date en milliseconde
        $out=str_replace(" ","",microtime());
        $out=str_replace(".","",$out);
        $outfilename=$dossier."/".$out.".".$extension_upload;
        if ( !move_uploaded_file($filetmp,$outfilename) )
        {
                echo _("Erreur lors du transfert du fichier");
                newline();
                upload_form();
                return NULL;
        }
        return $outfilename;
}
?>
