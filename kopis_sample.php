<?php
$ack['apiKey'] = ["email1", "email2", "PHA"];
$ack['apiKey'] = ["email1", "email2", "STC"];
$ack['apiKey'] = ["email1", "email2", "JHC"];
$ack['apiKey'] = ["email1", "email2", "PLK"];
$ack['apiKey'] = ["email1", "email2", "KVK"];
$ack['apiKey'] = ["email1", "email2", "ULK"];
$ack['apiKey'] = ["email1", "email2", "LBK"];
$ack['apiKey'] = ["email1", "email2", "HKK"];
$ack['apiKey'] = ["email1", "email2", "PAK"];
$ack['apiKey'] = ["email1", "email2", "OLK"];
$ack['apiKey'] = ["email1", "email2", "MSK"];
$ack['apiKey'] = ["email1", "email2", "JHM"];
$ack['apiKey'] = ["email1", "email2", "ZLK"];
$ack['apiKey'] = ["email1", "email2", "VYS"];
if (isset($_REQUEST["ack"])) {
    if (array_key_exists($_REQUEST["ack"], $ack)) {
        echo json_encode($ack[$_REQUEST["ack"]]);
    } else {
        echo '["ERROR", "ERROR", "ERROR"]';
    }
} else {
    echo '["ERROR", "ERROR", "ERROR"]';
}
?>
