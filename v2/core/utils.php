<?php

function response($code, $message, $db_error = false, $data = false)
{
    // header("HTTP/1.0 " . $code . " " . $http_codes[$code]);
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

function set_auth_user($token)
{
    connect($token, function ($token, $conn) {
        $q = 'SELECT id, clients, level FROM users WHERE token = ' . $token;
        $res = $conn->query($q);
        if ($res->num_rows > 0) {
            //Set global var for authorised user
            $data = $res->fetch_assoc();
            $GLOBALS['auth_user'] = [
                'id' => $data['id'],
                'clients' => explode(',', $data['clients']),
                'level' => $data['level']
            ];
            $GLOBALS['authorised'] = true;
        }
    });
}

function generate_token()
{
    return md5(uniqid());
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
