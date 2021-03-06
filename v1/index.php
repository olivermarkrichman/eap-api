<?php

    require("core/connect.php");
    require("core/utils.php");

    header("Access-Control-Allow-Origin: *");

	// print_r($_SERVER);
	// die();

    $rest_json = file_get_contents("php://input");
    $_POST = json_decode($rest_json, true);
    $_POST = clean($_POST);
    $headers = getallheaders();
    $GLOBALS['headers'] = $headers;
    $urls = explode("/", $_SERVER['REQUEST_URI']);
    $request = strtolower($_SERVER['REQUEST_METHOD']);
    $query_string = $_SERVER['QUERY_STRING'];
    $endpoint = array_key_exists(1, $urls) ? explode("?", $urls[1])[0] : null;
    $endpoint_id = array_key_exists(2, $urls) ? explode("?", $urls[2])[0] : null;
    $GLOBALS['query_string_array'] = explode("&", $query_string);
    $GLOBALS['query_string'] = array();
    $GLOBALS['endpoint_id'] = $endpoint_id;

    foreach ($GLOBALS['query_string_array'] as $query) {
        if (!empty($query)) {
            array_push($GLOBALS['query_string'], $query);
        }
    }

    $valid_endpoints = [
        'clients',
        'events',
        'incidents',
        'skills',
        'users',
        'venues',
		'eaps'
    ];

    if ($endpoint === "forgotpassword") {
        if (!empty($_POST['email'])) {
            require("core/email.php");
            send_reset_password_email($_POST['email']);
            return;
        } else {
            response(400, "Email address required");
        }
    }

    if ($endpoint === "resetpassword") {
        if (!empty($_POST['password']) && !empty($_POST['reset_code'])) {
            require("methods/password-functions.php");
            reset_password($_POST['password'], $_POST['reset_code']);
            return;
        } else {
            response(400, "Password and reset code is required");
        }
    }

    if ($endpoint === "login") {
        require("methods/login.php");
        return;
    }

    if ($endpoint === "reset-codes") {
        $d = [];
        connect($d, function ($d, $conn) {
            $q = "SELECT reset_code FROM passwords";
            $res = $conn->query($q);
            if ($res->num_rows > 0) {
                $data = [];
                while ($row = $res->fetch_assoc()['reset_code']) {
                    if (!empty($row)) {
                        array_push($data, $row);
                    }
                }
                header("Content-Type: application/json");
                echo json_encode($data);
            } else {
                header("Content-Type: application/json");
                echo json_encode([]);
            }
        });
        return;
    }
    if ($endpoint === "confirm-codes") {
        $d = [];
        connect($d, function ($d, $conn) {
            $q = "SELECT `confirm_code` FROM `users`";
            $res = $conn->query($q);
            if ($res->num_rows > 0) {
				$data = [];
				while ($row = $res->fetch_assoc()) {
					$code = $row['confirm_code'];
					if ($code){
			        	array_push($data, $code);
					}
		        }
                header("Content-Type: application/json");
                echo json_encode($data);
            } else {
                header("Content-Type: application/json");
                echo json_encode([]);
            }
        });
        return;
    }

    if ($endpoint === "register") {
        require("methods/register.php");
        return;
    }

    if ($endpoint === "confirm-register") {
        require("methods/register-confirm.php");
        return;
    }

    authorise($headers);

    if ($endpoint === "me") {
        if ($request === "get") {
            require("methods/me.php");
            getMe();
            return;
        } elseif ($request === "put") {
            if (array_key_exists('current_client', $_POST) && !empty($_POST['current_client'])) {
                require("methods/me.php");
                updateMe($_POST['current_client']);
            } else {
                response(400, "You need to send a current_client");
            }
            return;
        }
    }

    if ($endpoint === "changepassword") {
        if (!empty($endpoint_id)) {
            if ($request === "put") {
                require("methods/password-functions.php");
                change_password($endpoint_id);
                return;
            } else {
                invalid_request();
            }
        } else {
            invalid_request();
        }
    }

    if ($endpoint === "upload") {
        require("methods/upload.php");
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
