<?php
    include_once("php/utilities.php");
    include_once("php/handlesession.php");
    include_once("../connection.php");
    
    my_session_start();
    // If a user is not logged in and lands on this page, redirect to login
    if (!my_session_is_valid())
        header("Location: login.php");

    $user_id = $_SESSION['userId'];
    $name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Le mie proposte</title>
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
        include("php/popup.php");
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
                include("php/user_sidebar.php");
                if ($_SESSION['type'] == 'organization'){
                    echo "<script>document.getElementById(\"side-accepted\").remove();</script>";
                }
            ?>
            
            <div class="col-md-8">
                <div class="profile-content">
                    <main role="main">
                        <h4>Le mie proposte di volontariato</h4>
                        <div class="album">
                            <div class="container">
                                <div class="row">            
                                    <?php
                                        try {
                                            if(!($conn = dbConnect()))
                                                throw new Exception("sql ".mysqli_connect_error());
                                        
                                            $query = "SELECT * FROM proposal WHERE proposer_id = ".$user_id;

                                            if(!($result = mysqli_query($conn, $query))) 
                                                throw new Exception("sql ".mysqli_error());

                                        } catch (Exception $ex) {
                                            $error_flag = true;
                                            $error_message = $ex->getMessage();
                                            //echo $ex->getMessage();
                                            //echo $error_flag;
                                        }
                                            
                                        if (mysqli_num_rows($result) == 0) { // Empty result
                                            echo "Sembra che tu non abbia inserito alcuna proposta.";  
                                        } else { // Result not empty
                                            while($row = mysqli_fetch_assoc($result)) {
                                                printProposalInfo($conn, $row);
                                                echo "<div class=\"d-flex justify-content-between align-items-center\">";
                                                echo "<form method='POST'>
                                                        <input type='hidden' name='proposal_id' value='".$row['id']."'>
                                                        <input class='btn btn-sm btn-outline-secondary' type='submit' value='Modifica proposta' formaction='edit_proposal.php'>
                                                        <input class='btn btn-sm btn-outline-secondary' type='submit' value='Elimina proposta' formaction='delete_proposal.php'>
                                                        </form>
                                                        <br>";
                                                echo "</div></div></div>";
                                            }                                
                                        }         
                                        
                                    ?>
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
      document.getElementById("side-proposal").classList.add('active');
    </script>

</body>
</html>