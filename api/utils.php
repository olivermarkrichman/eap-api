<?php

include("httpResponses.php");

function response($code, $message)
{
    header("HTTP/1.0 " . $code . " " . $httpResponses[$code]);
    header("Content-Type: application/json");
    echo json_encode(['response'=>$message]);
    if (substr($code, 0, 1) >= 4 === true) {
        die();
    }
}

function invalidRequest()
{
    response(400, "Invalid Request");
}
