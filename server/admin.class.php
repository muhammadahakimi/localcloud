<?php
    class admin {
        
        public $list = array();
        public $folder = "";

        private $db;
        private $user_class;
        private $file_class;
        private $password = "";
        private $password_enc = sha1("AjJtcfvz");

        public $error_msg = "";

        function __construct() {
            if((session_status() !== PHP_SESSION_ACTIVE) || (session_status() === PHP_SESSION_NONE)) { session_start(); }
            if (!class_exists("db")) { include "db.class.php"; }
            if (!class_exists("user")) { include "user.class.php"; }
            if (!class_exists("file")) { include "file.class.php"; }

            $this->db = new db();
            $this->user_class = new user();
            $this->file_class = new file();
        }

        function file_list($path) {
            try {
                if ($path == "") { throw new Exception("[Error] path not assigned"); }
                $data = scandir($path);
                foreach ($data as $val) {
                    if (($val != ".") && ($val != "..")) {
                        $this->list[] = $val;
                    }
                }

                return $this->list;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function unknown_file() {
            $this->set_folder("file");
            $this->set_folder("../file");
            $this->file_list($this->folder);
            foreach ($this->list as $val) {
                if (!$this->file_class->is_file($this->file_id_format($val))) {

                }
            }
        }

        function set_folder($folder) {
            if (is_dir($folder)) {
                $this->folder = $folder;
                return true;
            } else {
                return false;
            }
        }

        function file_id_format($id) {
            $total = count(str_split($id));
            return substr($id, 0, ($total - 4));
        }

        function login($password) {
            try {
                $this->password = $password;
                if ($this->password == "") { throw new Exception("[Error] password not assigned"); }
                if (!$this->auth()) { throw new Exception("[Error] authentication failed"); }
                $session_id = session_id();
                $otp = sha1(getDateTime());
                if (!$this->db->sql_command("INSERT INTO `admin` (`session_id`,`otp`) VALUES ('$session_id','$otp')")) { throw new Exception("[Error] SQL Error"); }

                return true;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function is_login() {
            try {
                if ((session_status() !== PHP_SESSION_ACTIVE) || (session_status() === PHP_SESSION_NONE)) { throw new Exception("[Error] session not start"); }
                if (!isset($_SESSION['admin_otp'])) { throw new Exception("[Error] session admin_otp not assigned"); }
                $data = $this->db->sql_select("admin", [], "ORDER BY datetime DESC LIMIT 1;");
                if (count($data) == 0) { throw new Exception("[Error] not found"); }
                if (session_id() != $data[0]['session_id']) { throw new Exception("[Error] session id not match"); }
                if ($_SESSION['admin_otp'] != $data[0]['otp']) { throw new Exception("[Error] otp not match"); }

                return true;
            } catch (Exception $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }

        function auth() {
            return sha1($this->password) == $this->password_enc;
        }

        function add_error_msg($msg) {
            $this->error_msg .= $msg . "<br>";
            return true;
        }
    }

?>