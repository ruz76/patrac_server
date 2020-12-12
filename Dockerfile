# use debian as base image
FROM debian:latest

# get list of installable packets and install wget
RUN apt-get update && \
    apt-get -y install apache2 libapache2-mod-php php-pgsql postgresql sudo vim

RUN apt-get -y install python3-requests python3-psycopg2

ADD . /root/app
#CMD  bash /root/app/run.sh
