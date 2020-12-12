#!/usr/bin/env bash
service postgresql start
cp /root/app/db/user.sql /tmp/
chown postgres /tmp/user.sql
chmod 777 /tmp/user.sql
sudo -u postgres psql -f /tmp/user.sql
CON_STRING="dbname='patrac' port='5432' user='patrac' password='XaPforTesting' host='localhost'"
psql "$CON_STRING" -f /root/app/db/create.sql
psql "$CON_STRING" -f /root/app/db/populate.sql
