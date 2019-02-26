<?php
    $error_flag = false;
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
            
            $name = "name";
            $description = "description";
            $available_pos = "available_positions";
            $address = "address";
            $upload_picture = "upload_picture";

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

            $date = date("Y-m-d");  // Returns current date formatted as YYYY-MM-DD

            $user_id = $_SESSION['userId'];

            if (!($conn = dbConnect()))
                throw new Exception("mysql ".mysqli_connect_error());
            
            $query = "INSERT INTO proposal 
                    (name, description, picture, address, lat, lon, 
                        available_positions, date_inserted, proposer_id) 
                    VALUES (?,?,?,?,?,?,?,?,?)";

            if (!($stmt = mysqli_prepare($conn, $query)))
                throw new Exception("mysqli prepare".mysqli_error($conn));

            if (!mysqli_stmt_bind_param($stmt, "ssssddisi", $name_value, $description_value, $file, $address_value, $lat, $lon, 
                                            $available_pos_value, $date, $user_id))
                throw new Exception("mysqli bind param");
            
            if (!mysqli_stmt_execute($stmt))
                throw new InvalidArgumentException("mysqli execute".$stmt->error);

            if(mysqli_affected_rows($conn) == 1) {
                if ($editing) 
                    $_SESSION['message'] = "Proposta aggiornata correttamente.";
                else
                    $_SESSION['message'] = "Inserimento completato correttamente.";
                navigateTo("my_proposals.php");
            } else if ($editing){
                $_SESSION['message'] = "Non hai effettuato alcuna modifica.";
            } else {
                throw new Exception("mysql insert");
            }
            
            mysqli_stmt_close($stmt);
    
            mysqli_close($conn);
        }
    } catch (Exception $ex) {
        $error_flag = true;
        $error_message = $ex->getMessage();
        if (strlen($error_message) >= 5 && substr($error_message, 0, 5) == "mysql")
            $error_message = "mysql";
    }
?>