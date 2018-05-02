<?php
function bbCode($texte)
// remplace les balises BBCode par des balises HTML
{
   $texte = preg_replace('`\[url=([http://].+?)](.+?)\[/url]`si','<a href="#" onclick="window.open(\'$1\',\'popup\');" title="$1">$2</a>',$texte); 
	$texte = preg_replace('`\[url=(.+?)](.+?)\[/url]`si','<a href="#" onclick="window.open(\'http://$1\',\'popup\');" title="$1">$2</a>',$texte); 
	$texte = preg_replace('`\[url]([http://].+?)\[/url]`si','<a href="#" onclick="window.open(\'$1\',\'popup\');" title="$1">$1</a>',$texte); 
	$texte = preg_replace('`\[url](.+?)\[/url]`si','<a href="#" onclick="window.open(\'http://$1\',\'popup\');" title="$1">$1</a>',$texte); 
	     
	// Bloc des balises [b]-[/b]
	$texte = preg_replace('#\[b](.+?)\[/b]#si','<strong>$1</strong>',$texte); 
	
	// Bloc des balises [u]-[/u]
	$texte = preg_replace('#\[u](.+?)\[/u]#si','<span style="text-decoration:underline;">$1</span>',$texte);
	
	// Bloc des balises [i]-[/i]
	$texte = preg_replace('#\[i](.+?)\[/i]#si','<em>$1</em>',$texte);
	
	// Bloc des balises [strike]-[/strike]
	$texte = preg_replace('#\[strike](.+?)\[/strike]#si','<span style="text-decoration:line-through;">$1</span>',$texte); 
	
	// Bloc des balises [overline]-[/overline]
	$texte = preg_replace('#\[overline](.+?)\[/overline]#si','<span style="text-decoration:overline;">$1</span>',$texte); 
	
	// Bloc des balises [quote]-[/quote]
	$texte = preg_replace('#\[quote=me](.+?)\[/quote]#si','<br /><strong>J\'ai écrit</strong> :<br/><div class="quote">$1 </div><br />',$texte); 
	$texte = preg_replace('#\[quote=(.+?)](.+?)\[/quote]#si','<br /><strong>$1 a écrit</strong> :<br/><div class="quote">$2 </div><br />',$texte); 
	$texte = preg_replace('#\[quote](.+?)\[/quote]#si','<br /><strong>Citation</strong> :<br/><div class="quote">$1 </div><br />',$texte); 
	
	// Bloc des balises [img]-[/img]
	$texte = preg_replace('#\[img=(.+?)](.+?)\[/img]#si','<a href="$1"><div class="img01"><img class="imageforum" src="$1" alt="$2"/></div></a>',$texte);
	$texte = preg_replace('#\[img](.+?)\[/img]#si','<a href="$1"><div class="img01"><img class="imageforum" src="$1" /></div></a>',$texte);
	
	// Bloc des balises [mail]-[/mail]
	$texte = preg_replace('#\[mail=([mailto:].+?)](.+?)\[/mail]#si','<a href="$1">$2</a>',$texte); 
	$texte = preg_replace('#\[mail=(.+?)](.+?)\[/mail]#si','<a href="mailto:$1">$2</a>',$texte); 
	$texte = preg_replace('#\[mail]([mailto:].+?)\[/mail]#si','<a href="$1">$1</a>',$texte); 
	$texte = preg_replace('#\[mail](.+?)\[/mail]#si','<a href="mailto:$1">$1</a>',$texte); 
	
	// Bloc des balises [align]-[/align]
	$texte = preg_replace('#\[align=(left|center|right)](.+?)\[/align]#si','<div style="text-align:$1; width:100%;">$2</div>',$texte); 
	
	// Bloc des balises [color]-[/color]
	$texte = preg_replace('#\[color=(.+?)](.+?)\[/color]#si','<span style="color:$1;">$2</span>',$texte); 
	
	// Bloc des balises [size]-[/size]
	$texte = preg_replace('#\[size=([0-9]{1,2})](.+?)\[/size]#si','<span style="font-size:$1px;">$2</span>',$texte); 
	
	// Bloc des balises [thick]-[/thick]
	$texte = preg_replace('#\[thick=([0-9]{1,3})](.+?)\[/thick]#si','<span style="font-weight:$1px;">$2</span>',$texte); 
	
	// Bloc des balises [style]-[/style]
	$texte = preg_replace('#\[style=(normal|italique|oblique)](.+?)\[/style]#si','<span style="font-style:$1;">$2</span>',$texte); 
	
	// Bloc des balises [weight]-[/weight]
	$texte = preg_replace('#\[weight=(lighter|bold|bolder)](.+?)\[/weight]#si','<span style="font-weight:$1;">$2</span>',$texte); 
	
	// Bloc des balises [decoration]-[/decoration]
	$texte = preg_replace('#\[decoration=(underline|line-through|overline|blink)](.+?)\[/decoration]#si','<span style="font-weight:$1;">$2</span>',$texte); 
	
	// Bloc des balises [font]-[/font]
	$texte = preg_replace('#\[font=(.+?)](.+?)\[/font]#si','<span style="font-family:$1;">$2</span>',$texte);

	// Bloc des balises [list]-[/list]
	$texte = preg_replace('`\[list=(circle|disc|square|i)](.+?)\[/list]`si','<ul type="$1">$2</ul>',$texte);
	$texte = preg_replace('`\[list](.+?)\|/list]`si','<ul>$1</ul>',$texte);
	
	// Bloc des balises [*]
	$texte = preg_replace('`\[\*=(circle|disc|square|i)](.+?)`si','<li type="$1">$2',$texte);
	$texte = preg_replace('`\[\*](.+?)`si','<li>$1',$texte);

	//Bloc des balises [spoil]-[/spoil]
	$texte = preg_replace('`\[spoil\](.+)\[/spoil\]`isU', '<span class="spoilertexte">Texte caché : cliquez sur le cadre pour l\'afficher</span><div class="spoiler" onclick="switch_spoiler(this)"><div style="visibility: hidden;" class="spoiler3">$1</div></div>', $texte); 


   return $texte;
}
?>
