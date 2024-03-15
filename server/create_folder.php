<?php
    include 'setup.php';
    include 'file.class.php';

    $file_class = new file();
    
    $response['result'] = false;

    if(isset($_POST['folder'])&&isset($_POST['header'])) {
        $response['result'] = $file_class->create_folder($_POST['folder'], $_POST['header']);
        if($response['result']) {
            $response['gui'] = $file_class->html_folder();
        } else {
            $response['reason'] = $file_class->error_msg;
        }
    }

    echo json_encode($response);
?>