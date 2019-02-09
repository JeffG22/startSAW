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
?>