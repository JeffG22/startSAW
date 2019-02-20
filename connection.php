<?php
    function dbConnect() {
        require_once("confidential_info.php")
        $con = mysqli_connect($server, $user, $pswd, $db_name;);
        if (mysqli_connect_errno($con)) {
            return false;
        } else {
            return $con;
        }
    }
?>
    