<?php

function updateItem($endpoint,$endpointId) {
	if (!empty($_POST)){
		switch ($endpoint){
			case 'users':
				$acceptedFields = ['first_name','last_name','email','type','permissions'];
				updateUser($acceptedFields,$endpointId);
				break;
			default:
				invalidRequest();
				break;
		}
	} else {
		response(409, "You need to send valid JSON data");
	}
}

function updateUser($acceptedFields,$endpointId){
	$d= [
		acceptedFields=>$acceptedFields,
		endpointId=>$endpointId
	];
	connect($d,function($d,$pdo){
		$data = $_POST;
		$fieldsToUpdate = "";
		if ($data['password']){
			$changePassword = true;
		}
		// echo (count($data));
		foreach ($d['acceptedFields'] as $i => $acceptedField){
			if (!empty($data[$acceptedField])){
					if (gettype($data[$acceptedField]) === 'string'){
						$data[$acceptedField] = "'".$data[$acceptedField]."'";
					}
					$fieldsToUpdate .= $acceptedField." = ".$data[$acceptedField].", ";
			}
		}
		$fields = substr($fieldsToUpdate, 0, -2);
		$q = "SELECT `id` FROM `users` WHERE `id` = ".$d['endpointId'];
		$res = $pdo->query($q);
		if ($res->num_rows > 0){
			$q = "UPDATE users SET " . $fields . " WHERE id = ".$d['endpointId'];
			if ($pdo->query($q) === TRUE) {
		    		if ($changePassword){
			    		$q = "UPDATE passwords SET password = '".password_hash($data['password'],PASSWORD_DEFAULT)."' WHERE user_id = ".$d['endpointId'];
			    		if ($pdo->query($q) === TRUE) {
				     	response(200, "Updated Successfully");
			    		} else {
				     	response(503, $pdo->error);
					}
		    		} else {
					response(200, "Updated Successfully");
				}
			} else {
			    response(503, $pdo->error);
			}
		} else {
			response(404, "User not found");
		}
	});
}
