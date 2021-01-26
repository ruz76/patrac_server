<?php

require('config.php');

function checkSystemId() {
    if (!ctype_alnum($_REQUEST["id"])) die("E;checkSystemId:1");
    $SQL = "SELECT id, status FROM users WHERE id = '".$_REQUEST["id"]."'";
    $res = pg_query($SQL) or die("E;checkSystemId:2");
    $row = pg_fetch_array($res, null, PGSQL_ASSOC);
    if ($row["id"] != $_REQUEST["id"]) die("E;checkSystemId:3");
}

function createNewSearch() {
    checkSystemId();
    if (!ctype_alnum($_REQUEST["searchid"])) {
        die("E;createNewSearch:1");
    }
    $version = 0;
    if (isset($_REQUEST["version"])) {
      $version = $_REQUEST["version"];
    }
    $SQL = "INSERT INTO searches (searchid, name, description, status, region, version) 
        VALUES ('".$_REQUEST["searchid"]."', '".$_REQUEST["name"]."', '".$_REQUEST["desc"]."', 'confirmed', '".$_REQUEST["region"]."', ".$version.")";
    pg_query($SQL) or die("E;createNewSearch:2");
    mkdir("/var/local/patrac/coordinator".$_REQUEST["searchid"]."/", 0777);
    $SQL = "INSERT INTO users (sessionid, name) 
        VALUES ('coordinator".$_REQUEST["searchid"]."', 'Štáb')";
    pg_query($SQL) or die("E;createNewSearch:3");
}

function closeSearch() {
    checkSystemId();
    if (!ctype_alnum($_REQUEST["searchid"])) {
        die("E;closeSearch:1");
    }
    $accesskey = '';
    if (isset($_REQUEST["accesskey"])) {
        if (!ctype_alnum($_REQUEST["accesskey"])) {
            die("E;closeSearch:2");
        } else {
            $accesskey = $_REQUEST["accesskey"];
        }
    }
    $SQL = "UPDATE searches SET status = 'closed', accesskey = '".$accesskey."' WHERE searchid = '".$_REQUEST["searchid"]."'";
    pg_query($SQL) or die("E;closeSearch:3");
    $SQL = "UPDATE users SET status = 'waiting', arrive = '', searchid = '' WHERE searchid = '".$_REQUEST["searchid"]."'";
    pg_query($SQL) or die(mysqli_error("E;closeSearch:4"));
}

if (!isset($_REQUEST["operation"])) {
  die("E;operation:1");
}

// Connexion, sélection de la base de données
$dbconn = pg_connect("host=localhost dbname=" . $_DBNAME . " user=" . $_USER . " password=" . $_PASSWORD)
or die('{"ERROR":"connect"}');

switch ($_REQUEST["operation"]) {
    case "createnewsearch":
        createNewSearch();
        break;
    case "closesearch":
        closeSearch();
        break;
}

pg_close($dbconn);

?>

