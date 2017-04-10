/**
 * Created by Administrator on 2015/11/27.
 */
define([
  'app',
  'models/model_user',
  'html/common/global_service',
  'utils/toastUtil'],function(app){

  app.factory('userInfo', userInfo);

  userInfo.$inject = ['$state','userModel','Global','ToastUtils'] ;

  function userInfo($state,userModel,Global,ToastUtils){

    var userInformation = {
      uid : '',//用户ID
      name : '',//用户名
      nick : '',//用户呢称
      icon : 'img/default_icon.png',//用户头像 120*120
      iconraw : '',//原始头像链接
      sex : '',//用户性别 0:未知 1:男 2:女
      exp : '',//经验值，可能为负数
      score : '',//U分，可能为负数
      money : '',//平台币数量
      level : '',//用户等级
      sys_new : '',//系统消息数
      notify_reply_new : '',//新评论通知数
      notify_zan_new : '',//新赞通知数
      msg_new : '',//私信数
      last_check : '', //上一次签到的时间
      signature : '', //个性签名
      rebate_uid : '', //邀请者uid
      lucky_packet : '', //邀请者uid
      //代理模块模拟数据   以后删除
//    agencyData: {
//			agencyLevel:1,	//代表官方总代理；2代表一级代理；3代表普通代理
//			data:{
//				join_agency_time:'2016-6-23',	//加入时间
//				cash_deposit:2000,							//保证金
//				all_earning: 70000,						//累计收益
//				withdraw_deposit:'2000pp',				//可提现金额
//				data_statistics:{
//					yestodayPageView:36,			//昨日浏览量
//					allPageView: 356,				//总浏览量
//					yestodayGenerlize: 14,					//昨日推广数
//					allGenerlize: 422					//昨日推广数
//				}
//			}
//		},

      
      //end 代理模块模拟数据
      phone:'' //用户电话号码
    };

    /**
     * 更新头像
     */
    function updateHeadIcon(url){
      userInformation.icon = url ;
    }

    /**
     * 获取用户信息
     */
    function getUserInfo(){
      return userInformation ;
    }

    /**
     * 获取登录用户信息
     */
    function requestInfo(){
      userModel.getLoginUserInfo(onSuccess,onFail);
    }

    /**
     * 获取用户代理相关信息
     */
//  function getagencymsg(){
//    userModel.getagencymsg();
//  }

    /**
     * 获取用户当前未读信息
     */
    function requestNotice(){
      userModel.getLoginUserInfo(function(response, data){
        var code = data.code;
        if(code === 0){
          var noticeList = data.data;
          userInformation.sys_new = noticeList.sys_new ;
          userInformation.notify_reply_new = noticeList.notify_reply_new ;
          userInformation.notify_zan_new = noticeList.notify_zan_new ; 
          userInformation.msg_new = noticeList.msg_new ;
          Global.setNoticeNew(userInformation.sys_new,userInformation.notify_reply_new,userInformation.notify_zan_new);
        }else if(code === 6){//未登录
          ToastUtils.showWarning('加载消息失败：用户未登录或者登录状态过期！');
        }else{
          ToastUtils.showError('加载消息失败：'+data.msg);
        }
      },onFail);
    }

    function saveUserInfo(data){
      userInformation.uid = data.uid ;
      userInformation.name = data.name ;
      userInformation.nick = data.nick ;
      userInformation.icon = data.icon ;
      userInformation.iconraw = data.iconraw ;
      userInformation.sex = data.sex ;
      userInformation.exp = data.exp ;
      userInformation.score = data.score ;
      userInformation.money = data.money ;
      userInformation.level = data.level ;
      userInformation.sys_new = data.sys_new ;
      userInformation.notify_reply_new = data.notify_reply_new ;
      userInformation.notify_zan_new = data.notify_zan_new ;
      userInformation.msg_new = data.msg_new ;
      userInformation.last_check = data.last_check ;
      userInformation.signature = data.signature ;
      userInformation.rebate_uid = data.rebate_uid ;
      userInformation.phone = data.phone ;
      userInformation.lucky_packet = data.lucky_packet ;
//    userInformation.agencyData = data.agencyData ;
      //Global.setNoticeNew(userInformation.sys_new,userInformation.notify_reply_new,userInformation.notify_zan_new);
      //Global.setNoticeNew(2,3,3);
    }

    function onSuccess(response, data,status,headers,config,statusText){
      if(data.code === 0){
        saveUserInfo(data.data);
      }else{
        ToastUtils.showError(data.msg);
        $state.go('login');
      }
    }


    function onFail(response, data,status,headers,config,statusText){
      ToastUtils.showError('请检查网络状态，状态码：' + response.status );
    }



    return {
      requestInfo : requestInfo ,
//    getagencymsg: getagencymsg,
      saveUserInfo : saveUserInfo,
      getUserInfo : getUserInfo ,
      updateHeadIcon : updateHeadIcon ,
      requestNotice : requestNotice,
    }

  }

});
