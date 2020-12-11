<?php

require('config.php');
require('point_location.php');
require('kraje/extents.php');

function pointInRegion($regionId, $point) {
    $region_coords_string = file_get_contents("kraje/".$regionId.".coords");
    $region_coords = explode(",", $region_coords_string);
    //print_r($region_coords);
    $pointLocation2 = new pointLocation();
    $result = $pointLocation2->pointInPolygon($point, $region_coords);
    return $result;
}

function getRegion($kraje, $lon, $lat) {
    foreach ($kraje as $value){
        if ($lon>=$value[1] && $lon<=$value[2] && $lat>=$value[3] && $lat<=$value[4]) {
            if (pointInRegion($value[0], $lon." ".$lat) == "inside") {
                return $value[0];
            }
        }
    }
    return "OUT";
}

function checkSystemId() {
    if (!ctype_alnum($_REQUEST["id"])) die("E;checkSystemId:1");
    $SQL = "SELECT id, status FROM users WHERE id = '".$_REQUEST["id"]."'";
    $res = pg_query($SQL) or die("E;checkSystemId:2");
    $row = pg_fetch_array($res, null, PGSQL_ASSOC);
    if ($row["id"] != $_REQUEST["id"]) die("E;checkSystemId:3");
}

function getSystemUsers($kraje) {
    checkSystemId();
    $SQL = "SELECT * FROM users WHERE role IN ('registered', 'GINA', 'HS') ORDER BY searchid, name";
    $res = pg_query($SQL) or die("E;getSystemUsers:1");
    while ($row = pg_fetch_array($res, null, PGSQL_ASSOC)) {
        // TODO change with PostGIS
        $region = getRegion($kraje, $row["lon"], $row["lat"]);
        echo $row["sysid"].";".$row["name"].";".$row["status"].";".$row["searchid"].";".$region.";".$row["arrive"]."\n";
    }
}

function areItemsNumbers($array) {
    return ctype_digit(implode('',$array));
}

function changeStatus($items) {
    checkSystemId();
    if (areItemsNumbers($items)) {
        // TODO do it based on ids
        if (isset($_REQUEST["status_from"])) {
            $SQL = "UPDATE users SET status = '".$_REQUEST["status_to"]."' WHERE status = '".$_REQUEST["status_from"]."'";
        } else {
            $SQL = "UPDATE users SET status = '".$_REQUEST["status_to"]."'";
        }
        if ($_REQUEST["status_to"] == "waiting") {
            $SQL .= ", arrive = '', searchid = ''";
        } else {
            $SQL .= ", searchid = '".$_REQUEST["searchid"]."'";
        }
        $SQL .= " WHERE sysid IN (".implode(',',$items).")";
        echo "I;".$SQL;
        pg_query($SQL) or die("E;changeStatus:1");
    } else {
        $SQL = "UPDATE users SET status = '".$_REQUEST["status_to"]."'";
        if ($_REQUEST["status_to"] == "waiting") {
            $SQL .= ", arrive = '', searchid = ''";
        } else {
            $SQL .= ", searchid = '".$_REQUEST["searchid"]."'";
        }
        $SQL .= " WHERE id = '".$_REQUEST["id"]."'";
        echo "I;".$SQL;
        pg_query($SQL) or die("E;changeStatus:2");
    }
}

function moveGINATrackersToOnDuty() {
  $SQL = "UPDATE users SET status = 'onduty' WHERE role = 'GINA' AND status = 'calltocome'";
  pg_query($SQL) or die("E;moveGINA:1");
}

function getIdsHs() {
  $items = str_replace(";", "', '", $_REQUEST["ids"]);
  $items = "'".$items."'";
  $SQL = "SELECT string_agg(sysid::varchar, ';') sa FROM users WHERE id IN (".$items.")";
  $res = pg_query($SQL) or die("E;changeStatusHS:1");
  $row = pg_fetch_array($res, null, PGSQL_ASSOC);
  return explode(";", $row["sa"]);
}

if (!isset($_REQUEST["operation"])) {
  die("E;operation:1");
}

// Connexion, sélection de la base de données
$dbconn = pg_connect("host=localhost dbname=" . $_DBNAME . " user=" . $_USER . " password=" . $_PASSWORD)
or die('{"ERROR":"connect"}');

switch ($_REQUEST["operation"]) {
    case "changestatus":
        changeStatus(explode(";", $_REQUEST["ids"]));
        moveGINATrackersToOnDuty();
        break;
    case "changestatushs":
        $items = getIdsHs();
        changeStatus($items);
        break;
    case "getsystemusers":
        getSystemUsers($kraje);
        break;
}

pg_close($dbconn);

?>

