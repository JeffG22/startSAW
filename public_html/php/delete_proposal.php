<?php
    include("../../connection.php");
    include("utilities.php");

    $prev_location = "../my_proposals.php";

    if (empty($_POST) || empty($_POST['proposal_id'])) {
        $_SESSION['message'] = "Si è verificato un errore imprevisto nel ritirare la proposta. Riprova.";
        navigateTo($prev_location);
    }

    $proposal_id = intval($_POST['proposal_id']);

    if ($proposal_id < 0) {
        $_SESSION['message'] = "Si è verificato un errore imprevisto nel ritirare la proposta. Riprova.";
        navigateTo($prev_location);
    }

    $con = dbConnect();

    if (!$con) {
        navigateTo($prev_location);
    }

    // Not using prepared statements since the only user-submitted value has already
    // been sanitized by php function intval() which return an integer.
    mysqli_query($con, "DELETE FROM proposal 
                        WHERE id = ".$proposal_id." AND proposer_id = ".$user_id);
    
    if(mysqli_affected_rows($con) < 0) {    // MySQL error
        $_SESSION['message'] = "Errore nella registrazione della richiesta. Attendi qualche istante e riprova.";
        mysqli_rollback($con);
        navigateTo($prev_location);
    } else if(mysqli_affected_rows($con) == 0) {    // Wrong proposal_id or not belonging to this user
        $_SESSION['message'] = "Impossibile eliminare questa proposta. Potresti averla già eliminata.";
        mysqli_rollback($con);
        navigateTo($prev_location);
    }

    // No need to perform additional action, since db is configured to delete all other records
    // referencing a proposal when deleting one (through foreign key constraints)

    mysqli_close($con);

    navigateTo($prev_location);
?>