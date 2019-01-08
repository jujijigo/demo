<?php
session_start();

require './lib/dir.func.php';
require './lib/file.func.php';

// 接收的数据
$dirName = isset($_REQUEST['path']) && $_REQUEST['path'] != '.' ? $_REQUEST['path'] : '';
if (preg_match('/\\.\\./', $dirName)) {
    $dirName = '';
}
$act = isset($_POST['act']) ? $_POST['act'] : '';

$homePath = rtrim(__DIR__, '/') . DIRECTORY_SEPARATOR . 'userFiles' . DIRECTORY_SEPARATOR . $_SESSION['username'];
$curPath = $homePath . DIRECTORY_SEPARATOR . ltrim($dirName, '/');

// HTML代码中要额外用到的变量
$preDirName = !empty($dirName) && dirname($dirName) != '.' ? dirname($dirName) : '';
$dirItem = getDirItem($curPath);
if (!empty($_SESSION['copy'])) {
    $ptn = '#' . preg_quote($_SESSION['copy'], '#') . '#';
    $p_num = preg_match($ptn, $dirName, $matches);
}
if (!empty($_SESSION['move'])) {
    $ptn = '#' . preg_quote($_SESSION['move'], '#') . '#';
    $p_num = preg_match($ptn, $dirName, $matches);
}

