<?php

$d = [
    "endpoint"=>$endpoint,
    "fields"=> $GLOBALS['get_fields'][$endpoint]
];
connect($d, function ($d, $conn) {
    $fields = implode(", ", $d['fields']);
    $q = "SELECT " . $fields . " FROM " . $d['endpoint'];
    $res = $conn->query($q);
    if ($res->num_rows > 0) {
        $data = [];
        while ($row = $res->fetch_assoc()) {
            array_push($data, $row);
        }
        header("Content-Type: application/json");
        echo json_encode($data);
    } else {
        response(404, "No " . $d['endpoint'] . " to show.", $conn->error);
    }
});
