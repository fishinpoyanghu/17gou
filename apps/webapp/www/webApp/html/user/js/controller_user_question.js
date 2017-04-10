/**
 * Created by luliang on 2016/1/9.
 */
define(['app','utils/toastUtil'
],function(app){
  'use strict';
  app
    .controller('UserQuestionController',['$scope','$window','$stateParams','ToastUtils','$http','$sce',
      function($scope,$window,$stateParams,ToastUtils,$http,$sce){
      $scope.goBack = goBack;
      function goBack(){
        //if($ionicHistory.backView()){
        //  $ionicHistory.goBack();
        //}
        $window.history.back();
      }
      if($stateParams.viewName == 'question') {
        ToastUtils.showLoading('加载中....');
        var url = baseUrl.replace('apps/webapp/www/','');
        $http.get(url + 'uploads/other/question.html?='+(+new Date())).then(function(re) {
            $scope.html = $sce.trustAsHtml(re.data);
            ToastUtils.hideLoading();
        },function(err) {
            ToastUtils.hideLoading();
            ToastUtils.showError('加载出错');
        })
      }

      function writeBarTitle(){
        var view_name = 'question';
        view_name  = angular.isString($stateParams.viewName) ? $stateParams.viewName : view_name;
        var msg = '';
        switch (view_name){
          case 'disclaimer':
            msg = '免责声明';
            break;
          case 'privacy_policy':
            msg = '隐私政策';
            break;
          case 'question':
            msg = '常见问题';
            break;
          case 'term_of_service':
            msg = '服务条款';
            break;
          case 'about_us':
            msg = '关于我们';
            break;

        }
        $scope.title = msg;
        writeTitle(msg);
      }
      setTimeout(writeBarTitle,200);
    }]);
});
