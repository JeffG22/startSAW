<?php
    require_once("php/domain_constraints.php");
    require_once("php/utilities.php");
    require_once("php/handlesession.php");
    require_once("php/data.php");
    require_once("../confidential_info.php");

    my_session_start();
    if (my_session_is_valid()) // Se un utente è già registrato e atterra su questa pagina --> redirect to index.php
        navigateTo("index.php");
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
                throw new Exception("mysql ".mysqli_connect_error());
            $query1 = "INSERT INTO user (email, passwd, type) VALUES (?, ?, ?)"; 
            
        // ----- inizio transazione e prima query in Utente ------
            if (!mysqli_begin_transaction($conn))
                throw new Exception("mysql transaction".mysqli_connect_error($conn));
            if (!mysqli_autocommit($conn, FALSE))
                throw new Exception("mysql transaction".mysqli_connect_error($conn));
            $insert1 = false;
            if (!($stmt = mysqli_prepare($conn, $query1)))
                throw new Exception("mysql prepare".mysqli_error($conn));
            if (!mysqli_stmt_bind_param($stmt, 'sss', $fields_utente[0], $fields_utente[1], $fields_utente[2]))
                throw new Exception("mysql bind param");
            if (!mysqli_stmt_execute($stmt)) {
                if (mysqli_errno($conn) == 1062) // DUPLICATE PRIMARY KEY
                    throw new InvalidArgumentException($email."sql");
                throw new InvalidArgumentException("mysql execute".$stmt->error);
            }
            if (mysqli_stmt_affected_rows($stmt) == 1) { // unico utente inserito
                $insert1 = true;
                $idUtente = mysqli_insert_id($conn); // id dell'ultima t-upla inserita
            }
            else
                throw new InvalidArgumentException("mysql insert");
            mysqli_stmt_close($stmt);
            // ----- seconda query ------
            $insert2 = false;
            if ($person) {
                $query2 = "INSERT INTO person (id, name, surname, gender, birthdate, province, phone) VALUES (?, ?, ?, ?, ?, ?, ?)";
                if (!($stmt = mysqli_prepare($conn, $query2))) {
                    mysqli_rollback($conn);
                    throw new Exception("mysql prepare".$conn->error);
                }
                if (!mysqli_stmt_bind_param($stmt, 'issssss', 
                        $idUtente, $fields_value[0], $fields_value[1], $fields_value[2], $fields_value[3], 
                        $fields_value[4], $fields_value[5])) {
                    mysqli_rollback($conn);
                    throw new Exception("mysql bind param");
                }
            }
            else {
                $query2 = "INSERT INTO organization (id, name, province, sector, website, phone) VALUES (?, ?, ?, ?, ?, ?)";
                    if (!($stmt = mysqli_prepare($conn, $query2))) {
                        mysqli_rollback($conn);
                        throw new Exception("mysql prepare ".$conn->error);
                    }
                    if (!mysqli_stmt_bind_param($stmt, 'isssss', 
                            $idUtente, $fields_value[0], $fields_value[1], $fields_value[2], 
                            $fields_value[3], $fields_value[4])) {
                        mysqli_rollback($conn);                            
                        throw new Exception("mysql param");
                    }
            }
            if (!mysqli_stmt_execute($stmt)) {
                mysqli_rollback($conn);
                throw new InvalidArgumentException("mysql execute ".$stmt->error);
            }
            if (mysqli_stmt_affected_rows($stmt) == 1) // unica P/A inserita
                $insert2 = true;
            else {
                mysqli_rollback($conn);
                throw new InvalidArgumentException("mysql insert");
            }
            if (!mysqli_commit($conn))
                throw new Exception("mysql transaction failed");
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            // 7 ----- impostazione sessione e login automatico ----
            $display_name = ($person) ? $fields_value[0]." ".$fields_value[1] : $fields_value[0];
            my_session_login($idUtente, $person, $display_name, "");
            navigateTo("profile.php");
        }      
    } catch (Exception $ex) {
        $error_flag = true;
        $error_message = $ex->getMessage();
        //echo $error_message;
        if (strlen($error_message) >= 5 && substr($error_message, 0, 5) == "mysql")
            $error_message = "mysql";
    }
?>

