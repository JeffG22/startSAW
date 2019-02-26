<?php
    require_once("php/utilities.php");
    require_once("php/handlesession.php");
    require_once("../connection.php");
    
    my_session_start();
    // If a user is not logged in and lands on this page, redirect to login
    if (!my_session_is_valid())
        navigateTo("login.php");

    $user_id = $_SESSION['userId'];
    $name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Le mie proposte</title>
    
    <?php
        require("php/head_common.php");
    ?>
      
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/proposal.css">
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
            
            <div class="col-md-8">
                <div class="profile-content">
                    <main>
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
                                                printProposalInfo($conn, $row, false);
                                                echo "<div class=\"d-flex justify-content-between align-items-center\">";
                                                echo "<form method='POST'>
                                                        <input type='hidden' name='proposal_id' value='".$row['id']."'>
                                                        <input class='btn btn-sm btn-outline-secondary' type='submit' value='Modifica proposta' formaction='edit_proposal.php'>
                                                        <input class='btn btn-sm btn-outline-secondary' type='submit' value='Elimina proposta' formaction='php/delete_proposal.php'>
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

    <!--Profiles-->
    <script src="js/ajax.js"></script>

</body>
</html>