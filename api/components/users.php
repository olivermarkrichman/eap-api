<?php

$urls = explode("/", $_SERVER['REQUEST_URI']);
$reqUser = $urls[3];

echo $reqUser;

if (empty($reqUser)) {
    //No userID specified
    getAll();
} elseif (is_numeric($reqUser)) {
    //If numeric userID is specified
    getOne();
} else {
    //Invalid Request
}
