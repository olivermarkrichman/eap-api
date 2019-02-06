<?php

	function create($endpoint) {
		if (!empty($_POST)){
			switch ($endpoint){
				case 'users':
					$requiredFields = ['first_name','last_name','email','password'];
					createUsers($requiredFields);
					break;
				case 'clients':
					createClients();
					break;
				default:
					invalidRequest();
					break;
			}
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
							$q = $pdo->prepare("INSERT INTO users (first_name, last_name, email) VALUES (?,?,?)");
							$q->bind_param("sss",$data['first_name'],$data['last_name'],$data['email']);
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
					$q = $pdo->prepare("INSERT INTO users (first_name, last_name, email) VALUES (?,?,?)");
					$q->bind_param("sss",$data['first_name'],$data['last_name'],$data['email']);
					if ($q->execute()){
						$newId = $q->insert_id;
						$q = $pdo->prepare("INSERT INTO passwords (user_id, password) VALUES (?,?)");
						$q->bind_param("ss",$newId,password_hash($data['password'],PASSWORD_DEFAULT));
						if ($q->execute()){
							$createdUserCount++;
						}
					}
				}
			if ($createdUserCount){
				response(201,"Created ".$createdUserCount." User(s)");
			}
			$q->close();
		});
	}

	function createClients() {
		// connect(function($pdo){
		// 	if (isMultiArray($_POST)){
		// 		foreach ($_POST as $data) {
		// 			$q = $pdo->prepare("INSERT INTO clients (name, owner) VALUES (?,?)");
		// 			$q->bind_param("si",$data['name'],$data['owner']);
		// 			if ($q->execute()){
		// 				$createdClientCount++;
		// 			}
		// 		}
		// 	} else {
		// 		$data = $_POST;
		// 		$q = $pdo->prepare("INSERT INTO clients (name, owner) VALUES (?,?)");
		// 		$q->bind_param("si",$data['name'],$data['owner']);
		// 		if ($q->execute()){
		// 			$createdClientCount++;
		// 		}
		//
		// 	}
		// 	if ($createdClientCount){
		// 		response(201,"Created ".$createdClientCount." Client(s)");
		// 	}
		// 	$q->close();
		// });
	}

 ?>
