<?php
  require_once("php/domain_constraints.php");
  require_once("php/utilities.php");
  require_once("php/handlesession.php");
  require_once("../confidential_info.php");

  my_session_start();
  if (my_session_is_valid()) // Se un utente è già loggato e atterra su questa pagina --> redirect to index.php
    navigateTo("index.php");
  // Se un utente non è registrato e atterra su questa pagina --> ok

  // ----- CONTROLLI LATO SERVER su INPUT RICEVUTI -----
  $error_flag = false;
  try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      $email = "usr"; // email
      $password = "pwd"; // password
        
      // 1 ----- controllo dati utente ------
      if (empty($_POST[$email]) || !checksOnEmail($_POST[$email]))
        throw new InvalidArgumentException($email);
      if (empty($_POST[$password]) || !checksOnPswd($_POST[$password]))
        throw new InvalidArgumentException($password);
      

      // 2 ----- sanitizzazione input -----
      $fields_utente = sanitize_email($_POST[$email]);
        
      // 3 ----- inserimento nel DB se rispetta vincoli -----
      $query1 = "SELECT user_id, passwd, type, display_name, picture FROM user WHERE email = ?"; 
      require_once("../connection.php");
      if (!($conn = dbConnect()))
        throw new Exception("mysql ".mysqli_connect_error());
      if (!($stmt = mysqli_prepare($conn, $query1)))
        throw new Exception("mysql prepare".mysqli_error($conn));
      if (!mysqli_stmt_bind_param($stmt, 's', $fields_utente))
        throw new Exception("mysql bind param");
      if (!mysqli_stmt_execute($stmt))
        throw new InvalidArgumentException("mysql execute".$stmt->error);
      mysqli_stmt_store_result($stmt);
      if (mysqli_stmt_num_rows($stmt) != 1) // not match
          throw new InvalidArgumentException("Username o password errati, riprovare per favore.");
      if(!mysqli_stmt_bind_result($stmt, $id, $pswd, $type, $name, $picture))
        throw new Exception("mysql bind result".$stmt->error);
      if (mysqli_stmt_fetch($stmt)) {
          if(password_verify($_POST[$password], $pswd)) {
            // ----- 4 impostazione sessione ----
            my_session_login($id, ($type == "person"), $name, $picture);
            navigateTo("profile.php");
          } else
            throw new InvalidArgumentException("Username o password errati, riprovare per favore.");
      }
      else
        throw new Exception("mysql fetch");
      mysqli_stmt_close($stmt);
      mysqli_close($conn);
    }      
  } catch (Exception $ex) {
    $error_flag = true;
    $error_message = $ex->getMessage();
    if (strlen($error_message) >= 5 && substr($error_message, 0, 5) == "mysql")
      $error_message = "mysql";
  }  
?>

<!DOCTYPE html>
<html lang="it">

<head>
  <title>Log In</title>
	<?php require("php/head_common.php"); ?>
  
  <link rel="stylesheet" type="text/css" href="css/login.css">

  <script>
    "use strict"; //necessario per strict mode
    // creare un'array associativo "messaggio -> errore"           
    var err_array = {
      'usr' : 'Email non valida, riprovare per cortesia.',
      'pwd' : 'Password non valida, lunghezza minima 6 caratteri.',
      'mysql' : 'Login non riuscito, riprovare per favore.'
    };
    <?php
      $tempError = ($error_flag) ? $error_message : "";
      echo 'var id_errore = "'.$tempError.'";';
    ?>
    /** ----- operazione di recupero dati se non validi ----- */
    function loadPostData( jQuery ) {
    // ----- ricaricare dati inviati non validi -----
      <?php
        if ($error_flag && !empty($_POST[$email]))
          echo 'document.getElementById("'.$email.'").value="'.sanitize_email($_POST[$email]).'";';
      ?>
      if (id_errore == "usr" || id_errore == "pwd") {
        var field = document.getElementById(id_errore);
        field.setCustomValidity(err_array[id_errore]); // fa apparire la finestrella di html 5 con la scritta che comunica errore
        field.setAttribute("onclick", "this.setCustomValidity('');");         
        field.setAttribute("onchange", "this.setCustomValidity('');");
        field.style.color = "red";
        field.style.border = "2px solid red";
        field.style.borderRadius = "4px";
        document.getElementById("submit").click(); // show the validity box
      }
      else {
        if (id_errore == "mysql")
          id_errore = err_array[id_errore];
        document.getElementById("userMessage").insertAdjacentHTML( 'beforeend', "<p style='color: red'>"+id_errore+"</p>"); 
      }
    }
    <?php
      if ($error_flag) // se errore allora comunica all'utente ciò quando la pagina è ricaricata (funzione jquery)
        echo '$(document).ready(loadPostData);';
    ?>
  </script>
</head>

<body>
  <?php
		include("php/navbar.php")
	?>
  <div class="container">
      <div id="logcon" class="form-group">
          <form name="login" id="login" class="form-in" method="post" action="login.php">
              <span><legend>Log In</legend></span>
              <!-- div to show error message -->
              <div id="userMessage">
              </div>
              <!--Email box-->
              <label for="usr">Email: </label>
              <input type="email" name="usr" id="usr" class="form-control input-in" minlength="6"  maxlength="254" autocomplete="on" required>
              <!--Pwd box-->
              <label for="pwd">Password:</label>
              <input type="password" name="pwd" id="pwd" class="form-control input-in" minlength="6" maxlength="31" autocomplete="on" required>
              <!--Submit button-->
              <div class="btn-container">
                <button type="submit" id="submit" class="btn btn-primary">Log In</button>
              </div>
          </form>
      </div>
  </div>

</body>
</html>