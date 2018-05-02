#!/usr/bin/python
# -*- coding: utf-8 -*-
import xml.dom.minidom
import glob
import zipfile
from infos_db import *
import os
from import_export_includes import *
from optparse import OptionParser


parser = OptionParser()
parser.add_option("-f", "--file",
                  action="store", type="string", dest="filename",metavar="FILE")
parser.add_option("-d", "--debug",
                  action="store_true", dest="debug", default=False,
                  help="files will be imported in . instead of working dir")

(options, args) = parser.parse_args()

def usage():
	print "usage : ./import.py -f FILE"

if(options.debug):
	OUT_DIR="./"

if(options.filename==None):
	usage()	
	sys.exit()

filename=options.filename
	
if(not os.path.isfile(filename)):
	print "File invalid"
	sys.exit()

zip=zip_import_class(filename)
#zip.import_file("mp3/0257617001260823431.mp3","tmp")

zip.close()
cursor.close()
conn.close()
