<?php
    include "setup.php";
    include "user.class.php";

    $user = new user();

    $response['result'] = false;
    
    $response['result'] = $user->update_profile($_FILES['file']);
    if(!$response['result']) {
        $response['reason'] = $user->error_msg;
    }

    echo json_encode($response);
?>