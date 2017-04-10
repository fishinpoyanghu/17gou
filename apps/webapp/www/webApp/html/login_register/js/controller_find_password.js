/**
 * Created by Administrator on 2015/11/26.
 *
 *  找回密码页面controller
 */
define(['app','models/model_user', 'utils/toastUtil'],function(app){

  app.controller('FindPasswordCtrl',
  ['$scope','$state','$ionicHistory','$timeout','userModel','ToastUtils',
  function($scope, $state,$ionicHistory,$timeout,userModel,ToastUtils){

    $scope.security = {
      params: baseApiUrl + "?c=user&a=code&t=" + (+new Date()),
      code:''
    }

    $scope.changeGetCodeUrl = function() {
      $scope.security.params = baseApiUrl + "?c=user&a=code&t=" + (+new Date());
    }

    $scope.verifyBtnClickable = true ;

    $scope.account = {
      phoneNumber : '',
      verificationCode : '',
      verificationButtonText : '获取验证码'
    };

    $scope.sendSmsVerificationCode = function (){
      if(!$scope.verifyBtnClickable){
        return ;
      }
      if( $scope.account.phoneNumber === ''){
        ToastUtils.showWarning('请先输入手机号！');
        return  ;
      }
      if(!$scope.security.code) {
        ToastUtils.showWarning('获取手机验证码需要输入图形验证码');
        return;
      }
      //发送短信验证码
      userModel.sendForgetPasswordSms($scope.account.phoneNumber,$scope.security.code, onSendSuccess, onSendFail);
    };

    $scope.goNext = function(){
      //if($scope.account.phoneNumber === ''){
      //  //ToastUtils.showShortNow(STATE_STYLE.WARNING,"请先输入手机号！");
      //  alert('请先输入手机号！');
      //  return ;
      //}
      //if( $scope.account.verificationCode === ''){
      //  //ToastUtils.showShortNow(STATE_STYLE.WARNING,"请输入验证码！");
      //  alert('请输入验证码！');
      //  return ;
      //}
      //校验忘记密码的短信验证码
      userModel.checkVerificationCode($scope.account.phoneNumber, $scope.account.verificationCode, onVerifySuccess, onVerifyFail);
    };

    $scope.isEnableFindPassword = function(){
      return ($scope.account.phoneNumber.length > 0) && ($scope.account.verificationCode.length > 0);
    };


    var countdown = 60 ;//60 秒倒计时
    /**
     * 60秒倒计时
     */
    function setTimer() {
      if (countdown == 0) {
        $scope.account.verificationButtonText = "获取验证码";
        countdown = 60;
        $scope.verifyBtnClickable = true ;
      }else{
        $scope.account.verificationButtonText = "重发(" + countdown + ")";
        countdown -- ;
        $timeout(function() {
          setTimer()
        },1000);
      }
    }

    /**
     * 验证码发送成功
     */
    function onSendSuccess(response, data,status,headers,config,statusText){
      if(data.code === 0){
        $scope.verifyBtnClickable = false ;
        setTimer();
        ToastUtils.showSuccess(data.msg);
      }else{
        $scope.security.params = baseApiUrl + "?c=user&a=code&t=" + (+new Date());
        $scope.security.code = "";
        ToastUtils.showError(data.msg);
      }

    }


    /**
     * 验证码发送失败
     */
    function onSendFail(response, data,status,headers,config,statusText){
      ToastUtils.showError('请检查网络状态，状态码：' + response.status);
    }


    /**
     * 验证成功
     */
    function onVerifySuccess(response, data,status,headers,config,statusText){
      if(data.code === 0){
        $state.go('resetPassword' ,{saveKey:data.data.savekey});//跳转到修改密码页面
      }else{
        ToastUtils.showError("验证失败：" + data.msg);
      }

    }


    /**
     * 验证失败
     */
    function onVerifyFail(response, data,status,headers,config,statusText){
      ToastUtils.showError('请检查网络状态，状态码：' + response.status);
    }

  }]);

});
