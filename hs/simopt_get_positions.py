import requests, json
import psycopg2
import time, math, io
from datetime import datetime

user = 'patrac'
password = 'XaPforTesting'
db_name = 'patrac'
hs_key = '__HSKEY__'

def getPositionAndSearchId(conn, id):
    cursor = conn.cursor()
    query = "SELECT id, searchid, lon, lat FROM users WHERE id = '" + id + "';"
    cursor.execute(query)
    record = cursor.fetchone()
    return record

def getLocId(conn, id, searchid):
    cursor = conn.cursor()
    query = "SELECT locid FROM locations WHERE sessionid = '" + id + "' AND searchid = '" + searchid + "' ORDER BY locid DESC LIMIT 1"
    cursor.execute(query)
    record = cursor.fetchone()
    if record == None:
        return 0
    else:
        return record[0] + 1

def logIt(lon, lat, newlon, newlat):
    if math.hypot(newlon - lon, newlat - lat) > 0.0005:
        return True
    else:
        return False

def update():
    response = requests.get('https://www.horskasluzba.cz/cz/app-patrac-new-incident-test?accessKey=' + hs_key + '&lat=50&lng=15&title=ABC&text=CDE&searchRadius=500&userPhone=775032091&createIncident=1')
    if response.ok:
        # print(response.content)
        response_json = response.json()
        try:
            conn_string = "dbname='" + db_name + "' port='5432' user='" + user + "' password='" + password + "' host='localhost'"
            conn = psycopg2.connect(conn_string)
            # print(response_json)
            cursor = conn.cursor()
            for device in response_json["users"]:
                # print(device["Nickname"], device["IMEI"])
                if device["id"] != None and device["id"] != '' and device["lat"] != None and device["lng"] != None:
                    with open("all.log", "a+") as log:
                        now = datetime.now()
                        dt_string = now.strftime("%d/%m/%Y %H:%M:%S")
                        log.write(str(dt_string) + " " + str(device["id"]) + " " + str(device["name"]) + " " + str(device["lng"]) + " " + str(device["lat"]) + "\n")
                    sp = getPositionAndSearchId(conn, "hs" + str(device["id"]))
                    if sp is not None and len(sp) > 0 and logIt(sp[2], sp[3], device["lng"], device["lat"]):
                        update_string = "UPDATE users SET lon = " + str(device["lng"]) + ", lat = " + str(device["lat"]) + ", dt_updated = NOW() WHERE id = 'hs" + str(device["id"]) + "'"
                        print(update_string)
                        cursor.execute(update_string)
                        if sp[0] is not None and sp[0] != '':
                            locid = getLocId(conn, sp[0], sp[1])
                            insert_string = "INSERT INTO locations (sessionid, searchid, locid, lon, lat) VALUES ('" \
                                            + str(sp[0]) + "', '" + str(sp[1]) + "', " + str(locid) + ", " \
                                            + str(device["lng"]) + ", " + str(device["lat"]) + ")"
                            cursor.execute(insert_string)

            conn.commit()
            cursor.close()
        except psycopg2.Error as e:
            print(e)
    else:
        print('Something went wrong, server sent code {}'.format(response.status_code))

while True:
    try:
        update()
    except Exception as e:
        print("Exception: " + str(e))
    time.sleep(30)
