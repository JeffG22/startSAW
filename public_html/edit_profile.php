<?php
    require_once("php/domain_constraints.php");
    require_once("php/utilities.php");
    require_once("php/handlesession.php");
    require_once("php/data.php");
    require_once("../confidential_info.php");
    require_once("../connection.php");

    my_session_start();
    if (!my_session_is_valid()) // Se un utente non è registrato --> redirect to index.php
        navigateTo("Location: index.php");
    // Se un utente è registrato --> ok

    // ----- CONTROLLI LATO SERVER su INPUT RICEVUTI -----
    $error_flag = false;
    $updated = false;
    try {
        // ----- recupero dati utente -----
        $person = ($_SESSION["type"] == "person");
        $query1 = "SELECT * FROM ".$_SESSION['type']." WHERE id=".$_SESSION['userId'];
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
        // if person row(id, name, surname, birthdate, gender, phone, province)
        if ($person) {
            $surname_value = $row["surname"];
            $birthdate_value = $row["birthdate"];
            $gender_value = $row["gender"];
        }
        // if organization row(id, name, phone, province, sector, website)
        else {
            $sector_value = $row["sector"];
            $website_value = $row["website"];
        }
        $name_value = $row["name"];
        $phone_value = $row["phone"];
        $province_value = $row["province"];

        $nome = "nome"; // nome
        $tel = "tel"; // telefono
        $cognome = "cognome"; // cognome
        $data = "data"; // data
        $sex = "genere"; // genere
        $pr = "provincia"; // provincia
        $sett = "settore"; // settore
        $sito = "sito"; // sito
      
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // ----- controllo dati ------    
            if (!empty($_POST[$tel]) && !checksOnTel($_POST[$tel])) // due opzioni perchè non required
                throw new InvalidArgumentException($tel);
            if (empty($_POST[$nome]) || !checksOnName($_POST[$nome]))
                throw new InvalidArgumentException($nome);
            if ($person && (empty($_POST[$cognome]) || !checksOnSurname($_POST[$cognome])))
                throw new InvalidArgumentException($cognome);
            if ($person && (empty($_POST[$data]) || !checksOnDate($_POST[$data])))
                throw new InvalidArgumentException($data);
            if ($person && (empty($_POST[$sex]) || ($_POST[$sex] != "F" && $_POST[$sex] != "M" && $_POST[$sex] != "-")))
                throw new InvalidArgumentException($sex);
            if (empty($_POST[$pr]) || !checksOnProv($_POST[$pr]))
                throw new InvalidArgumentException($pr);
            if (!$person && (empty($_POST[$sett]) || !checksOnSettore($_POST[$sett])))
                throw new InvalidArgumentException($sett);
            if (!$person && (!empty($_POST[$sito]) && !checksOnSite($_POST[$sito])))
                throw new InvalidArgumentException($sito);
            
        // ----- sanitizzazione input -----           
            if ($person) {
            // person: (name, surname, gender, birthdate, province, phone)
                $_POST[$cognome] = sanitize_inputString($_POST[$cognome]);
                $_POST[$sex] = sanitize_inputString($_POST[$sex]);
                $_POST[$data] = sanitize_inputString($_POST[$data]);
            }
            else {
            // organization: (name, province, sector, website, phone)
                $_POST[$sett] = sanitize_inputString($_POST[$sett]);
                if (!empty($_POST[$sito]))
                    $_POST[$sito] = sanitize_url($_POST[$sito]);
                else
                    $_POST[$sito] = null;
            }
            $_POST[$nome] = sanitize_inputString($_POST[$nome]);
            $_POST[$pr] = sanitize_inputString($_POST[$pr]);
            if (!empty($_POST[$tel]))
                    $_POST[$tel] = sanitize_inputString($_POST[$tel]);
                else
                    $_POST[$tel] = null;
        
        // ----- inserimento nel DB se rispetta vincoli -----
            if (!($conn = dbConnect()))
                throw new Exception("mysql ".mysqli_connect_error());
            if ($person) {
                $query2 = "UPDATE person SET name=?, surname=?, gender=?, birthdate=?, province=?, phone=? WHERE id=".$_SESSION['userId'];
                if (!($stmt = mysqli_prepare($conn, $query2)))
                    throw new Exception("mysql ".$conn->error);
                if (!mysqli_stmt_bind_param($stmt, 'ssssss', 
                        $_POST[$nome], $_POST[$cognome], $_POST[$sex], $_POST[$data], $_POST[$pr], $_POST[$tel]))
                    throw new Exception("mysql bind param");
            }
            else {
                $query2 = "UPDATE organization SET name=?, province=?, sector=?, website=?, phone=? WHERE id=".$_SESSION['userId'];
                    if (!($stmt = mysqli_prepare($conn, $query2)))
                        throw new Exception("mysql prepare ".$conn->error);
                    if (!mysqli_stmt_bind_param($stmt, 'sssss', 
                            $_POST[$nome], $_POST[$pr], $_POST[$sett], $_POST[$sito], $_POST[$tel]))                           
                        throw new Exception("mysql param");
            }
            if (!mysqli_stmt_execute($stmt))
                throw new InvalidArgumentException("mysql execute ".$stmt->error);
            if (mysqli_stmt_affected_rows($stmt) != 1)
                throw new InvalidArgumentException("mysql insert");
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            if ($person) {
                $surname_value = $_POST[$cognome];
                $birthdate_value = $_POST[$data];
                $gender_value = $_POST[$sex];
            }
            else {
                $sector_value = $_POST[$sett];
                $website_value = $_POST[$sito];
            }
            $name_value = $_POST[$nome];
            $phone_value = $_POST[$tel];
            $province_value = $_POST[$pr];

            $updated = true;
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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit profile</title>
    
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

	<!--CSS-->
	<link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
        
    <!-- SCRIPT -->
    <!-- Google ReCaptcha -->    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- JS -->
    <script>
        "use strict"; //necessario per strict mode
        
        // creare un'array associativo "messaggio -> errore"           
        var err_array = {
                'tel' : 'Telefono inserito non valido.',
                'nome' : 'Nome non valido.',
                'cognome' : 'Cognome non valido.',
                'data' : 'Data non valida.',
                'genere' : 'Selezionare un genere..',
                'provincia' : 'Provincia scelta non valida.',
                'settore' : 'Settore non valido.',
                'sito' : 'Sito inserito non valido.'
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
            else {
                if ($_SESSION["type"] == "person") {
                    echo 'document.getElementById("'.$cognome.'").value="'.$surname_value.'";';
                    echo 'document.getElementById("'.$data.'").value="'.$birthdate_value.'";';
                    echo 'document.getElementById("'.$sex.'").value="'.$gender_value.'";';
                }
                else {
                    echo 'document.getElementById("'.$sett.'").value="'.$sector_value.'";';
                    echo 'document.getElementById("'.$sito.'").value="'.$website_value.'";';                
                }
                echo 'document.getElementById("'.$nome.'").value="'.$name_value.'";';
                echo 'document.getElementById("'.$pr.'").value="'.$province_value.'";';
                echo 'document.getElementById("'.$tel.'").value="'.$phone_value.'";';
            }
            if ($error_flag && $tempError != "mysql") {
                echo '
                        var field = document.getElementById(id_errore);
                        field.setCustomValidity(err_array[id_errore]); // fa apparire la finestrella di html 5 con la scritta che comunica errore
                        field.setAttribute("onclick", "this.setCustomValidity(\'\');");
                        field.setAttribute("onchange", "this.setCustomValidity(\'\');");         
                        field.style.color = "red";
                        field.style.border = "2px solid red";
                        field.style.borderRadius = "4px";
                        document.getElementById("submit").click(); // show the validity box
                ';
            }
            else if ($updated)
                echo 'document.getElementById("userMessage").insertAdjacentHTML(\'afterbegin\', "<p style=\'color: green\'>Aggiornamento del profilo riuscito!</p>")';
            ?>
        }
        <?php echo '$(document).ready(loadData);' ?>
        
    </script>
</head>

<!-- BODY con campi per modifica profilo -->
<body>
    <!-- Navbar -->
    <?php include("php/navbar.php") ?>
    <!-- MODIFICA PROFILO -->
	<div id="FirstBox" class="container">
	<div id="sigcon" class="form-group">
        <legend>Modifica Profilo</legend>
        <form name="editUser" id="editUser" class="form-in" method="POST" action="edit_profile.php">
            <!-- div to show error message -->                
            <div id="userMessage">
                <p>Aggiornare i campi che si intende modificare</p>
                <br/>
            </div>
            <fieldset>
            <?php
                if ($_SESSION["type"] == "person" && $tempError != "mysql") {
                    echo '
                        <div id="campiPerson">
                            <!-- nome -->
                            <div>
                                <label for="nome">Nome: </label>&emsp;
                                <input type="text" id="nome" name="nome" class="form-control input-in" minlength="3" maxlength="50" required>
                            </div>
                            <!-- cognome -->
                            <div>
                                <label for="cognome">Cognome: </label>&emsp;
                                <input type="text" id="cognome" name="cognome" class="form-control input-in" maxlength="50" required>
                            </div>
                            <!-- data di nascita -->
                            <div>
                                <label for="data">Data di nascita: </label>&emsp;
                                <input type="date" id="data" name="data" class="form-control input-in" min="1900-01-01" max="2006-12-31" required>
                            </div>
                            <!-- Sesso -->
                            <div>
                                <label for="genere">Sesso: </label>&emsp;
                                <select id="genere" name="genere" class="form-control input-in" required>
                                    <option value="-">Non specificato</option>
                                    <option value="F">F</option>
                                    <option value="M">M</option>
                                </select>
                            </div>
                            <div>
                            <!-- Provincia --> 
                                <label for="provincia">Provincia: </label>&emsp;
                                <select id="provincia" name="provincia" class="form-control input-in" required>';
                    show_province();
                    echo '
                            </select>
                            </div>
                            <!-- telefono -->
                            <div>
                                <label for="tel">Telefono: </label>&emsp;
                                <input type="tel" id="tel" class="form-control input-in" name="tel" pattern="[0-9]{3,15}" maxlength="15" minlength="3">
                            </div>
                        </div>
                        <div class="btn-container">
                        <input type="submit" id="submit" class="btn btn-primary" value="Modifica!">
                        </div>
                    ';
                }
                else if ($tempError != "mysql") {
                    echo '
                        <div id="campiOrganization">
                            <!-- nome -->
                            <div>
                                <label for="nome">Nome: </label>&emsp;
                                <input type="text" id="nome" class="form-control input-in" name="nome" minlength="3" maxlength="50">
                            </div>
                            <div>
                            <!-- Provincia --> 
                                <label for="provincia">Provincia delle sede: </label>&emsp;
                                <select id="provincia" name="provincia" class="form-control input-in">
                        ';
                    show_province();
                    echo'
                            </select>
                            </div>
                            <!-- settore -->
                            <div>
                                <label for="settore">Settore in cui opera: </label>&emsp;
                                <input type="text" id="settore" name="settore" class="form-control input-in" maxlength="35">
                            </div>
                            <!-- sito, non è required -->
                            <div>
                                <label for="sito">Sito web: </label>&emsp;
                                <input type="url" id="sito" class="form-control input-in" name="sito" maxlength="63">
                            </div>
                            <!-- telefono -->
                            <div>
                                <label for="tel">Telefono: </label>&emsp;
                                <input type="tel"id="tel" class="form-control input-in" name="tel" pattern="[0-9]{3,15}" maxlength="15" minlength="3">
                            </div>
                        </div>
                        <div class="btn-container">
                        <input type="submit" id="submit" class="btn btn-primary" value="Modifica!">
                        </div>
                    ';
                }
            ?>
            </fieldset>    
        </form>
    </div>
    </div>
</body>
</html>