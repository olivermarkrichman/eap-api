<?php

require('./fields/get-fields.php');

if (empty($endpoint_id)) {
    // Get all from $endpoint

    $d = [
        'endpoint' => $endpoint,
        'fields' => $get_fields[$endpoint][$GLOBALS['auth_user']['level']]
    ];

    connect($d, function ($d, $conn) {
        $q = 'SELECT ' . implode(', ', $d['fields']) . ' FROM ' . $d['endpoint'];
        $res = $conn->query($q);
        if ($res->num_rows > 0) {
            $data = [];
            while ($row = $res->fetch_assoc()) {
                array_push($data, $row);
            }
            echo json_encode($data);
        }
    });
} else {
    // Get $endpoint_id from $endpoint
}
