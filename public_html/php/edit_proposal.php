<?php
    include("../../connection.php");
    include("utilities.php");

    $prev_location = "view_my_proposals.php";

    if (empty($_POST) || empty($_POST['proposal_id'])) {
        $_SESSION['message'] = "Si è verificato un errore imprevisto. Riprova.";
        navigateTo($prev_location);
    }

    $proposal_id = intval($_POST['proposal_id']);

    if ($proposal_id < 0) {
        $_SESSION['message'] = "Impossibile modificare la proposta specificata. Riprova.";
        navigateTo($prev_location);
    }

    $con = dbConnect();

    if (!$con) {
        navigateTo($prev_location);
    }

    $result = mysqli_query($con, "SELECT *
                                  FROM proposal
                                  WHERE id = ".$proposal_id." AND proposer_id = ".$user_id);

    if (!$result) { // Query error
        $_SESSION['message'] = "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                Aspetta qualche istante e riprova.";
        navigateTo($prev_location);
    } else if (mysqli_num_rows($result) == 0) { // Empty result
        $_SESSION['message'] = "Proposta non trovata. Riprova.";
        navigateTo($prev_location);
    } else { // Result not empty
        $row = mysqli_fetch_assoc($result);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="../js/form_validation.js"></script>
</head>
<body>
    <a href="index_proposals.php">^ Home</a>
    <?php
        if (isset($_SESSION['message'])) {
            echo "<div>".$_SESSION['message']."</div>";
            unset($_SESSION['message']);
        }
    ?>  
    <form enctype="multipart/form-data" action="receive_edit_proposal.php" onsubmit="return checkData()" method="POST">
        <br>
        Nome
        <input type="text" name="name" value="<?php echo $row['name'] ?>" required>
        <br>
        Descrizione
        <textarea name="description" rows="5" cols="30" required><?php echo $row['description'] ?></textarea>
        <br>
        Immagine
        <!-- This hidden field is used by php to avoid uploading large files.
        Files lager than 4MB are not blocked by this, but upload stops at 4M
        and the file is not sent, thus preventing user from waiting for a file
        that will be rejected server-side.-->
        <input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
        <input type="file" name="picture" accept="image/png, image/jpeg, image/bmp">
        <br>
        Indirizzo
        <input type="text" name="address" value="<?php echo $row['address'] ?>">
        <br>
        Numero volontari richiesti
        <input type="number" name="available_positions" min="1" value="<?php echo $row['available_positions'] ?>" required>
        <br>
        <input type='hidden' name='proposal_id' value='<?php echo $proposal_id ?>'>
        <input type="submit">
    </form>
</body>
</html>