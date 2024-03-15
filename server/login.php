<?php
    include 'setup.php';
    include 'user.class.php';

    $user = new user();

    $response['result'] = false;

    if(isset($_POST['userid'])&&isset($_POST['password'])) {
        if($user->login($_POST['userid'], $_POST['password'])) {
            $response['result'] = true;
        } else {
            $response['result'] = false;
            $response['reason'] = $user->error_msg;
        }
    }

    echo json_encode($response);
?>