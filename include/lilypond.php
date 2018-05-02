<?php
require_once("include/mysql.php");
DEFINE('TOLY',"musicxml2ly ");
DEFINE('LILY',"lilypond --png ");
DEFINE('OUTPUT_LOG',' >> logs/command.txt');
DEFINE('LY_DIR',"ly/");
DEFINE('PNG_DIR',"png/");
DEFINE('MIDI_DIR','midi/');
DEFINE('CLEAN','./clean_midi_ps.sh ');
DEFINE('TO_MP3','./convert_midi_to_mp3.sh ');
/*
\header {
  composer = "Composer"
  tagline =  \markup{ \column{ \center-align{"Generated By Y.D D.B from ppiv.ovh.org "} \center-align{"Music engraving by Lilypond version---www.lilypond.org"}
  }}
}

*/

function add_midi($path,$nb_beats,$reference/* tempo quarter(noire,blanche,croche)*/)
{
	
        $string=file_get_contents($path);
#        $string=preg_replace("#score definition#","score definition \n \score {",$string);
#        $string.="\n 
#        	\midi{
#        		\context {
#      				\Score
#     				 tempoWholesPerMinute = #(ly:make-moment $nb_beats $reference)
#			}
#        	} \n
#         		\layout {}\n 
#         }";
		$string=preg_replace("#layout {}#","layout {} \\midi{ \\tempo $reference = $nb_beats } ",$string);
        $file=fopen($path,"w");
        fwrite($file,$string);
        fclose($file);
}

function music_xml_to_png($file,$nb_beats,$reference)
{
	// On purge le répertoire png/
	exec("rm png/*.ps");
	$out=str_replace(" ","",microtime());
        $out=str_replace(".","",$out);
        $out=escapeshellcmd($out);
        $command=TOLY." -o \"".LY_DIR.$out."\" ".$file;
        $command=escapeshellcmd($command);
        
        $out_r=exec($command.OUTPUT_LOG,$output=array(),$ret);
        command_log($command.OUTPUT_LOG." was executed by function music_xml_to_png: the function returned ".print_r($output,true)."and ".$out_r.$ret);
        /*$fp=fopen(LY_DIR.$out.".ly","r+");
        $string="\\header {
  tagline =  \\markup{ \\column{ \\center-align{\"Generated By Y.D D.B from ppiv.ovh.org \"} \\center-align{\"Music engraving by Lilypond version---www.lilypond.org\"}
  }}
}";
        fseek($fp,0,SEEK_END);
        fwrite($fp,$string,strlen($string));
        fclose($fp);*/
        // Convert lilypond to png and midi
        add_midi(LY_DIR.$out.".ly",$nb_beats,$reference);
        $command2=LILY." -o \"".PNG_DIR.$out."\" "." \"".LY_DIR.$out.".ly\"";
        $command2=escapeshellcmd($command2);
        $out_r2=exec($command2.OUTPUT_LOG,$output2=array(),$ret);
        command_log($command2.OUTPUT_LOG." was executed by function music_xml_to_png: the function returned ".print_r($output2,true)."and ".$out_r2.$ret);
        
        
        return $out;
}

function midi_to_mp3($midi)
{
	$out3=str_replace(" ","",microtime());
        $out3=str_replace(".","",$out3);
        $out3=escapeshellcmd($out3);
        $command3=TO_MP3.$midi." ".$out3.OUTPUT_LOG;
        $out_r3=exec($command3,$output3=array(),$ret);
        command_log($command3." was executed by function midi_to_mp3: the function returned ".print_r($output3,true)."and ".$out_r3.$ret);
        return $out3;
}


function clean_files($name)
{
	$out3=str_replace(" ","",microtime());
        $out3=str_replace(".","",$out3);
        $out3=escapeshellcmd($out3);
        $command3=CLEAN." $name".OUTPUT_LOG;
        $out_r3=exec($command3,$output3=array(),$ret);
        command_log($command3." was executed by function clean: the function returned ".print_r($output3,true)."and ".$out_r3.$ret);
        //exec("rm ly/$name.ly");
        return $out3;
}

?>
