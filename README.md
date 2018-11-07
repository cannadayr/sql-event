## SYNOPSIS
A simple API example to see if a room is available at a certain day

## DEPENDENCIES
    * php
    * sqlite
    * php-sqlite3

## SETUP STEPS
From the project root directory:
```
php -S 127.0.0.1:9090
```

## API ENDPOINTS
Currently only the /rooms endpoint is written.
A sample url to request:
```
http://127.0.0.1:9090/rooms?guests=1&storage=0&date=2018-11-01
```

