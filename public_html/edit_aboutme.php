<?php
    require_once("php/domain_constraints.php");
    require_once("php/utilities.php");
    require_once("php/handlesession.php");
    require_once("../confidential_info.php");
    require_once("../connection.php");

    my_session_start();
    if (!my_session_is_valid()) // Se un utente non è registrato --> redirect to index.php
        navigateTo("index.php");
    // Se un utente è registrato --> ok

    // ----- CONTROLLI LATO SERVER su INPUT RICEVUTI -----
    $error_flag = false;
    $updated = false;
    try {
        // ----- recupero dati utente -----
        $query1 = "SELECT description FROM user WHERE user_id=".$_SESSION['userId'];
        if (!($conn = dbConnect()))
            throw new Exception("mysql ".mysqli_connect_error());
        if (!($res = mysqli_query($conn, $query1)))
            throw new Exception("mysql ".mysqli_error($conn));
        if (!($row = mysqli_fetch_assoc($res)))
          throw new InvalidArgumentException("mysql");
        if (mysqli_num_rows($res) != 1) // not match
          throw new InvalidArgumentException("mysql");
        mysqli_close($conn);
        
        //print_r($row);
        // row(description, picture)
        $description_value = $row["description"];

        $description = "description"; // descrizione
        $picture = "picture"; // picture
      
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // ----- controllo dati ------
            if (empty($_POST[$description]) && empty($_FILES[$picture]['tmp_name']))
                throw new InvalidArgumentException("args");
            if (!empty($_POST[$description]) && !checksOnDescription($_POST[$description]))
                throw new InvalidArgumentException($description);
            if (!empty($_FILES[$picture]['tmp_name']) && !($file = uploadPicture($picture)))
                throw new InvalidArgumentException($picture);
            
        // ----- sanitizzazione input -----           
            if (!empty($_POST[$description]))
                    $_POST[$description] = nl2br(sanitize_inputString($_POST[$description]));
            else
                $_POST[$description] = null;
        
        // ----- inserimento nel DB se rispetta vincoli -----
            if (!($conn = dbConnect()))
                throw new Exception("mysql ".mysqli_connect_error());

            $query2 = "UPDATE user SET ";

            if (!empty($_POST[$description]))
                $query2 .= "description=?";
            if (!empty($_POST[$description]) && !empty($_FILES[$picture]['tmp_name']))
                $query2 .= ", picture=?";
            if (empty($_POST[$description]) && !empty($_FILES[$picture]['tmp_name']))
                $query2 .= "picture=?";
            $query2 .= " WHERE user_id=".$_SESSION['userId'];
            
            if (!($stmt = mysqli_prepare($conn, $query2)))
                    throw new Exception("mysql ".$conn->error);

            if (!empty($_POST[$description]) && !empty($_FILES[$picture]['tmp_name'])) {
                if (!mysqli_stmt_bind_param($stmt, 'ss', $_POST[$description], $file))
                    throw new Exception("mysql bind param");            
            } else if (empty($_FILES[$picture]['tmp_name'])) {
                if (!mysqli_stmt_bind_param($stmt, 's', $_POST[$description]))
                    throw new Exception("mysql bind param");
            } else {
                if (!mysqli_stmt_bind_param($stmt, 's', $file))
                    throw new Exception("mysql bind param");
            }

            if (!mysqli_stmt_execute($stmt))
                throw new InvalidArgumentException("mysql execute ".$stmt->error);
            $cnt_r = mysqli_stmt_affected_rows($stmt);
            if ($cnt_r == 0)
                throw new InvalidArgumentException("not modified");
            else if ($cnt_r != 1)
                throw new InvalidArgumentException("mysql insert");
            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            $description_value = $_POST[$description];
            $updated = true;
            if (!empty($_FILES[$picture]['tmp_name']))
                my_session_update_picture($file);
        }      
    } catch (Exception $ex) {
        $error_flag = true;
        $error_message = $ex->getMessage();
        if (strlen($error_message) >= 5 && substr($error_message, 0, 5) == "mysql")
            $error_message = "mysql";
    }
?>

