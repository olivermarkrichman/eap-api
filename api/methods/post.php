<?php
    switch ($endpoint) {
        case 'clients':
            $required_fields = ['name','owner'];
            $accepted_fields = ['name','owner','logo','colours'];
            $check_fields = ['name', 'owner'];
            $requirements = ['date_added'];
            is_assoc_array($_POST) ? create_multiple($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint) : create_one($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint);
            break;

        case 'events':
            $required_fields = ['name','venue','start_time'];
            $accepted_fields = ['name','venue','eap','start_time','end_time','away_team','notes'];
            $check_fields = ['name','venue','start_time'];
            $requirements = ['date_added'];
            is_assoc_array($_POST) ? create_multiple($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint) : create_one($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint);
            break;

        case 'incidents':
            $required_fields = ['name'];
            $accepted_fields = ['name','required_skills','preferred_skills'];
            $check_fields = ['name'];
            $requirements = ['date_added'];
            is_assoc_array($_POST) ? create_multiple($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint) : create_one($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint);
            break;

        case 'skills':
            $required_fields = ['name'];
            $accepted_fields = ['name','description'];
            $check_fields = ['name'];
            $requirements = [];
            is_assoc_array($_POST) ? create_multiple($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint) : create_one($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint);
            break;

        case 'users':
            $required_fields = ['first_name','last_name','email','level'];
            $accepted_fields = ['first_name','last_name','email','level','permissions','skills','profile_img'];
            $check_fields = ['email'];
            $requirements = ['date_added','token'];
            is_assoc_array($_POST) ? create_multiple($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint) : create_one($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint);
            break;

        case 'venues':
            $required_fields = ['name','first_line','city','postcode'];
            $accepted_fields = ['name','first_line','second_line','city','county','postcode','contact_email','contact_number'];
            $check_fields = ['name','first_line','city','postcode'];
            $requirements = [];
            is_assoc_array($_POST) ? create_multiple($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint) : create_one($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint);
            break;

        default:
            invalid_request();
            break;
    }

function create_multiple($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint)
{
    $fields = [];
    $GLOBALS['created_objects'] = [];
    foreach ($_POST as $data) {
        //Check the required fields aren't empty.
        foreach ($required_fields as $required_field) {
            if (empty($data[$required_field])) {
                response(409, "'" . $required_field . "' cannot be blank.");
            }
        }
        //Create a string of fields to go into the query.
        foreach ($accepted_fields as $i => $accepted_field) {
            if (!empty($data[$accepted_field])) {
                if (gettype($data[$accepted_field]) === 'string') {
                    $data[$accepted_field] = "'".$data[$accepted_field]."'";
                }
                $fields[] = $accepted_field;
            }
        }
        if ($endpoint === 'users' && !$data['password']) {
            response(409, "'password' cannot be blank.");
        }
        $fields = implode(", ", $fields);

        $d = [
            "data"=>$data,
            "endpoint"=>$endpoint,
            "fields"=>$fields,
            "check_fields"=>$check_fields,
            "requirements"=>$requirements
        ];

        connect($d, function ($d, $conn) {
            $endpoint_message_name = $d['endpoint'] === 'addresses' ? substr($d['endpoint'], 0, -2) : $d['endpoint'] === 'categories' ? "category" :substr($d['endpoint'], 0, -1);
            $data = $d['data'];
            $check_fields = "";
            foreach ($d['check_fields'] as $check_field) {
                $check_fields .= $check_field . " = " . $data[$check_field];
            }
            //Check item doesn't already already exist.
            $q = "SELECT id FROM ".$d['endpoint']." WHERE " . $check_fields;
            $res = $conn->query($q);
            if ($res->num_rows > 0) {
                response(409, "This " . $endpoint_message_name . " already exists.");
            } else {
                foreach ($d['requirements'] as $requirement) {
                    $d['fields'] .= ", ". $requirement;
                    if ($requirement === 'date_added') {
                        $data[$requirement] = time();
                    }
                    if ($requirement === 'token') {
                        $data[$requirement] = "'" . generate_token() . "'";
                    }
                }
                $values = [];
                foreach ($data as $key => $value) {
                    if ($key !== 'password') {
                        $values[] = $value;
                    }
                }
                //Add new item
                $q = "INSERT INTO " . $d['endpoint'] . " ( " . $d['fields'] . " ) VALUES ( " . implode(", ", $values) . " )";
                if ($conn->query($q) === true) {
                    $new_id = $conn->insert_id;
                    $q = "SELECT * FROM ".$d['endpoint']." WHERE id =" . $new_id;
                    $res = $conn->query($q);
                    if ($res->num_rows > 0) {
                        if ($d['endpoint'] === 'users') {
                            echo "password!";
                        } else {
                            $GLOBALS['created_objects'][] = $res->fetch_assoc();
                        }
                        // $GLOBALS['created_objects'][] = $res->fetch_assoc();
                    } else {
                        response(409, "Failed to retrieve " . $d['endpoint'] . ".", $conn->error);
                    }
                } else {
                    response(500, "Failed to create " . $d['endpoint'] . ".", $conn->error);
                }
            }
        });
        $fields = [];
    }
    header("Content-Type: application/json");
    if ($GLOBALS['created_objects']) {
        response(201, "Successfully created " . count($_POST) . " " . $d['endpoint'], false, $GLOBALS['created_objects']);
    } else {
        response(500, "Failed to create " . $d['endpoint']);
    }
    $GLOBALS['created_objects'] = [];
}

