var index = parent.layer.getFrameIndex(window.name);
$(".btn-outline").click(function() {
    parent.layer.close(index); //执行关闭
})
$.validator.setDefaults({
    submitHandler: function() {
        $(".col-sm-8 .btn").attr("disabled", true);
        var param = $("#signupForm").serialize();
        $.ajax({
            headers: {
                token: $.cookie('login_token')
            },
            url: WEBHTTP + url,
            type: "POST",
            dataType: "json",
            data: param,
            success: function(response) {
                if (response.error == 0) {
                    toastr.success('修改成功');
                    setTimeout(function() {
                        parent.location.href = parent.location.href
                    }, 1000);
                } else if(response.error == 401){
                    toastr.error(response.reason);
                    setTimeout(function(){
                        $.cookie('login_token', '', {path: '/', expires: -1});
                        $.cookie('login_account', '', {path: '/', expires: -1});
                        parent.parent.location.href = "../../index.html"
                    },500);
                }else {
                    toastr.error(response.reason);
                }
                $(".col-sm-8 .btn").attr("disabled", false);
            }
        });
    }
});

function getInfo(url, param, callback)
{
    $("#signupForm").hide();
    setTimeout(function(){
        layer.load(0, {shade: [0.6,'#000000']});
    },1);
    $.ajax({
        headers: {
            token: $.cookie('login_token')
        },
        url: WEBHTTP + url,
        type: "GET",
        dataType: "json",
        data: param,
        success: function(response) {
            if (response.error == 0) {
                callback(response.result);
                $("#signupForm").show();
                layer.closeAll();
            } else if(response.error == 401){
                toastr.error(response.reason);
                setTimeout(function(){
                    $.cookie('login_token', '', {path: '/', expires: -1});
                    $.cookie('login_account', '', {path: '/', expires: -1});
                    parent.parent.location.href = "../../index.html"
                },500);
            }else {
                toastr.error(response.reason);
                setTimeout(function(){
                    parent.layer.close(index); //执行关闭
                },2000);
            }
        }
    });
}