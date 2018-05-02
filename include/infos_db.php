<?php

//we use these for imports and exports
$bases_table=
array (
"nom_base","description","references","owner","permissions_groupe","permissions_others"
);


$recueils_table=
array (
"titre","titre_uniforme","abreviation","image_titre_recueil_jpg","image_table_matieres",
"imprimeur","lieu","timbre","solmisation","date_impression","comment_public","comment_reserve","nom_auteur_fiche",
"date_revision","nom_auteur_revision","commentaire_revision","editeur","adresse_biblio","auteur","compositeur","description_materielle",
"sources_bibliographiques","litterature_secondaire","bibliotheque","cote"
);

$pieces_table=
array (
"titre","auteur","fichier_finale","fichier_xml","png_lilypond","mp3","fichier_jpg","image_incipit_jpg","note_finale",
"ambitus","armure","cles","rubrique","nombre_parties","code_table_ref_3","code_table_ref_4","texte_additionnel","concordances",
"code_table_ref_5","comment_public","comment_reserve","comment_revision","compositeur","timbre","valide","date_validation","auteur_validation",
"nom_auteur_fiche","rang","pagination_ancienne","notes_biblio_pages_orig"
);


$parts_table=array(
"indice_partie"
);

$melodies_table=array("melodie","rythm","indice_partie","fichier_mp3","commentaire");
$textes_table=array("texte","auteur","id_groupe_texte","references_groupe_texte","biblio_texte");

$import_pieces_table=array(
"titre","auteur","fichier_finale","fichier_xml","png_lilypond",
"mp3","fichier_jpg","image_incipit_jpg","note_finale","ambitus",
"armure","cles","rubrique","nombre_parties","texte_additionnel","concordances",
"code_table_ref_3","code_table_ref_4",
"code_table_ref_5","comment_public","comment_reserve",
"comment_revision","compositeur","timbre","valide",
"date_validation","auteur_validation","nom_auteur_fiche"
);

$import_table_matieres_table=array(
"rang","pagination_ancienne","notes_biblio_pages_orig"
);


?>
