<?php
    include_once("domain_constraints.php");
    include_once("utilities.php");
    include_once("handlesession.php");
    my_session_start();
    my_session_is_valid(); // Se un utente è già registrato e atterra su questa pagina --> redirect to index.php
                           // Se un utente non è registrato e atterra su questa pagina --> ok
    // ----- CONTROLLI LATO SERVER su INPUT RICEVUTI -----
    $error_flag = false;
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['g-recaptcha-response'])) {
        // 1 ----- controllo captcha -----
            //----- dati per richiesta -----
            $client_response = $_POST['g-recaptcha-response'];
            $secret="6LdTc5AUAAAAAPVFH6LfqZMlxDR_TwYOYt-YtjEj";
            $url = "https://www.google.com/recaptcha/api/siteverify";
            //----- preparazione richiesta API in POST tramite CURL -----
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1); // set post data to true
            curl_setopt($curl, CURLOPT_POSTFIELDS, "secret=".$secret."&response=".$client_response);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $json_token = curl_exec($curl);
            curl_close($curl);
            echo $json_token;
            $json_decoded = json_decode($json_token);
            
            //----- decodifica del pacchetto json ricevuto -----
            // struttura: { "success": false | true, "error-codes": [ "..." ] }
            $success = $json_decoded->{'success'};
            if (!$success) {
                $code = $json_decoded->{'error-codes'};
                print_r($code);
                throw new InvalidArgumentException("captcha");
            }
        // 2 ----- controllo dati utente ------
            
            $privacy = "privacy"; // privacy concessa
            $email = "email"; // email nomeutente
            $password = "password"; // password
            $telefono = "telefono"; // telefono
            if (empty($_POST[$privacy][0]) || $_POST[$privacy][0] != "Y")
                throw new InvalidArgumentException($privacy);
            if (empty($_POST[$email]) || !checksOnEmail($_POST[$email]))
                throw new InvalidArgumentException($email);
            if (!isset($_POST[$telefono] || (!empty($_POST[$telefono]) && !checksOnTel($_POST[$telefono]))) // due opzioni perchè non required
                throw new InvalidArgumentException($telefono);
            if (empty($_POST[$password]) || !checksOnPswd($_POST[$password]))
                throw new InvalidArgumentException($password);
            
        // 3 ----- determina se l'utente è persona -----
            $tipoUtente = "tipoUtente";
            if (empty($_POST[$tipoUtente]) || !checksOnTipoUtente($_POST[$tipoUtente]))
                throw new InvalidArgumentException($tipoUtente);
            $person = ($_POST[$tipoUtente] == "person") ? true : false;
        
        // 4 ----- controlli sui campiV o campiA -----
            
            $nome = ($person) ? "nomeV" : "nomeA"; // nome v o a
            $cognome = "cognome"; // cognome
            $data = "data"; // data
            $sex = "genere"; // genere
            $city = ($persona) ? "comune" : "sede"; // comune v o a
            $pr = ($persona) ? "provinciaV" : "provinciaA"; // provincia v o a
            $sett = "settore"; // settore
            $sito = "sito"; // sito
            if (empty($_POST[$nomeV]) || !checksOnName($_POST[$nomeV]))
                throw new InvalidArgumentException($nomeV);
            if (empty($_POST[$cognome]) || !checksOnSurname($_POST[$cognome]))
                throw new InvalidArgumentException($cognome);
            if ($persona && (empty($_POST[$data]) || !checksOnDate($_POST[$data])))
                throw new InvalidArgumentException($data);
            if ($persona && (empty($_POST[$sex]) || ($_POST[$sex] != "F" && $_POST[$sex] != "M" && $_POST[$sex] != "-")))
                throw new InvalidArgumentException($sex);
            if (empty($_POST[$city]) || !checksOnCity($_POST[$city]))
                throw new InvalidArgumentException($city);
            if (empty($_POST[$pr]) || !checksOnProv($_POST[$pr]))
                throw new InvalidArgumentException($pr);
            if (!$persona && (empty($_POST[$sett]) || !checksOnSettore($_POST[$sett])))
                throw new InvalidArgumentException($sett);
            if (!$persona && (!isset($_POST[$sito]) || (!empty($_POST[$sito]) && !checksOnSite($_POST[$sito]))))
                throw new InvalidArgumentException($sito);
            
        // 5 ----- sanitizzazione input -----
            $fields_utente; // array che conterrà i campi di utente
            $fields_utente[0] = sanitize_inputString($_POST[$email]);
            $fields_utente[1] = password_hash($_POST[$password], PASSWORD_DEFAULT); // hashing pswd
            $fields_utente[2] = ($person) ? "person" : "organization";
           
            $fields_value; // array che conterrà i campi person / organization
            if ($persona) {
            // person: (id, name, surname, gender, birthdate, township, province, phone)
                $fields_value[0] = sanitize_inputString($_POST[$nome]);
                $fields_value[1] = sanitize_inputString($_POST[$cognome]);
                $fields_value[2] = sanitize_inputString($_POST[$sex]);
                $fields_value[3] = sanitize_inputString($_POST[$data]);
                $fields_value[4] = sanitize_inputString($_POST[$city]);
                $fields_value[5] = sanitize_inputString($_POST[$pr]);
                if (!empty($_POST[$telefono]))
                    $fields_value[6] = sanitize_inputString($_POST[$telefono]);
                else
                    $fields_value[6] = "";
            }
            else {
            // organization: (name, headquarter, province, sector, website, phone)
                $fields_value[0] = sanitize_inputString($_POST[$nome]);
                $fields_value[1] = sanitize_inputString($_POST[$city]);
                $fields_value[2] = sanitize_inputString($_POST[$pr]);
                $fields_value[3] = sanitize_inputString($_POST[$sett]);
                if (!empty($_POST[$sito]))
                    $fields_value[4] = sanitize_inputString($_POST[$sito]);
                else
                    $fields_value[4] = "";
                if (!empty($_POST[$telefono]))
                    $fields_value[5] = sanitize_inputString($_POST[$sito]);
                else
                    $fields_value[5] = "";
            }
        
        // 6 ----- inserimento nel DB se rispetta vincoli -----
            require_once "connection.php";
            if (!($conn = get_dbconnection()))
                throw new Exception("sql ".mysqli_connect_error());
            $query1 = "INSERT INTO user (email, passwd, type) VALUES (?, ?, ?)"; 
            
        // ----- inizio transazione e prima query in Utente ------
            if (!mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE))
                throw new Exception("sql transaction".mysqli_connect_error($conn));
            if (!mysqli_autocommit($conn, FALSE))
                throw new Exception("sql transaction".mysqli_connect_error($conn));
            $insert1 = false;
            if (!($stmt = mysqli_prepare($conn, $query1)))
                throw new Exception("mysqli prepare".mysqli_error());
            if (!mysqli_stmt_bind_param($stmt, 'sss', $fields_utente[0], $fields_utente[1], $fields_utente[2]))
                throw new Exception("mysqli bind param");
            if (!mysqli_stmt_execute($stmt)) {
                if (mysqli_errno($conn) == 1062) // DUPLICATE PRIMARY KEY
                    throw new InvalidArgumentException($email."sql");
                throw new InvalidArgumentException("mysqli execute".$stmt->error);
            }
            if (mysqli_stmt_affected_rows($conn) == 1) { // unico utente inserito
                $insert1 = true;
                $idUtente = mysql_insert_id(); // id dell'ultima t-upla inserita
            }
            else
                throw new InvalidArgumentException("sql insert");
            mysqli_stmt_close($stmt);
            // ----- seconda query ------
            $insert2 = false;
            if ($persona) {
                $query2 = "INSERT INTO person (id, name, surname, gender, birthdate, township, province, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                if (!($stmt = mysqli_prepare($conn, $query2))) {
                    mysqli_rollback($conn);
                    throw new Exception("mysqli prepare".$conn->error);
                }
                if (!mysqli_stmt_bind_param($stmt, 'issssssss', 
                        $idUtente, $fields_value[0], $fields_value[1], $fields_value[2], $fields_value[3], 
                        $fields_value[4], $fields_value[5], $fields_value[6])) {
                    mysqli_rollback($conn);
                    throw new Exception("mysqli bind param");
                }
            }
            else {
                $query2 = "INSERT INTO organization (id, name, headquarter, province, sector, website, phone) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    if (!($stmt = mysqli_prepare($conn, $query2))) {
                        mysqli_rollback($conn);
                        throw new Exception("mysqli prepare ".$conn->error);
                    }
                    if (!mysqli_stmt_bind_param($stmt, 'issssss', 
                            $idUtente, $fields_value[0], $fields_value[1], $fields_value[2], 
                            $fields_value[3], $fields_value[4], $fields_value[5])) {
                        mysqli_rollback($conn);                            
                        throw new Exception("mysqli param");
                    }
            }
            if (!mysqli_stmt_execute($stmt)) {
                mysqli_rollback($conn);
                throw new InvalidArgumentException("mysqli execute ".$stmt->error);
            }
            if (mysqli_stmt_affected_rows($conn) == 1) // unica P/A inserita
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
            // variabili di sessione
            $_SESSION['userId'] = $idUtente;
            $_SESSION['type'] = ($persona) ? "person" : "organization";
            header("Location: index.php");
        }      
    } catch (Exception $ex) {
        $error_flag = true;
        $error_message = $ex.getMessage();
        //echo $ex->getMessage();
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
    <style>
    </style>
    <title>StartSAW - registrazione</title>
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
                boxHidden = $("#campiPerson");
                boxShowed = $("#campiOrganization");
                htmlLegend.html("Dati volontario");
                campiV.prop('required',true);
                campiA.prop('required',false);
            }
            boxHidden.hide()
            boxShowed.show();
        }
        /** ----- operazione di recupero dati se non validi ----- */
        function loadPostData( jQuery ) {
            // creare un'array associativo messaggio -> errore            
            var err_array = {
                'captcha' : 'Selezionare correttamente captcha',
                'privacy' : 'Selezionare correttamente la casella',
                'email' : 'email non valida',
                'password' : 'password non valida, lunghezza richiesta 6 caratteri',
                'telefono' : 'telefono inserito non valido',
                'nomeV' : 'nome non valido',
                'nomeA' : 'nome non valido',
                'cognome' : 'cognome non valido'
                'data' : 'data non valida',
                'genere' : 'selezionare il genere',
                'comune' : 'comune non valido',
                'sede' : 'sede non valida',
                'provinciaV' : 'provincia inserita non valida',
                'provinciaA' : 'provincia inserita non valida',
                'settore' : 'settore inserito non valido',
                'sito' : 'sito inserito non valido',
                'emailsql' : 'l\'email inserita risulta gi&agrave registrata'
            };
            <?php
                echo 'var id_errore = "'.$error_message.'";';
            ?>
            for (var key in err_array) {
                if (key == id_errore) {
                    if (key == "emailsql") 
                        var field = document.getElementById("email");
                    else 
                        var field = document.getElementById(key);
                    field.focus();
                    field.setCustomValidity(err_array[key]); // fa apparire la finestrella di html 5 con la scritta che comunica errore
                }
            }
            // ----- ricaricare dati inviati non validi -----
            <?php
                echo 'document.getElementById("'.$email.'").value="'.$_POST[$email].'";';
                echo 'document.getElementById("'.$telefono.'").value="'.$_POST[$telefono].'";';
                echo 'document.getElementById("'.$nome.'").value="'.$_POST[$nome].'";';
                echo 'document.getElementById("'.$cognome.'").value="'.$_POST[$cognome].'";';
                echo 'document.getElementById("'.$data.'").value="'.$_POST[$data].'";';
                echo 'document.getElementById("'.$sex.'").value="'.$_POST[$sex].'";';
                echo 'document.getElementById("'.$city.'").value="'.$_POST[$city].'";';
                echo 'document.getElementById("'.$pr.'").value="'.$_POST[$pr].'";';
                echo 'document.getElementById("'.$sett.'").value="'.$_POST[$sett].'";';
                echo 'document.getElementById("'.$sito.'").value="'.$_POST[$sito].'";';
            ?>
        }
        <?php
            if ($error_flag) // se errore allora comunica all'utente ciò quando la pagina è ricaricata (funzione jquery)
                echo '$(document).ready(loadPostData);'
        ?>
        
    </script>
