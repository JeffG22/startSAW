<?php
    session_start();
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
    <a href="index.php">^ Home</a>
    <?php
        if (isset($_SESSION['message'])) {
            echo "<div>".$_SESSION['message']."</div>";
            unset($_SESSION['message']);
        }
    ?>  
    <form enctype="multipart/form-data" action="receive_new_proposal.php" onsubmit="return checkData()" method="POST">
        <br>
        Nome
        <input type="text" name="name" required>
        <br>
        Descrizione
        <textarea name="description" rows="5" cols="30" required></textarea>
        <br>
        Immagine
        <input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
        <input type="file" name="picture" accept="image/png, image/jpeg, image/bmp">
        <br>
        Indirizzo
        <input type="text" name="address">
        <br>
        Numero volontari richiesti
        <input type="number" name="available_positions" required>
        <br>
        <input type="submit">
    </form>
</body>
</html>