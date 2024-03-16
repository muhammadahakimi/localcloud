<?php
    class user {
        public $userid = "";
        public $password = "";
        public $password_hash = "";
        public $otp = "";
        public $name = "";
        public $phone = "";
        public $email = "";
        public $picture = "";
        public $usage = 0;
        public $limit = 0;
        public $group_folder = 0;
        public $last_log = "";
        
        public $table_link_userid = ["users","project"];
        
        public $error_msg = "";
        public $reason_login_failed = "";
        
        private $session_id = "";
        private $db;
        
        function __construct() {
            if((session_status() !== PHP_SESSION_ACTIVE) || (session_status() === PHP_SESSION_NONE)) { session_start(); }
            if(!class_exists("db")) { include 'db.class.php'; }
            $this->db = new db();
            $this->session_id = session_id();
            $this->clear_banned();
            if ($this->is_banned()) {
                header("Location: banned.php");
            }
        }

        function set_details($userid = "") {
            try {
                $this->userid = $userid != "" ? $userid : $this->userid;
                if($this->userid == "") { throw new Exception("[Error] userid not assigned"); }
                $data = $this->db->sql_select("users", [], "`userid`='$this->userid'");
                if(count($data) == 0) { throw new Exception("[Error] userid not found"); }
                $this->name = $data[0]['name'];
                $this->phone = $data[0]['phone'];
                $this->email = $data[0]['email'];
                $this->picture = $data[0]['picture'];
                $this->usage = $data[0]['usage'];
                $this->limit = $data[0]['limit'];
                $this->group_folder = $data[0]['group_folder'];
                $this->last_log = $data[0]['last_log'];

                return true;
            } catch(Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function topup_limit($userid, $val) {
            try {
                if (!$this->userid_exists($userid)) { throw new Exception("[Error] userid not exists"); }
                if ($val <= 0) { throw new Exception("[Error] val cannot be less than 0"); }
                $limit = $this->db->sql_select("users", "limit", "`userid`='$userid'")[0]['limit'];
                $limit += $val;
                if (!$this->db->sql_command("UPDATE `users` SET `limit`='$limit' WHERE `userid`='$userid'")) { throw new Exception("[Error] SQL Error"); }
                return true;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function login($userid = "", $password = "") {
            try {
                $this->userid = $userid != "" ? $userid : $this->userid;
                $this->password = $password != "" ? $password : $this->password;
                if($this->userid == "") { throw new Exception("[Error] userid not assigned"); }
                if($this->password == "") { throw new Exception("[Error] password not assigned"); }
                if(!$this->auth()) {
                    $this->reason_login_failed = $this->userid_exists($this->userid) ? "wrong password" : "userid not found";
                    return false;
                }
                $this->update_last_log();
                $this->update_otp();
                $this->set_session();
                
                return true;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }
        
        function logout() {
            $_SESSION['userid'] = "";
            $_SESSION['otp'] = "";
            session_destroy();
        }
        
        function clear_banned() {
            try {
                if(!$this->db->sql_command("DELETE FROM `banned` WHERE `duedate`<=NOW()")) {
                    throw new Exception("[Error] SQL Error");
                }

                return true;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function is_banned() {
            try {
                if (count($this->db->sql_select("banned", [], "`id`='$this->session_id'")) == 1) {
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }
        
        function auth($userid = "", $password = "") {
            try {
                $this->userid = $userid != "" ? $userid : $this->userid;
                $this->password = $password != "" ? $password : $this->password;
                if($this->userid == "") { throw new Exception("[Error] userid not assigned"); }
                if($this->password == "") { throw new Exception("[Error] password not assigned"); }
                $this->password_hash = sha1($this->password);
                if($this->password_hash == "") {
                    throw new Exception("[Error] password_hash failed to assigned");
                }
                $data = $this->db->sql_select("users", ["userid"], "`userid`='$this->userid' AND `password`='$this->password_hash'");
                $result = count($data);
                switch($result) {
                    case 0 :
                        throw new Exception("[Warning] auth failed");
                    case 2 :
                        throw new Exception("[Error] SQL Error");
                    case 1 :
                        return true;
                    default :
                        return false;
                }
            } catch(Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }
        
        function userid_exists($userid) {
            return count($this->db->sql_select("users", ["userid"], "`userid`='$userid'")) == 1 ? true : false;
        }
        
        function update_otp() {
            try {
                if($this->userid == "") { throw new Exception("[Error] userid not assigned"); } 
                $this->otp = sha1($this->last_log);
                if($this->otp == "") { throw new Exception("[Error] otp failed to assigned"); }
                $this->db->sql_command("INSERT INTO `userlog` (`userid`,`computer_name`) VALUES ('$this->userid','client')");
                return $this->db->sql_command("UPDATE `users` SET `otp`='$this->otp' WHERE `userid`='$this->userid'");
            } catch(Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }
        
        function update_password($password = "") {
            try {
                if(!$this->is_login()) { throw new Exception("[Warning] please login first"); }
                $this->password = $password != "" ? $password : $this->password;
                if($this->userid == "") { throw new Exception("[Error] userid not assigned"); }
                if($this->password == "") { throw new Exception("[Error] password not assigned"); }
                $this->password_hash = sha1($this->password);
                if($this->password_hash == "") { throw new Exception("[Error] password_hash failed to assigned"); }
                return $this->db->sql_command("UPDATE `users` SET `password`='$this->password_hash' WHERE `userid`='$this->userid'");
            } catch(Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }
        
        function update_last_log() {
            try {
                if($this->userid == "") {
                    throw new Exception("[Error] userid not assigned");
                } 
                $this->last_log = getDateTime();
                if($this->last_log == "") {
                    throw new Exception("[Error] last_log failed to assigned");
                }
                return $this->db->sql_command("UPDATE `users` SET `last_log`='$this->last_log' WHERE `userid`='$this->userid'");
            } catch(Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }
        
        function set_session() {
            $_SESSION['userid'] = $this->userid;
            $_SESSION['otp'] = $this->otp;
            
            return true;
        }
        
        function is_login() {
            if(!isset($_SESSION['userid']) || !isset($_SESSION['otp'])) {
                return false;
            }
            $this->userid = $_SESSION['userid'];
            $this->otp = $_SESSION['otp'];
            if(!$this->set_details()) { return false; }
            return count($this->db->sql_select("users", ["userid"], "`userid`='$this->userid' AND `otp`='$this->otp'")) == 1 ? true : false;
        }
        
        function permission($permission, $userid = "") {
            try {
                $this->userid = $userid != "" ? $userid : $this->userid;
                if($this->userid == "") { throw new Exception("[Error] userid not assigned"); }
                $result = count($this->db->sql_select("user_permission", ["rowid"], "`userid`='$this->userid' AND `permission`='$permission'"));
                if($result == 2) { throw new Exception("[Error] SQL Error"); }
                if($result == 1) {
                    return true;
                } else {
                    return false;
                }
            } catch(Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }
        
        function give_permission($userid, $permission) {
            try {
                if(!$this->is_login()) { throw new Exception("[Warning] please login first"); }
                if(!$this->permission("GIVE USER PERMISSION")) { throw new Exception("[Warning] you don`t have permission to give user permission"); }
                if($userid == "") { throw new Exception("[Error] parameter userid not assigned"); }
                if($permission == "") { throw new Exception("[Error] parameter permission not assigned"); }
                if($this->permission($permission, $userid)) { return false; }
                return $this->db->sql_command("INSERT INTO `user_permission` (`userid`,`permission`,`given_by`) VALUES ('$userid','$permission','$this->userid')");
            } catch(Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function url_profile($userid) {
            try {
                if($userid == "") { throw new Exception("[Error] userid not assigned"); }
                if(!$this->userid_exists($userid)) { throw new Exception("[Error] userid not found"); }
                $data = $this->db->sql_select("users", "picture", "`userid`='$userid'");
                if(count($data) == 0) { throw new Exception("[Error] userid not found 2"); }
                $picture = $data[0]['picture'];
                if($picture == "") {
                    return "resources/profiles/default.jpg";
                } else {
                    return "resources/profiles/" . $picture;
                }
            } catch(Exception $e) {
                $this->add_error_msg($e->getMessage());
                return "false";
            }
        }

        function limit_balance() {
            try {
                if(!$this->is_login()) { throw new Exception("[Warning] please login first"); }
                $balance = $this->limit - $this->usage;
                if($balance < 0) { throw new Exception("[Error] limit error"); }
                return $balance;
            } catch(Exception $e) {
                $this->add_error_msg($e->getMessage());
                return 0;
            }
        }

        function register() {
            try {
                if($this->userid == "") { throw new Exception("[Error] userid not assigned"); }
                if($this->userid_exists($this->userid)) { throw new Exception("[Warning] userid has taken"); }
                if($this->password == "") { throw new Exception("[Error] password not assigned"); }
                $this->password_hash = sha1($this->password);
                if($this->name == "") { throw new Exception("[Error] name not assigned"); }
                if($this->phone == "") { throw new Exception("[Error] phone not assigned"); }
                if($this->email == "") {  throw new Exception("[Error] email not assigned"); }

                return $this->db->sql_command("INSERT INTO `users` (`userid`,`password`,`name`,`phone`,`email`) VALUES ('$this->userid','$this->password_hash','$this->name','$this->phone','$this->email')");
            } catch(Exception $e) {
                $this->add_error_msg($e->getMessage());
                
                return false;
            }
        }
        
        function option_permission() {
            $ret_html = "";
            $data = $this->db->sql_select("type_user_permission", ["permission"]);
            foreach ($data as $val) {
                $ret_html .= "<option value='" . $val['permission'] ."'>" . $val['permission'] . "</option>";
            }
            return $ret_html;
        }

        function option_user() {
            $ret_html = "";
            $data = $this->db->sql_select("users", ["userid", "name"]);
            foreach ($data as $val) {
                $ret_html .= "<option value='" . $val['userid'] . "'>" . $val['userid'] . " - " . $val['name'] . "</option>";
            }
            return $ret_html;
        }

        function datalist_user($listid) {
            $ret_html = "";
            
            $data = $this->db->sql_select("users", ["userid"]);
            if (count($data) != 0) {
                $ret_html .= "<datalist id='$listid'>";
                foreach ($data as $val) {
                    $ret_html .= "<option value='". $val['userid'] ."'>";
                }
                $ret_html .= "</datalist>";
            }
            return $ret_html;
        }
        
        function add_error_msg($msg) {
            $this->error_msg .= $msg . "<br>";
            return true;
        }
    }
