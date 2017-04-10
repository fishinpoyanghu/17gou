    /**
 * Created by Administrator on 2015/11/26.
 *
 * 重置密码（忘记密码后）页面对应controller
 */
define([
  'app',
  'models/model_user',
  'utils/toastUtil'
],function(app){

  app.controller('ResetPasswordCtrl',
  ['$scope','$stateParams','$state','$ionicHistory','userModel','ToastUtils',
  function($scope, $stateParams, $state,$ionicHistory, userModel,ToastUtils){
    $scope.inputObject = {
      password : '',
      passwordAgain : ''
    };

    $scope.resetPasswordInputHint = {
      passwordHint : '请输入6-15位新密码',
      passwordAgainHint : '请再次输入密码'
    };

    $scope.resetPassword = function(){

      if($scope.inputObject.password === $scope.inputObject.passwordAgain){
        userModel.resetPassword($stateParams.saveKey, $scope.inputObject.password, onSuccess,onFail);
      }else{
        ToastUtils.showWarning('两次输入密码不相同！');
      }

    };

    $scope.isEnableResetPassword = function(){
      return ($scope.inputObject.password.length > 0) && ($scope.inputObject.passwordAgain.length > 0);
    };

    /**
     * 修改密码成功
     */
    function onSuccess(response, data,status,headers,config,statusText){
      if(data.code === 0){
        ToastUtils.showSuccess(data.msg);
        $state.go('login');
      }else{
        ToastUtils.showError(data.msg);
      }

    }

    /**
     * 修改密码失败
     */
    function onFail(response, data,status,headers,config,statusText){
      ToastUtils.showError('请检查网络状态，状态码：' + response.status);
    }


  }]);

});
