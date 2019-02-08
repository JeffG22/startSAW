<?php
    session_start();
    function uploadPicture() {
        if(!isset($_FILES['picture']) || !is_uploaded_file($_FILES['picture']['tmp_name'])) {
            echo "Errore nel caricamento dell'immagine. L'immagine potrebbe non esistere o essere troppo grande (max 4 MB).";
        } else {
            //$uploaddir = './uploads/';  
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
        $_SESSION['message'] = "Errore nella ricezione dei dati. L'errore potrebbe essere dovuto a un'immagine troppo grande (max 4MB)";
        header("location: new_proposal.php");
        exit();
    }

    if ( empty($_POST['name']) || empty($_POST['description']) || empty($_POST['available_positions']) ) {
        $_SESSION['message'] = "Errore. Compila tutti i campi richiesti.";
        header("location: new_proposal.php");
        exit();
    }

    $file = uploadPicture();

    if (!$file) {
        header("location: new_proposal.php");
        exit();
    }

    
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