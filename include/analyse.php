<?php
require_once("include/markov.php");
require_once("include/note.php");
require_once ("include/xml.php");
require_once("include/math.php");
require_once("include/config.php");
/*$tableau=array("Do","Do#", "Ré♭","Ré","Ré#", "Mi♭" ,"Mi","Fa" ,"Fa#", "Sol♭" ,"Sol","Sol#","La♭","La","La#","Si♭","Si");*/

/*
liste des fonctions 

classes
    gamme_class
        
    analyse_class
        analyse_class()
        analyse()
        output_line($tableau,$format,$end,$title)
        output_matrix()
        output_line
        output_tableau
        output()
        output_by_measures
        output_successions
        fragment()
        output_analyse_fragment()
        
product()
sum()
scalar_product()

 */   


// TT3TT
$penta=new gamme_class(array(0,2,4,7,9));
$majeure=new gamme_class(array(0,2,4,5,7,9,11));
$mineure=new gamme_class(array(0,2,3,5,7,8,11));


$majeure_interval=new gamme_by_interval_class(array("2M","2M","2m","2M","2M","2M"));
$mineure_interval=new gamme_by_interval_class(array("2M","2m","2M","2M","2m","2a"));



$penta_interval=new gamme_by_interval_class(array("2M","2M","3m","2M"));



init_get_var("special");

class analyse_class
{
	var $xml;
	var $filename;
	
	var $frequence_notes_absolues;
	var $frequence_notes_detailed;
	
	
	
	var $frequence_notes_detailed_by_measures; // on va calculer chaque coeff par mesures
	var $frequence_notes_absolues_by_measures;	
		
	var $occurrence_notes_absolues;
	var $nb_notes;
	
	var $majeure;
	var $mineure;
	var $penta;
	
	var $majeure_interval;
	var $mineure_interval;
	var $penta_interval;
	
	var $penta_monika;
	var $majeure_monika;
	var $mineure_monika;
	
	// here for non absolute (detailed)
	var $frequence_notes;
	var $occurrence_notes;
	
	//value in int
	var $interval_values;
	//value of form 4j (quarte juste)
	var $intervals_by_phrase;
	
	var $intervals; // by phrase merged
	var $phrases;
	
	var $finales_internes;
	
	var $extremas;
	var $extremas_sup;
	var $extremas_inf;
	
	var $mesure_invariante;
	
	var $successions_interval_note;
	var $successions_note_interval;
	
