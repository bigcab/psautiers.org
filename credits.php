<?php

require_once("include/auth.php");
require_once("include/xml.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/lilypond.php");



ob_start();

$title = _("Crédits");

begin_box("Crédits");
msg(_("Nous remercions")." ");

end_box();

dump_page();

?>
