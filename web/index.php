<?php
    // 登录认证部分
    require "authorize.php";
    if (check($_COOKIE["znzh_user"], $_COOKIE["znzh_passwd"]) != True) {
        header("Location: login.php?error=notloggedin");
    } else {
        header("Location: detail.php");
    }
?>