<?php

$d = [
    "endpoint"=>$endpoint,
    "endpoint_id"=>$endpoint_id,
    "fields"=> $GLOBALS['get_fields'][$endpoint]
];

connect($d, function ($d, $conn) {
    $endpoint_message_name = $d['endpoint'] === 'addresses' ? substr($d['endpoint'], 0, -2) : $d['endpoint'] === 'categories' ? "category" :substr($d['endpoint'], 0, -1);
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
        $q = "SELECT " . $fields . " FROM " . $d['endpoint'] . " WHERE id = ". $d['endpoint_id'];
    } else {
        $q = "SELECT " . $fields . " FROM " . $d['endpoint'] . " WHERE id = ". $d['endpoint_id'] . " AND " . implode(" AND ", $where_queries);
    }
    $res = $conn->query($q);
    if ($res->num_rows > 0) {
        header("Content-Type: application/json");
        echo json_encode($res->fetch_assoc());
    } else {
        response(404, "No " . $endpoint_message_name . " to show.", $conn->error);
    }
});
