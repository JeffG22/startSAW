<?php
    require_once("php/utilities.php");
    require_once("php/handlesession.php");
    require_once("../connection.php");
    
    my_session_start();
    // If a user is not logged in and lands on this page, redirect to login
    if (!my_session_is_valid()) {
        navigateTo("login.php");
    }

    $user_id = $_SESSION['userId'];
    $name = $_SESSION['name'];
    //$picture = $_SESSION['picture'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profilo</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

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
    <!--Bootstrap-->
    
    <!--Inclusions-->
    <script src="js/jquery-3.1.0.min.js"></script>
    <script src="js/jquery.easing.min.js"></script>
      
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/user.css">
</head>
<body>
  
    <!--Popup for session messages-->
    <?php
        require("php/popup.php");
    ?>
    <!--Popup-->

    <!--Header/Navbar-->
    <?php
        include("php/navbar.php");
    ?>
    <!--Header/Navbar-->

    <div class="container">
        <div class="row profile">
            
            <?php
                require("php/user_sidebar.php");
                if ($_SESSION['type'] == 'organization'){
                    echo "<script>document.getElementById(\"side-accepted\").remove();</script>";
                }
            ?>
            
            <?php
              if (($_SESSION['type'] == 'person')||($_SESSION['type'] == 'organization')){
                $type = $_SESSION['type'];
              } else {
                //Shouldn't happen naturally.
                throw new Exception("Errore nella connessione. Per favore, riconnettersi");
                my_session_logout();
              }

              try {
                  if(!($conn = dbConnect()))
                      throw new Exception("sql ".mysqli_connect_error());
                  $query = "SELECT user.description, ".$type.".phone FROM user, ".$type." WHERE user.user_id=".$user_id." AND user.user_id = ".$type.".id";
                  if(!($result = mysqli_query($conn, $query))) 
                      throw new Exception("sql ".mysqli_error($conn));
                  if (!($row = mysqli_fetch_assoc($result)))
                    throw new InvalidArgumentException("mysql");
                  mysqli_close($conn);
              } catch (Exception $ex) {
                  $error_flag = true;
                  $error_message = $ex->getMessage();
              }
            ?>

            <div class="col-md-8">
                <div class="profile-content">
                    <main role="main">
                      <h4>Informazioni</h4>
                        <div>
                            <div class="container">
                                <div class="row">
                                  <div class="card profile-card mb-4 box-shadow">
                                      <div class="card-body">
                                        <h5 class="card-title">Descrizione</h5>
                                        <p class="card-text">
                                          <?php   
                                              if (empty($row['description'])) // Empty result
                                                echo "Sembra che tu non abbia ancora aggiunto una tua descrizione.";  
                                              else // Result not empty
                                                printUserInfo($row);                               
                                          ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="btn-group">
                                              <a href="edit_aboutme.php">
                                                <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
                                              </a>
                                            </div>
                                        </div>
                                      </div>
                                  </div>
                                </div>
                                <div class="card profile-card mb-4 box-shadow">
                                      <div class="card-body">
                                        <h5 class="card-title">Telefono</h5>
                                        <p class="card-text">
                                          <?php   
                                              if (empty($row['phone'])) // Empty result
                                                echo "Sembra che tu non abbia ancora aggiunto il tuo numero di telefono.";  
                                              else // Result not empty
                                                echo($row['phone']);                               
                                          ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="btn-group">
                                              <a href="edit_profile.php">
                                                <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
                                              </a>
                                            </div>
                                        </div>
                                      </div>
                                  </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
    
    <!--Active sidebar script-->
    <script>
      document.getElementById("side-profile").classList.add('active');
    </script>
</body>
</html>