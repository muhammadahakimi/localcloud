<?php
    session_start();
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

    function convertDateFormat($date) {
        $str = str_split($date);
        $day = $str[8] . $str[9];
        $month = number_format($str[5] . $str[6]);
        $year = $str[0] . $str[1] . $str[2] . $str[3];
        $monthName = ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        return $day . " " . $monthName[$month] . " " . $year;
    }

    function authUser($userid, $password) {
        $data = json_decode(file_get_contents('users.json'), true);
        if(isset($data[$userid])) {
            if(sha1($password) == $data[$userid]) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function isLogin() {
        if(isset($_SESSION['userid'])&&$_SESSION['userid'] != "") {
            return true;
        } else {
            return false;
        }
    }

    function protect() {
        if(!isLogin()) {
            header('Location: login.php');
        }
    }

    function registerSession($userid) {
        $_SESSION['userid'] = $userid;
    }