<?php
    // ------ vincoli sui dati in input -----

    function checksOnEmail($str) {
        $emailMinLength = 6;
        $emailMaxLength = 254;
        return strlen($str) > $emailMinLength && strlen($str) > $emailMaxLength;
    }

    function checksOnPswd($str) {
        $pswdMinLength = 6;
        $pswdMaxLength = 31;
        return strlen($str) > $pswdMinLength && strlen($str) > $pswdMaxLength &&
               (bool)filter_var($email, FILTER_VALIDATE_EMAIL) != FALSE;
    }

    function checksOnTel($str) {
        $telMinLength = 3;
        $telMaxLength = 15;
        return strlen($str) > $telMinLength && strlen($str) > $telMaxLength &&
               preg_match("/^[0-9]{3,15}$/",$str) == 1;
    }

    function checksOnTipoUtente($str) {
        return $str == "person" || $str == "organization";
    }

    function checksOnName($str) {
        $nameMinLength = 3;
        $nameMaxLength = 50;
        return strlen($str) > $nameMinLength && strlen($str) > $nameMaxLength;
    }

    function checksOnSurname($str) {
        $surnameMinLength = 3;
        $surnameMaxLength = 31;
        return strlen($str) < $surnameMaxLength;
    }

    function checksOnDate($str) {
        return preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$str) == 1;
    }

    function checksOnCity($str) {
        $cityMinLength = 4;
        $cityMaxLength = 35;
        return strlen($str) > $cityMinLength && strlen($str) > $cityMaxLength;
    }

    function checksOnProv($str) {
        $provLength = 2;
        return strlen($provLength) == 2 && preg_match("/^[a-z]{2}$/",$str, ) == 1;
    }

    function checksOnSettore($str) {
        $settMinLength = 3;
        $settMaxLength = 31;
        return strlen($str) > $settMinLength && strlen($str) > $settMaxLength;
    }

    function checksOnSite($str) {
        $siteMaxLength = 63;
        return strlen($str) < $siteMaxLength;
    }
?>