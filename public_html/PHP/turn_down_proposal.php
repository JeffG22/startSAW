<?php
    include("../../connection.php");
    include("utilities.php");
    session_start();

    $prev_location = "view_accepted_proposals.php";

    if (empty($_POST) || empty($_POST['proposal_id'])) {
        $_SESSION['message'] = "Si è verificato un errore imprevisto nel rinunciare alla proposta. Riprova.";
        navigateTo($prev_location);
    }

    $proposal_id = intval($_POST['proposal_id']);

    if ($proposal_id < 0) {
        $_SESSION['message'] = "Si è verificato un errore imprevisto nel rinunciare alla proposta. Riprova.";
        navigateTo($prev_location);
    }

    $con = dbConnect();

    if (!$con) {
        navigateTo($prev_location);
    }

    // Using a dummy user id while sessions are not implemented.
    $user_id = 123;

    // Prevents inconsistent states, since I need to update multiple tables
    mysqli_begin_transaction($con);

    // Not using prepared statements since the only user-submitted value has already
    // been sanitized by php function intval() which return an integer.
    mysqli_query($con, "UPDATE proposal 
                        SET available_positions = available_positions + 1
                        WHERE id = ".$proposal_id);
    
    if(mysqli_affected_rows($con) < 0) {    // MySQL error
        $_SESSION['message'] = "Errore nella registrazione della richiesta. Attendi qualche istante e riprova.";
        mysqli_rollback($con);
        navigateTo($prev_location);
    } else if(mysqli_affected_rows($con) == 0) {    // Wrong proposal_id
        $_SESSION['message'] = "La proposta specificata sembra non esistere. Riprova.";
        mysqli_rollback($con);
        navigateTo($prev_location);
    }

    // If previous query succeeded, we know that proposal_id is valid
    $stmt = "DELETE FROM accepted
             WHERE proposal_id = ".$proposal_id." AND acceptor_id = ".$user_id;
    mysqli_query($con, $stmt);

    if(mysqli_affected_rows($con) == 1) {
        $_SESSION['message'] = "Hai rinunciato a questa proposta.";
    } else {
        $_SESSION['message'] = "Impossibile rinunciare a questa proposta. Potresti aver già rinunciato o non averla accettata.";
        mysqli_rollback($con);
    }

    mysqli_commit($con);
    mysqli_close($con);

    navigateTo($prev_location);
?>