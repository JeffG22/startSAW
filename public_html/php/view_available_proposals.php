<?php
    include("../../connection.php");
    include("utilities.php");

    if (!empty($_GET['search'])) {
        $search_query = sanitize_inputString($_GET['search']);
    } else if (isset($_GET['search'])) {    // If empty search query, removes "search=" from url
        navigateTo("view_available_proposals.php");
    } else {
        $search_query = "";
    }
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

    <h4>PROPOSTE DI VOLONTARIATO DISPONIBILI</h4>
    <form action = "view_available_proposals.php" method="GET">
        <input type="text" name="search" id="searchbox" value="<?php echo $search_query ?>">
        <input type="submit" value="Cerca">
        <input type="submit" value="Vedi tutte" onclick="document.getElementById('searchbox').value = '';">
    </form>

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

        if (!isPerson($con, $user_id)) {    // TODO: USE SESSION INSTEAD
            echo "Nota: non puoi accettare proposte perché hai eseguito il login come associazione.<br>";
        }

        $query = "SELECT *
                  FROM proposal
                  WHERE available_positions > 0";

        if (empty($_GET['search'])) {
            $result = mysqli_query($con, $query);
        }
               
        if (!empty($_GET['search'])) {
        
            $search_query = "%".$search_query."%";
            
            $query = $query." AND (name LIKE ? OR description LIKE ?)";

            $stmt = mysqli_prepare($con, $query);

            mysqli_stmt_bind_param($stmt, "ss", $search_query, $search_query);
            
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
        }

        if (!$result) {
            echo "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                    Aspetta qualche istante e riprova.";
        } else if (mysqli_num_rows($result) == 0) {
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