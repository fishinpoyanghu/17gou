$("#mobile").focus(function() {
	var mobile = $("#mobile").val();
	if (mobile == "请输入手机号") {
		$("#mobile").css({
			color : "#333"
		});
		$("#mobile").val('');
	}
});
$(window).resize(function(){
	var widthL=$("body").width();
	 $(".add_car").css({left:(widthL-270)/2+"px"});
	 $(".c_add_ygqq").css({right:($("body").width()-130)/2+"px"});
});
$(window).resize(function(){
	$("#Contract").css({left:($(window).width()-$("#Contract").width())/2})
})
$(window).resize();
$("#smsCode").click(function(){
	if($(this).val()=="请输入短信验证码"){
		$(this).val("")
	}
});
$(".close").click(function(){
	$("#Contract").slideUp(600);
	$(".modal-backdrop").fadeOut();
})
$(".btn-primary").click(function(){
	$("#Contract").slideUp(600);
	$(".modal-backdrop").fadeOut();
})
if(code!=''){
	$("#registerCode").val(code).css({color:"#666"});
	$("#registerCode").attr("disabled","disabled");
}
$("#registerCode").focus(function() {
	$("#registerCode").css({
		color : "#333"
	});
	$("#registerCode").val('');
});
$("#mobile").blur(function() {
	checkMobile();
});
$("#smsCode").blur(function() {
	if($("#smsCode").val()==""){
		$("#smsCode").val("请输入短信验证码");
	}
});
$("#pas").focus(function(){
	$("#pas").hide();
	$("#password").show();
	$("#password").focus().css({
		color : "#333"
	});
});
$(".error_p").mouseover(function(){
	$(this).hide();
});
$("#password").blur(function(){
	checkPassWord();
});
//校验手机号存在	
function checkMobileExist(){
	var flag = false;
	$.ajax({
		type: "post",
		url: "/api/uc/checkMobileExist.do",
		dataType:'json',
		async: false,
		data:{
			mobile :$("#mobile").val()
			},
		success:function(data){
			if(data.status == true){
				flag = true;	
			}else if(data.status == false){
				layer.tips('手机号已注册！', '#mobile');
				$("#mobile").focus();
				$("#mobile").css({border:"1px solid #E86969"})
	            $("#mobile").next().css({background:"url(/static/userCenter/newImages/false.png) no-repeat"});
				flag = false;
			}
		}
	});
	return flag;	
}
function doRegister(){
	if(!checkMobile()){
		return;
	}
	if(!checkPassWord()){
		return;
	}
	if($('#smsCode').val()==""||$('#smsCode').val()=="请输入短信验证码"){
		layer.tips('请填写短信验证码！', '#smsCode');
		$('#smsCode').val("");
		$("#smsCode").focus();
		return;
	}
	var registerCode = $('#registerCode').val();
	if(registerCode.indexOf("YG")<0&&registerCode.length==11){
		registerCode = "YG" +registerCode
	}
	$.ajax({
		type: "post",
		url: "/api/register/doRegisterForMobile.do",
		dataType:'json',
		async: false,
		data:{
			mobile :$("#mobile").val(),
			password :$("#password").val(),
			code:$('#smsCode').val(),
			registerCode:registerCode
			},
		success:function(data){
			if(data.status == true){
				window.location.href="/api/uc/registerSuccess.html";
			}else if(data.status == false){
				alerts("提示：",data.msg,"350","150");
			}
		}
	});
}
/*检查手机号*/
function checkMobile(){
	var mobile = $("#mobile").val();
	var isMobile = /^0?1[3-8][0-9]\d{8}$/; // 手机号码验证规则
	if (mobile == "请输入手机号" || mobile == "" || mobile == null) {
		$("#mobile").val("请输入手机号")
		$("#mobile").css({border:"1px solid #E86969"})
        $("#mobile").next().css({background:"url(/static/userCenter/newImages/false.png) no-repeat"});
		return false;
	} else if (!isMobile.test(mobile)) {
		$("#mobile").focus();
		$("#mobile").css({border:"1px solid #E86969"})
		layer.tips('手机号格式不正确！', '#mobile');
        $("#mobile").next().css({background:"url(/static/userCenter/newImages/false.png) no-repeat"});
		return false;
	} else if (!checkMobileExist()) {
		return false;
	}else{
		$("#mobile").css({border:"1px solid #0697DA"})
		$("#mobile").next().css({background:"url(/static/userCenter/newImages/true.png) no-repeat"}); 
		return true;
	}
}
/*检查密码*/
function checkPassWord(){
	var password = $("#password").val();
	if( password =="登录密码" || password == "" ){
		$("#password").hide();
		$("#password").css({border:"1px solid #E86969"})
        $("#password").next().css({background:"url(/static/userCenter/newImages/false.png) no-repeat"});
		$("#pas").show();
		return false;
	}else if(!/(?!^(\d+|[a-zA-Z]+|[~!@#$%^&*?]+)$)^[\w~!@#$%\^&*?]{8,20}$/.test(password)){
		$("#password").css({border:"1px solid #E86969"})
        $("#password").next().css({background:"url(/static/userCenter/newImages/false.png) no-repeat"});
		layer.tips('密码格式不正确！', '#password');
		return false;
	}else{
		$("#password").css({border:"1px solid #0697DA"})
		$("#password").next().css({background:"url(/static/userCenter/newImages/true.png) no-repeat"});
		return true;
	}
}
