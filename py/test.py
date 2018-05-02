#!/usr/bin/python
# -*- coding: utf-8 -*-
import sys
from optparse import OptionParser


parser = OptionParser()
parser.add_option("-f", "--file",
                  action="store", type="string", dest="filename",metavar="FILE")
parser.add_option("-d", "--debug",
                  action="store_true", dest="debug", default=False,
                  help="files will be imported in . instead of working dir")

(options, args) = parser.parse_args()




if (options.filename!=None):
	print "ok"

