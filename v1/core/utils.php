<?php

$GLOBALS['get_fields'] = [
    "clients" => ['id','name','owner','logo_img','colours','date_added'],
    "events" => ['id','name','venue','eap','start_time','end_time','away_team','notes','created_by'],
    "incidents" => ['id','name','required_skills','preferred_skills','created_by'],
    "skills" => ['id','name','description','client','created_by'],
    "users" => ['id','first_name','client','last_name','email','level','permissions','skills','profile_img','date_added','created_by'],
    "venues" => ['id','name','first_line','second_line','city','county','postcode','contact_email','contact_number','created_by'],
	"eaps" => ['id','name','events','users','skills']
];

function response($code, $message, $db_error = false, $data = false)
{
    http_response_code($code);
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
        $token = !empty($token[1]) ? '"'.$token[1].'"' : '';
        if (!empty($token)) {
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

function get_token()
{
    $headers = $GLOBALS['headers'];
    if (!empty($headers['Authorization'])) {
        $auth = $headers['Authorization'];
        $token = explode(" ", $auth);
        $token = $token[1];
        return $token;
    } else {
        response(401, 'No token');
    }
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
    require("password.php");
    $host = "peap-database.chzh3jub5r2w.us-east-1.rds.amazonaws.com";
    $user = "peap_user";
    $db_name = "peap_database";
    $conn = new mysqli($host, $user, $password, $db_name);
    $input = $conn->real_escape_string($input);
    return $input;
}
