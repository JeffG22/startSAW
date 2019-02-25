<?php
    require_once("handlesession.php");
    require_once("utilities.php");
    my_session_logout();
    navigateTo("../index.php");
?>