	var $succession_note_av_note_ap;
	var $succession_note_ap_note_av;
	
	
	var $majeure_monika_interval_by_measures;
	var $mineure_monika_interval_by_measures;
	
	
	var $frequence_notes_fragment_detailed;
	var $majeure_fragment;
	var $mineure_fragment;
	var $fragment_absolues;
	var $best_fragment;
	var $max_fragment;
	
	
	/*var $max_majeure_keys;
	var $max_mineure_keys;*/
	var $max;
	var $best;
	function analyse_class($filename)
	{
		global $majeure;
		global $mineure;
		global $penta;
		
		global $majeure_interval;
		global $mineure_interval;
		global $penta_interval;
		$this->filename=$filename;
		$this->xml=new music_xml_class($this->filename);
		
		$this->analyse();
		
		$this->majeure=identify_gamme($majeure,$this->frequence_notes_absolues);
		$this->mineure=identify_gamme($mineure,$this->frequence_notes_absolues);
		$this->penta=identify_gamme($penta,$this->frequence_notes_absolues);
		
		$this->penta_interval=identify_gamme($penta_interval,$this->frequence_notes_detailed,"interval");
		$this->majeure_interval=identify_gamme($majeure_interval,$this->frequence_notes_detailed,"interval");
		$this->mineure_interval=identify_gamme($mineure_interval,$this->frequence_notes_detailed,"interval");
		//print_r($penta_interval->correct_notes($this->frequence_notes_detailed,"C"));
		$this->penta_monika=identify_gamme($penta,$this->frequence_notes_absolues,"monika");
		$this->majeure_monika=identify_gamme($majeure,$this->frequence_notes_absolues,"monika");
		$this->mineure_monika=identify_gamme($mineure,$this->frequence_notes_absolues,"monika");
		
		/*$this->max_majeure_keys=array();
		$this->max_mineure_keys=array();*/
		$this->max=array(array());
		$this->best=array(array());
		//print_r($this->frequence_notes_detailed_by_measures);
		foreach($this->frequence_notes_detailed_by_measures as $key=> $elem)
		{
			
#			$this->mineure_monika_interval_by_measures[$key]=array_map("round",identify_gamme($mineure_interval,$elem,"interval_monika"));
#			$this->majeure_monika_interval_by_measures[$key]=array_map("round",identify_gamme($majeure_interval,$elem,"interval_monika"));
#			
#			$this->mineure_monika_interval_by_measures[$key]=array_map("round",identify_gamme($mineure_interval,$elem,"interval"));
#			$this->majeure_monika_interval_by_measures[$key]=array_map("round",identify_gamme($majeure_interval,$elem,"interval"));
#			$this->mineure_monika_interval_by_measures[$key]=array_map("round",identify_gamme($mineure_interval,$elem,"interval"));
			
			if($_GET["special"]=="interval")
			{
				$this->mineure_monika_interval_by_measures[$key]=identify_gamme($mineure_interval,$elem,"interval");
				$this->majeure_monika_interval_by_measures[$key]=identify_gamme($majeure_interval,$elem,"interval");
			}
			else
			{
				$this->mineure_monika_interval_by_measures[$key]=identify_gamme($mineure_interval,$elem,"interval_monika");
				$this->majeure_monika_interval_by_measures[$key]=identify_gamme($majeure_interval,$elem,"interval_monika");
			}
			$this->max[$key]=array_merge(concat_key($this->majeure_monika_interval_by_measures[$key],"majeur"),concat_key($this->mineure_monika_interval_by_measures[$key],"mineur"));
			//$this->max[$key]=array_map("intval",$this->max[$key]);
			$this->max[$key]=array_filter($this->max[$key],"non_null");
			arsort($this->max[$key],SORT_NUMERIC);
			
			//$this->max[$key]= array_merge(array_map("concat_majeur_s" , array_keys($this->majeure_monika_interval_by_measures[$key],$a ) ), array_map("concat_mineur_s",array_keys($this->mineure_monika_interval_by_measures[$key],$a))  ) ;
			//sort($this->max[$key],SORT_NUMERIC);
			
			$a=max($this->majeure_monika_interval_by_measures[$key]);
			$b=max( $this->mineure_monika_interval_by_measures[$key]);
			//echo "$key => majeur : $a mineur : $b <br/>";
			if( ($a - $b) > 0.1)
			{
				//echo "<!-- measure $key a>b -->";
				$this->best[$key]=array_map("concat_majeur" , array_keys($this->majeure_monika_interval_by_measures[$key],$a ) );
			}
			else if( ($b - $a) > 0.1 )
			{
				//echo "<!-- measure $key a<b -->";
				$this->best[$key]=array_map("concat_mineur", array_keys($this->mineure_monika_interval_by_measures[$key],$b)) ;
			}
			else
			{
				//echo "<!-- measure $key else -->";
				$this->best[$key]= array_merge( 
				array_map("concat_majeur",array_keys($this->majeure_monika_interval_by_measures[$key],$a)), 
				array_map("concat_mineur",array_keys($this->mineure_monika_interval_by_measures[$key],$b))  
				) ;
				//$this->best[$key]= array_map("concat_majeur" , array_keys($this->majeure_monika_interval_by_measures[$key],$a ) ) + array_map("concat_mineur",array_keys($this->mineure_monika_interval_by_measures[$key],$b))  ;
			}
		}
		
		unset($this->max[0]);
		unset($this->best[0]);
		
		//print_r($this->majeure_monika_interval_by_measures);
		//$this->majeure_monika_interval_by_measures=identify_gamme($majeure_interval,$this->frequence_notes_detailed,"interval_monika");
		$this->mesure_invariante = do_all_markov($this->successions_note_av_note_ap);
	}
	
	
	
	
	function analyse()
	{
		global $tableau_correspond_int_note;
		global $correspondances;
		global $alterations;
		
		$this->frequence_notes_absolues=array_fill(0,12,0);
		$this->frequence_notes_detailed=array();
		
		//of form tableau[measure][note]=statistics
		$this->frequence_notes_detailed_by_measures=array_fill(1,$this->xml->max_nb_measures,array());
		$this->frequence_notes_absolues_by_measures=array_fill(1,$this->xml->max_nb_measures,array_fill(0,12,0));
		
#		now initialization of this table 
			
		$this->occurrence_notes_absolues=array_fill(0,12,0);
		
		
#		tableau has type frequence_notes[$tableau_correspond_int_note[$note_info->step] ][$note_info->octave][$alter]
#		$this->occurrence_notes[$tableau_correspond_int_note[$note_info->step] ][$note_info->octave][$alter]
#		initiating this table
		$this->frequence_notes=array(array(array()));
		$this->occurrence_notes=array(array(array()));
		$i=0;
		for($i=0; $i<7 ; $i++ )
		{
			$this->frequence_notes[$i]=array_fill(0,11, array());
			$this->occurrence_notes[$i]=array_fill(0,11, array());
			foreach($this->frequence_notes[$i] as &$elem)
			{
				$elem=array(0 => 0, 
							-1=> 0,
							-2 => 0,
							1 => 0,
							2 => 0);
							
			}
			foreach($this->occurrence_notes[$i] as &$elem)
			{
				$elem=array(0 => 0, 
							-1=> 0,
							-2 => 0,
							1 => 0,
							2 => 0);
							
			}
		}
		

		
		$this->intervals_by_phrase=array(array());
		$this->intervals=array();
		$this->extremas=array();
		$this->finales_internes=array();
		
		$this->successions_interval_note=array(array());
		$this->successions_note_interval=array(array());
		
		$this->successions_note_av_note_ap=array(array());
		$this->successions_note_ap_note_av=array(array());
		
		$this->extremas_sup=array();
		$this->extremas_inf=array();
		
		$phrase=0;
		$duree_totale=0;
		$this->nb_notes=0;
		//$note=$this->xml->get_first_note();
		//$note_info=$note->note_info;
		
#		initiating silence
		$this->frequence_notes_absolues["silence"]=0;
		$this->occurrence_note_absolues["silence"]=0;
		$this->frequence_notes_detailed["silence"]=0;
						
		foreach($this->xml->parts as $part)
		{
			foreach($part->measures as $measure)
			{
				foreach($measure->notes as $note)
				{
					$note_info=$note->note_info;
					
					//silence are also taken into account
					//$value=note_class_to_rythm_value($note);
					$value=$note->duration;
					$duree_totale+=$value;
					
					if(!empty($note_info->step) )
					{
						
						$alter=$note_info->alter;
						
						
						
						if(empty($alter))
						{
							$alter=0;
						}
						if(!empty($last_step))
						{
							$int=convert_to_full_interval($last_step,$note_info->step,$last_octave,$note_info->octave,$last_value,$note_info->note_value);
							
							
							if(!isset($this->intervals_by_phrase[$phrase]))
							{
								$this->intervals_by_phrase[$phrase]=array();
							}
							if(!isset($this->intervals_by_phrase[$phrase][$int]))
							{
								$this->intervals_by_phrase[$phrase][$int]=0;
							}
							$this->intervals_by_phrase[$phrase][$int]+=1;
							$sign=$note_info->note_value-$last_value;
							if($last_alter!=0)
							{
								$last_note_name=$last_step.$last_octave.$alterations[$last_alter];
								

							}
							else
							{
								$last_note_name=$last_step.$last_octave;
								
							}
							if(!isset($this->successions_interval_note[$int]))
							{
								$this->successions_interval_note[$int]=array();
							}
							if(!isset($this->successions_interval_note[$int][$last_note_name]))
							{
								$this->successions_interval_note[$int][$last_note_name]=0;
							}
							$this->successions_interval_note[$int][$last_note_name]+=1;
							
							
							if(!isset($this->successions_note_interval[$last_note_name]))
							{
								$this->successions_note_interval[$last_note_name]=array();
							}
							if(!isset($this->successions_note_interval[$last_note_name][$int]))
							{
								$this->successions_note_interval[$last_note_name][$int]=0;
							}
							$this->successions_note_interval[$last_note_name][$int]+=1;
							
							
							
							
							$note_name=$note_info->step.$note_info->octave.$alterations[$alter];
							
							
							
							
							if(!isset($this->successions_note_av_note_ap[$note_name]))
							{
								$this->successions_note_av_note_ap[$note_name]=array();
							}
							if(!isset($this->successions_note_av_note_ap[$note_name][$last_note_name]))
							{
								$this->successions_note_av_note_ap[$note_name][$last_note_name]=0;
							}
							$this->successions_note_av_note_ap[$note_name][$last_note_name]+=1;
							
							
							
							if(!isset($this->successions_note_ap_note_av[$last_note_name]))
							{
								$this->successions_note_ap_note_av[$last_note_name]=array();
							}
							if(!isset($this->successions_note_ap_note_av[$last_note_name][$note_name]))
							{
								$this->successions_note_ap_note_av[$last_note_name][$note_name]=0;
							}
							$this->successions_note_ap_note_av[$last_note_name][$note_name]+=1;
							
							
							
							if($sign * $last_sign <0)
							{
								if(!isset($this->extremas[$last_note_name]))
								{
									$this->extremas[$last_note_name]=0;
								}
								$this->extremas[$last_note_name]+=1;
								// distinction
								if($last_sign>0)
								{
									//$sign <0 
									if(!isset($this->extremas_sup[$last_note_name]))
									{
										$this->extremas_sup[$last_note_name]=0;
									}
									$this->extremas_sup[$last_note_name]+=1;
								}
								else 
								{
									// $sign >0 and last_sign<0
									if(!isset($this->extremas_inf[$last_note_name]))
									{
										$this->extremas_inf[$last_note_name]=0;
									}
									$this->extremas_inf[$last_note_name]+=1;
								}
							}
							if(!isset($this->phrases[$phrase]))
							{
								$this->phrases[$phrase]="";
							}
							if($sign>=0)
							{
								
								$this->phrases[$phrase].="+$int/";
							}
							else
							{
								
								$this->phrases[$phrase].="-$int/";
							}
							
							
							
						}
						else
						{
							// last step is empty
							if(!isset($this->phrases[$phrase]))
							{
								$this->phrases[$phrase]="";
							}
							$this->phrases[$phrase].=$note_info->step.$note_info->octave.$alterations[$alter]."/";
							$sign=+1;
						}
						
						$last_sign=$sign;
						$last_step=$note_info->step;
						$last_octave=$note_info->octave;
						$last_alter=$alter;
						$last_value=$note_info->note_value;
						
						
						
						$this->frequence_notes_absolues[
							modulo_spe($correspondances[$note_info->step]+
							$note_info->alter,12)]+=$value;
						
						
			
						
						
						
						//tableau[measure][note]	
						$this->frequence_notes_absolues_by_measures[$measure->measure_number][
							modulo_spe($correspondances[$note_info->step]+
							$note_info->alter,12)]+=$value/$measure->measure_duration*100;
						
						
						// now for occurrences of an absolute note
						$this->occurrence_notes_absolues[modulo_spe($correspondances[$note_info->step]+
							$note_info->alter,12)]+=1;
							
						if(!isset($this->nb_notes))
						{
							$this->nb_notes=0;
						}
						$this->nb_notes+=1;
						
						
						
						//detailed mode ,table already initiated
						$this->frequence_notes[$tableau_correspond_int_note[$note_info->step] ][$note_info->octave][$alter]+=$value;
#						echo "<br/>octave ; ".$note_info->octave;
#						echo "<br/>step ; ".$tableau_correspond_int_note[$note_info->step];
#						echo "<br/>alter : ".$alter;
						
						$this->occurrence_notes[$tableau_correspond_int_note[$note_info->step] ][$note_info->octave][$alter]+=1;
						
						
						if(!isset($this->frequence_notes_detailed[$note_info->step.$alterations[$alter]]))
						{
							$this->frequence_notes_detailed[$note_info->step.$alterations[$alter]]=0;
						}
						$this->frequence_notes_detailed[$note_info->step.$alterations[$alter]]+=$value;
						
						
						if(!isset($this->frequence_notes_detailed_by_measures[$measure->measure_number][$note_info->step.$alterations[$alter]]))
						{
							$this->frequence_notes_detailed_by_measures[$measure->measure_number][$note_info->step.$alterations[$alter]]=0;
						}
						$this->frequence_notes_detailed_by_measures[$measure->measure_number][$note_info->step.$alterations[$alter]]+=$value/$measure->measure_duration*100;
						
					}
					else
					{
						//note_info step is empty
						if((isset($last_alter))&&(isset($last_octave))&&(isset($last_step))&&(!empty($last_step)))
						{
							if(!isset($this->finales_internes[$last_step.$last_octave.$alterations[$last_alter]]))
							{
								$this->finales_internes[$last_step.$last_octave.$alterations[$last_alter]]=0;
							}
							$this->finales_internes[$last_step.$last_octave.$alterations[$last_alter]]+=1;
						}
						else
						{
#							if(!isset($this->finales_internes[$last_step.$last_octave]))
#							{
#								$this->finales_internes[$last_step.$last_octave]=0;
#							}
#							$this->finales_internes[$last_step.$last_octave]+=1;
						}
						$this->frequence_notes_absolues["silence"]+=$value;
						$this->occurrence_note_absolues["silence"]+=1;
						$this->frequence_notes_detailed["silence"]+=$value;
						$phrase+=1;
					}
					
				}
			}
		
		}
		
		unset($this->frequence_notes_absolues_by_measures[0]);
		unset($this->frequence_notes_detailed_by_measures[0]);
		
		
		
		
		foreach($this->frequence_notes_absolues_by_measures as &$tableau)
		{
			ksort($tableau,SORT_NUMERIC);
		}
		foreach($this->frequence_notes_detailed_by_measures as &$tableau)
		{
			uksort($tableau,"cmp_note");
		}
		//sort by measure number
		ksort($this->frequence_notes_absolues_by_measures,SORT_NUMERIC);
		ksort($this->frequence_notes_detailed_by_measures,SORT_NUMERIC);
		
		
		$this->frequence_notes_absolues=renormalize_without_silence($this->frequence_notes_absolues);
		$this->frequence_notes_detailed=renormalize_without_silence($this->frequence_notes_detailed);
		
#		print_r($this->frequence_notes_absolues);
		
		foreach($this->frequence_notes as &$step )
		{
			foreach($step as &$octave)
			{
				foreach($octave as &$alter)
				{
					$alter/=($duree_totale/100);
					//$alter=number_format($alter,2);
				}
			}
		}
		foreach($this->intervals_by_phrase as $array)
		{
			foreach ($array as $key => $value)
			{
				if(isset($this->intervals[$key]))
				{
					$this->intervals[$key]+=$value;
				}
				else
				{
					$this->intervals[$key]=$value;
				}
				
			}	
		}
		uksort($this->successions_interval_note,"cmp_interval");
		uksort($this->successions_note_interval,"cmp_note");
		
		uksort($this->extremas,"cmp_note");
		uksort($this->extremas_sup,"cmp_note");
		uksort($this->extremas_inf,"cmp_note");
		
		uksort($this->successions_note_av_note_ap,"cmp_note");
		uksort($this->successions_note_ap_note_av,"cmp_note");
		uksort($this->frequence_notes_detailed,"cmp_note");
		uksort($this->intervals,"cmp_interval");
		
		
		
	}	
	
	
	function output_mesure_invariante()
	{
		begin_box_js("Mesure invariante","mesure_invariant_box");
		?><td>
		<table>
			
			<tr>
				<?php
				$line1="";
				$line2="";
				foreach($this->mesure_invariante as $key => $value)
				{
					$line1 .= "<td>".$key . "</td>";
					$line2 .= "<td>".number_format ( $value * 100 , 1) ."%</td>"; 
				}
			
				?>
				<?=$line1?>
			</tr>
			<tr><?=$line2?></tr>
		</table></td>
		<?php
		end_box();
	}
	
