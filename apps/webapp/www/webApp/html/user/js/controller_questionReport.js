
define(['app','utils/toastUtil','html/common/global_service','models/model_app'
],function(app){
  'use strict';
  app
    .controller('questionReportController',['$scope','$state','$ionicHistory','Global','ToastUtils','AppModel',
      function($scope,$state,$ionicHistory,Global,ToastUtils,AppModel){
      $scope.goBack = goBack;
      function goBack(){
      	$state.go('tab.account2');
//      if (Global.isInweixinBrowser()) {
//        history.back();
//      } else {
//        $ionicHistory.goBack();
//      }
       
      }
      $scope.feedback = {
        content:''
      }
      $scope.isReport = false;
      $scope.submit = function(){
        if($scope.isReport) return;
        if(!$scope.feedback.content) {
          ToastUtils.showTips('反馈内容不能为空')
          return;
        }
        $scope.isReport = true;
        AppModel.report($scope.feedback.content, function(xhr,re){
          if(re.code == 0) {
            ToastUtils.showSuccess('反馈成功');
            $scope.feedback.content = '';
          } else {
            ToastUtils.showMsgWithCode(code, re.msg);
          }
        },function(response){
          ToastUtils.showError('请检查网络状态，状态码：' + response.status);
        },function() {
          $scope.isReport = false;
        });
      };
   
    }]);
});
