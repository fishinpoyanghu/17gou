

define(
  [
    'app',
    'components/view-publish-item/view_publish_item',
    'components/view-list-view/view_list_view',
    'components/view-list-item/view_list_item',
    'models/model_goods',
    'models/model_pintuan'
  ],

  function (app) {
  app.controller('pintuanPublishCtrl',pintuanPublishCtrl);
  pintuanPublishCtrl.$inject = ['$scope','$state','$timeout','GoodsModel','PintuanModel','$timeout'];
  function pintuanPublishCtrl($scope,$state,$timeout,GoodsModel,PintuanModel,$timeout) {
    $scope.requestUrl = '?c=team&a=activity_list';
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
      setData: function (data) {
        $scope.list = data;
         angular.forEach($scope.list, function(activity, index) {
	        	if (activity.flag == 7) {
	        		activity.status = 1;
	        	}else if (activity.flag == 8) {
	        		activity.status = 2;
	        	}
	      });
      }
    };

    $scope.$on('$ionicView.beforeEnter', function(ev, data) {
        $scope.$broadcast('view_list_view.refresh', 'publish-001');
    })



    $scope.goNext = function(){
      $state.go('tab.trolley');
    };

    $scope.goPre = function(){
      $state.go('tab.classify');
    };


    $scope.$on('view_countdown.timeout', function(event, params) {
      var activityId = params.activityId;
      var activity = getActivityById(activityId) || {};
      var getNewActivity = function(response, data) {
        var newActivity = data.data;
        if(newActivity.lucky_num) {
          $timeout(function() {
            activity.status = newActivity.status;
            activity.lucky_unick = newActivity.lucky_unick;
            activity.lucky_num = newActivity.lucky_num;
            activity.lucky_user_num = newActivity.lucky_user_num;
            activity.lucky_ip = newActivity.lucky_ip;
          });
        } else {
            $timeout(function() {
                GoodsModel.getGoodsDetail(activityId,getNewActivity);
            },1000);
        }


      };
      if(activity!=null) {
        //获得该活动的最新信息
        GoodsModel.getGoodsDetail(activityId,getNewActivity);
      }
    });

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

