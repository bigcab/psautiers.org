<?php
class histogram_class
{
	var $width;
	var $height;
	var $border_x;
	var $border_y;
	var $spacing;
	var $image;
	var $noir;
	var $colors;
	
	function histogram_class($width=250,$height=250,$border_x=10,$border_y=40,$spacing=4)
	{
		$this->width=$width;
		$this->height=$height;
		$this->border_x=$border_x;
		$this->border_y=$border_y;
		$this->spacing=$spacing;
		$this->image=imagecreate($this->width+2*$border_x,$this->height+2*$border_y);
		
		
		
		$this->blanc= imagecolorallocate($this->image,255,255,255);
		$this->noir=imagecolorallocate($this->image,0,0,0);	
		
		$this->init_colors();
		
		$this->line(0,0,$this->width,0,$this->noir);
		$this->line($this->width,0,$this->width,$this->height,$this->noir);
		$this->line($this->width,$this->height,0,$this->height,$this->noir);
		$this->line(0,$this->height,0,0,$this->noir);
	}
	
	function line($x_i,$y_i,$x_f,$y_f,$color)
	{
		imageline($this->image,$this->border_x+$x_i,$this->border_y+$y_i,$this->border_x+$x_f,$this->border_y+$y_f,$color);
	}
	
	function affich()
	{
		imagepng($this->image);
		imagedestroy($this->image);
	}
	
	
	function rectangle($x_i,$y_i,$x_f,$y_f,$color)
	{
		imagefilledrectangle($this->image,$this->border_x+$x_i,$this->border_y+$y_i,$this->border_x+$x_f,$this->border_y+$y_f,$color);
	}	
	
	//$i from 0 to 11
	function bar($i,$percent)
	{
		$police="./include/DejaVuSans.ttf";
		$output=array("Do ","Do#","Ré ","Mi♭/\nRé#","Mi ","Fa " ,"Fa#", "Sol ","Sol#","La ","Si♭/\nLa#","Si ");
		$pas_x=$this->width/12;
		$y=$this->height*$percent/100;
		$x_i=$pas_x*$i+$this->spacing;
		$y_i=$this->height;
		$y_f=$y_i-$y;
		$x_f=$x_i+$pas_x -2*$this->spacing;
		$this->rectangle($x_i,$y_i,$x_f,$y_f,$this->colors[$i]);
		imagettftext($this->image,10*$this->width/600,0,$x_i+$this->border_x,$this->border_y+$y_f,$this->noir,$police,number_format($percent,1)."%");		
		/*
		450 -> 10
		$this->width -> x
		*/
		imagettftext($this->image,10*$this->width/450,0,$x_i+$this->border_x,3/2*$this->border_y+$this->height,$this->noir,$police,$output[$i]);
	}
	
	function init_colors()
	{
		$colors=array(
			array(255,0,0),
			array(255,0,255),
			array(0,0,255),
			array(0,255,255),
			array(0,255,0),
			array(255,255,0),
			array(251,154,11),
			array(226,151,226),
			array(155,155,235),
			array(156,225,225),
			array(156,239,156),
			array(240,240,153),
			array(0,0,0)
		);
		$max=pow(256,3);
		$pas=$max/12;
		$c=0;
		$this->colors=array();
		
		for($i=0; $i<12 ; $i++)
		{
			$r=$c%256;
			$g=(($c-$r)/256)%256;
			$b=( (($c-$r)/256) -$g  ) /256;
			array_push($this->colors,imagecolorallocate($this->image,$colors[$i][0],$colors[$i][1],$colors[$i][2]));
			$c+=$pas;
		}
	}
	
	function results($tableau)
	{
		for ($i=0;$i<12;$i++)
		{
			if(!isset($tableau[$i]))
			{
				$tableau[$i]=0;
			}
			$this->bar($i,$tableau[$i]);
		}
		
	}
}


