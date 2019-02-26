<?php
// Checks on user input
    if (empty($_POST[$name]) || !checksOnProposalName($_POST[$name]))
        throw new InvalidArgumentException($name);
    if (empty($_POST[$description]) || !checksOnDescription($_POST[$description]))
        throw new InvalidArgumentException($description);
    if (empty($_POST[$available_pos]) || !checksOnAvailablePos($_POST[$available_pos]))
        throw new InvalidArgumentException($available_pos);
    if (!empty($_FILES[$upload_picture]['tmp_name']) && !($file = uploadPicture($upload_picture)))
        throw new InvalidArgumentException($upload_picture);
    if (!empty($_POST[$address]) && !checksOnAddress($_POST[$address]))
        throw new InvalidArgumentException($address);

    $name_value = sanitize_inputString($_POST[$name]);
    $description_value = nl2br(sanitize_inputString($_POST[$description]));
    $available_pos_value = intval($_POST[$available_pos]);

    if (empty($_POST[$address])) {
        $address_value = NULL; 
        $lat = NULL;
        $long = NULL;
    } else {    // Convert address to coordinates using OpenStreetMap geocoding API
        $address_value = sanitize_inputString($_POST[$address]);
        $request = "http://nominatim.openstreetmap.org/search.php?q=".urlencode($address_value)."&email=ktmdy@hi2.in&format=json";
        $response = file_get_contents($request);
        $location = json_decode($response, true); // true = return as associative array
        if ($location != NULL && !empty($location)) {
            $lat = $location[0]['lat']."\n";
            $lon = $location[0]['lon'];
        }
    }
?>