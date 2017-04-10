/**
 * Created by luliang on 2016/1/20.
 */
define([
  'app',
  'utils/toastUtil',
  'models/model_app',
  'components/view-list-view/view_list_view'
],function(app){
  app.controller('PayResultController',['$scope','$location','$stateParams','$window','AppModel','ToastUtils','$ionicHistory','$state',
    function($scope,$location,$stateParams,$window,AppModel,ToastUtils,$ionicHistory,$state){

    var oderNum;
    var _interval_check;
    $scope.refreshOrderInfo = startCheck;
    $scope.goBack = goBack;
    $scope.goToLatestItem = goToLatestItem;
    $scope.goToMyRedPacket = goToMyRedPacket;
    function initial(){
      getInData();
      startCheck();
    }
    function getInData(){
      oderNum = $stateParams.oderNum;
    }

    function goBack(){
      $ionicHistory.goBack(-3);
    }

    function startCheck(){
      _interval_check = setTimeout(function(){
        refreshOrderInfo();
        startCheck();
      }, 3000);
    }

    function stopCheck(){
      if(_interval_check){
        clearTimeout(_interval_check);
        _interval_check = undefined;
      }
    }

    function isContinueCheck(dataResult){
      if(!dataResult || dataResult.length <= 0){
        return true;
      }else{
        stopCheck();
        return false;
      }
    }

    function refreshOrderInfo(){
      ToastUtils.showLoading('正在获取信息……');
      AppModel.getOrderResult(oderNum,function(response,data){
        var code = data.code;
        if(0 == code){
          var dataResult = data.data;
          if(isContinueCheck(dataResult)){
          }else{
            $scope.packet_stat = data.packet_stat;
            $scope.oderArray = dataResult;
          }
        }else{
          ToastUtils.showError('获取亿七购号失败：'+data.msg );
        }
      },function(response,data){
        ToastUtils.showError('网络异常：'+"请检查网络，再重新刷新一下");
      },function(){
        $scope.$broadcast('scroll.refreshComplete');
        ToastUtils.hideLoading();
      });
    }

    function goToLatestItem(activityId){
      // $location.path('/goodsDetail/'+activityId).replace();
      $state.go('activity-goodsDetail',{
        activityId:activityId
      })
    }

    function goToMyRedPacket(){
      // $location.path('/redPacket').replace();
      $state.go('redPacket')
    }

    $scope.continueBuy = function(){
      // $location.path('/tab/mainpage').replace();
      $state.go('tab.mainpage')
    };

    $scope.$on('$destroy', function() {
      stopCheck();
      ToastUtils.hideLoading();
    });

    initial();

  }]);
});
