<?php

	$rest_json = file_get_contents("php://input");
	$_POST= json_decode($rest_json, true);
	$headers = getallheaders();

	require("utils.php");
	require("../api/requests/login.php");
	require("../api/requests/get.php");
	require("../api/requests/create.php");
	require("../api/requests/update.php");
	require("../api/requests/delete.php");

	$urls = explode("/", $_SERVER['REQUEST_URI']);
	$request = strtolower($_SERVER['REQUEST_METHOD']);
     $endpoint = $urls[2];
	$endpointId = $urls[3];

	$validEndpoints = [
		"login",
		"users",
	];

	if ($endpoint === 'login'){
		if ($request === 'post'){
			login();
		} else {
			invalidRequest();
		}
		return;
	}

	authorise($headers);

	if (empty($endpoint)) {
		//No endpoint specified
     	invalidRequest();
	}

	if (!in_array($endpoint,$validEndpoints)){
		//Not a valid endpoint
		invalidRequest();
	}

	if (empty($endpointId)) {
		//No ID specified
		switch ($request){
			case "post":
				//Create new
				create($endpoint);
			    	break;
			case "get":
				//Get all
		 		getAll($endpoint);
				break;
			default:
				invalidRequest();
				break;
		}
	} elseif (is_numeric($endpointId)) {
	    	//If numeric ID is specified
		switch ($request){
			case "get":
				//Get by ID
				getOne($endpoint, $endpointId);
				break;
			case "put":
				//Update by ID
				updateItem($endpoint,$endpointId);
				break;
			case "delete":
				//Delete by ID
				deleteItem($endpoint, $endpointId);
				break;
			default:
				invalidRequest();
				break;
		}
	} else {
		//If the endpointId value isn't numeric
		invalidRequest();
	}
