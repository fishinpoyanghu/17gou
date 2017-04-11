/*
* Description : 首页主体数据获取与绑定
* Author : gaoxiaopeng@ddt.com
*  Time : 2015-5-1 */


//初始化函数
$(function(){
       tops();//2015-9-28 “编辑头像”弹窗
       
});

//逻辑函数
//S 2015-9-28 “编辑头像”弹窗
function tops(){
	$(window).resize(function(){
		$(".c_box_bg").css({height:$("body").height()+"px"});//遮罩层
		$(".c_newhead_portrait").css({left:($("body").width()-620)/2+"px",top:($(window).height()-436)/2+"px"});//弹窗居中
	});
	$(window).resize();
	$(".c_investor_img a").hover(function(){
		$(".c_text_top").show();
		$(".c_top_bg").show();
	},function(){
		$(".c_text_top").hide();
		$(".c_top_bg").hide();
	});//“编辑头像”显示
	$(".c_investor_img a").click(function(){
		$(".c_box_bg").show();
		$(".c_newhead_portrait").show();
	});//显示弹窗
	$(".c_close_btn").click(function(){
		$(".c_box_bg").hide();
		$(".c_newhead_portrait").hide();
	});//关闭弹窗
}
//E 2015-9-28

//修改头像成功以后,关闭隐藏框
function avatar_success(){
	$.ajax({
		type: "post",
		url: "/member/updateMemberIcoin.do",
		dataType:'json',
		success:function(data){
			if(data.status){
				$(".c_box_bg").hide();
				$(".c_newhead_portrait").hide();
			 	window.location.reload();
			}
		}	
	});
} 

