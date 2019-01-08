<?php

/**
 * 上传文件
 * @param string $fieldName input表单的name属性值
 * @param string $dstDir 目的目录
 * @return bool|string          成功返回true，失败返回提示字符串
 */
function upload($fieldName, $dstDir)
{
    if (!file_exists($dstDir) || !is_dir($dstDir)) {
        return '不能上传文件到此目录或目录已被删除';
    }

    $extWhiteList = ['jpeg', 'jpg', 'png', 'gif', 'txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    $allowSize = 2097152;// 2MB

    // 接收$_FILES 中的信息
    $name = preg_replace(['/\\\|\\//', '/\\.{2,}/'], ['_', '.'], $_FILES[$fieldName]['name']); // 源文件名称
    $tmpName = $_FILES[$fieldName]['tmp_name'];  // 临时文件名称
    $errorCode = $_FILES[$fieldName]['error']; // 错误码
    $size = $_FILES[$fieldName]['size']; // 文件大小（字节）

    // 处理错误
    if ($errorCode > 0) {
        switch ($errorCode) {
            case 1:
                $error = '文件大小超出限制，请上传小于 2MB 的文件';//文件大小超出了php.ini中upload_max_filesize的大小
                break;
            case 2:
                $error = '文件大小超出限制，请上传小于 2MB 的文件';//超出表单中的MAX_FILE_SIZE设置的大小
                break;
            case 3:
                $error = '文件损坏，请重新上传';//只有部分文件上传成功
                break;
            case 4:
                $error = '没有选择上传文件';
                break;
            case 6:
                $error = '文件夹不存在';//临时目录不存在
                break;
            case 7:
                $error = '服务器错误，请联系管理员~';//磁盘写入失败
                break;
            case 8:
                $error = '不允许上传的文件类型或没有上传权限';//文件上传被PHP扩展阻止
                break;
            default:
                $error = '未知错误，请联系管理员~';
                break;
        }
        return $error;
    }

    // 限制文件大小
    if ($size > $allowSize) {
        $error = "文件大小超出限制，请上传小于 2MB 的文件";
        return $error;
    }

    // 限制扩展名
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, $extWhiteList)) {
        $error = '不支持上传此类型文件';
        return $error;
    }

    // 检查文件是否已存在
    if (file_exists($dstDir . DIRECTORY_SEPARATOR . $name) && !is_dir($dstDir . DIRECTORY_SEPARATOR . $name)) {
        $error = '此文件夹下已存在同名文件【' . preg_replace('/\\.[^\\.]+(?!.*\\.)/', '', $name) . '】，请修改本地电脑或网盘其中一个的文件名称之后再重新上传';

        return $error;
    }

    // 移动文件到指定目录
    if (!is_uploaded_file($tmpName) || !move_uploaded_file($tmpName, $dstDir . DIRECTORY_SEPARATOR . $name)) {
        $error = '很抱歉，文件上传失败';
        return $error;
    }

    return true;
}

/**
 * 文件下载
 * @param string $path 下载文件的路径
 * @return bool|string      成功返回true，失败返回提示字符串
 */
function download($path)
{

    if (!file_exists($path)) {
        return '文件不存在';
    }

    if (!is_file($path)) {
        return '文件已损坏';
    }

    if (!is_readable($path)) {
        return '您没有权限下载这个文件';
    }

    // 清空缓冲区
    ob_clean();

    $handle = fopen($path, 'rb');

    if (!$handle) {
        return '文件下载失败，请稍后重试';
    }

    preg_match('/(?!.*\\/+).+\\.\\w+/', $path, $match);
    // header头
    header('Content-type:application/octet-stream;charset=utf-8;');
    header('Content-Transfer-Encoding:binary');
    header('Content-Length:' . filesize($path));
    header('Content-Disposition:attachment;filename="' . urlencode($match[0]) . '"');

    while (!feof($handle)) {
        echo fread($handle, 10240);
    }
    fclose($handle);

    return true;
}