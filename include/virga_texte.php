<?php


#Attention : ce code ne marche que pour les psaumes
#les rythmes acceptés sont la noire, blanche et les silences

class Texte {
  var $linkdb;
  var $texte;
  var $date;
  var $fichier; // le texte (à chaque entrée du tableau correspond une ligne)
  var $nomfich; // le fichier xml
  function Texte($fichier, $date, $nomfich){
    $mdb = new DataBase();
    $this->linkdb = $mdb->link;
    $this->date = $date;
    $this->fichier = $fichier;
    $this->nomfich = $nomfich;
    foreach($fichier as $ligne){
      $ligne = preg_replace("/\t+/", "\t", $ligne);
      $tab = explode("\t", $ligne);
      $texte[] = trim($tab[0]);
    }
    $this->texte = $texte;
  }
  
  
  function printTexte(){
    foreach ($this->texte as $ligne){
      print $ligne . "\n";
    }
  
  }
  function collecteH($drop = false){ //Collecte les H dans un texte marqué
    if ($drop){
      $requete = "DROP TABLE IF EXISTS collecte_h";
      $rep = mysql_query($requete, $this->linkdb);
      $requete = "CREATE TABLE collecte_h(" .
                 "id INT PRIMARY KEY AUTO_INCREMENT," .
                 "mot VARCHAR(30)," .
                 "date INT," .
                 "fichier VARCHAR(30)," .
                 "ligne INT," .
                 "rang INT," .
                 "aspire TINYINT, " .
                 "INDEX(mot)" .
                 ")";
      $rep = mysql_query($requete, $this->linkdb);           
      print mysql_error();
    }
    $texte = $this->getTexteSansComment();
    foreach($texte as $index => $ligne){
      $mots = $this->explose($ligne);
      $j = 0;
      foreach ($mots as $mot){
        if (preg_match("/^\**h/", $mot)){
          $aspire = true;
          if (preg_match("/^\*/", $mot)) $aspire = false;
          $mot = $this->epure($mot);
          $requete = "INSERT INTO collecte_h SET " .
                     "mot = \"$mot\"," .
                     "date = $this->date," .
                     "fichier = \"$this->nomfich\"," .
                     "ligne = " . ($index+1) . "," .
                     "rang = " . ($j+1) . "," .
                     "aspire = " . ($aspire ? 1 : 0);
          $rep = mysql_query($requete, $this->linkdb);
          print mysql_error();           
        }
        $j++;
      }
     }
   }
  
  function collecteEnt($drop = false){
    if ($drop){
      $requete = "DROP TABLE IF EXISTS collecte_ent";
      $rep = mysql_query($requete, $this->linkdb);
      $requete = "CREATE TABLE collecte_ent(" .
                 "id INT PRIMARY KEY AUTO_INCREMENT," .
                 "mot VARCHAR(30)," .
                 "date INT," .
                 "fichier VARCHAR(30)," .
                 "ligne INT," .
                 "rang INT," .
                 "feminin TINYINT, " .
                 "INDEX(mot)" .
                 ")";
      $rep = mysql_query($requete, $this->linkdb);           
      print mysql_error();
    }  
    $texte = $this->getTexteSansComment();
    foreach($texte as $index => $ligne){
      $mots = $this->explose($ligne);
      $j = 0;
      foreach ($mots as $mot){
        if (preg_match("/[eè]nt$/", $mot)){
          $feminin = true;
          if (preg_match("/ènt$/", $mot)) $feminin = false;
          $mot = $this->epure($mot);
          $requete = "INSERT INTO collecte_ent SET " .
                     "mot = \"$mot\"," .
                     "date = $this->date," .
                     "fichier = \"$this->nomfich\"," .
                     "ligne = " . ($index+1) . "," .
                     "rang = " . ($j+1) . "," .
                     "feminin = " . ($feminin ? 1 : 0);
          $rep = mysql_query($requete, $this->linkdb);
          print mysql_error();           
        }
        $j++;
      }
    }  
  }
  
  
  function collecteHiat($drop = false){
    if ($drop){
      $requete = "DROP TABLE IF EXISTS collecte_hiat";
      $rep = mysql_query($requete, $this->linkdb);
      $requete = "CREATE TABLE collecte_hiat(" .
                 "id INT PRIMARY KEY AUTO_INCREMENT," .
                 "mot VARCHAR(30)," .
                 "mothiat VARCHAR(31)," .
                 "date INT," .
                 "fichier VARCHAR(30)," .
                 "ligne INT," .
                 "rang INT," .
                 "hiat TINYINT, " .
                 "INDEX(mot)," .
                 "INDEX(mothiat)" .
                 ")";
      $rep = mysql_query($requete, $this->linkdb);           
      print mysql_error();
    }  
    $texte = $this->getTexteSansComment();
    foreach($texte as $index => $ligne){
      $mots = $this->explose($ligne);
      $j = 0;
      foreach ($mots as $mot){
        if (preg_match("/=/", $mot)){
          $motep = $this->epure($mot);
          $requete = "INSERT INTO collecte_hiat SET " .
                     "mot = \"$motep\"," .
                     "mothiat = \"$mot\"," .
                     "date = $this->date," .
                     "fichier = \"$this->nomfich\"," .
                     "ligne = " . ($index+1) . "," .
                     "rang = " . ($j+1) . "," .
                     "hiat = 1";
          $rep = mysql_query($requete, $this->linkdb);
          print mysql_error();           
        }
        $j++;
      }
             
    }  
  }
  
