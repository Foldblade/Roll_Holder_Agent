<?php require "./header.html" ?>
<div class="container">
    <div class="row">
        <div class="col s12 m6 offset-m3">
            <div class="card">
                <div class="card-image">
                    <img src="https://i.loli.net/2019/03/30/5c9f1cbec014a.jpg">
                    <span class="card-title">登录</span>
                </div>
                <div class="card-content">
                    <form action="make_cookie.php" method="post">
                        <div class="row">
                            <?php
                                if($_SERVER["REQUEST_METHOD"] == "GET") {
                                    if (array_key_exists("error", $_GET)) {
                                        if ($_GET["error"]== "checkfail"){
                                        echo '
                                            <div class="input-field col s10 offset-s1">
                                                <blockquote>错误的用户名/密码</blockquote>
                                            </div>';
                                        } else if ($_GET["error"]== "notloggedin") {
                                            echo '
                                            <div class="input-field col s10 offset-s1">
                                                <blockquote>您尚未登录，请登录后查看。</blockquote>
                                            </div>';
                                        }
                                    } else if (array_key_exists("status", $_GET)) {
                                        if ($_GET["status"]== "loggedout"){
                                        echo '
                                            <div class="input-field col s10 offset-s1">
                                                <blockquote>登出成功</blockquote>
                                            </div>';
                                        }
                                    }
                                };
                            ?>

                            <div class="input-field col s10 offset-s1">
                                <i class="material-icons prefix">account_circle</i>
                                <input name="user" id="user" type="text" class="validate">
                                <label for="user">用户名</label>
                            </div>

                            <div class="input-field col s10 offset-s1">
                                <i class="material-icons prefix">vpn_key</i>
                                <input name="password" id="password" type="password" class="validate">
                                <label for="password">管理密码</label>
                            </div>

                            <div class="input-field col s12 l4 offset-l1">
                                <p class='center-align'>
                                    <label>
                                        <input type="checkbox" name="remember" checked="checked"/>
                                        <span>保持登录状态</span>
                                    </label>
                                </p>
                            </div>

                            <div class="input-field col s12 l2">
                                <p class='center-align'>
                                    注册
                                </p>
                            </div>

                            <div class="input-field col s12 l4">
                                <p class="center-align">
                                    <button class="btn waves-effect red lighten-2 waves-light " type="submit" name="action">登录
                                        <i class="material-icons right">send</i>
                                    </button>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require "./footer.html" ?>