<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>高级留言板 - AnyDemo</title>
    <link href="../common/static/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="../common/static/jquery/jquery-3.3.1.min.js"></script>
    <!--[if lt IE 9]>
    <script src="https://cdn.jsdelivr.net/npm/html5shiv@3.7.3/dist/html5shiv.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/respond.js@1.4.2/dest/respond.min.js"></script>
    <![endif]-->
    <style>
        .captcha_img {
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <?php require '../common/navigator.html'; ?>
    <h3 align="center">高级留言板
        <small>AnyDemo.cn</small>
    </h3>
    <div id="d_div">
        <?php session_start();if (session_status() == PHP_SESSION_ACTIVE && !empty($_SESSION['username'])) :?>
        <p class="text-right">
            <span class="glyphicon glyphicon-user"></span><strong> <?= $_SESSION['username']; ?></strong> 您好！
            <button class="btn btn-default btn-sm" id="logout_btn" data-url="../users/logout.php">
                <span class="glyphicon glyphicon-log-out"></span> 注销
            </button>
        </p>
        <form data-url="./addMsg.php"  id="msg_form">
            <textarea class="form-control" id="content" name="content" rows="4.5" placeholder="请输入留言内容..."></textarea><br>
            <div class="form-inline">
                <span class="form-group">
                    <label for="captcha">验证码:</label>
                    <input type="text" class="form-control" name="captcha" id="captcha" placeholder="请输入验证码" />
                    <label for="captcha"><img alt="验证码" src="../common/captcha/show_captcha.php" title="点击刷新验证码" class="captcha_img" onclick="this.src=this.src+'?'" ></label>
                    <input id="hiddenText" type="text" style="display:none" />
                </span>
            </div><br />
            <button class="btn btn-primary" type="submit" id="msg_btn">发表留言</button>
        </form><br>
        <?php else: ?>
            <textarea class="form-control" id="content" rows="4.5" readonly placeholder="需要注册登录才能使用高级留言板服务~"></textarea>
            <p></p>
            <div class="panel panel-default">
                <div class="panel-heading"><b>登录</b></div>
                <div class="panel-body">
                    <form class="form-group form-inline" id="login_form" data-url="../users/login.php">
                        <label for="username">用户名：</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="请输入用户名">
                        <span>&emsp;</span><label for="passwd">密码：</label>
                        <input type="password" class="form-control" id="passwd" name="passwd" placeholder="请输入密码">
                        <span>&emsp;</span><label for="captcha">验证码：</label>
                        <input type="text" class="form-control" id="captcha" name="captcha" placeholder="请输入验证码">
                        <label for="captcha"><img alt="验证码" src="../common/captcha/show_captcha.php" title="点击刷新验证码" class="captcha_img" onclick="this.src=this.src+'?'" ></label>
                        <span>&emsp;&emsp;</span><input type="submit" class="btn btn-primary" id="login_btn" value="确认登录">
                    </form>
                </div>
                <div class="panel-footer" align="right">如没有账号，请点击 <a href="../users/index.php">注册</a></div>
            </div>
        <?php endif; ?>
        <div>
            <?php require './showMsg.php'; ?>
            <h4>最新留言</h4>
            <?php foreach ($result as $rows): ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p><span>#<?= $rows['id'] ?>&ensp;&ensp;留言人：<?= $rows['username']; ?>&ensp;&ensp;发表时间：<?= date("Y-m-d H:i:s", $rows['created_at']); ?></span></p>
                        <p><?= $rows['content']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <div align="center">
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php
                        if ($currentPage == 1) {
                            echo "<li class='disabled'><span>首页</span></li>";
                        } else {
                            echo "<li><a href='?page=1'>首页</a></li>";
                        }

                        if ($prePage == $currentPage) {
                            echo '<li class="disabled"><span>&laquo;</span></li>';
                        } else {
                            echo "<li><a href=\"?page=$prePage\" aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>";
                        }

                        if (isset($showPages)) {
                            foreach ($showPages as $showPage) {
                                if ($showPage == $currentPage) {
                                    echo "<li class='active'><span>$showPage</a></li>";
                                } else {
                                    echo "<li><a href=\"?page=$showPage\">$showPage</a></li>";
                                }
                            }
                        }

                        if ($nextPage == $currentPage) {
                            echo "<li  class='disabled'><span>&raquo;</span></li>";
                        } else {
                            echo "<li><a href=\"?page=$nextPage\" aria-label=\"Next\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
                        }

                        if ($currentPage == $totalPages || !isset($showPages)) {
                            echo "<li class='disabled'><span>尾页</span></li>";
                        } else {
                            echo "<li><a href=\"?page=$totalPages\">尾页</a></li>";
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script src="./js/events.js"></script>
<script src="../common/static/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="../common/static/layer/layer.js"></script>
</body>
</html>