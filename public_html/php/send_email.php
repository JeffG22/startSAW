<?php
    setlocale(LC_ALL, "it_IT"); // Required to print birth month in italian

    // Import PHPMailer classes into the global namespace
    // These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $acceptor_id = $user_id;

    $query = "SELECT *, proposal.name AS proposal_name, acceptor.description AS acceptor_descr, 
                        acceptor.email AS acceptor_email, proposer.email AS proposer_email,
                        acceptor.display_name AS acceptor_display_name, proposer.display_name AS proposer_display_name
              FROM accepted, person, user AS acceptor, user AS proposer, proposal
              WHERE accepted.proposal_id = proposal.id AND accepted.acceptor_id = acceptor.user_id AND
                    acceptor.user_id = person.id AND proposal.proposer_id = proposer.user_id AND
                    acceptor.user_id = ".$acceptor_id." AND proposal.id = ".$proposal_id;

    if(!($result = mysqli_query($conn, $query)))
        throw new Exception("mysql ".mysqli_errno($conn));  

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
    } else {
        throw new Exception("email");
    }

    $mail = new PHPMailer(true); // Passing `true` enables exceptions
        
    include("../../mail_server_settings.php");

    $mail->SMTPDebug = 0;

    $mail->CharSet = 'UTF-8';

    //Sender
    $mail->setFrom('federico.cassissa@libero.it', 'Hand-aid volontariato');

    //Recipient
    $mail->addAddress($row['proposer_email'], $row['proposer_email']);     // Add a recipient

    //Content
    $body = "Buone notizie, ".$row['proposer_display_name']."!!<br>
            <b>".$row['acceptor_display_name']."</b> 
            ha accettato la tua proposta di volontariato <b>".$row['proposal_name']."</b>.<br>
            <br>
            Ecco qualche altra informazione sul volontario che ha accettato la proposta:<br><br>";

    $body = $body."Sesso";
    if($row['gender'] == "-") {
        $body = $body." non specificato<br>";
    } else {
        $body = $body.": ".$row['gender']."<br>";
    }
    $body = $body."Data di nascita: ".strftime("%e %b %Y", strtotime($row['birthdate']))."<br>";
    $body = $body."Abita in questa provincia: ".$row['province']."<br>";
    if(!empty($row['acceptor_descr'])) {
        $body = $body."Il volontario si descrive così: ".$row['acceptor_descr']."<br>";
    } else {
        $body = $body."Il volontario non ha fornito una descrizione di sé.<br>";
    }
    $body = $body."Puoi contattare il volontario all'indirizzo email <b><a href=\"mailto:".$row['acceptor_email']."\">".$row['acceptor_email']."</a></b>";
    if(!empty($row['phone'])) {
        $body = $body." o al numero <b><a href=\"tel:".$row['phone']."\">".$row['phone']."</a></b>";
    }
    $body = $body.".<br>";
    // strftime requires a timestamp, so we use strtotime to convert from string to a timestamp
    // %e = 1-digit day of the month, %b = abridged month name, %Y = 4digits year

    echo $body;
    $mail->isHTML(true); // Set email format to HTML

    // Since subject is not rendered as HTML, I need to decode special characters such as accents
    $mail->Subject = "Buone notizie, ".html_entity_decode($row['display_name'])."! La tua proposta è stata accettata!";
    $mail->Body    = $body;

    $mail->send();
?>