</head>

<!-- BODY con campi per registrazione -->
<body>
    <!-- REGISTRAZIONE -->
	<fieldset class="box" id="FirstBox">
            <legend>Registrazione volontario</legend>
            <form name="registration" id="registration" method="POST" action="registration_form.php">
                <div>
                    <!-- tipo utente -->
                    <p>Registrati come: &emsp;            
                        <input type="radio" id="persona" name="tipoUtente" value="person" onchange="showSecondBox();" checked required>
                        <label for="persona">persona</label>
                        <input type="radio" id="associazione" name="tipoUtente" value="organization" onchange="showSecondBox();">
                        <label for="associazione">associazione</label>
                    </p>
                </div>
                <div id="campiUser">
                    <!-- email -->
                    <div>
                        <label for="email">Email: </label>&emsp;
                        <input type="email" id="email" name="email" minlength="6"  maxlength="254" placeholder="name@domain.net" autocomplete="on" required>
                    </div>
                    <!-- password -->
                    <div>
                        <label for="password">Password: </label>&emsp;
                        <input type="password" id="password" name="password" minlength="6" maxlength="31" placeholder="6 characters minimum" autocomplete="on" required>
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
                            <input type="text" id="nomeV" name="nomeV" class="campiV" minlength="4" maxlength="50" required>
                        </div>
                        <!-- cognome -->
                        <div>
                            <label for="cognome">Cognome: </label>&emsp;
                            <input type="text" id="cognome" name="cognome" class="campiV" maxlength="50" required>
                        </div>
                        <!-- data di nascita -->
                        <div>
                            <label for="data">Data di nascita: </label>&emsp;
                            <input type="date" id="data" name="data" class="campiV" min="1900-01-01" max="2006-12-31" required>
                        </div>
                        <!-- Sesso -->
                        <div>
                            <label for="genere">Sesso: </label>&emsp;
                            <select id="genere" name="genere" class="campiV" required>
                                <option value="-" selected>Non specificato</option>
                                <option value="F">F</option>
                                <option value="M">M</option>
                            </select>
                        </div>
                        <!-- Comune -->
                        <div>
                            <label for="comune">Comune: </label>&emsp;
                            <input type="text" id="comune" name="comune" class="campiV" minlength="4" maxlength="35" required>
                            &emsp;
                        <!-- Provincia --> 
                            <label for="provinciaV">Provincia: </label>&emsp;
                            <select id="provinciaV" name="provinciaV" class="campiV" required>
                            <option value="" selected>--</option>
                            <?php
                                include("data.php");
                                show_province();
                            ?>
                        </div>
                    </div>
                    <!-- Registrazione Associazione !-->
                    <div id="campiOrganization" style="display: none;">
                        <!-- nomeA -->
                        <div>
                            <label for="nomeA">Nome: </label>&emsp;
                            <input type="text" id="nomeA" class="campiA" name="nomeA" minlength="3" maxlength="50">
                        </div>
                        <!-- sede -->
                        <div>
                            <label for="sede">Comune della sede: </label>&emsp;
                            <input type="text" id="sede" name="sede" class="campiA" minlength="4" maxlength="35">
                            &emsp;
                        <!-- Provincia --> 
                            <label for="provinciaA">Provincia: </label>&emsp;
                            <select id="provinciaA" name="provinciaA" class="campiA">
                            <option value="" selected>--</option>
                            <?php
                                include("data.php");
                                show_province();
                            ?>
                        </div>
                        <!-- settore -->
                        <div>
                            <label for="settore">Settore in cui opera: </label>&emsp;
                            <input type="text" id="settore" name="settore" class="campiA" maxlength="35">
                        </div>
                        <!-- sito, non è required -->
                        <div>
                            <label for="sito">Sito web: </label>&emsp;
                            <input type="url" id="sito" name="sito" maxlength="63">
                        </div>
                    </div>
                    <!-- telefono -->
                    <div>
                        <label for="telefono">Telefono: </label>&emsp;
                        <input type="tel" id="telefono" name="telefono" pattern="[0-9]{3,15}" maxlength="15" minlength="3">
                    </div>
                </fieldset>    
                <br/>
                <!-- CONTROLLI PRIVACY e CAPTCHA -->
                <div id="controlli">
                    <input type="checkbox" id="privacy" name="privacy" value="Y" checked required>
                    <label for="privacy">D&ograve; il consenso al trattamento dei dati nelle modalità conformi al D. Lgs. 30 giugno 2003, n. 196 e successivi aggiornamenti </label>
                    <div class="g-recaptcha" data-sitekey="6LdTc5AUAAAAAAJBUM9xlw-zpEf9o__oypShRBCv"></div>
                    <br/>
                </div>
                <input type="submit" value="Registrami!">
            </form>
    </fieldset>
</body>
</html>