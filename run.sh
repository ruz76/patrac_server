#!/usr/bin/env bash

# Postgresql
service postgresql start
cp /root/app/db/user.sql /tmp/
chown postgres /tmp/user.sql
chmod 777 /tmp/user.sql
sudo -u postgres psql -f /tmp/user.sql
CON_STRING="dbname='patrac' port='5432' user='patrac' password='XaPforTesting' host='localhost'"
psql "$CON_STRING" -f /root/app/db/create.sql
psql "$CON_STRING" -f /root/app/db/populate.sql

# HS server cache loader
cd /root/app/hs
nohup python3 simopt_get_positions.py &

# GINA server cache loader
cd /root/app/gina
nohup python3 gina_get_positions.py &

# HTTP server
cd /root/app
mkdir /var/www/html/patrac
cp ../*.php /var/www/html/patrac/
chown -R www-data:www-data /var/www/html/patrac/
mkdir /var/local/patrac/
chown www-data:www-data /var/local/patrac/
service apache2 start

