<?php
session_start();

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['passwd'];
$repassword = $_POST['repasswd'];
$captcha = strtoupper($_POST['captcha']);
$captcha_code = strtoupper($_SESSION['captcha_code']);

if (empty($captcha_code) || $captcha != $captcha_code) {
    $result['status'] = 'fail';
    $result['msg'] = '验证码不正确！请重新输入。';
    exit(json_encode($result));
}
if (empty($username) || strlen($username) < 4) {
    $result['status'] = 'fail';
    $result['msg'] = '用户名字符长度至少为4位';
    exit(json_encode($result));
}
if (preg_match('/[^A-Za-z0-9_]/', $username)) {
    $result['status'] = 'fail';
    $result['msg'] = '用户名称只能使用字母、数字、下划线';
    exit(json_encode($result));
}
if (empty($email)) {
    $result['status'] = 'fail';
    $result['msg'] = '邮箱不能为空';
    exit(json_encode($result));
}
if (preg_match_all('/[\\w|\\d|_]+@[\\w|\\d]+\\.[\\w|\\d]+/', $email) != 1) {
    $result['status'] = 'fail';
    $result['msg'] = '邮箱格式错误，请重试';
    exit(json_encode($result));
}
if (empty($password) || strlen($password) < 6) {
    $result['status'] = 'fail';
    $result['msg'] = '密码长度不能小于6位';
    exit(json_encode($result));
}
if ($password !== $repassword) {
    $result['status'] = 'fail';
    $result['msg'] = '两次输入的密码不一样，请重试';
    exit(json_encode($result));
}

require '../common/database/connect.php';
$sql = "SELECT id,username FROM users WHERE username=\"$username\"";
$res = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
if ($res) {
    $result['status'] = 'fail';
    $result['msg'] = '用户名已存在，请使用其他用户名注册';
    exit(json_encode($result));
}
try {
    $sql = "INSERT INTO users(username,email,password,created_at) VALUES(:username,:email,:password,:created_at);";
    $stmt = $pdo->prepare($sql);
    $data = [
        ':username' => $username,
        ':email' => $email,
        ':password' => md5($password),
        ':created_at' => time(),
    ];
    $res = $stmt->execute($data);
    if ($res) {
        $result['status'] = 'ok';
        $result['msg'] = '恭喜您！注册成功，系统将自动跳转到登录界面~';
        exit(json_encode($result));
    }
} catch (PDOException $e) {
    $result['status'] = 'fail';
    $result['msg'] = '服务器繁忙，请稍后再试~';
    exit(json_encode($result));
}


