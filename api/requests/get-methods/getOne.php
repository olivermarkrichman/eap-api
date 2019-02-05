<?php

function getOne($endpoint,$endpointId){
	$d = [
		endpoint=>$endpoint,
		endpointId =>$endpointId
	];
	connect($d,function($d,$pdo){
		$q = "SELECT * FROM `".$d['endpoint']."` WHERE id = ".$d['endpointId'];
		$res = $pdo->query($q);
		if ($res->num_rows > 0) {
			$data = [];
		    	while($row = $res->fetch_assoc()) {
				array_push($data,$row);
			}
			header("Content-Type: application/json");
           	echo json_encode($data);
		}
	});
}
