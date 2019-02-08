<?php
$d = [];
connect($d, function ($d, $conn) {
    $data = $_POST;
    // print_r($_POST);
    if (!$data['email']) {
        response(400, "Email cannot be blank.");
    }
    if (!$data['password']) {
        response(400, "Password cannot be blank.");
    }
    $q = "SELECT * FROM users WHERE email = '" . $data['email'] . "'";
    $res = $conn->query($q);
    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $id = $user['id'];
        // echo $id;
        $q = "SELECT password FROM passwords WHERE user_id = " . $id;
        $res = $conn->query($q);
        if ($res->num_rows > 0) {
            $hash = $res->fetch_assoc()['password'];
            if (password_verify($data['password'], $hash)) {
                response(200, "Successfully signed in", false, $user);
            } else {
                response(400, "Invalid Password");
            }
        } else {
            response(500, "Failed to sign in.", $conn->error);
        }
    } else {
        response(400, "Invalid Email");
    }
});