	function output_line($tableau,$format,$end,$title)
	{
		
		?>
		<tr>
			<th><?=$title?></th>
		<?php
			for($i=0;$i<12;$i++)
			{
				?>
				<td><?= ((empty($tableau[$i]))?0:number_format($tableau[$i],$format) ).$end ?></td>
				<?php
			}
			?>
			<!--<td><?= ((empty($tableau["silence"]))?"X":number_format($tableau['silence'],$format).$end ) ?></td>-->
			<?php
		?>
		</tr>
		<?php
	}
	
	function output_matrix($t1,$t2)
	{
	
		?>
		<tr>
			<td></td>
		<?php
		foreach ($t2 as $key =>$value)
		{
			?>
				<th><?=$key?></th>	
			<?php
		}
		?>
		</tr>
		<?php
		foreach($t1 as $key=>$tableau)
		{
			?>
			<tr>
				<th><?=$key?></th>
				<?php
				foreach($t2 as $key2 =>$value)
				{
					?>
					<td><?=(empty($t1[$key][$key2])) ? "" :$t1[$key][$key2]?></td>
					<?php
				}
				?>
			</tr>	
			<?php
		}
	}
	
	function 
	output_tableau($tableau,$title,$title2,$format=2,$option="default")
	{
		?>
			<tr>
				<?php
				if ($option=="default")
				{
					?>
					<td><?=$title?></td>
					<?php
				}
				?>
				<th><?=$title2?></th>
			</tr>
		<?php
		if($option=="default")
		{
			foreach($tableau as $key=>$value)
			{ 
				?>
				<tr>
					<th><?=$key?></th>
					<td><?=(is_numeric($value))?number_format($value,$format):$value?></td>
				</tr>
				<?php
			}
		}
		else
		{
			foreach($tableau as $key=>$value)
			{ 
				?>
				<tr>
					<td><?=(is_numeric($value))?number_format($value,$format):$value?></td>
				</tr>
				<?php
			}
		}
	}
	
