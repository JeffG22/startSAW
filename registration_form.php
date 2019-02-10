<?php

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

            //----- preparazione richiesta tramite CURL -----
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
        // 2 - controllo .... TODOSSSS
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
        function showSecondBox() {
            var selectedRadioValue = document.querySelector('input[name=tipoUtente]:checked').value;
            var boxHidden, boxShowed;
            var htmlLegend = $("#legendaTipoInput");
            if (selectedRadioValue == "A") {
                boxHidden = $("#campiVolontario");
                boxShowed = $("#campiAssociazione");
                htmlLegend.html("Dati Associazione/Azienda");

            }
            else if (selectedRadioValue == "P") {
                boxHidden = $("#campiAssociazione");
                boxShowed = $("#campiVolontario");
                htmlLegend.html("Dati Persona");

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
                    <p>Registrazione come: </p>&emsp;            
                    <input type="radio" id="persona" name="tipoUtente" value="P" onchange="showSecondBox();" checked required>
                    <label for="persona">persona</label>
                    <input type="radio" id="associazione" name="tipoUtente" value="A" onchange="showSecondBox();">
                    <label for="associazione">associazione/azienda</label>
                </div>
                <div id="campiUtente">
                    <!-- email -->
                    <div>
                        <label for="email">Email: </label>&emsp;
                        <input type="email" id="email" name="email" minlength="4"  maxlength="31" placeholder="name@domain.net" autocomplete="on" required>
                    </div>
                    <!-- password -->
                    <div>
                        <label for="password">Password: </label>&emsp;
                        <input type="password" id="password" name="password" minlength="6" placeholder="6 characters minimum" autocomplete="on" required>
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
                <!-- i campi di volontario e associazione non hanno attributo required !-->
                <!-- Registrazione Volontario !-->
                    <div id="campiVolontario">
                        <!-- nomeV -->
                        <div>
                            <label for="nomeV">Nome: </label>&emsp;
                            <input type="text" id="nomeV" name="nomeV" maxlength="20">
                        </div>
                        <!-- cognome -->
                        <div>
                            <label for="cognome">Cognome: </label>&emsp;
                            <input type="text" id="cognome" name="cognome" maxlength="20">
                        </div>
                        <!-- data di nascita -->
                        <div>
                            <label for="data">Data di nascita: </label>&emsp;
                            <input type="date" id="data" name="data" min="1900-01-01">
                        </div>
                        <!-- Sesso -->
                        <div>
                            <label for="genere">Sesso: </label>&emsp;
                            <select id="genere" name="genere">
                                <option value="" selected>Selezionare</option>
                                <option value="F">F</option>
                                <option value="M">M</option>
                            </select>
                        </div>
                        <!-- Comune -->
                        <div>
                            <label for="comune">Comune: </label>&emsp;
                            <input type="text" id="comune" name="comune" maxlength="20">
                        </div>
                        <!-- Provincia -->
                        <div> 
                            <label for="provinciaV">Provincia: </label>&emsp;
                            <input type="text" id="provinciaV" name="provinciaV" maxlength="20">
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
                            <label for="luogo">Comune della sede: </label>&emsp;
                            <input type="text" id="luogo" name="luogo" maxlength="20">
                        </div>
                        <!-- Provincia -->
                        <div> 
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
                <input type="submit" value="Registrami!" onclick="return doRegistration(document.registration);">
            </form>
    </fieldset>
</body>
</html>
<?php
    if ($error_flag) {
        // comunicare errore ad utente con javascript
    }
?>