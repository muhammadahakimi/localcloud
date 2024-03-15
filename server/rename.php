<?php
    include 'setup.php';
    include 'file.class.php';

    $file_class = new file();

    $response['result'] = false;

    if(isset($_POST['id'])&&isset($_POST['name'])) {
        $response['result'] = $file_class->rename($_POST['id'], $_POST['name']);
        if($response['result']) {
            $response['gui'] = $file_class->html_folder();
        } else {
            $response['reason'] = $file_class->error_msg;
        }
    }

    echo json_encode($response);
?>