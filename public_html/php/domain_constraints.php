<?php
    // ------ vincoli sui dati in input -----
    // see: http://php.net/manual/en/filter.filters.validate.php
    function checksOnEmail($str) {
        $emailMinLength = 6;
        $emailMaxLength = 254;
        return strlen($str) >= $emailMinLength && strlen($str) < $emailMaxLength && filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    function checksOnPswd($str) {
        $pswdMinLength = 6;
        $pswdMaxLength = 31;
        return strlen($str) >= $pswdMinLength && strlen($str) < $pswdMaxLength;
    }

    function checksOnTel($str) {
        $telMinLength = 3;
        $telMaxLength = 15;
        return strlen($str) >= $telMinLength && strlen($str) < $telMaxLength &&
               preg_match("/^[0-9]{3,15}$/",$str) == 1;
    }

    function checksOnTipoUtente($str) {
        return $str == "person" || $str == "organization";
    }

    function checksOnName($str) {
        $nameMinLength = 3;
        $nameMaxLength = 50;
        return strlen($str) >= $nameMinLength && strlen($str) < $nameMaxLength;
    }

    function checksOnSurname($str) {
        $surnameMinLength = 3;
        $surnameMaxLength = 31;
        return strlen($str) >= $surnameMinLength && strlen($str) < $surnameMaxLength;
    }

    function checksOnDate($str) {
        return preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$str) == 1;
    }

    function checksOnProv($str) {
        return strlen($str) == 2 && preg_match("/^[a-z]{2}$/",$str);
    }

    function checksOnSettore($str) {
        $settMinLength = 3;
        $settMaxLength = 31;
        return strlen($str) >= $settMinLength && strlen($str) < $settMaxLength;
    }

    function checksOnSite($str) {
        $siteMaxLength = 63;
        return strlen($str) < $siteMaxLength && filter_var($str, FILTER_VALIDATE_URL);
    }

    // ------ specific constraints for proposals -----
    function checksOnProposalName($str) {
        $nameMinLength = 3;
        $nameMaxLength = 100;
        return strlen($str) >= $nameMinLength && strlen($str) <= $nameMaxLength;
    }

    function checksOnDescription($str) {
        $descriptionMinLength = 10;
        $descriptionMaxLength = 50000;
        return strlen($str) >= $descriptionMinLength && strlen($str) <= $descriptionMaxLength;
    }

    function checksOnAvailablePos($str) {
        $value = intval($str);
        $minAvailablePos = 1;
        $maxAvailablePos = 500;
        return $value >= $minAvailablePos && $value <= $maxAvailablePos;
    }

    function checksOnAddress($str) {
        $addressMinLength = 3;
        $addressMaxLength = 100;
        return strlen($str) >= $addressMinLength && strlen($str) <= $addressMaxLength;
    }
?>