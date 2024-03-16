<?php
    include 'server/setup.php';
    include 'server/user.class.php';
    include 'server/file.class.php';

    $user = new user();
    $file = new file();

    if(!$user->is_login()) {
        header("Location: login.php");
    }

    $header = "";
    if(isset($_GET['path'])) {
        if($_GET['path'] != "") { 
            $header = $_GET['path']; 
        } else {
            $header = $user->userid;
        }
    } else {
        $header = $user->userid;
    }
?>
<html>
<head>
    <title>Dashboard</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <script src="jquery/jquery.js"></script>
    <script src="js/setup.js"></script>
    <link rel="stylesheet" href="css/setup.css">
    <style>
        body {
            display: flex;
            height: 100vh;
            width: 100vw;
            background: slateblue;
        }

        #navbar {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            gap: 5px;
            padding: 10px;
            color: white;
            background: slateblue;
        }

        #navbar > div {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        #navbar > div:first-child {
            flex: 1;
        }

        #navbar > div > div {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 0px;
            padding-bottom: 5px;
        }

        #navbar > div > div:hover {
            border-bottom: 5px solid white;
            border-bottom-right-radius: 3px;
            border-bottom-left-radius: 3px;
            padding-bottom: 0px;
        }

        #navbar > div > div > img {
            height: 30px;
        }

        #navbar > div > div > p {
            display: none;
            margin: 0px;
        }

        #navbar:hover p {
            display: block;
        }

        #container {
            background: white;
            flex: 1;
            border-radius: 30px 0px 0px 30px;
            padding: 30px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            overflow-y: auto;
        }

        #container > div:first-child > div:first-child {
            padding: 15px 20px;
            background: #f0eeff;
            border-radius: 30px;
        }
            
        .table2 {
            width: 100%;
            border-collapse: collapse;
        }

        .table2 > tbody > tr > th {
            border-bottom: 2px solid gray;   
            padding: 10px;         
        }

        .table2 > tbody > tr > td {
            text-align: center;
            padding: 10px;
            margin-top: 10px;
        }

        .table2 > tbody > tr:not(:first-child) > td:first-child {
            border-radius: 5px 0px 0px 5px;
        }

        .table2 > tbody > tr:not(:first-child) > td:last-child {
            border-radius: 0px 5px 5px 0px;
        }

        .table2 tbody > tr:not(:first-child):hover {
            background: #f0eeff;
            border-radius: 5px;
        }

        .table2 > tbody > tr > td:first-child, .table2 > tbody > tr > th:first-child, .table2 > tbody > tr > td:last-child, .table2 > tbody > tr > th:last-child{
            width: 20px;
        }

        .table2 > tbody > tr > td:nth-child(2) {
            white-space: nowrap; 
            overflow: hidden;
            text-align: left;
            text-overflow: ellipsis; 
        }

        .btn_save {
            margin-top: 20px;
            color: white;
            background: dodgerblue;
            padding: 10px 15px;
            border-radius: 7px;
            border: none;
        }

        .btn_cancel {
            background: none;
            margin-top: 20px;
            padding: 10px 15px;
            border-radius: 7px;
            border: none;
        }

        .btn_properties {
            display: flex;
            margin-top: 12px;
            width: 300px;
            align-items : center;
            justify-content: center;
            padding: 7px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            color: white;
            background: #8C7FDA;
        }

        #divLoadingProgress > div > div {
            display: block;
            height: 30px;
            width: 400px;
            border-radius: 5px;
            background: lightgray;
        }

        #divLoadingProgress > div > div > div {
            display: block;
            margin-top: 10px;
            height: 30px;
            width: 200px;
            border-radius: 5px;
            background: mediumseagreen;
        }

        .tab {
            display: none;
        }

        #img_profile {
            display: block;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: auto;
        }

        .searchinput {
            display: flex;
            width: fit-content;
            border: 1px solid gray;
            justify-content: center;
            padding: 5px;
            border-radius: 7px;
        }

        .searchinput > input {
            border: none;
            outline: none;
            font-size: 16px;
        }

        .searchinput > button {
            background: none;
            border: none;
        }

        #div_group_folder {
            margin-top: 15px;
            display: flex;
            gap: 15px 15px;
        }

        .groupfolder {
            width: 150px;
            color: white;
            background: slateblue;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 5px 10px 0 rgba(0, 0, 0, 0.1);
           
        }

        .groupfolder > img:first-child {
            width: 150px;
        }

        .groupfolder > h4 {
            width: 150px;
            white-space: nowrap; 
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn_style1 {
            display: flex;
            align-items: center;
            background: slateblue;
            border: none;
            padding: 7px 10px;
            gap: 5px;
            color: white;
            border-radius: 5px;
        }
        
        #div_user_group_folder {
            margin-top: 10px;
            display: flex;
            width: 400px;
            gap: 10px;
            flex-wrap: wrap;
        }

        #div_user_group_folder > div {
            color: white;
            display: flex;
            width: fit-content;
            gap: 5px;
            align-items: center;
            background: tomato;
            padding: 7px 10px;
            border-radius: 5px;
        }

        .percent_style1 {
            height: 10px;
            background: lightgray;
            border-radius: 5px;
        }

        .percent_style1 > div {
            height: 10px;
            background: mediumseagreen;
            border-radius: 5px;
        }

        .inputfile {
            display: flex;
            align-items: center;
            color: white;
            background: mediumseagreen;
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
        }

        .inputfile > input {
            display: none;
        }

    </style>
