<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>注册/登录 - AnyDemo</title>
    <link href="../common/static/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="../common/static/jquery/jquery-3.3.1.min.js"></script>
    <!--[if lt IE 9]>
    <script src="https://cdn.jsdelivr.net/npm/html5shiv@3.7.3/dist/html5shiv.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/respond.js@1.4.2/dest/respond.min.js"></script>
    <![endif]-->
    <style>
        label {
            font-weight: normal;
        }
    </style>
</head>
<body>
<div class="container" id="d_div">
    <?php session_start();require '../common/navigator.html'; ?>
    <?php if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['username'])) :?>
            <div class="jumbotron">
                <p><span class="glyphicon glyphicon-user"></span> <?=$_SESSION['username'];?>：</p>
            <p>&emsp;&emsp;亲，您已登录！可以使用 高级留言板 和 在线网盘 服务了哦。如需退出，请点击
                <button class="btn btn-default" id="logout_btn" data-url="./logout.php">
                    <span class="glyphicon glyphicon-log-out"></span> 注销
                </button>
            </p>
            </div>
    <?php endif;?>
    <?php if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['username'])) :?>
        <div id="register_div"  class="center-block"  style="width: 360px;">
            <div class="btn-group" role="group" style="margin: auto">
                <button type="button" class="btn btn-default disabled" style="cursor: default">注册</button>
                <button type="button" class="btn btn-default toggle_login">登录</button>
            </div>
            <h3 align="center">用户注册
                <small>AnyDemo.cn</small>
            </h3>
            <form data-url="./addUser.php" class="form-group" id="register_form">
                <div>
                    <label for="username">用户名：</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="用户名长度至少4位">
                </div>
                <div>
                    <label for="email">邮箱：</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="请输入邮箱地址">
                </div>
                <div>
                    <label for="passwd">密码：</label>
                    <input type="password" class="form-control" id="passwd" name="passwd" placeholder="密码长度至少6位">
                </div>
                <div>
                    <label for="repasswd">确认密码：</label>
                    <input type="password" class="form-control" id="repasswd" name="repasswd" placeholder="请再次输入密码">
                </div>
                <div class="form-inline">
                    <label for="captcha">验证码：</label><br>
                    <input type="text" class="form-control" id="captcha" name="captcha" placeholder="请输入验证码">
                    <label for="captcha"><img src="../common/captcha/show_captcha.php" alt="验证码" title="点击刷新验证码" class="captcha_img" onclick="this.src=this.src+'?'" style="cursor: pointer;"></label>
                </div>
                <div align="center">
                    <br />
                    <button type="submit" class="btn btn-primary" id="register_btn">提交注册</button>
                </div>
            </form>
            <p align="center">已有账号？请直接 <a href="javascript:void(0)" class="toggle_login">登录</a></p>
        </div>
        <div id="login_div" class="center-block"  style="width: 360px;display: none;">
            <div class="btn-group" role="group" style="margin: auto">
                <button type="button" class="btn btn-default toggle_register">注册</button>
                <button type="button" class="btn btn-default disabled" style="cursor: default">登录</button>
            </div>
            <h3 align="center">登录
                <small>AnyDemo.cn</small>
            </h3>
            <form data-url="./login.php" class="form-group" id="login_form">
                <div>
                    <label for="username_login">用户名：</label>
                    <input type="text" class="form-control" id="username_login" name="username" placeholder="请输入用户名">
                </div>
                <div>
                    <label for="passwd_login">密码：</label>
                    <input type="password" class="form-control" id="passwd_login" name="passwd" placeholder="请输入密码">
                </div>
                <div class="form-inline">
                    <label for="captcha_login">验证码：</label><br>
                    <input type="text" class="form-control" id="captcha_login" name="captcha" placeholder="请输入验证码">
                    <label for="captcha"><img src="../common/captcha/show_captcha.php" alt="验证码" title="点击刷新验证码" class="captcha_img" onclick="this.src=this.src+'?'" style="cursor: pointer;"></label>
                </div>
                <div align="center">
                    <br />
                    <button type="submit" class="btn btn-primary" id="login_btn">登录</button>
                </div>
            </form>
            <p align="center">如没有账号，请先 <a href="javascript:void(0)" class="toggle_register">注册</a></p>
        </div>
    <?php endif;?>
</div>


<script src="./js/events.js"></script>
<script src="../common/static/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="../common/static/layer/layer.js"></script>
</body>
</html>