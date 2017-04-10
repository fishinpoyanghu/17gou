/**
 * Created by luliang on 2016/1/4.
 */
define([
	'app',
	'utils/toastUtil',
	'html/trolley/trolley_service',
	'html/common/local_database',
	'models/model_app',
	'html/common/global_service',
	'html/thirdParty/thirdparty_wechat',
	'html/thirdParty/third_party_wechat_whole',
	'html/common/storage'
], function(app) {
	app
		.controller('PayController', ['$scope', '$sce', '$ionicHistory', '$location', '$timeout', '$window', '$ionicPopup', 'trolleyInfo', 'AppModel', 'localDatabase', 'Global', 'WeChatShare', 'OneWeChat', 'ToastUtils', '$state', 'Storage',
			function($scope, $sce, $ionicHistory, $location, $timeout, $window, $ionicPopup, trolleyInfo, AppModel, localDatabase, Global, WeChatShare, OneWeChat, ToastUtils, $state, Storage) {
				$scope._loaded = false;
				$scope.goBack = goBack;
				if(window.localStorage.getItem('parent_invite_code')) {
					var parent_invite_code = window.localStorage.getItem('parent_invite_code');
				}

				if(Global.isInweixinBrowser()) {
					$scope.show_wx_pay = true;
					$scope.show_al_pay = false;
				} else if(Global.isInAPP()) {
					$scope.show_wx_pay = true;
					$scope.show_al_pay = true;
				} else {
					$scope.show_wx_pay = false;
					$scope.show_al_pay = true;
				}

				function isCacheEmpty(oderInfo) {
					return !oderInfo;
				}

				function saveCache() {
					var saveData = {};
					saveData.pay = $scope.pay;
					saveData.oderInfo = $scope.oderInfo;
					localDatabase.setOderInfo(saveData);

				}

				function clearCache() {
					localDatabase.removeOderInfo();
				}

				function start() {
					// var oderInfo = localDatabase.getOderInfo();
					// if (isCacheEmpty(oderInfo)) {
					initial();
					// } else {
					//     $scope.pay = oderInfo.pay;
					//     $scope.oderInfo = oderInfo.oderInfo;
					//     $scope.shopList = $scope.oderInfo.result_data;
					//     $scope._loaded = true;
					//     checkOrderState();
					//     clearCache();
					// }

					setUpDefaultPayWay();
				}

				function initial() {
					searchTrolley();
				}

				function setUpDefaultPayWay() {
					if($scope.oderInfo && $scope.oderInfo.need_money <= 0) {
						$scope.pay = {
							type: '-1',
							title: '本地支付'
						};
					} else {
						isWxInstalled();
					}
				}

				function isWxInstalled() {
					if(!Global.isInweixinBrowser()) {
						var promise_wx = WeChatShare.isWeChatInstalled();
						promise_wx.then(function() {
							$scope.pay = {
								type: '0',
								title: '微信支付'
							};
							$scope.hasWeChat = true;
						}, function(error) {
							//console.info(error);
							$scope.pay = {
								type: '1',
								title: '支付宝支付'
							};
							$scope.hasWeChat = false;
						});
					} else {
						$scope.pay = {
							type: '0',
							title: '微信支付'
						};
						$scope.hasWeChat = true;
					}
				}

				function goBack() {
					$ionicHistory.goBack(-1);
				}

				function goToResult() {
					var oderNum;
					try {
						oderNum = $scope.oderInfo.order_num;
					} catch(e) {}
					if(!oderNum) {
						ssjjLog.warn('订单号为空，去不了支付结果页面');
						return;
					}
					//清空购物车
					if(!$scope.buyNow) {
						$scope.$applyAsync(function() {
							trolleyInfo.clear();
						});
					}
					console.log($scope)
					$timeout(function(){
						if($scope.fromWhichState.slice(0, 7) == 'pintuan') {
							$state.go('pintuan_order');
						} else if($scope.fromWhichState == 'tab.account2'||$scope.fromWhichState=='chongzhi') {
							$state.go('tab.account2');
						} else {
							$state.go('myIndianaRecord');
						}
					},1500)

				}

				$scope.$on('$destroy', function() {
					clearCache();
					ToastUtils.hideLoading();
					$scope.chongzhiPopup && $scope.chongzhiPopup.close();
				});

				function searchTrolley() {

					var commitData = [];
					$scope.arrActivity_type = [];
					$scope.arr_hot_luckyBuy = [];
					if($scope.buyNow) {
						commitData = Storage.get('commitData');
						/*幸运购添加*/
						//	                            $scope.shopList[i].activity_type = goodsList[i].activity_type;
						$scope.arrActivity_type[0] = commitData[0].activity_type;
						if(commitData[0].activity_type == 6) {
							//	                            $scope.shopList[i].hot_luckyBuy = goodsList[i].hot_luckyBuy;
							//                          	good.hot_luckyBuy = commitData[0].hot_luckyBuy;
							$scope.arr_hot_luckyBuy[0] = commitData[0].hot_luckyBuy;
						} else {
							$scope.arr_hot_luckyBuy[0] = 0;
						}
						/*幸运购*/

						$scope.commitDataGoods = commitData;
						console.log('立即购买通道')
					} else {
						var goodsList = trolleyInfo.getGoodsInfo();

						var _good;
						var goodKey;

						for(var i = 0, len = goodsList.length; i < len; i++) {
							/*是从view_buy_pop中的Storage.set('commitData')中获取*/
							var good = {
								activity_id: goodsList[i].activity_id,
								goods_title: goodsList[i].goods_title,
								activity_type: goodsList[i].activity_type,
								need_num: goodsList[i].need_num,
								join_number: goodsList[i].join_number,
								parent_invite_code: parent_invite_code,
								num: goodsList[i].join_number
							};

							/*幸运购添加*/
							//	                            $scope.shopList[i].activity_type = goodsList[i].activity_type;
							$scope.arrActivity_type[i] = goodsList[i].activity_type;
							if(good.activity_type == 6) {
								//	                            $scope.shopList[i].hot_luckyBuy = goodsList[i].hot_luckyBuy;
								good.hot_luckyBuy = goodsList[i].hot_luckyBuy;
								$scope.arr_hot_luckyBuy[i] = goodsList[i].hot_luckyBuy;
							} else {
								$scope.arr_hot_luckyBuy[i] = 0;
							}
							/*幸运购*/

							console.log('购物车通道')
							commitData.push(good);
						}
					}

					if(commitData.length <= 0) {
						console.log('warning :' + 'trolley is empty');
						if(Global.isInAPP()) {
							goBack();
						} else {
							commitData = Storage.get('payCommitData');
							if(!commitData) {

								$state.go('tab.mainpage');
								return;
							}
						}

					}

					ToastUtils.showLoading('正在提交……');
					//                  console.log(commitData);
					//                  console.log(123);
					//                  console.log(trolleyInfo);
					AppModel.getOrderInfo(commitData, function(response, data) {
						ToastUtils.hideLoading();
						try {
							var code = data.code;
							if(0 == code) {
								$scope.oderInfo = data.data;
								$scope.shopList = $scope.oderInfo.result_data;
								/*幸运购添加*/
								for(var i = 0; i < $scope.shopList.length; i++) {
									$scope.shopList[i].activity_type = $scope.arrActivity_type[i];
									if($scope.shopList[i].activity_type == 6) {
										$scope.shopList[i].hot_luckyBuy = $scope.arr_hot_luckyBuy[i]
									}
								}
								/*end 幸运购*/
								if(!$scope.oderInfo || !$scope.shopList) {
									goBack();
									return;
								}
								$scope._loaded = true;
								
								//生产订单后清除数据
								Storage.set('commitData',null)
								
								setUpDefaultPayWay();
							} else if(code == 1) {
								$scope.oderInfo = data.data;
								// ToastUtils.showError('订单生成失败：' + data.msg);
								$scope.chongzhiPopup = $ionicPopup.confirm({
									title: '订单生成失败：' + data.msg,
									scope: $scope,
									buttons: [{
										text: Global.isInweixinBrowser() ? '回首页' : '返回',
										onTap: function(e) {
											if(Global.isInweixinBrowser()) {
												$state.go('tab.mainpage');
											} else {
												goBack();
											}

											return false;
										}
									}, {
										text: '去充值',
										type: 'button-assertive',
										onTap: function(e) {
											Storage.set('payCommitData', commitData);
											Storage.set('needCheckPaySuccess', 'needCheckPaySuccess')
											$state.go('chongzhi');
											return false;
										}
									}]
								});
							} else if(code == 2) {
								$scope.chongzhiPopup = $ionicPopup.confirm({
									title: '订单生成失败：' + data.msg,
									scope: $scope,
									buttons: [{
										text: Global.isInweixinBrowser() ? '回首页' : '返回',
										onTap: function(e) {
											if(Global.isInweixinBrowser()) {
												$state.go('tab.mainpage');
											} else {
												goBack();
											}

											return false;
										}
									}]
								});
							} else {
								$scope.chongzhiPopup = $ionicPopup.confirm({
									title: '订单生成失败：' + data.msg,
									scope: $scope,
									buttons: [{
										text: Global.isInweixinBrowser() ? '回首页' : '返回',
										onTap: function(e) {
											if(Global.isInweixinBrowser()) {
												$state.go('tab.mainpage');
											} else {
												goBack();
											}

											return false;
										}
									}]
								});
							}
						} catch(e) {
							console.error('failed 订单获取失败：' + e.message);
							goBack();
						}
					}, function(response, data) {
						//                  	console.log('这里');
						//                  	console.log(response)
						//                  	console.log(data)
						ToastUtils.hideLoading();
						ToastUtils.showError('网络异常：' + '状态码：' + response.status);
						goBack();
					}, function() {

					});
				}

				function switchPayUrl(type) {
					var url = '';
					switch(type) {
						case '0':
							$scope.pay.title = '微信支付';
							url = $scope.oderInfo.wx_pay;
							break;
						case '1':
							$scope.pay.title = '支付宝';
							url = $scope.oderInfo.al_pay;
							break;
					}
					$scope.payUrl = url;
				}

				function switchPayWay() {
					if($scope.oderInfo.need_money <= 0) {
						$scope.pay = {
							type: '-1',
							title: '本地支付'
						};
					} else {
						switchPayUrl('0');
					}
				}

				function getNoPay() {
					dealPay()
				}

				function dealPay() {
					ToastUtils.showLoading('正在支付，请稍候……');
					AppModel.getNoPay($scope.oderInfo.order_num, function(response, data) {
						var code = data.code;
						if(0 == code) {
							ToastUtils.showSuccess(data.msg);
							goToResult();
						} else {
							ToastUtils.showError(data.msg);
						}
					}, function(response, data) {
						ToastUtils.showError('网络异常：' + '状态码：' + response.status);

					}, function() {
						ToastUtils.hideLoading();
					})
				}

				function getThirdPay() {
					if(0 == $scope.pay.type) { //微信支付下
						if(Global.isInAPP()) {
							ToastUtils.showLoading('正在调用支付服务，请稍候……');
							AppModel.wx_app_pay($scope.oderInfo.order_num, function(xhr, re) {
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
										ToastUtils.showSuccess('支付成功');
										goToResult();
									}, function(err) {
										//支付失败
										ToastUtils.showMsgWithCode(7, '用户取消或支付失败');
									})
								} else {
									ToastUtils.showMsgWithCode(code, re.msg);
								}
							}, function(response, data) {
								ToastUtils.showMsgWithCode(7, '支付失败');
							}, function() {
								ToastUtils.hideLoading();
							})
						} else {
							OneWeChat.isWxEnable(function() {
								var params = {
									url: $scope.payUrl
								};
								OneWeChat.pay(params, function() {}, function() {

								})
							});
						}

					} else if(1 == $scope.pay.type) { //支付宝下
						if(Global.isInAPP()) {
							ToastUtils.showLoading('正在调用支付服务，请稍候……');
							AppModel.al_app_pay($scope.oderInfo.order_num, function(xhr, re) {
								var code = re.code;
								if(code == 0) {
									dmwechat.aliPay(re.data.param, function() {
										//支付成功
										ToastUtils.showSuccess('支付成功');
										goToResult();
									}, function(err) {
										//支付失败
										ToastUtils.showMsgWithCode(7, '用户取消或支付失败');
									})
								} else {
									ToastUtils.showMsgWithCode(code, re.msg);
								}
							}, function(response, data) {
								ToastUtils.showMsgWithCode(7, '支付失败');
							}, function() {
								ToastUtils.hideLoading();
							})
						} else {
							window.location.href = $scope.payUrl;
						}
					}
				}

				function checkOrderState() {
					ToastUtils.showLoading('查询订单中……');
					AppModel.getOrderStat($scope.oderInfo.order_num, function(response, data) {
						var code = data.code;
						var result = data.data;
						if(0 == code && result) {
							var status = result.status;
							if(1 == status) {
								ToastUtils.showSuccess('订单支付成功');
								goToResult();
							} else if(2 == status) {
								// oderStateCheckFailed();
								ToastUtils.showError('订单未支付');
							} else {
								ToastUtils.showError('订单支付失败');
							}
						} else {
							ToastUtils.showError('订单支付失败：' + data.msg);
							// oderStateCheckFailed();
						}
					}, function(response, data) {
						ToastUtils.showError('网络异常：' + '状态码：' + response.status);
						// oderStateCheckFailed();

					}, function() {
						ToastUtils.hideLoading();
					});
				}

				function oderStateCheckFailed() {
					if(_check_retry) {
						showConfirm();
					} else {
						$state.go('question', {
							viewName: 'question'
						})
					}
				}

				var _check_retry = true;

				function showConfirm() {
					var confirmPopup = $ionicPopup.confirm({
						title: '还没有收到订单支付结果？',
						scope: $scope,
						buttons: [{
							text: '<b>刷新查询</b>',
							type: 'button-assertive',
							onTap: function(e) {
								_check_retry = true;
								return _check_retry;
							}
						}, {
							text: '支付遇到问题',
							onTap: function(e) {
								_check_retry = false;
								return _check_retry;
							}
						}]
					});
					confirmPopup.then(function(res) {
						if(res) {
							checkOrderState();
						} else {
							// $location.path('/question/question').replace();
							$state.go('question', {
								viewName: 'question'
							})
						}
					});
				}

				$scope.goToPay = function() {
					switchPayWay();
					saveCache();
					if($scope.pay.type == '-1') {
						getNoPay();
					} else {
						getThirdPay();
					}
				};
				$scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
					$scope.fromWhichState = fromState.name;
					$scope.buyNow = true; //此处是兼容其他地方带不进参数过来支付页面
					console.log(fromState.name)
					if (fromState.name=='pintuan_order') {
						$state.go('tab.account2');
					}

					if(toState.name !== 'pay') return; ////activity-lastPublishes  往期揭晓  activity-fullIntroduce 图文详情  activity-shareOrder 晒单分享  //activity-joinRecords 参与记录
					if(fromState.name == 'activity-goodsDetail' || fromState.name == 'activity-lastPublishes' || fromState.name == 'activity-fullIntroduce' || fromState.name == 'activity-shareOrder' || fromState.name == 'activity-joinRecords' || fromState.name =='baituan_member/:team'|| fromState.name =='pintuan_apply' || fromState.name =='chongzhi'|| fromState.name == 'tab.account2'|| fromState.name == 'hispage'||fromState.name=='myIndianaRecord') {
						$scope.buyNow = true;
					} else {
						$scope.buyNow = false;
					}
                    if($scope.buyNow==true){
                        if(fromState.name == 'myIndianaRecord') {
                            console.log(window.location.href.split('#')[0] + '#/tab/account2');
                            if(!!(window.history && history.pushState)) {
                                history.pushState(null,null, window.location.href.split('#')[0] + '#/tab/account2');
                            }
                        }
                    }

					start()
				});

				// setTimeout(start, 300);
			}
		]);
});