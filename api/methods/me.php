<?php
$d = [];
connect($d, function ($d, $conn) {
    $q = "SELECT " . implode(", ", $GLOBALS['get_fields']['users']) . " FROM users WHERE token = ". $GLOBALS['token'];
    $res = $conn->query($q);
    if ($res->num_rows > 0) {
        header("Content-Type: application/json");
        echo json_encode($res->fetch_assoc());
    } else {
        response(404, "Failed to retrieve Me data", $conn->error);
    }
});
