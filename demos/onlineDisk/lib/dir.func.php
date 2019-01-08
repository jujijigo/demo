<?php
date_default_timezone_set('PRC');

/**
 * 获取目录的子目录和文件项目列表，及文件大小、修改时间信息
 * @param string $path 要读取的目录路径
 * @return array|bool       成功返回数组，失败返回false
 */
function getDirItem($path)
{
    if (!file_exists($path) || !is_dir($path)) {
        return false;
    }
    $info = [];
    $dirItem = [];
    $size = '';
    $kb = 1024;
    $mb = $kb * 1024;
    $gb = $mb * 1024;
    $handle = opendir($path);
    while (($item = @readdir($handle)) !== false) {
        if ($item != '.' && $item != '..') {
            $itemPath = $path . DIRECTORY_SEPARATOR . $item;
            $info['basename'] = $item;
            $info['mtime'] = date('Y/m/d H:i:s', filemtime($itemPath));
            if (!is_dir($itemPath)) {
                switch ($byte = filesize($itemPath)) {
                    case $byte < $kb:
                        $size = $byte . 'B';
                        break;
                    case $byte < $mb:
                        $size = round(($byte / $kb), 2) . ' KB';
                        break;
                    case $byte < $gb:
                        $size = round(($byte / $mb), 2) . ' MB';
                        break;
                }
                $info['size'] = $size;
            }

            if (is_dir($itemPath)) {
                $dirItem['dir'][] = $info;
            }
            if (!is_dir($itemPath)) {
                $dirItem['file'][] = $info;
            }
        }
    }
    closedir($handle);
    return $dirItem;
}

/**
 * 创建目录
 * @param string $path 创建路径
 * @return bool|string      成功返回true，失败返回提示字符串
 */
function createDir($path)
{
    if (preg_match('/\\s/', $path)) {
        return '名称中不能含有空格';
    }

    if (is_dir($path)) {
        return "当前位置已存在同名文件夹";
    }

    if (!mkdir($path, 0755)) {
        return "您没有相关权限，请联系系统管理员";
    }

    return true;
}

/**
 * 重命名文件或目录
 * @param string $oldPath 旧路径
 * @param string $newPath 新路径
 * @return bool|string          成功返回true，失败返回提示字符串
 */
function renameFunc($oldPath, $newPath)
{
    if (preg_match('/\\s/', $newPath)) {
        return '名称中不能含有空格';
    }

    if (is_dir($oldPath) && is_dir($newPath)) {
        return '当前位置已存在同名文件夹：' . basename($newPath) . '，请重新输入另外的文件夹名称';
    }

    if (is_file($oldPath) && is_file($newPath)) {
        return '当前位置已存在同名文件：' . basename($newPath) . '，请重新输入另外的文件名称';
    }

    if (!rename($oldPath, $newPath)) {
        if (is_dir($oldPath)) {
            return '您没有权限重命名此文件夹';
        } else {
            return '您没有权限重命名此文件';
        }
    }

    return true;
}

/**
 * 删除目录
 * @param string $path 要删除的目录路径
 * @return bool|string      成功返回true，失败返回提示字符串
 */
function delDir($path)
{
    if (!is_dir($path) || preg_match('/\\.\\./', $path)) {
        return '删除失败！要删除的文件夹不存在';
    }

    $handle = opendir($path);
    while (($item = @readdir($handle)) !== false) {
        if ($item != '.' && $item != '..') {
            $itemPath = $path . DIRECTORY_SEPARATOR . $item;
            if (!is_dir($itemPath)) {
                @unlink($itemPath);
            }
            if (is_dir($itemPath)) {
                delDir($itemPath);
            }
        }
    }
    if (!rmdir($path)) {
        return '删除失败！您没有权限删除此文件夹！';
    }
    closedir($handle);
    return true;
}

/**
 * 删除文件
 * @param string $path 文件路径
 * @return bool|string      成功返回true，失败返回提示字符串
 */
function delFile($path)
{
    if (!file_exists($path) && !is_file($path) && preg_match('/\\.\\./', $path)) {
        return '删除失败！文件不存在';
    }

    if (unlink($path) === false) {
        return '删除失败！您没有权限删除该文件';
    }

    return true;
}

/**
 * 复制文件
 * @param string $srcFile 源文件路径
 * @param string $dstDir 目标目录
 * @return bool|string      成功返回true，失败返回提示字符串
 */
function copyFile($srcFile, $dstDir)
{
    if (!is_file($srcFile)) {
        return '原文件不存在或已损坏';
    }

    if (!is_dir($dstDir)) {
        return '目标文件夹不存在';
    }

    preg_match('/(?!.*\\/+).+\\.\\w+/', $srcFile, $match);
    $dstFile = $dstDir . DIRECTORY_SEPARATOR . $match[0];
    if (file_exists($dstFile)) {
        return '此位置已存在同名文件';
    }

    if (!copy($srcFile, $dstFile)) {
        return '您没有相关权限，请联系系统管理员';
    }

    return true;
}

/**
 * 复制目录
 * @param string $src 源目录
 * @param string $dst 目的目录
 * @return bool|string      成功返回true，失败返回提示字符串
 */
