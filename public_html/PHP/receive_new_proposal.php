<?php
    include("../../connection.php");
    include("utilities.php");
    session_start();

    $prev_location = "new_proposal.php";

    function uploadPicture() {
        if(isset($_FILES['picture']) && is_uploaded_file($_FILES['picture']['tmp_name'])) {
            $uploaddir = "../userpics/";
            $filename = (microtime(true)*10000);
            $uploadfile = $uploaddir.$filename.".".pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);

            $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_BMP);
            $detectedType = exif_imagetype($_FILES['picture']['tmp_name']);

            if(!in_array($detectedType, $allowedTypes)) {
                $_SESSION['message'] = "Formato file non ammesso";
            } else if ($_FILES['picture']['size'] > 4194304) {
                $_SESSION['message'] = "Dimensione massima superata.\n";
            } else if (move_uploaded_file($_FILES['picture']['tmp_name'], $uploadfile)) {
                $_SESSION['message'] = "File caricato con successo.\n";
                return $uploadfile;
            } else {
                $_SESSION['message'] = "Caricamento fallito.\n";
            }
        }
    }      

    if (empty($_POST)) {
        $_SESSION['message'] = "Nessun dato ricevuto.";
        navigateTo($prev_location);
    }

    if ( empty($_POST['name']) || empty($_POST['description']) || empty($_POST['available_positions']) ) {
        $_SESSION['message'] = "Errore. Compila tutti i campi richiesti.";
        navigateTo($prev_location);
    } else {
        $name = htmlspecialchars($_POST['name']);
        $description = htmlspecialchars($_POST['description']);
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
        $address = htmlspecialchars($_POST['address']);
        $request = "http://nominatim.openstreetmap.org/search.php?q=".urlencode($address)."&email=ktmdy@hi2.in&format=json";
        $response = file_get_contents($request);
        $location = json_decode($response, true); // true = return as associative array
        if ($location != NULL && !empty($location)) {
            $lat = $location[0]['lat']."\n";
            $lon = $location[0]['lon'];
        }
    }

    $date = date("Y-m-d");
    $user_id = 112;
    mysqli_stmt_bind_param($stmt, "ssssddisi", $name, $description, $file, $address, $lat, $lon, 
                                $available_pos, $date, $user_id);
    mysqli_stmt_execute($stmt);

    if(mysqli_affected_rows($con) === 1) {
        $_SESSION['message'] = "Inserimento completato correttamente.";
    } else {
        $_SESSION['message'] = "Errore nell'inserimento. Riprova.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    navigateTo($prev_location);
?>