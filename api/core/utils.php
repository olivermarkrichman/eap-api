<?php

$GLOBALS['get_fields'] = [
    "clients" => ['id','name','owner','logo_img','colours','date_added'],
    "events" => ['id','name','venue','eap','start_time','end_time','away_team','notes'],
    "incidents" => ['id','name','required_skills','preferred_skills'],
    "skills" => ['id','name','description'],
    "users" => ['id','first_name','client','last_name','email','level','permissions','skills','profile_img','date_added'],
    "venues" => ['id','name','first_line','second_line','city','county','postcode','contact_email','contact_number']
];

function response($code, $message, $db_error = false, $data = false)
{
    require("http-codes.php");
    header("HTTP/1.0 " . $code . " " . $http_codes[$code]);
    header("Content-Type: application/json");
    $response = [
        'code'=>$code,
        'message'=>$message
    ];
    if ($db_error) {
        $response['db_error'] = $db_error;
    }
    if ($data) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    if (substr($code, 0, 1) >= 4 === true) {
        die();
    }
}

function authorise($headers)
{
    $auth = !empty($headers['Authorization']) ? $headers['Authorization'] : '';
    if (strpos($auth, "EAP") !== false) {
        $token = explode(" ", $auth);
        $token = "'".$token[1]."'";
        connect($token, function ($token, $conn) {
            $q = "SELECT `id` FROM `users` WHERE `token` = ".$token;
            $res = $conn->query($q);
            $GLOBALS['token'] = $token;
            if (!$res->fetch_assoc()['id']) {
                response(401, "Invalid Token");
            }
        });
    } else {
        response(401, "Invalid Token");
    }
}

function invalid_request()
{
    response(400, "Invalid request, please try again.");
}

function is_assoc_array($array)
{
    foreach ($array as $item) {
        if (is_array($item)) {
            return true;
        }
        return false;
    }
}

function generate_token()
{
    return md5(uniqid());
}

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

function clean($post)
{
    if ($post) {
        $clean_post = [];
        foreach ($post as $key => $value) {
            $clean_post[sanitize($key)] = sanitize($value);
        }
        return $clean_post;
    }
}

function sanitize($input)
{
    $input = htmlentities(trim(strip_tags(stripcslashes(htmlspecialchars($input)))));
    require('password.php');
    $conn = new mysqli("160.153.129.203", "eap-api", $password, "eap-db");
    $input = $conn->real_escape_string($input);
    return $input;
}
