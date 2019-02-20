<?php
    include("php/utilities.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="js/inputChecks.js"></script>

      <!--Boostrap-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" 
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
	integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" 
	integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" 
	integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!--Inclusions-->
    <script src="js/include.js"></script> 
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>

    <!--Navbar-->
    <?php
		include("php/navbar.php")
	?>

    <a href="php/index_proposals.php">^ Home</a>
    <?php
        if (isset($_SESSION['message'])) {
            echo "<div>".$_SESSION['message']."</div>";
            unset($_SESSION['message']);
        }
    ?>  
    <form enctype="multipart/form-data" action="php/receive_new_proposal.php" onsubmit="return checkPicture()" method="POST">
    <!-- enctype is necessary to encode picture correctly -->
        <br>
        <label for="name">Nome: </label>&emsp;
        <input type="text" name="name" id="name" required>
        <br>
        <label for="description">Descrizione: </label>&emsp;
        <textarea name="description" rows="5" cols="30" required></textarea>
        <br>
        <label for="upload_picture">Immagine: </label>&emsp;
        <!-- This hidden field is used by php to avoid uploading large files.
        Files lager than 4MB are not blocked by this, but upload stops at 4M
        and the file is not sent, thus preventing user from waiting for a file
        that will be rejected server-side.-->
        <input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
        <input type="file" name="upload_picture" id="upload_picture" accept="image/png, image/jpeg, image/jpg, image/bmp" onchange="checkPicture()">
        <br>
        <label for="address">Indirizzo: </label>&emsp;
        <input type="text" name="address" id="address">
        <br>
        <label for="available_positions">Numero volontari richiesti: </label>&emsp;
        <input type="number" name="available_positions"  id="available_positions" min="1" required>
        <br>
        <input type="submit">
    </form>
</body>
</html>