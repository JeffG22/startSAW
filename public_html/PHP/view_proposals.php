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
    <br>
    <?php
        if (isset($_SESSION['message'])) {
            echo "<div>".$_SESSION['message']."</div>";
            unset($_SESSION['message']);
        }
        
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
                    echo "<br><div>\n";
                    echo "<img src='".$row['picture']."' height='50px'>\n";
                    echo "<b>".$row['name']."</b><br>\n";
                    echo "<i>Inserito in data: ".$row['date_inserted'];
                    if ($name = getUserName($con, $row['proposer_id'])) {
                        echo " da ".$name;
                    }
                    echo "</i><br>\n";
                    echo "Descrizione: ".$row['description']."<br>\n";
                    echo "Numero di volontari richiesti: <b><i>".$row['available_positions']."</b></i><br>\n";
                    echo "Indirizzo: ".$row['address']."<br>\n";
                    echo "<form action='accept_proposal.php' method='post'>
                            <input type='hidden' name='proposal_id' value='".$row['id']."'>
                            <input type='submit' value='Accetta questa proposta'>
                          </form>
                          <br>";
                    echo "</div>";
                }
            }
        }
    ?>
</body>
</html>