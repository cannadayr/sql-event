## SYNOPSIS
A simple API example to see if a room is available at a certain day

## DEPENDENCIES
    * php
    * sqlite
    * php-sqlite3

## SETUP STEPS
From the project root directory:
```
sqlite3 inventory.db < schema.sql
sqlite3 inventory.db < sample_data.sql
php -S 127.0.0.1:9090
```

## API ENDPOINTS
/rooms endpoint
A sample url to request:
```
http://127.0.0.1:9090/rooms?guests=12&duration=12&storage=1&time=2018-01-01%2000:00:00
```

/times endpoint
A sample url to request:
```
http://127.0.0.1:9090/times?start_time=2018-11-01%2000:00:00&room_num=2&duration=20
```