  function collecteNonHiat($drop = false){ //Collecte des homographes sans hiatus des mots avec hiatus
    if ($drop){
      $requete = "DELETE from collecte_hiat WHERE hiat = 0";
      $result = mysql_query($requete);
      print mysql_error();
    }
    $texte = $this->getTexteSansComment();
    foreach($texte as $index => $ligne){
      $mots = $this->explose($ligne);
      $j = 0;
      foreach ($mots as $mot){
        $nb = 0;
        if ((! preg_match("/=/", $mot)) && (preg_match("/[aàâeéèêëiìïîoôòuùûüy&]{2,}/", $mot))){
          $motep = $this->epure($mot);
          $requete = "SELECT mot FROM collecte_hiat WHERE mot = \"$motep\" AND hiat = 1";
          $result = mysql_query($requete, $this->linkdb);
          $nb = mysql_num_rows($result);
          if ($nb){
            //print $mot . "\n";
            $requete = "INSERT INTO collecte_hiat SET " .
                       "mot = \"$motep\"," .
                       "mothiat = \"$mot\"," .
                       "date = $this->date," .
                       "fichier = \"$this->nomfich\"," .
                       "ligne = ". ($index+1) . "," .
                       "rang = " . ($j+1) . "," .
                       "hiat = 0";
            $result = mysql_query($requete, $this->linkdb);
            print mysql_error();          
          }
        }
        $j++;
      }
     }
   }
   
#    le formulaire pour pouvoir marquer les hiatus graphiquement
    function marque_h_form($disque=false)
    {
        begin_box(_("Marquage des hiatus"),"hiatus_form_box");
        $texte = $this->getTexteSansComment();
        
        foreach($texte as $index => $ligne){
          $ligne = " " . $ligne . " ";
          while (preg_match("/[ '°\-]+(h.*) *[\.,:;\!\?\[\]\(\]*[ '°\-]/iU", $ligne, $matches)){
            $moth = $matches[1];
            $motrech = $this->epure($moth);
            $requete = "SELECT AVG(aspire) FROM collecte_h WHERE mot = \"$motrech\"";
            $result = requete($requete);
            $tab = mysql_fetch_row($result); 
            $etoile = false;
            $demande = false;
            if (is_null($tab[0])) {
              $demande = true;  
            }
            elseif ($tab[0] < 0.1){
              $etoile = true;    
            }
            elseif ($tab[0] < 0.9){
              $demande = true;
            }
            if ($demande)
            {
                $rep = "";
              
#                ici il faut affichier des messages             
                ?>
                <tr>
                    <td>H aspiré <?=$moth?></td>
                    <td>Oui <input type="radio" name="<?=$moth?>" value="yes"/> Non <input type="radio" name="<?=$moth?>" value="no"/></td>
                </tr>
                <?php
            }
            
            
          } 
        }
#        $this->recolleTexte($texte, $disque);   
        end_box();
    }
  function marqueH($disque = false){ //marque les H dans un texte non marqué
    $texte = $this->getTexteSansComment();
    print "\nTRAITEMENT DES H INITIAUX\n";
    print "-------------------------\n\n";
    foreach($texte as $index => $ligne){
      $ligne = " " . $ligne . " ";
      while (preg_match("/[ '°\-]+(h.*) *[\.,:;\!\?\[\]\(\]*[ '°\-]/iU", $ligne, $matches)){
        $moth = $matches[1];
        $motrech = $this->epure($moth);
        $requete = "SELECT AVG(aspire) FROM collecte_h WHERE mot = \"$motrech\"";
        $result = mysql_query($requete, $this->linkdb);
        $tab = mysql_fetch_row($result); 
        $etoile = false;
        $demande = false;
        if (is_null($tab[0])) {
          $demande = true;  
        }
        elseif ($tab[0] < 0.1){
          $etoile = true;    
        }
        elseif ($tab[0] < 0.9){
          $demande = true;
        }
        if ($demande){
          $rep = "";
          while ($rep != "o" && $rep != "n"){
            print "--->" . ($index + 1) . " - '$moth' : h aspiré ? [o, n]\n";
            $rep = strtolower(trim(fgets(STDIN)));
          }
          if ($rep == "n"){
            $etoile = true;
            $tab[0] = 0;
          }
          if ($rep == "o") {
            $tab[0] = 1;
          }    
        }
        if ($etoile){
          $ligne = preg_replace("/$moth/iU", "*$moth", $ligne, 1);
        }
        print $index + 1 . " - " . $moth . " : " . ($tab[0] < 0.1 ? "non aspiré" : "aspiré") . "\n";
        $ligne = preg_replace("/$moth/iU", "£$moth", $ligne, 1);
        
      } 
      $ligne = preg_replace("/£/", "", $ligne);
      
      $texte[$index] = trim($ligne);
    }
    $this->recolleTexte($texte, $disque);
  }
  
