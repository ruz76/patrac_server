<?php

function uploadFile($id) {

    if (!isset($_FILES["fileToUpload"])) return "";

    $target_dir = "/var/local/patrac/".$id."/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;

    // Check if file already exists
    if (file_exists($target_file)) {
        $uploadOk = 0;
        return '';
    }

    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 10000000) {
        $uploadOk = 0;
        return '';
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        // if everything is ok, try to upload file
        return '';
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            return basename( $_FILES["fileToUpload"]["name"]);
        } else {
            return '';
        }
    }
}

function archiveResult() {
    $filename = uploadFile("results");
    if ($filename == '') {
        echo "E;uploadfile:1";
    } else {
        echo "OK;" . $filename ;
    }
}

if (!isset($_REQUEST["operation"])) {
    die("E;operation:1");
}

switch ($_REQUEST["operation"]) {
    case "archive":
        archiveResult();
        break;
}

?>
