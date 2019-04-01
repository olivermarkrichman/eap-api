<?php

function connect($d, $callback)
{
    require("password.php");
    try {
        $conn = new mysqli("160.153.129.203", "eap-api", $password, "eap-db");
        $callback($d, $conn);
        $conn->close();
    } catch (PDOException $e) {
        echo `Connection failed: ${$e->getMessage()}`;
    }
}
