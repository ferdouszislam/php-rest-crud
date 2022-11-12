<?php

include_once '../util/init.php';
include_once '../models/Font.php';

$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$UPLOAD_DIR = 'src' . DIRECTORY_SEPARATOR . 'Assets' . DIRECTORY_SEPARATOR;
$RELATIVE_UPLOAD_DIR = '..' . DIRECTORY_SEPARATOR . $UPLOAD_DIR;
$FONT_FILE = 'fontFile';

$URL_PARAMS = array();
parse_str($_SERVER['QUERY_STRING'], $URL_PARAMS);

$response = array();


switch($REQUEST_METHOD) {

    case "POST":
        $response = createFont($FONT_FILE, $UPLOAD_DIR, $RELATIVE_UPLOAD_DIR, $db);
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

function createFont($FONT_FILE, $UPLOAD_DIR, $RELATIVE_UPLOAD_DIR, $db) {
    if (!$_FILES[$FONT_FILE]) {
        return array(
            "status" => "error",
            "error" => true,
            "message" => "No file provided"
        );
    }
    try {
        if (!is_dir($RELATIVE_UPLOAD_DIR)) mkdir($RELATIVE_UPLOAD_DIR, 077, true);
        $file_name = $_FILES[$FONT_FILE]["name"];
        $file_tmp_name = $_FILES[$FONT_FILE]["tmp_name"];
        $file_size = $_FILES[$FONT_FILE]["size"] / 1000.00 . "KB";
        $upload_path = $UPLOAD_DIR . $file_name;
        $upload_path = preg_replace('/\s+/', '-', $upload_path);
        $upload_relative_path = $RELATIVE_UPLOAD_DIR . $file_name;
        $upload_relative_path = preg_replace('/\s+/', '-', $upload_relative_path);
        $error = $_FILES[$FONT_FILE]["error"];
        if ($error > 0) {
            http_response_code($error == 4 ? 422 : 500);
            return array(
                "status" => "error",
                "error" => true,
                "message" => $error == 4 ? "no file attached" : "error uploading the file",
                "data" => array(
                    "errorCode" => $error
                )
            );
        }
        if (file_exists($upload_relative_path)) {
            http_response_code(422);
            return array(
                "status" => "error",
                "error" => false,
                "message" => "font file already exists"
            );
        }
        move_uploaded_file($file_tmp_name, $upload_relative_path);
        $font = new Font($db);
        $font->font_name = $file_name;
        $font->file_path = $upload_path;
        $font->file_size = $file_size;
        $success = $font->create();
        if (!$success) {
            throw new Exception("failed to save font in db");
        }
        return array(
            "status" => "success",
            "error" => false,
            "message" => "font created successfully"
        );
    } catch (Exception $e) {
        // delete uploaded font file if uploaded
        if (file_exists($upload_relative_path)) unlink($upload_relative_path);
        http_response_code(500);
        return array(
            "status" => "error",
            "error" => true,
            "message" => "failed to create font: " . $e->getMessage(),
            "data" => $e
        );
    }
}

function readAllFonts($db) {
    try{
        $fontModel = new Font($db);
       return $fontModel->readAll();
    } catch (Exception $e) {
        http_response_code(500);
        return array(
            "status" => "error",
            "error" => true,
            "message" => "failed to read fonts: " . $e->getMessage(),
            "data" => $e
        );
    }
}

function deleteFont($font_id, $db) {
    try{
        $font = new Font($db);
        $font = $font->read($font_id);
        if ($font == null) {
            http_response_code(404);
            return array(
                "message" => "font with id: " . $font_id . " not found"
            );
        }
        $font_file_relative_path = '..' . DIRECTORY_SEPARATOR . $font->file_path;
        if (file_exists($font_file_relative_path)) unlink($font_file_relative_path);
        $font->delete($font_id);
        return array(
            "status" => "success",
            "error" => false,
            "message" => "font with id: " . $font_id . " deleted"
        );
    } catch (Exception $e) {
        http_response_code(500);
        return array(
            "status" => "error",
            "error" => true,
            "message" => "failed to delete font: " . $e->getMessage(),
            "data" => $e
        );
    }
}

?>