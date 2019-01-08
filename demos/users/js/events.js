// 动态div
var ddiv = $('#d_div');

// 阻止submit的默认跳转
ddiv.on('submit', '#register_form', function () {
    return false;
});
ddiv.on('submit', '#login_form', function () {
    return false;
});

// 切换显示 注册/登录 表单
ddiv.on('click', '.toggle_login', function () {
    $('#register_div').hide();
    $('#login_div').show();
    $('.captcha_img').trigger('click');
});
ddiv.on('click', '.toggle_register', function () {
    $('#login_div').hide();
    $('#register_div').show();
    $('.captcha_img').trigger('click');
});

// 提交注册表单
ddiv.on('click', '#register_btn', function () {
    var form = $('#register_form');
    var url = form.attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.status === 'ok') {
                layer.msg(result.msg, {time: 2000}, function (index) {
                    $('.toggle_login').trigger('click');
                    $('.captcha_img').trigger('click');
                    layer.close(index);
                })
            } else {
                layer.msg(result.msg, {icon: 5, time: 2500}, function (index) {
                    $('.captcha_img').trigger('click');
                    layer.close(index);
                })
            }
        }
    });
});

// 提交登录表单
ddiv.on('click', '#login_btn', function () {
    var form = $('#login_form');
    var url = form.attr('data-url');
    var href = location.href;
    $.ajax({
        url: url,
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.status === 'ok') {
                layer.msg(result.msg, {time: 1500});
                ddiv.load(href + ' #d_div');
            } else {
                layer.open({
                    type: 0,
                    title: '提示',
                    content: result.msg,
                    icon: 2,
                    time: 2000,
                    btn: false,
                    end: function (index) {
                        $('.captcha_img').trigger('click');
                        layer.close(index);
                    }
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
            layer.msg(result.msg, {time: 500}, function (index) {
                ddiv.load(href + ' #d_div', function () {
                    $('.toggle_login').trigger('click');
                });
            });
        }
    });
});