// 操作
if (!empty($act)) {
    switch ($act) {
        // 创建文件夹
        case 'create_dir':
            $name = $_POST['name'];
            $path = rtrim($curPath, '/') . DIRECTORY_SEPARATOR . $name;
            if (preg_match('/\\s/', $name)) {
                $result['status'] = 'alert';
                $result['msg'] = '名称中不能含有空格';
                exit(json_encode($result));
            }
            if (preg_match('/\\/|\\.\\./', $name)) {
                $result['status'] = 'alert';
                $result['msg'] = '新建失败，文件夹名称中含有非法字符';
                exit(json_encode($result));
            }
            $res = createDir($path);
            if ($res === true) {
                $result['status'] = 'ok';
                $result['msg'] = '新建成功！';
            } else {
                $result['status'] = 'fail';
                $result['msg'] = $res;
            }
            exit(json_encode($result));
            break;
        // 重命名文件或文件夹
        case 'rename':
            $name = $_POST['new_name'];
            $ext = isset($_POST['ext']) ? $_POST['ext'] : '';
            if (preg_match('/\\s/', $name)) {
                $result['status'] = 'alert';
                $result['msg'] = '名称中不能含有空格';
                exit(json_encode($result));
            }
            if (!empty($ext)) {
                $oldName = $_POST['old_name'] . '.' . $ext;
                $newName = $name.'.'.$ext;
            } else {
                $oldName = $_POST['old_name'];
                $newName = $name;
            }
            if (preg_match('/\\/|\\\|\\.\\./', $newName)) {
                $result['status'] = 'alert';
                $result['msg'] = '重命名失败，新名称中含有非法字符';
                exit(json_encode($result));
            }
            $extWhiteList = ['','jpeg', 'jpg', 'png', 'gif', 'txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
            if (!in_array(strtolower($ext), $extWhiteList)) {
                $result['status'] = 'alert';
                $result['msg'] = '重命名失败，新名称中含有非法扩展名';
                exit(json_encode($result));
            }
            $oldPath = $curPath . DIRECTORY_SEPARATOR . $oldName;
            $newPath = $curPath . DIRECTORY_SEPARATOR . $newName;
            $res = rename($oldPath, $newPath);
            if ($res === true) {
                $result['status'] = 'ok';
                $result['msg'] = '已成功重命名为：' . $name;
            } else {
                $result['status'] = 'fail';
                $result['msg'] = $res;
            }
            exit(json_encode($result));
            break;
        // 删除目录
        case 'del_dir':
            $name = $_POST['dir_name'];
            $path = $curPath . DIRECTORY_SEPARATOR . $name;
            $res = delDir($path);
            if ($res === true) {
                $result['status'] = 'ok';
                $result['msg'] = '删除成功！';
            } else {
                $result['status'] = 'fail';
                $result['msg'] = $res;
            }
            exit(json_encode($result));
            break;
        // 删除文件
        case 'del_file':
            $name = $_POST['file_name'];
            $path = $curPath . DIRECTORY_SEPARATOR . $name;
            $res = delFile($path);
            if ($res === true) {
                $result['status'] = 'ok';
                $result['msg'] = '删除成功!';
            } else {
                $result['status'] = 'fail';
                $result['msg'] = $res;
            }
            exit(json_encode($result));
            break;
        // 复制文件或目录（获取文件或者目录名称存到session）
        case 'copy':
            if (isset($_SESSION['move'])) {
                unset($_SESSION['move']);
            }
            $item = $_POST['item'];
            if ($_SESSION['copy'] = $item) {
                $result['status'] = 'ok';
                $result['msg'] = '复制成功！请打开目标文件夹点击“粘贴”按钮';
            } else {
                $result['status'] = 'fail';
                $result['msg'] = '未知错误，请联系网站管理员';
            }
            exit(json_encode($result));
            break;
        // 剪切文件或目录（获取文件或者目录名称存到session）
        case 'move':
            if (isset($_SESSION['copy'])) {
                unset($_SESSION['copy']);
            }
            $item = $_POST['item'];
            if ($_SESSION['move'] = $item) {
                $result['status'] = 'ok';
                $result['msg'] = '剪切成功！请打开目标文件夹点击“粘贴”按钮';
            } else {
                $result['status'] = 'fail';
                $result['msg'] = '未知错误，请联系网站管理员';
            }
            exit(json_encode($result));
            break;
        // 取消 复制/剪切 操作（把session中的copy 或 move 字段删除）
        case 'cancel':
            if (isset($_SESSION['copy'])) {
                unset($_SESSION['copy']);
            }
            if (isset($_SESSION['move'])) {
                unset($_SESSION['move']);
            }
            exit(json_encode(['msg' => '已取消']));
            break;
        // 粘贴
        case 'paste':
            if (isset($_SESSION['copy'])) {
                $src = $homePath . DIRECTORY_SEPARATOR . $_SESSION['copy'];
                $dst = $curPath;
                if (is_dir($src)) {
                    $res = copyDir($src, $dst);
                } else {
                    $res = copyFile($src, $dst);
                }
                if ($res === true) {
                    $result['status'] = 'ok';
                    $result['msg'] = '粘贴成功';
                    unset($_SESSION['copy']);
                } else {
                    $result['status'] = 'fail';
                    $result['msg'] = '粘贴失败！' . $res;
                }
                exit(json_encode($result));
            }
            if (isset($_SESSION['move'])) {
                $src = $homePath . DIRECTORY_SEPARATOR . $_SESSION['move'];
                $dst = $curPath;
                if (is_dir($src)) {
                    $res = moveDir($src, $dst);
                } else {
                    $res = moveFile($src, $dst);
                }
                if ($res === true) {
                    $result['status'] = 'ok';
                    $result['msg'] = '粘贴成功';
                    unset($_SESSION['move']);
                } else {
                    $result['status'] = 'fail';
                    $result['msg'] = '粘贴失败！' . $res;
                }
                exit(json_encode($result));
            }
            break;
    }
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>在线网盘 - anydemo.cn</title>
    <link href="../common/static/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="../common/static/json2/json2.js"></script>
    <script src="../common/static/jquery/jquery-3.3.1.min.js"></script>
    <!--[if lt IE 9]>
    <script src="https://cdn.jsdelivr.net/npm/html5shiv@3.7.3/dist/html5shiv.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/respond.js@1.4.2/dest/respond.min.js"></script>
    <![endif]-->
    <style>
        #prompt {
            float: right;
            color: red;
        }

        .dir_name {
            cursor: pointer;
        }

        .table > tbody > tr > td {
            vertical-align: middle;
        }

        .table > thead > tr > th {
            vertical-align: middle;
        }

    </style>
</head>
<body class="container">
<?php require '../common/navigator.html'; ?>
<h3 align="center">在线网盘
    <small>AnyDemo.cn</small>
</h3><br><br>
<?php if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['username'])) :?>
<div align="center" style="color: red;">
    <span class="glyphicon glyphicon-bullhorn"></span> 亲，需要先登录才能使用在线网盘服务哦
