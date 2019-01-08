<?php
date_default_timezone_set('PRC');
session_start();

$content = $_POST['content'];
$username = $_POST['username'];
$captcha = strtolower($_POST['captcha']);
$captcha_code = strtolower($_SESSION['captcha_code']);

if (empty($username)) {
    $result['status'] = 'fail';
    $result['msg'] = '姓名/昵称不能为空！';
    exit(json_encode($result));
} elseif (empty($content)) {
    $result['status'] = 'fail';
    $result['msg'] = '留言内容不能为空！';
    exit(json_encode($result));
}

if(empty($captcha_code) || $captcha != $captcha_code){
    $result['status'] = 'fail';
    $result['msg'] = '验证码不正确，请重新输入';
    exit(json_encode($result));
}

require '../common/database/connect.php';
try {
    $sql = 'insert into sp_msg(username,content,created_at) values(:user_name,:content,:created_at)';
    $stmt = $pdo->prepare($sql);
    $data = [
        ':user_name' => $username,
        ':content' => $content,
        ':created_at' => time(),
    ];
    $res = $stmt->execute($data);
    if ($res) {
        $result['status'] = 'ok';
        $result['msg'] = '恭喜您！留言成功！';
        exit(json_encode($result));
    }
} catch (PDOException $e) {
    $result['status'] = 'ok';
    $result['msg'] = '服务器繁忙，请稍后再试~';
    exit(json_encode($result));
}
