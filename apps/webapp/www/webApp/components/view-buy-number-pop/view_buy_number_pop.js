/**
 * Created by suiman on 16/1/30.
 */

/**
 * 云购号弹窗
 */

define(
  [
    'app',
    'models/model_app',
    'utils/arrayUtil'
  ],
  function(app) {

    app.directive('viewBuyNumberPop', viewBuyNumberPop);
    viewBuyNumberPop.$inject = ['AppModel', 'ArrayUtil']
    function viewBuyNumberPop(AppModel, ArrayUtil) {
      return {
        restrict: 'E',
        scope: {
          compId: '@',
          title: '@'
        },
        templateUrl: 'webApp/components/view-buy-number-pop/view_buy_number_pop.html',
        link: function preLink(scope) {

          (function init() {
            scope.isBuyNumShow = false;
            scope.hideBuyNum = hideBuyNum;
            scope.$on('view_buy_number_pop.show', showEvent);
          })();


          function hideBuyNum() {
            scope.isBuyNumShow = false;
          }

          function showEvent(event, params) {
            if(scope.compId==params.compId) {
              var activityId = params.activityId;
              var uid = params.uid;
              getBuyNumber(activityId, uid);
            }
          }

          function getBuyNumber(activityId, uid) {
            AppModel.getActivityNum(activityId, uid, function (response, data) {
              var code = response.data.code;
              if (0 == code) {
                var activityNums = data.data;
                //scope.activityNumCols = ArrayUtil.splitArray(activityNums, 3);
                scope.activityNumCols = activityNums;
                scope.isBuyNumShow = true;
              }
            });
          }


        }
      }
    }
  })