</div><br><br>
<div class="panel panel-default center-block" style="width: 450px">
    <div class="panel-heading" style="text-align: center">登录</div>
    <div class="panel-body">
        <form class="form-horizontal" id="login_form" data-url="../users/login.php">
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">用户名</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="username" name="username" placeholder="请输入用户名...">
                </div>
            </div>
            <div class="form-group">
                <label for="passwd" class="col-sm-2 control-label">密码</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="passwd" name="passwd" placeholder="请输入密码...">
                </div>
            </div>
            <div class="form-group">
                <label for="captcha" class="col-sm-2 control-label">验证码</label>
                <div class="col-sm-10" id="captcha_img">
                    <input type="text" class="form-control" id="captcha"  name="captcha" placeholder="请输入验证码..." style="width: 45%">
                    <label for="captcha"><img src="../common/captcha/show_captcha.php" alt="验证码" title="点击刷新验证码" class="captcha_img" style="cursor: pointer;" onclick="this.src=this.src+'?'"></label>
                </div>
            </div>
            <div class="form-group" align="center">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" class="btn btn-primary" id="login_btn" value="确认登录">
                </div>
            </div>
        </form>
    </div>
    <div class="panel-footer" align="right">如没有账号，请点击 <a href="../users/index.php">注册</a></div>
