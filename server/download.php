<?php
    include 'setup.php';
    include 'file.class.php';

    $file_class = new file();

    $response['result'] = false;
    if(isset($_POST['id'])) {
        $response['result'] = $file_class->open_access_download($_POST['id']);
        $response['folder'] = $file_class->download_folder;
        $response['link'] = $file_class->download_link;
        $response['filename'] = $file_class->name;
    }

    echo json_encode($response);
?>