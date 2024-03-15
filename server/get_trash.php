<?php
    include "setup.php";
    include "file.class.php";

    $file = new file();

    $response['result'] = true;
    $response['gui'] = $file->html_my_trash();

    echo json_encode($response);
?>