</div>
<script src="js/events.js"></script>
<script src="../common/static/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="../common/static/layer/layer.js"></script>
</body>
</html>
<?php exit;endif;?>
<div id="d_div">
    <p class="text-right">
        <span class="glyphicon glyphicon-user"></span><strong> <?= $_SESSION['username']; ?></strong> 您好！
        <button class="btn btn-default btn-sm" id="logout_btn" data-url="../users/logout.php">
            <span class="glyphicon glyphicon-log-out"></span> 注销
        </button>
    </p>
    <form enctype="multipart/form-data" method="post" style="display: none" id="file_form" data-path="?path=<?=$dirName;?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
        <input type="file" name="user_file" id="file_item" required>
    </form>
    <p>
        <button class="btn btn-primary" id="create_dir" data-url="./index.php?path=<?= $dirName; ?>">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新建文件夹
        </button>

        <?php if (empty($_SESSION['copy']) && empty($_SESSION['move'])) : ?>
            <button class="btn btn-primary" id="upload_btn"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span>上传文件</button>
        <?php else: ?>
            <button class="btn btn-success" id="paste" data-url="./index.php?path=<?= $dirName; ?>"><span class="glyphicon glyphicon-paste" aria-hidden="true"></span> 粘贴</button>
            <button class="btn btn-success" id="cancel" data-url="./index.php?path=<?= $dirName; ?>"><span class="glyphicon glyphicon glyphicon-ban-circle" aria-hidden="true"></span> 取消</button>
        <?php endif; ?>

        <?php if ($curPath != $homePath && $dirName != ''): ?>
            <button class="btn btn-info" id="home_dir" data-url="./index.php"><span class="glyphicon glyphicon-fast-backward"></span> 返回根目录</button>
            <button class="btn btn-info" id="level_up" data-url="./index.php?path=<?= $preDirName; ?>">
                <span class="glyphicon glyphicon-level-up"></span> 返回上一级
            </button>
        <?php else: ?>
            <button disabled="disabled" class="btn btn-default" style="cursor:default"><span class="glyphicon glyphicon-home"></span> 根目录</button>
        <?php endif; ?>

        <span id="prompt"><br><span class="glyphicon glyphicon-bullhorn"></span> 提示：只允许上传小于2MB 的图片、txt文本、Word文件、Excel文件、PPT文件。</span>
    </p>
    <table class="table table-hover">
        <thead>
        <tr>
            <th width="30%">文件名</th>
            <th width="10%">大小</th>
            <th width="20%">修改日期</th>
            <th width="40%" style="text-align: center">操作</th>
        </tr>
        </thead>
        <tbody>

        <?php
        // 如果是目录
        if (isset($dirItem['dir'])) {
            foreach ($dirItem['dir'] as $dir) :
        ?>
            <tr>
                <td class="dir_name" data-url="./index.php?path=<?= ltrim($dirName . DIRECTORY_SEPARATOR . $dir['basename'], '/'); ?>" style="text-align: left"><span style="color: #ffd700;"><span class="glyphicon glyphicon-folder-open"></span></span> <?= $dir['basename']; ?></td>
                <td>-</td>
                <td><?= $dir['mtime']; ?></td>
                <td style="text-align: center">
                    <button class="btn btn-info btn-sm open_dir" data-url="./index.php?path=<?= ltrim($dirName . DIRECTORY_SEPARATOR . $dir['basename'], '/'); ?>">
                        <span class="glyphicon glyphicon-eye-open"></span> 打开
                    </button>
                    <button class="btn btn-primary btn-sm rename" data-url="./index.php?path=<?= $dirName; ?>" data-name="<?= $dir['basename']; ?>">
                        <span class="glyphicon glyphicon-pencil"></span> 重命名
                    </button>
                    <button class="btn btn-primary btn-sm copy" data-url="./index.php?path=<?= $dirName; ?>" data-name="<?= ltrim($dirName . DIRECTORY_SEPARATOR . $dir['basename'], '/'); ?>">
                        <span class="glyphicon glyphicon-duplicate"></span> 复制
                    </button>
                    <button class="btn btn-primary btn-sm move" data-url="./index.php?path=<?= $dirName; ?>" data-name="<?= ltrim($dirName . DIRECTORY_SEPARATOR . $dir['basename'], '/'); ?>">
                        <span class="glyphicon glyphicon-scissors"></span> 剪切
                    </button>
                    <button href="javascript:void(0)" class="btn btn-danger btn-sm delete_dir" data-url="./index.php?path=<?= $dirName; ?>" data-name="<?= $dir['basename']; ?>">
                        <span class="glyphicon glyphicon-trash"></span> 删除
                    </button>
                </td>
            </tr>
        <?php
            endforeach;
        }
        // 如果是文件
        if (isset($dirItem['file'])) {
            foreach ($dirItem['file'] as $file) :
        ?>
            <tr>
                <td style="text-align: left"><span style="color: #A9A9A9"><span class="glyphicon glyphicon-file"></span></span> <?= $file['basename'] ?></td>
                <td><?= $file['size'] ?></td>
                <td><?= $file['mtime'] ?></td>
                <td style="text-align: center">
                    <a href="./download.php?path=<?=$dirName;?>&file=<?=$file['basename'];?>" class="btn btn-success btn-sm">
                        <span class="glyphicon glyphicon-cloud-download"></span> 下载
                    </a>
                    <button class="btn btn-primary btn-sm rename" data-url="./index.php?path=<?= $dirName; ?>" data-name="<?= preg_replace('/\\.\\w+(?!.*\\.)/','',$file['basename']);?>" data-ext="<?=pathinfo($file['basename'],PATHINFO_EXTENSION)?>">
                        <span class="glyphicon glyphicon-pencil"></span> 重命名
                    </button>
                    <button class="btn btn-primary btn-sm copy" data-url="./index.php?path=<?= $dirName; ?>" data-name="<?= ltrim($dirName . DIRECTORY_SEPARATOR . $file['basename'], '/'); ?>">
                        <span class="glyphicon glyphicon-duplicate"></span> 复制
                    </button>
                    <button class="btn btn-primary btn-sm move" data-url="./index.php?path=<?= $dirName; ?>" data-name="<?= ltrim($dirName . DIRECTORY_SEPARATOR . $file['basename'], '/'); ?>">
                        <span class="glyphicon glyphicon-scissors"></span> 剪切
                    </button>
                    <button class="btn btn-danger btn-sm delete_file" data-url="./index.php?path=<?= $dirName; ?>" data-name="<?= $file['basename']; ?>">
                        <span class="glyphicon glyphicon-trash"></span> 删除
                    </button>
                </td>
            </tr>
        <?php
            endforeach;
        }
        ?>
        </tbody>
    </table>
    <?php
    if (isset($dirItem) && count($dirItem) < 1) {
        echo '<p align="center" class="alert alert-warning" role="alert">空文件夹！</p>';
    }
    if ($dirItem === false && !empty($dirName)) {
        echo '<p align="center" class="alert alert-warning" role="alert">文件夹不存在或已被删除！</p>';
    } elseif ($dirItem === false && empty($dirName)) {
        echo '<p align="center" class="alert alert-warning" role="alert">亲~您的网盘是空的，可以新建文件夹或者上传文件哦</p>';
    }
    ?>
</div>

<script src="js/events.js"></script>
<script src="../common/static/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="../common/static/layer/layer.js"></script>
</body>
</html>