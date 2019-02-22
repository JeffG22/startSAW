<?php
    require_once("php/domain_constraints.php");
    require_once("php/utilities.php");
    require_once("php/handlesession.php");
    require_once("php/data.php");
    require_once("../confidential_info.php");

    my_session_start();
    if (my_session_is_valid()) // Se un utente è già registrato e atterra su questa pagina --> redirect to index.php
        header("Location: index.php");
    // Se un utente non è registrato e atterra su questa pagina --> ok

    // ----- CONTROLLI LATO SERVER su INPUT RICEVUTI -----
    $error_flag = false;
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['g-recaptcha-response'])) {
        //print_r($_POST);
        // 3 ----- determina se l'utente è persona -----
        $tipoUtente = "tipoUtente";
        if (empty($_POST[$tipoUtente]) || !checksOnTipoUtente($_POST[$tipoUtente]))
            throw new InvalidArgumentException($tipoUtente);
        $person = ($_POST[$tipoUtente] == "person") ? true : false;
        $privacy = "privacy"; // privacy concessa
        $email = "email"; // email nomeutente
        $password = "password"; // password
        $telefono = "telefono"; // telefono
        $nome = ($person) ? "nomeV" : "nomeA"; // nome v o a
        $cognome = "cognome"; // cognome
        $data = "data"; // data
        $sex = "genere"; // genere
        $pr = ($person) ? "provinciaV" : "provinciaA"; // provincia v o a
        $sett = "settore"; // settore
        $sito = "sito"; // sito
        
        // 1 ----- controllo captcha -----
            //----- dati per richiesta -----
            $client_response = $_POST['g-recaptcha-response'];
            $secretToken=$storedSecretToken;
            $url = "https://www.google.com/recaptcha/api/siteverify";
            //----- preparazione richiesta API in POST tramite CURL -----
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1); // set post data to true
            curl_setopt($curl, CURLOPT_POSTFIELDS, "secret=".$secretToken."&response=".$client_response);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $json_token = curl_exec($curl);
            curl_close($curl);
            //echo $json_token;
            $json_decoded = json_decode($json_token);
            
            //----- decodifica del pacchetto json ricevuto -----
            // struttura: { "success": false | true, "error-codes": [ "..." ] }
            $success = $json_decoded->{'success'};
            if (!$success) {
                $code = $json_decoded->{'error-codes'};
                throw new InvalidArgumentException("captcha");
            }
        // 2 ----- controllo dati utente ------
            
            if (empty($_POST[$privacy][0]) || $_POST[$privacy][0] != "Y")
                throw new InvalidArgumentException($privacy);
            if (empty($_POST[$email]) || !checksOnEmail($_POST[$email]))
                throw new InvalidArgumentException($email);
            if (empty($_POST[$password]) || !checksOnPswd($_POST[$password]))
                throw new InvalidArgumentException($password);
            if (!empty($_POST[$telefono]) && !checksOnTel($_POST[$telefono])) // due opzioni perchè non required
                throw new InvalidArgumentException($telefono);            

        // 4 ----- controlli sui campiV o campiA -----
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
            
        // 5 ----- sanitizzazione input -----
            $fields_utente; // array che conterrà i campi di utente
            $fields_utente[0] = sanitize_email($_POST[$email]);
            $fields_utente[1] = password_hash($_POST[$password], PASSWORD_DEFAULT); // hashing pswd
            $fields_utente[2] = ($person) ? "person" : "organization";
           
            $fields_value; // array che conterrà i campi person / organization
            if ($person) {
            // person: (id, name, surname, gender, birthdate, township, province, phone)
                $fields_value[0] = sanitize_inputString($_POST[$nome]);
                $fields_value[1] = sanitize_inputString($_POST[$cognome]);
                $fields_value[2] = sanitize_inputString($_POST[$sex]);
                $fields_value[3] = sanitize_inputString($_POST[$data]);
                $fields_value[4] = sanitize_inputString($_POST[$pr]);
                if (!empty($_POST[$telefono]))
                    $fields_value[5] = sanitize_inputString($_POST[$telefono]);
                else
                    $fields_value[5] = null;
            }
            else {
            // organization: (name, headquarter, province, sector, website, phone)
                $fields_value[0] = sanitize_inputString($_POST[$nome]);
                $fields_value[1] = sanitize_inputString($_POST[$pr]);
                $fields_value[2] = sanitize_inputString($_POST[$sett]);
                if (!empty($_POST[$sito]))
                    $fields_value[3] = sanitize_url($_POST[$sito]);
                else
                    $fields_value[3] = null;
                if (!empty($_POST[$telefono]))
                    $fields_value[4] = sanitize_inputString($_POST[$telefono]);
                else
                    $fields_value[4] = null;
            }
        
        // 6 ----- inserimento nel DB se rispetta vincoli -----
            require_once("../connection.php");
            if (!($conn = dbConnect()))
                throw new Exception("sql ".mysqli_connect_error());
            $query1 = "INSERT INTO user (email, passwd, type) VALUES (?, ?, ?)"; 
            
        // ----- inizio transazione e prima query in Utente ------
            if (!mysqli_begin_transaction($conn))
                throw new Exception("sql transaction".mysqli_connect_error($conn));
            if (!mysqli_autocommit($conn, FALSE))
                throw new Exception("sql transaction".mysqli_connect_error($conn));
            $insert1 = false;
            if (!($stmt = mysqli_prepare($conn, $query1)))
                throw new Exception("mysqli prepare".mysqli_error($conn));
            if (!mysqli_stmt_bind_param($stmt, 'sss', $fields_utente[0], $fields_utente[1], $fields_utente[2]))
                throw new Exception("mysqli bind param");
            if (!mysqli_stmt_execute($stmt)) {
                if (mysqli_errno($conn) == 1062) // DUPLICATE PRIMARY KEY
                    throw new InvalidArgumentException($email."sql");
                throw new InvalidArgumentException("mysqli execute".$stmt->error);
            }
            if (mysqli_stmt_affected_rows($stmt) == 1) { // unico utente inserito
                $insert1 = true;
                $idUtente = mysqli_insert_id($conn); // id dell'ultima t-upla inserita
            }
            else
                throw new InvalidArgumentException("sql insert");
            mysqli_stmt_close($stmt);
            // ----- seconda query ------
            $insert2 = false;
            if ($person) {
                $query2 = "INSERT INTO person (id, name, surname, gender, birthdate, province, phone) VALUES (?, ?, ?, ?, ?, ?, ?)";
                if (!($stmt = mysqli_prepare($conn, $query2))) {
                    mysqli_rollback($conn);
                    throw new Exception("mysqli prepare".$conn->error);
                }
                if (!mysqli_stmt_bind_param($stmt, 'issssss', 
                        $idUtente, $fields_value[0], $fields_value[1], $fields_value[2], $fields_value[3], 
                        $fields_value[4], $fields_value[5])) {
                    mysqli_rollback($conn);
                    throw new Exception("mysqli bind param");
                }
            }
            else {
                $query2 = "INSERT INTO organization (id, name, province, sector, website, phone) VALUES (?, ?, ?, ?, ?, ?)";
                    if (!($stmt = mysqli_prepare($conn, $query2))) {
                        mysqli_rollback($conn);
                        throw new Exception("mysqli prepare ".$conn->error);
                    }
                    if (!mysqli_stmt_bind_param($stmt, 'isssss', 
                            $idUtente, $fields_value[0], $fields_value[1], $fields_value[2], 
                            $fields_value[3], $fields_value[4])) {
                        mysqli_rollback($conn);                            
                        throw new Exception("mysqli param");
                    }
            }
            if (!mysqli_stmt_execute($stmt)) {
                mysqli_rollback($conn);
                throw new InvalidArgumentException("mysqli execute ".$stmt->error);
            }
            if (mysqli_stmt_affected_rows($stmt) == 1) // unica P/A inserita
                $insert2 = true;
            else {
                mysqli_rollback($conn);
                throw new InvalidArgumentException("sql insert");
            }
            if (!mysqli_commit($conn))
                throw new Exception("transaction failed");
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            // 7 ----- impostazione sessione e login automatico ----
            my_session_login($idUtente, $person);
            header("Location: index.php"); //TODO change to personal page
        }      
    } catch (Exception $ex) {
        $error_flag = true;
        $error_message = $ex->getMessage();
        //echo $ex->getMessage();
        //echo $error_flag;
    }
