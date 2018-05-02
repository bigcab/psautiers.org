<?php
require_once("include/auth.php");
require_once("include/global.php"); // for ALLOW_DOWNLOAD_XML
require_once("include/xml.php");
require_once("include/note.php");
require_once("include/mysql.php");
require_once("include/page.php");
require_once("include/upload.php");
require_once("include/check.php");
require_once("include/analyse.php");

$title=_("Synthese graphique des analyses sur une pièce");
ob_start();


is_authorized();


function modulo_pos ($a, $n)
{
	$b=($a % $n);
	return ($b<0)?($b+$n):($b);
}


//echo modulo_pos(-35, 10);
if (isset($_GET["id_recueil"]))
{
	(isset($_GET["indice"]))?($indice=$_GET["indice"]):($indice=0);
	
	$sql="SELECT DISTINCT tm.id_piece,p.titre FROM table_matieres  tm INNER JOIN pieces p ON tm.id_piece=p.id_piece WHERE id_recueil='{$_GET["id_recueil"]}' ORDER BY tm.rang";
	$req=requete($sql);
	$n=num_rows($req);
	$count=0;
	begin_box("Histogrammes","hist");
	?>
	<tr><td>
		<table>
		<tr>
		<td colspan="4" align="center">
			<a href="?id_recueil=<?=$_GET['id_recueil']?>&amp;indice=<?=($indice-1)?>"><img alt='' src="images/design/previous.png" />
			</a>
			<a href="?id_recueil=<?=$_GET['id_recueil']?>&amp;indice=<?=($indice+1)?>"><img alt='' src="images/design/next.png" />
			</a>
		</td>
		</tr>
		<tr>
		<?
	
		while ($response=fetch_array($req))
		{
			$array[$count]=array($response['id_piece'],$response["titre"]);
			
		
			$count=($count+1)%$n;
		}
		for ($i=0 ;$i<3 ; $i++)
		{
			?>
				<td>
					<table>
						<tr>
							<th><?=$array[modulo_pos($indice*3 + $i,$n)][1]?></th>
						</tr>
						<tr>
							<td><img alt='' width="250" height="300" src="affich_histogram.php?id_piece=<?=$array[modulo_pos($indice*3 + $i,$n )][0]?>"></img></td>
						</tr>
					</table>
				</td>
			<?
		}
		?>
	
		</tr>
		</table>
	</td>
	</tr>
	<?php
	end_box();
}
else
{
    begin_box("Erreur");
	msg("Veuillez spécifier un recueil");
    end_box();
}


dump_page();



?>