function create_one($required_fields, $accepted_fields, $check_fields, $requirements, $endpoint)
{
    $data = $_POST;
    $fields = [];
    //Check the required fields aren't empty.
    foreach ($required_fields as $required_field) {
        if (empty($data[$required_field])) {
            response(409, "'" . $required_field . "' cannot be blank.");
        }
    }
    //Create a string of fields to go into the query.
    foreach ($accepted_fields as $i => $accepted_field) {
        if (!empty($data[$accepted_field])) {
            if (gettype($data[$accepted_field]) === 'string') {
                $data[$accepted_field] = "'".$data[$accepted_field]."'";
            }
            $fields[] = $accepted_field;
        }
    }
    if ($endpoint === 'users' && ((array_key_exists('password', $data) && !$data['password']) || !array_key_exists('password', $data))) {
        response(409, "'password' cannot be blank.");
    }
    $fields = implode(", ", $fields);

    $d = [
            "data"=>$data,
            "endpoint"=>$endpoint,
            "fields"=>$fields,
            "check_fields"=>$check_fields,
            "requirements"=>$requirements
        ];

    connect($d, function ($d, $conn) {
        $endpoint_message_name = $d['endpoint'] === 'addresses' ? substr($d['endpoint'], 0, -2) : $d['endpoint'] === 'categories' ? "category" :substr($d['endpoint'], 0, -1);
        $data = $d['data'];
        $check_fields = [];
        foreach ($d['check_fields'] as $check_field) {
            $check_fields[] = $check_field . " = " . $data[$check_field];
        }
        $check_fields = implode(" AND ", $check_fields);
        //Check item doesn't already already exist.
        $q = "SELECT id FROM ".$d['endpoint']." WHERE " . $check_fields;
        $res = $conn->query($q);
        if ($res->num_rows > 0) {
            response(409, "This " . $endpoint_message_name . " already exists.");
        } else {
            foreach ($d['requirements'] as $requirement) {
                $d['fields'] .= ", ". $requirement;
                if ($requirement === 'date_added') {
                    $data[$requirement] = time();
                }
                if ($requirement === 'token') {
                    $data[$requirement] = "'" . generate_token() . "'";
                }
            }
            $values = [];
            foreach ($data as $key => $value) {
                if ($key !== 'password') {
                    $values[] = $value;
                }
            }
            //Add new item
            $q = "INSERT INTO " . $d['endpoint'] . " ( " . $d['fields'] . " ) VALUES ( " . implode(", ", $values) . " )";
            if ($conn->query($q) === true) {
                $new_id = $conn->insert_id;
                $q = "SELECT * FROM ".$d['endpoint']." WHERE id = " . $new_id;
                $res = $conn->query($q);
                if ($res->num_rows > 0) {
                    $created_user = $res->fetch_assoc();
                    if ($d['endpoint'] === 'users') {
                        $q = "INSERT INTO passwords (user_id, password) VALUES ( ". $new_id . ", '" . password_hash($data['password'], PASSWORD_DEFAULT) . "')";
                        if ($conn->query($q) === true) {
                            response(201, "Successfully created " . $endpoint_message_name, false, $created_user);
                        } else {
                            response(500, "Failed to create password", $conn->error);
                        }
                    } else {
                        response(201, "Successfully created " . $endpoint_message_name, false, $created_user);
                    }
                } else {
                    response(409, "Failed to retrieve " . $endpoint_message_name . ".", $conn->error);
                }
            } else {
                response(500, "Failed to create " . $endpoint_message_name . ".", $conn->error);
            }
        }
    });
}
