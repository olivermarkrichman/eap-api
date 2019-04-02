<?php

function connect($d, $callback)
{
    require("password.php");
    try {
        $conn = new mysqli("eapdb.conrpivrbo5x.eu-west-2.rds.amazonaws.com", "eapuser", $password, "eapdb");
        $callback($d, $conn);
        $conn->close();
    } catch (PDOException $e) {
        echo `Connection failed: ${$e->getMessage()}`;
    }
}
