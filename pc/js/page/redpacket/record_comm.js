/*
* Description : 首页主体数据获取与绑定
* Author : yucaiyan@ddt.com
*  Time : 2015-5-1 */
//全局变量
var validCode=true;    //获取验证码倒计时变量

//初始化函数
$(function(){
    $(window).resize();
    record_buy();   //个人中心-选项卡
    record_sift();  //个人中心-按时间筛选
    //logistics();    //查看物流弹窗 2015-9-23 删除
});

//逻辑函数
//元素实时根据窗口变化
$(window).resize(function(){
    $(".b_msgbox_bj").height($("body").height()); //灰色背景height=body的height
    $("#b_logistics_window").css({left:($(window).width()-$("#b_logistics_window").width())/2,top:($(window).height()-$("#b_logistics_window").height())/2});    //物流弹窗设置弹框top、left
})
// 个人中心-选项卡
function record_buy(){
    $(".b_record_title li").click(function(){
        var index=$(this).index(".b_record_title li");  //获取选中元素的索引
        $(this).addClass("b_record_this").siblings().removeClass("b_record_this");//为选中元素的增加样式
        $(".b_record_list").eq(index).show().siblings().hide();  //与选中元素相对应的模块内容显示
    })
}
//个人中心-按时间筛选
function record_sift(){
  $(".b_choose_day li").click(function(){
      $(this).addClass("b_choose_this").siblings().removeClass("b_choose_this");//为选中元素的增加样式
  })
}
//查看物流弹窗  删除2015-9-23 
// function logistics(){
    //点击“查看物流”显示弹框
    // $(".b_logistics").click(function(){
    //     $("#b_logistics_window").show();   //物流弹框
    //     $(".b_msgbox_bj").height($("body").height()).show();  //灰色背景
    // })
    //点击“关闭按钮”关闭弹框
    // $("#b_logistics_close").click(function(){
    //     $("#b_logistics_window").hide();  //物流弹框
    //     $(".b_msgbox_bj").hide();  //灰色背景
    // })
    //点击“确定”关闭弹框
    // $(".b_logistics_confirm").click(function(){
    //     $("#b_logistics_window").hide();  //物流弹框
    //     $(".b_msgbox_bj").hide();  //灰色背景
    // })
//}
//获取验证码倒计时
function get_code(obj){
    obj.click(function(){
        var time=120;
        if (validCode) {
            validCode=false;
            var t=setInterval(function  () {
            time--;
                obj.html(time+"秒后重新获取");
                if (time==0) {
                    clearInterval(t);
                obj.html("重新获取");
                    validCode=true;
                }
            },1000)
        }
    })
}

//查看物流弹窗 2015-9-23 添加
$(window).resize(function(){
    $("#paywindow").css({left:($(window).width()-542)/2+"px",top:($(window).height()-360)/2+"px"});
    });
    $(".c_see").click(function(){
        $("#paywindow").show();
        $(".b_msgbox_bj").height($("body").height()).show();  //灰色背景
    })
    $(".pay_window_quit").click(function(){
        $("#paywindow").hide();
        $(".b_msgbox_bj").hide();  //灰色背景
    })