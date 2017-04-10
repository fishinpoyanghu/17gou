/**
 * Created by suiman on 15/12/30.
 */

define(
  [
    'app',
    'html/thirdParty/third_party_wechat_whole',
    'utils/toastUtil',
    'components/view-countdown/view_countdown',
    'lib/ng-lazyload',
    'models/model_pintuan',
    'html/common/storage',
  ],
  function (app,OneWeChat) {

    app.directive('viewIndianaRecordItem', viewRecordItem);
    viewRecordItem.$inject = ['OneWeChat','$ionicPopup','AppModel','ToastUtils','PintuanModel','Storage','MyUrl'];
    function viewRecordItem(OneWeChat,$ionicPopup,AppModel,ToastUtils,PintuanModel,Storage,MyUrl) {
      return {
        restrict: 'E',
        templateUrl: function(elem, attrs) {
          /*var path = 'webApp/components/pintuan_view-indiana-record-item/';
          var name = 'pintuan_view_indiana_record_item_' + attrs.type + '.html';
          return (path+name);*/
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
        },
        scope: {
          activity: '=',
          callback: '='
        },
        controller: function ($scope, $state) {
            $scope.quexiao=false;
            /*取消订单*/
            $scope.cancel_dingdan=function(ordermsg){
                $scope.quexiao=true;
                $ionicPopup.confirm({
                    title: '是否取消订单',
                    cancelText : '取消',
                    cancelType : 'button-default',
                    okText : '确定',
                    okType : 'button-positive'
                }).then(function(res) {
                    if(res) {//logout
                       PintuanModel.updateorderstatus(ordermsg.order_num,-2,'',onSuccess,onFailed, onFinal) ;
                    } else {
                        //cancel logout
                    }
                });
            }
            $scope.close_this=function(){
                $scope.quexiao=false;
            }
            $scope.orderstatus=function(order_num,status,msg){
                 $scope.quexiao=false;
                 PintuanModel.updateorderstatus(order_num,status,msg,onSuccess,onFailed, onFinal) ;
            }
            function onSuccess(response, data) { 
              if(data.code == 0) {
                 ToastUtils.showSuccess(data.msg); 
                  $state.go("pintuan_order",{}, {reload: true}); 
                 //更新状态成功刷新当前页面 
              }else{
                 ToastUtils.showError(data.msg); 
                  $state.go("pintuan_order",{}, {reload: true}); 
                 //更新状态失败刷新当前页面
              } 
            } 
           function onFailed(response, data) {
              if(response.status !== 200) {
                ToastUtils.showError('请检查网络');
              }
           }
          function onFinal(){
            ToastUtils.hideLoading();
          }

          $scope.gotoDetail = function () {
            $state.go('activity-goodsDetail', {activityId:$scope.activity.activity_id});
          }
           
          
          //跳转到商品id和订单号码对应的快递查询页面
          $scope.go_express_record = function(activity ) {
              $state.go('express_query', {
                  'activity_id': 1,
                  'logistics_num': activity.logistics_num,
                  'logistics_id': activity.logistics_id, 
              })
              Storage.set('logistics_goods_img',activity.main_img);
              Storage.set('logistics_goods_title',activity.title);
               
          };

            //支付订单
          $scope.pay_order = function (ordernum,goodstitle,moneyinfo,wxpay) { 
              //每次付款的时候请求判断订单的信息
               
               PintuanModel.getpayorder(ordernum,function(response, data){
                      if(data.code!=0){
                         ToastUtils.showError(data.msg);
                         return false;
                      }
                      if(data.data.paytype==1){
                          ToastUtils.showLoading('正在支付，请稍候……');
                              AppModel.getNoPay(ordernum, function(response, data) {
                                  var code = data.code;
                                  if (0 == code) {
                                          ToastUtils.showSuccess(data.msg); 
                                          $state.go("pintuan_order",{}, {reload: true}); 
                                          //这里延迟执行刷新订单页面

                                  } else {
                                      ToastUtils.showError(data.msg);
                                  }
                              }, function(response, data) {
                                  ToastUtils.showError('网络异常：' + '状态码：' + response.status);

                              }, function() {
                                  ToastUtils.hideLoading();
                              })

                        }else{    
                             OneWeChat.isWxEnable(function() { 
                             var params = {
                                        url: wxpay
                                     };
                                   OneWeChat.pay(params, function() {}, function() {

                                  })
                          },function(){   
                           var alertPopup = $ionicPopup.alert({
                               title: '提示',
                               template: '很抱歉，当前服务只支持微信端付款',
                               buttons:[{
                                    text:'确定'
                                  }]
                                }); 
                            });


                       } 
                
             },onFailed, onFinal);
             
             
          



             //付款接口
            // $state.go('activity-goodsDetail', {activityId:$scope.activity.activity_id});
          }
             $scope.goMember = function(id){
              	$state.go('pintuan_member',{
              		team:id
              	});
              	console.log()
             };
          //跳转到2人团商品详情页 
          $scope.gotoPintuan_Detail = function(goods_id) {
            $state.go('pintuan_detail', {
              goods_id: goods_id
            });
          };

                    
            /**
             * 跳转到选择地址页面
             * @param activityId
             */
            $scope.startToAddressSelect = function(activityId, type,order_num) {
               // console.log(activityId, type,order_num,$scope.activity.logistics_id);return false;
                if(type == 'pintuan2') {
                    $state.go('addressSelect', { activity_id: order_num, type: type,  });
                }

            };
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

