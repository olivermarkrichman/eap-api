<?php
    require("core/connect.php");
    require("core/utils.php");

    $headers = getallheaders();
    $rest_json = file_get_contents("php://input");
    $_POST = json_decode($rest_json, true);
    $urls = explode("/", $_SERVER['REQUEST_URI']);
    $request = strtolower($_SERVER['REQUEST_METHOD']);
    $query_string = $_SERVER['QUERY_STRING'];
    $endpoint = array_key_exists(2, $urls) ? explode("?", $urls[2])[0] : null;
    $endpoint_id = array_key_exists(3, $urls) ? explode("?", $urls[3])[0] : null;
    $GLOBALS['query_string_array'] = explode("&", $query_string);
    $GLOBALS['query_string_array'][0] = substr($GLOBALS['query_string_array'][0], 1);

    $valid_endpoints = [
        'clients',
        'events',
        'incidents',
        'skills',
        'users',
        'venues'
    ];

    if ($endpoint === "login") {
        require("methods/login.php");
        return;
    }

    authorise($headers);

    if ($endpoint === "me") {
        require("methods/me.php");
        return;
    }

    if (!$endpoint) {
        invalid_request();
    }

    if (!in_array($endpoint, $valid_endpoints)) {
        invalid_request();
    }

    if (!$endpoint_id) {
        switch ($request) {
            case 'get':
                //Get all from specified endpoint.
                require("methods/get-all.php");
                break;

            case 'post':
                //Create new item(s) for specified endpoint.
                require("methods/post.php");
                break;

            default:
                invalid_request();
                break;
        }
    } elseif (is_numeric($endpoint_id)) {
        switch ($request) {
            case 'get':
                //Get item by ID from specified endpoint.
                require("methods/get-one.php");
                break;

            case 'put':
                //Edit item by ID from specified endpoint.
                require("methods/edit.php");
                break;

            case 'delete':
                //Delete item by ID from specified endpoint.
                require("methods/delete.php");
                break;

            default:
                invalid_request();
                break;
        }
    } else {
        invalid_request();
    }
