<?php
    require_once("../../connection.php");
    require_once("utilities.php");
    $conn = dbConnect();    
    $id = sanitize_inputString($_POST['id']);
    $query1 = "SELECT user.user_id, user.type
                FROM user, proposal
                WHERE proposal.proposer_id = user.user_id 
                AND proposal.id =".$id." GROUP BY user.user_id";
    //$res = mysqli_query($conn, "SELECT id FROM proposal WHERE id =".$id);
    $res = mysqli_query($conn, $query1);
    $rev = mysqli_fetch_array($res, MYSQLI_NUM);
    $new_id = $rev[0];
    //if($rev[1] == 'person') {
        $re = mysqli_query($conn, "SELECT display_name, 
                                          type, picture,
                                          description
                                   FROM user
                                   WHERE user_id =".$new_id);
    /*} else {
        $re = mysqli_query($conn, "SELECT display_name, 
                                          user.type, organization.picture,
                                          organization.description
                                   FROM user, organization
                                   WHERE organization.id = user.user_id,
                                   AND organization.id =".$new_id);
    }*/
    $rex = mysqli_fetch_array($re, MYSQLI_NUM);
    if(mysqli_num_rows($re) == 1)
        echo json_encode($rex);
?>