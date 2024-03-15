<?php
  //Database Details
  function DBDetails() {
    $data['hostname'] = "localhost";
    $data['databasename'] = "cloud";
    $data['username'] = "root";
    $data['password'] = "";
    return $data;
  }

  //PDO Connnection
  function DBCon() {
    $DBHost = DBDetails()['hostname'];
    $DBName = DBDetails()['databasename'];
    $DBUser = DBDetails()['username'];
    $DBPass = DBDetails()['password'];
    $conn = new PDO("mysql:host=$DBHost; dbname=$DBName", $DBUser, $DBPass);
    $conn->exec("SET CHARACTER SET utf8");
    return $conn;
  }

  //Select data from table database
  function sqlSelect($data) {
    $conn = DBCon();
      try{
        $sqlTable = $data['table'];
        $sqlColumn = "";
        $sqlWhere = "";
        if(isset($data['where'])) {
          $sqlWhere = "WHERE " . $data['where'];
        }
        $first = true;
        foreach($data['column'] as $col) {
          if($first) {
            $sqlColumn = $sqlColumn . "`" . $col . "`";
          } else {
            $sqlColumn = $sqlColumn . ", `" . $col . "`";
          }
        $first = false;
      }

      $sql = "SELECT $sqlColumn FROM `$sqlTable` $sqlWhere";
      $result = $conn->query($sql);

      if($result !== false) {
        $cols = $result->columnCount();
        $count = 0;
        $response = [];
        foreach($result as $row) {
          $response[$count] = sqlSelectClearance($cols, $row);
          $count++;
        }
      } else {
        $response['result'] = false;
        $response['reason'] = "wrong query";
      }
    } catch(PDOException $e) {
      $response['result'] = false;
      $response['reason'] = $e->getMessage();
    }

    return $response;
  }

  function sqlCommand($sql) {
    $conn = DBCon();
    try {
      if($conn->query($sql) == TRUE) {
        return true;
      } else {
        return false;
      }
    } catch(PDOException $e) {
      $e->getMessage();
      return false;
    }
  }

  function sqlSelectClearance($length, $arr) {
    for($l = 0; $l < $length; $l++) {
      unset($arr[$l]);
    }
    return $arr;
  }

  function numberFormat($value) {
    $str = str_split($value);
    $str1 = [];
    $return = "";
    $num = count($str) - 1;
    for($l = 0; $l < count($str); $l++) {
      $str1[$num] = $str[$l];
      $num--;
    }
    for($l = 0; $l < count($str1); $l++) {
      $return = $return . $str1[$l];
      if(($l == 2)||($l == 5)||($l == 8)) {
        $return = $return . ",";
      }
    }
    $str2 = str_split($return);
    $num = count($str2) - 1;
    for($l = 0; $l < count($str2); $l++) {
      $str3[$num] = $str2[$l];
      $num--;
    }
    $return = "";
    for($l = 0; $l < count($str3); $l++) {
      $return = $return . $str3[$l];
    }
    return $return;
  }

  function getDateTime() {
    date_default_timezone_set("Asia/Kuala_Lumpur");
    return date("Y-m-d H:i:s");
  }

  function generateID($table, $column, $header) {
    include 'DBDetails.php';

    $uid = uniqid($header);

    $sql = "SELECT `$column` FROM `$table` WHERE `$column`='$uid'";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
      return generateID($table, $column, $header);
    } else {
      return $uid;
    }
  }

  function console($data) {
    echo "<br>console>" . $data . "<br>";
  }


  function convertDateFormat($date) {
    $str = str_split($date);
    $day = $str[8] . $str[9];
    $month = number_format($str[5] . $str[6]);
    $year = $str[0] . $str[1] . $str[2] . $str[3];
    $monthName = ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    return $day . " " . $monthName[$month] . " " . $year;
  }

  function convertDateTimeFormat($datetime) {
    $str = str_split($datetime);
    $day = $str[8] . $str[9];
    $month = number_format($str[5] . $str[6]);
    $year = $str[0] . $str[1] . $str[2] . $str[3];

    $hour = $str[11] . $str[12];
    $minute = $str[14] . $str[15];
    $ampm = $hour > 12 ? "PM" : "AM";
    $hour = $hour > 12 ? $hour - 12 : $hour;

    $monthName = ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    return $day . " " . $monthName[$month] . " " . $year . " " . $hour . ":" . $minute  . " " . $ampm;
  }

  function check_post($name) {
    for($l = 0; $l < count($name); $l++) {
        if(!isset($_POST[$name[$l]])) {
            return false;
        }
        if($_POST[$name[$l]] == "") {
            return false;
        }
    }
    return true;
  }


  function userid_exists($userid) {
    if(count(sqlSelect(array("table" => "user", "column" => array("userid"), "where" => "`userid`='$userid'"))) > 0) {
      return true;
    } else {
      return false;
    }
  }

  function ringgit($num) {
    return number_format((float)$num, 2, '.', '');
  }

  function registerSession($userid) {
    session_start();
    $_SESSION['userid'] = $userid;
  }

  function size_as_kb($size) {
    if($size < 1024) {
      return "{$size} Bytes";
    } else if($size < 1048576) {
      $size_kb = round($size/1024);
      return "{$size_kb} KB";
    } else if($size < 1073741824){
      $size_mb = round($size/1048576, 1);
      return "{$size_mb} MB";
    } else {
      $size_gb = round($size/1073741824, 1);
      return "{$size_gb} GB";
    }
  }
?>