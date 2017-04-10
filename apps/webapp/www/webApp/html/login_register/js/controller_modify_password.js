/**
 * Created by Administrator on 2015/11/26.
 *
 * 修改密码（知道旧密码）页面对应controller
 */
define([
  'app',
  'models/model_user',
  'utils/toastUtil',
  'html/common/global_service'
],function(app){

  app.controller('ModifyPasswordCtrl',
  ['$scope','$state','$ionicHistory','userModel','ToastUtils','Global',
  function($scope, $state,$ionicHistory,userModel,ToastUtils,Global){
    $scope.$on('$stateChangeSuccess',function(event, toState, toParams, fromState, fromParams){
      if(fromState.name=='login'){
        back()
      }
    });
    function back() {
      if (Global.isInweixinBrowser()) {
        history.back();
      } else {
        $ionicHistory.goBack();
      }
    }
    $scope.passwordObject = {
      oldPassword : '',
      newPassword : '',
      newPasswordAgain : ''
    };

    $scope.modifyPasswordInputHint = {
      oldPasswordHint : '请输入原始密码',
      newPasswordHint : '设置新密码',
      newPasswordAgainHint : '确认新密码'
    };

    $scope.modifyPassword = function(){

      if($scope.passwordObject.newPassword === $scope.passwordObject.newPasswordAgain){
        if($scope.passwordObject.oldPassword === $scope.passwordObject.newPassword){
          ToastUtils.showWarning('新密码与旧密码不能一样！');
        }else{
          userModel.modifyPassword($scope.passwordObject.oldPassword, $scope.passwordObject.newPassword, onSuccess,onFail);
        }
      }else{
        ToastUtils.showWarning('两次输入新密码不相同！');
      }

    };

    /**
     * 修改密码成功
     */
    function onSuccess(response, data,status,headers,config,statusText){
      if(data.code === 0){
        ToastUtils.showSuccess('密码修改成功，请重新登录');
        $state.go('login');
      }else if(data.code === 6){
        ToastUtils.showWarning(data.msg);
        $state.go('login');
      }else{
        ToastUtils.showError(data.msg);
      }

    }

    /**
     * 修改密码失败
     */
    function onFail(response, data,status,headers,config,statusText){
      ToastUtils.showError('请检查网络状态,状态码：' + response.status);
    }


    $scope.isEnableModifyPassword = function(){
      return ($scope.passwordObject.oldPassword.length > 0) && ($scope.passwordObject.newPassword.length > 0)  && ($scope.passwordObject.newPasswordAgain.length > 0);
    };

  }]);

});
