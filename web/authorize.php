<?php
    function check($user, $sha1passwd) {
        header("Content-type:text/html;charset=utf-8"); 
        $config_json = file_get_contents(".sql_config.json");
        $sqlconfig = json_decode($config_json, true);
        $config_json = file_get_contents(".config.json");
        $config = json_decode($config_json, true);

        $con = mysqli_connect($sqlconfig["host"], $sqlconfig["user"], $sqlconfig["password"], $sqlconfig["database"]);
        mysqli_query($con, "SET NAMES UTF8"); 

        if (!$con) {
          die('Could not connect: '.mysqli_error());
        }

        $user_info = mysqli_fetch_all(mysqli_query($con, "SELECT password FROM `users` WHERE ID = ".$user), MYSQLI_ASSOC);
        if ($user_info == False) {
            header("Location: login.php?error=checkfail"); // 没有用户名，redirect返回login显示“密码/用户名错误”
        } else {
            foreach($user_info as $info) {
                // print_r($info);
                $to_check = $info["password"];
                if ($sha1passwd == sha1($to_check.$config["sha1_salt"])) { // 校验
                    return True;
                } else {
                    return False;
                }
            }
        }
    }
?>