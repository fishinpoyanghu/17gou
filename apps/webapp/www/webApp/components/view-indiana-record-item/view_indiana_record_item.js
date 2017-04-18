/**
 * Created by suiman on 15/12/30.
 */

define(
  [
    'app',
    'components/view-countdown/view_countdown',
    'lib/ng-lazyload',
  ],
  function (app) {
    app.directive('viewIndianaRecordItem', viewRecordItem);
    viewRecordItem.$inject = [];
    function viewRecordItem() {
      return {
        restrict: 'E',
        templateUrl: function(elem, attrs) {
            //判断拼团订单和购买记录的显示该记录页面。
            if(attrs.type=='me'||attrs.type=='others'){
                var path = 'webApp/components/view-indiana-record-item/';
                var name = 'view_indiana_record_item_' + attrs.type + '.html';
                return (path+name);
            }
            else if(attrs.type=='PinTuanme'||attrs.type=='PinTuanothers'){
                var path = 'webApp/components/pintuan_view-indiana-record-item/';
                var name = 'pintuan_view_indiana_record_item_' + attrs.type + '.html';
                return (path+name);
            }
            else if(attrs.type=='GameRecordme'||attrs.type=='GameRecordothers'){
                var path = 'webApp/components/game_view-indiana-record-item/';
                var name = 'game_view_indiana_record_item_' + attrs.type + '.html';
                return (path+name);
            }


        },
        scope: {
          activity: '=',
          callback: '='
        },
        controller: function ($scope, $state) {
          $scope.gotoDetail = function () {
            $state.go('activity-goodsDetail', {activityId:$scope.activity.activity_id});
          }
        },
        compile: function (elem, attrs) {
          return {
            pre: function preLink(scope, iElem, iAttrs) {

            },
            post: function postLink(scope, iElem, iAttrs) {
              var _callback = {
                showBuyPop: scope.callback.showBuyPop || angular.noop,
                showBuyNum: scope.callback.showBuyNum || angular.noop,
                showWinOne: scope.callback.showWinOne || angular.noop
              };

              scope.timeoutCallback = function () {
                ssjjLog('time is out, activity is: '+scope.activity.activity_id)
              };

              scope.showAddPay = function (activity) {
                _callback.showBuyPop(activity);
              };

              scope.getHisNumber = function (activityId, uid) {
                _callback.showBuyNum(activityId, uid);
              };

              scope.goToHisPage = function (unick, uicon, uid) {
                _callback.showWinOne(unick, uicon, uid);
              }
              
              /*跳到百度彩票双色球页面*/
              scope.jumpShuangSeQiu = function () {
                window.location.href = 'http://wap.baidu.com/s?ie=utf-8&f=8&rsv_bp=1&ch=&tn=baiduerr&bar=&wd=%E5%8F%8C%E8%89%B2%E7%90%83';
              }
            }
          }
        }

      }
    }


  })

