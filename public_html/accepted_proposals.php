<?php
    include_once("php/utilities.php");
    include_once("php/handlesession.php");
    include_once("../connection.php");
    
    my_session_start();
    // If a user is not logged in and lands on this page, redirect to login
    if (!my_session_is_valid()) {
        navigateTo("login.php");
    } else if ($_SESSION['type'] == "organization") {   // An organization can't have accepted proposals
        navigateTo("profile.php");
    }

    $user_id = $_SESSION['userId'];
    $name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Proposte Accettate</title>

    <?php
        require("php/head_common.php");
    ?>

    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/proposal.css">

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
            ?>

            <div class="col-md-8">
                <div class="profile-content">
                    <main role="main">
                        <h4>Proposte di volontariato accettate</h4>
                        <div class="album">
                            <div class="container album-container">
                                <div class="row"> 
                                    <?php
                                        $error_flag = false;
                                        try {
                                            if(!($conn = dbConnect()))
                                                throw new Exception("sql ".mysqli_connect_error());
                                        
                                            $query = "SELECT *
                                                      FROM proposal, accepted
                                                      WHERE proposal.id = accepted.proposal_id AND acceptor_id = ".$user_id;

                                            if(!($result = mysqli_query($conn, $query))) 
                                                throw new Exception("mysql ".mysqli_error());

                                        } catch (Exception $ex) {
                                            $error_flag = true;
                                            $error_message = $ex->getMessage();
                                        }
                                            
                                        if ($error_flag) {
                                            echo "<p id='errmessage'>Errore di accesso al database. Riprova.</p>";
                                        } else if (mysqli_num_rows($result) == 0) { // Empty result
                                            echo "Sembra che tu non abbia ancora accettato alcuna proposta.";  
                                        } else { // Result not empty
                                            while($row = mysqli_fetch_assoc($result)) {
                                                printProposalInfo($conn, $row, true);
                                                echo "<div class=\"d-flex justify-content-between align-items-center\">";
                                                echo "<form action='php/turn_down_proposal.php' method='post'>
                                                        <input type='hidden' name='proposal_id' value='".$row['id']."'>
                                                        <button type=\"submit\" value=\"Rinuncia\" class=\"btn btn-sm btn-outline-secondary\">Rinuncia</button>
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
        document.getElementById("side-accepted").classList.add('active');
    </script>
    <script src="js/ajax.js"></script>
</body>
</html>