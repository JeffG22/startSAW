<?php
    include("../../connection.php");
    include("utilities.php");

    $prev_location = "view_my_proposals.php";

    if (empty($_POST) || empty($_POST['proposal_id'])) {
        $_SESSION['message'] = "Errore nella ricezione dei dati.";
        navigateTo($prev_location);
    }

    $proposal_id = intval($_POST['proposal_id']);

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

    $stmt = mysqli_prepare($con, "UPDATE proposal 
                                  SET name=?,  description=?, picture=IFNULL(?, picture), address=?, lat=?, lon=?, available_positions=?
                                  WHERE id = ".$proposal_id." AND proposer_id = ".$user_id);

    // IFNULL(a, b) returns a if a is not null, otherwise it returns b
    
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

    mysqli_stmt_bind_param($stmt, "ssssddi", $name, $description, $file, $address, $lat, $lon, $available_pos);
    mysqli_stmt_execute($stmt);


    if(mysqli_affected_rows($con) === 1) {
        $_SESSION['message'] = "Inserimento completato correttamente.";
    } else {
        $_SESSION['message'] = "Nessuna modifica effettuata.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    navigateTo($prev_location);
?>