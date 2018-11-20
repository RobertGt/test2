var index = parent.layer.getFrameIndex(window.name);
$(".btn-outline").click(function() {
    parent.layer.close(index); //执行关闭
})
$.validator.setDefaults({
    submitHandler: function() {
        $(".btn-primary").attr("disabled", true);
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
                    toastr.success('添加成功');
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
                $(".btn-primary").attr("disabled", false);
            }
        });
    }
});