<?php

$GLOBALS['fields'] = [
	users=>['id','first_name','last_name','email','type','permissions'],
	clients=>['id','name','owner','logo','colour']
];

function getOne($endpoint,$endpointId){
	$d = [
		endpoint=>$endpoint,
		endpointId =>$endpointId,
		fields=> $GLOBALS['fields']
	];
	connect($d,function($d,$pdo){
		$fields = implode(", ", $d['fields'][$d['endpoint']]);
		$q = "SELECT ".$fields." FROM ".$d['endpoint']." WHERE id = ".$d['endpointId'];
		$res = $pdo->query($q);
		if ($res->num_rows > 0) {
			header("Content-Type: application/json");
           	echo json_encode($res->fetch_assoc());
		}
	});
}

function getAll($endpoint){
	$d = [
		endpoint=>$endpoint,
		fields=> $GLOBALS['fields']
	];
	connect($d,function($d,$pdo){
		$fields = implode(", ", $d['fields'][$d['endpoint']]);
		$q = "SELECT ".$fields." FROM ". $d['endpoint'];
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
