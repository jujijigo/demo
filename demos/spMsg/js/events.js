// 动态div
var ddiv = $('#d_div');

// 阻止submit默认的跳转
ddiv.on('submit', '#msg_form', function () {
    return false;
});

// 添加留言
ddiv.on('click', '#msg_btn', function () {
    var form = $('#msg_form');
    var url = form.attr('data-url');
    var href = location.href;
    $.ajax({
        url: url,
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.status === 'ok') {
                layer.msg('留言成功！', {time: 1200}, function () {
                    ddiv.load(href + ' #d_div');
                })
            } else {
                layer.alert(result.msg, {icon: 2, title: '提示'}, function (index) {
                    $('.captcha_img').trigger('click');
                    $('#captcha').val('');
                    layer.close(index);
                })
            }
        }
    });
});
