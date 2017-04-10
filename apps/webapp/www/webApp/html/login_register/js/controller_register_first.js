/**
 * Created by Administrator on 2015/11/27.
 */
define([
  'app',
  'models/model_user',
   'html/common/service_user_info',
  'utils/toastUtil',
  'html/common/global_service'
],function(app){
  app.controller('RegisterFirstCtrl',
  ['$scope','$state','$ionicHistory','$ionicPopup','$timeout','userModel','userInfo','ToastUtils','Global','$stateParams',
  function ($scope, $state,$ionicHistory, $ionicPopup,$timeout,userModel,userInfo,ToastUtils,Global,$stateParams) {
      $scope.pwd_box=false;
      $scope.pwd_again_box=false;
      $scope.pwd_focus=function(){
          $scope.pwd_box=true;
          $timeout(function(){
              $scope.pwd_box=false;
          },3000)
      }
      $scope.pwd_hide=function(){
          $scope.pwd_box=false;
      }

    var inviteCode = $stateParams.invite_code;
    if(inviteCode&&inviteCode!=null){
          Global.setInviteCode(inviteCode);
     }
    $scope.security = {
      params:baseApiUrl + "?c=user&a=code&t=" + (+new Date()),
      code:''
    }

    $scope.changeGetCodeUrl = function() {
      $scope.security.params = baseApiUrl + "?c=user&a=code&t=" + (+new Date());
    }

    $scope.verifyBtnClickable = true ;
    $scope.agreement = true;

    $scope.registerInfo = {
      phoneNumber : '',
      password : '',
      passwordAgain : '',
      verificationCode : '',
      verificationButtonText : '获取验证码',
      inviteCode : '',
      nickname:''
    };
    $scope.registerPageInputHint = {
      phoneNumberHint : '请输入手机号码',
      passwordHint : '请输入6-15位密码',
      passwordAgainHint : '请再次输入密码',
      inviteCodeHint : '邀请码，没有则不填'
    };

    initInviteCode() ;
    function initInviteCode(){
      var inviteCode = Global.getInviteCode();
      if(inviteCode==null || inviteCode=='' || angular.isUndefined(inviteCode)){
        $scope.registerInfo.inviteCode = '' ;
      }else{
        $scope.registerInfo.inviteCode = inviteCode ;
        Global.removeInviteCode();
      }
    }

    $scope.isReadOnly = function(){
      var readOnly = false ;
      if($scope.registerInfo.inviteCode!=''){
        readOnly = true ;
      }
      return readOnly ;
    };

    $scope.sendSmsVerificationCode = function (){
      if(!$scope.verifyBtnClickable){
        return ;
      }
      if(!$scope.security.code) {
        ToastUtils.showWarning('获取手机验证码需要输入图形验证码');
        return;
      }
      if($scope.registerInfo.phoneNumber != ""){
        var number=$scope.registerInfo.phoneNumber;
        userModel.getRegisterSms(number,$scope.security.code,onSuccess,onFail);
      }else{
        ToastUtils.showWarning('请先输入手机号！');
      }
    };


    $scope.registerNext = function(){
      if($scope.registerInfo.verificationCode != ""){
        if($scope.registerInfo.password === $scope.registerInfo.passwordAgain) {

           var nickname= $scope.registerInfo.nickname;
            var sex =  $scope.registerInfo.sex ;
            var icon =  $scope.iconUrl ;
            var registerRequest = {
                name : $scope.registerInfo.phoneNumber ,
                password : $scope.registerInfo.password ,
                sex : 1 ,
                nick : nickname ,
                mcode : $scope.registerInfo.verificationCode,
                invite_code : $scope.registerInfo.inviteCode,
                icon : icon

            };

           userModel.register(registerRequest,regonSuccess,onFail);
          //直接单页面注册。不再跳转到注册页面2
       /*   $state.go('registerSecond', {
            phoneNumber : $scope.registerInfo.phoneNumber ,
            password : $scope.registerInfo.password ,
            mcode : $scope.registerInfo.verificationCode,
            inviteCode : $scope.registerInfo.inviteCode
          });*/
        }else{
          ToastUtils.showWarning('两次的密码不相同！');
        }
      }else{
        ToastUtils.showWarning('验证码为空！');
      }
    };

    $scope.clickAgreement = function(){
      $scope.agreement = !$scope.agreement;
    };


    $scope.isEnableRegister = function(){
     /* return ($scope.registerInfo.phoneNumber.length > 0) && ($scope.registerInfo.password.length > 0)
        &&($scope.registerInfo.passwordAgain.length > 0) && ($scope.registerInfo.verificationCode.length > 0)
        &&($scope.agreement);*/
        return ($scope.registerInfo.phoneNumber.length > 0) &&($scope.registerInfo.verificationCode.length > 0)
        &&($scope.agreement);
    };

    function onFail(response){
      ToastUtils.showError('请检查网络状态，状态码：' + response.status);
    }


    function onSuccess(response){
      var code = response.data.code;
      var msg=response.data.msg;
      switch(code){
        case 0:
          ToastUtils.showSuccess(msg);
          $scope.verifyBtnClickable = false ;
          setTimer();
          break;
        default :
          $scope.security.params = baseApiUrl + "?c=user&a=code&t=" + (+new Date());
          $scope.security.code = "";
          ToastUtils.showError(msg);
          break;
      }
    }
     /**
     * 注册成功回调
     * @param response
     */
    function regonSuccess(response){
     
      var code = response.data.code;
      var msg = response.data.msg;
      var data = response.data.data;  
      switch(code){
        case 0:
          ToastUtils.showSuccess(msg);
          userInfo.saveUserInfo(data);//保存用户信息
          $state.go('tab.mainpage');
          break;
         case 2:      //密码发送失败的情况       
             $ionicPopup.alert({
                  title: '温馨提示',
                  template:msg ,
                  okText: '确定',
              }) 
              userInfo.saveUserInfo(data);
              $state.go('tab.mainpage');   
          break;  
        default :
          ToastUtils.showError(msg);
          break;
      }
    }


    var countdown = 60 ;//60 秒倒计时
    /**
     * 60秒倒计时函数
     */
    function setTimer() {
      if (countdown == 0) {
        $scope.registerInfo.verificationButtonText = "获取验证码";
        countdown = 60;
        $scope.verifyBtnClickable = true ;
      }else{
        $scope.registerInfo.verificationButtonText = "重发(" + countdown + ")";
        countdown -- ;
        $timeout(function() {
          setTimer()
        },1000);
      }
    }

  }]);

});
