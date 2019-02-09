<?php
    include("../../connection.php");
    session_start();   

    $con = dbConnect();

    if (!$con) {
        header("location: new_proposal.php");
        exit();
    }

    $stmt = mysqli_prepare($con, "INSERT INTO proposal 
                                    (name, description, picture, address, lat, lon, 
                                        available_positions, date_inserted, proposer_id) 
                                    values (?,?,?,?,?,?,?,?,?)");

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
    $id = 112;
    mysqli_stmt_bind_param($stmt, "ssssddisi", $name, $description, $file, $address, $lat, $lon, 
                                $available_pos, $date, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>View proposals</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="../js/form_validation.js"></script>
</head>
<body>
    <a href="index_proposals.php">^ Home</a>
    <form enctype="multipart/form-data" action="receive_new_proposal.php" onsubmit="return checkData()" method="POST">
        <br>
        Nome
        <input type="text" name="name" required>
        <br>
        Descrizione
        <textarea name="description" rows="5" cols="30" required></textarea>
        <br>
        Immagine
        <!-- This hidden field is used by php to avoid uploading large files.
        Files lager than 4MB are not blocked by this, but upload stops at 4M
        and the file is not sent, thus preventing user from waiting for a file
        that will be rejected server-side.-->
        <input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
        <input type="file" name="picture" accept="image/png, image/jpeg, image/bmp">
        <br>
        Indirizzo
        <input type="text" name="address">
        <br>
        Numero volontari richiesti
        <input type="number" naTme="available_positions" min="1" required>
        <br>
        <input type="submit">
    </form>
</body>
</html>