<!doctype html>
<html lang="it">
<!-- HEAD -->
<head>    
    <title>Registrati</title>
    
    <?php
        require("php/head_common.php");
    ?>

    <link rel="stylesheet" type="text/css" href="css/login.css">
    
    
    <!-- SCRIPT -->
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
                'emailsql' : 'L\'email inserita è già registrata!',
                'mysql' : 'Registrazione non riuscita, si prega di riprovare tra qualche minuto.'
        };
        <?php
            $tempError = ($error_flag) ? $error_message : "";
            echo 'var id_errore = "'.$tempError.'";';
        ?>
        /** ----- operazione di recupero dati se non validi ----- */
        function loadPostData( jQuery ) {
            // ----- ricaricare dati inviati non validi -----
            <?php
                if ($error_flag && $tempError != "tipoUtente") {
                    if (isset($_POST[$email]))
                        echo 'document.getElementById("'.$email.'").value="'.sanitize_email($_POST[$email]).'";';
                    if (isset($_POST[$telefono]))
                        echo 'document.getElementById("'.$telefono.'").value="'.sanitize_inputString($_POST[$telefono]).'";';
                    if (isset($_POST[$nome]))
                        echo 'document.getElementById("'.$nome.'").value="'.sanitize_inputString($_POST[$nome]).'";';
                    if (isset($_POST[$cognome]))
                        echo 'document.getElementById("'.$cognome.'").value="'.sanitize_inputString($_POST[$cognome]).'";';
                    if (isset($_POST[$data]))
                        echo 'document.getElementById("'.$data.'").value="'.sanitize_inputString($_POST[$data]).'";';
                    if (isset($_POST[$sex]))
                        echo 'document.getElementById("'.$sex.'").value="'.sanitize_inputString($_POST[$sex]).'";';
                    if (isset($_POST[$pr]))
                        echo 'document.getElementById("'.$pr.'").value="'.sanitize_inputString($_POST[$pr]).'";';
                    if (isset($_POST[$sett]))
                        echo 'document.getElementById("'.$sett.'").value="'.sanitize_inputString($_POST[$sett]).'";';
                    if (isset($_POST[$sito]))
                        echo 'document.getElementById("'.$sito.'").value="'.sanitize_inputString($_POST[$sito]).'";';
                    if (isset($person) && $person)
                        echo 'document.getElementById("persona").checked = true;';
                    else
                        echo 'document.getElementById("associazione").checked = true;';
                    echo 'showSecondBox();';
                }
            ?>

            
            if (id_errore == "emailsql") 
                var field = document.getElementById("email");
            else if (id_errore == "mysql")
                var field = document.getElementById("userMessage");                
            else 
                var field = document.getElementById(id_errore);
            if (id_errore == "captcha")
                field.insertAdjacentHTML( 'beforeend', "<p style='color: red'> Captcha non valido! </p>");
            else if (id_errore == "mysql")
                field.insertAdjacentHTML( 'beforeend', "<p style='color: red'>"+err_array[id_errore]+"</p>");                
            else {
                document.getElementById("password").required = false;
                document.getElementById("password").minlength = 0;
                field.setCustomValidity(err_array[id_errore]); // fa apparire la finestrella di html 5 con la scritta che comunica errore
                field.setAttribute("onclick", "this.setCustomValidity('');");         
                field.setAttribute("onchange", "this.setCustomValidity('');");         
                field.style.color = "red";
                field.style.border = "2px solid red";
                field.style.borderRadius = "4px";
                document.getElementById("submit").click(); // show the validity dialog
                document.getElementById("password").required = true;
                document.getElementById("password").minlength = 6;                  
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
                <!-- div to show error message -->                
                <div id="userMessage">
                </div>
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
                        <input type="password" id="password" class="form-control input-in" name="password" minlength="6" maxlength="31" placeholder="6 characters minimum" autocomplete="on" required>
                    </div>
                    <!-- telefono -->
                    <div>
                        <label for="telefono">Telefono: </label><i>facoltativo</i>&emsp;
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
                            <label for="sito">Sito web: </label><i>facoltativo</i>&emsp;
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
                    <div class="captcha-box" id="captcha">
                        <div class="g-recaptcha" role="captcha" data-sitekey="6LdTc5AUAAAAAAJBUM9xlw-zpEf9o__oypShRBCv"></div>
                    </div>
                </div>
                <div class="btn-container">
                    <input type="submit" id="submit" class="btn btn-primary" value="Registrami">
                </div>
            </form>
    </fieldset>
</body>
</html>