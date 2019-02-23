<?php
    include_once("php/utilities.php");
    include_once("php/handlesession.php");
    include_once("../connection.php");
    
    my_session_start();

    // If a user is not logged in and lands on this page, redirect to login
    if (my_session_is_valid())
        $user_id = $_SESSION['userId'];

    if (!empty($_GET['search'])) {
        $search_query = sanitize_inputString($_GET['search']);
    } else if (isset($_GET['search'])) {    // If empty search query, removes "search=" from url
        navigateTo("browse_proposals.php");
    } else {
        $search_query = "";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Proposte di volontariato</title>
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
</head>
<body>
    <!--Header/Navbar-->
    <?php
		include("php/navbar.php");
	?>
	<!--Header/Navbar-->


    <h4>Proposte di volontariato disponibili</h4>

    <form action = "browse_proposals.php" method="GET">
        <input type="search" name="search" id="searchbox" value="<?php echo $search_query ?>">
        <input type="submit" value="Cerca">
        <input type="submit" value="Vedi tutte" onclick="document.getElementById('searchbox').value = '';">
    </form>

    <?php
    
        if (isset($_SESSION['message'])) {
            echo "<div>".$_SESSION['message']."</div>";
            unset($_SESSION['message']);
        }

        try {
            if(!($conn = dbConnect()))
                throw new Exception("sql ".mysqli_connect_error());

            if (!my_session_is_valid()) {
                echo "<div>Esplora liberamente le proposte disponibili. Per accettare una proposta, <a href=\"login.php\">effettua il login</a>!</div>";
            } else if ($_SESSION['type'] == 'organization') {
                echo $_SESSION['type'];
                echo "<div>Non puoi accettare proposte perché hai eseguito il login come associazione. Per accettare una proposta, <a href=\"index.php\">effettua il login come utente</a>!</div>";
            }
        
            $query = "SELECT * FROM proposal WHERE available_positions > 0";

            if (empty($_GET['search'])) {
                $result = mysqli_query($conn, $query);
            } else {
                $search_query = "%".$search_query."%";
                $query = $query." AND (name LIKE ? OR description LIKE ?)";
                
                if(!($stmt = mysqli_prepare($conn, $query))) 
                    throw new Exception("mysqli prepare".mysqli_errno($conn));

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
            
        if (mysqli_num_rows($result) == 0) { // Empty result
            echo "Nessuna proposta disponibile al momento. Torna presto a controllare.";  
        } else { // Result not empty
            while($row = mysqli_fetch_assoc($result)) {
                echo "<br><div>\n";
                printProposalInfo($conn, $row);

                if (my_session_is_valid() && $_SESSION['type'] == 'person') {   
                    if($row['proposer_id'] == $_SESSION['userId']) {
                        echo "<input type=\"button\" disabled value=\"Non puoi accettare una proposta inserita da te\">";
                    } else {
                        echo "<form action='php/accept_proposal.php' method='post'>
                            <input type='hidden' name='proposal_id' value='".$row['id']."'>
                            <input type='submit' value='Accetta questa proposta'>
                            </form>";
                    }
                }

                echo "</div>";
            }                                
        }         
        
    ?>
</body>
</html>