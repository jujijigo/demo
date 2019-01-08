// 动态div
let ddiv = $('#d_div');

// ajax请求响应 前进/后退 操作（前提是ajax请求中使用了history.pushState() 或者 history.replaceState() ）
window.onpopstate = function (event) {
    let url = location.href;
    if (event.state === 'dir_jump') {
        $('#d_div').load(url + ' #d_div');
    }
};

// 阻止默认submit跳转
$('#login_form').submit(function () {
    return false;
});


// 登录
$('#login_btn').click(function () {
    var form = $("#login_form");
    var url = form.attr('data-url');
    $.ajax({
        url: url,
        type: "POST",
        data: form.serialize(),
        dataType: 'json',
        success: function (result) {
            switch (result.status) {
                case 'fail':
                    var icon = 5;
                    break;
                case 'ok':
                    var icon = 6;
                    break;
            }
            if (result.status === 'ok') {
                layer.msg(result.msg, {time: 1200}, function (index) {
                    location.reload();
                });
            } else {
                layer.alert(result.msg, {icon: icon}, function (index) {
                    $('.captcha_img').trigger('click');
                    $('#captcha').val("");
                    layer.close(index);
                });
            }
        }
    });
});

// 注销
ddiv.on('click', '#logout_btn', function () {
    var url = $(this).attr('data-url');
    layer.confirm('确定要注销吗？', {title: '提示'}, function (index) {
        $.ajax({
            url: url,
            type: 'POST',
            data: {act: "logout"},
            dataType: 'json',
            success: function (result) {
                layer.msg(result.msg, {time: 500}, function (index) {
                    location.reload();
                    layer.close(index);
                });
            }
        });
        layer.close(index);
    }, function () {
        layer.msg('就知道你还舍不得我^_^', {time: 1200});
    });
});

// 替换选择文件按钮
ddiv.on('click', '#upload_btn', function () {
    $('#file_item').click();
});

// 上传文件
ddiv.on('change', '#file_form', function () {
    let file = document.getElementById('file_item').files[0];
    if (file !== undefined && file.size > 2100000) {
        layer.alert('不能上传大于 2MB 的文件', {time: 5000, title: '提示', icon: 2});
        return;
    }
    let form = document.getElementById('file_form');
    let formData = new FormData(form);
    let path = form.getAttribute('data-path');
    let url = './upload.php' + path;
    let loadUrl = './index.php' + path;
    let xhr = new XMLHttpRequest();
    formData.append("act", "upload");
    xhr.upload.onloadstart = function () {
        layer.alert('正在上传，请勿刷新此页面', {title: '提示', btn: 0});
    };
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (!!window.ActiveXObject || "ActiveXObject" in window) {
                var result = JSON.parse(xhr.response)
            } else {
                var result = xhr.response;
            }
            if (result.status === "ok") {
                layer.msg('上传成功');
                ddiv.load(loadUrl + ' #d_div');
            } else {
                layer.alert(result.msg, {title: '提示', icon: 5, closeBtn: 0});
                ddiv.load(loadUrl + ' #d_div');
            }
        }
    };
    xhr.responseType = "json";
    xhr.open("POST", url, true);
    xhr.send(formData);
});

// 创建目录
ddiv.on('click', '#create_dir', function () {
    let url = $(this).data('url');
    layer.prompt({
        formType: 0,
        value: 'NewFolder',
        title: '新建文件夹'
    }, function (value, index) {
        $.ajax({
            url: url,
            type: 'POST',
            data: {act: 'create_dir', name: value},
            dataType: 'json',
            success: function (result) {
                if (result.status === 'ok') {
                    layer.msg(result.msg, {time: 2000});
                    ddiv.load(url + ' #d_div');
                    layer.close(index);
                } else {//result.status === 'fail' || 'alert'
                    layer.alert(result.msg, {title: '提示', icon: 2}, function (index) {
                        layer.close(index);
                    });
                }
            },
            error: function () {
                layer.msg('网络故障，请稍后再试~', {time: 2000});
            }
        });
    });
});

// 打开目录（点击打开按钮）
ddiv.on('click', '.open_dir', function () {
    let url = $(this).data('url');
    ddiv.load(url + ' #d_div', function () {
        history.pushState('dir_jump', '', url);
    });
});
// 打开目录（点击目录名称）
ddiv.on('click', '.dir_name', function () {
    let url = $(this).data('url');
    ddiv.load(url + ' #d_div', function () {
        history.pushState('dir_jump', '', url);
    });
});


// 返回上一级目录
ddiv.on('click', '#level_up', function () {
    let url = $(this).data('url');
    ddiv.load(url + ' #d_div', function () {
        history.pushState('dir_jump', '', url);
    });
});

// 返回根目录
ddiv.on('click', '#home_dir', function () {
    let url = $(this).data('url');
    ddiv.load(url + ' #d_div', function () {
        history.pushState('dir_jump', '', url);
    });
});

