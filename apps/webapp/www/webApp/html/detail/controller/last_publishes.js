/**
 * Created by suiman on 16/1/7.
 */

define(
  [
    'app',
    'components/view-list-view/view_list_view',
    'components/view-list-item/view_list_item',
    'components/view-buy-footer/view_buy_footer',
    'models/model_goods',
    'html/common/service_user_info',
  ],
  function (app) {

    app.controller('LastPublishesCtrl', lastPublishesCtrl);

    lastPublishesCtrl.$inject = ['$scope', '$stateParams', '$state', 'GoodsModel', 'userInfo'];
    function lastPublishesCtrl($scope, $stateParams, $state, GoodsModel, userInfo) {

      (function init() {
        $scope.isEmpty = false;
        $scope.goodsId = parseInt($stateParams.goodsId);
        $scope.activityId = parseInt($stateParams.activityId);
        $scope.requestUrl = GoodsModel.getHistoryUrl();
        $scope.requestParams = {
          goods_id: $scope.goodsId
        };
        $scope.callBack = {
          setData: function (data) {
            $scope.list = data;
          },
          setEmpty: function (isEmpty) {
            $scope.isEmpty = isEmpty;
          }
        };
        if($stateParams.activity==null) {
          setTimeout(function() {
            getActivity($scope.activityId);
          }, 300);
        }else {
          $scope.activity = $stateParams.activity;
        }
      })();

      $scope.gotoHisPage = function (publish) {
        var uicon = publish.lucky_uicon;
        var unick = publish.lucky_unick;
        var uid = publish.lucky_uid;

        if(userInfo.getUserInfo().uid==uid){
          $state.go('myIndianaRecord');
        }else{
          $state.go('hispage', {uicon: uicon, unick: unick, uid:uid});
        }
      };

      function getActivity(activityId) {
        GoodsModel.getGoodsDetail(activityId,
          function onSuccess(response, data) {
            if (data.code == 0) {
              $scope.activity = data.data;
            }
          });
      }
    }
  });
