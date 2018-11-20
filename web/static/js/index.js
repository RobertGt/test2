var reset = null;
$.validator.setDefaults({
    submitHandler: function() {
		var http = WEBHTTP + 'admin/login';
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
                    $.cookie('login_token', result.result.token, {path: '/' });
                    $.cookie('login_account', result.result.account, {path: '/' });
                    location.href = "view/index/index.html"
					return false;
				}
				layer.alert(result.reason,{time: 2000, icon:2});
			}
		});
    }
});
$(document).on("click","#reset",function(){
    layer.open({
        type: 2,
        title: '',
        shadeClose: true,
        shade: 0.8,
        area: ['18%', '45%'],
        content: 'view/index/reset.html'
    });
})
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
