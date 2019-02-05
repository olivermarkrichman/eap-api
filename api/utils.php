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
