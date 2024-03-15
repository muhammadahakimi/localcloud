<?php
    include "setup.php";
    include "file.class.php";

    $file = new file();

    if(validation()) {
        $response = $file->get_group_folder_details($_POST['id']);
        if($response != false) {
            $response['result'] = true;
            $response['gui_user'] = $file->html_user_group_folder($_POST['id']);
        } else {
            $response['result'] = false;
            $response['reason'] = $file->error_msg;
        }
        $response['id'] = $_POST['id'];
    } else {
        $response['result'] = false;
        $response['reason'] = "POST data incomplete";
    }

    echo json_encode($response);

    function validation() {
        if(!isset($_POST['id'])) {
            return false;
        }

        return true;
    }
?>