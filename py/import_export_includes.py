# -*- coding: utf-8 -*-
import MySQLdb
import random
import time
import zipfile
import os
import glob
from infos_db import *
import xml.dom.minidom
import sys
import datetime
from ppiv_global_constant import *
from version import *


update_version()
default_banner="./images/design/ban5.jpg"
#init db
conn = MySQLdb.connect (host = "name of the host",\
                       user = "name of the sql user",\
                       passwd = "password of the user",\
                       db = "ppiv")\

conn.set_character_set('utf8')


cursor = conn.cursor (MySQLdb.cursors.DictCursor)

cursor.execute('SET NAMES utf8;')
cursor.execute('SET CHARACTER SET utf8;')
cursor.execute('SET character_set_connection=utf8;')



today = datetime.datetime.today()

format = "%a_%b_%d_%H:%M:%S_%Y"
localtime= today.strftime(format)





        
#OUT_DIR="/var/www/"
class zip_import_class:
    def __init__ (self,outfilename):
        self.filename=outfilename
        self.zip=zipfile.ZipFile(self.filename,"r")
        
        
        self.doc=xml.dom.minidom.parseString(self.zip.read("export.xml"))
        #listing of all files
        self.list=self.zip.namelist()
        #for file in self.zip.namelist():
        #    print file
        for child in self.doc.childNodes :
            if child.nodeName=="base":
                self.import_base(child)
    def close(self):
        self.zip.close()    

    def import_file(self,file,dir=""):
        extension=get_extension(file)
        out=random_filename()+"."+extension
#        print out
        data=self.zip.read(file)
        fichier=open(OUT_DIR+dir+"/"+out,"w")
        fichier.write(data)
        fichier.close()
        return out
        
                
    
    #attention copier coller ne marche pas encore
    def import_base(self,node):
            #does the base already exist
            i=0
            table={}
            #first sql to check if the base already exists
            sql="SELECT id_base FROM bases WHERE "
            #second to insert base
            sql2="INSERT INTO bases ("
            end_sql2="VALUES ("
            for attr in bases_table:
                table[attr]=MySQLdb.escape_string(node.getAttribute(attr).encode('utf-8'))
                if(i==0):
                    sql+="`"+attr+"`='"+table[attr]+"'"
                    sql2+="`"+attr+"`"
                    end_sql2+="'"+table[attr]+"'"                    
                else:
                
                    sql+=" AND `"+attr+"`='"+table[attr]+"'"
                    sql2+=",`"+attr+"`"
                    end_sql2+=",'"+table[attr]+"'"                    
                i+=1            
            sql2+=")"
            end_sql2+=")"
            response=sql_one(sql)               
            if(cursor.rowcount!=0):
                print "La base existe déjà"
                id_base=str(response["id_base"])
            else:            
                print "La base n'existe pas elle sera créée."
                sql_one(sql2+" "+end_sql2)                    
                id_base=str(cursor.lastrowid) 
            
            #now taking care of banner
            banner=""
            if(not empty(table["banner"])):
                if (table['banner'] != default_banner):
                    banner="banners/"+self.import_file(table["banner"],"banners")
                else:
                    banner=default_banner  
                sql_one("UPDATE bases SET banner='"+banner+"' WHERE id_base='"+id_base+"'")
            print "Importation des recueils de la base" 
            for child in node.childNodes:            
                if(child.nodeName=="recueil"):
                    self.import_recueil(child,id_base)
    
    
    
            
    def import_recueil(self,node,id_base):
            #does the recueil already exist
            table={}
            i=0
            #first sql to check if the recueil already exists
            sql="SELECT id_recueil FROM recueils WHERE "
            #second to insert recueil
            sql2="INSERT INTO recueils (`id_base`"
            end_sql2="VALUES ('"+str(id_base)+"'"
            for attr in recueils_table  :      
                table[attr]=MySQLdb.escape_string(node.getAttribute(attr).encode('utf-8'))
                if(i==0):
                    sql+="`"+attr+"`='"+table[attr]+"'"
                        
                    
                else:
                    sql+=" AND `"+attr+"`='"+table[attr]+"'"
                    
                
                sql2+=",`"+attr+"`"
                end_sql2+=",'"+table[attr]+"'"
                i+=1
            
            sql2+=")"
            end_sql2+=")"
            #checking if the recueil already exists
            response=sql_one(sql)
            image_titre_recueil=""
            image_table_matieres=""
            
            if(cursor.rowcount!=0):
            
                #the recueil already exist we don't do anything, just add piece
                print "Le recueil existe déjà dans la base"
                id_recueil=str(response["id_recueil"])
            
            else:
            
                #inserting recueil
                print "Le recueil"+" "+table["titre"]+" "+"n'existe pas, il va etre importe"
                sql_one(sql2+" "+end_sql2)
                
                id_recueil=str(cursor.lastrowid)
                
                #We update the files*/
                # image_titre_recueil_jpg       image_table_matiere
                if(not empty(table["image_titre_recueil_jpg"])):
                    image_titre_recueil="images_titres/"+self.import_file(table["image_titre_recueil_jpg"],"images_titres")
                if( not empty(table["image_table_matieres"])):                
                    image_table_matieres="images_table_matieres/"+self.import_file(table["image_table_matieres"],"images_table_matieres")
                
                sql_one("UPDATE recueils\
                SET image_titre_recueil_jpg='"+image_titre_recueil+"',\
                image_table_matieres='"+image_table_matieres+"'\
                WHERE id_recueil='"+id_recueil+"'")
            
            
            
            for child in node.childNodes:
                if(child.nodeName=="piece"):
                    self.import_piece(child,str(id_recueil))
    def import_words_db(self,node,name,table_info,id_piece):
        table={}
        start_sql="INSERT INTO "+name +" (`id_piece`"
        end_sql=" VALUES  ('"+str(id_piece)+"'"
