<?php
    function dbConnect() {
        require("confidential_info.php");
        // Turn off all error reporting
        error_reporting(0);
        $con = mysqli_connect($server, $user, $pswd, $db_name);
        // Report all PHP errors
        error_reporting(-1);
        if (mysqli_connect_errno($con)) {
            return false;
        } else {
            return $con;
        }
        
    }
?>
    