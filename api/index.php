<?php
	require("utils.php");
	require("../api/requests/get.php");

	$urls = explode("/", $_SERVER['REQUEST_URI']);
     $endpoint = $urls[2];

	if (empty($endpoint)) {
     	invalidRequest();
	}

     switch ($endpoint) {
     	case 'users':
          	require("components/users.php");
     		break;

     	default:
          	invalidRequest();
          	break;
	}
