<?php
    include_once("utilities.php");
    include_once("handlesession.php");
    include_once("../../connection.php");
    
    my_session_start();
    // If a user is not logged in and lands on this page, redirect to login
    if (!my_session_is_valid()) {
        navigateTo("../login.php");
    } else if ($_SESSION['type'] == "organization") {   // An organization can't have accepted proposals
        navigateTo("../profile.php");
    }

    $error_flag = false;
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST) && !empty($_POST['proposal_id'])) {
            
            $proposal_id = intval($_POST['proposal_id']);
            $user_id = $_SESSION['userId'];

            if ($proposal_id <= 0)
                throw new InvalidArgumentException("Si è verificato un errore imprevisto nel ritirare la proposta. Riprova.");

            if (!($conn = dbConnect()))
                throw new Exception("mysql ".mysqli_connect_error());

            // Prevents inconsistent states, since I need to update multiple tables
            if (!mysqli_begin_transaction($conn))
                throw new Exception("mysql transaction".mysqli_connect_error($conn));

            if (!mysqli_autocommit($conn, FALSE))
                throw new Exception("mysql transaction".mysqli_connect_error($conn));
            
            $query1 = "UPDATE proposal
                       SET available_positions = available_positions + 1
                       WHERE proposal.id = ".$proposal_id." AND 
                             proposal.id IN 
                                        (SELECT id
                                         FROM accepted
                                         WHERE acceptor_id = ".$user_id.")";
            
            // Not using prepared statements since the only user-submitted value has already
            // been sanitized by php function intval() which returns an integer.
            if(!mysqli_query($conn, $query1))
                    throw new Exception("mysql ".mysqli_errno($conn));            
            
            if(mysqli_affected_rows($conn) < 0) {    // MySQL error
                mysqli_rollback($conn);
                throw new Exception("mysql ".mysqli_errno($conn));
            } else if(mysqli_affected_rows($conn) == 0) {    // Wrong proposal_id or not belonging to this user
                mysqli_rollback($conn);
                throw new Exception("Errore nel rifiutare la proposta. 
                    La proposta potrebbe non esistere o non essere stata accettata da te. Riprova.");
            }   

            // If previous query succeeded, we know that proposal_id is valid
            $query2 = "DELETE FROM accepted
                       WHERE proposal_id = ".$proposal_id." AND acceptor_id = ".$user_id;

            if(!mysqli_query($conn, $query2))
                throw new Exception("mysql ".mysqli_errno($conn));

            if(mysqli_affected_rows($conn) == 1) {
                $_SESSION['message'] = "Hai rinunciato a questa proposta";
            } else {
                $_SESSION['message'] = "Errore: hai già rinunciato a questa.";
                mysqli_rollback($conn);
            }

            if (!mysqli_commit($conn))
                throw new Exception("mysql transaction failed");
                
            mysqli_close($conn);            
        }
    } catch (Exception $ex) {
        $error_flag = true;
        $error_message = $ex->getMessage();

        if (strlen($error_message) >= 5 && substr($error_message, 0, 5) == "mysql")
            $_SESSION['message'] = "Errore nell'eliminazione della proposta. Attendi qualche istante e riprova.";
        else
            $_SESSION['message'] = $error_message;
    }    
    navigateTo("../accepted_proposals.php");
?>