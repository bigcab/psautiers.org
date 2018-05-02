<?php
require_once("include/config.php");

#ce fichier header checke si la page que l'on consulte est une page de développement ou non
#voir la variable dev_server_bool 



if($dev_server_bool!=TRUE)
{
    begin_box(_("Attention"),"dev_serv_error");
    msg(_("Vous consultez une page du serveur de consultation, cette page vous est inaccessible. "));
    end_box();
    dump_page($jquery);
    return ;
}
?>
