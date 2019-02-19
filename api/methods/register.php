<?php

    $data = $_POST;
    $required_fields = ['first_name','last_name','email','password','company_name',"date_added","token","level"];
    $fields = [];
    $values = [];

    $data["date_added"] = time();
    $data["token"] = generate_token();
    $data["level"] = 3;

    foreach ($required_fields as $key) {
        if (empty($data[$key])) {
            response(400, $key . " is required");
        } else {
            if ($key === 'password' && strlen($data[$key]) < 8) {
                response(400, "Password must be at least 8 characters long.");
            }
            if ($key !== 'company_name' && $key !== 'password') {
                $fields[] = "`" . $key . "`";
                if (gettype($data[$key]) === 'string') {
                    $values[] = "'" . $data[$key] . "'";
                } else {
                    $values[] = $data[$key];
                }
            }
        }
    }

    $d = [
        "data"=>$data,
        "fields"=>implode(", ", $fields),
        "values"=>implode(", ", $values)
    ];

    connect($d, function ($d, $conn) {
        $q = "SELECT `id` FROM `clients` WHERE `name` = '" . $d['data']['company_name'] . "'";
        $res = $conn->query($q);
        if ($res->num_rows > 0) {
            response(409, "This company name is already in use.");
        }
        $q = "SELECT `id` FROM `users` WHERE `email` = '" . $d['data']['email'] . "'";
        $res = $conn->query($q);
        if ($res->num_rows > 0) {
            response(409, "This email address is already in use.");
        }
        $q = "INSERT INTO `users` ( " . $d['fields'] . " ) VALUES ( " . $d['values'] . " )";

        if ($conn->query($q) === true) {
            $new_id = $conn->insert_id;
            //create client
            $q = "INSERT INTO `clients` (`name`,`owner`) VALUES ('" . $d['data']['company_name'] . "', " . $new_id . ")";
            if ($conn->query($q) === true) {
                $created_client = true;
            } else {
                response(500, "Failed to create client", $conn->error);
            }
            //create password
            $q = "INSERT INTO `passwords` (`user_id`,`password`) VALUES (" . $new_id . ", '" . password_hash($d['data']['password'], PASSWORD_DEFAULT) . "')";
            if ($conn->query($q) === true) {
                $created_password = true;
            } else {
                response(500, "Failed to create client", $conn->error);
            }

            if ($created_client && $created_password) {
                response(201, "Successfully created");
            }
        } else {
            response(500, "Failed to create user", $conn->error);
        }
    });
