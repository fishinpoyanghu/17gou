/**
 * Created by Administrator on 2015/11/30.
 */
define(['app',
  'models/model_user',
  'html/common/service_user_info',
  'utils/toastUtil',
  'html/common/global_service'
],function(app){

  app.controller('ModifyNickCtrl',
  ['$scope','$state','$ionicHistory','$stateParams','userModel','userInfo','ToastUtils','Global',
  function($scope,$state,$ionicHistory,$stateParams, userModel, userInfo,ToastUtils,Global){

    $scope.account = {
        nickname : ''
    } ;


    /**
     * 修改昵称
     */
    $scope.modifyNickname = function(){
      userModel.modifyNick($scope.account.nickname,onSuccess,onFail)
    };

    function onSuccess(response, data,status,headers,config,statusText){
      if(data.code === 0){
        userInfo.requestInfo();
        if (Global.isInweixinBrowser()) {
          history.back();
        } else {
          $ionicHistory.goBack();
        }
        
      }else if(data.code === 6){
        ToastUtils.showWarning(data.msg);
        $state.go('login');
      }else{
        ToastUtils.showError(data.msg);
      }
    }

    function onFail(response, data,status,headers,config,statusText){
      ToastUtils.showError('请检查网络状态，状态码：' +  response.status);
    }

  }]);

});
