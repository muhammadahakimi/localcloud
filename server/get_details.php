<?php
    include 'setup.php';
    include 'file.class.php';

    $file_class = new file();

    $response['result'] = false;

    if(isset($_POST['id'])) {
        $response['result'] = $file_class->set_details($_POST['id']);
        if($response['result']) {
            $response['details']['id'] = $file_class->id;
            $response['details']['name'] = $file_class->name;
            $response['details']['header'] = $file_class->header;
            $response['details']['name'] = $file_class->name;
            $response['details']['type'] = $file_class->type;
            $response['details']['size'] = $file_class->size;
            $response['details']['uploaded_by'] = $file_class->uploaded_by;
            $response['details']['uploaded_on'] = $file_class->uploaded_on;
            $response['details']['deleted_on'] = $file_class->deleted_on;
        } else {
            $response['reason'] = $file_class->error_msg;
        }
    }

    echo json_encode($response);
?>