</head>
<body>
    <div id="navbar">
        <div>
            <div onclick="location.replace('?')">
                <img src="resources/icons/home.svg">
                <p>Home</p>
            </div>
            <div onclick="opentab('tab_group_folder');">
                <img src="resources/icons/folder_shared_white.svg">
                <p>Group Folder</p>
            </div>
            <div onclick="$('#divCreateFolder').css('display','flex');">
                <img src="resources/icons/create_folder_white.svg">
                <p>Create Folder</p>
            </div>
            <div onclick="$('#divUpload').css('display','flex');">
                <img src="resources/icons/upload_white.svg">
                <p>Upload</p>
            </div>
            <div onclick="alert('This function not available yet!');">
                <img src="resources/icons/file_move_white.svg">
                <p>Move File</p>
            </div>
            <div onclick="opentab('tab_trash')">
                <img src="resources/icons/delete_white.svg">
                <p>Trash Bin</p>
            </div>
            <div onclick="opentab('tab_trash')">
                <img src="resources/icons/help_white.svg">
                <p>Help</p>
            </div>
        </div>
        <div>
            <div onclick="opentab('tab_profile')">
                <img src="resources/icons/profile_white.svg">
                <p>Profile</p>
            </div>
            <div onclick="logout()">
                <img src="resources/icons/logout_white.svg">
                <p>Logout</p>
            </div>
        </div>
    </div>
    <div id="container">
        <div id="tab_file" class="tab">
            <div>Path: <?php print $file->find_path($header); ?></div>
            <table id="table_file" class="table2">
                <?php print $file->html_folder($header); ?>
            </table>
        </div>
        <div id="tab_group_folder" class="tab">
            <h2>Group Folder</h2>
            <button class="btn_style1" onclick="$('#divCreateGroupFolder').css('display','flex');" style="margin-top:10px;"><img src="resources/icons/create_folder_white.svg">Create Group Folder</button>
            <div id="div_group_folder">
                <?php print $file->html_group_folder(); ?>
            </div> 
            <!--<div class="searchinput">
                <input>
                <button><img src="resources/icons/search.svg"></button>
            </div>-->
        </div>
        <div id="tab_trash" class="tab">
            <table id="table_trash" class="table2">
                <?php print $file->html_my_trash(); ?>
            </table>
        </div>
        <div id="tab_profile" class="tab">
            <h2>Profile</h2>
            <div style="display:flex;gap:20px;width:100%">
                <div>
                    <img id="img_profile" src="<?php print $user->url_profile($user->userid); ?>">
                    <label style="display:block;margin:auto;text-align:center">userid</label>
                </div>
                <div style="flex:1;">
                    <div style="display:flex;gap:10px;">
                        <div style="flex:1;">
                            <label class="labelinputbox">Name</label>
                            <input class="inputbox">
                        </div>
                        <div style="flex:1;">
                            <label class="labelinputbox">Phone</label>
                            <input class="inputbox">
                        </div>
                    </div>
                    
                    
                    <label class="labelinputbox">Email</label>
                    <input class="inputbox">
                </div>
                
            </div>
            
            
        </div>
    </div>
    <div id="divUpload" class="overlay">
        <div>
            <h2>Upload File</h2>
            <br>
            <label class="inputfile">
                <img src="resources/icons/upload_white.svg">
                Choice File:&nbsp
                <input id="input_upload" type="file" onclick="$('#spaninputfile01').html('');" onchange="$('#spaninputfile01').html(document.getElementById('input_upload').files[0].name);" />
                <span id="spaninputfile01"></span>
            </label>
            <button class="btn_save" onclick="upload();">Upload</button>
            <button class="btn_cancel" onclick="$('#divUpload').css('display','none');">Cancel</button>
        </div>
    </div>
    <div id="divCreateFolder" class="overlay">
        <div>
            <h2>Create New Folder</h2>
            <label class="labelinputbox">Folder Name</label>
            <input type="text" id="input_create_folder_name" class="inputbox" autocomplete="off">
            <button class="btn_save" onclick="create_folder()">Create</button>
            <button class="btn_cancel" onclick="$('#divCreateFolder').css('display','none');">Cancel</button>
        </div>
    </div>
    <div id="divCreateGroupFolder" class="overlay">
        <div>
            <h2>Create New Group Folder</h2>
            <label class="labelinputbox">Group Folder Name</label>
            <input type="text" id="input_create_group_folder_name" class="inputbox" autocomplete="off">
            <label class="labelinputbox">Limit Size</label>
            <div style="display:flex; gap:10px;">
                <input id="input_create_group_folder_limit_size" type="number" class="inputbox" style="text-align:right;" autocomplete="off">
                <select id="input_create_group_folder_limit_unit" class="inputbox" value='1'>
                    <option value="1">Bytes</option>
                    <option value="1024">KB</option>
                    <option value="1048576">MB</option>
                    <option value="1073741824">GB</option>
                </select>
            </div>
            <button class="btn_save" onclick="create_group_folder()">Create</button>
            <button class="btn_cancel" onclick="$('#divCreateGroupFolder').css('display','none');">Cancel</button>
        </div>
    </div>
    <div id="divEditGroupFolder" class="overlay">
        <div>
            <h2>Edit Group Folder</h2>
            <input type="hidden" id="input_edit_group_folder_id">
            <label class="labelinputbox">Group Folder Name</label>
            <input type="text" id="input_edit_group_folder_name" class="inputbox" autocomplete="off">
            <label class="labelinputbox">Limit Size</label>
            <div style="display:flex; gap:10px;">
                <input id="input_edit_group_folder_limit_size" type="number" class="inputbox" style="text-align:right;" autocomplete="off">
                <select id="input_edit_group_folder_limit_unit" class="inputbox" value='1'>
                    <option value="1">Bytes</option>
                    <option value="1024">KB</option>
                    <option value="1048576">MB</option>
                    <option value="1073741824">GB</option>
                </select>
            </div>
            <br>
            <div style="display:flex;gap:10px;align-items:center;">
                <div class="percent_style1" style="flex:1;"><div id="percent_edit_group_folder"></div></div>
                <h5 id="span_percent_edit_group_folder">55%</h5>
            </div>
            <br>
            <h3>Group User</h3>
            <div style="display:flex;margin-top:10px;gap:10px;">
                <input id="input_add_user_group_folder" list="user_list" class="inputbox" placeholder="User ID to invite" autocomplete="off">
                <button class="btn_save" style="margin:0px;" onclick="add_user_group_folder()">Invite</button>
                <?php print $user->datalist_user("user_list"); ?>
            </div>
            <div id="div_user_group_folder"></div>
            <button class="btn_save" onclick="update_group_folder()">Save Changes</button>
            <button class="btn_cancel" onclick="$('#divEditGroupFolder').css('display','none');">Cancel</button>
        </div>
    </div>
    <div id="divProperties" class="overlay">
        <div>
            <h2>Properties</h2>
            <button class="btn_properties properties_file" onclick="download()"><img src="resources/icons/download_white.svg"> Download</button>
            <button class="btn_properties" onclick="open_divRename()"><img src="resources/icons/edit_white.svg">  Rename</button>
            <button class="btn_properties" onclick="$('#divDelete').css('display','flex');"><img src="resources/icons/delete_white.svg"> Delete</button>
            <button class="btn_properties" onclick="$('#divDeletePermanent').css('display','flex');"><img src="resources/icons/delete_white.svg"> Delete Permanent</button>
            <button class="btn_properties properties_file" onclick=""><img src="resources/icons/file_move_white.svg"> Move File</button>
            <button class="btn_properties" style="color:black;background:lightgray;" onclick="$('#divProperties').css('display','none');">Close</button>
        </div>
    </div>
    <div id="divRename" class="overlay">
        <div>
            <h2>Rename File or Folder</h2>
            <label class="labelinputbox">Rename</label>
            <input id="input_rename" class="inputbox">
            <button class="btn_save" onclick="rename();">Yes</button>
            <button class="btn_cancel" onclick="$('#divRename').css('display','none');">No</button>
        </div>
    </div>
    <div id="divDelete" class="overlay">
        <div>
            <h2>Confirm to Delete</h2>
            <p style="margin:0px;">ID: <span></span></p>
            <button class="btn_save" onclick="delete_file();">Yes</button>
            <button class="btn_cancel" onclick="$('#divDelete').css('display','none');">No</button>
        </div>
    </div>
    <div id="divDeletePermanent" class="overlay">
        <div>
            <h2>Confirm to Delete Permanent</h2>
            <p style="margin:0px;">ID: <span></span></p>
            <button class="btn_save" onclick="delete_permanent();">Yes</button>
            <button class="btn_cancel" onclick="$('#divDeletePermanent').css('display','none');">No</button>
        </div>
    </div>
    <div id="divLoadingProgress" class="overlay">
        <div>
            <h3>Loading</h3>
            <div>
                <div></div>
            </div>
        </div>
    </div>
