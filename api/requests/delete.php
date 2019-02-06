<?php

function deleteItem($endpoint,$endpointId){
	$d = [
		endpoint=>$endpoint,
		endpointId=> $endpointId
	];
	connect($d,function($d,$pdo){
		$q = "DELETE FROM ".$d['endpoint']." WHERE id = ".$d['endpointId'];
		if ($pdo->query($q) === TRUE) {
		    response(200, "Deleted ID:".$d['endpointId']." from ".$d['endpoint']);
		} else {
		    response(404, "Record not found");
		}
		
	});
}
