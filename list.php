<?php
    include "server/setup.php";
    include "server/file.class.php";

    $file = new file();
    $dir    = 'file';
    $files = scandir($dir);

    $option = "";
    $del = array();

    foreach($files as $val) {
        if(!$file->is_file(id_format($val)) && $val != "." && $val != "..") {
            $dir = "./file/" . $val;
            unlink($dir);
            $del[] = $val;
            $option .= "<option>" . $val . "</option>";
        }
    }

    function id_format($id) {
        $total = count(str_split($id));

        return substr($id, 0, ($total - 4));
    }
?>

<html>
<head>
</head>
<body>
<h2>Total: <?php print count($files); ?></h2>
<h2>Deleted: <?php print count($del); ?></h2>
<select>
 <?php echo $option; ?>
</select>
</body>
</html>

<?php print $file->error_msg; ?>