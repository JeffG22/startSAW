<?php
    include_once("php/utilities.php");
    include_once("php/handlesession.php");
    include_once("../connection.php");
    
    my_session_start();

    if (my_session_is_valid())
        $user_id = $_SESSION['userId'];

    if (!empty($_GET['search'])) {
        $search_query = sanitize_inputString(strip_tags($_GET['search']));
    } else if (isset($_GET['search'])) {    // If empty search query, removes "search=" from url
        navigateTo("browse_proposals.php");
    } else {
        $search_query = "";
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>Proposte di volontariato</title>
    
    <?php
        require("php/head_common.php");
    ?>

    <link rel="stylesheet" href="css/proposal.css">
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

    <div class="col-md-11 container"> 
        <h4>Proposte di volontariato disponibili</h4>

        <form class="form-inline my-2 my-lg-0" action ="browse_proposals.php" method="GET">
            <input type="search" name="search" id="searchbox" class="form-control ml-auto" value="<?php echo $search_query ?>">
            <div class="btn-group src-btn">
                <input type="submit" class="btn btn-outline-dark my-2 my-sm-0" value="Cerca">
                <input type="submit" class="btn btn-outline-dark my-2 my-sm-0" value="Vedi tutte" 
                       onclick="document.getElementById('searchbox').value = '';">
            </div>
        </form>
            <div id="proposal-container" class="album">
                <div class="row">
                <?php
                    $error_flag = false;
                    try {
                        if(!($conn = dbConnect()))
                            throw new Exception("mysql ".mysqli_connect_error());

                        if (!my_session_is_valid()) {
                            echo "<div class=\"alert alert-primary\" id=\"notice-account\">Esplora liberamente le proposte disponibili. Per accettare una proposta, <a href=\"login.php\">effettua il login</a>!</div>";
                        } else if ($_SESSION['type'] == 'organization') {
                            echo "<div class=\"alert alert-primary\" id=\"notice-account\">Non puoi accettare proposte perché hai eseguito il login come associazione. Per accettare una proposta, <a href=\"logout_then_in.php\">effettua il login come utente</a>!</div>";
                        }
                    
                        $query = "SELECT *, proposal.description AS description, proposal.picture AS picture
                                  FROM proposal, user 
                                  WHERE proposer_id = user_id 
                                  AND available_positions > 0";

                        if (empty($_GET['search'])) {
                            $result = mysqli_query($conn, $query);
                        } else {
                            $search_query = "%".$search_query."%";
                            $query = $query." AND (proposal.name LIKE ? OR proposal.description LIKE ?)";
                            
                            if(!($stmt = mysqli_prepare($conn, $query))) 
                                throw new Exception("mysqli prepare ".mysqli_errno($conn));

                            if(!mysqli_stmt_bind_param($stmt, "ss", $search_query, $search_query))
                                throw new Exception("mysqli bind param");
                            
                            if(!mysqli_stmt_execute($stmt))
                                throw new Exception("mysqli execute ".mysqli_errno($conn));

                            $result = mysqli_stmt_get_result($stmt);
                        }

                        if(!($result)) 
                            throw new Exception("sql ".mysqli_errno());

                    } catch (Exception $ex) {
                        $error_flag = true;
                        $error_message = $ex->getMessage();
                        //echo $ex->getMessage();
                        //echo $error_flag;
                    }

                    if ($error_flag) {
                        echo "<p id='errmessage'>Errore di accesso al database. Riprova.</p>"; 
                    } else if (mysqli_num_rows($result) == 0) { // Empty result
                        echo "Nessuna proposta disponibile al momento. Torna presto a controllare.";  
                    } else { // Result not empty
                        while($row = mysqli_fetch_assoc($result)) {
                            printProposalInfo($conn, $row, true);
                            echo "<div class=\"d-flex justify-content-between align-items-center\">";
                            if (my_session_is_valid() && $_SESSION['type'] == 'person') {   
                                if($row['proposer_id'] == $_SESSION['userId']) {
                                    echo "<input type='hidden' class='proposal_id' name='proposal_id' value='".$row['id']."'>
                                    <input type=\"button\" class=\"btn btn-sm btn-outline-secondary\" 
                                            name='".$row['id']."' disabled value=\"Non puoi accettare una proposta inserita da te\">";
                                } else {
                                    echo "<form action='php/accept_proposal.php' method='post'>
                                        <input type='hidden' class='proposal_id' name='proposal_id' value='".$row['id']."'>
                                        <input type='submit' class=\"btn btn-sm btn-outline-secondary\" value='Accetta questa proposta'>
                                        </form>";
                                }
                            } else if (my_session_is_valid() && $_SESSION['type'] == 'organization') {
                                echo "<input type='hidden' class='proposal_id' name='proposal_id' value='".$row['id']."'>
                                <input type=\"button\" class=\"btn btn-sm btn-outline-secondary\" 
                                        disabled value=\"Un'organizzazione non può accettare una proposta\">";
                            }
                            echo "</div></div></div>";
                        }                                
                    }         
                ?>
            </div>
        </div>
    </div>

    <!--Profiles-->
    <script src="js/ajax.js"></script>

</body>
</html>