<?php

function change_password($user_id)
{
    if (!$_POST['current_password']) {
        response(400, "Current Password is required");
    } else {
        if (!$_POST['password']) {
            response(400, "New Password is required");
        } else {
            $d = [
                "current_password"=>$_POST['current_password'],
                "password"=>$_POST['password'],
                "user_id"=>$user_id
            ];
            connect($d, function ($d, $conn) {
                $q = "SELECT `password` FROM `passwords` WHERE `user_id` = " . $d['user_id'];
                $res = $conn->query($q);
                if ($res->num_rows > 0) {
                    $hash = $res->fetch_assoc()['password'];
                    if (password_verify($d['current_password'], $hash)) {
                        $q = "UPDATE `passwords` SET `password` = '" . password_hash($d['password'], PASSWORD_DEFAULT) . "' WHERE `user_id` = " . $d['user_id'];
                        if ($conn->query($q) === true) {
                            response(200, "Password Changed!");
                        } else {
                            response(500, "Failed to create password", $conn->error);
                        }
                    } else {
                        response(400, "Invalid password");
                    }
                } else {
                    response(404, "Invalid Password");
                }
            });
        }
    }
}
