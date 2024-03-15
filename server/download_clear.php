<?php
    include 'setup.php';
    include 'file.class.php';

    $file_class =  new file();

    $response['result'] = false;
    if(isset($_POST['folder']) && isset($_POST['filename'])) {
        $response['result'] = $file_class->remove_access_download($_POST['folder'], $_POST['filename']);
    }

    echo json_encode($response);
?>