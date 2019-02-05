<?php
function login() {
	if (!empty($_POST['email']) && !empty($_POST['password'])){
		connect($d,function($d,$pdo){
			$q = "SELECT `id` FROM `users` WHERE `email` = '".$_POST['email']."'";
			$res = $pdo->query($q);
			if ($res->num_rows > 0) {
				//PASSWORD STUFF
			} else {
				response(401, "Incorrect email or password");
			}
		});
	} else {
		response(401, "Incorrect email or password");
	}
}
