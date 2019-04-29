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
        "user",
        "owner"
    ];
    $toExpand = [];
    foreach ($GLOBALS['query_string'] as $query) {
        $key = explode("=", $query)[0];
        $val = explode("=", $query)[1];
        if (in_array($key, $accepted_queries)) {
            if (!empty($key) && !empty($val)) {
                array_push($where_queries, $key . ' = ' . $val);
            }
        }
        if ($key === 'expand') {
            array_push($toExpand, $val);
        }
    }

    if (empty($where_queries)) {
        $q = "SELECT " . $fields . " FROM " . $d['endpoint'];
    } else {
        $q = "SELECT " . $fields . " FROM " . $d['endpoint'] . " WHERE " . implode(" AND ", $where_queries);
    }

	if ($d['endpoint'] === 'events'){
		$q .= " ORDER BY start_time DESC";
	}

    $res = $conn->query($q);
    if ($res->num_rows > 0) {
        $data = [];
        while ($row = $res->fetch_assoc()) {
            array_push($data, $row);
        }
        foreach ($data as $index => $client) {
            if (in_array('owner', $toExpand)) {
                $q = "SELECT " . implode(', ', $GLOBALS['get_fields']['users']) . " FROM users WHERE id = " . $client['owner'];
                $res = $conn->query($q);
                if ($res->num_rows > 0) {
                    $client['owner'] = $res->fetch_assoc();
                    $data[$index] = $client;
                }
            }
            if (in_array('created_by', $GLOBALS['get_fields'][$d['endpoint']])) {
                if (empty($client['created_by'])) {
                    unset($client['created_by']);
                } else {
                    $q = "SELECT id,first_name,last_name,profile_img FROM users WHERE id = " . $client['created_by'];
                    $res = $conn->query($q);
                    if ($res->num_rows > 0) {
                        $client['created_by'] = $res->fetch_assoc();
                        $data[$index] = $client;
                    }
                }
            }
        }
        foreach ($data as $index => $event) {
            if (in_array('venue', $toExpand)) {
                $q = "SELECT `id`,`name` FROM venues WHERE id = " . $event['venue'];
                $res = $conn->query($q);
                if ($res->num_rows > 0) {
                    $event['venue'] = $res->fetch_assoc();
                    $data[$index] = $event;
                }
            }
        }
        header("Content-Type: application/json");
        echo json_encode($data);
    } else {
        response(404, "No " . $d['endpoint'] . " to show.", $conn->error);
    }
});
