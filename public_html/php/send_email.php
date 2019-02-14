<?php
    include("../../connection.php");
    
    include("utilities.php");

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
              FROM accepted, person
              WHERE person.id = acceptor_id";

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
            $mail->addAddress($proposal['email'], $proposal['display_name']);     // Add a recipient
    
            //Attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    
            //Content

            $body = "Buone notizie, ".$proposal['display_name']."!!<br>
                    <br>
                    L'utente <b>".$acceptor['name']." ".$acceptor['surname']."</b> ha accettato la tua proposta di volontariato <b>".$proposal['name']."</b>.";

            $mail->isHTML(true);                                  // Set email format to HTML

            // Since subject is not rendered as HTML, I need to decode special characters such as accents
            $mail->Subject = "Buone notizie, ".html_entity_decode($proposal['display_name'])."! La tua proposta è stata accettata!";
            $mail->Body    = $body;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
?>