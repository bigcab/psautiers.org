<?php
DEFINE("LOG_DIR","./logs/");
function erreur($string)
{
        $fichier=fopen(LOG_DIR."error.txt","r+");
        fseek($fichier,0,SEEK_END);
        fwrite($fichier,date("d/m/Y H:i")." : ".$string."\n\n\n\n");
        fclose($fichier);
}
function mysql_log($string)
{
        $fichier=fopen(LOG_DIR."log.txt","r+");
        fseek($fichier,0,SEEK_END);
        fwrite($fichier,date("d/m/Y H:i")." : ".$string."\n\n\n");
        fclose($fichier);
}

function auth_log($string)
{
        $fichier=fopen(LOG_DIR."auth.txt","r+");
        fseek($fichier,0,SEEK_END);
        fwrite($fichier,date("d/m/Y H:i")." : ".$string."\n\n\n\n");
        fclose($fichier);
}

function command_log($string)
{
        $fichier=fopen(LOG_DIR."command.txt","r+");
        fseek($fichier,0,SEEK_END);
        fwrite($fichier,date("d/m/Y H:i")." : ".$string."\n\n\n");
        fclose($fichier);
}
?>
