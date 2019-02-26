<?php
    function getUserName($con, $id) {
        $result = mysqli_query($con, "SELECT name, surname
                                      FROM user, person
                                      WHERE user_id = ".$id." AND user_id = person.id");
        if (!$result) {
            return FALSE;
        } else if (mysqli_num_rows($result) != 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['name']." ".$row['surname'];
        } else {    // If no result, it might be an organization
            $result = mysqli_query($con, "SELECT name
                                      FROM user, organization
                                      WHERE user_id = ".$id." AND user_id = organization.id");
            if (!$result) {
                return FALSE;
            } else if (mysqli_num_rows($result) != 0) {
                $row = mysqli_fetch_assoc($result);
                return $row['name'];
            } else {
                return FALSE;
            }
        }
    }

    function getUserDesc($con, $id) {
        $result = mysqli_query($con, "SELECT description
                                      FROM user, person
                                      WHERE user_id = ".$id." AND user_id = person.id");
        if (!$result) {
            return FALSE;
        } else if (mysqli_num_rows($result) != 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['description'];
        } else {    // If no result, it might be an organization
            $result = mysqli_query($con, "SELECT description
                                      FROM user, organization
                                      WHERE user_id = ".$id." AND user_id = organization.id");
            if (!$result) {
                return FALSE;
            } else if (mysqli_num_rows($result) != 0) {
                $row = mysqli_fetch_assoc($result);
                return $row['description'];
            } else {
                return FALSE;
            }
        }
    }


    function getUserRole($con, $id) {
        $result = mysqli_query($con, "SELECT type
                                      FROM user
                                      WHERE user_id = ".$id);
        if (!$result) {
            return FALSE;
        } else if (mysqli_num_rows($result) != 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['type'];
        } 
    }


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
        
            $name = getUserName($con, $row['proposer_id']);
            if (!empty($name)) {
            echo " da <div class=\"proposer-name\">".$name.
                        "<div class=\"card profile-card mb-4 box-shadow profile-overlay\">
                            <div class=\"profile-userpic\">
                                <img class=\"userpic-inner\" src=\"\" alt=\"Immagine del profilo\">
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

        // Temporary hack to allow login
        //session_start();
?>