	function output($option="js",$fast="false")
	{
		$output=array("Do","Do#","Ré","Mi♭/Ré#","Mi","Fa" ,"Fa#", "Sol","Sol#","La","Si♭/La#","Si");
		global $tableau_correspond_int_note;
		if($option=="js")
		{	
			begin_box_js("Analyse détaillée","analyse"." box");
		}
		else
		{
			begin_box("Analyse détaillée","analyse");
		}
		?>
		<tr><td>
			<table> 
				<tr>
					<td></td>
					<?php
					foreach($output as $elem)
					{
						?>
						<th><?=$elem ?></th>
						<?php
					}
					?>
				</tr>
		<?php
		$this->output_line($this->frequence_notes_absolues,1,"%","Durée");
		$this->output_line($this->occurrence_notes_absolues,0,"","Occurences");
		$this->output_line($this->penta,1,"%","<acronym title=\"Score de l'échelle TT3T en partant de la note ...\">Pentatonicité</acronym> <br/><br/>pondérée");
		$this->output_line($this->penta_monika,1,"%","<acronym title=\"Score de l'échelle TT3T en partant de la note ...\">Pentatonicité</acronym><br/>Monika");
		$this->output_line($this->majeure,1,"%","<acronym title='... ou \"diatonicité\", structure TTSTTT/'>Majeure</acronym>:<br/> pondérée");
		$this->output_line($this->majeure_monika,1,"%","<acronym title='...ou \"diatonicité\", structure TTSTTT/'>Majeure</acronym>: <br/>Monika");
		$this->output_line($this->mineure,1,"%","<acronym title='Mineure harmonique'>Mineure</acronym> :<br/>pondérée");
		$this->output_line($this->mineure_monika,1,"%","<acronym title='Mineure harmonique'>Mineure</acronym> <br/>Monika");
		?>
			</table>
		</td></tr>
		<tr>
			<td>
				<table>
				<tr>
					<td>
						<table>
						<?php
						$this->output_tableau($this->finales_internes,"","Finales internes",0);
						?>
						</table>
					</td>
					<td>	
						<table>
						<?php
						$this->output_tableau($this->intervals,"Intervalles","Occurrences",0);
						?>
						</table>
					
						
					</td>
					
					<td >
						<table>
						<?php
						$this->output_tableau($this->extremas,"","Pics mélodiques (supérieurs et inférieurs)",0);
						?>
						</table>
					</td>
					
				</tr>
				<tr>
					<td>
						<table>
						<?php
						$this->output_tableau($this->extremas_sup,"","Pics mélodiques supérieurs",0);
						?>
						</table>
					</td>
					<td>
						<table>
						<?php
						$this->output_tableau($this->extremas_inf,"","Pics mélodiques inférieurs",0);
						?>
						</table>
					</td>
					<td>
						<table>
						<?php
						$this->output_tableau($this->frequence_notes_detailed,"","Fréquence des hauteurs nominales",2);
						?>
						</table>
					</td>
					
				</tr>
				<tr>
					<td>
						<table>
						<?php
						$this->output_tableau($this->penta_interval,"","Pentatonicité (hauteurs nominales)",2);
						?>
						</table>
					</td>
					<td>
						<table>
						<?php
						$this->output_tableau($this->majeure_interval,"","Majeure (hauteurs nominales)");
						?>
						</table>
					</td>
					<td>
						<table>
						<?php
						$this->output_tableau($this->mineure_interval,"","<acronym title='Mineure harmonique'>Mineure</acronym> (hauteurs nominales)");
						?>
						</table>
					</td>
					
				</tr>
				
				
				</table>
			</td>
				
			
			
		</tr>
		
		<tr>
			<td colspan="2">	Succession hauteur/intervalle
				<table>
				<?php
				$this->output_matrix($this->successions_note_interval,$this->successions_interval_note);
				?>
				</table>
			</td>
		</tr>
		<?php
		if($fast=="false")
		{
		    ?>
		    <tr>
		    	<td colspan="2">
			
		    	    <a href="#" onclick="window.open('analyse.php?option=successions_note_note&amp;id_piece=<?=$_GET['id_piece']?>','1','menubar=no, status=no, scrollbars=yes, menubar=no, width=500, height=500')"> Voir le tableau de succession des notes</a>
			    
		    	</td>
	    	</tr>
		    <?php
	    }
	    ?>
		<!--<tr>
			<td colspan="2">	Succession des hauteurs
				<table>
				<?php
				$this->output_matrix($this->successions_note_av_note_ap,$this->successions_note_ap_note_av);
				?>
				</table>
			</td>
		</tr>-->
				
		
		<?php
		//print_r($this->phrases);
		end_box();		
	}
	
	
	function output_by_measures($hist="true")
	{
		begin_box_js("Résultats Par Mesure","by_measures");
		$ids=array("majeure_monika_interval_by_measures","mineure_monika_interval_by_measures","max","best");
		?>
		<tr>
			<td align="center">
				<input type="hidden" name="hist" id='hist' value="<?=($hist=='true')?1:0?>"/>
				<script type="text/javascript" language="Javascript"><!--
				
                var tableau_measure_strings= 
                    new Array(
                    <?php
                        $n=0;
                        foreach ($this->frequence_notes_absolues_by_measures as $elem )
                        {
                            if($n==0)
                            {
                                echo "'".convert_to_get_string($elem)."'";
                            }
                            else
                            {
                                echo ",'".convert_to_get_string($elem)."'";
                            }
                            $n++; 
                        }
                    ?> 
                    ) ;
				
				--></script>
				
				<a style="text-align: center;" onclick="<?php 	foreach ($ids as $id ) { echo 'change_div(-1,\''.$id.'\','.$_GET['id_piece'].');' ;	}?> ; change_value(-1);">
				<img src="./images/design/previous.png" alt="<?=_("Précédent")?>" title="<?=_("Précédent")?>"/></a>
				Mesure :<input type="button"  name="measure_number" value="1" size='2' id="measure_number"/>
				<a style="text-align: center;" onclick="<?php 	foreach ($ids as $id ) { echo 'change_div(1,\''.$id.'\','.$_GET['id_piece'].');' ;	}?>; change_value(1);">
				<img src="./images/design/next.png" alt="<?=_("Suivant")?>" title="<?=_("Suivant")?>"/></a>
			</td>
		</tr>
		<tr>
		<?php
		if($hist=="true")
		{
		    ?>
		    <td align="center">
		        <img id="preload" width="200" height="200" src="affich_histogram.php?id_piece=<?=$_GET['id_piece']?>&amp;measure=1" alt="<?=_("Histogramme")?>"/>
		    </td>
		    <?php
		}
		else
		{
		    ?>
		    <td align="center">
		        <img id="preload" width="200" height="200" src="fast_histogram.php?<?=convert_to_get_string($this->frequence_notes_absolues_by_measures[1])?>" alt="<?=_("Histogramme")?>"/>
		    </td>
		    <?php
		}
		?>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<th>Majeure par mesures</th>
						<th>Mineure par mesures</th>
						<th>Meilleurs scores</th>
						<th>Synthese</th>
			
					</tr>
					<tr>
						<td>
							<?php
							output_tab_tab($this->majeure_monika_interval_by_measures,"majeure_monika_interval_by_measures");
							?>
						</td>
						<td>
							<?php
							output_tab_tab($this->mineure_monika_interval_by_measures,"mineure_monika_interval_by_measures");
							?>
						</td>
						<td>
							<?php
							output_tab_tab($this->max,"max");
							?>
						</td>
						<td>
							<?php
							output_tab_tab($this->best,"best","","no_key");
							?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
		end_box();
	}

	
	
