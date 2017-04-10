/**
 * Created by Administrator on 2015/12/29.
 */
define([
  'app',
  'models/model_red_packet',
  'html/common/constants',
  'components/view-list-item/view_list_item',
  'utils/toastUtil'
],function(app){

  app.controller(
    'RedPacketCtrl',['$scope','$ionicHistory','$state','$location','$timeout','redPacketModel','ToastUtils',
      function($scope,$ionicHistory,$state,$location,$timeout,redPacketModel,ToastUtils){

        $scope.requestUrl = redPacketModel.getUrlRedPacket();

        $scope.callBack = {
          setData: function setData(data) {
            $scope.list = data;
          }
        };

        $scope.callBackUsed = {
          setData: function setData(data) {
            $scope.usedList = data;
          }
        };

        $scope.requestParams = {
          status:1,
        };

        $scope.requestParamsUsed = {
          status:2,
        };

        $scope.goToBuy = function(){
          $state.go('tab.mainpage');
        }

        function delayRefresh(){
          $timeout(function(){
            $scope.$broadcast('view_list_view.refresh','15-1-1');
          },1000);
        }

        delayRefresh();

  }])

});
