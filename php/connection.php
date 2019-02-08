<?php
    $host = "localhost";
    $user = "S4328810";
    $pwd = "TiganiForever";
    $dbname = "S4328810";

    function dbConnect() {
        $con = mysqli_connect($host, $user, $pwd, $dbname);
        if (mysqli_connect_errno($con)) {
            $_SESSION['message'] = "Error";
            return false;
        } else {
            return $con;
        }
    }
    