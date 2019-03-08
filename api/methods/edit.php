<?php

switch ($endpoint) {
    case 'clients':
        $accepted_fields = ['name','owner','logo_img','colours'];
        break;

    case 'events':
        $accepted_fields = ['name','venue','eap','start_time','end_time','away_team','notes'];
        break;

    case 'incidents':
        $accepted_fields = ['name','required_skills','preferred_skills'];
        break;

    case 'skills':
        $accepted_fields = ['name','description'];
        break;

    case 'users':
        $accepted_fields = ['first_name','last_name','email','level','permissions','skills','profile_img'];
        break;

    case 'venues':
        $accepted_fields = ['name','first_line','second_line','city','county','postcode','contact_email','contact_number'];
        break;

    default:
        invalid_request();
        break;
}

$d = [
    "endpoint"=>$endpoint,
    "endpoint_id"=>$endpoint_id,
    "accepted_fields"=>$accepted_fields
];

connect($d, function ($d, $conn) {
    $endpoint_message_name = $d['endpoint'] === 'addresses' ? substr($d['endpoint'], 0, -2) : $d['endpoint'] === 'categories' ? "category" :substr($d['endpoint'], 0, -1);
    $data = $_POST;
    $fields = [];
    foreach ($d['accepted_fields'] as $i => $accepted_field) {
        if (!empty($data[$accepted_field])) {
            if (gettype($data[$accepted_field]) === 'string') {
                $data[$accepted_field] = "'".$data[$accepted_field]."'";
            }
            $fields[] = $accepted_field." = ".$data[$accepted_field];
        }
    }
    $fields = implode(", ", $fields);
    $q = "SELECT `id` FROM " . $d['endpoint'] . " WHERE `id` = ".$d['endpoint_id'];
    $res = $conn->query($q);
    if ($res->num_rows > 0) {
        $q = "UPDATE " . $d['endpoint'] . " SET " . $fields . " WHERE id = ".$d['endpoint_id'];
        if ($conn->query($q)) {
            $q = "SELECT " . implode(", ", $GLOBALS['get_fields'][$d['endpoint']]) . " FROM " . $d['endpoint'] . " WHERE `id` = ".$d['endpoint_id'];
            $res = $conn->query($q);
            if ($res->num_rows > 0) {
                response(200, "Updated Successfully", false, $res->fetch_assoc());
            } else {
                response(500, "Failed to retrieve changed " . $endpoint_message_name, $pdo->error);
            }
        } else {
            response(500, "Failed to update " . $endpoint_message_name, $pdo->error);
        }
    } else {
        response(404, ucfirst($endpoint_message_name) . " not found");
    }
});