$jquery_histogram="<script type='text/javascript' src='./jquery/jquery.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.core.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.widget.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.draggable.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.mouse.js'></script>
        <script type='text/javascript' src='./jquery/ui/jquery.ui.sortable.js'></script>
        <script type='text/javascript' ><!--
	        \$(function() {
	                var \$trash=$('#trash');
	                var \$main=$('#main_sortable');
	        		var \$sortable = $( \".sortable\" );
		        \$sortable.sortable({
			        placeholder: 'ui-state-highlight',
			        change: function (event, ui) { update(\$(this));},
			        start: function (event, ui) { update(\$(this));},
			        create: function (event, ui) { update(\$(this));},
			        stop:function (event, ui) { update(\$(this));}
		        });
		        \$sortable.disableSelection();
		        // there's the gallery and the trash



		// let the gallery items be draggable
		$( \".draggable\", \$sortable ).draggable({
			cancel: \"a.ui-icon\", // clicking an icon won't initiate dragging
			revert: \"invalid\", // when not dropped, the item will revert back to its initial position
			helper: \"clone\",
			cursor: \"move\"
		});
function update(\$container)
{
    var l=\$('li' , \$container).length;
    var i;
    var i0=\$('li',\$container).index(\$('#separation'));
    var string='';
    var sum=0;
    for (i=0 ; i< l ; i++ )
    {
        var elem=\$('li', \$container)[i];
        var id=elem.getAttribute('id');
        string= string + ' ' + id;
        if(i > i0)
        {

                \$(elem).removeClass(id).addClass('nothing');
            
        }
        else if (i < i0)
        {
            sum=parseInt(\$('input',elem).attr('value'))+sum;
            if(\$(elem).hasClass('nothing'))
            {
                \$(elem).removeClass('nothing').addClass(id);
            }
            

        }
    }
    i0=\$('li',\$container).index(\$('#separation'));
    i=0;
    sum=Math.min(100,sum);
    \$('#sum').attr('value',sum+'%');
    \$('#sum_trash').attr('value',(100-sum)+'%');
    if((sum!=0)&&(!isNaN(sum)))
    {
        for (i=0 ; i< l ; i++ )
        {
            var elem=\$('li', \$container)[i];
            var id=elem.getAttribute('id');
            if (i < i0)
            {

                var orig_value=\$('.orig_hidden',elem).attr('value');
                var old_value=\$('.dynamic_hidden',elem).attr('value');
                var new_value=Math.floor(parseInt(orig_value)*100/sum);
                var old_class='b'+old_value;
                var new_class='b'+new_value;
                \$('.dynamic_hidden',elem).attr('value',new_value);
                \$(elem).attr('class',new_class+' ' + id + ' draggable');
                \$('.box',elem).empty().html(new_value+'%');
                
            }
            else if (i < i0)
            {
                
                var orig_value=\$('.orig_hidden',elem).attr('value');
                \$(elem).attr('class',orig_value+' nothing draggable');
            }
        }
    }
};	

});
	        --></script>";
$css_histogram="<link href='css/style_histogram.css' rel='stylesheet' type='text/css' /> 
<link href='./jquery/themes/base/jquery.ui.all.css' rel='stylesheet' type='text/css' /> ";
// default is list_notes_full
function jquery_histogram($tableau,$option="default")
{
    ?>
    Total activé: <input id="sum" type="text" disabled='disabled' value="100"/> Total désactivé : <input id="sum_trash" disabled='disabled' value="0"/> 
    <br/>
    
    <ul id="sortable" class="sortable">



        <?php
        $list_notes_full=array('do'=>'Do','dod'=>'Do#','reb'=>'Re&#9837;','re'=>'Re','red'=>'Ré#','mib'=>'Mi&#9837;',
                            'mi'=>'Mi','fa'=>'Fa','fad'=>'Fa#','solb'=>'Sol&#9837;',
                            'sol'=>'Sol','sold'=>'Sol#','lab'=>'La&#9837;','la'=>'La','lad'=>'La#','sib'=>'Si&#9837;','si'=>'Si');
        $list_notes_partial=array("do"=>"Do ","dod"=>"Do#","re"=>"Ré ","mib"=>"Mi&#9837;/<br/>Ré#",
                                    "mi"=>"Mi ","fa"=>"Fa " ,"fad"=>"Fa#","sol"=> "Sol ","lab"=>"Sol#/<br/>La&#9837;","la"=>"La ","sib"=>"Si&#9837;/<br/>La#","si"=>"Si ");
        if( $option =="default")
        {
            foreach ($list_notes_full as $note=> $string )
            {
                ?>
                <li  id="<?=$note?>" class="b<?=$tableau[$i]?> draggable <?=$note?>">
                    <input type='hidden' class="orig_hidden" value="<?=$tableau[$i]?>"/>
                    <input type='hidden' class="dynamic_hidden" value="<?=$tableau[$i]?>"/>
                    <div class="bar_haut" ></div>
                    <div class='box' ><?=$_GET['note']?>%</div>
                    <div class="bar_bas" ></div><?=$string?>
                </li>
                <?php
            }
        }
        else
        {
            $i=0;
            foreach ($list_notes_partial as  $note=>$string )
            {
                ?>
                <li id="<?=$note?>" class="b<?=$tableau[$i]?> draggable <?=$note?>">
                    
                    <input type='hidden' class="orig_hidden" value="<?=$tableau[$i]?>"/>
                    <input type='hidden' class="dynamic_hidden" value="<?=$tableau[$i]?>"/>
                    <div class="bar_haut" ></div><div class='box' >
                        <?=$tableau[$i]?>%
                    </div>
                    <div class="bar_bas" >
                    </div><?=$string?>
                </li>
                <?php
                $i++;
            }
        }
        ?>
	                            <!-- barre de séparation-->
	                            <li id="separation" style="width: 1px; margin: 10px 0 10px; padding: 40px;"><div style=" width: 1px; height: 100%;background-color: black;"></div></li>         

	                            
                                                                          
    </ul>

    

    <?php
}



?>
