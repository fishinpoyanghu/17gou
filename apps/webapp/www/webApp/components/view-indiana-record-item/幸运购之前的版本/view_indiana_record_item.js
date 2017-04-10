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
          var path = 'webApp/components/view-indiana-record-item/';
          var name = 'view_indiana_record_item_' + attrs.type + '.html';
          return (path+name);
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
            }
          }
        }

      }
    }


  })