  function marqueEnt($disque = false){ //marque les ent dans un texte non marqué
    $texte = $this->getTexteSansComment();
    print "\nTRAITEMENT DES ENT FINAUX\n";
    print "-------------------------\n\n";
    foreach($texte as $index => $ligne){
      $ligne = " " . $ligne . " ";
      while (preg_match("/[ '°\-]+([^ '°\-]*ent) *[\.,:;\!\?\[\]\(\]*[ '°\-]/i", $ligne, $matches)){
        $moth = $matches[1];
        $motrech = $this->epure($moth);
        $requete = "SELECT AVG(feminin) FROM collecte_ent WHERE mot = \"$motrech\"";
        $result = mysql_query($requete, $this->linkdb);
        $tab = mysql_fetch_row($result); 
        $grave = false;
        $demande = false;
        $debmot = substr($moth, 0, -3);
        $debmot = preg_replace("/^\*/", "", $debmot);
        if (is_null($tab[0])) {
          $demande = true;  
        }
        elseif ($tab[0] == 0){
          $grave = true;
        }
        elseif ($tab[0] < 1){
          $demande = true;    
        }
        elseif ($tab[0] == 1){
         
        }
        if ($demande){
          $rep = "";
          while ($rep != "o" && $rep != "n"){
            print "--->" . trim($ligne) . "\n";
            print "--->" . ($index + 1) . " - '$moth' : e grave ? [o, n]\n";
            $rep = strtolower(trim(fgets(STDIN)));
          }
          if ($rep == "n"){
            $tab[0] = 1;
          }
          if ($rep == "o") {
            $tab[0] = 0;
            $grave = true;
          }    
        }
        if ($grave){
          $ligne = preg_replace("/" . $debmot . "ent/iU", $debmot . "ènt", $ligne, 1);
          
        }
        print $index + 1 . " - " . $moth . " : " . ($tab[0] == 1 ? "pas d'accent" : "accent grave") . "\n";
        $ligne = preg_replace("/(" . $debmot . "[eè])(nt)/iU", "$1£$2", $ligne, 1);
        
      } 
      $ligne = preg_replace("/£/", "", $ligne);
      $texte[$index] = trim($ligne);
    }
    $this->recolleTexte($texte, $disque);
  }
  
