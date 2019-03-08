<?php

if ($endpoint === 'users') {
    //delete users.
    //confirm password for any user.
    //if deleteing user is owner of client then delete both
    $d = [
        "user_id"=> $endpoint_id,
        "password"=> $_POST['confirm_password'],
    ];
    connect($d, function ($d, $conn) {
        $user_id = $d['user_id'];
        // $endpoint_message_name = $d['endpoint'] === 'addresses' ? substr($d['endpoint'], 0, -2) : $d['endpoint'] === 'categories' ? "category" :substr($d['endpoint'], 0, -1);
        $q = "SELECT id,client FROM`users` WHERE id = " . $user_id;
        $res = $conn->query($q);
        if ($res->num_rows > 0) {
            //check confirm_password
            $q = "SELECT password FROM passwords WHERE user_id = " . $user_id;
            $res = $conn->query($q);
            if ($res->num_rows > 0) {
                $hash = $res->fetch_assoc()['password'];
                if (password_verify($d['password'], $hash)) {
                    $q = "DELETE FROM `users` WHERE id = ".$user_id;
                    if ($conn->query($q)) {
                        $q = "DELETE FROM `passwords` WHERE user_id = ".$user_id;
                        if ($conn->query($q)) {
                            $q = "DELETE FROM `clients` WHERE owner = ".$user_id;
                            if ($conn->query($q)) {
                                response(200, "Successfully Deleted");
                            } else {
                                response(500, "Unable to delete record.", $conn->error);
                            }
                        } else {
                            response(500, "Unable to delete record.", $conn->error);
                        }
                    } else {
                        response(500, "Unable to delete record.", $conn->error);
                    }
                } else {
                    response(400, "Invalid password");
                }
            } else {
                response(500, "Failed to check password.", $conn->error);
            }
        } else {
            response(404, ucfirst($endpoint_message_name) . " doesn't exist");
        }
    });
} else {
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
}
