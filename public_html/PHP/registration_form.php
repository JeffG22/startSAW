<?php
    function sanitize_inputString($value) {
        return htmlspecialchars(stripslashes(trim($value)));
    }
    // Se un utente è già registrato e atterra su questa pagina
    // Se un utente non è registrato e atterra su questa pagina

    // ----- CONTROLLI LATO SERVER su INPUT RICEVUTI -----
    $error_flag = false;
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['g-recaptcha-response'])) {
        // 1 ----- controllo captcha -----
            // echo $_POST['g-recaptcha-response'].'<br/><br/><br/>';
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
            
            //----- elaborazione del pacchetto json ricevuto -----
            // { "success": false | true, "error-codes": [ "..." ] }
            $success = $json_decoded->{'success'};
            if (!$success) {
                $code = $json_decoded->{'error-codes'};
                print_r($code);
                $message = "Controllo reCAPTCHA fallito, riprovare!";
                throw new InvalidArgumentException("captcha");
            }
        // 2 - controllo dati utente
        // TODO controllare isset e nel caso toglierlo
            // email nomeutente
            $email = "email";
            if (!isset($_POST[$email]) || empty($_POST[$email]) || !checksOnEmail($_POST[$email]))
                throw new InvalidArgumentException($email);
            // password
            $password = "password";
            if (!isset($_POST[$password]) || empty($_POST[$password]) || !checksOnPswd($_POST[$password]))
                throw new InvalidArgumentException($password);
            // telefono
            $tel = "telefono";
            if (!isset($_POST[$tel]) || empty($_POST[$tel]) || !checksOnTel($_POST[$tel]))
                throw new InvalidArgumentException($tel);

        // 3 - controllo spunta Utente Volontario o Associazione
            $tipo = "tipoUtente";
            if (!isset($_POST[$tipo]) || empty($_POST[$tipo]) || ($_POST[$tipo] != "P" && $_POST[$tipo] != "A"))
                throw new InvalidArgumentException($tipo);
            $persona = ($_POST[$tipo] == "P") ? true : false;
        
        // 4 - controlli sui campi di persona o associazione
            // nome v o a
            $nome = ($persona) ? "nomeV" : "nomeA";
            if (!isset($_POST[$nomeV]) || empty($_POST[$nomeV]) || !checksOnName($_POST[$nomeV]))
                throw new InvalidArgumentException($nomeV);
            // cognome
            $cognome = "cognome";
            if ($persona && (!isset($_POST[$cognome]) || empty($_POST[$cognome]) || !checksOnSurname($_POST[$cognome])))
                throw new InvalidArgumentException($cognome);
            // data
            $data = "data";
            if ($persona && (!isset($_POST[$data]) || empty($_POST[$data]) || !checksOnDate($_POST[$data])))
                throw new InvalidArgumentException($data);
            // genere
            $sex = "genere";
            if ($persona && (!isset($_POST[$sex]) || empty($_POST[$sex]) || ($_POST[$sex] != "F" && $_POST[$sex] != "M")))
                throw new InvalidArgumentException($sex);
            // comune v o a
            $city = ($persona) ? "comune" : "sede";
            if (!isset($_POST[$city]) || empty($_POST[$city]) || !checksOnCity($_POST[$city]))
                throw new InvalidArgumentException($city);
            // provincia v o a
            $pr = ($persona) ? "provinciaV" : "provinciaA";
            if (!isset($_POST[$pr]) || empty($_POST[$pr]) || !checksOnCity($_POST[$pr]))
                throw new InvalidArgumentException($pr);
            // settore
            $sett = "settore";
            if (!$persona && (!isset($_POST[$sett]) || empty($_POST[$sett]) || !checksOnSettore($_POST[$sett])))
                throw new InvalidArgumentException($sett);
            // sito
            $sito = "sito";
            if (!$persona && (!isset($_POST[$sito]) || empty($_POST[$sito]) || !checksOnSite($_POST[$sito])))
                throw new InvalidArgumentException($sito);
            
        // 5 - sanitizzazione input TODO: funzione che fa queste tre funzioni e mysqli real escape string
            $fields_value;
            $fields_value[0] = sanitize_inputString($_POST[$email]);
            $fields_value[1] = sanitize_inputString($_POST[$password]);
            $fields_value[2] = sanitize_inputString($_POST[$tel]);
            $fields_value[3] = sanitize_inputString($_POST[$nome]);
            if ($persona) {
                $fields_value[4] = sanitize_inputString($_POST[$cognome]);
                $fields_value[5] = sanitize_inputString($_POST[$data]);
                $fields_value[6] = sanitize_inputString($_POST[$sex]);
                $fields_value[7] = sanitize_inputString($_POST[$city]);
                $fields_value[8] = sanitize_inputString($_POST[$pr]);
            }
            else {
                $fields_value[4] = sanitize_inputString($_POST[$city]);
                $fields_value[5] = sanitize_inputString($_POST[$pr]);
                $fields_value[6] = sanitize_inputString($_POST[$sett]);
                $fields_value[7] = sanitize_inputString($_POST[$sito]);
            }
        
        // 6 - inserimento nel DB se rispetta vincoli univocità
            require_once "connection.php";
            $conn = get_dbconnection();
            foreach ($fields_value as $v)
                $v = mysqli_real_escape_string($conn, $v); // sono tutte stringhe
            $query1 = "INSERT INTO Utenti ( TODOfields ) VALUES (?, ?, ?, ?)";
            $query2;
            if ($persona)
                $query2 = "INSERT INTO Utenti ( TODOfields ) VALUES (?, ?, ?, ?)";
            else
                $query2 = "INSERT INTO Utenti ( TODOfields ) VALUES (?, ?, ?, ?)"; 
            /*if ($stmt = mysqli_prepare($conn, $query)) {
                mysqli_stmt_bind_param($stmt, 's', $user);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_bind_result($stmt, $tableUsers);
                    mysqli_stmt_fetch($stmt);
                    
                    $row = mysqli_fetch_assoc($res);
                    if (mysqli_stmt_num_rows($res) == 1) { // unico utente
                        // mysqli_stmt_affected_rows
                        // verifica della password
                        // registrazione della sessione
                        $_SESSION['user'] = $user;
                    }
                    else { // tentativo di injection o errore
                        // fare qualcosa tipo redirect
                        // $error="Your Login Name or Password is invalid";
                    }
                    mysqli_free_result($tableUsers); 
                }
                else
                    $message = "query non eseguita";
                mysqli_stmt_free_result($stmt); 
                mysqli_stmt_close($stmt);
            }
            else
                $message = "query fallita.";
            mysqli_close($conn);
            */


        // 7 - rilascio dei cookie (impostazione della sessione) -> login automatico (?)
        
            

            
        }
        else {
            $error_flag = true;
            // dire o fare qualcosa?
        }       
    } catch (Exception $ex) {
        echo $ex->message;
        // errore da comunicare
        $error_flag = true;

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
        function setToRequired(item) {
            document.getElementById(item).required = true;
        }
        
        function setToUnrequired(item) {
            document.getElementById(item).required = false;
        }

        function showSecondBox() {
            var selectedRadioValue = document.querySelector('input[name=tipoUtente]:checked').value;
            var boxHidden, boxShowed;
            var htmlLegend = $("#legendaTipoInput");
            var volontarioIdFields = ["nomeV", "cognome", "data", "genere", "comune", "provinciaV"];
            var associazioneIdFields = ["nomeA", "sede", "provinciaA", "settore", "sito"];

            if (selectedRadioValue == "A") {
                boxHidden = $("#campiVolontario");
                boxShowed = $("#campiAssociazione");
                htmlLegend.html("Dati Associazione/Azienda");
                volontarioIdFields.forEach(setToUnrequired);
                associazioneIdFields.forEach(setToRequired);
            }
            else if (selectedRadioValue == "P") {
                boxHidden = $("#campiAssociazione");
                boxShowed = $("#campiVolontario");
                htmlLegend.html("Dati Persona");
                volontarioIdFields.forEach(setToRequired);
                associazioneIdFields.forEach(setToUnrequired);
            }
            boxHidden.hide()
            boxShowed.show();
        }
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
                        <input type="radio" id="persona" name="tipoUtente" value="P" onchange="showSecondBox();" checked required>
                        <label for="persona">persona</label>
                        <input type="radio" id="associazione" name="tipoUtente" value="A" onchange="showSecondBox();">
                        <label for="associazione">associazione/azienda</label>
                    </p>
                </div>
                <div id="campiUtente">
                    <!-- email -->
                    <div>
                        <label for="email">Email: </label>&emsp;
                        <input type="email" id="email" name="email" minlength="4"  maxlength="35" placeholder="name@domain.net" autocomplete="on" required>
                    </div>
                    <!-- password -->
                    <div>
                        <label for="password">Password: </label>&emsp;
                        <input type="password" id="password" name="password" minlength="6" maxlength="35" placeholder="6 characters minimum" autocomplete="on" required>
                    </div>
                    <!-- telefono -->
                    <div>
                        <label for="telefono">Telefono: </label>&emsp;
                        <input type="tel" id="telefono" name="telefono" pattern="[0-9]{7,12}" maxlength="12" minlength="7" required>
                    </div>
                </div>
                <br/>
                <fieldset class="box" id="SecondBox">
                <legend id="legendaTipoInput">Dati Persona</legend> <!-- OR Dati Associazione !-->
                <!-- i campi di associazione non hanno attributo required in static time !-->
                <!-- Registrazione Volontario !-->
                    <div id="campiVolontario">
                        <!-- nomeV -->
                        <div>
                            <label for="nomeV">Nome: </label>&emsp;
                            <input type="text" id="nomeV" name="nomeV" maxlength="20" required>
                        </div>
                        <!-- cognome -->
                        <div>
                            <label for="cognome">Cognome: </label>&emsp;
                            <input type="text" id="cognome" name="cognome" maxlength="20" required>
                        </div>
                        <!-- data di nascita -->
                        <div>
                            <label for="data">Data di nascita: </label>&emsp;
                            <input type="date" id="data" name="data" min="1900-01-01" required>
                        </div>
                        <!-- Sesso -->
                        <div>
                            <label for="genere">Sesso: </label>&emsp;
                            <select id="genere" name="genere" required>
                                <option value="" selected>Selezionare</option>
                                <option value="F">F</option>
                                <option value="M">M</option>
                            </select>
                        </div>
                        <!-- Comune -->
                        <div>
                            <label for="comune">Comune: </label>&emsp;
                            <input type="text" id="comune" name="comune" maxlength="20" required>
                            &emsp;
                        <!-- Provincia -->
                            <label for="provinciaV">Provincia: </label>&emsp;
                            <input type="text" id="provinciaV" name="provinciaV" maxlength="20" required>
                        </div>
                    </div>
                    <!-- Registrazione Associazione !-->
                    <div id="campiAssociazione" style="display: none;">
                        <!-- nomeA -->
                        <div>
                            <label for="nomeA">Nome: </label>&emsp;
                            <input type="text" id="nomeA" name="nomeA" maxlength="20">
                        </div>
                        <!-- sede -->
                        <div>
                            <label for="sede">Comune della sede: </label>&emsp;
                            <input type="text" id="sede" name="sede" maxlength="20">
                            &emsp;
                        <!-- Provincia --> 
                            <label for="provinciaA">Provincia: </label>&emsp;
                            <input type="text" id="provinciaA" name="provinciaA" maxlength="20">
                        </div>
                        <!-- settore -->
                        <div>
                            <label for="settore">Settore in cui opera: </label>&emsp;
                            <input type="text" id="settore" name="settore" maxlength="20">
                        </div>
                        <!-- sito -->
                        <div>
                            <label for="sito">Sito web: </label>&emsp;
                            <input type="text" id="sito" name="sito" maxlength="20">
                        </div>
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
<?php
    if ($error_flag) {
        // comunicare errore ad utente con javascript
    }
?>