	function output_successions_note_note()
	{
		?>
		<table border="1">
		<?php
		$this->output_matrix($this->successions_note_av_note_ap,$this->successions_note_ap_note_av);
		?>
		</table>
		<?php
		
	}
	
	
	
	
	function fragment($measure_start,$measure_end,$beat_start,$beat_end)
	{
		global $majeure_interval;
		global $mineure_interval;
		$this->frequence_notes_fragment_detailed=$this->get_fragment($measure_start,$measure_end,$beat_start,$beat_end);
		
		$this->majeure_fragment=identify_gamme($majeure_interval,$this->frequence_notes_fragment_detailed,"interval_monika");
		$this->mineure_fragment=identify_gamme($mineure_interval,$this->frequence_notes_fragment_detailed,"interval_monika");
		
		//$this->majeure_fragment=identify_gamme($majeure_interval,$this->frequence_notes_fragment_detailed,"default");
		//$this->mineure_fragment=identify_gamme($mineure_interval,$this->frequence_notes_fragment_detailed,"default");
		
			
		$this->max_fragment=array_merge(concat_key($this->majeure_fragment,"majeur"),concat_key($this->mineure_fragment,"mineur"));
		//$this->max_fragment=array_map("intval",$this->max_fragment);
		$this->max_fragment=array_filter($this->max_fragment,"non_null");
		arsort($this->max_fragment,SORT_NUMERIC);
		
		//$this->max_fragment= array_merge(array_map("concat_majeur_s" , array_keys($this->majeure_fragment,$a ) ), array_map("concat_mineur_s",array_keys($this->mineure_fragment,$a))  ) ;
		//sort($this->max_fragment,SORT_NUMERIC);
		
		$a=max($this->majeure_fragment);
		$b=max( $this->mineure_fragment);
		//echo "$key => majeur : $a mineur : $b <br/>";
		if( ($a - $b) > 0.1)
		{
			//echo "<!-- measure $key a>b -->";
			$this->best_fragment=array_map("concat_majeur" , array_keys($this->majeure_fragment,$a ) );
		}
		else if( ($b - $a) > 0.1 )
		{
			//echo "<!-- measure $key a<b -->";
			$this->best_fragment=array_map("concat_mineur", array_keys($this->mineure_fragment,$b)) ;
		}
		else
		{
			//echo "<!-- measure $key else -->";
			$this->best_fragment= array_merge( 
			array_map("concat_majeur",array_keys($this->majeure_fragment,$a)), 
			array_map("concat_mineur",array_keys($this->mineure_fragment,$b))  
			) ;
			//print_r($this->best_fragment);
			//$this->best_fragment= array_map("concat_majeur" , array_keys($this->majeure_fragment,$a ) ) + array_map("concat_mineur",array_keys($this->mineure_fragment,$b))  ;
		}
		
		//unset($this->max_fragment[0]);
		//unset($this->best_fragment[0]);
		
	}
	
	
	function output_analyse_fragment()
	{
		begin_box_js("analyse fragment","fragment");
		?>
		<tr><td>
			<table>
			
			<tr>
				<td>
					<table>
					<?php
				
					$this->output_tableau($this->majeure_fragment,"%","Majeure (monika)");
					?>
					</table>
				</td>
				<td>
					<table>
					<?php
			
					$this->output_tableau($this->mineure_fragment,"%","<acronym title='Mineure harmonique'>Mineure</acronym> (monika)");
					?>
					</table>
				</td>
				<td>
					<table>
					<?php
			
					$this->output_tableau($this->max_fragment,"%","Bilan");
					?>
					</table>
				</td>
				<td>
					<table>
					<?php
			
					$this->output_tableau($this->best_fragment,"%","Synthese","0","no_key");
					?>
					</table>
				</td>
				<td>
					
				</td>
			</tr>
			
			</table>
		</td></tr>
		<?php
		end_box();
	}
	//function returns an array (for each part) but only a fragment of the music
	//carefull measure not their real values but their index value in the table
	// it means it is real_measure_value - 1
	function get_fragment($measure_start,$measure_end,$beat_start,$beat_end)
	{
		global $alterations;
		//$out=array();
		$fragment=array();
		$duree_totale=0;
		foreach ($this->xml->parts as $part)
		{
			
			//$fragment=array();
			if($measure_start < $measure_end)
			{
				$duree_totale+=$part->measures[$measure_start]->measure_duration-$beat_start;
				$duree_totale+=$beat_end;
				//echo count($part->measures[0]->notes)."<br/>";
				foreach($part->measures[$measure_start]->notes as $note)
				{
				
					$value=min($note->duration,$note->time_location+$note->duration-$beat_start);
					$value=max(0,$value);
					$note_info=$note->note_info;
					//echo $note->time_location.$note_info->step."<br>";
					if(!empty($note_info->step))
					{
						$alter=$note_info->alter;
					
					
						if(empty($alter))
						{
							$alter=0;
						}
					
					
						$fragment[$note_info->step.$alterations[$alter]]+=$value;
					
					}
					else
					{
					
						$fragment["silence"]+=$value;
					}
				}
				//this is the easy part we copy from measure measure_start+1 to measure_end-1
				for($i= ($measure_start+1) ; $i<=($measure_end-1) ;$i ++)
				{
					foreach($part->measures[$i]->notes as $note)
					{
						$note_info=$note->note_info;
						//$value=note_class_to_rythm_value($note);
						$value=$note->duration;
						if(!empty($note_info->step))
						{
							$alter=$note_info->alter;
						
						
						
							if(empty($alter))
							{
								$alter=0;
							}
						
						
							$fragment[$note_info->step.$alterations[$alter]]+=$value;
						
						}
						else
						{
						
							$fragment["silence"]+=$value;
						}
					
					}
					$duree_totale+=$part->measures[$i]->measure_duration;
				
				}
			
			
			
				foreach($part->measures[$measure_end]->notes as $note)
				{
					$value=min($note->duration,$beat_end-$note->time_location);
					$value=max(0,$value);
					$note_info=$note->note_info;
					//echo $note->time_location.$note_info->step."<br>";
					if(!empty($note_info->step))
					{
						$alter=$note_info->alter;
					
					
					
						if(empty($alter))
						{
							$alter=0;
						}
					
					
						$fragment[$note_info->step.$alterations[$alter]]+=$value;
					
					}
					else
					{
					
						$fragment["silence"]+=$value;
					}
				}
			}
			else
			{
				//same measure
				$duree_totale=$beat_end-$beat_start;
				foreach($part->measures[$measure_start]->notes as $note)
				{
				
					$value=min($note->duration,$note->time_location+$note->duration-$beat_start,$beat_end-$note->time_location);
					$value=max(0,$value);
					$note_info=$note->note_info;
					//echo $note->time_location.$note_info->step."<br>";
					if(!empty($note_info->step))
					{
						$alter=$note_info->alter;
					
					
						if(empty($alter))
						{
							$alter=0;
						}
					
					
						$fragment[$note_info->step.$alterations[$alter]]+=$value;
					
					}
					else
					{
					
						$fragment["silence"]+=$value;
					}
				}
				
				
			}
			//array_push($out,$fragment);
		}
		$duree_totale/=$this->xml->nb_parts;
		//echo $duree_totale;
		foreach($fragment as &$e)
		{
			/*foreach($elem as &$e)
			{*/
				$e/= ($duree_totale/100);
			/*}*/
		}
		//return $out;
		return $fragment;
	}
}



