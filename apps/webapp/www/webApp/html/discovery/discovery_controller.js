/**
 * Created by suiman on 15/12/30.
 */

define(
  [
    'app',
    'components/view-publish-item/view_publish_item',
    'components/view-list-view/view_list_view',
    'components/view-list-item/view_list_item',
    'models/model_goods',
  ],

  function (app) {
  app.controller('discoveryCtrl',discoveryCtrl);
  discoveryCtrl.$inject = ['$scope','$state','$timeout','GoodsModel'];
  function discoveryCtrl($scope,$state,$timeout,GoodsModel) {
    $scope.requestUrl = '?c=nc_activity&a=activity_list';
    $scope.requestParams = {
      goods_type_id : null,
      key_word : null,
      order_key : null,
      order_type : null,
      from : null,
      count : null,
      status : 3,
      activity_type : null,
    };
    $scope.callBack = {
      // setData: function (data) {
      //   $scope.list = data;
      // }
    };

    $scope.goNext = function(){
      $state.go('tab.trolley');
    };

    $scope.goPre = function(){
      $state.go('tab.publish');
    };

    // $timeout(function() {
    //   var activityId = 72;
    //   var activity = getActivityById(activityId);
    //   activity.status = 1;
    //   activity.remain_time = 10;
    // }, 1000);

    // $scope.$on('view_countdown.timeout', function(event, params) {
    //   var activityId = params.activityId;
    //   var activity = getActivityById(activityId);
    //   var getNewActivity = function(response, data) {
    //     $timeout(function() {
    //       var newActivity = data.data;
    //       activity.status = newActivity.status;
    //       activity.lucky_unick = newActivity.lucky_user;
    //       activity.lucky_num = newActivity.lucky_num;
    //       activity.lucky_user_num = newActivity.lucky_user_num;
    //     });
    //   };
    //   if(activity!=null) {
    //     //获得该活动的最新信息
    //     GoodsModel.getGoodsDetail(activityId,getNewActivity);
    //   }
    // });

    function getActivityById(activityId) {
      var bingo = null;
      angular.forEach($scope.list, function(activity, index) {
        if(activity.activity_id==activityId) {
          bingo =  activity;
        }
      });
      return bingo;
    }

  }
})

