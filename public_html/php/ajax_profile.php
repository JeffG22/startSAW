<?php
    require_once("../../connection.php");
    require_once("utilities.php");
    $conn = dbConnect();    
    $id = intval($_POST['id']);
    $query1 = "SELECT user.user_id, user.type, user.description, user.picture, user.display_name
                FROM user, proposal
                WHERE proposal.proposer_id = user.user_id 
                AND proposal.id =".$id;
    $res = mysqli_query($conn, $query1);
    $rev = mysqli_fetch_assoc($res);
    if(mysqli_num_rows($res) == 1)
        echo json_encode($rev);
?>