#        taking info from the xml file
        for info in table_info:
            table[info]=MySQLdb.escape_string(node.getAttribute(info).encode('utf-8'))
#            req=req+', `'+ info + '`=' + "'"+table[info] + "'"
            start_sql=start_sql+ ',`'+info+'`'
            end_sql= end_sql+ ", '"+table[info]+"'"
        sql_one(start_sql+ " ) " + end_sql + " ) ")

        
    def import_piece(self,node,id_recueil):
            #does the piece already exist
            table={}
            i=0
            #first sql to check if the piece already exists
            sql="SELECT id_piece FROM pieces WHERE "
            #second to insert piece
            sql2="INSERT INTO pieces ("
            end_sql2="VALUES ("
            for attr in import_pieces_table:                                
                    table[attr]=MySQLdb.escape_string(node.getAttribute(attr).encode('utf-8'))
                    if(i==0):
                        sql+="`"+attr+"`='"+table[attr]+"'"
                        sql2+="`"+attr+"`"
                        end_sql2+="'"+table[attr]+"'"
                    else:
                        sql+=" AND `"+attr+"`='"+table[attr]+"'"
                        sql2+=",`"+attr+"`"
                        end_sql2+=",'"+table[attr]+"'"
                    i+=1
            sql2+=")"
            end_sql2+=")"
            #check if the piece already exists
            response=sql_one(sql)
            
            if(cursor.rowcount !=0):
                print "La piece "+table["titre"]+" existe deja"
                id_piece=str(response["id_piece"])
                
                #check if piece is already in recueil
                req2=sql_one("SELECT id_piece FROM table_matieres WHERE id_piece='"+id_piece+"' AND id_recueil='"+id_recueil+"'")
                if(cursor.rowcount==0):
                    #if not in recueil
                    #inserting to table matierses that's all we have to do
                    sql3="INSERT INTO table_matieres (`id_recueil`,`id_piece`"
                    end_sql3="VALUES ('"+id_recueil+"','"+id_piece+"'"
                    for attr in import_table_matieres_table :
                        table[attr]=MySQLdb.escape_string(node.getAttribute(attr))
                        sql3+=",`"+attr+"`"
                        end_sql3+=",'"+table[attr]+"'"
                    sql3+=")"
                    end_sql3+=")"
                    sql_one(sql3+" "+end_sql3)
                    
                        
                    
                #now we know piece is in the recueil
                
                #print("not finished this condition yet")
                #then return stop
                return
            
            #insert the piece
            print "Import de la piece"+" "+table["titre"]
            sql_one(sql2+" "+end_sql2)
            id_piece=str(cursor.lastrowid)
            
            mp3=""
            fichier_finale=""
            fichier_xml=""
            fichier_jpg=""
            image_incipit_jpg=""
            png_lilypond=""
            #now import files
            if(not empty(table["fichier_finale"])):
                print "=>Import fichier finale" 
                fichier_finale="fichiers_finale/"+self.import_file(table["fichier_finale"],"fichiers_finale")
            if(not empty(table["fichier_xml"])):
                print "=>Import fichier xml"
                fichier_xml="xml/"+self.import_file(table["fichier_xml"],"xml")
            if(not empty(table["mp3"])):
                print "=>Import fichier mp3"
                mp3=self.import_file("mp3/"+table["mp3"],"mp3")
            if(not empty(table["fichier_jpg"])):
                print "=>Import fichier jpg"
                fichier_jpg="images_jpg/"+self.import_file(table["fichier_jpg"],"images_jpg")
            if(not empty(table["image_incipit_jpg"])):
                print "=>Import fichier image incipit jpg"
                image_incipit_jpg="incipits_jpg/"+self.import_file(table["image_incipit_jpg"],"incipits_jpg")
            #print(table["png_lilypond"])
            #png now
            if(not empty(table["png_lilypond"])):
                print "=>Import fichiers png lilypond"
                #print(WORK_DIR+"png/"+table["png_lilypond"]+"*.png")
                #pages=glob(WORK_DIR+table["png_lilypond"]+"*.png")
                pages=find_in_list(self.list,table["png_lilypond"])
                #print_r(pages)
                n=len(pages)
                png_lilypond="png/"+random_filename()
                if(n==1):
                    data=self.zip.read(pages[0])
                    #now write
                    fichier=open(OUT_DIR+png_lilypond+".png","w")
                    fichier.write(data)
                    fichier.close()
                    #print(pages[0])
                        #copy(pages[0],png_lilypond+".png")
                else:
                    
                    for i in range(1,n):
                        data=self.zip.read(pages[i-1])
                        fichier=open(OUT_DIR+png_lilypond+"-page"+str(i)+".png","w")
                        fichier.write(data)
                        fichier.close()
                        #print(pages[i-1])
                        #copy(pages[i-1],png_lilypond+"-page"+i+".png")
                            
            #now taking care of words db      
            if (table["psaume"] == "1" ):
                
                print "=>La piece est un psaume : Importation des mots"
                for child in node.childNodes:
