<?php
    include_once("utilities.php");
    include_once("handlesession.php");
    include_once("../../connection.php");
    
    my_session_start();
    // If a user is not logged in and lands on this page, redirect to login
    if (!my_session_is_valid()) {
        navigateTo("../login.php");
    } else if ($_SESSION['type'] == "organization") {   // An organization can't have accepted proposals
        navigateTo("../user.php");
    }

    $user_id = $_SESSION['userId'];


    $prev_location = "../browse_proposals.php";

    if (empty($_POST) || empty($_POST['proposal_id'])) {
        $_SESSION['message'] = "Si è verificato un errore imprevisto nell'accettare la proposta. Riprova.";
        navigateTo($prev_location);
    }

    $proposal_id = intval($_POST['proposal_id']);

    if ($proposal_id < 0) {
        $_SESSION['message'] = "Si è verificato un errore nell'accettare la proposta. Riprova.";
        navigateTo($prev_location);
    }

    $con = dbConnect();

    if (!$con) {
        navigateTo($prev_location);
    }

    // Prevents an association from accepting a proposal.
    // This could only happen if someone logged in as an association, copied the session id
    // and used it to construct a custom payload to accept a proposal.
    if(!isPerson($con, $user_id)) {
        $_SESSION['message'] = "Impossibile per una associazione accettare una proposta. 
                                Effettua il login come persona e riprova.";
        navigateTo($prev_location);
    }  

    // Prevents inconsistent states, since I need to update multiple tables
    mysqli_begin_transaction($con);

    mysqli_autocommit($con, FALSE);

    // Not using prepared statements since the only user-submitted value has already
    // been sanitized by php function intval() which return an integer.
    mysqli_query($con, "UPDATE proposal 
                        SET available_positions = available_positions - 1
                        WHERE id = ".$proposal_id." AND available_positions > 0");
    
    if(mysqli_affected_rows($con) < 0) {    // MySQL error
        $_SESSION['message'] = "Errore nella registrazione della richiesta. Attendi qualche istante e riprova.";
        mysqli_rollback($con);
        navigateTo($prev_location);
    } else if(mysqli_affected_rows($con) == 0) {    // Wrong proposal_id or no available positions
        $_SESSION['message'] = "La proposta specificata sembra non esistere o non avere posizioni disponibili. Riprova.";
        mysqli_rollback($con);
        navigateTo($prev_location);
    }

    // Prevent accepting proposals by the same user which inserted it
    $res = mysqli_query($con, "SELECT * 
                        FROM proposal
                        WHERE id = ".$proposal_id." AND proposer_id = ".$user_id);

    if (mysqli_num_rows($res) != 0) {
        $_SESSION['message'] = "Errore: impossibile accettare una proposta inserita da te.";
        mysqli_rollback($con);
        mysqli_close($con);
        navigateTo($prev_location);
    }

    // If previous query succeeded, we know that proposal_id is valid
    $stmt = "INSERT INTO accepted
                        (acceptor_id, proposal_id)
                        VALUES (".$user_id.", ".$proposal_id.")";
    mysqli_query($con, $stmt);

    if(mysqli_affected_rows($con) == 1) {
        $_SESSION['message'] = "Proposta accettata. Grazie del tuo aiuto!";
    } else {
        $_SESSION['message'] = "Errore: hai già accettato questa proposta.";
        mysqli_rollback($con);
    }

    mysqli_commit($con);
    mysqli_close($con);

    navigateTo($prev_location);
?>