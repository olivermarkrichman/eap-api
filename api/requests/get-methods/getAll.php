<?php

function getAll($endpoint){
	connect($endpoint,function($endpoint,$pdo){
		$q = "SELECT * FROM `".$endpoint."`";
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
