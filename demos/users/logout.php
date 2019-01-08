<?php
session_start();
if (!isset($_POST['act']) || $_POST['act'] !== "logout") {
    exit;
}
if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['username'])) {
    echo json_encode(['status' => 'fail', 'msg' => '您尚未登录，不需要注销']);
}
$_SESSION = array(); //清除SESSION值.
if (isset($_COOKIE[session_name()])) {  //判断客户端的cookie文件是否存在,存在的话将其设置为过期.
    setcookie(session_name(), '', time() - 1, '/');
}
session_destroy();  //清除服务器的session文件
echo json_encode(['status' => 'ok', 'msg' => '已注销']);