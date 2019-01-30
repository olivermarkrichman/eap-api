<?php

	function create($endpoint) {
		switch ($endpoint){
			case 'users':
				createUsers();
				break;
			default:
				invalidRequest();
				break;
		}
	}

	function createUsers() {
		connect(function($pdo){
			if (isMultiArray($_POST)){
				foreach ($_POST as $user) {
					$data = $user;
					$q = $pdo->prepare("INSERT INTO users (first_name, last_name, email) VALUES (?,?,?)");
					$q->bind_param("sss",$data['first_name'],$data['last_name'],$data['email']);
					if ($q->execute()){
						response(201,"Created User");
					}
				}
			} else {
				$data = $_POST;
				$q = $pdo->prepare("INSERT INTO users (first_name, last_name, email) VALUES (?,?,?)");
				$q->bind_param("sss",$data['first_name'],$data['last_name'],$data['email']);
				if ($q->execute()){
					response(201,"Created User");
				}

			}
			$q->close();
		});
	}

 ?>
