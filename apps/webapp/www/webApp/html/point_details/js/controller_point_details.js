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
    'pointDetailsCtrl',['$scope','$ionicHistory','$location','$state','$stateParams','$timeout','userModel','ToastUtils','Storage',
      function($scope,$ionicHistory,$location,$state,$stateParams,$timeout,userModel,ToastUtils,Storage){
        var sessId = Storage.get("sessId");
        var myPointData = Storage.get('myPointData_' + sessId);
        if(!myPointData) {
          getMyPoint()
        } else {
          $scope.myPointData = myPointData;
        }
        console.log($scope)
        $scope.goBack = function(){
          $ionicHistory.goBack();
        };
        $scope.myPointDetailsData = [];
        $scope.page = 0;
        $scope.isLoadFinished = true;
        getMyPointDetails();
        $scope.getMyPointDetails = getMyPointDetails;
        function getMyPointDetails(doRefresh) {
            if (!$scope.isLoadFinished) return;
            $scope.page++;
            $scope.isLoadFinished = false;
            userModel.getMyPointDetails($scope.page, function(xhr, re) {
                var code = re.code;
                if (code == 0) {
                    var data = re.data;
                    var len = data.length;
                    for (var i = 0; i < len; i++) {
                        $scope.myPointDetailsData.push(data[i]);
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
          $scope.myPointDetailsData = [];
          getMyPointDetails('doRefresh');
        }
        function getMyPoint() {
          userModel.getMyPoint(function(xhr,re) {
            if(re.code == 0) {
              $scope.myPointData = re.data;
              Storage.set('myPointData_' + sessId,re.data);
            } else {
              ToastUtils.showError(re.msg);
            }
          },function(xhr,re) {
            ToastUtils.showError(re.msg);
          })
        }

      }])

});
