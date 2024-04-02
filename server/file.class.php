<?php
    class file{
        public $id = "";
        public $header = "";
        public $name = "";
        public $type = "";
        public $size = 0;
        public $uploaded_by = "";
        public $uploaded_on = "";
        public $deleted_on = "";
        
        public $download_folder = "";
        public $download_link = "";

        public $path = "";
        public $format = array();

        private $db;
        private $user_class;

        public $error_msg = "";

        private $encryptionKey = 'your_secret_key';

        function __construct() {
            if (!class_exists("db")) { include 'db.class.php'; }
            if (!class_exists("user")) { include 'user.class.php'; }

            $this->db = new db();
            $this->user_class = new user();

            $this->clear_delete();
        }

        function set_details($id = "") {
            try {
                $this->id = $id != "" ? $id : $this->id;
                if ($this->id == "") { throw new Exception("[Error] id not assigned"); }
                $data = $this->db->sql_select("files", [], "`id`='$this->id'");
                if (count($data) == 0) { throw new Exception("[Error] id not found"); }
                $this->name = $data[0]['name'];
                $this->header = $data[0]['header'];
                $this->type = $data[0]['type'];
                $this->size = $data[0]['size'];
                $this->uploaded_by = $data[0]['uploaded_by'];
                $this->uploaded_on = $data[0]['uploaded_on'];
                
                return true;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function get_group_folder_details($id) {
            try {
                if ($id == "") { throw new Exception("[Error] id not assigned"); }
                if (!$this->is_group_folder($id)) { throw new Exception("[Error] is not group folder"); }
                $data = $this->db->sql_select("group_folder", [], "`id`='$id'");
                if (count($data) == 0) { throw new Exception("[Error] id not found"); }
                $data[0]['percent'] = $data[0]['usage'] == "0" ? "100" : number_format(($data[0]['usage'] / $data[0]['limit']) * 100);
                return $data[0];
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function exists($id) {
            try {
                if ($id == "") { throw new Exception("[Error] id not assigned"); }
                if (count($this->db->sql_select("files", "id", "`id`='$id'")) == 0) {
                    return false;
                } else {
                    return true;
                }
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function is_file($id) {
            if (count($this->db->sql_select("files", "id", "`id`='$id' AND `type`!='folder'")) == 0) {
                return false;
            } else {
                return true;
            }
        }

        function is_myfile($id) {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Warning] please login first"); }
                if ($id == "") { throw new Exception("[Error] id not assigned"); }
                $uploaded_by = $this->user_class->userid;
                if (count($this->db->sql_select("files", "id", "`id`='$id' AND `uploaded_by`='$uploaded_by'")) == 0) {
                    return false;
                } else {
                    return true;
                }
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function is_folder($id) {
            if (count($this->db->sql_select("files", "id", "`id`='$id' AND `type`='folder'")) == 0) {
                return false;
            } else {
                return true;
            }
        }

        function is_group_folder($id) {
            if (count($this->db->sql_select("group_folder", "id", "`id`='$id'")) == 0) {
                return false;
            } else {
                return true;
            }
        }

        function gen_id() {
            $this->id = sha1($this->user_class->userid . getDateTime());
            return $this->id;
        }

        function create_file($id, $header, $name, $size = 0, $type = "unknown") {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Warning] please login first"); }
                $this->id = $id;
                $this->name = $name;
                $this->header = $header;
                $this->size = $size;
                $this->type = $type;
                if ($this->id == "") { throw new Exception("[Error] id not assigned"); }
                if ($this->name == "") { throw new Exception("[Error] name not assigned"); }
                if ($this->header == "") { throw new Exception("[Error] header not assigned"); }
                $this->uploaded_by  =$this->user_class->userid;
                if (!$this->db->sql_command("INSERT INTO `files` (`id`,`header`,`name`,`size`,`type`,`uploaded_by`) VALUES ('$this->id','$this->header','$this->name','$this->size','$this->type','$this->uploaded_by')")) {
                    throw new Exception("[Error] System Error");
                }

                return $this->header_update_total($header);
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function upload_file($file, $header = "") {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Warning] Please login first"); }
                $this->header = $header != "" ? $header : $this->user_class->userid;
                if ($this->header == "") { throw new Exception("[Error] header not assigned"); }
                $this->name = $file['name'];
                $this->size = $file['size'];
                $this->type = pathinfo($this->name, PATHINFO_EXTENSION);
                
                if (!$this->is_group_folder($this->header) && $this->size > $this->user_class->limit_balance()) { throw new Exception("[Warning] your balance space not enough"); }
                if ($this->is_group_folder($this->header) && $this->size > $this->group_folder_balance($this->header)) { throw new Exception("[Warning] space not enough"); }
                $location = "../file/" . $this->id . ".lcf";

                if (!in_array(strtolower($this->type),$this->set_format())) { throw new Exception("[Error] Invalid format type"); }
                if (!move_uploaded_file($file["tmp_name"], $location)) { throw new Exception("[Error] failed to store file in server"); }
                if (!$this->create_file($this->id, $this->header, $this->name, $this->size, $this->type)) { throw new Exception("[Error] failed to create_file"); }

                return true;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function create_folder($name, $header) {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Warning] please login first"); }
                $this->name = $name;
                $this->header = $header;
                if ($this->name == "") { throw new Exception("[Error] name not assigned"); }
                if ($this->header == "") { throw new Exception("[Error] header not assigned"); }
                if ($this->exists_folder_in_header($name, $header)) { throw new Exception("[Error] folder name is already exists in header"); }
                if ($this->gen_id() == "") { throw new Exception("[Error] gen_id failed to return value"); }
                $this->uploaded_by = $this->user_class->userid;
                return $this->db->sql_command("INSERT INTO `files` (`id`,`header`,`name`,`type`,`uploaded_by`) VALUES ('$this->id','$header','$name','folder','$this->uploaded_by')");
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function create_group_folder($name, $limit) {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Warning] Please login first"); }
                $userid = $this->user_class->userid;
                if ($name == "") { throw new Exception("[Error] name not assigned"); }
                if ($limit == 0) { throw new Exception("[Warning] group folder size cannot be o"); }
                if ($this->user_class->group_folder < $limit) { throw new Exception("[Warning] not enough qouta for create group folder"); }
                $id = $this->gen_id();
                if (!$this->db->sql_command("INSERT INTO `group_folder` (`id`,`name`,`limit`,`created_by`) VALUES ('$id','$name','$limit','$userid')")) { throw new Exception("[Error] SQL Error"); }
                if (!$this->db->sql_command("UPDATE `users` SET `group_folder`=group_folder-$limit WHERE `userid`='$userid'")) { throw new Exception("[Error] SQL Error 2"); }

                return true;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function update_group_folder($id, $name, $limit) {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Warning] Please login first"); }
                if (!$this->owned_group_folder($id)) { throw new Exception("[Warning] not your group folder"); }
                if ($id == "") { throw new Exception("[Error] id not assigned"); }
                if (!$this->is_group_folder($id)) { throw new Exception("[Error] id is not group folder"); }
                if ($name == "") { throw new Exception("[Error] name not assigned"); }
                if (!is_numeric($limit)) { throw new Exception("[Error] limit is not numeric"); }
                $details = $this->db->sql_select("group_folder", [], "`id`='$id'")[0];
                $gap = $limit - $details['limit'];
                $balance = $this->user_class->group_folder - $gap;
                if ($limit < $details['usage']) { throw new Exception("[Error] limit than usage "); }
                if ($this->user_class->group_folder < $gap) { throw new Exception("[Warning] not enough qouta for update group folder"); }
                
                if (!$this->db->sql_command("UPDATE `group_folder` SET `name`='$name', `limit`='$limit' WHERE `id`='$id'")) {
                    throw new Exception("[Error] failed to update table group_folder");
                }

                if (!$this->db->sql_command("UPDATE `users` SET `group_folder`='$balance' WHERE `userid`='" . $this->user_class->userid . "'")) {
                    throw new Exception("[Error] failed to update table users");
                }

                return true; 
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function group_folder_balance($id) {
            try {
                if ($id == "") { throw new Exception("[Error] id not assigned"); }
                if (!$this->is_group_folder($id)) { throw new Exception("[Error] id is not group folder"); }
                $data = $this->db->sql_select("group_folder", ["limit","usage"], "`id`='$id'");
                if (count($data) == 0) { throw new Exception("[Error] id not found"); }

                $balance = $data[0]['limit'] - $data[0]['usage'];
                return $balance;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return 0;
            }
        }

        function header_update_total($header) {
            $total = $this->db->sql_select_total("files", "size", "`header`='$header'");
            if ($this->is_folder($header)) {
                $data = $this->db->sql_select("files","header", "`id`='$header'");
                if (count($data) == 0) {
                    return false;
                } else {
                    $header_header = $data[0]['header'];
                }
                $this->db->sql_command("UPDATE `files` SET `size`='$total' WHERE `id`='$header'");
                return $this->header_update_total($header_header);
            } else if ($this->is_group_folder($header)) {
                return $this->db->sql_command("UPDATE `group_folder` SET `usage`='$total' WHERE `id`='$header'");
            } else {
                return $this->db->sql_command("UPDATE `users` SET `usage`='$total' WHERE `userid`='$header'");
            }
        }

        function open_access_download($id) {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Warning] please login first"); }
                if (!$this->set_details($id)) { throw new Exception("[Error] failed to set details"); }
                $this->gen_download_folder();
                if (!mkdir("../download/".$this->gen_download_folder(), 0755)) { throw new Exception("[Error] failed to create folder"); }
                $dir = "../download/" . $this->download_folder . "/" . $this->name;
                if (copy("../file/".$this->id.".lcf", $dir)) {
                    $this->download_link = "download/" . $this->download_folder . "/" . $this->name;
                    return true;
                } else {
                    throw new Exception("[Error] System Error");
                }
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function gen_download_folder() {
            $this->download_folder = sha1("donwload" . $this->user_class->userid . getDateTime());
            return $this->download_folder;
        }

        function remove_access_download($download_folder = "", $name = "") {
            try {
                $this->download_folder = $download_folder != "" ? $download_folder : $this->download_folder;
                $this->name = $name != "" ? $name : $this->name;
                if ($this->download_folder == "") { throw new Exception("[Error] download_folder not assigned"); }
                if ($this->name == "") { throw new Exception("[Error] name not assigend"); }
                
                $dir_folder = "../download/" . $this->download_folder;
                $dir_file = $dir_folder . "/" . $this->name;
                if (!is_dir($dir_folder)) { throw new Exception("[Error] Folder does not exist"); }
                if (!unlink($dir_file)) { throw new Exception("[Error] failed to delete file"); }

                return rmdir($dir_folder);
            }  catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function find_path($header = "") {
            try {
                if ($header == "") { throw new Exception("[Error] header not assigned"); }
                if ($this->is_folder($header)) {
                    $data = $this->db->sql_select("files","header", "`id`='$header'");
                    return $this->find_path($data[0]['header']) . " > " . "<a href=\"?path=".$header."\">".$this->get_name($header)."</a>";
                } else {
                    return "<a href=\"?path=".$header."\">".$this->get_name($header)."</a>";
                }

            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function get_header($id) {
            try {
                if ($id == "") { throw new Exception("[Error] id not assigned"); }
                if (!$this->is_folder($id)) { throw new Exception("[Error] is not folder"); }
                $data = $this->db->sql_select("files", "header", "`id`='$id'");

                if (count($data) == 0) { throw new Exception("[Error] id not found"); }

                return $data[0]["header"];
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function header_access($header) {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Error] Please login first"); }
                if ($header == "") { throw new Exception("[Error] header not assigned"); }
                if ($this->is_folder($header)) {
                    return $this->header_access($this->get_header($header));
                } else if ($this->is_group_folder($header)) {
                    $data = $this->db->sql_select("group_folder", "id", "`id`='$header' AND `created_by`='" . $this->user_class->userid . "'");
                    $data1 = $this->db->sql_select("user_group_folder", "id", "`id`='$header' AND `userid`='" . $this->user_class->userid . "'");
                    return (count($data) != 0) || (count($data1) != 0);
                } else {
                    return $header == $this->user_class->userid ? true : false;
                }
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function rename($id, $name) {
            try {
                $this->id = $id;
                if ($this->id == "") { throw new Exception("[Error] id not assigned"); }
                if ($name == "") { throw new Exception("[Error] name not assigned"); }
                if (!$this->set_details()) { throw new Exception("[Error] failed to set details"); }

                return $this->db->sql_command("UPDATE `files` SET `name`='$name' WHERE `id`='$this->id'");
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function owned_group_folder($id) {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Warning] Please login first"); }
                $data = $this->db->sql_select("group_folder", "created_by", "`id`='$id'");
                if (count($data) == 0) { throw new Exception("[Error] id not found"); }

                return $this->user_class->userid == $data[0]['created_by'];
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function add_user_group_folder($id, $userid) {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Error] Please login first"); }
                if (!$this->owned_group_folder($id)) { throw new Exception("[Error] not your group folder"); }
                if ($id == "") { throw new Exception("[Error] id not assigned"); }
                if (!$this->is_group_folder($id)) { throw new Exception("[Error] id is not group folder"); }
                if ($userid == "") { throw new Exception("[Error] userid not assigned"); }
                if (!$this->user_class->userid_exists($userid)) { throw new Exception("[Error] userid not exists"); }
                if (count($this->db->sql_select("user_group_folder", "rowid","`id`='$id' AND `userid`='$userid'")) != 0) { throw new Exception("[Error] user already join group folder"); }

                if (!$this->db->sql_command("INSERT INTO `user_group_folder` (`id`,`userid`) VALUES ('$id','$userid')")) { throw new Exception("[Error] SQL Error"); }

                return true;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function remove_user_group_folder($rowid) {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Error] Please login first"); }
                if ($rowid == "") { throw new Exception("[Error] rowid not assigned"); }
                $id = $this->db->sql_select("user_group_folder", "id", "`rowid`='$rowid'")[0]['id'];
                if (!$this->owned_group_folder($id)) { throw new Exception("[Error] not your group folder"); }
                
                if (!$this->db->sql_command("DELETE FROM `user_group_folder` WHERE `rowid`='$rowid'")) { throw new Exception("[Error] SQL Error"); }

                return true;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function get_group_folder_name($id) {
            try {
                if ($id == "") { throw new Exception("[Error] id not assigned"); }
                if (!$this->is_group_folder($id)) { throw new Exception("[Error] is not group folder"); }
                $data = $this->db->sql_select("group_folder", "name", "`id`='$id'");
                if (count($data) == 0) { throw new Exception("[Error] id not found"); }
                
                $name = $data[0]['name'];
                if ($name == "") { throw new Exception("[Error] name failed to assign"); }
                
                return $name;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return "unknown";
            }
        }

        function get_name($id) {
            if ($this->is_folder($id)) {
                $data = $this->db->sql_select("files","name", "`id`='$id'");
            } else if ($this->is_group_folder($id)) {
                $data = $this->db->sql_select("group_folder","name", "`id`='$id'");
            } else {
                $data = [];
            }
            if (count($data) == 0) {
                return $id;
            } else {
                return $data[0]['name'];
            }
        }

        function html_folder($header = "") {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Error] Please login first"); }
                $this->header = $header != "" ? $header : $this->header;
                if ($this->header == "") { throw new Exception("[Error] header not assigned"); }
                if (!$this->header_access($this->header)) { throw new Exception("[Error] Access Denied"); }
                $this->path = $this->find_path($this->header);
                $ret_html = "
                    <tbody>
                        <tr>
                            <th></th>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Type</th>
                            <th>Uploaded On</th>
                            <th></th>
                        </tr>
                ";
                $data = $this->db->sql_select("files", [], "`header`='$this->header' AND `deleted_on` IS NULL ORDER BY (type = 'folder') DESC, name");
                if (count($data) == 0) { $ret_html .= "<tr><td colspan='10'> No File</td></tr>"; }
                foreach ($data as $val) {
                    $val['name'] = $val['type'] == "folder" ? "<a href=\"?path=".$val['id']."\">".$val['name']."</a>" : $val['name'];
                    $ret_html .= "
                        <tr class='tr_file' oncontextmenu=\"open_properties('" . $val['id'] . "');event.preventDefault();\">
                            <td><input type='checkbox'></td>
                            <td>" . $val['name'] . "</td>
                            <td>" . size_as_kb($val['size']) . "</td>
                            <td>" . $val['type'] . "</td>
                            <td>" . convertDateTimeFormat($val['uploaded_on']) . "</td>
                            <td><img src='resources/icons/more_horiz.svg' onclick=\"open_properties('" . $val['id'] . "');\"></td>
                        </tr>
                    ";
                }
                $ret_html .= "</tbody>";
                
                return $ret_html;
            } catch(Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function html_group_folder() {
            try {   
                $ret_html = "";
                if (!$this->user_class->is_login()) { throw new Exception("[Error] Please login first"); }
                $userid = $this->user_class->userid;
                $data = $this->db->sql_select("group_folder", [], "`created_by`='$userid'");
                foreach ($data as $val) {
                    $ret_html .= "
                        <div class='groupfolder' onclick=\"open_folder('" . $val['id'] . "')\" oncontextmenu=\"openEditGroupFolder('" . $val['id'] . "');event.preventDefault();\">
                            <img src='resources/icons/folder_white.svg'>
                            <h4 style='display:inline;'>" . $this->get_group_folder_name($val['id']) . "</h4>
                            <img onclick=\"openEditGroupFolder('" . $val['id'] . "')\" style='float:right;' src='resources/icons/setting_white.svg'>
                        </div>
                    ";
                }

                $data = $this->db->sql_select("user_group_folder", "id", "`userid`='$userid'");
                foreach ($data as $val) {
                    $ret_html .= "
                        <div class='groupfolder' onclick=\"open_folder('" . $val['id'] . "')\">
                            <img src='resources/icons/folder_white.svg'>
                            <h4>" . $this->get_group_folder_name($val['id']) . " </h4>
                        </div>
                    ";
                }

                return $ret_html;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function html_user_group_folder($id) {
            try {
                if ($id == "") { throw new Exception("[Error] id not assigned"); }
                if (!$this->is_group_folder($id)) { throw new Exception("[Error] id is not group folder"); }
                $ret_html = "";
                $data = $this->db->sql_select("user_group_folder", [], "`id`='$id'");
                foreach ($data as $val) {
                    $ret_html .= "
                        <div>" . $val['userid'] . "<img src='resources/icons/delete_white.svg' onclick=\"remove_user_group_folder('" . $val['rowid'] . "')\"></div>
                    ";
                }
                return $ret_html;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function html_my_trash() {
            try {
                if (!$this->user_class->is_login()) { throw new Exception("[Warning] Please login first"); }
                
                $ret_html = "
                    <tbody>
                        <tr>
                            <th></th>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Type</th>
                            <th>Deleted On</th>
                            <th>Restore</th>
                            <th>Delete</th>
                        </tr>
                ";
                $data = $this->db->sql_select("files", [], "`deleted_on` IS NOT NULL AND `uploaded_by`='" . $this->user_class->userid . "'");
                if (count($data) == 0) { $ret_html .= "<tr><td colspan='10'> No File</td></tr>"; }
                foreach ($data as $val) {
                    $val['name'] = $val['type'] == "folder" ? "<a href=\"?path=".$val['id']."\">".$val['name']."</a>" : $val['name'];
                    $ret_html .= "
                        <tr class='tr_file'>
                            <td><input type='checkbox'></td>
                            <td>" . $val['name'] . "</td>
                            <td>" . size_as_kb($val['size']) . "</td>
                            <td>" . $val['type'] . "</td>
                            <td>" . convertDateTimeFormat($val['deleted_on']) . "</td>
                            <td><img src='resources/icons/restore_from_trash.svg' onclick=\"restore('" . $val['id'] . "');\"></td>
                            <td><img src='resources/icons/delete.svg' onclick=\"delete_permanent('" . $val['id'] . "', 'trash');\"></td>
                        </tr>
                    ";
                }
                $ret_html .= "</tbody>";

                return $ret_html;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function clear_delete() {
            foreach ($this->delete_list() as $val) {
                $this->delete_permanent($val);
            }
        }

        function delete_list() {
            try {
                $data = $this->db->sql_select("files", "id", "`deleted_on` <= NOW()");
                if (count($data) == 0) { return [];}
                $arr = array();
                for ($l = 0; $l < count($data); $l++) {
                    $arr[$l] = $data[$l]['id'];
                }

                return $arr;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return [];
            }
        }

        function delete($id) {
            try {
                if ($id == "") { throw new Exception("[Error] id not assigned"); }
                $data = $this->db->sql_select("files", ["header","type","size"], "`id`='$id'");
                if (count($data) == 0) { throw new Exception("[Error] id not found"); }
                if ($data[0]['type'] == "folder") {
                    if ($data[0]['size'] > 0) { throw new Exception("[Error] this folder not empty"); }
                } 

                return $this->db->sql_command("UPDATE `files` SET `deleted_on`=DATE_ADD(CURDATE(), INTERVAL 1 WEEK) WHERE `id`='$id'");
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function delete_permanent($id = "") {
            try {
                if ($id == "") { throw new Exception("[Error] id not assigned"); }
                $data = $this->db->sql_select("files", ["type","header","size"], "`id`='$id'");
                if (count($data) == 0) { throw new Exception("[Error] id not found"); }
                if ($data[0]['type'] != "folder") {
                    if (!$this->is_myfile($id)) { throw new Exception("[Warning] this is not your file"); }
                    $dir = "../file/" . $id . ".lcf";
                    if (!file_exists($dir)) { throw new Exception("[Error] file not found"); }
                    if (!unlink($dir)) { throw new Exception("[Error] failed to delete file"); }
                } else {
                    if ($data[0]['size'] > 0) { throw new Exception("[Error] folder not empty"); }
                }
                if (!$this->db->sql_command("DELETE FROM `files` WHERE `id`='$id'")) {
                    throw new Exception("[Error] System Error");
                }
                return $this->header_update_total($data[0]['header']);
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function restore($id) {
            try {
                if ($id == "") { throw new Exception("[Error] id not assigned"); }
                if (!$this->exists($id)) { throw new Exception("[Error] id not found"); }
                if (!$this->db->sql_command("UPDATE `files` SET `deleted_on`=NULL WHERE `id`='$id'")) { throw new Exception("[Error] SQL Error"); }

                return true;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function add_error_msg($msg) {
            $this->error_msg .= $msg . "<br>";
            return true;
        }

        function get_next_day() {
            date_default_timezone_set("Asia/Kuala_Lumpur");
            $currentDateTime = new DateTime();
            $currentDateTime->add(new DateInterval('P1D'));
            $newDateTimeString = $currentDateTime->format('Y-m-d H:i:s');

            return $newDateTimeString;
        }

        function exists_folder_in_header($name, $header) {
            try {
                if ($name == "") { throw new Exception("[Error] name not assigned"); }
                if ($header == "") { throw new Exception("[Error] header not assigned"); }
                
                if (count($this->db->sql_select("files", "id", "`header`='$header' AND `name`='$name'")) == 1) {
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function set_format() {
            $data = $this->db->sql_select("format", []);
            for ($l = 0; $l < count($data); $l++) {
                $this->format[$l] = $data[$l]['format'];
            }
            return $this->format;
        }

    }