<?php
    include_once("utilities.php");
    include_once("handlesession.php");
    include_once("../../connection.php");
    
    my_session_start();
    // If a user is not logged in and lands on this page, redirect to login
    if (!my_session_is_valid()) {
        navigateTo("../login.php");
    } else if ($_SESSION['type'] == "organization") {   // An organization can't accept proposals
        navigateTo("../profile.php");
    }

    $prev_location = "../browse_proposals.php";

    $error_flag = false;
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST) && !empty($_POST['proposal_id'])) {
            
            $proposal_id = intval($_POST['proposal_id']);
            $user_id = $_SESSION['userId'];
            
            if ($proposal_id <= 0)
                throw new InvalidArgumentException("Si è verificato un errore imprevisto nell'accettare la proposta. Riprova.");

            if (!($conn = dbConnect()))
                throw new Exception("mysql ".mysqli_connect_error());

            // Prevents inconsistent states, since I need to update multiple tables
            if (!mysqli_begin_transaction($conn))
                throw new Exception("mysql transaction".mysqli_connect_error($conn));

            if (!mysqli_autocommit($conn, FALSE))
                throw new Exception("mysql transaction".mysqli_connect_error($conn));
            
            $query1 = "UPDATE proposal 
                       SET available_positions = available_positions - 1
                       WHERE id = ".$proposal_id." AND proposer_id <> ".$user_id." AND available_positions > 0 ";
            
            // Not using prepared statements since the only user-submitted value has already
            // been sanitized by php function intval() which returns an integer.
            if(!mysqli_query($conn, $query1))
                    throw new Exception("mysql ".mysqli_errno($conn));            
            
            if(mysqli_affected_rows($conn) < 0) {    // MySQL error
                mysqli_rollback($conn);
                throw new Exception("mysql ".mysqli_errno($conn));
            } else if(mysqli_affected_rows($conn) == 0) {    // Wrong proposal_id or not belonging to this user
                mysqli_rollback($conn);
                throw new Exception("Errore nell'accettazione della proposta. 
                    La proposta potrebbe non esistere, non avere posti disponibili o essere stata inserita da te.");
            }   

            // If previous query succeeded, we know that proposal_id is valid
            $query2 = "INSERT INTO accepted
                       (acceptor_id, proposal_id)
                       VALUES (".$user_id.", ".$proposal_id.")";

            if(!mysqli_query($conn, $query2)) {
                if (mysqli_errno($conn) == 1062) {  // duplicate entry
                    mysqli_rollback($conn);
                    throw new Exception("Errore: hai già accettato questa proposta.");
                } else
                    throw new Exception("mysql ".mysqli_error($conn));
            }

            if(mysqli_affected_rows($conn) == 1) {
                $_SESSION['message'] = "Proposta accettata. Grazie del tuo aiuto!";
            } else {
                throw new Exception("mysql ".mysqli_error($conn));
            }

            if (!mysqli_commit($conn))
                throw new Exception("mysql transaction failed");
                
            require("send_email.php");

            mysqli_close($conn);
            
        }
        navigateTo("../browse_proposals.php");
    } catch (Exception $ex) {
        $error_flag = true;
        $error_message = $ex->getMessage(); 

        if (strlen($error_message) >= 5 && substr($error_message, 0, 5) == "mysql")
            $_SESSION['message'] = "Errore nell'accettazione della proposta. Attendi qualche istante e riprova.";
        else if ($error_message != "email")
            $_SESSION['message'] = "$error_message";
        
        if ($error_message == "email" || !empty($mail->ErrorInfo))
            $_SESSION['message'] = $_SESSION['message']."<br>PS: C'è stato un problema con l'invio della mail. 
                                        Non preoccuparti, la proposta è stata comunque accettata.";

        navigateTo("../browse_proposals.php");
    } 


    

    mysqli_commit($con);
    mysqli_close($con);

    navigateTo($prev_location);
?>