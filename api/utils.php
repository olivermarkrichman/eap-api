<?php

require("httpResponses.php");

function connect($d,$callback) {
	require("dbInfo.php");
	try {
		$pdo = new mysqli($dbInfo['host'],$dbInfo['user'], $dbInfo['password'],$dbInfo['db']);
		$callback($d,$pdo);
	} catch(PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
	}
}

function response($code, $message){
    header("HTTP/1.0 " . $code . " " . $httpResponses[$code]);
    header("Content-Type: application/json");
    echo json_encode(['response'=>$message]);
    if (substr($code, 0, 1) >= 4 === true) {
        die();
    }
}

function authorise($headers){
	$auth = $headers['Authorization'];
	if (strpos( $auth, "EAP" ) !== false){
		$token = explode(" ",$auth);
		$token = "'".$token[1]."'";
		connect($token,function($token,$pdo){
			$q = "SELECT `id` FROM `users` WHERE `token` = ".$token;
			$res = $pdo->query($q);
			if (!$res->fetch_assoc()['id']){
				response(401, "Invalid Token");
			}

		});
	} else {
		response(401, "Invalid Token");
	}
}

function invalidRequest(){
    response(400, "Invalid Request");
}

function isMultiArray($array){
	foreach ($array as $item){
		if (is_array($item)){
			return true;
		}
		return false;
	}
}

function generateToken() {
	return md5(uniqid());
}
