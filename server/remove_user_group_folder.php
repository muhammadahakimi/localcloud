<?php
    include "setup.php";
    include "file.class.php";
    
    $file = new file();
    $response['result'] = false;

    if(validation()) {
        $response['result'] = $file->remove_user_group_folder($_POST['rowid']);
        if($response['result']) {
            $response['gui'] = $file->html_user_group_folder($_POST['id']);
        } else {
            $response['reason'] = $file->error_msg;
        }
    } else {
        $response['reason'] = "POST data incomplete";
    }

    echo json_encode($response);

    function validation() {
        if(!isset($_POST['rowid'])) {
            return false;
        }
        if(!isset($_POST['id'])) {
            return false;
        }

        return true;
    }

?>