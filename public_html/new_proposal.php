<?php
    include_once("php/domain_constraints.php");
    include_once("php/utilities.php");
    include_once("php/handlesession.php");
    include_once("../connection.php");
    
    my_session_start();
    // If a user is not logged in and lands on this page, redirect to login
    if (!my_session_is_valid())
        header("Location: login.php");

    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {

            $name = "name";
            $description = "description";
            $available_pos = "available_positions";
            $upload_picture = "upload_picture";
            $address = "address";

        // Checks on user input
            if (empty($_POST[$name]) || !checksOnProposalName($_POST[$name]))
                throw new InvalidArgumentException($name);
            if (empty($_POST[$description]) || !checksOnDescription($_POST[$description]))
                throw new InvalidArgumentException($description);
            if (empty($_POST[$available_pos]) || !checksOnAvailablePos($_POST[$available_pos]))
                throw new InvalidArgumentException($available_pos);
            if (!empty($_POST[$upload_picture]) && !($file = uploadPicture()))
                throw new InvalidArgumentException($upload_picture);
            if (!empty($_POST[$address]) && !checksOnAddress($_POST[$address]))
                throw new InvalidArgumentException($address);

            $name_value = sanitize_inputString($_POST[$name]);
            $description_value = nl2br(sanitize_inputString($_POST[$description]));
            $available_pos_value = intval($_POST[$available_pos]);

            if (empty($_POST[$address])) {
                $address_value = NULL; 
                $lat = NULL;
                $long = NULL;
            } else {    // Convert adress to coordinates using OpenStreetMap geocoding API
                $address_value = sanitize_inputString($_POST[$address]);
                $request = "http://nominatim.openstreetmap.org/search.php?q=".urlencode($address_value)."&email=ktmdy@hi2.in&format=json";
                $response = file_get_contents($request);
                $location = json_decode($response, true); // true = return as associative array
                if ($location != NULL && !empty($location)) {
                    $lat = $location[0]['lat']."\n";
                    $lon = $location[0]['lon'];
                }
            }

            $date = date("Y-m-d");  // Returns current date formatted as YYYY-MM-DD

            $user_id = $_SESSION['userId'];

            if (!($conn = dbConnect()))
                throw new Exception("sql ".mysqli_connect_error());
            
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
                $_SESSION['message'] = "Inserimento completato correttamente.";
                header("Location: my_proposals.php");
            } else {
                throw new Exception("sql insert");
            }
            
            mysqli_stmt_close($stmt);
    
            mysqli_close($conn);
        }
    } catch (Exception $ex) {
        $error_flag = true;
        $error_message = $ex->getMessage();
        echo $ex->getMessage();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="js/inputChecks.js"></script>

    <!--Boostrap-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" 
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
	integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" 
	integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" 
	integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!--Inclusions-->
    <script src="js/include.js"></script> 
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">


    <script>
        "use strict"; //necessario per strict mode
        // creare un'array associativo "messaggio -> errore"           
        var err_array = {
                'name' : 'Nome non valido.',
                'description' : 'Descrizione non valida.',
                'available_pos' : 'Il numero di posizioni disponibili deve essere compreso tra 1 e 300.',
                'upload_picture' : 'Errore nel caricamento dell\'immagine. Assicurati che il tipo di file sia supportato (JPG, PNG, BMP) e che le dimensioni non superino i 4MB',
                'address' : 'Indirizzo non valido.',
                'altro' : 'Inserimento non riuscito, si prega di riprovare'
        };
    </script>
</head>
<body>

    <!--Navbar-->
    <?php
		include("php/navbar.php")
	?>

    <a href="php/index_proposals.php">^ Home</a>
    <?php
        if (isset($_SESSION['message'])) {
            echo "<div>".$_SESSION['message']."</div>";
            unset($_SESSION['message']);
        }
    ?>  
    <div class="container">
        <div class="form-group">
            <form enctype="multipart/form-data" id="input_proposal" action="new_proposal.php" onsubmit="return checkPicture()" method="POST">
            <!-- enctype is necessary to encode picture correctly -->
                <legend>Inserisci nuova proposta</legend>
                <!-- Name -->
                <label for="name">Nome proposta: </label>&emsp;
                <input type="text" name="name" id="name" class="form-control" 
                    minlength="3" maxlength="100" required>

                <!-- Description -->
                <label for="description">Descrizione: </label>&emsp;
                <textarea name="description" rows="6" class="form-control" 
                    minlength="10" maxlength="50000" required></textarea>

                <!-- Upload picture -->
                <label for="upload_picture">Immagine: </label>&emsp;
                <!-- This hidden field is used by php to avoid uploading large files.
                Files lager than 4MB are not blocked by this, but upload stops at 4M
                and the file is not sent, thus preventing user from waiting for a file
                that will be rejected server-side.-->
                <input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
                <input type="file" name="upload_picture" id="upload_picture" class="form-control" accept="image/png, image/jpeg, image/jpg, image/bmp" onchange="checkPicture()">
                
                <!-- Address -->
                <label for="address">Indirizzo: </label>&emsp;
                <input type="text" name="address" id="address" class="form-control">

                <!-- Number of available positions-->
                <label for="available_positions">Numero volontari richiesti: </label>&emsp;
                <input type="number" name="available_positions"  id="available_positions" class="form-control"
                    min="1" max="3000" required>
                
                <!-- Submit -->
                <input type="submit" class="btn btn-primary">
            </form>
        </div>
    </div>
</body>
</html>