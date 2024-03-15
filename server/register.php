<?php
    include 'setup.php';
    include 'user.class.php';

    $user = new user();

    $response['result'] = false;
    $response['reason'] = "not found";

    try {
        if(!isset($_POST['userid'])) { throw new Exception("[Error] userid not assigned"); }
        $user->userid = $_POST['userid'];
        if(!isset($_POST['password'])) { throw new Exception("[Error] password not assigned"); }
        $user->password = $_POST['password'];
        if(!isset($_POST['name'])) { throw new Exception("[Error] name not assigned"); }
        $user->name = $_POST['name'];
        if(!isset($_POST['phone'])) { throw new Exception("[Error] phone not assigned"); }
        $user->phone = $_POST['phone'];
        if(!isset($_POST['email'])) { throw new Exception("[Error] email not assigned"); }
        $user->email = $_POST['email'];

        if(!$user->register()) { throw new Exception($user->error_msg); }
        $response['result'] = true;
    } catch(Exception $e) {
        $response['reason'] = $e->getMessage();
        $response['result'] = false;
    }


    echo json_encode($response);
?>