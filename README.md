# Patrac server

This server was used quite often when PoC of Android app was used.
Now it is used for some purposes:

* Store each 10 seconds locations from HS server and GINA server
* Get locations from the cache build from HS and GINA servers
* Store results of searches

HS server now supports location history, so we do not need this cache for HS server.
For GINA server we do not have access to the history, so this way has to be used.
Results of searches should be stored somewhere on PČR server, but we do not have any yet. 

## How to run
* You have to replace __VARIABLE__ with appropripate values in python files.
* The Python scripts have to be run on background for example with nohup utility.
* The PHP scripts may be hosted on Apache2 server with connection to PostgreSQL database.
* The PostgreSQL at least version 10 has to be used. The database configuration and population files ate in db directory.
* You have to allow files upload for PHP and HTTP server into /var/local/patrac/ directory.

## Docker
* Build docker: 
docker build -t ruz76-patrac-server .

* Run docker in iteractive mode
docker run -it ruz76-patrac-server /bin/bash
