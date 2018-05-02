<?php
require_once("include/clavier.php");
//validator ok yes finally
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
           "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<link href='css/style_clavier.css' rel='stylesheet' type='text/css' /> 
<title>Virtual Keyboard</title>
</head>
<body>
<script language="Javascript" type="text/javascript"><!--
function getFlash()
{
        return window.document.Note_player;
}
function play(file)
{
        getFlash().playjs(file);
}
function stop()
{
        getFlash().stopjs();
} 
-->
</script>





<!--
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="1" height="1" id="Note_player" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="Note_player.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="Note_player.swf" quality="high" bgcolor="#ffffff" width="1" height="1" name="Note_player" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>-->


<div id="clavier">
	<object id="Note_player" type="application/x-shockwave-flash" data="Note_player.swf" width="1" height="1">
	  <param name="movie" value="Note_player.swf" />
	</object>

	<?php
	clavier($_GET["nb_octave"],$_GET["begin"]);
	?>
</div>
</body>
</html>
