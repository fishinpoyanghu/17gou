/**
 * Created by Administrator on 2015/11/27.
 */
define([
  'app',
  'models/model_user',
  'utils/toastUtil',
  'components/view-nav-footer/view_nav_footer',
  'html/common/global_service'
],function(app){
  app.controller('BoundPhoneNumberCtrl',
  ['$scope','$state','$ionicHistory','$timeout','userModel','ToastUtils','Global','$stateParams',
  function ($scope, $state,$ionicHistory, $timeout,userModel,ToastUtils,Global,$stateParams) {
    var inviteCode = $stateParams.invite_code;
    if(inviteCode&&inviteCode!=null){
          Global.setInviteCode(inviteCode);
     }
    $scope.security = {
      params:baseApiUrl + "?c=user&a=code&t=" + (+new Date()),
      code:''
    }
    var redpack = $stateParams.redpacket; //用户名
    
    $scope.changeGetCodeUrl = function() {
      $scope.security.params = baseApiUrl + "?c=user&a=code&t=" + (+new Date());
    }

    $scope.verifyBtnClickable = true ;
    $scope.agreement = true;

    $scope.registerInfo = {
      phoneNumber : '',      
      verificationCode : '',
      verificationButtonText : '获取验证码',
      inviteCode : ''
    };
    $scope.registerPageInputHint = {
      phoneNumberHint : '请输入手机号码', 
      
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
        userModel.sendBindPhonedSms(number,$scope.security.code,onSuccess,onFail);
      }else{
        ToastUtils.showWarning('请先输入手机号！');
      }
    };


    $scope.bindphone = function(){   
       
      if ($scope.registerInfo.verificationCode == "") {
              ToastUtils.showWarning('请输入短信验证码！');
              return;
      } 
      if(!(/^(13|18|15|14|17)\d{9}$/i.test($scope.registerInfo.phoneNumber))){
             ToastUtils.showWarning('请输入正确的手机号码！');
             return;
      }  
      //userModel.getRegisterSms(number,$scope.security.code,onSuccess,onFail);
      userModel.bindphone({phone:$scope.registerInfo.phoneNumber,mcode:$scope.registerInfo.verificationCode},bindphoneSuccess,onFail);
    };

    $scope.clickAgreement = function(){
      $scope.agreement = !$scope.agreement;
    };


    $scope.isEnableRegister = function(){
      return ($scope.registerInfo.phoneNumber.length > 0)  
         && ($scope.registerInfo.verificationCode.length > 0) 
        &&($scope.agreement);
    };

    function onFail(response){
      ToastUtils.showError('请检查网络状态，状态码：' + response.status);
    }

    function bindphoneSuccess(response){
      var code = response.data.code;
      var msg=response.data.msg;
      switch(code){
        case 0:
          ToastUtils.showSuccess(msg);
         /* $scope.verifyBtnClickable = false ;
          setTimer();*/
         setTimeout(function() { 
          if(redpack=='yes'){
             $state.go('tab.account');
             //$state.go('qiangRedPacket');
          }else{
            $state.go('tab.account');
          }
           
           }, 2000)
          break;
        default :
          $scope.security.params = baseApiUrl + "?c=user&a=code&t=" + (+new Date());
          $scope.security.code = "";
          ToastUtils.showError(msg);
          break;
      }
    }


    function onSuccess(response){
      var code = response.data.code;
      var msg=response.data.msg;
      switch(code){
        case 0:
          ToastUtils.showSuccess(msg); 
         /* $scope.verifyBtnClickable = false ;
          setTimer();*/
         
          break;
        default :
          $scope.security.params = baseApiUrl + "?c=user&a=code&t=" + (+new Date());
          $scope.security.code = "";
          ToastUtils.showError(msg);
          break;
      }
    }

      /*添加跳转*/
     /* $scope.goindex=function(){
          $state.go('tab.mainpage');
      }
      $scope.goclassify=function(){
          $state.go('tab.classify');
      }
      $scope.goshare=function(){
          $state.go('tab.shareOrder');
      }
      $scope.gocart=function(){
          $state.go('tab.trolley');
      }
      $scope.goaccount=function(){
          $state.go('tab.account');
      }*/


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
