<?php
    function sanitize_inputString($value) {
        //return htmlspecialchars(stripslashes(trim($value)));
        return htmlspecialchars(trim($value));
        // TODO considerare nl2br se serve e strtr per convertire cose
        // strtr(str, "<br />", " "); 
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
                throw new InvalidArgumentException("captcha");
            }
        // 2 ----- controllo dati utente ------
            
            $priv = "privacy"; // privacy concessa
            $email = "email"; // email nomeutente
            $password = "password"; // password
            $tel = "telefono"; // telefono TODO giù

            if (empty($_POST[$priv][0]) || $_POST[$priv][0] != "Y")
                throw new InvalidArgumentException($priv);
            if (empty($_POST[$email]) || !checksOnEmail($_POST[$email]))
                throw new InvalidArgumentException($email);
            if (empty($_POST[$password]) || !checksOnPswd($_POST[$password]))
                throw new InvalidArgumentException($password);
            if (empty($_POST[$tel]) || !checksOnTel($_POST[$tel]))
                throw new InvalidArgumentException($tel);

        // 3 ----- determina se l'utente è persona -----

            $tipo = "tipoUtente";
            if (empty($_POST[$tipo]) || ($_POST[$tipo] != "P" && $_POST[$tipo] != "A"))
                throw new InvalidArgumentException($tipo);
            $persona = ($_POST[$tipo] == "P") ? true : false;
        
        // 4 ----- controlli sui campiV o campiA -----
            
            $nome = ($persona) ? "nomeV" : "nomeA"; // nome v o a
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
            if ($persona && (empty($_POST[$sex]) || ($_POST[$sex] != "F" && $_POST[$sex] != "M")))
                throw new InvalidArgumentException($sex);
            if (empty($_POST[$city]) || !checksOnCity($_POST[$city]))
                throw new InvalidArgumentException($city);
            if (empty($_POST[$pr]) || !checksOnProv($_POST[$pr]))
                throw new InvalidArgumentException($pr);
            if (!$persona && (empty($_POST[$sett]) || !checksOnSettore($_POST[$sett])))
                throw new InvalidArgumentException($sett);
            if (!$persona && (empty($_POST[$sito]) || !checksOnSite($_POST[$sito])))
                throw new InvalidArgumentException($sito);
            
        // 5 ----- sanitizzazione input -----

            $fields_utente; // array che conterrà i campi di utente
            $fields_utente[0] = sanitize_inputString($_POST[$email]);
            $fields_utente[1] = password_hash($_POST[$password], PASSWORD_DEFAULT); // hashing pswd
            $fields_utente[2] = sanitize_inputString($_POST[$tel]);
            $fields_value; // array che conterrà i campi associazione / persona
            $fields_value[0] = sanitize_inputString($_POST[$nome]);
            if ($persona) {
                $fields_value[1] = sanitize_inputString($_POST[$cognome]);
                $fields_value[2] = sanitize_inputString($_POST[$sex]);
                $fields_value[3] = sanitize_inputString($_POST[$data]);
                $fields_value[4] = sanitize_inputString($_POST[$city]);
                $fields_value[5] = sanitize_inputString($_POST[$pr]);
            }
            else {
                $fields_value[1] = sanitize_inputString($_POST[$city]);
                $fields_value[2] = sanitize_inputString($_POST[$pr]);
                $fields_value[3] = sanitize_inputString($_POST[$sett]);
                $fields_value[4] = sanitize_inputString($_POST[$sito]);
            }
        
        // 6 ----- inserimento nel DB se rispetta vincoli -----

            require_once "connection.php";
            if (!($conn = get_dbconnection()))
                throw new Exception("sql ".mysqli_connect_error());
            $query1 = "INSERT INTO user (email, passwd, type) VALUES (?, ?, ?)"; 
            
        // ----- inizio transazione e prima query in Utente ------
            if (!mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE))
                throw new Exception("sql ".mysqli_connect_error($conn));
            if (!mysqli_autocommit($conn, FALSE))
                throw new Exception("sql ".mysqli_connect_error($conn));

            $insert1 = false;
            if (!($stmt = mysqli_prepare($conn, $query1)))
                throw new Exception("sql ".mysqli_error());
            if (!mysqli_stmt_bind_param($stmt, 'sss', $fields_utente[0], $fields_utente[1], $fields_utente[2]))
                throw new Exception("sql param");
            if (!mysqli_stmt_execute($stmt)) {
                if (mysqli_errno($conn) == 1062) // DUPLICATE PRIMARY KEY
                    throw new InvalidArgumentException($email."sql");
                throw new InvalidArgumentException("sql ".$stmt->error);
            }
            if (mysqli_stmt_affected_rows($conn) == 1) { // unico utente inserito
                $insert1 = true;
                $idUtente = mysql_insert_id(); // id dell'ultima t-upla inserita
            }
            else
                throw new InvalidArgumentException("sql");
            mysqli_stmt_close($stmt);

            // ----- seconda query ------
            $insert2 = false;
            if ($persona) {
                $query2 = "INSERT INTO person (id, name, surname, gender, birthdate, township, province, phone) VALUES (?, ?, ?, ?, ?, ?)";
                if (!($stmt = mysqli_prepare($conn, $query2))) {
                    mysqli_rollback($conn);
                    throw new Exception("sql ".$conn->error);
                }
                if (!mysqli_stmt_bind_param($stmt, 'issssss', 
                        $idUtente, $fields_value[0], $fields_value[1], $fields_value[2], $fields_value[3], $fields_value[4], $fields_value[5])) {
                    mysqli_rollback($conn);
                    throw new Exception("sql param");
                }
            }
            else {
                $query2 = "INSERT INTO organization (name, headquarter, province, sector, website) VALUES (?, ?, ?, ?, ?)";
                    if (!($stmt = mysqli_prepare($conn, $query2))) {
                        mysqli_rollback($conn);
                        throw new Exception("sql ".$conn->error);
                    }
                    if (!mysqli_stmt_bind_param($stmt, 'isssss', 
                            $idUtente, $fields_value[0], $fields_value[1], $fields_value[2], $fields_value[3], $fields_value[4])) {
                        mysqli_rollback($conn);                            
                        throw new Exception("sql param");
                    }
            }
            if (!mysqli_stmt_execute($stmt)) {
                mysqli_rollback($conn);
                throw new InvalidArgumentException("sql ".$stmt->error);
            }
            if (mysqli_stmt_affected_rows($conn) == 1) // unica P/A inserita
                $insert2 = true;
            else {
                mysqli_rollback($conn);
                throw new InvalidArgumentException("sql");
            }
            if (!mysqli_commit($conn))
                throw new Exception("transaction");

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
        echo $ex->getMessage();
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

    <!--Boostrap-->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" 
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
	    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" 
	    integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" 
	    integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  
    <!--Inclusions-->
        <script src="js/include.js"></script> 
         <link rel="stylesheet" href="css/global.css">
         <link rel="stylesheet" type="text/css" href="css/login.css">

    <style>
    </style>
    <title>StartSAW - registrazione</title>

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

            if (selectedRadioValue == "A") { // si vuole inserire un'associazione
                boxHidden = $("#campiVolontario");
                boxShowed = $("#campiAssociazione");
                htmlLegend.html("Dati Associazione/Azienda");
                campiV.prop('required',false);
                campiA.prop('required',true);
            }
            else if (selectedRadioValue == "P") { // si vuole inserire una persona
                boxHidden = $("#campiAssociazione");
                boxShowed = $("#campiVolontario");
                htmlLegend.html("Dati Volontario");
                campiV.prop('required',true);
                campiA.prop('required',false);
            }
            boxHidden.hide()
            boxShowed.show();
        }

        /** ----- operazione di recupero dati se non validi ----- */
        function loadPostData( jQuery ) {
            // utente -> ricaricare email, password, telefono, flag privacy
            // tipo -> P / A
            // persona -> nome, cognome, data, sesso, comune, provincia
            // associaz. -> nome, comune, provincia, settore, sito
            // comunicare errore ad utente con javascript
        }
        <?php
            if ($error_flag)
                echo '$( document ).ready( loadPostData );'
        ?>
        
    </script>
