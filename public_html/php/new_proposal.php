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
    <script src="../js/inputChecks.js"></script>
</head>
<body>
    <a href="index_proposals.php">^ Home</a>
    <?php
        if (isset($_SESSION['message'])) {
            echo "<div>".$_SESSION['message']."</div>";
            unset($_SESSION['message']);
        }
    ?>  
    <form enctype="multipart/form-data" action="receive_new_proposal.php" onsubmit="return checkPicture()" method="POST">
        <br>
        Nome
        <input type="text" name="name" required>
        <br>
        Descrizione
        <textarea name="description" rows="5" cols="30" required></textarea>
        <br>
        Immagine
        <!-- This hidden field is used by php to avoid uploading large files.
        Files lager than 4MB are not blocked by this, but upload stops at 4M
        and the file is not sent, thus preventing user from waiting for a file
        that will be rejected server-side.-->
        <input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
        <input type="file" name="picture" id="upload_picture" accept="image/png, image/jpeg, image/jpg, image/bmp" onchange="checkPicture()">
        <br>
        Indirizzo
        <input type="text" name="address">
        <br>
        Numero volontari richiesti
        <input type="number" name="available_positions" min="1" required>
        <br>
        <input type="submit">
    </form>
</body>
</html>