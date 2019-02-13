<?php
    include("../../connection.php");
    include("utilities.php");
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

        if (!$con) {
            echo "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                    Aspetta qualche istante e riprova.";
        } 
        echo "<br><b>PROPOSTE DI VOLONTARIATO DISPONIBILI</b><br>";
        if (!isPerson($con, $user_id)) {    // TODO: USE SESSION INSTEAD
            echo "Nota: non puoi accettare proposte perché hai eseguito il login come associazione.<br>";
        }
        $result = mysqli_query($con, "SELECT *
                                      FROM proposal
                                      WHERE available_positions > 0");
            
        if (!$result) {
            echo "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                    Aspetta qualche istante e riprova.";
        } else if (mysqli_num_rows($result) == 0 && !$is_assoc) {
            echo "Nessuna proposta disponibile al momento. Torna presto a controllare.";
        } else {
                
            while($row = mysqli_fetch_assoc($result)) {
                echo "<br><div>\n";
                printProposalInfo($con, $row);

                if (isPerson($con, $user_id)) {    // TODO: USE SESSION INSTEAD
                    if($row['proposer_id'] == $user_id) {
                        echo "<input type=\"button\" disabled value=\"Non puoi accettare una proposta inserita da te\">";
                    } else {
                        echo "<form action='accept_proposal.php' method='post'>
                            <input type='hidden' name='proposal_id' value='".$row['id']."'>
                            <input type='submit' value='Accetta questa proposta'>
                            </form>
                            <br>";
                    }
                }

                echo "</div>";
            }
        }
    ?>
</body>
</html>