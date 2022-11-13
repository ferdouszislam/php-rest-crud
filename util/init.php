<?php

include_once '../config/Database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1); // set to 1 to show errors

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");

$URL = '';
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
    $URL = "https://";   
else  
    $URL = "http://";   
// Append the host(domain name, ip) to the URL.   
$URL.= $_SERVER['HTTP_HOST'];   
// Append the requested resource location to the URL   
$URL.= $_SERVER['REQUEST_URI']; 
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
        "status" => "method_not_allowed",
        "error" => true,
        "message" => "unsupported request method: " . $request_method
    );
}

function validation_fail_response($message, $statusCode) {
    http_response_code($statusCode);
    return array(
        "status" => "validation_fail",
        "error" => true,
        "message" => $message
    );
}

function file_validation_fail_response($message) {
    http_response_code(422);
    return array(
        "status" => "file_validation_fail",
        "error" => true,
        "message" => $message
    );
}

function data_constraint_violation_response(PDOException $e) {
    http_response_code(422);
    return array(
        "status" => "data_constrain_violation",
        "error" => true,
        "message" => "provided data violates contraints: " . $e->getMessage(),
        "data" => $e
    );
}

function not_found_response($message) {
    http_response_code(404);
    return array(
        "status" => "not_found",
        "error" => true,
        "message" => $message
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