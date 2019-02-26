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
    // to change message wording
    $editing = false; 
    $error_flag = false;
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST) &&empty($_POST['proposal_id'])) {

            require("php/proposalform_serverchecks.php");

            $date = date("Y-m-d");  // Returns current date formatted as YYYY-MM-DD

            $user_id = $_SESSION['userId'];

            if (!($conn = dbConnect()))
                throw new Exception("mysql ".mysqli_connect_error());
            
            $query = "INSERT INTO proposal 
                    (name, description, picture, address, lat, lon, 
                        available_positions, date_inserted, proposer_id) 
                    VALUES (?,?,?,?,?,?,?,?,?)";

            if (!($stmt = mysqli_prepare($conn, $query)))
                throw new Exception("mysqli prepare".mysqli_error($conn));

            if (!mysqli_stmt_bind_param($stmt, "ssssddisi", $name_value, $description_value, $file, $address_value, $lat, $lon, 
                                            $available_pos_value, $date, $user_id))
                throw new Exception("mysqli bind param");
            
            if (!mysqli_stmt_execute($stmt))
                throw new InvalidArgumentException("mysqli execute".$stmt->error);

            if(mysqli_affected_rows($conn) == 1) {
                if ($editing) 
                    $_SESSION['message'] = "Proposta aggiornata correttamente.";
                else
                    $_SESSION['message'] = "Inserimento completato correttamente.";
                navigateTo("my_proposals.php");
            } else if ($editing){
                $_SESSION['message'] = "Non hai effettuato alcuna modifica.";
            } else {
                throw new Exception("mysql insert");
            }
            
            mysqli_stmt_close($stmt);
    
            mysqli_close($conn);

        }
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
    <title>Inserisci nuova proposta</title>
    

    <!--Bootstrap-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" 
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
	        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" 
	        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" 
            integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" 
		  integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!--Inclusions-->
    <link rel="stylesheet" href="css/global.css">
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