?>

<!doctype html>
<html lang="it">
<!-- HEAD -->
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- What this tag suggests
        1. Do NOT use large fixed width elements - For example, if an image is displayed at a width wider than the viewport it can cause the viewport to scroll horizontally. Remember to adjust this content to fit within the width of the viewport.
        2. Do NOT let the content rely on a particular viewport width to render well - Since screen dimensions and width in CSS pixels vary widely between devices, content should not rely on a particular viewport width to render well.
        3. Use CSS media queries to apply different styling for small and large screens - Setting large absolute CSS widths for page elements will cause the element to be too wide for the viewport on a smaller device. Instead, consider using relative width values, such as width: 100%. Also, be careful of using large absolute positioning values. It may cause the element to fall outside the viewport on small devices.
    -->
    <title>Sign up</title>
    
    <!--Boostrap-->
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
    <!-- JQuery -->    
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <!-- Google ReCaptcha -->    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- JS -->
    <script>
        "use strict"; //necessario per strict mode
        function showSecondBox() {
            var selectedRadioValue = document.querySelector('input[name=tipoUtente]:checked').value; // selezione persona oppure associazione
            var boxHidden, boxShowed;
            var htmlLegend = $("#legendaTipoInput"); // comunicare inserimento persona oppure azienda
            var campiV = $(".campiV"); // campi volontario
            var campiA = $(".campiA"); // campi associazione

            if (selectedRadioValue == "organization") { // si vuole inserire un'associazione
                boxHidden = $("#campiPerson");
                boxShowed = $("#campiOrganization");
                htmlLegend.html("Dati associazione");
                campiV.prop('required',false);
                campiA.prop('required',true);
            }
            else if (selectedRadioValue == "person") { // si vuole inserire una persona
                boxHidden = $("#campiOrganization");
                boxShowed = $("#campiPerson");
                htmlLegend.html("Dati volontario");
                campiV.prop('required',true);
                campiA.prop('required',false);
            }
            boxHidden.hide()
            boxShowed.show();
        }
        
        // creare un'array associativo "messaggio -> errore"           
        var err_array = {
                'captcha' : 'Captcha non verificato, riprovare per cortesia.',
                'privacy' : 'Selezionare la casella per il consenso alla privacy.',
                'email' : 'Email non valida, riprovare per cortesia.',
                'password' : 'Password non valida, lunghezza minima 6 caratteri.',
                'telefono' : 'Telefono inserito non valido.',
                'nomeV' : 'Nome non valido.',
                'nomeA' : 'Nome non valido.',
                'cognome' : 'Cognome non valido.',
                'data' : 'Data non valida.',
                'genere' : 'Selezionare un genere..',
                'provinciaV' : 'Provincia scelta non valida.',
                'provinciaA' : 'Provincia scelta non valida.',
                'settore' : 'Settore non valido.',
                'sito' : 'Sito inserito non valido.',
                'emailsql' : 'L\'email inserita è già registrata!'
        };
        <?php
            $tempError = ($error_flag) ? $error_message : "";
            echo 'var id_errore = "'.$tempError.'";';
        ?>
        /** ----- operazione di recupero dati se non validi ----- */
        function loadPostData( jQuery ) {
            // ----- ricaricare dati inviati non validi -----
            <?php
                if ($error_flag && $error_message != "TipoUtente") {
                    echo 'document.getElementById("'.$email.'").value="'.$_POST[$email].'";';
                    echo 'document.getElementById("'.$telefono.'").value="'.$_POST[$telefono].'";';
                    echo 'document.getElementById("'.$nome.'").value="'.$_POST[$nome].'";';
                    echo 'document.getElementById("'.$cognome.'").value="'.$_POST[$cognome].'";';
                    echo 'document.getElementById("'.$data.'").value="'.$_POST[$data].'";';
                    echo 'document.getElementById("'.$sex.'").value="'.$_POST[$sex].'";';
                    echo 'document.getElementById("'.$pr.'").value="'.$_POST[$pr].'";';
                    echo 'document.getElementById("'.$sett.'").value="'.$_POST[$sett].'";';
                    echo 'document.getElementById("'.$sito.'").value="'.$_POST[$sito].'";';
                    if (isset($person) && $person)
                        echo 'document.getElementById("persona").checked = true;';
                    else
                        echo 'document.getElementById("associazione").checked = true;';
                    echo 'showSecondBox();';
                }
            ?>

            for (var key in err_array) {
                if (key == id_errore) {
                    if (key == "emailsql") 
                        var field = document.getElementById("email");
                    else 
                        var field = document.getElementById(key);
                    if (key == "captcha") {
                        field.insertAdjacentHTML( 'beforeend', "<p style='color: red'> Captcha non valido! </p>");
                    }
                    else {
                        alert(key);
                        field.setCustomValidity(err_array[key]); // fa apparire la finestrella di html 5 con la scritta che comunica errore
                        field.setAttribute("isvalid", "false");
                        field.setAttribute("onchange", "this.setCustomValidity(''); this.focus();");         
                        field.style.color = "red";
                        field.focus();                        
                    }
                    break;
                }
            }
        
        }
        <?php
            if ($error_flag) // se errore allora comunica all'utente ciò quando la pagina è ricaricata (funzione jquery)
                echo '$(document).ready(loadPostData);'
        ?>
        
    </script>
