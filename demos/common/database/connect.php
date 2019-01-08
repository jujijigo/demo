<?php
date_default_timezone_set('PRC');
header("Content-type: text/html; charset=utf-8");
try {
    $dsn = "mysql:host=localhost;dbname=demos";
    $attr = [
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND=>'set names utf8'
    ];
    $pdo = new PDO($dsn,'','',$attr);
} catch (PDOException $e) {
    echo '数据库连接失败，请稍后再试~';
}