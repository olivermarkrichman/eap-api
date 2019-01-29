<?php
    $urls = explode("/", $_SERVER['REQUEST_URI']);
    $endpoint = $urls[2];

    echo $endpoint;
