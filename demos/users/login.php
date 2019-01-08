<?php

$username = $_POST['username'];
$password = md5($_POST['passwd']);
$captcha = strtoupper($_POST['captcha']);

session_start();
$captcha_code = strtoupper($_SESSION['captcha_code']);

if (empty($captcha_code) || $captcha != $captcha_code) {
    $status = 'fail';
    $msg = "验证码不正确！请重新输入。";
    echo json_encode(['status' => $status, 'msg' => $msg]);
    return;
}

require '../common/database/connect.php';
try {
    $sql = 'SELECT id,username,email,password,created_at FROM users WHERE username=:username AND password=:password';
    $stmt = $pdo->prepare($sql);
    $data = [
        ':username' => $username,
        ':password' => $password,
    ];
    $stmt->execute($data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $_SESSION = $result;
        $status = 'ok';
        $msg = '恭喜您，登录成功！';
        echo json_encode(['status' => $status, 'msg' => $msg]);
        return;
    } else {
        $status = 'fail';
        $msg = '很遗憾，登录失败！用户名或密码输入错误。';
        echo json_encode(['status' => $status, 'msg' => $msg]);
        return;
    }
} catch (PDOException $e) {
    $status = 'fail';
    $msg = '服务器繁忙，请稍后重试~';
    echo json_encode(['status' => $status, 'msg' => $msg]);
    return;
}