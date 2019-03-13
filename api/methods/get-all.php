<?php

$d = [
    "endpoint"=>$endpoint,
    "fields"=> $GLOBALS['get_fields'][$endpoint]
];
connect($d, function ($d, $conn) {
    $fields = implode(", ", $d['fields']);
    $where_queries = [];
    $accepted_queries = [
        "client",
        "user"
    ];
    foreach ($GLOBALS['query_string'] as $query) {
        $key = explode("=", $query)[0];
        $val = explode("=", $query)[1];
        if (in_array($key, $accepted_queries)) {
            if (!empty($key) && !empty($val)) {
                array_push($where_queries, $key . ' = ' . $val);
            }
        }
    }

    if (empty($where_queries)) {
        $q = "SELECT " . $fields . " FROM " . $d['endpoint'];
    } else {
        $q = "SELECT " . $fields . " FROM " . $d['endpoint'] . " WHERE " . implode(" AND ", $where_queries);
    }

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
