<?php
    include 'setup.php';
    protect();

    if(isset($_GET['delete'])&&$_SESSION['userid'] == "hakimi") {
        deleteFile($_GET['delete']);
        header('Location: index.php');
    } else if(isset($_GET['delete'])) {
        header('Location: index.php?alert=Access Denied');
    }
?>

<html>
<head>
    <title>Cloud</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0px;
        }

        .margin-none {
            margin: 0px;
        }

        table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            border-collapse:separate;
            border-spacing:0 10px;
        }

        table td, table th {
            padding: 10px;
        }

        td{ 
            border-right:hidden;
        }

        tr {
            margin-top: 10px;
            padding: 15px;
            background: white;
        }

        table tr:hover {
            background-color: #ddd;
        }

        .text-center {
            text-align: center;
        }

        .btn-download {
            display: block;
            width: fit-content;
            margin: auto;
            height: 18px;
            opacity: 30%;
        }

        .btn-download:hover {
            opacity: 100%;
        }

        #btn_upload {
            display: block;
            width: fit-content;
            text-decoration: none;
            color: white;
            background: MediumSeaGreen;
            padding: 5px 10px;
            border-radius: 5px;
            margin : 15px 0px;
        }

        .btn-refresh {
            height: 14px;
            opacity: 40%;
        }

        .btn-refresh:hover {
            opacity: 100%;
        }

        #container {
            height: 100vh;
            display: flex;
            justify-content: center; /* align item horizontally */
        }

        #nav {
            color: white;
            background: #06367a;
            padding: 20px;
        }

        #main {
            padding: 20px;
            flex: 1;
            background: #ebf2fc;
        }

        .div-list {
            padding: 10px;
            border-radius: 10px;
            background: white;
        }

        .btn-nav {
            color: #45699d;
            font-weight: bold;
            margin-top: 15px;
            display: block;
            text-decoration: none;
            padding: 10px 15px;
            background: white;
            border-radius: 10px;
        }

        .index-list {
            color: white;
            background: #6663fe;
            padding: 5px;
            border-radius: 5px;
            min-width: 19px;
        }
    </style>
</head>
<body>
    <div id="container">
        <div id="nav">
            <h1 class="margin-none">Local Cloud</h1>
            <h2 class="margin-none">(<?php echo $_SERVER['SERVER_ADDR']; ?>)</h2>
            <a class="btn-nav" href=""><img class='btn-refresh' src='logo_refresh.png'> Refresh</a>
            <a class="btn-nav" href="form.php">Upload File</a>
            <a class="btn-nav" href="form.php">Manage User</a>
            <a class="btn-nav" href="logout.php">Logout</a>
        </div>
        <div id="main">
            <h2 style="margin-top:0px;color: #45699d;">Files List:</h2>
            <table>
                <?php 
                    for($l = 0; $l < count($data); $l++) {
                        echo "<tr><td class='text-center' style='border-radius:10px 0px 0px 10px'><div class='index-list''>" . ($l + 1) . "</div></td><td>" . $data[$l]['filename'] . "</td><td class='text-center'>" . $data[$l]['type'] . "</td><td class='text-center'>" . $data[$l]['size'] . "</td><td class='text-center'>" . $data[$l]['last_access'] . "</td><td><a href='index.php?delete=" . $data[$l]['filename'] ."'><img class='btn-download' src='logo_delete.png'></a></td><td style='border-radius:0px 10px 10px 0px'><a href='" . $data[$l]['directory'] ."' download><img class='btn-download' src='logo_download.png'></a></td></tr>";                   } 
                ?>
            </table>
        </div>
    </div>
    <script>
        <?php
            if(isset($_GET['alert'])) {
                echo "alert('" . $_GET['alert'] . "');";
            }
        ?>
    </script>
</body>
</html>