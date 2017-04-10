
/**
 * Created by songmars on 16/1/5.
 */

define(
  [ 'app',
    'html/share_order/js/service_share_order',
    'components/view-image-list/view-image-list',
    'models/model_goods'],

  function () {
    var app = require('app');
    app.directive(
      'viewOrderListItem',

        [function () {
          return {
            restrict: 'E',
            templateUrl:'webApp/components/view-order-list/view_order_list.html',
            controller:['$scope','$attrs','$state','orderListInfo','GoodsModel',function($scope,$attrs,$state,orderListInfo,GoodsModel){

              //$scope.orderlist=orderListInfo.getOrderListInfo();
              //console.log('长度'+$scope.orderlist);

              GoodsModel.getShowOrderList($scope.goods_id,null,null,null,onSuccess,onFail);




              function onFail(response){
                ssjjLog.log("requestFailed："+window.JSON.stringify(response));
                var errmsg = '';
                try {
                  if(angular.isUndefined(response.data)){
                    errmsg = '网络异常';
                  }else{
                  }
                } catch (e) {
                  ssjjLog.error("加载失败：" + errmsg);
                }
              }
              function onSuccess(response){
                ssjjLog.log("onSuccess："+window.JSON.stringify(response));
                var data=response.data.data;
                var msg=response.data.msg;
                var code=response.data.code;
                $scope.orderlist=data;
              }








            }]
          }
        }]);
  });



