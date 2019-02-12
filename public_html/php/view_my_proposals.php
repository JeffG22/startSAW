<?php
    include("../../connection.php");
    include("utilities.php");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>View accepted proposals</title>
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

        if (!$con) { // Connection error
            echo "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                    Aspetta qualche istante e riprova.";
        } else // Connection succeeded
            echo "<br><b>LE MIE PROPOSTE DI VOLONTARIATO</b><br>";

        $result = mysqli_query($con, "SELECT *
                                      FROM proposal
                                      WHERE proposer_id = ".$user_id);

        if (!$result) { // Query error
            echo "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                    Aspetta qualche istante e riprova.";
        } else if (mysqli_num_rows($result) == 0) { // Empty result
            echo "Sembra che tu non abbia ancora accettato alcuna proposta.";
        } else { // Result not empty
            while($row = mysqli_fetch_assoc($result)) {
                printProposalInfo($con, $row);

                echo "<form method='POST'>
                        <input type='hidden' name='proposal_id' value='".$row['id']."'>
                        <input type='submit' value='Modifica proposta' formaction='edit_proposal.php'>
                        <input type='submit' value='Elimina proposta' formaction='delete_proposal.php'>
                        </form>
                        <br>";
                echo "</div>";
            }                                
        }         
        
    ?>
</body>
</html>