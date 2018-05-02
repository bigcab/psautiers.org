

accent_db_table=["mot","syllabe","vers_n","syllabe_n","accent"]
hiatus_db_table=["mot","syllabe","vers_n","syllabe_n","start_hiatus","end_hiatus","start_pos","end_pos","hiatus_string","hiatus_form"]
h_db_table=["mot","syllabe","vers_n","syllabe_n","aspire"]
ent_db_table=["mot","syllabe","vers_n","syllabe_n","grave"]

#we use these for imports and exports
bases_table=[\
"nom_base","description","references","owner","permissions_groupe","permissions_others","body_background_color","banner",\
"liens"
]


recueils_table=\
[\
"titre","titre_uniforme","abreviation","image_titre_recueil_jpg","image_table_matieres",\
"imprimeur","lieu","timbre","solmisation","date_impression","comment_public","comment_reserve","nom_auteur_fiche",\
"date_revision","nom_auteur_revision","commentaire_revision","editeur","adresse_biblio","auteur","compositeur","description_materielle",\
"sources_bibliographiques","litterature_secondaire","bibliotheque","cote"\
]

pieces_table=\
[\
"titre","auteur","fichier_finale","fichier_xml","png_lilypond","mp3","fichier_jpg","image_incipit_jpg","note_finale",\
"ambitus","armure","cles","rubrique","nombre_parties","code_table_ref_3","code_table_ref_4","texte_additionnel","concordances",\
"code_table_ref_5","comment_public","comment_reserve","comment_revision","compositeur","timbre","valide","date_validation","auteur_validation",\
"nom_auteur_fiche","rang","pagination_ancienne","notes_biblio_pages_orig",\
"psaume"
]


parts_table=[\
"indice_partie"\
]

melodies_table=["melodie","rythm","indice_partie","fichier_mp3","commentaire"]
textes_table=["texte","auteur","id_groupe_texte","references_groupe_texte","biblio_texte"]

import_pieces_table=[\
"titre","auteur","fichier_finale","fichier_xml","png_lilypond",\
"mp3","fichier_jpg","image_incipit_jpg","note_finale","ambitus",\
"armure","cles","rubrique","nombre_parties","texte_additionnel","concordances",\
"code_table_ref_3","code_table_ref_4",\
"code_table_ref_5","comment_public","comment_reserve",\
"comment_revision","compositeur","timbre","valide",\
"date_validation","auteur_validation","nom_auteur_fiche",\
"texte_class","psaume"
]

import_table_matieres_table=[\
"rang","pagination_ancienne","notes_biblio_pages_orig"\
]


