<?php
    include("../../connection.php");
    include_once("handlesession.php");
    include("utilities.php");

    my_session_start();
    // If a user is not logged in and lands on this page, redirect to login
    if (!my_session_is_valid())
        navigateTo("login.php");

    $error_flag = false;
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST) && !empty($_POST['proposal_id'])) {
            
            $proposal_id = intval($_POST['proposal_id']);
            $user_id = $_SESSION['userId'];
            
            if ($proposal_id <= 0)
                throw new InvalidArgumentException("Si è verificato un errore imprevisto nel ritirare la proposta. Riprova.");

            if (!($conn = dbConnect()))
                throw new Exception("mysql ".mysqli_connect_error());

            $query = "DELETE 
                      FROM proposal 
                      WHERE id = ".$proposal_id." AND proposer_id = ".$user_id;
            
            // Not using prepared statements since the only user-submitted value has already
            // been sanitized by php function intval() which returns an integer.
            if(!mysqli_query($conn, $query))
                    throw new Exception("mysql ".mysqli_errno($conn));
            
            if(mysqli_affected_rows($conn) < 0) {    // MySQL error
                throw new Exception("mysql ".mysqli_errno($conn));
            } else if(mysqli_affected_rows($conn) == 0) {    // Wrong proposal_id or not belonging to this user
                throw new Exception("Impossibile eliminare questa proposta. Potresti averla già eliminata.");
            }

            mysqli_close($conn);

            $_SESSION['message'] = "Proposta eliminata correttamente!";
            
        }
        navigateTo("../my_proposals.php");
    } catch (Exception $ex) {
        $error_flag = true;
        $error_message = $ex->getMessage();

        if (strlen($error_message) >= 5 && substr($error_message, 0, 5) == "mysql")
            $_SESSION['message'] = "Errore nell'eliminazione della proposta. Attendi qualche istante e riprova.";
        else
            $_SESSION['message'] = $error_message;

        navigateTo("../my_proposals.php");
    }    
?>