<?php

function getMe()
{
    $d = [];
    connect($d, function ($d, $conn) {
        $q = "SELECT * FROM users WHERE token = ". $GLOBALS['token'];
        $res = $conn->query($q);
        if ($res->num_rows > 0) {
            header("Content-Type: application/json");
            echo json_encode($res->fetch_assoc());
        } else {
            response(404, "Failed to retrieve Me data", $conn->error);
        }
    });
}

function updateMe($current_client)
{
    connect($current_client, function ($current_client, $conn) {
        $q = "UPDATE users SET current_client = " . $current_client . " WHERE token = ". $GLOBALS['token'];
        $res = $conn->query($q);
        if ($conn->query($q)) {
            getMe();
        } else {
            response(404, "Failed to update me data", $conn->error);
        }
    });
}