</body>
<script>
    var id_selected = "";
    var file_details = {};
    var download_folder = "";
    var download_filename = "";
    var header = "<?php print $header; ?>";
    console.log(header);

    opentab('tab_file');

    function opentab(tab) { 
        $(".tab").css("display","none");
        $("#" + tab).css("display","block");

        if (tab == "tab_trash") {
            loadingStart();
            var data = {};
            $.ajax({
                url: 'server/get_trash.php',
                type: 'post',
                data: data,
                dataType: 'JSON',
                success: function(response) {
                    console.log(response);
                    if(response.result) {
                        $("#table_trash").html(response.gui);
                    }
                    loadingEnd();
                }    
            });
        }
    }

    function navbar_show_details() {
        if($("#navbar > div > div > p").css("display") == "none") {
            $("#navbar > div > div > p").show();
        } else {
            $("#navbar > div > div > p").hide();
        }
    }

    function open_folder(path) {
        location.replace("?path=" + path);
    }

    function create_folder() {
        if($("#input_create_folder_name").val() == "") {
            console.log("Please fill in input folder name");
        } else {
            var data = {};
            data['folder'] = $("#input_create_folder_name").val();
            data['header'] = header;
            $.ajax({
                url: 'server/create_folder.php',
                type: 'post',
                data: data,
                dataType: 'JSON',
                success: function(response) {
                    console.log(response);
                    if(response.result) {
                        $("#table_file").html(response.gui);
                        $('#input_create_folder_name').val("");
                        $('#divCreateFolder').css('display','none');
                    }
                }    
            });
        }
    }

    function create_group_folder() {
        loadingStart();
        if($("#input_create_group_folder_name").val() == "") { loadingEnd(); return false; }
        if($("#input_create_group_folder_limit_size").val() == "") { loadingEnd(); return false; }
        var data = {};
        data['name'] = $("#input_create_group_folder_name").val();
        data['limit'] = $("#input_create_group_folder_limit_size").val() * $("#input_create_group_folder_limit_unit").val();
        console.table(data);
        $.ajax({
            url: 'server/create_group_folder.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                console.log(response);
                if(response.result) {
                    $("#div_group_folder").html(response.gui);
                    $('#input_create_group_folder_name').val("");
                    $('#input_create_group_folder_limit_size').val("");
                    $('#input_create_group_folder_limit_unit').val(1);
                    $('#divCreateGroupFolder').css('display','none');
                } else {
                    alert(response.reason);
                }
                loadingEnd();
            }    
        });
    }

    function openEditGroupFolder(id) {
        $('#divEditGroupFolder').css('display','flex');
        var data = {};
        data['id'] = id;
        $.ajax({
            url: 'server/get_group_folder_details.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                console.log(response);
                if(response.result) {
                    var unit = "";
                    var size = 0;
                    if(response.limit < 1024) {
                        size = response.limit
                        unit = "1";
                    } else if(response.limit < 1048576) {
                        size = response.limit / 1024;
                        unit = "1024";
                    } else if(response.limit < 1073741824){
                        size = response.limit / 1048576;
                        unit = "1048576";
                    } else {
                        size = response.limit / 1073741824;
                        unit = "1073741824";
                    }
                    $("#input_edit_group_folder_id").val(response.id);
                    $('#input_edit_group_folder_name').val(response.name);
                    $('#input_edit_group_folder_limit_size').val(size);
                    $('#input_edit_group_folder_limit_unit').val(unit);
                    $("#percent_edit_group_folder").css("width",response.percent + "%");
                    $("#span_percent_edit_group_folder").html(response.percent + "%");
                    $("#div_user_group_folder").html(response.gui_user);
                }
            }
        });
    }

    function update_group_folder() {
        if($("#input_edit_group_folder_id").val() == "") { return false; }
        if($("#input_edit_group_folder_name").val() == "") { return false; }
        if($("#input_edit_group_folder_limit_size").val() == "") { return false; }
        if($("#input_edit_group_folder_limit_unit").val() == "") { return false; }
        var data = {};
        data['id'] = $("#input_edit_group_folder_id").val();
        data['name'] = $("#input_edit_group_folder_name").val();
        data['limit'] = $("#input_edit_group_folder_limit_size").val() * $("#input_edit_group_folder_limit_unit").val();
        $.ajax({
            url: 'server/update_group_folder.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                console.log(response);
                if(response.result) {
                    $("#div_group_folder").html(response.gui);
                    $('#input_edit_group_folder_name').val("");
                    $('#input_edit_group_folder_limit_size').val("");
                    $('#input_edit_group_folder_limit_unit').val(1);
                    $('#divEditGroupFolder').css('display','none');
                } else {
                    alert(response.reason);
                }
            }
        });
    }

    function add_user_group_folder() {
        if($("#input_edit_group_folder_id").val() == "") { return false; }
        if($("#input_add_user_group_folder").val() == "") { return false; }
        var data = {};
        data['id'] = $("#input_edit_group_folder_id").val();
        data['userid'] = $("#input_add_user_group_folder").val();
        $.ajax({
            url: 'server/add_user_group_folder.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                console.log(response);
                if(response.result) {
                    $("#div_user_group_folder").html(response.gui);
                } else {
                    alert(response.reason);
                }
            }
        });
    }

    function remove_user_group_folder(rowid) {
        if(rowid == "") { return false; }
        var data = {};
        data['id'] = $("#input_edit_group_folder_id").val();
        data['rowid'] = rowid;
        $.ajax({
            url: 'server/remove_user_group_folder.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                console.log(response);
                if(response.result) {
                    $("#div_user_group_folder").html(response.gui);
                } else {
                    alert(response.reason);
                }
            }
        });
    }

    function open_divRename() {
        $('#divRename').css('display','flex');
        $("#input_rename").val(file_details.name);
    }

    function rename() {
        if($("#input_rename").val() == "") {
            console.log("please fill in input");
        } else {
            var data = {};
            data['id'] = id_selected;
            data['name'] = $("#input_rename").val();
            $.ajax({
                url: 'server/rename.php',
                type: 'post',
                data: data,
                dataType: 'JSON',
                success: function(response) {
                    console.log(response);
                    if(response.result) {
                        $("#table_file").html(response.gui);
                        $("#input_rename").val("");
                        $('#divRename').css('display','none');
                        $('#divProperties').css('display','none');
                    }
                }    
            });
        }
    }

    function download() {
        var data = {};
        data['id'] = id_selected;
        $.ajax({
            url: 'server/download.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                console.log(response);
                if(response.result) {
                    download_folder = response.folder;
                    download_filename = response.filename;
                    download_file(response.link, response.filename);
                }
            }    
        });
    }

    function upload() {
        if(document.getElementById("input_upload").files.length == 0 ){
            alert("Please choose your file");
        } else {
        
            console.log("Start upload");
            loadingStart();
            var fd = new FormData();
            var files = $('#input_upload')[0].files[0];
            fd.append('file',files);
            fd.append('header', header);

            var xhr = new XMLHttpRequest();

            xhr.upload.addEventListener("progress", function(event) {
                if (event.lengthComputable) {
                    var percentComplete = (event.loaded / event.total) * 100;
                    console.log("Upload progress: " + percentComplete.toFixed(2) + "%");
                    loadingProgressUpdate(percentComplete.toFixed(0));
                }
            });

            xhr.addEventListener("load", function(event) {
                if (xhr.status === 200) {
                    console.log("Upload complete");
                    console.log(xhr.responseText);
                    var response = JSON.parse(xhr.responseText);
                    console.log(response);
                    if(response.result) {
                        $("#table_file").html(response.gui);
                        $('#input_upload').val("");
                        $('#divUpload').css('display','none');
                    } else {
                        alert(response.reason);
                    }
                    
                    loadingEnd();
                } else {
                    console.error("Upload failed with status: " + xhr.status);
                }
            });

            xhr.addEventListener("error", function(event) {
                console.error("Upload failed");
                loadingEnd();
            });

            xhr.addEventListener("abort", function(event) {
                console.log("Upload aborted");
                loadingEnd();
            });

            xhr.open("POST", "server/upload.php", true);
            xhr.send(fd);
        }
    }

    function open_properties(id) {
        $("#divDelete > div > p > span").html(id);
        $("#divDeletePermanent > div > p > span").html(id);
        id_selected = id;
        var data = {};
        data['id'] = id_selected;
        $.ajax({
            url: 'server/get_details.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                console.log(response);
                if(response.result) {
                    file_details = response.details;
                    if(response.details.type == "folder") {
                        $(".properties_folder").css("display", "flex");
                        $(".properties_file").css("display", "none");
                    } else {
                        $(".properties_folder").css("display", "none");
                        $(".properties_file").css("display", "flex");
                    }
                    $("#divProperties").css('display',"flex");
                }
            }    
        });
    }

    function loadingStart() {
        console.log("Loading Start");
        $("#divLoadingProgress").css('display','flex');
        $("#divLoadingProgress > div > div > div").css('width','0px');
    }

    function loadingProgressUpdate(value) {
        value = value * 4;
        $("#divLoadingProgress > div > div > div").css('width', value+'px');
    }

    function loadingEnd() {
        console.log("Loading End");
        $("#divLoadingProgress").css('display','none');
    }

    function logout() {
        location.replace("logout.php");
    }

    function download_file(url, filename) {
        loadingStart();
        fetch(url).then(response => {
            const contentLength = response.headers.get('content-length');
            let downloadedBytes = 0;

            const blobUrl = URL.createObjectURL(new Blob());

            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.style.display = 'none';

            document.body.appendChild(link);

            const reader = response.body.getReader();

            reader.read().then(function process({ done, value }) {
                if(done) {
                    loadingEnd();
                    console.log('Download complete.');
                    URL.revokeObjectURL(blobUrl);
                    download_clear();
                    $('#divProperties').css('display','none');
                    return;
                }

                downloadedBytes += value.byteLength;
                const progress = Math.floor((downloadedBytes / contentLength) * 100);
                loadingProgressUpdate(progress);

                return reader.read().then(process);
            });

            link.click();
        }).catch(error => {
            loadingEnd();
            console.error('Download failed:', error);
        });
    }

    function download_clear() {
        var data = {};
        data['folder'] = download_folder;
        data['filename'] = download_filename;
        $.ajax({
            url: 'server/download_clear.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                console.log(response);
            }    
        });
    }

    function delete_file() {
        loadingStart();
        var data = {};
        data['id'] = id_selected;
        $.ajax({
            url: 'server/delete.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                console.log(response);
                if(response.result) {
                    $("#table_file").html(response.gui);
                    $('#divDelete').css('display','none');
                    $('#divProperties').css('display','none');
                }
                loadingEnd();
            }    
        });
    }

    function delete_permanent(id = "", return_gui = "folder") {
        loadingStart();
        var data = {};
        data['id'] = id != "" ? id : id_selected;
        data['return_gui'] = return_gui;
        $.ajax({
            url: 'server/delete_permanent.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                console.log(response);
                if (response.result) {
                    if (response.return_gui == "folder") {
                        $("#table_file").html(response.gui);
                        $('#divDeletePermanent').css('display','none');
                        $('#divProperties').css('display','none');
                    } else if (response.return_gui == "trash") {
                        $("#table_trash").html(response.gui);
                    }
                }
                loadingEnd();
            }    
        });
    }

    function restore(id) {
        if (id == "") { return false; }
        var data = {};
        data['id'] = id;
        $.ajax({
            url: 'server/restore.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                console.log(response);
                if (response.result) {
                    $("#table_trash").html(response.gui);
                }
                loadingEnd();
            }    
        });
    }

    function report_error(msg) {
        if(msg != "") {
            console.log(msg);
        }
    }

    report_error("<?php print $file->error_msg; ?>");
    report_error("<?php print $user->error_msg; ?>");
</script>
</html>