<?php
    include_once("php/utilities.php");
    include_once("php/handlesession.php");
    include_once("../connection.php");
    
    my_session_start();
    // If a user is not logged in and lands on this page, redirect to login
    if (!my_session_is_valid()) {
        navigateTo("login.php");
    } else if ($_SESSION['type'] == "organization") {   // An organization can't have accepted proposals
        navigateTo("user.php");
    }

    $user_id = $_SESSION['userId'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Le mie proposte</title>
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
    <!--Boostrap-->
    
    <!--Inclusions-->
    <script src="js/jquery-3.1.0.min.js"></script>
    <script src="js/jquery.easing.min.js"></script>
      
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <!--Header/Navbar-->
    <?php
		include("php/navbar.php");
	?>
	<!--Header/Navbar-->

    <div class="container">
        <div class="row profile">
            <div class="col-md-4">
                <div class="profile-sidebar">
                    <!-- SIDEBAR USERPIC -->
                    <div class="profile-userpic">
                        <img src="media/profile-placeholder.png" class="img-responsive" alt="">
                    </div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name">
                            Placeholder
                        </div>
                        <div class="profile-usertitle-job">
                            Placeholder
                        </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->

                    <!-- SIDEBAR MENU -->
                    <div class="profile-usermenu">
                        <ul class="nav">
                            <li class="active">
                                <a href="#">
                                <i class="fas fa-home"></i>
                                Profilo </a>
                            </li>
                            <li>
                                <a href="#">
                                <i class="fas fa-user"></i>
                                Impostazioni</a>
                            </li>
                            <li>
                                <a href="#" target="_blank">
                                <i class="fas fa-list-alt"></i>
                                Le Mie Proposte </a>
                            </li>
                            <li>
                                <a href="#" target="_blank">
                                <i class="fas fa-list-alt"></i>
                                Proposte Accettate </a>
                            </li>
                        </ul>
                    </div>
                    <!-- END MENU -->
                </div>
            </div>
            <div class="col-md-8">
                <div class="profile-content">
                    <main role="main">
                        <div class="album py-5 bg-light">
                            <div class="container">
                                <div class="row">
                                    <h4>Proposte di volontariato accettate</h4>
                                    
                                    <?php
                                    
                                        if (isset($_SESSION['message'])) {
                                            echo "<div>".$_SESSION['message']."</div>";
                                            unset($_SESSION['message']);
                                        }

                                        try {
                                            if(!($conn = dbConnect()))
                                                throw new Exception("sql ".mysqli_connect_error());
                                        
                                            $query = "SELECT *
                                                      FROM proposal, accepted
                                                      WHERE proposal.id = accepted.proposal_id AND acceptor_id = ".$user_id;

                                            if(!($result = mysqli_query($conn, $query))) 
                                                throw new Exception("sql ".mysqli_error());

                                        } catch (Exception $ex) {
                                            $error_flag = true;
                                            $error_message = $ex->getMessage();
                                            //echo $ex->getMessage();
                                            //echo $error_flag;
                                        }
                                            
                                        if (mysqli_num_rows($result) == 0) { // Empty result
                                            echo "Sembra che tu non abbia ancora accettato alcuna proposta.";  
                                        } else { // Result not empty
                                            while($row = mysqli_fetch_assoc($result)) {
                                                printProposalInfo($conn, $row);

                                                echo "<form action='php/turn_down_proposal.php' method='post'>
                                                        <input type='hidden' name='proposal_id' value='".$row['id']."'>
                                                        <input type='submit' value='Rinuncia'>
                                                        </form>
                                                        <br>";
                                                echo "</div>";
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
</body>
</html>