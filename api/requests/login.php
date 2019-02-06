<?php
function login() {
	if (!empty($_POST['email']) && !empty($_POST['password'])){
		connect($d,function($d,$pdo){
			$q = "SELECT `id` FROM `users` WHERE `email` = '".$_POST['email']."'";
			$res = $pdo->query($q);
			if ($res->num_rows > 0) {
				//PASSWORD STUFF
				$userId = $res->fetch_assoc()['id'];
				$q = "SELECT `password` FROM `passwords` WHERE `user_id` = ".$userId;
				$res = $pdo->query($q);
				$hash = $res->fetch_assoc()['password'];
				if (password_verify($_POST['password'], $hash)){
					$q = "SELECT * FROM users WHERE id = ".$userId;
					$res = $pdo->query($q);
					if ($res->num_rows > 0) {
						header("Content-Type: application/json");
			           	echo json_encode($res->fetch_assoc());
					}
				} else {
					response(401, "Incorrect email or password");
				}
			} else {
				response(401, "Incorrect email or password");
			}

		});
	} else {
		response(401, "Incorrect email or password");
	}
}
