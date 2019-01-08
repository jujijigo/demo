<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>简易留言板 - AnyDemo</title>
    <link href="../common/static/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="../common/static/jquery/jquery-3.3.1.min.js"></script>
    <!--[if lt IE 9]>
    <script src="https://cdn.jsdelivr.net/npm/html5shiv@3.7.3/dist/html5shiv.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/respond.js@1.4.2/dest/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <?php require '../common/navigator.html'; ?>
    <h3 align="center">简易留言板
        <small>AnyDemo.cn</small>
    </h3>
    <div id="d_div">
        <form data-url="./addMsg.php" id="msg_form">
            <textarea class="form-control" rows="4.5" name="content" placeholder="请输入留言内容..."></textarea><br>
            <div class="form-inline">
                <span class="form-group">
                    <label for="username">姓名/昵称:</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="请输入名字/昵称">
                    <span class="form-group">
                </span>
                <span>
                    <label for="captcha">验证码</label>
                    <input type="text" class="form-control" id="captcha" name="captcha" placeholder="请输入验证码">
                    <label for="captcha"><img alt="验证码" src="../common/captcha/show_captcha.php" title="点击刷新验证码" class="captcha_img" onclick="this.src=this.src+'?'" style="cursor: pointer;"></label>
                </span>
            </div>
            <br>
            <input type="submit" class="btn btn-primary" id="msg_btn" value="发表留言">
        </form>
        <div>
            <h4><br>最新留言
                <small>(只显示最新20条)</small>
            </h4>
            <hr>
            <?php
            require '../common/database/connect.php';
            $sql = 'select username,content,created_at from sp_msg ORDER BY created_at DESC LIMIT 20';
            $result = $pdo->query($sql);
            $n = 1;
            foreach ($result as $rows):
            ?>
                <p><span>留言人：<?= $rows['username']; ?></span><span>&ensp;&ensp;</span><span>发表时间：<?= date("Y-m-d H:i:s", $rows['created_at']); ?> #<?=$n++;?> </span></p>
                <p><?= $rows['content']; ?></p>
                <hr>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="./js/events.js"></script>
<script src="../common/static/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="../common/static/layer/layer.js"></script>
</body>
</html>