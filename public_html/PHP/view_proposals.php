<?php
    include("../../connection.php");
    include("utilities.php");
    session_start();   
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>View available proposals</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

        // Using a dummy user id while sessions are not implemented.
        $user_id = 123;

        if (!$con) {
            echo "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                    Aspetta qualche istante e riprova.";
        } else {
            if (empty($_GET) || !isset($_GET['filter']) || $_GET['filter'] == "available") {
                echo "<br><b>PROPOSTE DI VOLONTARIATO DISPONIBILI</b><br>";
                $result = mysqli_query($con, "SELECT *
                                          FROM proposal
                                          WHERE available_positions > 0");
            } else { // If payload is invalid redirect to default view
                navigateTo("view_proposals.php");
            }
            
            if (!$result) {
                echo "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                    Aspetta qualche istante e riprova.";
            } else if (mysqli_num_rows($result) == 0 && !$is_assoc) {
                echo "Nessuna proposta disponibile al momento. Torna presto a controllare.";
            } else {
                
                while($row = mysqli_fetch_assoc($result)) {
                    printProposalInfo($con, $row);

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