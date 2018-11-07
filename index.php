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

#htprint($_SERVER);

# db conn
try {

    # only get associative arrays
    $options = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    # connect to db
    $db = new PDO('sqlite:inventory.db',"","",$options);
}
catch(PDOException $e) {
    htprint($e);
}

# parse request_url
$url = parse_url($_SERVER['REQUEST_URI']);
$route = $url['path'];

# GET requests
if ($_SERVER['REQUEST_METHOD'] === "GET") {

    #htprint($_GET);
    # get the availability of all entities at a single timestamp
    if ($route === "/entity_collection_moment") {

        # set get vars
        /*
        if (isset($_GET)) {
            (integer) $guests = isset($_GET['guests'])
                        ? $_GET['guests']
                        : NULL;
            (integer) $storage = isset($_GET['storage'])
                        ? $_GET['storage']
                        : NULL;
            $date = (isset($_GET['date'])
                     && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$_GET['date']))
                        ? $_GET['date']
                        : date("Y-m-d",time());
        }
        */

        # construct 'where' clauses
        /*
        $guest_clause = isset($guests)
                        ? "and r.max_guests >= $guests"
                        : "";
        $storage_clause = isset($storage)
                        ? "and r.max_storage >= $storage"
                        : "";
        */

        # build query
        $duration = 1800; # seconds
        $moment = "2018-01-01 01:05:00";

        ob_start();
        require("queries/entity_collection_moment.sql.php");
        $query = ob_get_clean();
        htprint($query);

        $results = $db->query($query)->fetchAll();

        htprint($results);
    }

}
