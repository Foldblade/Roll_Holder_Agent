<?php require "./header.html" ?>
<?php

    // 登录认证部分
    require "authorize.php";
    if (check($_COOKIE["znzh_user"], $_COOKIE["znzh_passwd"]) != True) {
        header("Location: login.php?error=notloggedin");
    }
?>
<div class="container">


    <div class="row">
        <div class="col s12 m6 offset-m3">
            <div class="card">
                <div class="card-image">
                    <img src="https://i.loli.net/2019/03/30/5c9f717d00729.jpg">
                    <span class="card-title">管理</span>
                </div>
                <div class="card-content">
                    <p>您可以在这里登出您的账号</p>
                </div>
                <div class="card-action">
                    <a href="make_cookie.php?operation=logout"><i class="material-icons left">exit_to_app</i>登出</a>
                </div>
            </div>
        </div>
    </div>
</div>
            
<?php require "./footer.html" ?>

