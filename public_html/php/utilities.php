<?php
    // Simple function to stop current script and navigate to a specified location
    function navigateTo($location) {
        header("Location: ".$location);
        exit();
    }

    // Prints informations about a specified proposal, passed as an argument in form of a complete
    // row fetched as an associative array from the "proposal" table in the database
    function printUserInfo($row) {
        echo $row['description'];
    }
    

    function printProposalInfo($con, $row) {
        echo "<div class=\"card proposal-card mb-4 box-shadow\">";
        if (!empty($row['picture'])) {
            echo "<img class=\"card-img-top\" src='".$row['picture']."' alt=\"Immagine della proposta\"> ";
        }
        echo "<div class=\"card-body\">";
        echo "<b>".$row['name']."</b><br>\n";
        echo "<i class=\"text-muted\">Inserito in data: ".$row['date_inserted'];
        
            if (!empty($row['display_name'])) {
            echo " da <div class=\"proposer-name\">".$row['display_name'].
                        "<div class=\"card profile-card mb-4 box-shadow profile-overlay\">
                            <div class=\"profile-userpic-card\">
                                <img class=\"userpic-inner\" src=\"media/profile-placeholder.png\" alt=\"Immagine del profilo\">
                            </div>
                            <div class=\"info-container\">
                                <div class=\"profile-usertitle-name\" id=\"hover-name\"></div>
                                <div class=\"profile-usertitle-job\" id=\"hover-role\"></div>
                                <div class=\"hover-desc\"></div>
                            </div>
                        </div>
                       </div>";
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
        return (!empty($value)) ? htmlspecialchars(trim($value)) : ""; 
    }

    function sanitize_url($value) {
        return (!empty($value)) ? filter_var(trim($value), FILTER_SANITIZE_URL) : "";
    }

    function sanitize_email($value) {
        return (!empty($value)) ? filter_var(trim($value), FILTER_SANITIZE_EMAIL) : "";
    }

?>