#                    print child.nodeName
                    if(child.nodeName =="accent_db"):
#                        print "accent db recognized"
                        self.import_words_db(child,"accent_db",accent_db_table, id_piece)
                    elif child.nodeName == "accent_mus_db":
#                        print 'accent mus db'
                        self.import_words_db(child,"accent_mus_db",accent_db_table, id_piece)
                    elif (child.nodeName=="h_db"):
                        self.import_words_db(child,"h_db",h_db_table, id_piece)
                    elif (child.nodeName=="h_mus_db"):
                        self.import_words_db(child,"h_mus_db",h_db_table, id_piece)
                    elif (child.nodeName=="hiatus_db"):
                        self.import_words_db(child,"hiatus_db",hiatus_db_table, id_piece)
                    elif (child.nodeName=="ent_db"):
                        self.import_words_db(child,"ent_db",ent_db_table, id_piece)
                    elif (child.nodeName=="ent_mus_db"):
                        self.import_words_db(child,"ent_mus_db",ent_db_table, id_piece)
                          
            sql_one(\
                "UPDATE pieces \
                SET png_lilypond='"+png_lilypond+"',\
                mp3='"+mp3+"',\
                fichier_xml='"+fichier_xml+"',\
                fichier_finale='"+fichier_finale+"',\
                fichier_jpg='"+fichier_jpg+"',\
                image_incipit_jpg='"+image_incipit_jpg+"'\
                WHERE id_piece='"+id_piece+"'")
                
            
            #ici à modifier et si la pièce est déja dans le recueil
            
            #inserting to table matierses
            sql3="INSERT INTO table_matieres (`id_recueil`,`id_piece`"
            end_sql3="VALUES ('"+id_recueil+"','"+id_piece+"'"
            for attr in import_table_matieres_table :
                    table[attr]=MySQLdb.escape_string(node.getAttribute(attr).encode('utf-8'))
                    sql3+=",`"+attr+"`"
                    end_sql3+=",'"+table[attr]+"'"
            sql3+=")"
            end_sql3+=")"
            print "=>ajout dans la table des matieres"
            sql_one (sql3+" "+end_sql3)
            
            #next add parts and text and melodies
            for child in node.childNodes:
                    if(child.nodeName=="part"):                 
                        self.import_part(child,id_piece)
            #sql_one("")
        
                                    
        #def import_piece(self,node,id_recueil):
        #    print node.getAttribute("titre")
        
        
    def import_part(self,part_node,id_piece):
        indice_partie=str(part_node.getAttribute("indice_partie"))
        id_melodie=""
        id_text=""
        print "Import partie indice_partie"
        #next add parts and text and melodies
        for child in part_node.childNodes:
            if child.nodeName=="text":
                id_text=check_add_text(child)
            elif child.nodeName=="melodie":
                id_melodie=check_add_melodie(child)
        sql_one("INSERT INTO parts (`id_piece`,`id_melodie`,`id_text`,`indice_partie`) VALUES ('"+id_piece+"','"+id_melodie+"','"+id_text+"','"+indice_partie+"')")
    

