<?php

    $data = $_POST;
    $required_fields = ['first_name','last_name','password','confirm_code'];
    $fields = [];
    $values = [];

    foreach ($required_fields as $key) {
        if (empty($data[$key])) {
            response(400, $key . " is required");
        } else {
            if ($key === 'password' && strlen($data[$key]) < 8) {
                response(400, "Password must be at least 8 characters long.");
            }
            if ($key !== 'password' && $key !== 'confirm_code') {
                if (gettype($data[$key]) === 'string') {
                    array_push($fields, $key .' = ' ."'" . $data[$key] . "'");
                } else {
                    array_push($fields, $key . ' = ' . $data[$key]);
                }
            }
        }
    }

    $d = [
        "data"=>$data,
        "fields"=>implode(", ", $fields)
    ];

    connect($d, function ($d, $conn) {
        $q = "SELECT `id`,'client' FROM `users` WHERE `confirm_code` = '" . $d['data']['confirm_code'] . "'";
        $res = $conn->query($q);
        if (!$res->num_rows) {
            response(409, "This user doesn't exist");
        } else {
            $user_id = $res->fetch_assoc()['id'];
            $client_id = $res->fetch_assoc()['client'];
            $q = "UPDATE `users` SET " . $d['fields'] . ", confirm_code = NULL, confirmed = 1 WHERE id = " . $user_id;
            if ($conn->query($q) === true) {
                //create password
                $q = "INSERT INTO `passwords` (`user_id`,`password`) VALUES (" . $user_id . ", '" . password_hash($d['data']['password'], PASSWORD_DEFAULT) . "')";
                if ($conn->query($q) === true) {
                    $created_password = true;
                } else {
                    response(500, "Failed to create password", $conn->error);
                }

                $q = "SELECT `token` FROM `users` WHERE `id` = " . $user_id;
                $res = $conn->query($q);
                if ($res->num_rows > 0) {
                    if ($created_password) {
                        response(200, "Successfully updated", false, ["token"=>$res->fetch_assoc()['token'],"client_id"=>$client_id]);
                    }
                }
            } else {
                response(500, 'Failed to update user', $conn->error);
            }
        }
    });
