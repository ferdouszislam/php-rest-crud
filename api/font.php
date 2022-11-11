<?php

include_once '../util/init.php';

$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$UPLOAD_DIR = '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Assets' . DIRECTORY_SEPARATOR;
$FONT_FILE = 'fontFile';

$URL_PARAMS = array();
parse_str($_SERVER['QUERY_STRING'], $URL_PARAMS);

$response = array();


switch($REQUEST_METHOD) {

    case "POST":
        $response = createFont($FONT_FILE, $UPLOAD_DIR);
        break;

    case "GET":
        $response = readAllFonts();
        break;

    case "DELETE":
        $response = isset($URL_PARAMS['id']) ? deleteFont($URL_PARAMS['id']) : array(
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

function createFont($FONT_FILE, $UPLOAD_DIR) {
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

function readAllFonts() {

    // TODO: fetch from database
    $response = array();
    array_push($response, array(
        "id" => 1, 
        "fontName" => 'Roboto-Black', 
        "filePath" => 'src/Assets/Roboto-Black.ttf', 
        "fileSize" => "1000KB"
    ));
    array_push($response, array(
        "id" => 2, 
        "fontName" => 'Roboto-Bold', 
        "filePath" => 'src/Assets/Roboto-Bold.ttf', 
        "fileSize" => "1000KB"
    ));

    return $response;
}

function deleteFont($font_id) {

    return array(
        "status" => "success",
        "error" => false,
        "message" => "font with id: " . $font_id . " deleted"
    );
}

?>