function renormalize_without_silence($tableau)
{
    #    on modifie le tableau pour s'occuper des silences
    $total=sum($tableau)/100;
    if($total != 0)
    {
        foreach($tableau as &$elem)
        {
            $elem/=$total;
        }
    }
    return $tableau;
}



# -----------------------------------------------------gamme by interval-----------------------------------------------------




//different from gamme_class, take it by interval
class gamme_by_interval_class
{
	var $nb_notes;
	var $intervals;
	
	function gamme_by_interval_class($intervals)
	{
		$this->intervals=$intervals;
		$this->nb_notes=count($intervals)+1;
	}
	
	
	# ressort un tableau de forme array("C" => nb , "D" => nb2 ....)
	function correct_notes($tableau,$tonique)
	{
#	    $tableau=renormalize_without_silence($tableau);
	    
		$results=array();
		$note=$tonique;
		if(isset($tableau[$tonique]))
		{
			$results[$tonique]=$tableau[$tonique];
		}
		else
		{
			$results[$tonique]=0;
		}
		for ($i=0 ; $i< count($this->intervals);$i++)
		{
			$note=next_note_string_string_special($note,$this->intervals[$i],"+");
			if(isset($tableau[$note]))
			{
				$results[$note]=$tableau[$note];
			}
			else
			{
				$results[$note]=0;
			}
		}
		return $results;
	}
	
#	fonction retourne un tableau avec les notes de la gammes (C , D ,E ,F ,G ,A B)
	function get_notes($tonique)
	{
		$results=array();
		$note=$tonique;
		$results[$tonique]=1;
		
		for ($i=0 ; $i< count($this->intervals);$i++)
		{
			$note=next_note_string_string_special($note,$this->intervals[$i],"+");
			$results[$note]=1;
		}
		return $results;
	}
#	
#	ici il y a un problème avec fill zero, on ne joue pas avec des nombres (notes absolues) mais des notes (do dièse différent de réb)
	function coef_in_gamme($tableau,$i)
	{
		$correct_notes=$this->correct_notes($tableau,$i);
		//avoid division by zero
		if(norm($correct_notes)==0)
		{
			return 0;
		}

		$correct_notes_normed=normalize($correct_notes);
		$theta_zero=acos( 1/ (sqrt($this->nb_notes)) );
		$max_vector=$this->get_notes($i);
		$max_vector=normalize($max_vector);
		$s=scalar_product($correct_notes_normed,$max_vector);
		if( $s == 0 )
		{
			return 0;
		}
		$theta=acos( $s );
		
		return (1- ($theta/$theta_zero))*100;
	}
	
