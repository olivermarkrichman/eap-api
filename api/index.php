<?php

    $urls = explode("/", $_SERVER['REQUEST_URI']);
    $endpoint = $urls[2];

    switch ($endpoint) {
        case 'users':
        require("components/users.php");
        break;

        case 'clients':
        echo "/clients";
        break;
    }
