/**
 * Created by songmars on 15/12/29.
 */

define(
	[
		'app',
		'components/view-progress/view_progress',
		'components/view-countdown/view_countdown',
		'components/view-buy-footer/view_buy_footer',
		//'components/view-buy-footer-baituan/view_buy_footer_baituan', 当前js丢失。
		'components/view-buy-number-pop/view_buy_number_pop',
		'models/model_goods',
		'models/model_pintuan', 
		'models/model_activity',
		'html/common/service_user_info',
		'html/common/geturl_service',
		'html/common/global_service',
		'html/thirdParty/thirdparty_wechat_js',
		'html/common/storage',
	],
	function(app) {
		"use strict";

		app.controller('express_queryCtrl', express_queryCtrl);
        express_queryCtrl.$inject = ['$scope', '$state', '$stateParams', 'GoodsModel','PintuanModel', 'ActivityModel', 'MyUrl', 'ToastUtils', 'userInfo', 'Global', 'weChatJs', 'Storage','$ionicHistory', '$timeout', '$ionicSlideBoxDelegate'];

		function express_queryCtrl($scope, $state, $stateParams, GoodsModel,PintuanModel, ActivityModel, MyUrl, ToastUtils, userInfo, Global, weChatJs, Storage,$ionicHistory, $timeout, $ionicSlideBoxDelegate) {
			(function init() {
                $scope.activity_id = $stateParams.activity_id;//商品id
                $scope.logistics_num = $stateParams.logistics_num;//订单号码
                $scope.logistics_id = $stateParams.logistics_id;//订单号码
                $scope.type=$stateParams.type;
                console.log($scope.type);
				$scope.isLoading = true;
				/*$scope.refresh = refresh;
				refresh();*/
			})();
            $scope.code={
                "showapi_res_code":0,
                "showapi_res_error":"",
                "showapi_res_body":{
                    "ret_code":"-1",
                    "flag":false,
                    "msg":"参数错误！请核实传递的参数信息是否为空，单号长度是否满足要求!" //这个是错误的单号返回的json
                }
            }
             if(Storage.get('logistics_goods_img')){
            	$scope.logistics_goods_img=Storage.get('logistics_goods_img');
            }





            $scope.gotoMainPage = function() {
					$state.go('tab.mainpage')
				}
				//    跳到支付页面



			$scope.gotoFullIntroduce = function() {
				$state.go('activity-fullIntroduce', {
					activity: $scope.activity,
					activityId: $scope.activityId,
					goodsId: $scope.activity.goods_id
				});
			};

			/*function refresh() {
               ToastUtils.showLoading('加载中...');
                GoodsModel.getGoodsDetail('', onSuccess, onFailed, onFinal);
			}*/


            //添加物流状态的数据
            $scope.express_status={
                text:""
            };

			function onSuccess(response, data) {

				if(data.code == 0) {  
					$scope.express = data.data;
                    console.log($scope.express);
                    console.log($scope.express.showapi_res_body.flag);
                    var status=$scope.express.showapi_res_body.status;
                    switch (status){
                        case -1 :
                            $scope.express_status.text="待查询";
                            break;
                        case 0 :
                            $scope.express_status.text="查询异常";
                            break;
                        case 1 :
                            $scope.express_status.text="暂无记录";
                            break;
                        case 2 :
                            $scope.express_status.text="运输中";
                            break;
                        case 3 :
                            $scope.express_status.text="派送中";
                            break;
                        case 4 :
                            $scope.express_status.text="已签收";
                            break;
                        case 5 :
                            $scope.express_status.text="用户拒签";
                            break;
                        case 6 :
                            $scope.express_status.text="疑难件";
                            break;
                        case 7 :
                            $scope.express_status.text="无效单";
                            break;
                        case 8 :
                            $scope.express_status.text="超时单";
                            break;
                        case 9 :
                            $scope.express_status.text="签收失败";
                            break;
                        case 10 :
                            $scope.express_status.text="退回";
                            break;

                    }
					$scope.isLoading = false;
					if(Global.isInweixinBrowser()) {
						weChatJs.wxShareToTimeline('对不起，这么晚才告诉你！全部1块钱，随便选！', baseUrl + '#/activity/' + $scope.activity.activity_id, $scope.activity.img, function() {
							// ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
						}, function() {});
						weChatJs.wxShareToAppMessage('对不起，这么晚才告诉你！全部1块钱，随便选！', $scope.activity.goods_subtitle, baseUrl + '#/activity/' + $scope.activity.activity_id, $scope.activity.img, function() {
							// ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
						}, function() {});
					}
				} else {
					ToastUtils.showError(data.msg);
				}
			}

			function onFailed(response) {
				if(response.status !== 200) {
					ToastUtils.showError('请检查网络');
				}
			}

			function onFinal() {
				$scope.$broadcast('scroll.refreshComplete');
				ToastUtils.hideLoading()
			}

			function getHomeNewPublish() {
				ActivityModel.getbaituanNewPublish(function(xhr, re) {
					var code = re.code;
					if(code == 0) {
						var data = re.data;
						if(data.length > 3) data.length = 3;
						$scope.newPublish = data;
					} else {
						ToastUtils.showMsgWithCode(code, re.msg);
					}
				}, function(response, data) {
					ToastUtils.showMsgWithCode(7, '获取最新揭晓失败：' + '状态码：' + response.status);
				})
			}
			$scope.$on('$ionicView.beforeEnter', function(ev, data) {
//				getHomeNewPublish()
				/*$timeout(function() {
					$ionicSlideBoxDelegate.next();
				}, 1000)*/
			});
            $scope.$on('$ionicView.enter', function() {
                if(!$ionicHistory.backView() && !Global.isInAPP()) {
                    $scope.firstInIsGoodsPage = true;
                } else {
                    $scope.firstInIsGoodsPage = false;
                }
                //获取快递路径---接口
                GoodsModel.getExpressQuery($scope.logistics_id,$scope.type, onSuccess, onFailed, onFinal);
                //refresh();
            });
		}
	});


