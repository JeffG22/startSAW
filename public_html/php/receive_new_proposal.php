<?php
    include("../../connection.php");
    include("utilities.php");

    $prev_location = "new_proposal.php";

    if (empty($_POST)) {
        $_SESSION['message'] = "Nessun dato ricevuto.";
        navigateTo($prev_location);
    }

    if ( empty($_POST['name']) || empty($_POST['description']) || empty($_POST['available_positions']) ) {
        $_SESSION['message'] = "Errore. Compila tutti i campi richiesti.";
        navigateTo($prev_location);
    } else {
        $name = sanitize_inputString($_POST['name']);
        $description = nl2br(sanitize_inputString($_POST['description']));
        $available_pos = intval($_POST['available_positions']);
        if ($available_pos <= 0) {
            $_SESSION['message'] = "Controlla il numero di posizioni disponibili. Deve essere un numero positivo.";
            navigateTo($prev_location);
        }
    }

    $file = uploadPicture();

    $con = dbConnect();

    if (!$con) {
        navigateTo($prev_location);
    }

    $stmt = mysqli_prepare($con, "INSERT INTO proposal 
                                    (name, description, picture, address, lat, lon, 
                                        available_positions, date_inserted, proposer_id) 
                                    VALUES (?,?,?,?,?,?,?,?,?)");

    if (empty($_POST['address'])) {
        $address = NULL; 
        $lat = NULL;
        $long = NULL;
    } else {
        $address = sanitize_inputString($_POST['address']);
        $request = "http://nominatim.openstreetmap.org/search.php?q=".urlencode($address)."&email=ktmdy@hi2.in&format=json";
        $response = file_get_contents($request);
        $location = json_decode($response, true); // true = return as associative array
        if ($location != NULL && !empty($location)) {
            $lat = $location[0]['lat']."\n";
            $lon = $location[0]['lon'];
        }
    }

    $date = date("Y-m-d");

    mysqli_stmt_bind_param($stmt, "ssssddisi", $name, $description, $file, $address, $lat, $lon, 
                                $available_pos, $date, $user_id);
    mysqli_stmt_execute($stmt);

    if(mysqli_affected_rows($con) == 1) {
        $_SESSION['message'] = "Inserimento completato correttamente.";
    } else {
        $_SESSION['message'] = "Errore nell'inserimento. Riprova.";
    }

    mysqli_stmt_close($stmt);
    
    mysqli_close($con);

    navigateTo($prev_location);
?>