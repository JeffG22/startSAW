<?php
    // ------ vincoli sui dati in input -----

    function checksOnEmail($str) {
        $emailMinLength = 6;
        $emailMaxLength = 31;
    }

    function checksOnPswd($str) {
        $pswdMinLength = 6;
        $pswdMaxLength = 31;
    }

    function checksOnTel($str) {
        $telMinLength = 6;
        $telMaxLength = 15;
    }

    function checksOnName($str) {
        $nomeMinLength = 4;
        $nomeMaxLength = 31;
    }

    function checksOnSurname($str) {
        $surnameMinLength = 3;
        $surnameMaxLength = 31;
    }

    function checksOnDate($str) {
        $dateLength = 10;
        // TODO formato data pattern
    }

    function checksOnCity($str) {
        $cityMinLength = 3;
        $cityMaxLength = 31;
    }

    function checksOnProv($str) {
        $provMinLength = 2;
        $provMaxLength = 31;
    }

    function checksOnSettore($str) {
        $settMinLength = 3;
        $settMaxLength = 31;
    }

    function checksOnSite($str) {
        $siteMinLength = 3;
        $siteMaxLength = 63;
    }
?>