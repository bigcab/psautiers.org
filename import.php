<!--<?php
require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/lilypond.php");
require_once("include/infos_db.php");
require_once("include/upload.php");
require_once("include/import.php");
ob_start();
begin_box("import");
if(!$_SESSION["admin"])
{
        echo "Pas les droits";
        end_box();
        dump_page();
        return ;
}




if(!check_file("file"))
{
        ?>
        <form method="post" enctype="multipart/form-data">
        <input type="file" name="file"/>
        <input type="text" name="id"/>
        <input type="submit" value="ok"/>
        </form> 
        <?php
        end_box();
        dump_page();
        return ;
}
$extensions=array("bac");
$file=upload("file",$extensions,"temp");



$zip=new zip_import_class($file);

$zip->import_zip($_POST["id"]);
$zip->free();
end_box();
//$doc=new DOMDocument();
//$doc->load("temp/0424448001234948631/export.xml");
dump_page();
?>-->
