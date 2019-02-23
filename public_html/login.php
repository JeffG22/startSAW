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
      $query1 = "SELECT user_id, passwd, type FROM user WHERE email = ?"; 

      require_once("../connection.php");
      if (!($conn = dbConnect()))
        throw new Exception("sql ".mysqli_connect_error());
      if (!($stmt = mysqli_prepare($conn, $query1)))
        throw new Exception("mysqli prepare".mysqli_error($conn));
      if (!mysqli_stmt_bind_param($stmt, 's', $fields_utente))
        throw new Exception("mysqli bind param");
      if (!mysqli_stmt_execute($stmt))
        throw new InvalidArgumentException("mysqli execute".$stmt->error);
      mysqli_stmt_store_result($stmt);

      if (mysqli_stmt_num_rows($stmt) != 1) // not match
          throw new InvalidArgumentException("Fail");
      if(!mysqli_stmt_bind_result($stmt, $id, $pswd, $type))
        throw new Exception("mysqli bind result".$stmt->error);
      if (mysqli_stmt_fetch($stmt)) {
        if(password_verify($_POST[$password], $pswd)) {
        // ----- 4 impostazione sessione ----
          $person = ($type == "person");
          my_session_login($id, $person);
          header("Location: index.php"); //TODO change to personal page - login avvenuto con successo
        }
        else
          throw new InvalidArgumentException("Fail");
      }
        
      else
        throw new Exception("mysqli fetch");
      mysqli_stmt_close($stmt);
      mysqli_close($conn);
    }      
  } catch (Exception $ex) {
    $error_flag = true;
    $error_message = $ex->getMessage();
  }  
?>

<!DOCTYPE html>
<html lang="it">

<head>
  <title>Log In</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
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
  
  <!--Inclusions
  <script src="js/include.js"></script> -->

  <!--CSS-->
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" type="text/css" href="css/login.css">

  <script>
    "use strict"; //necessario per strict mode
    // creare un'array associativo "messaggio -> errore"           
    var err_array = {
      'usr' : 'Email non valida, riprovare per cortesia.',
      'pwd' : 'Password non valida, lunghezza minima 6 caratteri.',
      'Fail' : 'Username o password errati, riprovare per favore.',
      'Altro' : 'Login non riuscito, riprovare per favore.',
    };
    <?php
      $tempError = ($error_flag) ? $error_message : "";
      echo 'var id_errore = "'.$tempError.'";';
    ?>
    /** ----- operazione di recupero dati se non validi ----- */
    function loadPostData( jQuery ) {
    // ----- ricaricare dati inviati non validi -----
      <?php
        if ($error_flag)
          echo 'document.getElementById("'.$email.'").value="'.$_POST[$email].'";';
      ?>
      for (var key in err_array) {
        if (key == id_errore) {
          if (key == "Fail")
            var field = document.getElementById("usr");
          else { //usr o pwd
            var field = document.getElementById(key);
          }
          field.setCustomValidity(err_array[key]); // fa apparire la finestrella di html 5 con la scritta che comunica errore
          field.setAttribute("onkeydown", "this.setCustomValidity('');");         
          field.style.color = "red";
          field.style.border = "2px solid red";
          field.style.borderRadius = "4px";
          document.getElementById("submit").click(); // show the validity box
          break;
        }
        if (key == "Altro")
            document.getElementById("userMessage").insertAdjacentHTML( 'beforeend', "<p style='color: red'>"+err_array[key]+"</p>");
      }   
    }
    <?php
      if ($error_flag) // se errore allora comunica all'utente ciò quando la pagina è ricaricata (funzione jquery)
        echo '$(document).ready(loadPostData);'
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
              <legend>Log In</legend>
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