<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // print_r($_POST);
    $config_json = file_get_contents(".config.json");
    $config = json_decode($config_json, true);
    $user = $_POST["user"];
    $sha1passwd = sha1($_POST["password"].$config["sha1_salt"]);
    if (array_key_exists("remember", $_POST)) {
        $remember_me = $_POST["remember"]; // 记住，要记住的值为"on"
    } else {
        $remember_me = "off";
    }
    if ($remember_me == "on") {
        setcookie("znzh_user", $user, time()+3600*24*14);
        setcookie("znzh_passwd", $sha1passwd, time()+3600*24*14);
    } else {
        setcookie("znzh_user", $user, time()+3600);
        setcookie("znzh_passwd", $sha1passwd, time()+3600);
    }
    header("Location: detail.php");
    
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (array_key_exists("operation", $_GET)) {
        if ($_GET["operation"]== "logout"){
            if (array_key_exists("znzh_user", $_COOKIE)) {
                setcookie("znzh_user", $_COOKIE["znzh_user"], time()-1);
            }
            if (array_key_exists("znzh_passwd", $_COOKIE)) {
                setcookie("znzh_passwd",  $_COOKIE["znzh_passwd"], time()-1);
            }
        }
    }
    header("Location: login.php?status=loggedout");
}
?>