#does exactly the same as in include/import.php
#except it is faster
#and will be on the bot
def check_add_melodie(melodie_node):
    end_sql=" WHERE "
    sql2="INSERT INTO melodies ("
    values=" VALUES ("
    i=0
    for info in melodies_table:
        if i !=    0 :
            end_sql += " AND "
            sql2 += ","
            values+=","
        attr=str(melodie_node.getAttribute(info))
        end_sql+="`"+info+"`='"+attr+"' "
        sql2+="`"+info+"`"
        values+="'"+attr+"'"
        i+=1
    sql2+=")"
    values+=")"
    res=sql_one("SELECT id_melodie FROM melodies "+end_sql)
    if cursor.rowcount!=0:
        return str(res['id_melodie'])
    else:
        sql_one(sql2+values)
        return  str(cursor.lastrowid)  
        


#does exactly the same as in include/import.php
#except it is faster
#and will be on the bot
def check_add_text(text_node):
    end_sql=" WHERE "
    sql2="INSERT INTO textes ("
    values=" VALUES ("
    i=0
    for info in textes_table:
        if i !=    0 :
            end_sql += " AND "
            sql2 += ","
            values+=","
        attr=MySQLdb.escape_string(text_node.getAttribute(info).encode('utf-8'))
        end_sql+="`"+info+"`='"+attr+"' "
        sql2+="`"+info+"`"
        values+="'"+attr+"'"
        i+=1
    sql2+=")"
    values+=")"
    res=sql_one("SELECT id_text FROM textes "+end_sql)
    if cursor.rowcount!=0:
        return str(res['id_text'])
    else:
        sql_one(sql2+values)
        return  str(cursor.lastrowid)  
        



def empty (string):
    return (len(string)==0)

