<?php
    require_once("handlesession.php");

    my_session_logout();
    header("Location: login.php");
?>