function copyDir($src, $dst)
{
    // 这个函数认定$s 和$d 目录都已存在，直接把$s 中的内容都复制到$d 中
    function copyD($s, $d)
    {
        $handle = opendir($s);
        while (($item = @readdir($handle)) !== false) {
            if ($item != '.' && $item != '..') {
                $itemPath = rtrim($s) . DIRECTORY_SEPARATOR . trim($item);
                if (is_file($itemPath)) {
                    copy($itemPath, $d . DIRECTORY_SEPARATOR . $item);
                }
                if (is_dir($itemPath)) {
                    $subD = rtrim($d) . DIRECTORY_SEPARATOR . $item;
                    @mkdir($subD, 0755);
                    copyD($itemPath, $subD);
                }
            }
        }
        closedir($handle);
        return true;
    }

    if (!file_exists($src)) {
        return '源目录不存在';
    }
    if (!is_dir($dst) || preg_match('/\\.\\./', $dst)) {
        return '粘贴位置不存在';
    }

    preg_match('/[^\\/]+(?!.*\\/.+)/', $src, $matches);
    $dir = $matches[0];
    $dstDir = $dst . DIRECTORY_SEPARATOR . $dir;
    if (is_dir($dstDir)) {
        return '此位置已存在同名文件夹，请先重命名再进行 复制-粘贴 操作';
    }

    // 先复制到临时目录，防止无限递归
    $tempHome = rtrim(__DIR__, '/') . '/../userFiles/.temp/' . $_SESSION['username'];
    $temp = $tempHome . '/' . $dir;
    if (is_dir($temp)) {
        delDir(realpath($temp));
    }

    @mkdir($temp, 0755, true);
    if (copyD($src, $temp) !== true) {
        return '服务器错误，请稍后再试或联系管理员';
    }

    @mkdir($dstDir, 0755, true);
    if (copyD($temp, $dstDir) !== true) {
        return '您没有粘贴文件夹到此位置的权限';
    } else {
        delDir(realpath($tempHome));
    }

    return true;
}

/**
 * 移动文件
 * @param string $srcFile 源文件路径
 * @param string $dstDir 目标目录路径
 * @return bool|string          成功返回true，失败返回提示字符串
 */
function moveFile($srcFile, $dstDir)
{
    if (!file_exists($srcFile) || preg_match('/\\.\\./', $srcFile)) {
        return '原文件不存在或已损坏';
    }

    if (!is_dir($dstDir) || preg_match('/\\.\\./', $dstDir)) {
        return '目标文件夹不存在';
    }

    preg_match('/(?!.*\\/+).+\\.\\w+/', $srcFile, $match);
    $dstFile = $dstDir . DIRECTORY_SEPARATOR . $match[0];
    if (file_exists($dstFile)) {
        return '此位置已存在同名文件，请先重命名再重新进行 复制-粘贴 操作';
    }

    if (!rename($srcFile, $dstFile)) {
        return '您没有相关权限，请联系系统管理员';
    }

    return true;
}

/**
 * 移动目录
 * @param string $src 源目录
 * @param string $dst 目标位置
 * @return bool|string      成功返回true，失败返回提示字符串
 */
function moveDir($src, $dst)
{
    // 这个函数认定$s 和$d 目录都已存在，直接把$s 中的内容都移动到$d 中，最后删除$s。
    function moveD($s, $d)
    {
        $handle = opendir($s);
        while (($item = @readdir($handle)) !== false) {
            if ($item != '.' && $item != '..') {
                $itemPath = rtrim($s) . DIRECTORY_SEPARATOR . trim($item);
                if (is_dir($itemPath) && !is_file($itemPath)) {
                    $subD = rtrim($d) . DIRECTORY_SEPARATOR . $item;
                    mkdir($subD, 0755);
                    moveD($itemPath, $subD);
                } else {
                    rename($itemPath, $d . DIRECTORY_SEPARATOR . $item);
                }
            }
        }
        closedir($handle);
        rmdir($s);
        return true;
    }

    if (!file_exists($src)) {
        return '源目录不存在';
    }

    if (!is_dir($dst) || preg_match('/\\.\\./', $dst)) {
        return '粘贴位置不存在';
    }

    preg_match('/[^\\/]+(?!.*\\/.+)/', $src, $matches);
    $dir = $matches[0];
    $dstDir = $dst . DIRECTORY_SEPARATOR . $dir;
    if (is_dir($dstDir)) {
        return '此位置已存在同名文件夹，请先重命名再进行 剪切-粘贴 操作';
    }

    // 先移动到临时目录，防止无限递归
    $temp = rtrim(__DIR__, '/') . '/../userFiles/.temp/' . $_SESSION['username'] . '/' . $dir;
    if (is_dir($temp)) {
        delDir($temp);
    }

    @mkdir($temp, 0755, true);
    if (moveD($src, $temp) !== true) {
        return '服务器错误，请稍后再试或联系管理员';
    }

    @mkdir($dstDir, 0755, true);
    if (moveD($temp, $dstDir) !== true) {
        return '您没有移动目录的权限';
    }

    return true;
}