	function monika($tableau,$i)
	{
		$correct_notes=$this->correct_notes($tableau,$i);
		return sum($correct_notes);
	}
};




class gamme_class
{
	var $nb_notes;
	var $notes;
	function gamme_class($notes)
	{
		$this->nb_notes=count($notes);
		$this->notes=$notes;
	}
	//function return the notes in gamme in array
	function correct_notes($tableau,$i)
	{
	    
#        $tableau=renormalize_without_silence($tableau);
        
		$result=array();
		foreach($this->notes as $note)
		{
			if(isset($tableau[($i+$note)%12]))
			{
				$value=$tableau[($i+$note)%12];
			}
			else
			{
				$value=0;
			}
			array_push($result,$value);
		}
		return $result;
	}
	
	
	function coef_in_gamme($tableau,$i)
	{
		$correct_notes=$this->correct_notes($tableau,$i);
		//print_r($correct_notes);
		//avoid division by zero
		if(norm($correct_notes)==0)
		{

			return 0;
		}

		$correct_notes_normed=normalize($correct_notes);
		$theta_zero=acos( 1/ (sqrt($this->nb_notes)) );
		$max_vector=array_fill(0, $this->nb_notes , 1/sqrt($this->nb_notes) );
		$theta=acos( ( scalar_product($correct_notes_normed,$max_vector) )  );
		
#		return (1- ($theta/$theta_zero))*sum($correct_notes);
		return (1- ($theta/$theta_zero))*100;
	}
	
	
	function monika($tableau,$i)
	{
		$correct_notes=$this->correct_notes($tableau,$i);
		return sum($correct_notes);
	}
}





function identify_gamme($gamme,$tableau,$option="default")
{

#    print_r($tableau);
    
	$results=array();
	switch ($option)
	{
		case "default":
			for($i=0;$i<12;$i++)
			{
				$results[$i]=$gamme->coef_in_gamme($tableau,$i);
			}
		break;
		
		case "monika":
			for($i=0;$i<12;$i++)
			{
				$results[$i]=$gamme->monika($tableau,$i);
			}
		break;
		
		case "interval":
			$notes=array("C","C#","D♭","D","D#","E♭","E","F" ,"F#","G♭", "G","G#","A♭","A","A#","B♭","B");
			foreach($notes as $note)
			{
				$results[$note]=$gamme->coef_in_gamme($tableau,$note);
				
			}
		break;
		
		case "interval_monika":
			$notes=array("C","C#","D♭","D","D#","E♭","E","F" ,"F#","G♭", "G","G#","A♭","A","A#","B♭","B");
			foreach($notes as $note)
			{
				$results[$note]=$gamme->monika($tableau,$note);
				
			}
		break;
	}
	return $results;
}

function show_identify_gamme($gamme,$analyse,$nom_gamme,$option="default",$nom_option="Monika pondérée",$js="js")
{
	$tableau=$analyse->frequence_notes_absolues;
	$results=identify_gamme($gamme,$tableau,$option);
	output($results,"$nom_gamme selon la méthode $nom_option",$js,"identify_$nom_gamme"."_".$option);
}




function output_occurrences($tableau,$title,$option="js")
{
	$output=array("Do","Do#","Ré","Mi♭/Ré#","Mi","Fa" ,"Fa#", "Sol","Sol#","La","Si♭/La#","Si");
	global $tableau_correspond_int_note;
	if($option=="js")
	{	
		begin_box_js($title,"occurences_box");
	}
	else
	{
		begin_box($title,"occurences");
	}
	?>
	<tr><td>
		<table> 
			<tr>
				<?php
				foreach($output as $elem)
				{
					?>
					<th><?=$elem ?></th>
					<?php
				}
				?>
			</tr>
			<tr>
				<?php
				for($i=0;$i<12;$i++)
				{
					?>
					<th><?= (empty($tableau[$i]))?0:$tableau[$i] ?></th>
					<?php
				}
				?>
			</tr>
		</table>
	</td></tr>
	<?php
	end_box();
}



// to output array array
function output_tab_tab($tableau,$id,$format=0,$option="default")
{
	$notes=array("C","C#","D♭","D","D#","E♭","E","F" ,"F#","G♭", "G","G#","A♭","A","A#","B♭","B");
	$nb=count($tableau);
	?>
	<br/>
	<!--<a style="text-align: center;" onclick="change_div(-1,'<?=$id?>');"><img src="./images/design/previous.png" alt="<?=_("Précédent")?>" title="<?=_("Précédent")?>"/></a>
	<input type="text" name="<?=$id?>_measure_number" value="1" size='2' id="<?=$id?>_measure_number"/>
	<a  style="text-align: center;" onclick="change_div(1,'<?=$id?>');"><img src="./images/design/next.png" alt="<?=_("Suivant")?>" title="<?=_("Suivant")?>"/></a>-->
	<?php
	if($option=="default")
	{
		foreach($tableau as $measure=> $tab)
		{
			?>
			<div id="<?=$id?>_measure_<?=$measure?>" style="<?=($measure==1)?('display: block'):'display: none;' ;?>">
				<table>
				<?php
				foreach($tab as $note=> $result)
				{
					?>
					<tr>
						<th><?=$note?></th>
						<td><?=(is_float($tab[$note]))?number_format($tab[$note],$format):$tab[$note]?></td>
					</tr>
					<?php
				}
				?>
				</table>
			</div>
			<?php
		}
	}
	else
	{
		foreach($tableau as $measure=> $tab)
		{
			?>
			<div id="<?=$id?>_measure_<?=$measure?>" style="<?=($measure==1)?('display: block'):'display: none;' ;?>">
				<table>
				<?php
				foreach($tab as $note=> $result)
				{
					?>
					<tr>
						<td><?=(is_float($tab[$note]))?number_format($tab[$note],$format):$tab[$note]?></td>
					</tr>
					<?php
				}
				?>
				</table>
			</div>
			<?php
		}
	}
}