// 重命名文件或目录
ddiv.on('click', '.rename', function () {
    let url = $(this).attr('data-url');
    let oldName = $(this).attr('data-name');
    let ext = $(this).attr('data-ext');
    layer.prompt({
        formType: 0,
        title: '重命名',
        value: oldName
    }, function (value, index) {
        $.ajax({
            url: url,
            type: 'POST',
            data: {act: 'rename', old_name: oldName, ext: ext, new_name: value},
            dataType: 'json',
            success: function (result) {
                if (result.status === 'ok') {
                    layer.alert(result.msg, {icon: 6, title: '提示'}, function (index) {
                        ddiv.load(url + ' #d_div');
                        layer.close(index);
                    });
                    layer.close(index);
                } else {//result.status === 'fail' || 'alert'
                    layer.alert(result.msg, {icon: 5, title: '提示'}, function (index) {
                        ddiv.load(url + ' #d_div');
                        layer.close(index);
                    });
                }
            }
        });
    });
});

// 删除目录
ddiv.on('click', '.delete_dir', function () {
    let url = $(this).data('url');
    let name = $(this).data('name');
    layer.open({
        type: 0,
        title: '提示',
        content: '亲~确认删除【' + name + '】文件夹吗？此文件夹下的所有文件会被一并删除并且不可恢复哦',
        icon: 0,
        btn: ['确认', '取消'],
        yes: function (index) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {act: 'del_dir', dir_name: name},
                dataType: 'json',
                success: function (result) {
                    if (result.status === 'ok') {
                        layer.msg(result.msg, {time: 2000});
                        ddiv.load(url + ' #d_div');
                    } else {
                        layer.msg(result.msg, {time: 2000});
                    }
                }
            });
            layer.close(index);
        },
        btn2: function () {
            layer.msg('明智的选择~', {time: 2000});
        },
        closeBtn: 0,
    });
});

// 删除文件
ddiv.on('click', '.delete_file', function () {
    let url = $(this).data('url');
    let name = $(this).data('name');
    layer.open({
        type: 0,
        title: '提示',
        content: '亲~确认删除文件：' + name + ' ？删除后不可恢复哦',
        icon: 0,
        btn: ['确认', '取消'],
        yes: function (index) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {act: 'del_file', file_name: name},
                dataType: 'json',
                success: function (result) {
                    if (result.status === 'ok') {
                        layer.msg(result.msg, {time: 1500});
                        ddiv.load(url + ' #d_div');
                    } else {
                        layer.alert(result.msg, {icon: 5});
                    }
                }
            });
            layer.close(index);
        },
        btn2: function () {
            layer.msg('明智的选择~', {time: 2000});
        },
        closeBtn: 0,
    });
});

// 复制文件或目录
ddiv.on('click', '.copy', function () {
    let url = $(this).data('url');
    let name = $(this).data('name');
    $.ajax({
        url: url,
        type: 'POST',
        data: {act: 'copy', item: name},
        dataType: 'json',
        success: function (result) {
            if (result.status === 'ok') {
                layer.alert(result.msg, {icon: 1, title: '提示'}, function (index) {
                    ddiv.load(url + ' #d_div');
                    layer.close(index);
                });
            } else {
                layer.alert(result.msg, {icon: 1, title: '提示'});
            }
        }
    });
});

// 剪切目录或文件
ddiv.on('click', '.move', function () {
    let url = $(this).data('url');
    let name = $(this).data('name');
    $.ajax({
        url: url,
        type: 'POST',
        data: {act: 'move', item: name},
        dataType: 'json',
        success: function (result) {
            if (result.status === 'ok') {
                layer.alert(result.msg, {icon: 1, title: '提示'}, function (index) {
                    ddiv.load(url + ' #d_div');
                    layer.close(index);
                });
            } else {
                layer.alert(result.msg, {icon: 1, title: '提示'});
            }
        }
    });
});

// 取消 复制/剪切 操作
ddiv.on('click', '#cancel', function () {
    let url = $(this).data('url');
    $.ajax({
        url: url,
        type: 'POST',
        data: {act: 'cancel'},
        dataType: 'json',
        success: function (result) {
            layer.msg(result.msg, {time: 1500});
            ddiv.load(url + ' #d_div');
        },
        error: function () {
            layer.alert('网络故障，请刷新网页重试', {title: '提示'});
        }
    });
});

// 粘贴
ddiv.on('click', '#paste', function () {
    let url = $(this).data('url');
    $.ajax({
        url: url,
        type: 'POST',
        data: {act: 'paste'},
        dataType: 'json',
        success: function (result) {
            if (result.status === 'ok') {
                let href = location.href;
                ddiv.load(href + ' #d_div');
                layer.msg(result.msg, {time: 1500});
            } else {
                layer.alert(result.msg, {icon: 2, title: '提示'});
            }
        }
    });
});