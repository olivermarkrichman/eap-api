<?php

$d = [
    "endpoint"=>$endpoint,
    "endpoint_id"=> $endpoint_id
];
connect($d, function ($d, $conn) {
    $endpoint_message_name = $d['endpoint'] === 'addresses' ? substr($d['endpoint'], 0, -2) : $d['endpoint'] === 'categories' ? "category" :substr($d['endpoint'], 0, -1);
    $q = "SELECT id FROM " . $d['endpoint'] . " WHERE id = " . $d['endpoint_id'];
    $res = $conn->query($q);
    if ($res->num_rows > 0) {
        $q = "DELETE FROM ".$d['endpoint']." WHERE id = ".$d['endpoint_id'];
        if ($conn->query($q)) {
            response(200, "Deleted ID: ".$d['endpoint_id']." from ".$d['endpoint']);
        } else {
            response(500, "Unable to delete record.", $conn->error);
        }
    } else {
        response(404, ucfirst($endpoint_message_name) . " doesn't exist");
    }
});
