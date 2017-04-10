//付款方式
var PAY_TYPE_WEIXIN = 1;
var PAY_TYPE_ZHIFUBAO = 2;

//查询本地充值订单号的键值
var KEY_RECHARGE_ORDER = 'recharge_order_key6666';

define(
	[
		'app',
		'components/view-card-radio/view_card_radio',
		'models/model_app',
		'html/common/storage',
		'html/common/global_service',
		'html/thirdParty/third_party_wechat_whole'
	],
	function(app) {

		app.controller('rechargeCtrl', rechargeCtrl);

		rechargeCtrl.$inject = ['$scope','$timeout', 'AppModel', '$window', 'ToastUtils', 'Storage', '$state', '$stateParams', 'OneWeChat', 'Global', '$ionicPopup', '$ionicHistory'];

		function rechargeCtrl($scope,$timeout, AppModel, $window, ToastUtils, Storage, $state, $stateParams, OneWeChat, Global, $ionicPopup, $ionicHistory) {

			//纠正错误参数：不等于2就是1
			$scope.$on('$ionicView.beforeEnter', function() {
				console.log('this')
				if($stateParams.rechargeType != 2) {
					$scope.rechargeType = 1;
				}
			})
			$scope.rechargeType = $stateParams.rechargeType
			$scope.goBack = function() {
				console.log('zhixingle')
				$state.go('tab.account2')
					//              if (!ionic.Platform.isWebView()) {
					//                  history.back();
					//              } else {
					//                  $ionicHistory.goBack();
					//              }
			};

			/*根据应用环境，选择默认的付款方式*/
			function getDefaultPayWay() {
				if(Global.isInweixinBrowser()) {
					$scope.hideWeixin = false;
					$scope.hidezhifubao = true;
					$scope.pay.type = PAY_TYPE_WEIXIN;
				} else if(Global.isInAPP()) {
					$scope.hideWeixin = true;
					$scope.hidezhifubao = false;
					$scope.pay.type = PAY_TYPE_WEIXIN;
				} else {
					$scope.hideWeixin = true;
					$scope.hidezhifubao = false;
					$scope.pay.type = PAY_TYPE_ZHIFUBAO;
				}
			}

			function hideWeixin(flag) {
				if(flag) {
					$scope.hideWeixin = true;
					$scope.pay.type = PAY_TYPE_ZHIFUBAO;
				} else {
					$scope.hideWeixin = false;
					$scope.pay.type = PAY_TYPE_WEIXIN;
				}
			}

			/*获得用户选择的金额*/
			function selectMoney(money) {
				$scope.pay.money = money;
			}

			/*检查参数*/
			function validParams() {
				var isMoneyValid = angular.isNumber($scope.pay.money) && $scope.pay.money >= 1;
				var isPayWayValid = $scope.pay.type == PAY_TYPE_WEIXIN || $scope.pay.type == PAY_TYPE_ZHIFUBAO;
				return isMoneyValid && isPayWayValid;
			}

			/*生成订单*/
			function createOrder() {
				ToastUtils.showLoading('提交订单中...');

				AppModel.getRecharge($scope.pay.money, $scope.pay.type,
					function onSuccess(response, data) {
						if(data.code == 0) {
							createOrderSuccess(data.data);
						} else {
							createOrderFailded(data.msg);
						}
					}, onNetError, onFinal);
			}

			/*生成订单成功*/
			function createOrderSuccess(data) {

				$scope.orderNum = data.order_num;
				$scope.payUrl = data.pay_url;
				$scope.payUrl2 = data.pay_url2;
				$scope.sign = data.sign;
				ToastUtils.hideLoading();
				payOrder($scope.orderNum, $scope.payUrl);

			}

			/*生成订单失败*/
			function createOrderFailded(msg) {
				ToastUtils.hideLoading();
				ToastUtils.showError('生成订单失败: ' + msg);
			}

			/**
			 * 支付订单
			 * @param orderNum 订单号
			 * @param payUrl   支付地址
			 */
			function payOrder(orderNum, payUrl) {
				saveOrder(orderNum);
				getThirdPay(orderNum, payUrl)
			}

			//发起支付
			function getThirdPay(orderNum, payUrl) {
				if(PAY_TYPE_WEIXIN == $scope.pay.type) { //微信支付下
					if(Global.isInAPP()) {
						ToastUtils.showLoading('正在调用支付服务，请稍候……');
						AppModel.wx_app_pay(orderNum, function(xhr, re) {
							var code = re.code;
							if(code == 0) {
								var data = re.data.param
								var params = {
									"appId": data.appid,
									"partnerId": data.partnerid,
									"prepayId": data.prepayid,
									"packageValue": data.package,
									"nonceStr": data.noncestr,
									"timeStamp": data.timestamp,
									"sign": data.sign
								};
								dmwechat.wechatPay(params, function() {
									//支付成功
									paySuccess();
								}, function(err) {
									//支付失败
									ToastUtils.showMsgWithCode(7, '用户取消或支付失败');
									$scope.showWap = false;
								})
							} else {
								ToastUtils.showMsgWithCode(code, re.msg);
							}
						}, function(response, data) {
							ToastUtils.showMsgWithCode(7, '支付失败：' + '状态码：' + response.status);
						}, function() {
							ToastUtils.hideLoading();
						})
					} else {
						OneWeChat.isWxEnable(function() {
							var params = {
								url: payUrl
							};
							OneWeChat.pay(params, function() {}, function() {

							})
						});
					}

				} else if(PAY_TYPE_ZHIFUBAO == $scope.pay.type) { //支付宝下
					if(Global.isInAPP()) {
						ToastUtils.showLoading('正在调用支付服务，请稍候……');
						AppModel.al_app_pay(orderNum, function(xhr, re) {
							var code = re.code;
							if(code == 0) {
								dmwechat.aliPay(re.data.param, function() {
									//支付成功
									paySuccess();
								}, function(err) {
									//支付失败
									ToastUtils.showMsgWithCode(7, '用户取消或支付失败');
									$scope.showWap = false;
								})
							} else {
								ToastUtils.showMsgWithCode(code, re.msg);
							}
						}, function(response, data) {
							ToastUtils.showMsgWithCode(7, '支付失败：' + '状态码：' + response.status);
						}, function() {
							ToastUtils.hideLoading();
						})
					} else {
						window.location.href = payUrl;
					}
				}
			}

			/**
			 * 查询订单状态
			 * @param orderNum 订单号
			 */
			function checkOrderState(orderNum) {
				AppModel.getOrderStat(orderNum,
					function onSuccess(response, data) {
						if(data.code == 0) {
							if(data.data.status == 1) {
								deleteOrder();
								showComfirm()
							}

						} else {
							payFailed(data.msg);
						}
					}, onNetError)
			}

			/*支付成功*/
			function paySuccess(data) {
				$timeout(function(){
					deleteOrder();
					ToastUtils.hideLoading();
					ToastUtils.showSuccess('充值成功');
					showComfirm();
				},1000);
			}

			/*支付失败*/
			function payFailed(msg) {
				ToastUtils.hideLoading();
				ToastUtils.showWarning('充值失败');
				deleteOrder();
			}

			/*保存订单*/
			function saveOrder(orderNum) {
				Storage.set(KEY_RECHARGE_ORDER, orderNum);
			}

			/*删除旧订单*/
			function deleteOrder() {
				Storage.remove(KEY_RECHARGE_ORDER);
			}

			/**
			 * 查询是否有旧订单
			 * @returns {string} 订单号 存在则返回订单号 不存在返回undefined
			 */
			function getOrder() {
				return Storage.get(KEY_RECHARGE_ORDER);
			}

			/*网络错误*/
			function onNetError() {
				ToastUtils.hideLoading();
				ToastUtils.showError('网络错误');
			}

			function onFinal() {
				// ToastUtils.hideLoading();
			}
			$scope.$on('$ionicView.beforeEnter', function(event, params) {
				$scope.pay = {
					type: PAY_TYPE_ZHIFUBAO,
					money: null
				};
				getDefaultPayWay();
				$scope.moneySelectCallback = {
					selectMoney: selectMoney
				};
				$scope.validParams = validParams;
				$scope.payNow = createOrder;

				//如果存在旧订单，检查支付结果
				var orderNum = getOrder();
				if(orderNum && Storage.get('needCheckPaySuccess') == 'needCheckPaySuccess' && !ionic.Platform.isWebView()) {
					checkOrderState(orderNum)
				}
			});

			function showComfirm() {
				var confirmPopup = $ionicPopup.confirm({
					title: '是否继续购买未支付商品？',
					scope: $scope,
					buttons: [{
						text: '取消',
						onTap: function(e) {

							return false;
						}
					}, {
						text: '确定',
						type: 'button-assertive',
						onTap: function(e) {
							return true;
						}
					}]
				});
				confirmPopup.then(function(res) {
					if(res) {
						$scope.goBack()
					} else {

					}
				});
			}
			/**
			 * 追加云购并跳转到支付页面
			 */
			$scope.startToPay = function(num) {
				$timeout(function() {
//					$scope.pay.money
//					num = scope.addPay.join_number = validJoinNum(scope.addPay.join_number);
					var commitData = {
//						activity_id: scope.addPay.activity_id,
//						goods_title: scope.addPay.goods_title,
//						activity_type: scope.addPay.activity_type,
//						need_num: scope.addPay.need_num,
//						join_number: scope.addPay.join_number,
//						num: scope.addPay.join_number,
//						remain_num: scope.addPay.remain_num
						activity_id: -1,
						goods_title: 'fudai',
						activity_type: '0',
						need_num: $scope.pay.money,
						join_number: $scope.pay.money,
						num: $scope.pay.money,
						orderType:6,
						remain_num: $scope.pay.money
					}
					Storage.set('commitData', [commitData])
					$state.go('pay');
				}, 10)

			};
		}
	})