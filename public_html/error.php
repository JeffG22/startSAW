<?php
	require_once("php/handlesession.php");
    my_session_start();
    $text = "";
	if(!empty($_GET["code"])) {
        if ($_GET["code"] == 400)
            $text = "Oops! 400 Bad Request";
        else if ($_GET["code"] == 401)
            $text = "Oops! 401 Unauthorized";
        else if ($_GET["code"] == 403)
            $text = "Oops! 403 Forbidden";
        else if ($_GET["code"] == 500)
            $text = "Oops! 500 Internal Server Error";
        else
            $text = "Oops! 404 Not Found";
    } else
        $text = "Oops! 404 Not Found";
    $message = "<br/>Siamo spiacenti, non è stato possibile soddisfare la richiesta.";
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <title>Error</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="css/error.css">
</head>

<body>
	<div id="ErrorBox">
        <h2><span></span>
        <?php echo $text; ?>
        </h2>
        <h3>
        <?php echo $message; ?>
        </h3>
        <a id="btn" href="index.php"><strong>Torna alla home!</strong></a>
    </div>
</body>
</html>