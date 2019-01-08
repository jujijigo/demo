<?php
session_start();
require './lib/file.func.php';

$dirName = isset($_GET['path']) && $_GET['path'] != '.' ? $_GET['path'] : '';
if (preg_match('/\\.\\./', $dirName)) {
    exit('非法请求');
}
$file = $_GET['file'];
if (!isset($file)) {
    exit('非法请求');
}

$homePath = rtrim(__DIR__, '/') . DIRECTORY_SEPARATOR . 'userFiles' . DIRECTORY_SEPARATOR . $_SESSION['username'];
$curPath = $homePath . DIRECTORY_SEPARATOR . ltrim($dirName, '/');

$path = $curPath.DIRECTORY_SEPARATOR.$file;
download($path);