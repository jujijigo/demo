<?php
session_start();
$captcha_code = strtoupper($_SESSION['captcha_code']);

if (isset($_POST) && !empty($_POST)){
    $content = $_POST['content'];
    $captcha = strtoupper($_POST['captcha']);
}

if(empty($captcha_code) || $captcha != $captcha_code){
    $status = 'fail';
    $msg = "验证码不正确！请重新输入。";
    echo json_encode(['status' => $status, 'msg' => $msg]);
    return;
}

if (empty($_SESSION['username'])) {
    $status = 'fail';
    $msg = "抱歉！您没有登录，登录后才能发表留言。";
    echo json_encode(['status' => $status, 'msg' => $msg]);
    return;
} elseif (empty($content)) {
    $status = 'fail';
    $msg = "留言内容不能为空！";
    echo json_encode(['status' => $status, 'msg' => $msg]);
    return;
}

require '../common/database/connect.php';
try {
    $sql = 'insert into ad_msg(content,created_at,user_id) values(:content,:created_at,:user_id)';
    $stmt = $pdo->prepare($sql);
    $data = [
        ':content' => $content,
        ':created_at' => time(),
        ':user_id'=>$_SESSION['id'],
    ];
    $result = $stmt->execute($data);
    if ($result) {
        $status = 'ok';
        $msg = "恭喜您！留言成功";
        echo json_encode(['status' => $status, 'msg' => $msg]);
        return;
    }
} catch (PDOException $e) {
    $status = 'fail';
    $msg = "未知错误，请稍后重试或联系管理员~";
    echo json_encode(['status' => $status, 'msg' => $msg]);
    return;
}