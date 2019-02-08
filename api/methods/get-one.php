<?php

$d = [
    "endpoint"=>$endpoint,
    "endpoint_id"=>$endpoint_id,
    "fields"=> $GLOBALS['get_fields'][$endpoint]
];


// if ($GLOBALS['query_string_array']) {
// 	foreach ($GLOBALS['query_string_array'] as $query) {
// 		$query_split = explode("=", $query);
// 		$query_key = array_key_exists(0, $query_split) ? $query_split[0] : null;
// 		$query_value = array_key_exists(1, $query_split) ? $query_split[1] : null;
// 	}
// }

connect($d, function ($d, $conn) {
    $endpoint_message_name = $d['endpoint'] === 'addresses' ? substr($d['endpoint'], 0, -2) : $d['endpoint'] === 'categories' ? "category" :substr($d['endpoint'], 0, -1);
    $fields = implode(", ", $d['fields']);
    $q = "SELECT " . $fields . " FROM " . $d['endpoint'] . " WHERE id = ". $d['endpoint_id'];
    $res = $conn->query($q);
    if ($res->num_rows > 0) {
        header("Content-Type: application/json");
        echo json_encode($res->fetch_assoc());
    } else {
        response(404, "No " . $endpoint_message_name . " to show.", $conn->error);
    }
});
