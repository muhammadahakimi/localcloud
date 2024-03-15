<?php
    include 'setup.php';
    include 'user.class.php';
    include 'file.class.php';

    $user = new user();
    $file_class = new file();

    $id = $file_class->gen_id();
    $header = isset($_POST['header']) ? $_POST['header'] : "";

    $response['result'] = false;

    $response['result'] = $file_class->upload_file($_FILES['file'], $header);
    if($response['result']) {
        $response['gui'] = $file_class->html_folder($header);
    } else {
        $response['reason'] = $file_class->error_msg;
    }
    /*try {
        if(!$user->is_login()) { throw new Exception("[Warning] please login first"); }
        if($id == "") { throw new Exception("[Error] id not assigned"); }
        if($header == "") { throw new Exception("[Error] header not assigned"); }
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        if($fileSize > $user->limit_balance()) { throw new Exception("[Warning] your balance not enough"); }

        $location = "../file/" . $id . ".lcf";
        $valid_extensions = $file_class->set_format();
        if(!in_array(strtolower($fileType),$valid_extensions)) {
           throw new Exception("[Error] Invalid format type");
        }
        if(move_uploaded_file($_FILES["file"]["tmp_name"], $location)) {
            $response['result'] = $file_class->create_file($id, $header, $fileName, $fileSize, $fileType);
        } else {
            throw new Exception("[Error] System Error");
        }
        if($response['result']) {
            $response['gui'] = $file_class->html_folder($header);
        } else {
            throw new Exception($file_class->error_msg);
        }
    } catch(Exception $e) {
        $response['reason'] = $e->getMessage();
        $response['result'] = false;
    }*/
    
    echo json_encode($response);
?>