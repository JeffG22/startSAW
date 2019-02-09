<?php
    include("../../connection.php");
    include("utilities.php");
    session_start();   
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
    <?php
        $con = dbConnect();

        if (!$con) {
            echo "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                    Aspetta qualche istante e riprova.";
        } else {
            $result = mysqli_query($con, "SELECT *
                                          FROM proposal
                                          WHERE available_positions > 0");
            if (!$result) {
                echo "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                    Aspetta qualche istante e riprova.";
            } else if (mysqli_num_rows($result) == 0) {
                echo "Nessuna proposta disponibile al momento. Torna presto a controllare.";
            } else {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<div>";
                    echo "<img src='".$row['picture']."' height='50px'>";
                    echo "<b>".$row['name']."</b><br>";
                    echo "<i>Inserito in data: ".$row['date_inserted'];
                    if ($name = getUserName($con, $row['proposer_id'])) {
                        echo " da ".$name;
                    }
                    echo "</i><br>";
                    echo "Descrizione: ".$row['description']."<br>";
                    echo "Numero di volontari richiesti: <b><i>".$row['available_positions']."</b></i><br>";
                    echo "Indirizzo: ".$row['address']."<br>";
                    echo "<br></div>";
                }
            }
        }
    ?>
</body>
</html>

    
<!--
    

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
-->