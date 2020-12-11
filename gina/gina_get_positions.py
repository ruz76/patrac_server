import requests, json
import psycopg2
import time, math, io
from datetime import datetime

user = '__USER__'
password = '__PASSWORD__'
db_name = '__DBNAME__'
gina_key = '__GINAKEY__'

def getPositionAndSearchId(conn, sessionid):
    cursor = conn.cursor()
    query = "SELECT searchid, lon, lat FROM users WHERE sessionid = '" + sessionid + "';"
    cursor.execute(query)
    records = cursor.fetchone()
    return records


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
    response = requests.post('https://live.ginasystem.com/EndPoints/MessengerEndPoint.asmx',
                             data='{"Method":"GetClients","Parameters":["' + gina_key + '","0",""]}')
    if response.ok:
        # print(response.content)
        response_json = response.json()
        try:
            conn_string = "dbname='" + db_name + "' port='5432' user='" + user + "' password='" + password + "' host='localhost'"
            locid = '0'
            conn = psycopg2.connect(conn_string)
            # print(response_json)
            cursor = conn.cursor()
            for device in response_json["Value"]:
                # print(device["Nickname"], device["IMEI"])
                if device["IMEI"] != None and device["IMEI"] != '' and device["Location"] != None and len(device["Location"]) == 2 and device["LocationTimeStamp"] != None:
                    with open(device["IMEI"] + ".log", "a+") as log:
                        now = datetime.now()
                        dt_string = now.strftime("%d/%m/%Y %H:%M:%S")
                        log.write(str(dt_string) + " " + str(device["Location"][0]) + " " + str(device["Location"][1]) + " " + str(device["LocationTimeStamp"]) + "\n")

                    # print (str(device["IMEI"]))
                    # print (device["Location"] [0], device["Location"][1])
                    sp = getPositionAndSearchId(conn, str(device["IMEI"]))
                    # print(sp)
                    if sp is not None and len(sp) > 0 and logIt(sp[1], sp[2], device["Location"][0], device["Location"][1]):
                        update_string = "UPDATE users SET lon = " + str(device["Location"][0]) + ", lat = " + str(device["Location"][1]) + ", dt_updated = NOW() WHERE sessionid = '" + str(
                            device["IMEI"]) + "'"
                        print(update_string)
                        cursor.execute(update_string)

                        if sp[0] is not None and sp[0] != '':
                            locid = getLocId(conn, device["IMEI"], sp[0])
                            insert_string = "INSERT INTO locations (sessionid, searchid, locid, lon, lat, ts) VALUES ('" + str(
                                device["IMEI"]) + "', '" + str(sp[0]) + "', " + str(locid) + ", " + str(
                                device["Location"][0]) + ", " + str(device["Location"][1]) + ", to_timestamp(round(" + str(
                                device["LocationTimeStamp"]) + " / 1000000000)))"
                            print(insert_string)
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
    time.sleep(10)
