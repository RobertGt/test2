$.validator.setDefaults({
    submitHandler: function() {
        var http = WEBHTTP + 'admin/reset';
        $(".btn-block").attr("disabled",true);
        var param = $("#signupForm").serialize();
        $.ajax({
            url : http,
            type : "post",
            dataType : "json",
            data: param,
            success : function(result){
                $(".btn-block").attr("disabled",false);
                if(result.error == 0){
                    layer.alert("重置成功",{time: 2000, icon:1});
                    var index = parent.layer.getFrameIndex(window.name);
                    setTimeout(function(){
                        parent.layer.close(index); //执行关闭
                    }, 2000)
                    return false;
                }
                layer.alert(result.reason,{time: 2000, icon:2});
            }

        });
    }
});
$(document).on("click",".form-group i",function(){
    if($(this).hasClass('fa-eye-slash')){
        $(this).removeClass('fa-eye-slash');
        $(this).addClass('fa-eye');
        $(this).next().attr('type', 'text');
    }else{
        $(this).removeClass('fa-eye');
        $(this).addClass('fa-eye-slash');
        $(this).next().attr('type', 'password');
    }
})