  function marqueHiat($disque = false){ //marque les ent dans un texte non marqué
    $texte = $this->getTexteSansComment();
    print "\nTRAITEMENT DES HIATUS\n";
    print "---------------------\n\n";
    foreach($texte as $index => $ligne){
      $ligneIntacte = $ligne;
      $ligne = " " . $ligne . " ";
      while (preg_match("/[ '°\-]+([^ '°\-]*[aàâeéèêëiìïîoôòuùûüy&]{2,}[^ '°\-\.,:;\!\?\[\]\(\)]*) *[\.,:;\!\?\[\]\(\)]*[ '°\-]/i",
                        $ligne, $matches)){
        $moth = $matches[1];
        if (! preg_match("/=/", $moth)){
          $motrech = $this->epure($moth);
          $requete = "SELECT AVG(hiat) FROM collecte_hiat WHERE mot = \"$motrech\"";
          $result = mysql_query($requete, $this->linkdb);
          $tab = mysql_fetch_row($result); 
          $hiatus = false;
          $demande = false;
          if ($tab[0] == 0){
          }
          elseif ($tab[0] < 1){
            $demande = true;    
          }
          elseif ($tab[0] == 1){
            $hiatus = true;         
          }
          if ($demande){
            $rep = "";
            while ($rep != "o" && $rep != "n"){
              print "--->" . trim($ligneIntacte) . "\n";
              print "--->" . ($index + 1) . " - '$moth' : hiatus ? [o, n]\n";
              $rep = strtolower(trim(fgets(STDIN)));
            }
            if ($rep == "n"){
              $tab[0] = 0;
            }
            if ($rep == "o") {
              $tab[0] = 1;
              $hiatus = true;
            }    
          }
          if ($hiatus){
            $requete = "SELECT mothiat FROM collecte_hiat WHERE mot = \"$motrech\" AND hiat = 1";
            $result = mysql_query($requete, $this->linkdb);
            $tabrep = mysql_fetch_array($result);
            $motrep = $tabrep[0];
            if (preg_match("/^\*/", $motrep) && ! preg_match("/^\*/", $moth)){
              $motrep = substr($motrep, 1);
            }
            $moth = preg_replace("/^\*/", "", $moth);
            print ($index + 1) . " - " . $moth . " -> " . $motrep . "\n";
            $fragmotrep = explode("=", $motrep);
            $tabmoth = array();
            $motacouper = $moth;
            foreach($fragmotrep as $frag){
              $long = strlen($frag);
              $tabmoth[] = substr($motacouper, 0, $long);
              $motacouper = substr($motacouper, $long);
            }
            $motrempl = implode("=", $tabmoth);
            //print $ligne . "---" . $moth . "---" . $motrempl . "\n";
            $ligne = preg_replace("/$moth/", $motrempl, $ligne, 1);
            $moth = $motrempl;
          }
          $cpt = strlen($moth);
          $chaine = "";
          for ($i = 0; $i < $cpt; $i++){
            $chaine .= $moth[$i] . "£";
          }
          $moth = preg_replace("/^\*/", "", $moth);
          $chaine = preg_replace("/^\*/", "", $chaine);
          //print $ligne . "--" . $moth . "---" . $chaine . "\n";
          $ligne = preg_replace("/$moth/", $chaine, $ligne, 1);
        }
        
      }
      $ligne = preg_replace("/£/", "", $ligne);
      $texte[$index] = trim($ligne);
    }
    $this->recolleTexte($texte, $disque);
  }
  
  function explose($ligne){
    $ligne = preg_replace("/[°'\-]/", " ", $ligne);
    $ligne = preg_replace("/[\.,:;\!\?\[\]\(\)]/", "", $ligne);
    $ligne = preg_replace(" {2,}", " ", $ligne);
    $tab = explode(" ", $ligne);
    return $tab;
  }
  
  function epure($mot){
    $mot = strtolower($mot);
    $mot = preg_replace("/^\*/", "", $mot);
    $mot = preg_replace("/=/", "", $mot);
    $mot = preg_replace("/ènt/", "ent", $mot);
    return $mot;
  }
  
  function recolleTexte($texte, $disque = false){
    $fichier = $this->fichier;
    foreach ($fichier as $index => $lignepleine){
      if (isset($texte[$index])){
        $fichier[$index] .= "\t";
        $fichier[$index] = preg_replace("/.*\t/U", $texte[$index] . "\t", $fichier[$index], 1);
        $fichier[$index] = preg_replace("/\t$/", "", $fichier[$index]);
       }
    }
    $this->fichier = $fichier;
    if ($disque) {
      $this->enregistreFichier();  
    }
    
  }
  
  function enregistreFichier(){
    rename ($this->nomfich, $this->nomfich . ".bak");
      $fp = fopen($this->nomfich, "w");
      foreach($this->fichier as $ligne){
        fwrite($fp, $ligne . "\n");
      }  
      fclose($fp);
      $this->Texte($this->fichier, $this->date, $this->nomfich);
  }
  
  
  function getTexteSansComment(){
    $tab = array();
    $i = 0;
    foreach($this->texte as $ligne){
      if (preg_match("/^#|^\/\//", $ligne)){
        //Exclusion des titres et des commentaires
      }
      else {
        $tab[$i] = $ligne;
      }
      $i++;
    }
    return $tab;
  }
}
?>
