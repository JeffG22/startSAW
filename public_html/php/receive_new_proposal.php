<?php
    include("../../connection.php");
    session_start();
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
        header("location: new_proposal.php");
        exit();
    }

    if ( empty($_POST['name']) || empty($_POST['description']) || empty($_POST['available_positions']) ) {
        $_SESSION['message'] = "Errore. Compila tutti i campi richiesti.";
        header("location: new_proposal.php");
        exit();
    } else {
        $name = htmlspecialchars($_POST['name']);
        $description = htmlspecialchars($_POST['description']);
        $available_pos = intval($_POST['available_positions']);
        if ($available_pos <= 0) {
            $_SESSION['message'] = "Controlla il numero di posizioni disponibili. Deve essere un numero positivo.";
            header("location: new_proposal.php");
            exit();
        }
    }

    $file = uploadPicture();

    $con = dbConnect();

    if (!$con) {
        header("location: new_proposal.php");
        exit();
    }

    $stmt = mysqli_prepare($con, "INSERT INTO proposal (name, description, picture, address, coord_x, coord_y, available_positions, date_inserted, proposer_id) values (?,?,?,?,?,?,?,?,?)");

    if (empty($_POST['address'])) {
        $address = NULL;
    } else {
        $address = htmlspecialchars($_POST['address']);
    }

    $date = date("Y-m-d");
    $coord_x = 123.45;
    $coord_y = -566.1221;
    $id = 112;
    mysqli_stmt_bind_param($stmt, "ssssddisi", $name, $description, $file, $address, $coord_x, $coord_y, $available_pos, $date, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <title>Login</title>
</head>
<body>
    <img src="
        <?php
            echo $file;
        ?>
    ">
</body>
</html>