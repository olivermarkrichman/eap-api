<?php

// function getAll($endpoint)
// {
// 	connect(function($pdo){
// 		echo "teest";
// 		// $q = "SELECT * FROM users";
// 		// $res = $pdo->query($q);
// 		//
// 		// if ($res->num_rows > 0) {
//     		// while($row = $result->fetch_assoc()) {
//         	// 	echo $row;
//     		}
// } else {
//     echo "0 results";
// }
//
// 	});
// }

function getAll($endpoint){
	connect(function($pdo){
		$q = "SELECT * FROM users";
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
