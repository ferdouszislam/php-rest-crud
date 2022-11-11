<?php

include_once '../util/init.php';
include_once '../models/Font.php';

$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$UPLOAD_DIR = '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Assets' . DIRECTORY_SEPARATOR;
$FONT_FILE = 'fontFile';

$URL_PARAMS = array();
parse_str($_SERVER['QUERY_STRING'], $URL_PARAMS);

$response = array();


switch($REQUEST_METHOD) {

    case "POST":
        $response = createFont($FONT_FILE, $UPLOAD_DIR, $db);
        break;

    case "GET":
        $response = readAllFonts($db);
        break;

    case "DELETE":
        $response = isset($URL_PARAMS['id']) ? deleteFont($URL_PARAMS['id'], $db) : array(
            "status" => "error",
            "error" => true,
            "message" => "id not provided in query parameted for delete item"
        );
        break;

    default:
        $response = array(
            "status" => "error",
            "error" => true,
            "message" => "unsupported request method: " . $REQUEST_METHOD
        );

}

echo json_encode($response);

function createFont($FONT_FILE, $UPLOAD_DIR, $db) {
    if ($_FILES[$FONT_FILE]) {
        $file_name =  $_FILES[$FONT_FILE]["name"];
        $file_tmp_name = $_FILES[$FONT_FILE]["tmp_name"];
        $file_size = $_FILES[$FONT_FILE]["size"] / 1000.00 . "KB";
        $error = $_FILES[$FONT_FILE]["error"];
    
        if ($error > 0) {
            return array(
                "status" => "error",
                "error" => true,
                "message" => "Error uploading the file: " . $error
            );
        } else {
            $upload_path = $UPLOAD_DIR . $file_name;
            $upload_path = preg_replace('/\s+/', '-', $upload_path);
            try {
                move_uploaded_file($file_tmp_name, $upload_path);
            } catch (Exception $e) {
                return array(
                    "status" => "error",
                    "error" => true,
                    "message" => "Error uploading the file: " . $e->getMessage()
                );
            }
    
            // TODO: add font to db

            // $host = "localhost";
            // $user = "root";
            // $password = "";
            // $dbname = "testingdb";

            // $con = mysqli_connect($host, $user, $password, $dbname);

            // if (!$con) {
            //     die("Connection failed: " . mysqli_connect_error());
            // }

            // $sql = "insert into users (username, name, photo) values ('cairocoders', 'cairocoders Ednalan', '$upload_path')";
            // mysqli_query($con, $sql);

            return array(
                "status" => "success",
                "error" => false,
                "message" => "file uploaded successfully. [path: '" . $upload_path . "', size: " . $file_size . " KB]"
            );
        }
    } else {
        return array(
            "status" => "error",
            "error" => true,
            "message" => "No file provided"
        );
    }
}

function readAllFonts($db) {
    $fontModel = new Font($db);
    return $fontModel->readAll();
}

function deleteFont($font_id, $db) {

    return array(
        "status" => "success",
        "error" => false,
        "message" => "font with id: " . $font_id . " deleted"
    );
}

?>