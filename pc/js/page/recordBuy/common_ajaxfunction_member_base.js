/*
 * Description : 云购个人中心
 * Author : lijianyun@ddtkj.com
 * Time : 2015年 9月16日
 * */
	//全局变量
var signToken="";//签到token
var bindQQFlag=0;//qq绑定状态
var bindWxFlag = 0;//微信绑定状态
var bindWbFlag = 0;//微博绑定状态
var tableId='';//
	$(function(){
		ajaxMemberDetail();//加载会员基本信息
		ajaxHotWord(bindHotWord);//获取热点关键词
		//我的云购记录
		$(".MyzhLi").hover(function (){
			$(".Myzh").show();
			$(".MyzhLi i").attr("class","bottom");
		},function(){
			$(".Myzh").hide();
			$(".MyzhLi i").attr("class","top");
		});
		//修改昵称
		$("#nicknamemsg").click(function(){
			$(this).hide();
			$("#updateNickName").show();
			$("#updateNickName").focus();
			$("#updateNickName").select();
			$("#updateNickName").blur(function(){
				updateNickName();
			});
		});
		//修改时间标识
		var newda=new Date();
		newdaHours=newda.getHours();
		if(newdaHours>=7&&newdaHours<=10){
			$("#timeMark").html("早上好");
			$("#prompt").html("拥抱积极的心态，改变现有的生活。");
			$(".c_homepage_header").css({background:"url(/static/new/img/show/morning.jpg) no-repeat"});
		}else if(newdaHours>10&&newdaHours<=13){
			$("#timeMark").html("中午好");
			$("#prompt").html("这一刻的犹豫，很有可能便是下一刻的遗憾。");
			$(".c_homepage_header").css({background:"url(/static/new/img/show/morning.jpg) no-repeat"});
		}else if(newdaHours>13&&newdaHours<=18){
			$("#timeMark").html("下午好");
			$("#prompt").html("成功的秘籍就是坚持一下，再坚持一下。");
			$(".c_homepage_header").css({background:"url(/static/new/img/show/nooning.jpg) no-repeat"});
		}else{
			$("#timeMark").html("晚上好");
			$("#prompt").html("即使梦想被搁浅，我们还能找到新的目标去努力实现。");
			$(".c_homepage_header").css({background:"url(/static/new/img/show/evening.jpg) no-repeat"});
		}
		
		//全部 今天 等按钮点击事件 清空事件选择框
		$(".b_choose_day li").click(function(){
			$(".b_choose_cal input").val("");
		});
		//点击便签的时候选中全部按钮 并将时间输入框清空
		$(".b_record_title li a").click(function(){
			$(".b_choose_cal input").val("");
			$(".b_choose_day li").removeClass("b_choose_this");
			$(".b_choose_day li").eq(0).addClass("b_choose_this");
		});
	});
	//更改昵称
	function updateNickName(){
		var nickName = $("#updateNickName").val();
		var str = /^[a-zA-Z0-9\u4e00-\u9fa5]+$/;
		if(nickName == null || nickName == ""){
			layer.tips('昵称不能为空!', '#updateNickName');
			$(".c_nickname_instruc").html("昵称不能为空!");
			$("#updateNickName").focus();
			return;
		}else if(!str.test(nickName)){
			layer.tips('昵称只能由数字、字母、汉字组成!', '#updateNickName');
			$("#updateNickName").focus();
			return;
		}else {
			$.ajax({
				type: "post",
				url: "/member/updateMemberNickname.do",
				dataType:'json',
				data:{
					nickname : nickName
				},
				success:function(data){
				if(data.status){
					$("#updateNickName").hide();
					if(nickName.length>5){
						$("#nicknamemsg").html(nickName.substring(0, 5)+"...");
					}else{
						$("#nicknamemsg").html(nickName);
					}
					$("#nicknamemsg").show();
				}else{
					layer.tips(data.msg, '#updateNickName');
					$("#updateNickName").focus();
					$("#updateNickName").val($("#nicknamemsg").html());
				}
			}
		});
		
	  }
	}
	/*
	 * 方法描述：获取会员个人信息
	 * 返回值：Json格式   
	 */
	function ajaxMemberDetail(){
		$.ajax({
			type: "post",
			url: "/member/memberInfo.do",
			dataType:'json',
			beforeSend: ajaxBeforeSendOfDetail,
	    	complete: ajaxCompleteOfDetail,
			success:function(data){
					if (data.status) {
						// 会员头像

						var pic = document.getElementById("alterFace");
						if (pic != null) {
							pic.src =imagePath+data.memberAccountVO.icons;
						}
/*
						$("#rankName").html(data.rankName);// 会员等级*/
						// 红包个数
						$("#redCount").html(data.redCount);
						// 可用佣金
						/*$("#brokerageCount").html(
								moneyFormat(data.memberAccountVO.brokerageUsable));*/
						$("#accountMoney").html(moneyIntFormat(data.memberAccountVO.rechargeUsable+data.memberAccountVO.scoreConvertUsable));
						// 剩余可用金额（消费记录页）
						/*$("#statistics4")
								.html(
										"（￥"
												+ parseInt(data.mem.account.moneyTotal
														+ data.mem.account.scoreTotal
														+ data.mem.account.jsdCardTotal)
												+ '）');*/
						// 可用余额
						$("#accountUsableMoney").html(moneyIntFormat(data.memberAccountVO.rechargeUsable
								+Math.floor(data.memberAccountVO.brokerageUsable)+data.memberAccountVO.scoreConvertUsable
								+Math.floor(data.memberAccountVO.rebateUsable)));
						// 可用购物卡
						$("#JSDshopCard").html(
								moneyIntFormat(data.memberAccountVO.cardUsable));
						// 积分可用
						$("#scoreBalance").html(moneyIntFormat(data.memberAccountVO.scoreUsable));
						// 佣金可用
						$("#brokerageUsable").html(moneyFormat(data.memberAccountVO.brokerageUsable));
						// 佣金可用
						$("#rebateUsable").html(moneyFormat(data.memberAccountVO.rebateUsable));
						/*
						 * if(typeof($("#money").html()) != "undefined"){ //消费明细
						 * $("#money").html(moneyIntFormat(data.mem.account.moneyTotal+data.mem.account.scoreTotal+data.mem.account.brokerageTotal)) }
						 * //佣金
						 * $("#mem_brokerageUsable").html((data.mem.account.brokerageUsable).toFixed(2));
						 */
						// 昵称
						var nickname = data.memberAccountVO.nickname;
						if (nickname.length > 5) {
							$("#nicknamemsg").html(
									nickname.substring(0, 5) + "...");
						} else {
							$("#nicknamemsg").html(nickname);
						}
						$('#updateNickName').val(nickname);
						// 查看晋商贷卡手机号
						$("#cardMobile").html(data.memberAccountVO.mobile);
						// 头部手机号
						$("#memberMobile").html(data.memberAccountVO.mobile);
						// 会员等级
						$("#rankName").html(data.rankName);

						$("#mobile").html(data.memberAccountVO.mobile);// 会员手机号
						$("#ID").html(data.memberAccountVO.mid);// 会员ID

						var email = data.memberAccountVO.email;
						if (email == "") {
							// 会员顶部显示邮箱
							$("#email").html("未绑定");// 会员邮箱
							$("#updateEmailA").html("绑定");// 修改a标签的文字
							// 安全中心邮箱修改显示
							$("#updateEmailHideEmailView").html("--");
							// 如果未绑定邮箱，修改邮箱隐藏、绑定邮箱显示
							$("#updateEmailView").hide();
							$("#bindEmailView").show();
							// 修改手机号，判断邮箱是否绑定
							$("#emailStatus").removeClass("c_find_way_email")
									.addClass("c_newadd_color");
							$(".c_newadd_color").html("未绑定");
						} else {
							$("#email").html(email);// 会员邮箱
							// 安全中心邮箱修改显示
							$("#updateEmailHideEmailView").html(
									data.memberAccountVO.hideEmail);
							// 用邮箱修改手机号
							$("#updateMobileHideEmail")
									.html(data.memberAccountVO.hideEmail);

							// 修改邮箱，隐藏修改号码
							$("#updateEmailHideMail").html(data.memberAccountVO.hideEmail);

							$("#updateEmailView").show();
							$("#bindEmailView").hide();

							// 修改手机号，判断邮箱是否绑定
							$("#emailStatus").removeClass("c_newadd_color")
									.addClass("c_find_way_email");
							$(".c_find_way_email").html("立即修改");
						}

						// 安全中心（手机号码）(修改前面的)
						$("#hideMobile").html(data.memberAccountVO.hideMobile);
						$("#updateHideMobile").html(data.memberAccountVO.hideMobile);
						$("#bindEmailHideMobile").html(data.memberAccountVO.hideMobile);
						// 签到数据
						$("#signLogo").attr("title",
								"已经连续签到" + data.memberAccountVO.signDays + "天");
						$(".c_personage_data").attr("title",
								"已经连续签到" + data.memberAccountVO.signDays + "天");
						if(data.memberAccountVO.signTime){
							$('#signTime').val(data.memberAccountVO.signTime.time);
						}else{
							$('#signTime').val(0);
						}
						$('#mid').val(data.memberAccountVO.mid);
						//邀请页面动态链接
						$("#wxImg").attr("src",
								"http://wx.ygqq.com/main_controller/wx_qrCode.do?content=http://wx.ygqq.com/share-"+data.memberAccountVO.mid+".html");
						var img = '<img  src="/static/img/front/hhr.png" style="width: 53px;"/>';
						if(data.memberAccountVO.partnerFlag!=0){
							$('#userName').html(data.memberAccountVO.mobile+img)
						}else{
							$('#userName').html(data.memberAccountVO.mobile)
						}
						checkSign();
						if(typeof($('#rankWith').html())!="undefined"){
							bindQQFlag = data.memberAccountVO.bindQqFlag;
							bindWxFlag = data.memberAccountVO.bindWxFlag;
							bindWbFlag = data.memberAccountVO.bindWbFlag;
							checkQQandWx();//判断微信 qq绑定状态
							var width = 88;
							if (!$($(".c_save_state")[2]).is(":visible")) {
								width += 82;
							}
							if (bindWxFlag > 0) {
								width += 82;
							}
							if (width == 88) {
								$("#rank").html("低");
							} else if (width == 170) {
								$("#rank").html("中");
							} else {
								$("#rank").html("高");
							}
							$("#rankWith").width(width);
							 //自动打开
							   if(flag==1){//改密码
									$($(".c_save_rechange")[0]).find("a").click();
								}else if(flag==2){//改手机
									$($(".c_save_rechange")[1]).find("a").click();
									$($($(".c_save_rechange")[1]).parent(".c_set_save_box").find(".img_click")[0]).trigger("click");
								}else if(flag==3){//邮箱
									if($('#email').html()=="未绑定"){
										$($(".c_save_rechange")[2]).find("a").click();
										$($($(".c_save_rechange")[2]).parent(".c_set_save_box").find(".img_click")[0]).trigger("click");
									}else{
										$($(".c_save_rechange")[3]).find("a").click();
										$($($(".c_save_rechange")[2]).parent(".c_set_save_box").find(".img_click")[0]).trigger("click");
									}
								}
						}
					}
			},
			error: function(r) {
				handlingException(r);
			} //错误处理
		});
		
	}
	/*
	 * 描述：顶部点击按钮搜索
	 * 事件类型：鼠标点击或 回车事件
	 * */
	function actionHeaderSearch(){
		    location.href='/goods/allCat.do?q='+$("#q").val();
	}
