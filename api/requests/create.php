<?php

	function create($endpoint) {
		if (!empty($_POST)){
			switch ($endpoint){
				case 'users':
					$requiredFields = ['first_name','last_name','email','password','type'];
					createUsers($requiredFields);
					break;
				default:
					invalidRequest();
					break;
			}
		} else {
			response(409, "You need to send valid JSON data");
		}
	}

	function createUsers($requiredFields) {
		connect($requiredFields,function($requiredFields,$pdo){
				if (isMultiArray($_POST)){
					foreach ($_POST as $data) {
						foreach ($requiredFields as $requiredField){
							if (empty($data[$requiredField])){
								response(409, $requiredField . " is blank");
							}
						}
						$q = "SELECT id FROM users WHERE email = '".$data['email']."'";
						$res = $pdo->query($q);
						if ($res->num_rows > 0) {
							response(409,"email is already registered");
						} else {
							$now = time();
							$token = strtoupper(generateToken());
							$q = $pdo->prepare("INSERT INTO users (first_name, last_name, email,created_at,token,type) VALUES (?,?,?,?,?,?)");
							$q->bind_param("sssisi",$data['first_name'],$data['last_name'],$data['email'],$now,$token,$data['type']);
							if ($q->execute()){
								$newId = $q->insert_id;
								$q = $pdo->prepare("INSERT INTO passwords (user_id, password) VALUES (?,?)");
								$q->bind_param("is",$newId,password_hash($data['password'],PASSWORD_DEFAULT));
								if ($q->execute()){
									$createdUserCount++;
								}
							}
						}
					}
				} else {
					$data = $_POST;
					foreach ($requiredFields as $requiredField){
						if (empty($data[$requiredField])){
							response(409, $requiredField . " is blank");
						}
					}
					$q = "SELECT id FROM users WHERE email = '".$data['email']."'";
					$res = $pdo->query($q);
					if ($res->num_rows > 0) {
						response(409,"email is already registered");
					} else {
						$now = time();
						$token = strtoupper(generateToken());
						$q = $pdo->prepare("INSERT INTO users (first_name, last_name, email,created_at,token,type) VALUES (?,?,?,?,?,?)");
						$q->bind_param("sssisi",$data['first_name'],$data['last_name'],$data['email'],$now,$token,$data['type']);
						if ($q->execute()){
							$newId = $q->insert_id;
							$q = $pdo->prepare("INSERT INTO passwords (user_id, password) VALUES (?,?)");
							$q->bind_param("ss",$newId,password_hash($data['password'],PASSWORD_DEFAULT));
							if ($q->execute()){
								$createdUserCount++;
							}
						} else {
						}
					}
				}
			if ($createdUserCount){
				response(201,"Created ".$createdUserCount." User(s)");
			}

		});
	}
