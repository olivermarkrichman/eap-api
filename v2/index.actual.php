<?php

header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Headers: *');

// $urls = explode('/', $_SERVER['REQUEST_URI']);
// print_r($urls);
// http_response_code(201);


// Require the base operations of the API, the connection function and the utility functions file
require('core/connect.php');
require('core/utils.php');
require('core/base.php');

// Define accepted requests and endpoints
$accepted_requests = ['get','post','put','delete','options'];
$pre_auth_endpoints = ['login','register'];
$accepted_endpoints = ['users'];

// Check if requested endpoint is one that doesn't require authorisation
if (in_array($endpoint, $pre_auth_endpoints)) {
    require('pre-auth/' . $endpoint . '.php');
    die();
}

// Check request is accepted
if (!in_array($request, $accepted_requests)) {
    response(403, 'This request method is not allowed.');
}

// Check endpoint is accepted
if (!in_array($endpoint, $accepted_endpoints)) {
    response(404, 'This endpoint does not exist.');
}

// Check that endpoint ID is an integer
if (!empty($endpoint_id) && !is_numeric($endpoint_id)) {
    response(400, 'This endpoint ID is invalid.');
}

// If the process has reached this point, routing the request can happen.
if ($GLOBALS['authorised']) {
    require('routes/' . $request . '.php');
} else {
    response(401, 'You are unauthorised to perform this request');
}
