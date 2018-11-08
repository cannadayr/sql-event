<?php

# global debug
$GLOBALS['debug'] = true;
#$GLOBALS['debug'] = false;

function htprint($obj, $title = '#####', $verb = false) {
    if ($GLOBALS['debug']) {
        echo '<pre><br>';
        echo "##### $title ##### $title ##### $title #####<br>";
        if ($verb == true) {
            var_dump($obj);
        }
        if ($verb == false) {
            print_r($obj);
        }
        echo "<br>##### $title ##### $title ##### $title #####";
        echo '<br></pre>';
    }
}

#htprint($_SERVER,"_SERVER");

# db conn
try {

    # only get associative arrays
    $options = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
    ];

    # connect to db
    $db = new PDO('sqlite:inventory.db',"","",$options);
}
catch(PDOException $e) {
    htprint($e,"EXCEPTION");
}

# parse request_url
$url = parse_url($_SERVER['REQUEST_URI']);
$route = $url['path'];

# GET requests
if ($_SERVER['REQUEST_METHOD'] === "GET") {

    htprint($_GET,"_GET");
    # get the availability of all entities at a single timestamp
    if ($route === "/rooms") {

        # set get vars
        if (isset($_GET)) {

            $guests = (isset($_GET['guests']) && is_int((integer) $_GET['guests']))
                        ? (int) $_GET['guests']
                        : 1;
            htprint($guests,"guests");

            $storage = (isset($_GET['storage']) && is_int((integer) $_GET['storage']))
                        ? $_GET['storage']
                        : NULL;
            htprint($storage,"storage");

            $duration = (isset($_GET['duration']) && is_int((integer) $_GET['duration']))
                        ? ($_GET['duration'] * 60 * 60) # convert to seconds
                        : (12 * 60 * 60); # 12 hours
            htprint($duration,"duration");

            $time = (
                        isset($_GET['time'])
                        && strtotime($_GET['time'])
                    )
                    ? date_create_from_format("Y-m-d H:i:s",$_GET['time'])->format("Y-m-d H:i:s")
                    : date("Y-m-d H:i:s",time());
            htprint($time,"time");
        }

        # construct 'where' clauses
        $guest_clause = isset($guests)
                        ? "and max_guests >= $guests"
                        : "";
        $storage_clause = isset($storage)
                        ? "and max_storage >= $storage"
                        : "";

        ob_start();
        require("queries/entity_collection_moment.sql.php");
        $query = ob_get_clean();
        htprint($query,"query");

        $results = $db->query($query);

        if (!$results) {
            htprint($db->errorInfo(),"sql err");
        }
        else {
            $resultArr = $results->fetchAll();
            htprint($resultArr,"resultArr");

            htprint(json_encode($resultArr,JSON_PRETTY_PRINT),"json_results");

            # if we're not in debug mode return json normally
            if (!$GLOBALS['debug']) {
                print("<pre>".json_encode($resultArr,JSON_PRETTY_PRINT)."</pre>");
            }
        }

    }

}
