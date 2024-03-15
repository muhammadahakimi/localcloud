<?php
    include 'setup.php';
    include 'file.class.php';

    $file_class = new file();

    $response['result'] = false;

    if(isset($_POST['id'])) {
        $file_class->set_details($_POST['id']);
        $response['result'] = $file_class->delete($_POST['id']);
        $response['reason'] = $file_class->error_msg;
        if($response['result']) {
            $file_class->set_details($_POST['id']);
            $response['gui'] = $file_class->html_folder();
        }
    }

    echo json_encode($response);
?> 