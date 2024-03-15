<?php
    include 'setup.php';
    include 'file.class.php';

    $file_class = new file();

    $response['result'] = false;
    $response['return_gui'] = isset($_POST['return_gui']) ? $_POST['return_gui'] : "folder";

    if (isset($_POST['id'])) {
        $file_class->set_details($_POST['id']);
        $response['result'] = $file_class->delete_permanent($_POST['id']);
        $response['reason'] = $file_class->error_msg;
        if ($response['result']) {
            $file_class->set_details($_POST['id']);
            if ($response['return_gui'] == "folder") {
                $response['gui'] = $file_class->html_folder();
            } else if ($response['return_gui'] == "trash") {
                $response["gui"] = $file_class->html_my_trash();
            }
                
        }
    }

    echo json_encode($response);
?> 