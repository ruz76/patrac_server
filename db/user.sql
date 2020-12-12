-- sudo -u postgres psql -f /root/app/db/user.sql
create database patrac;
create user patrac with encrypted password 'XaPforTesting';
grant all privileges on database patrac to patrac;
