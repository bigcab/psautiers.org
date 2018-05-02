<?php

//      on crée la partition
//      
require_once("include/draw.php");
require_once("include/xml.php");
require_once("include/draw_music_xml.php");
header ("Content-type: image/png");




$music_xml=new music_xml_class("xml/yoann.xml");

draw_parts($music_xml->parts);

?>