/*签到*/
function sign(){
	var signTime = $('#signTime').val();
	var memberMid = $('#mid').val();
	if(memberMid!=''){
		var date = new Date(signTime*1000);
	    signTime = date.getFullYear().toString()+(date.getMonth() + 1)+date.getDate().toString()+"";
		date = new Date();
	    var today = date.getFullYear().toString()+(date.getMonth() + 1)+date.getDate().toString()+"";
	    if(today!=signTime){
	    	ajaxSign(signToken,signReturn);//会员签到
	    }
	}else{
		window.location.href ="/api/uc/login.do";//跳转到登录
	}
}
/*签到回调*/
function signReturn(){
	ajaxMemberDetail();
}
/*
 * 描述：检查是否签到
 * 参数：无
 * 返回值：无
 * */
function checkSign(){
	var signTime = $('#signTime').val();
	var date = new Date(parseInt(signTime));
    signTime = date.getFullYear().toString()+(date.getMonth() + 1)+date.getDate().toString()+"";
	date = new Date();
    var today = date.getFullYear().toString()+(date.getMonth() + 1)+date.getDate().toString()+"";
    if(today==signTime){
    	$("#signLogo").html("已签到")
    	$("#sign").html("已签到")
    }else{
    	ajaxGetSign(bindSignToken);
    }
}

