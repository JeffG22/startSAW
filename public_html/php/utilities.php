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

    // Simple function to stop current script and navigate to a specified location
    function navigateTo($location) {
        header("location: ".$location);
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
    function printProposalInfo($con, $row) {
                    if (!empty($row['picture'])) {
                        echo "<img src='".$row['picture']."' height='50px'> ";
                    }
                    echo "<b>".$row['name']."</b><br>\n";
                    echo "<i>Inserito in data: ".$row['date_inserted'];
                    if ($name = getUserName($con, $row['proposer_id'])) {
                        echo " da ".$name;
                    }
                    echo "</i><br>\n";
                    echo "Descrizione: ".$row['description']."<br>\n";
                    echo "Numero di volontari richiesti: <b><i>".$row['available_positions']."</b></i><br>\n";
                    if (!empty($row['address'])) {
                        echo "Indirizzo: ".$row['address']."<br>\n";
                    }
    }

    function uploadPicture() {
        if(isset($_FILES['picture']) && is_uploaded_file($_FILES['picture']['tmp_name'])) {
            $uploaddir = "../userpics/";
            $filename = (microtime(true)*10000);
            $uploadfile = $uploaddir.$filename.".".pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);

            $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_JPG, IMAGETYPE_BMP);
            $detectedType = exif_imagetype($_FILES['picture']['tmp_name']);

            if(!in_array($detectedType, $allowedTypes)) {
                $_SESSION['message'] = "Formato file non ammesso";
            } else if ($_FILES['picture']['size'] > 4194304) {
                $_SESSION['message'] = "Dimensione massima superata.\n";
            } else if (move_uploaded_file($_FILES['picture']['tmp_name'], $uploadfile)) {
                $_SESSION['message'] = "File caricato con successo.\n";
                return $uploadfile;
            } else {
                $_SESSION['message'] = "Caricamento fallito.\n";
            }
        }
    }

    /** ----- Sanitization utility ----- */
    
    function sanitize_inputString($value) {
        return htmlspecialchars(trim($value)); 
    }

    // Temporary hack to allow login
    session_start();
    if (isset($_GET['id']))
        $_SESSION['user_id'] = $_GET['id'];
    $user_id = $_SESSION['user_id'];
?>