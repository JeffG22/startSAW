<?php
    include("../../connection.php");
    include("utilities.php");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>View accepted proposals</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                                    <?php
                                        if (isset($_SESSION['message'])) {
                                            echo "<div>".$_SESSION['message']."</div>";
                                            unset($_SESSION['message']);
                                        }

                                        $con = dbConnect();

                                        if (!$con) { // Connection error
                                            echo "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                                                    Aspetta qualche istante e riprova.";
                                        } else // Connection succeeded
                                            echo "<br><b>LE MIE PROPOSTE DI VOLONTARIATO</b><br>";

                                        $result = mysqli_query($con, "SELECT *
                                                                    FROM proposal
                                    WHERE proposer_id = "./*$user_id*/1);

                                        if (!$result) { // Query error
                                            echo "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                                                    Aspetta qualche istante e riprova.";
                                        } else if (mysqli_num_rows($result) == 0) { // Empty result
                                            echo "Sembra che tu non abbia inserito alcuna proposta.";
                                        } else { // Result not empty
                                            while($row = mysqli_fetch_assoc($result)) {
                                                printProposalInfo($con, $row);

                                                echo "<form method='POST'>
                                                        <input type='hidden' name='proposal_id' value='".$row['id']."'>
                                                        <input type='submit' value='Modifica proposta' formaction='edit_proposal.php'>
                                                        <input type='submit' value='Elimina proposta' formaction='delete_proposal.php'>
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
</body>
</html>