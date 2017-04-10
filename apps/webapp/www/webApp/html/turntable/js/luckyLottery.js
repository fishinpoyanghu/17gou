/**
 * 获奖记录
 * Created by Administrator on 2016/1/7.
 */
define([
  'app',
  'models/model_user',
  'utils/toastUtil',
  'html/common/storage'

],function(app){

  app.controller(
    'luckyLotteryCtrl',['$scope','$ionicHistory','$location','$state','$stateParams','$timeout','userModel','ToastUtils','Storage',
      function($scope,$ionicHistory,$location,$state,$stateParams,$timeout,userModel,ToastUtils,Storage){
        $scope.goBack = function(){
          $ionicHistory.goBack();
        };
        $scope.lotteryListData = [];
        $scope.page = 0;
        $scope.isLoadFinished = true;
        getLotteryList();
        $scope.getLotteryList = getLotteryList;
        function getLotteryList(doRefresh) {
            if (!$scope.isLoadFinished) return;
            $scope.page++;
            $scope.isLoadFinished = false;
            userModel.getLotteryList_1($scope.page,20, function(xhr, re) {
                var code = re.code;
                if (code == 0) {
                    var data = re.data;
                    var len = data.length;
                    for (var i = 0; i < len; i++) {
                        $scope.lotteryListData.push(data[i]);
                    }
                    if (len < 10) {
                        $scope.moreDataCanBeLoaded = false;
                    } else {
                        $scope.moreDataCanBeLoaded = true;
                    }
                    $scope.$broadcast('scroll.infiniteScrollComplete');
                } else {
                    ToastUtils.showMsgWithCode(code, re.msg);
                }
            }, function(response, data) {
                ToastUtils.showMsgWithCode(7, '获取余额列表失败：' + '状态码：' + response.status);
            }, function() {
                $scope.isLoadFinished = true;
                if (doRefresh) $scope.$broadcast('scroll.refreshComplete');
            })
        }
        $scope.doRefresh = function() {
          $scope.page = 0;
          $scope.lotteryListData = [];
          getLotteryList('doRefresh');
        }
        

      }])

});
