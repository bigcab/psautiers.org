#!/bin/sh
echo date>> /home/cab/cron.txt
cd /home/cab/project_pp4


/usr/bin/svn up /home/cab/project_pp4 


cp *.php /var/www/
cp include/*.php /var/www/include

#cp convert_to_mo.py /var/www
#chmod 755 /var/www/convert_to_mo.py
cp include/DejaVuSans.ttf /var/www/include/
cp -drf sound /var/www/
cp -drf images /var/www/
cp -drf css /var/www/

cp locale/traductions/orig.po /var/www/locale/traductions/orig.po
cp -drf jquery /var/www/
cp -drf js /var/www/
cp -drf pics /var/www
cp clean_midi_ps.sh /var/www
cp convert_midi_to_mp3.sh /var/www


rm -drf /var/www/py

cd /var/www
#mkdir encoded
#bencoder -f -o encoded/ ./*.php
#mkdir encoded/include
#bencoder -f -o encoded/ include/*.php

#cp -drf encoded/*.php /var/www
#cp -drf encoded/include/*.php /var/www/include
#rm -drf encoded


chown -R apache:apache /var/www
cd /home/cab/project_pp4/
/usr/bin/svn up /home/cab/project_pp4


cp -drf locale /var/www
