/**
 * Created by luliang on 2016/1/6.
 */
define(
  [
    'app',
    'angularSanitize',
    'filters/html_parse_filter',
    'models/model_goods',
    'utils/toastUtil',
    'components/view-buy-footer/view_buy_footer'
  ],
  function (app) {
    app.controller('GoodsDetailFullIntroduceController', ['$scope', '$sanitize', '$stateParams', 'GoodsModel', 'ToastUtils',
      function ($scope, $sanitize, $stateParams, GoodsModel, ToastUtils) {
        $scope.getGoodsDetail = getGoodsDetail;
        $scope.content = '';
        $scope.activityId = parseInt($stateParams.activityId);

        (function initial() {
          getGoodsDetail();
          if($stateParams.activity==null) {
            setTimeout(function() {
              getActivity($scope.activityId);
            }, 300);
          }else {
            $scope.activity = $stateParams.activity;
          }
        })();

        function getGoodsDetail() {
          GoodsModel.getGoodsImgDetail($stateParams.goodsId, function onSuccess(response, data) {
            var code = data.code;
            if (0 == code) {
              $scope.content = data.data.html;
              $scope.hasLoad = true;
            } else {
              ToastUtils.showError('加载失败：' + data.msg);
            }
          }, function onFailed(response, data) {
            ToastUtils.showError('网络异常：' + '状态码：' + response.statusText);
            $scope.disconnected = true;
          });
        }

        $scope.isShowDisconnect = function () {
          return ($scope.content.length <= 0) && $scope.disconnected;
        };

        $scope.isShowEmpty = function () {
          return ($scope.content.length <= 0) && $scope.hasLoad;
        };


        function getActivity(activityId) {
          GoodsModel.getGoodsDetail(activityId,
            function onSuccess(response, data) {
              if (data.code == 0) {
                $scope.activity = data.data;
              }
            });
        }
      }]);
  });
