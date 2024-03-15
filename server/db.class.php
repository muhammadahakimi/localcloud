<?php
    class db {
        public $conn;
        public $error_msg = "";
        
        private $hostname = "localhost";
        private $databasename = "cloud";
        private $username = "root";
        private $password = "";
        
        public $sql_select_result = false;
        public $sql_select_reason = "";
        
        function __construct($hostname = "", $databasename = "", $username = "", $password = "") {
            try {
                $this->hostname = $hostname != "" ? $hostname : $this->hostname;
                $this->databasename = $databasename != "" ? $databasename : $this->databasename;
                $this->usernamme = $username != "" ? $username : $this->username;
                $this->password = $password != "" ? $password : $this->password;
                if($this->hostname == "") { throw new Exception("[Error] hostname not assigned"); }
                if($this->databasename == "") { throw new Exception("[Error] databasename not assigned"); }
                if($this->username == "") { throw new Exception("[Error] username not assigned"); }
                //if($this->password == "") { throw new Exception("[Error] password not assigned"); }
                
                $this->conn = new PDO("mysql:host=$this->hostname; dbname=$this->databasename", $this->username, $this->password);
                $this->conn->exec("SET CHARACTER SET utf8");
            } catch(Exception $e) {
                $this->add_error_msg($e->getMessage());
            }
        }
        
        function sql_command($sql) {
            try {
                if ($this->conn->query($sql) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            } catch(PDOException $e) {
                $this->add_error_msg($e->getMessage());
                return false;
            }
        }
        
        function sql_select($table = "", $column = [], $where = "") {
            $response = [];
            try{
                if($table == "") { throw new PDOException("[Error] data key table not assigned"); }
                if($column == "") { throw new PDOException("[Error] data key column not assigned"); }
                $sqlColumn = "";
                if($where != "") {
                    $where = "WHERE " . $where;
                }
                
                if(is_array($column)) {
                    $first = true;
                    foreach($column as $col) {
                        if($first) {
                            $sqlColumn .= "`" . $col . "`";
                        } else {
                            $sqlColumn .= ", `" . $col . "`";
                        }
                        $first = false;
                    }
                    if(count($column) == 0) {
                        $sqlColumn = "*";
                    }
                } else {
                    $sqlColumn = $column;
                }
                
                $result = $this->conn->query("SELECT $sqlColumn FROM `$table` $where");
    
                if($result !== false) {
                    $cols = $result->columnCount();
                    $count = 0;
                    foreach($result as $row) {
                        $response[$count] = $this->sql_select_clearance($cols, $row);
                        $count++;
                    }
                    $this->sql_select_result = true;
                } else {
                    throw new PDOException("[Error] Wrong SQL");
                }
            }
            catch(PDOException $e) {
                $this->sql_select_result = false;
                $this->sql_select_reason = $e->getMessage();
                $this->add_error_msg($e->getMessage());
                return false;
            }
    
            return $response;
        }
        
        function sql_select_clearance($length, $arr) {
            for($l = 0; $l < $length; $l++) {
                unset($arr[$l]);
            }
            return $arr;
        }

        function sql_select_total($table, $column, $where = "") {
            $response = 0;
            try{
                if($where != "") {
                    $where = "WHERE " . $where;
                }

                $result = $this->conn->query("SELECT SUM($column) AS total FROM `$table` $where");
                if($result !== false) {
                    $cols = $result->columnCount();
                    $count = 0;
                    foreach($result as $row) {
                        $response = $this->sql_select_clearance($cols, $row)['total'];
                        $count++;
                    }
                    $this->sql_select_result = true;
                } else {
                    throw new PDOException("[Error] Wrong SQL");
                }
            }
            catch(PDOException $e) {
                $this->sql_select_result = false;
                $this->sql_select_reason = $e->getMessage();
                $this->add_error_msg($e->getMessage());
                return false;
            }
    
            return $response;
        }
        
        function add_error_msg($msg) {
            $this->error_msg .= $msg . "<br>";
            return true;
        }
    }
?>