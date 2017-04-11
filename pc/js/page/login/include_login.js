
	$(window).resize(function(){
		var widthL=$("body").width();
		 $(".add_car").css({left:(widthL-270)/2+"px"});
		 $(".c_add_ygqq").css({right:($("body").width()-130)/2+"px"});
	});
	$(window).resize();
   
	    $(".loginContent #user input").focus(function(){
			$(".loginContent #user input").css({color:"#333"});
			$(".c_error_username").hide();
			if($(".loginContent #user input").val() == "用户名/邮箱/手机号"){
				$(".loginContent #user input").val("");
			}
		})
		
		$(".loginContent #user input").blur(function(){
			var username = $(".loginContent #user input").val();
			if(username =="用户名/邮箱/手机号" || username =="" || username == null){
				$("#username").val("用户名/邮箱/手机号").css({color:"#c9c9cf"});
			}
		})
		
		$("#prompt").focus(function(){
			 $(this).hide();
			 $("#password").show();
			 $("#password").focus();
		})
		$("#password").blur(function(){
			var password = $("#password").val();
			if( password=="请输入密码" || password == "" || password == null){
				$(this).hide();
				 $("#prompt").show();
			}
		})
		//点击用户名错误提示信息
		$(".c_error_username").click(function(){
			$(".loginContent #user input").focus()
			$(".c_error_username").hide();
		});
  
	    $("#password").focus(function(){
			$("#password").css({color:"#333"});
			$(".c_error_password").hide();
			if($("#password").val() == "请输入密码"){
				$("#password").val("");
			}
		})
		//点击密码错误提示信息
		$(".c_error_password").click(function(){
			$(".loginContent #pas input").focus()
			$(".c_error_password").hide();
		});
	 	//按回车键执行下一步操作(按回车登录)
	   $(document).keydown(function(event){ 
	   	 if(event.keyCode == 13){
	   		$("#loginSubmit").trigger("click");
	   	}
	   });  
		//会员登录
		$("#loginSubmit").click(function(){
				var username = $("#username").val();		
				var password = $("#password").val();	
				if(username==null || username == "" || username == "用户名/邮箱/手机号" ){
					$(".c_error_username").html("请输入用户名或邮箱或手机号");
					$("#username").siblings(".c_error_username").show(100).delay(2000).hide(0);
					return ;
				}else if(password==null ||password =="" || password == "请输入密码"){
					$(".c_error_password").html("请输入密码");
					$("#password").siblings(".c_error_password").show(100).delay(2000).hide(0);
					return ;
				}else{
					var passwordTips = password;
					//doEncrypt();
					password = $("#password").val();
					
					$("#loginSubmit").val("等待...");
					$.ajax({
						type: "post",
						url: "/api/uc/popupDoLogin.do?id="+Math.random(),
						dataType:'json',
						data:{
							username :username,
							isForum:isForum,
							password : password
						},
						success:function(data){
							if(data.status){
								jaaulde.utils.cookies.set('orderUrl', '',{path:"/"});
								if(data.msg != ''&& data.msg != null){
									window.location.href= data.msg;
								}else{
									if(typeof(login_wp)!="undefined"){
										top.location.href="/api/uc/gotoSkyDrive.do"
									}else{
										window.location.href="/member_new/account.do"
									}
								}	
							}else{ 
								$("#loginSubmit").val("立即登录");
								if(data.code == 121010 || data.code == "login-e1"){
									$("#password").val(passwordTips);
									$(".c_error_username").html(data.msg);
									$("#username").siblings(".c_error_username").show(100).delay(2000).hide(0);
								}else if(data.code == 121004 || data.code == "login-e2" ){
									$("#password").val(passwordTips);
									$(".c_error_password").html(data.msg);
									$("#password").siblings(".c_error_password").show(100).delay(2000).hide(0);
								}else{
									$("#password").val(passwordTips);
									$(".c_error_password").html(data.msg);
									$("#password").siblings(".c_error_password").show(100).delay(2000).hide(0);
								}
								
							}
						}
					});		
			}
			 		
		});
		 /**
	      * RSA加密操作
	      * */
	     function doEncrypt(){
			var  result = $("#password").val();
		    setMaxDigits(130); 
		   	key = new RSAKeyPair(e,"",m);
			result = encryptedString(key, result);
	        $("#password").val(result);
	    }     