#!/usr/bin/python
# -*- coding: utf-8 -*-

#this file provides function to be able to track versioning on exports-imports so that we don't have to check compatibilities
#between versions of export files, their options etc...

VERSION="1.2"
VERSION_DESCRIPTION="This version supports the following options : "
VERSION_OPTIONS=["pattern recognition for import export","xml import export","import/export fichiers_finales, mp3, fichier_jpg,image_incipit_jpg,fichier_lilypond","melody import/export","hiatus table with hiatus_string and hiatus_form","accent_db table", "h_db_table","field texte_class not usefull anymore"]

fichier="version.txt"

def print_version():
    print "Export file for psautiers.org/ppiv version "+VERSION
#    print "\n"
    print VERSION_DESCRIPTION
#    print "\n"
    for option in VERSION_OPTIONS:        
        print "\t - "+option 
        
        
        
#print_version()

def update_version():
    file=open(fichier,"w")
    file.write( "Export file for psautiers.org/ppiv version "+VERSION)
    file.write( "\n")
    file.write( VERSION_DESCRIPTION)
    file.write( "\n")
    for option in VERSION_OPTIONS:        
        file.write( "\t - "+option )
        file.write( "\n")
        
#update_version()