</head>

<!-- BODY con campi per registrazione -->
<body>
    <!-- Navbar -->
    <?php include("php/navbar.php") ?>

    <!-- REGISTRAZIONE -->
	<div id="FirstBox" class="container">
	<div id="sigcon" class="form-group">
            <legend>Registrazione</legend>
            <form name="registration" id="registration" class="form-in" method="POST" action="registration_form.php">
                <div>
                    <!-- tipo utente -->
                    <p>Registrati come:</p>
                        <label class="radio-inline" for="persona">Persona
                            <input type="radio" id="persona" class="form-control input-in" name="tipoUtente" value="person" onchange="showSecondBox();" checked required>
                        </label>
                        <label class="radio-inline" for="associazione">Associazione
                            <input type="radio" id="associazione" class="form-control input-in" name="tipoUtente" value="organization" onchange="showSecondBox();">
                        </label>
                </div>
                <div id="campiUser">
                    <!-- email -->
                    <div>
                        <label for="email">Email: </label>&emsp;
                        <input type="email" id="email" class="form-control input-in" name="email" minlength="6"  maxlength="254" placeholder="name@domain.net" autocomplete="on" required>
                    </div>
                    <!-- password -->
                    <div>
                        <label for="password">Password: </label>&emsp;
                        <input autofocus type="password" id="password" class="form-control input-in" name="password" minlength="6" maxlength="31" placeholder="6 characters minimum" autocomplete="on" required>
                    </div>
                    <!-- telefono -->
                    <div>
                        <label for="telefono">Telefono: </label>&emsp;
                        <input type="tel" id="telefono" class="form-control input-in" name="telefono" pattern="[0-9]{3,15}" maxlength="15" minlength="3">
                    </div>
                </div>
                <br/>
                <fieldset class="box" id="SecondBox">
                <legend id="legendaTipoInput">Dati volontario</legend> <!-- OR Dati Associazione !-->
                <!-- i campi di associazione non hanno attributo required in static time !-->
                <!-- Registrazione Volontario !-->
                    <div id="campiPerson">
                        <!-- nomeV -->
                        <div>
                            <label for="nomeV">Nome: </label>&emsp;
                            <input type="text" id="nomeV" name="nomeV" class="campiV form-control input-in" minlength="3" maxlength="50" required>
                        </div>
                        <!-- cognome -->
                        <div>
                            <label for="cognome">Cognome: </label>&emsp;
                            <input type="text" id="cognome" name="cognome" class="campiV form-control input-in" maxlength="50" required>
                        </div>
                        <!-- data di nascita -->
                        <div>
                            <label for="data">Data di nascita: </label>&emsp;
                            <input type="date" id="data" name="data" class="campiV form-control input-in" min="1900-01-01" max="2006-12-31" required>
                        </div>
                        <!-- Sesso -->
                        <div>
                            <label for="genere">Sesso: </label>&emsp;
                            <select id="genere" name="genere" class="campiV form-control input-in" required>
                                <option value="" selected>Selezionare</option>
                                <option value="-">Non specificato</option>
                                <option value="F">F</option>
                                <option value="M">M</option>
                            </select>
                        </div>
                        <div>
                        <!-- Provincia --> 
                            <label for="provinciaV">Provincia: </label>&emsp;
                            <select id="provinciaV" name="provinciaV" class="campiV form-control input-in" required>
                            <option value="" selected>--</option>
                            <?php show_province(); ?>
                            </select>
                        </div>
                    </div>
                    <!-- Registrazione Associazione !-->
                    <div id="campiOrganization" style="display: none;">
                        <!-- nomeA -->
                        <div>
                            <label for="nomeA">Nome: </label>&emsp;
                            <input type="text" id="nomeA" class="campiA form-control input-in" name="nomeA" minlength="3" maxlength="50">
                        </div>
                        <div>
                        <!-- Provincia --> 
                            <label for="provinciaA">Provincia delle sede: </label>&emsp;
                            <select id="provinciaA" name="provinciaA" class="campiA form-control input-in">
                            <option value="" selected>--</option>
                            <?php show_province(); ?>
                            </select>
                        </div>
                        <!-- settore -->
                        <div>
                            <label for="settore">Settore in cui opera: </label>&emsp;
                            <input type="text" id="settore" name="settore" class="campiA form-control input-in" maxlength="35">
                        </div>
                        <!-- sito, non è required -->
                        <div>
                            <label for="sito">Sito web: </label>&emsp;
                            <input type="url" id="sito" class="form-control input-in" name="sito" maxlength="63">
                        </div>
                    </div>
                </fieldset>    
                <br/>
                <!-- CONTROLLI PRIVACY e CAPTCHA -->
                <div id="controlli">
                    
                    <label id="privacy-label" class="container" for="privacy">
                        <input type="checkbox" id="privacy" name="privacy" value="Y" required>
                        <div id="blurb">Do il consenso al trattamento dei dati nelle modalità conformi al D. Lgs. 30 giugno 2003, n. 196 e successivi aggiornamenti.</div>
                    </label>
                    <div class=captcha-box>
                        <div class="g-recaptcha" role="captcha" data-sitekey="6LdTc5AUAAAAAAJBUM9xlw-zpEf9o__oypShRBCv"></div>
                    </div>
                </div>
                <div class="btn-container">
                    <input type="submit" class="btn btn-primary" value="Registrami">
                </div>
            </form>
    </fieldset>
</body>
</html>