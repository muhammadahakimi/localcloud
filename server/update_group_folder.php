<?php
    include "setup.php";
    include "file.class.php";

    $file = new file();
    
    $response['result'] = false;
    
    if(validation()) {
        $response['result'] = $file->update_group_folder($_POST['id'], $_POST['name'], $_POST['limit']);
        if($response['result']) {
            $response['gui'] = $file->html_group_folder();
        } else {
            $response['reason'] = $file->error_msg; 
        }
    } else {
        $response['reason'] = "POST data incomplete";
    }

    echo json_encode($response);

    function validation() {
        if(!isset($_POST['id'])) { return false; }
        if(!isset($_POST['name'])) { return false; }
        if(!isset($_POST['limit'])) { return false; }

        return true;
    }
?>