class zip_export_class:
    def __init__(self,outfilename):
        self.error=0 #init the error count
        self.doc=xml.dom.minidom.Document()
        #self.filename=TEMP_DIR+"/" + str(time.time()).replace(".","") + ".zip" ;
        self.filename=outfilename
        self.zip=zipfile.ZipFile(self.filename,"w")
        self.save_version()
        folders=['fichiers_finale','images_jpg','images_table_matieres','images_titres','incipits_jpg','mp3','png','xml','pdf','banners']
        for folder in folders :
            self.add_empty_dir(folder)
        
    
    def clean(self) : #if anything goes wrong we have to delete
        os.unlink(self.filename)
        
    def close(self):
        self.zip.close()
    
    def save_version(self):
        if(os.path.isfile("version.txt")):
            self.zip.write("version.txt","version.txt")
            return 1
        else:
            print "error version.txt does not exist"
    
    
    def save_xml(self):
        #dump_xml(self.doc,"export.xml")    xml.dom.ext.PrettyPrint().

        #print self.doc.toxml()
        self.zip.writestr("export.xml",self.doc.toprettyxml())
        
    def add_empty_dir(self,folder):
        zfi=zipfile.ZipInfo(folder+"/")
        self.zip.writestr(zfi,'')
    
    
    def save_file(self,file):
        if len(file)!=0:
            arcname=file
            file= WORK_DIR+file
            if os.path.isfile(file):
                self.zip.write(file,arcname)
                return 1 
            else :
                print file+" does not exist"
            
                self.error+=1
                return 0
            
    def create_element_in_node(self,node,name,table,table_info):
        elem=self.doc.createElement(name)
        node.appendChild(elem)
        for info in table_info:
            elem.setAttribute(info,str(table[info]))
        return elem
        
    def export_text_in_node(self,node,id_text):
        if len(str(id_text))==0:
            return 
        r=sql_one("SELECT * FROM textes WHERE id_text='"+str(id_text)+"'")
        if cursor.rowcount != 0 :
            self.create_element_in_node(node,"text",r,textes_table)
    
    def export_words_db(self,node,name, table, table_info):
        for r in table:
            self.create_element_in_node(node,name,r,table_info)    
        
        
    def export_piece_in_node(self,node,id_piece):
        res=sql_one("SELECT DISTINCT * FROM pieces p INNER JOIN table_matieres tm ON tm.id_piece='"+str(id_piece)+"'\
            WHERE p.id_piece='"+str(id_piece)+"'")
        if cursor.rowcount == 0:
            return
            
        tableau=["fichier_finale","fichier_xml","fichier_jpg","image_incipit_jpg"]
        for elem in tableau:
            error=self.save_file(str(res[elem]))
            check_error(error,"error while importing piece id:"+id_piece +" and file import error "+elem+"\n")
        if len(str(res['mp3']))!=0:    
            error=self.save_file("mp3/"+str(res['mp3']))
            check_error(error,"error while importing piece id:"+id_piece +" and file import error mp3\n")
        
        file=res['png_lilypond']
        pages=glob.glob(WORK_DIR+file+"*.png")
        for page in pages:
            page=page.replace(WORK_DIR,"")
            error=self.save_file(page)
            check_error(error,"error while importing piece id:"+id_piece +" and file import error png lilypond\n")
        piece=self.create_element_in_node(node,"piece",res,pieces_table)
        
        req=sql("SELECT id_part FROM parts WHERE id_piece='"+str(id_piece)+"'");
        for r in req:
            self.export_part_in_node(piece,r['id_part'])
        # now let's check if it is a psaume
        if (res['psaume']==1):
            print 'This piece is a psaume : starting exporting words database'
            res_accent_db=sql("SELECT * FROM accent_db WHERE id_piece='"+str(id_piece)+"'")    
            res_accent_mus_db=sql("SELECT * FROM accent_mus_db WHERE id_piece='"+str(id_piece)+"'")
            res_h_db=sql("SELECT * FROM h_db WHERE id_piece='"+str(id_piece)+"'") 
            res_h_mus_db=sql("SELECT * FROM h_mus_db WHERE id_piece='"+str(id_piece)+"'") 
            res_hiatus_db=sql("SELECT * FROM hiatus_db WHERE id_piece='"+str(id_piece)+"'") 
            res_ent_db=sql("SELECT * FROM ent_db WHERE id_piece='"+str(id_piece)+"'") 
            res_ent_mus_db=sql("SELECT * FROM ent_mus_db WHERE id_piece='"+str(id_piece)+"'") 
            self.export_words_db(piece,"accent_db",res_accent_db,accent_db_table)
            self.export_words_db(piece,"accent_mus_db",res_accent_mus_db,accent_db_table)
            self.export_words_db(piece,"h_mus_db",res_h_mus_db,h_db_table)
            self.export_words_db(piece,"h_db",res_h_db,h_db_table)
            self.export_words_db(piece,"hiatus_db",res_hiatus_db,hiatus_db_table)    
            self.export_words_db(piece,"ent_db",res_ent_db,ent_db_table)
            self.export_words_db(piece,"ent_mus_db",res_ent_mus_db,ent_db_table )
            
    def export_recueil_in_node(self,node,id_recueil):
        res=sql_one("SELECT * FROM recueils WHERE id_recueil='"+str(id_recueil)+"'")
        if cursor.rowcount!=0:
            recueil=self.create_element_in_node(node,"recueil",res,recueils_table)
            error=self.save_file(res["image_titre_recueil_jpg"])
            check_error(error,"error while importing recueil id:"+id_recueil +" and file import error image_titre_recueil\n")
            #print res["image_titre_recueil_jpg"]
            error=self.save_file(res["image_table_matieres"])
            check_error(error,"error while importing recueil id:"+id_recueil +" and file import error image_table_matieres\n")
            req=sql("SELECT id_piece FROM table_matieres WHERE id_recueil='"+str(id_recueil)+"'")
            for r in req:
                self.export_piece_in_node(recueil,str(r['id_piece']))
                
    def export_base_in_node(self,node,id_base):
        res=sql_one("SELECT * FROM bases WHERE id_base='"+str(id_base)+"'")    
        if cursor.rowcount!=0:
            #print res
            base=self.create_element_in_node(node,'base',res,bases_table)
            req=sql("SELECT id_recueil FROM recueils WHERE id_base='"+str(id_base)+"'")
            
            #import banner
            error=self.save_file(str(res["banner"]))
            check_error(error,"error while importing base id:"+id_base +" and file import error banner \n")
            
            
            for r in req:
                self.export_recueil_in_node(base,str(r['id_recueil']))
            
    def export_melody_in_node(self,node,id_melodie):
        if len(str(id_melodie))==0:
            return
        res=sql_one("SELECT * FROM melodies WHERE id_melodie='"+str(id_melodie)+"'")
        if cursor.rowcount != 0 :
            #print res
            self.create_element_in_node(node,"melodie",res,melodies_table)
        
        
        
    def export_part_in_node(self,node,id_part):
        if len(str(id_part))==0:
            return 
        res=sql_one("SELECT * FROM parts WHERE id_part='"+str(id_part)+"'")
        if cursor.rowcount != 0 :
            part=self.create_element_in_node(node,"part",res,parts_table)
            self.export_melody_in_node(part,res['id_melodie'])
            self.export_text_in_node(part,res['id_text'])
        

def log_msg(string):
    log=open(LOG_FILE,"r+")
    log.seek(0,2) #go to the end
    log.write(string)
    log.close()
def check_error (boolean,string):
    if(boolean==0):
        log_msg(string)

#function to go faster
def mysql_log(string):
    log=open(MYSQL_LOG_FILE,"r+")
    log.seek(0,2) #go to the end
    log.write(string)
    log.close()

def sql(req):
    cursor.execute(req)
    mysql_log(localtime+":"+req+"\n")
    return cursor.fetchall()
def sql_one(req):
    #print req
    mysql_log(localtime+":"+req+"\n")
    cursor.execute(req)
    return cursor.fetchone()    


def random_filename():
    return str(time.time()).replace(".","")+str(random.random()).replace(".","")
    
def get_extension(file):
    return file.split(".")[-1]
    
    
def find_in_list(list,sub):
    out=[]
    for elem in list:
        if str(elem).find(sub)!=-1:
            out.append(str(elem))
    return out
