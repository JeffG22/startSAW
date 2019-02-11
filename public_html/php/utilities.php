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
        echo "<br><div>\n";
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
?>