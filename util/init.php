<?php

include_once '../config/Database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1); // set to 1 to show errors

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");

$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$URL_PARAMS = array();
parse_str($_SERVER['QUERY_STRING'], $URL_PARAMS);
$REQUEST_BODY_JSON = json_decode(file_get_contents('php://input'));

$database = new Database();
$db = $database->connect();

function no_id_in_query_param_bad_request() {
    http_response_code(400);
    return array(
        "status" => "error",
        "error" => true,
        "message" => "id not provided in query parameter"
    );
}

function request_method_not_allowed($request_method) {
    http_response_code(405);
    return array(
        "status" => "error",
        "error" => true,
        "message" => "unsupported request method: " . $request_method
    );
}

function server_side_exception_response(Exception $e) {
    http_response_code(500);
    return array(
        "status" => "error",
        "error" => true,
        "message" => "failed to read fonts: " . $e->getMessage(),
        "data" => $e
    );
}

?>