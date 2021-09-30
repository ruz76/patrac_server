import sys
day_time = '2021-03-16 08:0'
with open(sys.argv[1]) as f:
    lines = f.readlines()
    minute = 0
    second = 0
    locid = 190
    for line in lines:
        lon = line.rstrip().split(';')[1]
        lat = line.rstrip().split(';')[0]
        SQL="INSERT INTO locations (sessionid, lat, lon, searchid, dt_updated, locid) VALUES ("
        if second < 10:
            dt = day_time + str(minute) + ":0" + str(second)
        else:
            dt = day_time + str(minute) + ":" + str(second)
        SQL += "'hs10423', " + str(lat) + ", " + str(lon) + ", 'babicea1b4e7ea06a340', " + "'" + dt + "', " + str(locid) + ");"
        if second > 58:
            second = 0
            minute += 1
        else:
            second += 1
        locid += 1
        print(SQL)
