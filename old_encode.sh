#!/bin/sh
echo date>> /home/cab/cron.txt
cd /home/cab/project_pp4


/usr/bin/svn up /home/cab/project_pp4 

rm -drf encoded
mkdir encoded
bencoder -f -o encoded/ ./*.php
mkdir encoded/include
 bencoder -f -o encoded/ include/*.php

#cp convert_to_mo.py /home/ovh/www
#chmod 755 /home/ovh/www/convert_to_mo.py
cp images/design/* /home/ovh/www/images/design/
cp css/style.css /home/ovh/www/css/
cp css/* /home/ovh/www/css/
cp locale/traductions/orig.pot /home/ovh/www/locale/traductions/orig.pot
cp -r jquery/* /home/ovh/www/jquery
cp js/*.js /home/ovh/www/js

cp clean_midi_ps.sh /home/ovh/www
cp convert_midi_to_mp3.sh /home/ovh/www

cp -drf encoded/*.php /home/ovh/www
cp -drf encoded/include/*.php /home/ovh/www/include
rm -drf encoded
rm -drf /home/ovh/www/py
chown -R ovh:ovh /home/ovh/www
/usr/bin/svn up /home/cab/project_pp4


cp -drf locale /home/ovh/www