/*
 * 描述：绑定Token
 * 参数：result json
 * 返回值：
 * */
function bindSignToken(result){
	signToken=result.signToken;
}

/*
 * 方法描述：获取会员签到信息
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   会员信息
 * 返回值主体：total
 */
function ajaxGetSign(callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url:"/member/getSign.do",
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}

/*
 * 方法描述：会员签到
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   会员信息
 * 返回值主体：total
 */
function ajaxSign(signToken,callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url:"/member/sign.do?signToken="+signToken,
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}
	/*
	 * 方法描述：获取会员的地址信息 
	 * 参数：Function callback ajax成功后回调函数
	 * 返回值：Json格式 
	 */
	function ajaxMemberAddressDetail(callback) {
		$.ajax({
			type : "post",
			url : "/member/address/addressList.do?" + 'randomTime='
					+ (new Date()).getTime(),
			dataType : 'json',
			beforeSend : ajaxBeforeSendOfList,
			complete : ajaxCompleteOfDetail,
			success : function(data) {
				callback(data);
			},
			error : function(r) {
				handlingException(r);
			} // 错误处理
		});
	}
	
	/*
	 * 方法描述：会员添加或修改地址时获取验证码
	 * 参数：Function callback ajax成功后回调函数
	 * 返回值：Json格式
	 */
	function ajaxMemberAddressSendMsg(callback) {
		$.ajax({
			type : 'post',
			url : "/member/address/getCode.do?" + 'randomTime='
					+ (new Date()).getTime(),
			dataType : 'json',
			beforeSend : ajaxBeforeSendOfDetail,
			complete : ajaxCompleteOfDetail,
			success : function(result) {
				callback(result);
			},
			error : function(r) {
				handlingException(r);
			} // 错误处理
		});
	}
	
	/*
	 * 方法描述：会员保存地址 
	 * 参数： String name 收货人姓名；
	 *      String province 收货省份；
	 *      String city 收货地市；
	 *      String area 收货区县；
	 *      String street 收货地址详情；
	 *      String mobile 收货人手机号；
	 *      String code 验证码；
	 *      Function callback ajax成功后回调函数；
	 * 返回值：Json格式 
	 */
	function ajaxMemberAddressAdd(name, province, city, area, street, mobile, code,zfbAccount,zfbName,callback,isdefault) {
		$.ajax({
			type : 'post',
			url : '/member/address/add.do',
			data : {
				consignee : name,
				province : province,
				city : city,
				area : area,
				street : street,
				mobile : mobile,
				ifCheckCard : 0,
				zfbAccount : zfbAccount,
				zfbName : zfbName,
				code : code,
				isdefault : isdefault
			},
			dataType : 'json',
			beforeSend : ajaxBeforeSendOfDetail,
			complete : ajaxCompleteOfDetail,
			success : function(result) {
				callback(result);
			},
			error : function(r) {
				handlingException(r);
			} // 错误处理
		});
	}
	
	/*
	 * 方法描述：会员修改地址信息保存
	 * 参数： String name 收货人姓名；
	 *      String province 收货省份；
	 *      String city 收货地市；
	 *      String area 收货区县；
	 *      String street 收货地址详情；
	 *      String mobile 收货人手机号；
	 *      String code 验证码；
	 *      String addressId 地址id；
	 *      Function callback ajax成功后回调函数；
	 * 返回值：Json格式 
	 */
	function ajaxMemberAddressUpdate(name, province, city, area, street, mobile, code, addressId, zfbAccount,zfbName,callback,isdefault) {
		$.ajax({
			type : 'post',
			url : '/member/address/update.do',
			data : {
				consignee : name,
				province : province,
				city : city,
				area : area,
				street : street,
				mobile : mobile,
				ifCheckCard : 0,
				code : code,
				zfbAccount : zfbAccount,
				zfbName : zfbName,
				id : addressId,
				isdefault : isdefault
			},
			dataType : 'json',
			success : function(result) {
				callback(result);
			},
			error : function(r) {
				handlingException(r);
			} // 错误处理
		});
	}
	
	/*
	 * 方法描述：会员删除地址
	 * 参数： String id 地址id；
	 *      Function callback ajax成功后回调函数；
	 * 返回值：Json格式 
	 */
	function ajaxMemberAddressDelete(id, callback) {
		$.ajax({
			type : 'post',
			url : "/member/address/delete.do",
			data : {id : id},
			dataType : 'json',
			success : function(result) {
				callback(result);
			},
			error : function(r) {
				handlingException(r);
			} // 错误处理
		});
	}
	
	/*
	 * 方法描述：会员设置默认地址
	 * 参数： String id 地址id；
	 * 返回值：Json格式 
	 */
	function ajaxMemberAddressSetDefault(id,callBack) {
		$.ajax({
			type : 'post',
			url : "/member/address/isdefault.do?&randomTime="
					+ (new Date()).getTime(),
			data : {id : id},
			dataType : 'json',
			success : function(result) {
				if(callBack){
					callBack(result);
				}else{
					location.reload();
				}
			}
		});
	}
	 /*
	  * 描述：绑定搜索热词
	  * 参数：result json 
	  * 返回值：无
	  * */
	 function bindHotWord(result){
		var html='';
		words = JSON.parse(result.words);
		$(words).each(function(index, word){
			html+='<a href="/goods/allCat'+word.cid+'.html">'+word.word+'</a>';
		});
		$(".search_span_a").html(html);
	}
	 /*
	  * 方法描述：获取搜索热词
	  * 返回值：Json格式   往期揭晓集合
	  * 返回值主体：words
	  */
	 function ajaxHotWord(callbackFun) {
	 	$.ajax({
	 		type: "post",
	 		timeout:timeout,
	 		dataType: "json",
	 		cache: true,
	 		url:"/goods/hotwords.do",
	 		success: function(result) {
	 			//调用绑定页面数据方法
	 			if (verification(result)) { //验证result 是否有效
	 				callbackFun(result);//调用回调函数 传输json结果
	 			}
	 		},
	 		error: function(r) {
	 			handlingException(r);
	 		} //错误处理
	 	});
	 }
	 /* 数字转动*/
	 function showSun(){
		
		var attr=0;
		 attr=$(".yJoinNum input").val();
		
		var attr1=[];
		var nums=0;
		$('.yNumList').remove();
		for(i=0;i<attr.length;i++){
			var nums=attr.slice(i,i+1);
			attr1.push(nums);
			$('.w_ci_bg').before('<span class="yNumList"><ul style="margin-top: -270px;">'+
					'<li t="9">9</li><li t="8">8</li><li t="7">7</li><li t="6">6</li><li t="5">5</li>'+
			'<li t="4">4</li><li t="3">3</li><li t="2">2</li><li t="1">1</li><li t="0">0</li></ul></span>');
		}
		$(".yNumList ul").css("marginTop","-270px");
		var list=0;
		for(i=0;i<attr1.length;i++){
			list=attr[i];
			$($(".yNumList ul")[i]).animate({marginTop:(list*30-270)},1000)
		}
		if($(".yNumList").length<attr1.length){
				var more=attr1.length-$(".yNumList").length;
				for(i=0;i<more;i++){
					$($(".yNumList")[0]).clone(true).insertAfter($($(".yNumList")[$(".yNumList").length-1]))
				}
			}
	}