function output($tableau,$title,$option="js",$id)
{
	$output=array("Do","Do#","Ré","Mi♭/Ré#","Mi","Fa" ,"Fa#", "Sol","Sol#","La","Si♭/La#","Si");
	global $tableau_correspond_int_note;
	if($option=="js")
	{	
		begin_box_js($title,$id);
	}
	else
	{
		begin_box($title,$id);
	}
	?>
	<tr><td>
		<table> 
			<tr>
				<?php
				foreach($output as $elem)
				{
					?>
					<th><?=$elem ?></th>
					<?php
				}
				?>
			</tr>
			<tr>
				<?php
				for($i=0;$i<12;$i++)
				{
					?>
					<th><?= (empty($tableau[$i]))?0:number_format($tableau[$i],2) ?>%</th>
					<?php
				}
				?>
			</tr>
		</table>
	</td></tr>
	<?php
	end_box();
}




function convert_note_to_absolute_interval($notes)
{
	$string="";
	$data=explode("/",$notes);
	$last_note=note_to_value($data[0]);
	unset($data[0]);
	foreach($data as $note)
	{
		if(!empty($note))
		{
			$value=note_to_value($note);
			$string.=modulo_spe($value-$last_note,12)."/";
			
		}
		$last_note=$value;
		
	}
	return $string;
}








// take string of form C4/+1D4/ and convert to gamme_class
function parse_gamme($notes)
{
	$data=explode("/",$notes);
	$first_note=note_to_value($data[0]);
	unset($data[0]);
	$out=array(0);
	foreach($data as $note)
	{
		if(!empty($note))
		{
			$value=note_to_value($note);
			array_push($out,modulo_spe($value-$first_note,12));
			
		}
	}
	$out=array_unique($out);
	return new gamme_class($out);
}

function choose_fragment_form($nb_measures)
{
	?>
	<form action="?option=fragment&amp;id_piece=<?=$_GET['id_piece']?>" method="post">
	
	<?php
	begin_box_js("Choix d'un fragment","choose_fragment");

	?>
	<tr>
		<td >
		     De la mesure:   
		</td>
		<td>
			<select name="measure_start" id="measure_start" onchange="update_measure_end();">
				<option selected="selected" value="0">1</option>
				<?php
				for($i=1;$i<$nb_measures;$i++)
				{
					?>
					<option value="<?=$i?>"><?=$i+1?></option>
					<?php
				}
				?>
			</select>
		</td>
		<td>
			à la mesure: 
		</td>
		<td>
			<select name="measure_end" id="measure_end" onchange="update_measure_start();">
				<?php
				for($i=0;$i<$nb_measures-1;$i++)
				{
					?>
					<option value="<?=$i?>"><?=$i+1?></option>
					<?php
				}
				?>
					<option selected="selected" value="<?=$nb_measures-1?>"><?=$nb_measures?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td>
			<acronym title='exemple : 1.3;1.5;2 ...'>A partir du temps:</acronym>
		</td>
		<td>
			<input type="text" name="beat_start" />
		</td>
		<td>
			<acronym title='exemple : 1.3;1.5;2 ...'>jusqu'au temps: </acronym>
		</td>
		<td>
			<input type="text" name="beat_end"/>
		</td>
	</tr>
	<tr>
		<td colspan="4" align="right"><input type="button" value="<?=_("Effacer")?>" onclick="effacer()" /><input type="submit" value="<?=_("Analyse")?>!" /></td>
	</tr>
	<?php
	end_box();
	?>
	</form>
	<?php
}

function choose_gamme_form()
{
	?>
	<form action="?option=gamme&amp;id_piece=<?=$_GET['id_piece']?>" method="post">
	
	
	<input type="hidden" id="clef_sign" value="G"/>
	<input type="hidden" id="clef_line" value="2"/>
	<?php
	begin_box_js("Choix d'une gamme","gamme");

	?>
	<tr>
        <td colspan="2">
                <div id="partition_temporaire">
	        <img alt="" id="part_image" src="afficher_partition.php?clef_sign=G&amp;clef_line=2" />
                </div>
        </td>
    </tr>
    <tr>
            <td colspan="2">
            <table class="note_table" border="0" style="border-collapse: collapse">
	    <tr>
		    <td>
			                <script type="text/javascript" language="Javascript" src="js/clavier.js"></script>
                            <iframe src="clavier.php?nb_octave=2&amp;begin=4" 
    scrolling="no" frameborder="0" width="850px" 
    height="235px" style="border:0; overflow:hidden;"></iframe>
                            <?php
#                                clavier_recherche_melodie(2,4);
                            ?>
                            
		    </td>
            </tr>
            </table>
	    </td>
    </tr>
	<tr>
		<td colspan="2" align="right"><input type="hidden" id="melodie" name="melodie" value=""/><input type="button" value="<?=_("Effacer")?>" onclick="effacer()" /><input type="submit" value="<?=_("Analyse")?>!" /></td>
	</tr>
	<?php
	end_box();
	?>
	</form>
	<?php
}

function concat_majeur($a)
{
	return $a."majeur";
}
function concat_mineur($a)
{
	return $a."mineur";
}

function concat_key($array,$string)
{
	$out=array();
	foreach ($array as $key=>$elem)
	{
		$out[$key.$string]=$elem;
	}
	//asort($out,SORT_NUMERIC);
	return $out;
}

function non_null($a)
{
	return($a!=0);
}


// function converts ..., i=> value_i ... to ...&note_i=value&...
function convert_to_get_string($tableau)
{
    $out="";
    $n=0;
    foreach ($tableau as $key => $elem)
    {
        if($n==0)
        {
            $out.="note_".$key."=".$elem;
        }
        else
        {
            $out.="&"."note_".$key."=".$elem;
        }
        $n++;
    }
    return $out;
}

?>
