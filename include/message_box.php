<?php
#This php file contains one function to output an input like the one you find in forums to post new messages

function message_box($contenu="")
{
    ?>
    <script language="Javascript" type="text/javascript" src="js/bbcode.js"></script>
    <p>
    Contenu :<br />
    <select onchange="insertTag('[size=' + this.options[this.selectedIndex].value  + ']', '[/size]','textarea', 'textarea');">
	    <option value="none" class="selected" selected="selected">Taille</option>
	    <option value="5">Très très petit</option>
	    <option value="10">Très petit</option>
	    <option value="15">Petit</option>
	    <option value="20">Gros</option>
	    <option value="25">Très gros</option>
	    <option value="30">Très très gros</option>

    </select>
    <button type="button" onclick="insertTag('[img]', '[/img]','textarea', 'textarea');" name="img">img</button>
    <button type="button" onclick="insertTag('[i]', '[/i]','textarea', 'textarea');" name="i">italique</button>
    <button type="button" onclick="insertTag('[b]', '[/b]','textarea', 'textarea');" name="b">gras</button>
    <button type="button" onclick="insertTag('[justify]', '[/justify]','textarea', 'textarea');" name="justify">justifier</button>
    <button type="button" onclick="insertTag('[url=]', '[/url]','textarea', 'textarea');" name="url">url</button>
    <button type="button" onclick="insertTag('[center]', '[/center]','textarea', 'textarea');" name="center">centré</button>
    <button type="button" onclick="insertTag('[img]', '[/img]','textarea', 'textarea');" name="img">image</button>
    <button type="button" onclick="insertTag('[right]', '[/right]','textarea', 'textarea');" name="right">droit</button>
    <button type="button" onclick="insertTag('[left]', '[/left]','textarea', 'textarea');" name="left">gauche</button>
    <button type="button" onclick="insertTag('[color=]', '[/color]','textarea', 'textarea');" name="color">couleur</button>
    <button type="button" onclick="insertTag('[email]', '[/email]','textarea', 'textarea');" name="email">email</button>
    <img src="http://users.teledisnet.be/web/mde28256/smiley/smile.gif" alt=":)" onclick="insertTag(':)', '', 'textarea');" />
    <img src="http://users.teledisnet.be/web/mde28256/smiley/unsure2.gif" alt=":euh:" onclick="insertTag(':euh:', '', 'textarea');" />
    <br/>
    <textarea id="textarea" name="contenu" cols="50" rows="10"><?=$contenu?></textarea>
    <br />
    <?php
}

?>
