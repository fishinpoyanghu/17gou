/**
 * Created by Administrator on 2016/1/7.
 */
define([
  'app',
  'models/model_app',
  'html/common/constants',
  'utils/toastUtil',
  'components/view-indiana-record-item/view_indiana_record_item',
  'components/view-list-view/view_list_view',
  'components/view-list-item/view_list_item',
  'components/view-buy-pop/view_buy_pop',
  'components/view-buy-number-pop/view_buy_number_pop',
  'html/common/service_user_info',
], function (app) {

  app.controller(
    'hisPageCtrl', ['$scope', '$stateParams', '$ionicHistory', '$state', 'AppModel','userInfo',

      function ($scope, $stateParams, $ionicHistory, $state, AppModel,userInfo) {
        $scope.uicon = $stateParams.uicon;
        $scope.unick = $stateParams.unick;
        $scope.uid = $stateParams.uid;
        $scope.isIndianaRecordEmpty = false;
        $scope.isWinRecordEmpty = true;
        $scope.isBuyNumShow = false;


        /**
         * 云购记录
         */
        $scope.indianaRequestUrl = AppModel.getRecordListUrl();

        $scope.indianaRequestParams = {
          uid: $scope.uid,
          status: null
        };
        $scope.indianaCallBack = {
          setData: function (data) {
            $scope.indianaList = data;
            angular.forEach($scope.indianaList,function(n,i,data){
            	n.isHispage = true
            })
            $scope.indianaList
            console.log($scope.indianaList)
          },
          setEmpty: function (isEmpty) {
            $scope.isIndianaRecordEmpty = isEmpty;
          }
        };
        $scope.itemCallBack = {
          showBuyPop: function (activity) {
            $scope.$broadcast('view-buy-pop.show', activity);
          },
          showBuyNum: function (activityId, uid) {
            $scope.$broadcast('view_buy_number_pop.show', {compId:'1-4-1', activityId:activityId, uid:$scope.uid});
          },
          showWinOne: function (unick, uicon, uid) {
            ssjjLog.log('uid1'+userInfo.getUserInfo().uid);
            ssjjLog.log('uid2'+uid);
            if(userInfo.getUserInfo().uid==$stateParams.uid||userInfo.getUserInfo().uid==uid){
              $state.go('myIndianaRecord');
            }else{
              $state.go('hispage', {uicon:uicon, unick:unick, uid:uid});
              ssjjLog.log('uicon'+uicon);
              ssjjLog.log('unick'+unick);
              ssjjLog.log('uid'+uid);
            }
          }
        };


        /**
         * 中奖记录
         */
        /*$scope.winRequestUrl = AppModel.getWinRecordListUrl();
        $scope.winRequestParams = {
          uid: $stateParams.uid,
          from: null
        };*/
        /**
         * 晒单记录
         */
        $scope.ShareRequestUrl = AppModel.getShareRecordListUrl();
        $scope.winRequestParams = {
          uid: $stateParams.uid,
          from: null
        };


        $scope.gotoDetail = function(id){
          $state.go('activity-goodsDetail', {activityId:id});
        };
        $scope.winCallBack = {
          setData: function (data) {
            $scope.winList = data;
          },
          setEmpty: function (isEmpty) {
            $scope.isWinRecordEmpty = isEmpty;
          }
        };

        $scope.startToMainPage = function() {
          $state.go('tab.mainpage');
        }


      }

    ])

});
