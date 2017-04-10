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
    app.controller('joinRecordCtrl', joinRecordCtrl);

    joinRecordCtrl.$inject = ['$stateParams', '$scope', '$state', 'GoodsModel','userInfo'];
    function joinRecordCtrl($stateParams, $scope, $state, GoodsModel,userInfo) {

      (function init() {
        $scope.isEmpty = false;
        $scope.activityId = parseInt($stateParams.activityId);
        $scope.requestUrl = GoodsModel.getJoinRecordUrl();
        $scope.requestParams = {
          activity_id: $scope.activityId
        };
        $scope.callBack = {
          setData: function (data) {
            $scope.list = data;
          },
          setEmpty: function (isEmpty) {
            $scope.isEmpty = isEmpty;
          }
        }
        if($stateParams.activity==null) {
          setTimeout(function() {
            getActivity($scope.activityId);
          }, 300);
        }else {
          $scope.activity = $stateParams.activity;
        }
      })();

      $scope.gotoHisPage = function (joinRecordItem) {
        var uicon = joinRecordItem.uicon;
        var unick = joinRecordItem.unick;
        var uid = joinRecordItem.uid;

        if(userInfo.getUserInfo().uid==uid){
          $state.go('myIndianaRecord');
        }else{
          $state.go('hispage', {uicon: uicon, unick: unick,uid:uid});
        }
      }

      function getActivity(activityId) {
        GoodsModel.getGoodsDetail(activityId,
          function onSuccess(response, data) {
            if (data.code == 0) {
              $scope.activity = data.data;
            }
          });
      }



    }
  }
)
