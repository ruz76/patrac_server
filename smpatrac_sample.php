<?php
$apikeys = array('apiKey1', 'apiKey2', 'apiKeyN');
if (in_array($_REQUEST["apikey"], $apikeys)) {
    $headers = 'From: ' . $_REQUEST["from"] . "\r\n" .
        'Reply-To: ' . $_REQUEST["from"] . "\r\n";
    mail($_REQUEST["to1"], $_REQUEST["subject"], $_REQUEST["body"], $headers);
    mail($_REQUEST["to2"], $_REQUEST["subject"], $_REQUEST["body"], $headers);
}
?>

