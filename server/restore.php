<?php
    include "setup.php";
    include "file.class.php";

    $file = new file();

    $response['result'] = false;

    if (validation()) {
        $response['result'] = $file->restore($_POST['id']);
        if ($response['result']) {
            $response['gui'] = $file->html_my_trash();
        } else {
            $response['reason'] = $file->error_msg;
        }
    } else {
        $response['reason'] = "POST data incomplete";
    }

    echo json_encode($response);

    function validation() {
        if (!isset($_POST['id'])) {
            return false;
        }

        return true;
    }
?>