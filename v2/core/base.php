<?php

// Display Errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

//Getting and Cleaning POST data
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$_POST = clean($_POST);

//Getting Request method and URLS
$request = strtolower($_SERVER['REQUEST_METHOD']);
$urls = explode('/', $_SERVER['REQUEST_URI']);
$endpoint = array_key_exists(1, $urls) ? explode('?', $urls[1])[0] : null;
$endpoint_id = array_key_exists(2, $urls) ? explode('?', $urls[2])[0] : null;

//Getting authorisation header
$GLOBALS['authorised'] = false;
$headers = getallheaders();
$auth_header = !empty($headers['Authorization']) ? $headers['Authorization'] : '';
if (strpos($auth_header, 'EAP') !== false) {
    $token = explode(' ', $auth_header);
    $token = '"'.$token[1].'"';
    set_auth_user($token);
}

//Set Content-Type to always return JSON data
header("Content-Type: application/json");