//物流详情
/*$scope.express={
 "showapi_res_code":0,//showapi平台返回码,0为成功,其他为失败
 "showapi_res_error":"",//showapi平台返回的错误信息
 "showapi_res_body":{
 "mailNo":"883828504604679530",//快递单号
 "update":1483066014796,//数据最后查询的时间
 "updateStr":"2016-12-30 10:46:54",//数据最后更新的时间
 "ret_code":0,//接口调用是否成功,0为成功,其他为失败
 "flag":true,//物流信息是否获取成功
 "status":4,//-1 待查询 0 查询异常 1 暂无记录 2 运输中 3 派送中 4 已签收 5 用户拒签 6 疑难件 7 无效单 8 超时单 9 签收失败 10 退回
 "tel":"021-69777888/999",//快递公司电话
 "data":[//具体快递路径信息
 {
 "time":"2016-12-19 13:22:15",
 "context":"客户 签收人: 快递管家 已签收  感谢使用圆通速递，期待再次为您服务"
 },
 {
 "time":"2016-12-19 09:46:03","context":"广东省广州市天河区龙洞公司(点击查询电话)苏** 派件中 派件员电话13025467887"},{"time":"2016-12-19 07:41:21",
 "context":"广东省广州市天河区龙洞公司 已发出,下一站 广州市天河区龙洞岑村分部"
 },
 {"time":"2016-12-19 07:40:21","context":"广东省广州市天河区龙洞公司 已收入"
 },
 {
 "time":"2016-12-19 05:18:41",
 "context":"广州转运中心 已发出,下一站 广东省广州市天河区龙洞"
 },
 {
 "time":"2016-12-19 05:11:08",
 "context":"广州转运中心 已收入"
 },
 {
 "time":"2016-12-17 22:05:20",
 "context":"潍坊转运中心 已发出,下一站 广州转运中心"
 },{
 "time":"2016-12-17 22:02:54",
 "context":"潍坊转运中心 已收入"
 },
 {
 "time":"2016-12-17 22:01:55",
 "context":"山东省潍坊市公司 已发出,下一站 潍坊转运中心"
 },
 {
 "time":"2016-12-17 21:55:41",
 "context":"山东省潍坊市公司 已打包"
 },
 {
 "time":"2016-12-17 20:42:22",
 "context":"山东省潍坊市公司(点击查询电话) 已揽收"
 },
 {
 "time":"2016-12-17 17:00:15",
 "context":"山东省潍坊市公司 取件人: 陈麦收 已收件"
 }],
 "expSpellName":"yuantong",//快递字母简称
 "expTextName":"圆通速递"//快递公司名
 }
 };
 */
