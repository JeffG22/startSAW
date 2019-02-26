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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            $pswdOld = "pswdOld"; // last pswd
            $pswdNew1 = "pswdNew1"; // new one
            $pswdNew2 = "pswdNew2"; // new one


        // ----- controllo dati ------    
            if (empty($_POST[$pswdOld]) || !checksOnPswd($_POST[$pswdOld]))
                throw new InvalidArgumentException($pswdOld);
            if (empty($_POST[$pswdNew1]) || !checksOnPswd($_POST[$pswdNew1]))
                throw new InvalidArgumentException($pswdNew1);
            if (empty($_POST[$pswdNew2]) || $_POST[$pswdNew1] != $_POST[$pswdNew2])
                throw new InvalidArgumentException($pswdNew1);
            
        // ----- sanitizzazione input -----           
            $_POST[$pswdNew1] = password_hash($_POST[$pswdNew1], PASSWORD_DEFAULT); // hashing pswd

        // ----- recupero pswd -----
            $query1 = "SELECT passwd FROM user WHERE user_id=".$_SESSION['userId'];
            if (!($conn = dbConnect()))
                throw new Exception("mysql ".mysqli_connect_error());
            if (!($res = mysqli_query($conn, $query1)))
                throw new Exception("mysql ".mysqli_error($conn));
            if (!($row = mysqli_fetch_assoc($res)))
                throw new InvalidArgumentException("mysql");
            if (mysqli_num_rows($res) != 1) // not match
                throw new InvalidArgumentException("mysql");
            mysqli_close($conn);

            if(!password_verify($_POST[$pswdOld], $row['passwd']))
                throw new InvalidArgumentException($pswdOld);

        // ----- aggiornamento DB se rispetta vincoli -----
            if (!($conn = dbConnect()))
                throw new Exception("mysql ".mysqli_connect_error());
            $query2 = "UPDATE user SET passwd=? WHERE user_id=".$_SESSION['userId'];
            if (!($stmt = mysqli_prepare($conn, $query2)))
                throw new Exception("mysql ".$conn->error);
            if (!mysqli_stmt_bind_param($stmt, 's', $_POST[$pswdNew1]))
                throw new Exception("mysql bind param");
            if (!mysqli_stmt_execute($stmt))
                throw new InvalidArgumentException("mysql execute ".$stmt->error);
            if (mysqli_stmt_affected_rows($stmt) != 1)
                throw new InvalidArgumentException("mysql insert");
            $updated = true;
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } catch (Exception $ex) {
            $error_flag = true;
            $error_message = $ex->getMessage();
            if (strlen($error_message) >= 5 && substr($error_message, 0, 5) == "mysql")
                $error_message = "mysql";
        }
    }
?>

<!doctype html>
<html lang="it">
<!-- HEAD -->
<head>
    <title>Modifica password</title>
    
    <?php
        require("php/head_common.php");
    ?>
    
    <link rel="stylesheet" type="text/css" href="css/login.css">
        
    <!-- SCRIPT -->
    <!-- JS -->
    <script>
        "use strict"; //necessario per strict mode
        
        // creare un'array associativo "messaggio -> errore"           
        var err_array = {
                'pswdOld' : 'Password attuale errata.',
                'pswdNew1' : 'Password nuova non valida o non corrispondente.'
        };
        <?php
            $tempError = ($error_flag) ? $error_message : "";
            echo 'var id_errore = "'.$tempError.'";';
        ?>
        /** ----- operazione di recupero dati se non validi ----- */
        function loadData( jQuery ) {
            <?php
            // ----- caricare dati utente -----
            if ($tempError == "mysql")
                echo 'document.getElementById("userMessage").innerHTML = "<p style=\'color: red\'>Non sono riuscito a caricare i dati del profilo, si prega di riprovare.</p>"';
            else if ($error_flag) {
                echo 'document.getElementById("userMessage").innerHTML = "<p style=\'color: red\'>Password non modificata.</p>"';
                echo '
                    for (var key in err_array) {
                        if (key == id_errore) {
                            var field = document.getElementById(key);
                            field.setCustomValidity(err_array[key]); // fa apparire la finestrella di html 5 con la scritta che comunica errore
                            field.setAttribute("onclick", "this.setCustomValidity(\'\');");
                            field.setAttribute("onchange", "this.setCustomValidity(\'\');");         
                            field.style.color = "red";
                            field.style.border = "2px solid red";
                            field.style.borderRadius = "4px";
                            document.getElementById("submit").click(); // show the validity box
                            break;              
                    }
                }
                ';
            }
            else if ($updated)
                echo 'document.getElementById("userMessage").insertAdjacentHTML(\'afterbegin\', "<p style=\'color: green\'>Password modificata con successo!</p>")';
            ?>
        }
        <?php echo '$(document).ready(loadData)'; ?>
        
    </script>
</head>

<!-- BODY con campi per modifica pswd -->
<body>
    <!-- Navbar -->
    <?php include("php/navbar.php") ?>
    <!-- MODIFICA PASSWORD -->
	<div id="FirstBox" class="container">
	<div id="sigcon" class="form-group">
        <legend>Modifica della password</legend>
        <form name="editPswd" id="editPswd" class="form-in" method="POST" action="edit_pswd.php">
            <!-- div to show error message -->                
            <div id="userMessage">
            </div>
            <fieldset>
                <div id="campiPswd">
                    <!-- password precedente -->
                    <div>
                        <label for="pswdOld">Password attuale:</label>
                        <input type="password" name="pswdOld" id="pswdOld" class="form-control input-in" maxlength="31" autocomplete="on">
                    </div>
                    <!-- password nuova -->
                    <div>
                        <label for="pswdNew1">Password nuova: </label>&emsp;
                        <input type="password" id="pswdNew1" class="form-control input-in" name="pswdNew1" minlength="6" maxlength="31" placeholder="6 characters minimum" autocomplete="on" required>
                    </div>
                    <div>
                        <label for="pswdNew2">Conferma password: </label>&emsp;
                        <input type="password" id="pswdNew2" class="form-control input-in" name="pswdNew2" minlength="6" maxlength="31" placeholder="6 characters minimum" autocomplete="on" required>
                    </div>
                </div>
                <div class="btn-container">
                    <input type="submit" id="submit" class="btn btn-primary" value="Modifica!">
                    <a href="profile.php"><button type="button" class="btn btn-danger" value="Indietro">Indietro</button></a>
                </div>

            </fieldset>    
        </form>
    </div>
    </div>
</body>
</html>