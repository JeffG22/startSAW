<?php
    // Simple function to stop current script and navigate to a specified location
    function navigateTo($location) {
        header("Location: ".$location);
        exit();
    }

    // Checks if given user_id is a person.
    function isPerson($con, $user_id) {
        $res = mysqli_query($con, "SELECT user_id
                        FROM user
                        WHERE user_id = ".$user_id." AND user_id IN (SELECT id FROM person)");
        if(mysqli_num_rows($res) == 1)
            return TRUE;
        else 
            return FALSE;
    }

    // Prints informations about a specified proposal, passed as an argument in form of a complete
    // row fetched as an associative array from the "proposal" table in the database
    function printUserInfo($con, $row) {
        echo "Descrizione: ".$row['description']."<br>\n";
    }
    

    function printProposalInfo($con, $row, $show_name) {
        echo "<div class=\"card proposal-card mb-4 box-shadow\">";
        if (!empty($row['picture'])) {
            echo "<img class=\"card-img-top\" src='".$row['picture']."' alt=\"Immagine della proposta\"> ";
        }
        echo "<div class=\"card-body\">";
        echo "<b>".$row['name']."</b><br>\n";
        echo "<i class=\"text-muted\">Inserito in data: ".$row['date_inserted'];
        if ($show_name) {
            $name = $row['display_name'];
            echo " da ".$name;
        }
        echo "</i><br>\n";
        echo "<p class=\"card-text\">";
        echo "Descrizione: ".$row['description']."<br>\n";
        echo "Numero di volontari richiesti: <b><i>".$row['available_positions']."</b></i><br>\n";
        if (!empty($row['address'])) {
            echo "Indirizzo: ".$row['address']."<br>\n";
        }
        echo "</p>";
}

    function uploadPicture($fieldname) {
        if(isset($_FILES[$fieldname]) && is_uploaded_file($_FILES[$fieldname]['tmp_name'])) {
            $uploaddir = "./userpics/";
            $filename = (microtime(true)*10000);
            $uploadfile = $uploaddir.$filename.".".pathinfo($_FILES[$fieldname]['name'], PATHINFO_EXTENSION);

            $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_BMP);
            $detectedType = exif_imagetype($_FILES[$fieldname]['tmp_name']);

            if(!in_array($detectedType, $allowedTypes)) {
                $_SESSION['message'] = "Formato file non ammesso";
            } else if ($_FILES[$fieldname]['size'] > 4194304) {
                $_SESSION['message'] = "Dimensione massima superata.\n";
            } else if (move_uploaded_file($_FILES[$fieldname]['tmp_name'], $uploadfile)) {
                return $uploadfile;
            } else {
                $_SESSION['message'] = "Caricamento fallito.\n";
            }
            return false;
        } else
            return false;
    }

    /** ----- Sanitization utility ----- */
    // see: http://php.net/manual/en/filter.filters.sanitize.php
    function sanitize_inputString($value) {
        return htmlspecialchars(trim($value)); 
    }

    function sanitize_url($value) {
        return filter_var(trim($value), FILTER_SANITIZE_URL);
    }

    function sanitize_email($value) {
        return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
    }

        // Temporary hack to allow login
        //session_start();
?>