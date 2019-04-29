<?php

function connect($d, $callback)
{
    require("password.php");
    $host = "peap-database.chzh3jub5r2w.us-east-1.rds.amazonaws.com";
    $user = "peap_user";
    $db_name = "peap_database";
    try {
        $conn = new mysqli($host, $user, $password, $db_name);
        $callback($d, $conn);
        $conn->close();
    } catch (PDOException $e) {
        echo `Connection failed: ${$e->getMessage()}`;
    }
}
