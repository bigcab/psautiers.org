#!/usr/bin/python
import datetime
from import_export_includes import *


today = datetime.datetime.today()

format = "%a_%b_%d_%H:%M:%S_%Y"
localtime= today.strftime(format)




def dump_xml(xml,name):
	fp=open(name,"w")
	xml.writexml(fp, "    ", "", "\n", "UTF-8")
	fp.close()


		

#zip=zip_export_class()
#zip.export_melody_in_node(zip.doc,372)
#zip.export_recueil_in_node(zip.doc,55)
#zip.export_text_in_node(zip.doc,258)
#zip.export_part_in_node(zip.doc,282)
#zip.export_piece_in_node(zip.doc,263)
#zip.export_base_in_node(zip.doc,41)
# base psaumes
#zip.export_base_in_node(zip.doc,24)
#zip.save_xml()
#zip.close()



def main():
	#first we update base then recueil
	
	#updated is zero if not updated and 1 for true	
	#export is the location of the export file
	log_msg(localtime+" : Night export started\n")
	res=sql("SELECT id_base,nom_base,export FROM bases WHERE updated='0'")
	for r in res:
		nom_base=r['nom_base']
		print "export base for "+nom_base
		last_export=str(r['export'])
		id_base=str(r['id_base'])
		filename=random_filename()+extension
		zip=zip_export_class(EXPORT_DIR+filename)
		zip.export_base_in_node(zip.doc,id_base)
		zip.save_xml()
		zip.close()
		if(zip.error==0):
			#now we delete the last export because we have just updated
			if(os.path.isfile(EXPORT_DIR+last_export)):
				os.unlink(EXPORT_DIR+last_export)
			sql_one("UPDATE bases SET updated='1',export='"+filename+"' WHERE id_base='"+id_base+"'")
		else:
			log_msg(localtime+" : Erreur lors de l'export de la base :"+nom_base+" id: "+id_base+"\n")
			zip.clean()
	res2=sql("SELECT id_recueil,titre,export FROM recueils WHERE updated='0'")
	
	for r in res2:
		titre=r['titre']
		print "export recueil for "+titre
		last_export=str(r['export'])
		id_recueil=str(r['id_recueil'])
		filename=random_filename()+extension
		zip=zip_export_class(EXPORT_DIR+filename)
		zip.export_recueil_in_node(zip.doc,id_recueil)
		zip.save_xml()
		zip.close()
		if(zip.error==0):
			#now we delete the last export because we have just updated
			if(os.path.isfile(EXPORT_DIR+last_export)):
				os.unlink(EXPORT_DIR+last_export)
			sql("UPDATE recueils SET updated='1',export='"+filename+"' WHERE id_recueil='"+id_recueil+"'")
		else :
			log_msg(localtime+" : Erreur lors de l'export du recueil :"+titre+" id: "+id_recueil+"\n")
			zip.clean()
	#res2=sql("")
	
	log_msg(localtime+" : Night export ended\n")
			
print localtime
		

main()


cursor.close()
conn.close()