<!doctype html>
<html lang="it">
<!-- HEAD -->
<head>
    <title>Aggiorna descrizione e immagine</title>
    
    <?php
        require("php/head_common.php");
    ?>
    
    <link rel="stylesheet" type="text/css" href="css/login.css">
        
    <!-- JS -->
    <script src="JS/inputChecks.js"></script>
    <script>
        "use strict"; //necessario per strict mode
        
        // creare un'array associativo "messaggio -> errore"           
        var err_array = {
                'description' : 'Descrizione non valida.',
                'picture' : 'Immagine non valida.'
        };
        <?php
            $tempError = ($error_flag) ? $error_message : "";
            echo 'var id_errore = "'.$tempError.'";';
        ?>
        /** ----- operazione di recupero dati se non validi ----- */
        function loadData( jQuery ) {
            <?php
            // ----- comunicare errore -----
            if ($tempError == "mysql")
                echo 'document.getElementById("userMessage").innerHTML = "<p style=\'color: red\'>Non sono riuscito a caricare i dati del profilo, si prega di riprovare.</p>"';
            else if ($tempError == "not modified") {
                echo 'document.getElementById("userMessage").innerHTML = "<p style=\'color: red\'>Non sono state modificate informazioni.</p>"';
                $tempError = "mysql";
            } else
                echo 'document.getElementById("'.$description.'").value="'.$description_value.'";';

            if ($error_flag && $tempError != "mysql") {
                if ($tempError == "args")
                    echo 'document.getElementById("userMessage").insertAdjacentHTML(\'afterbegin\', "<p style=\'color: red\'>Dati inseriti non validi.</p>")';
                else if ($tempError == "description")
                    echo 'document.getElementById("userMessage").insertAdjacentHTML(\'afterbegin\', "<p style=\'color: red\'>Controllare la descrizione.</p>")';
                else // picture
                    echo 'document.getElementById("userMessage").insertAdjacentHTML(\'afterbegin\', "<p style=\'color: red\'>Immagine non valida.</p>")';
            }
            else if ($updated)
                echo 'document.getElementById("userMessage").insertAdjacentHTML(\'afterbegin\', "<p style=\'color: green\'>Aggiornamento riuscito!</p>")';
            ?>
        }
        <?php echo '$(document).ready(loadData);' ?>
        
    </script>
</head>

<!-- BODY con campi per aggiornamento profilo -->
<body>
    <!-- Navbar -->
    <?php include("php/navbar.php") ?>
    <!-- AGGIORNA PROFILO -->
	<div id="FirstBox" class="container">
	<div id="sigcon" class="form-group">
        <legend>Aggiorna le informazioni su di te</legend>
        <form enctype="multipart/form-data" name="editUser" id="editUser" class="form-in" method="POST" action="edit_aboutme.php">
            <!-- ^ enctype is necessary to encode picture correctly ^ -->
            <!-- div to show error message -->                
            <div id="userMessage">
            </div>
            <fieldset>
            <?php
                if ($tempError != "mysql") {
                    echo '
                            <!-- Descrizione -->
                            <div>
                                <label for="description">Descrizione: </label>&emsp;
                                <textarea id="description" placeholder="Racconta qualcosa di te!" class="form-control input-in" rows="6" name="description" minlength="10" maxlength="50000"></textarea>
                            </div>
                            <!-- Picture -->
                            <br/>
                            <div>
                            <!-- Upload picture -->
                                <label for="picture">Carica la tua immagine di profilo:</label>
                                <!-- This hidden field is used by php to avoid uploading large files.
                                Files lager than 4MB are not blocked by this, but upload stops at 4M
                                and the file is not sent, thus preventing user from waiting for a file
                                that will be rejected server-side.-->
                                <input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
                                <input type="file" name="picture" id="picture" accept="image/png, image/jpeg, image/jpg, image/bmp"  onchange="checkPicture()">
                            </div>
                    ';
                }
                echo '<div class="btn-container"> ';
                if ($tempError != "mysql")
                        echo '<input type="submit" id="submit" class="btn btn-primary" value="Modifica!">';
                echo ' <a href="profile.php" class="btn btn-danger" role="button">Indietro</a>
                       </div>
                     ';
            ?>
            </fieldset>    
        </form>
    </div>
    </div>
</body>
</html>