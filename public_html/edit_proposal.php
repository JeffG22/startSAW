<?php
    require_once("php/domain_constraints.php");
    require_once("php/utilities.php");
    require_once("php/handlesession.php");
    require_once("../connection.php");
    
    my_session_start();
    // If a user is not logged in and lands on this page, redirect to login
    if (!my_session_is_valid())
        navigateTo("login.php");

    $name = "name";
    $description = "description";
    $available_pos = "available_positions";
    $address = "address";
    $upload_picture = "upload_picture";

    // I keep a variable to check I'm entering a new proposal (false) or editing one (true)
    // to change wording in included scripts
    $editing = true; 

    $error_flag = false;
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST) && !empty($_POST['proposal_id'])) {
            
            $proposal_id = intval($_POST['proposal_id']);
            $user_id = $_SESSION['userId'];

            if (empty($_POST['edited'])) { // User hasn't made changes yet. Retrieve fields from db.
                if ($proposal_id <= 0) 
                    throw new InvalidArgumentException("proposal_id");

                if (!($conn = dbConnect()))
                    throw new Exception("mysql ".mysqli_connect_error());

                $query = "SELECT *
                          FROM proposal
                          WHERE id = ".$proposal_id." AND proposer_id = ".$user_id;
                
                if(!($result = mysqli_query($conn, $query)))
                    throw new Exception("mysql ".mysqli_error());

                if (mysqli_num_rows($result) != 1)
                    throw new InvalidArgumentException("proposal_id");

                if (!($row = mysqli_fetch_assoc($result)))
                    throw new InvalidArgumentException("mysql");
               
                mysqli_close($conn);
                

            } else {    // User has sent form with modifications. Check input and write to db.
                require("php/proposalform_serverchecks.php");

                if (!($conn = dbConnect()))
                    throw new Exception("mysql ".mysqli_connect_error());
                
                $query = "UPDATE proposal 
                          SET name=?,  description=?, picture=IFNULL(?, picture), address=?, 
                              lat=?, lon=?, available_positions=?
                          WHERE id = ".$proposal_id." AND proposer_id = ".$user_id;

                if (!($stmt = mysqli_prepare($conn, $query)))
                    throw new Exception("mysqli prepare".mysqli_error($conn));

                if (!mysqli_stmt_bind_param($stmt, "ssssddi", $name_value, $description_value, $file, $address_value, 
                                                $lat, $lon, $available_pos_value))
                    throw new Exception("mysqli bind param");
                
                if (!mysqli_stmt_execute($stmt))
                    throw new InvalidArgumentException("mysqli execute".$stmt->error);

                if(mysqli_affected_rows($conn) == 1) {
                    $_SESSION['message'] = "Proposta aggiornata correttamente.";
                } else {
                    $_SESSION['message'] = "Non hai effettuato alcuna modifica.";
                }
                
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                
                navigateTo("my_proposals.php");
            }
        } else  // Manually navigated to this page of forged input
            navigateTo("my_proposals.php");
        
    } catch (Exception $ex) {
        $error_flag = true;
        $error_message = $ex->getMessage();
        if (strlen($error_message) >= 5 && substr($error_message, 0, 5) == "mysql")
            $error_message = "mysql";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifica proposta</title>
    
    <?php
        require("php/head_common.php");
    ?>
    
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <script src="js/inputChecks.js"></script>
    
    <?php
        require("php/proposalform_js.php");
    ?>
</head>
<body>
    <!--Navbar-->
    <?php
		include("php/navbar.php")
    ?>
    
    <?php
        include("php/popup.php");
    ?>

    <?php
        require("php/proposalform_html.php")
    ?>
</body>
</html>