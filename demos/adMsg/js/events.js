// 动态div
var ddiv = $('#d_div');

//阻止submit 和button 的默认跳转
$('#login_form').submit(function () {
    return false;
});
ddiv.on('submit', '#msg_form', function () {
    return false;
});

// 登录
ddiv.on('click', '#login_btn', function () {
    var form = $("#login_form");
    var url = form.attr('data-url');
    var href = location.href;
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
                layer.msg(result.msg, {time:1200}, function (index) {
                    ddiv.load(href + ' #d_div');
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
    var href = location.href;
    $.ajax({
        url: url,
        type: 'POST',
        data: {act: "logout"},
        dataType: 'json',
        success: function (result) {
            layer.msg(result.msg, {time:1200}, function (index) {
                location.reload();
            });
        }
    });
});

// 添加留言
ddiv.on('click', '#msg_btn', function () {
    var form = $('#msg_form');
    var url = form.attr('data-url');
    var href = location.href;
    $.post(url, form.serialize(), function (result) {
        switch (result.status) {
            case 'fail':
                var icon = 5;
                break;
            case 'ok':
                var icon = 6;
                break;
        }
        if (result.status === 'ok') {
            layer.msg(result.msg, {time:1200}, function (index) {
                ddiv.load(href + ' #d_div');
            });
        } else {
            layer.alert(result.msg, {icon: icon}, function (index) {
                $('.captcha_img').trigger('click');
                $('#captcha').val("");
                layer.close(index);
            });
        }
    }, 'json');
});

