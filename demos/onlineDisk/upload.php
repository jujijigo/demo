<?php
session_start();
require './lib/file.func.php';

if ((!isset($_POST['act'])) || ($_POST['act'] != 'upload')) {
    exit;
}

$dirName = isset($_REQUEST['path']) && $_REQUEST['path'] != '.' ? $_REQUEST['path'] : '';
if (preg_match('/\\.\\./', $dirName)) {
    $dirName = '';
}

$homePath = rtrim(__DIR__, '/') . DIRECTORY_SEPARATOR . 'userFiles' . DIRECTORY_SEPARATOR . $_SESSION['username'];
$curPath = $homePath . DIRECTORY_SEPARATOR . ltrim($dirName, '/');

if (empty($_FILES)) {
    $result['status'] = 'fail';
    $result['msg'] = '没有选择上传文件';
    exit(json_encode($result));
}

if (!is_dir($homePath)) {
    @mkdir($homePath, 0755);
}
$res = upload('user_file', $curPath);
if ($res === true) {
    $result['status'] = 'ok';
    $result['msg'] = '上传成功';
    exit(json_encode($result));
} else {
    $result['status'] = 'fail';
    $result['msg'] = $res;
    exit(json_encode($result));
}