</head>

<!-- BODY con campi per registrazione -->
<body>

    <!--Navbar-->
    <?php
		include("php/navbar.php")
	?>

    <!-- REGISTRAZIONE -->
    <div class="container">
    <div class="form-group">
	    <fieldset class="box" id="FirstBox">
            <legend>Registrazione volontario</legend>
            <form name="registration" id="registration" method="POST" action="registration_form.php">
            <div class="field">
                <!-- tipo utente -->
                <p>Registrati come:</p>            
                    <input type="radio" id="persona" name="tipoUtente" value="person" onchange="showSecondBox();" checked required>
                    <label for="persona">persona</label>
                    <input type="radio" id="associazione" name="tipoUtente" value="organization" onchange="showSecondBox();">
                    <label for="associazione">associazione</label>
                
            </div>

            <div id="campiUser" class="field">
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
                <!-- telefono TODO in tabella organization oppure person e facoltativo -->
                <div>
                    <label for="telefono">Telefono: </label>&emsp;
                    <input type="tel" id="telefono" name="telefono" pattern="[0-9]{3,15}" maxlength="15" minlength="3">
                </div>
            </div>
        </fieldset>
        <br/>
          
        <fieldset class="box" id="SecondBox">
            <legend id="legendaTipoInput">Dati Volontario</legend> <!-- OR Dati Associazione !-->
            <!-- i campi di associazione non hanno attributo required in static time !-->
            <!-- Registrazione Volontario !-->
            <div id="campiPerson" class="field">
                <!-- nomeV -->
                <div>
                    <label for="nomeV">Nome: </label>&emsp;
                    <input type="text" id="nomeV" name="nomeV" class="campiV" maxlength="50" required>
                </div>
                    
                <!-- cognome -->
                <div>
                    <label for="cognome">Cognome: </label>&emsp;
                    <input type="text" id="cognome" name="cognome" class="campiV" maxlength="50" required>
                </div>
                    
                <!-- data di nascita TODO DINAMICO e CONVERTIRE YYYY-MM-DD -->
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
                    <input type="text" id="comune" name="comune" class="campiV" maxlength="35" required>
                    &emsp;
                    
                    <!-- Provincia TODO autocomplete e pattern-->
                        <label for="provinciaV">Provincia: </label>&emsp;
                        <input type="text" id="provinciaV" name="provinciaV" class="campiV" size="2" required>
                </div>
            </div>

            <!-- Registrazione Associazione !-->
                <div id="campiAssociazione" style="display: none;" class="field">
            
                    <!-- nomeA -->
                    <div>
                        <label for="nomeA">Nome: </label>&emsp;
                        <input type="text" id="nomeA" class="campiA" name="nomeA" maxlength="64">
                    </div>
            
                    <!-- sede -->
                    <div>
                        <label for="sede">Comune della sede: </label>&emsp;
                        <input type="text" id="sede" name="sede" class="campiA" maxlength="35">
                        &emsp;
                
                        <!-- Provincia TODO autocomplete --> 
                            <label for="provinciaA">Provincia: </label>&emsp;
                            <input type="text" id="provinciaA" name="provinciaA" class="campiA" size="2">
                    </div>
                
                    <!-- settore -->
                    <div>
                        <label for="settore">Settore in cui opera: </label>&emsp;
                        <input type="text" id="settore" name="settore" class="campiA" maxlength="35">
                    </div>
                
                    <!-- sito -->
                    <div>
                        <label for="sito">Sito web: </label>&emsp;
                        <input type="url" id="sito" name="sito" maxlength="64">
                    </div>
                </div>
            </fieldset>    
            <br/>
            
            <!-- CONTROLLI PRIVACY e CAPTCHA -->
            <div id="controlli" class="field">
                <input type="checkbox" id="privacy" name="privacy" value="Y" checked required>
                <label for="privacy">Do il consenso al trattamento dei dati nelle modalità conformi al D. Lgs. 30 giugno 2003, n. 196 e successivi aggiornamenti </label>
                <div class="g-recaptcha" data-sitekey="6LdTc5AUAAAAAAJBUM9xlw-zpEf9o__oypShRBCv"></div>
                <br/>
            </div>
            
            <input type="submit" value="Registrami!">
        </form>
    </fieldset>
    </div>
    </div>
</body>
</html>
