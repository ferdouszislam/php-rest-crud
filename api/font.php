<?php

include_once '../util/init.php';
include_once '../models/Font.php';

$UPLOAD_DIR = 'src' . DIRECTORY_SEPARATOR . 'Assets' . DIRECTORY_SEPARATOR;
$RELATIVE_UPLOAD_DIR = '..' . DIRECTORY_SEPARATOR . $UPLOAD_DIR;
$FONT_FILE = 'fontFile';

switch($REQUEST_METHOD) {

    case "POST":
        $response = createFont($FONT_FILE, $UPLOAD_DIR, $RELATIVE_UPLOAD_DIR, $db);
        break;

    case "GET":
        $response = readAllFonts($db);
        break;

    case "DELETE":
        $response = isset($URL_PARAMS['id']) ? deleteFont($URL_PARAMS['id'], $db) : no_id_in_query_param_bad_request();
        break;

    default:
        $response = request_method_not_allowed($REQUEST_METHOD);

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
        $font->fontName = $file_name;
        $font->filePath = $upload_path;
        $font->fileSize = $file_size;
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
        return server_side_exception_response($e);
    }
}

function readAllFonts($db) {
    try {
        $fontModel = new Font($db);
        return $fontModel->readAll();
    } catch (Exception $e) {
        return server_side_exception_response($e);
    }
}

function deleteFont($font_id, $db) {
    try{
        $font = new Font($db);
        $font = $font->read($font_id);
        if ($font == null) {
            return not_found_response("font with id: " . $font_id . " not found");
        }
        $font_file_relative_path = '..' . DIRECTORY_SEPARATOR . $font->filePath;
        if (file_exists($font_file_relative_path)) unlink($font_file_relative_path);
        $font->delete($font_id);
        return array(
            "status" => "success",
            "error" => false,
            "message" => "font with id: " . $font_id . " deleted"
        );
    } catch (Exception $e) {
        return server_side_exception_response($e);
    }
}

?>