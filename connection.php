<?php
    function dbConnect() {
        $con = mysqli_connect("localhost", "S4328810", "TiganiForever", "S4328810");
        if (mysqli_connect_errno($con)) {
            $_SESSION['message'] = "Error";
            return false;
        } else {
            return $con;
        }
    }
    