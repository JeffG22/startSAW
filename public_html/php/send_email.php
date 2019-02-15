<?php
    include("../../connection.php");
    include("utilities.php");
    setlocale(LC_ALL, "it_IT"); // Required to print birth month in italian

    // Import PHPMailer classes into the global namespace
    // These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    //Load Composer's autoloader
    //require 'vendor/autoload.php';

    if (isset($_SESSION['message'])) {
        echo "<div>".$_SESSION['message']."</div>";
        unset($_SESSION['message']);
    }
    
    // TEMPORARY
    if (!empty($_GET['proposal_id'])) {
        $proposal_id = $_GET['proposal_id'];
    } else {
        echo "No proposal_id specified (GET)";
        exit();
    }

    $con = dbConnect();

    if (!$con) {
        echo "Errore nella connessione al database. Potrebbero esserci troppi utenti connessi. 
                Aspetta qualche istante e riprova.";
        exit();
    } 

    $query = "SELECT *
              FROM proposal, user
              WHERE proposer_id = user_id AND id = ".$proposal_id;

    $proposal_res = mysqli_query($con, $query);

    $query = "SELECT *
              FROM accepted, person, user
              WHERE person.id = acceptor_id AND acceptor_id = user_id";

    $acceptor_res = mysqli_query($con, $query);

    if (mysqli_num_rows($proposal_res) == 1 && mysqli_num_rows($acceptor_res) == 1) {
        $proposal = mysqli_fetch_assoc($proposal_res);
        $acceptor = mysqli_fetch_assoc($acceptor_res);
    } else {
        echo "Proposta e/o utente non trovati";
        exit();
    }

        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            include("../../mail_server_settings.php");
    
            $mail->CharSet = 'UTF-8';

            //Recipients
            $mail->setFrom('federico.cassissa@libero.it', 'La tua piattaforma per il volontariato');
            //$mail->addAddress($proposal['email'], $proposal['display_name']);     // Add a recipient

            //Attachments
            if(!empty($acceptor['picture'])) {
                $mail->AddEmbeddedImage("../userpics/".$acceptor['picture'], 'profile_pic');
            }
                
            //Content
            $body = "Buone notizie, ".$proposal['display_name']."!!<br>
                    <b>".$acceptor['name']." ".$acceptor['surname']."</b> 
                    ha accettato la tua proposta di volontariato <b>".$proposal['name']."</b>.<br>
                    <br>
                    Ecco qualche altre informazione sul volontario che ha accettato la proposta:<br><br>";
                
            if(!empty($acceptor['picture'])) {
                $body = $body."<img src=\"cid:profile_pic\" height=\"100px\"><br>"; // src content id defined before with addEmbeddedImage
            }
            $body = $body."Sesso";
            if($acceptor['gender'] == "-") {
                $body = $body." non specificato<br>";
            } else {
                $body = $body.": ".$acceptor['gender']."<br>";
            }
            $body = $body."Data di nascita: ".strftime("%e %b %Y", strtotime($acceptor['birthdate']))."<br>";
            $body = $body."Abita a ".$acceptor['township']." (".$acceptor['province'].")<br>";
            if(!empty($acceptor['description'])) {
                $body = $body."Il volontario si descrive così: ".$acceptor['description']."<br>";
            } else {
                $body = $body."Il volontario non ha fornito una descrizione di sé.<br>";
            }
            $body = $body."Puoi contattare il volontario all'indirizzo email <b><a href=\"mailto:".$acceptor['email']."\">".$acceptor['email']."</a></b>";
            if(!empty($acceptor['phone'])) {
                $body = $body." o al numero <b><a href=\"tel:".$acceptor['phone']."\">".$acceptor['phone']."</a></b>";
            }
            $body = $body.".<br>";
            // strftime requires a timestamp, so we use strtotime to convert from string to a timestamp
            // %e = 1-digit day of the month, %b = abridged month name, %Y = 4digits year

            echo $body;
            $mail->isHTML(true);                                  // Set email format to HTML

            // Since subject is not rendered as HTML, I need to decode special characters such as accents
            $mail->Subject = "Buone notizie, ".html_entity_decode($proposal['display_name'])."! La tua proposta è stata accettata!";
            $mail->Body    = $body;

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
?>