/**
 * Created by songmars on 15/12/29.
 */

define(
	[
		'app',
		'components/view-progress/view_progress',
		'components/view-countdown/view_countdown',
		'components/view-buy-footer/view_buy_footer',
		'components/view-buy-footer/view_buy_footer',
		'models/model_goods',
		'models/model_pintuan',
		'models/model_address',
		'html/common/service_user_info',
		'html/common/geturl_service',
		'html/common/global_service',
		'html/common/storage',
		'html/thirdParty/thirdparty_wechat_js'
	],
	function(app) {
		"use strict";

		app.controller('pintuanApplyCtrl', pintuanApplyCtrl);
		pintuanApplyCtrl.$inject = ['$scope', '$state', '$stateParams', '$ionicPopup', 'GoodsModel', 'PintuanModel', 'addressModel', 'MyUrl', 'ToastUtils', 'userInfo', 'Global', 'Storage', 'weChatJs', '$ionicHistory'];

		function pintuanApplyCtrl($scope, $state, $stateParams, $ionicPopup, GoodsModel, PintuanModel, addressModel, MyUrl, ToastUtils, userInfo, Global, Storage, weChatJs, $ionicHistory) {
			//			(function init() {
			$scope.isLogin = MyUrl.isLogin();

			$scope.activityId = parseInt($stateParams.activityId);
			$scope.orderType = parseInt($stateParams.orderType);
			$scope.teamwarId = parseInt($stateParams.teamwarId);
			$scope.addressList = []; //收货地址列表
			$scope.displayAddressList = []; //显示在页面的地址
			$scope.isLoadFinished = false; //是否加载结束
			$scope.goods_num = 1;
			$scope.refresh = refresh;

//			//测试数据
//			$scope.natures = [];
//			$scope.natures = [{
//				name: '颜色',
//				values: ['红色', '蓝色', '橙色',
//					'天蓝色', '赤橙黄绿'
//				]
//			}, {
//				name: '尺寸',
//				values: ['165cm', '185cm', '170cm',
//					'175cm', '195cm','205cm'
//				]
//			}];

			refresh();
			//			})();
			$scope.$on('$ionicView.enter', function() {
				getAddressList()
				if(!$ionicHistory.backView() && !Global.isInAPP()) {
					$scope.firstInIsGoodsPage = true;
				} else {
					$scope.firstInIsGoodsPage = false;
				}
			});

			$scope.gotoMainPage = function() {
				$state.go('tab.mainpage')
			}

			function refresh() {
				ToastUtils.showLoading('加载中...');
				//      getAddressList()
				PintuanModel.pintuan_getGoodsDetail_info($scope.activityId, function onSuccess(response, data) {
					if(data.code == 0) {
						$scope.broad = data.data;
						console.log($scope.broad)
					} else {
						ToastUtils.showError(data.msg);
					}
				}, function onFailed(response) {
					if(response.status !== 200) {
						ToastUtils.showError('请检查网络');
					}
				}, function onFinal() {
					$scope.$broadcast('scroll.refreshComplete');
					ToastUtils.hideLoading()
				});
				//商品获取之后的回调

			}

			$scope.changeNum = function(add) {
					if(!add && $scope.goods_num == 1) {
						return;
					}
					add ? $scope.goods_num++ : $scope.goods_num--;
					//				$scope.goods_num==0&&$scope.goods_num++;
				}
				//下面是地址
				//获取地址信息
				//    getAddressList()
			function getAddressList() {
				addressModel.getAddressList(function(response) {
					//onSuccess
					isConnect = true;
					var code = response.data.code;
					var msg = response.data.msg;
					$scope.isLoadFinished = true;
					switch(code) {
						case 0:
							$scope.addressList = response.data.data;
							$scope.isDisplayAddress();
							break;
						case 6:
							ToastUtils.showWarning(msg);
							$state.go('login');
							break;
						default:
							ToastUtils.showError(msg);
							break;
					}
				}, function(response) {
					//onFail
					isConnect = false;
					ToastUtils.showError('请检查网络状态');
				}, function() {
					//onFinal
					$scope.isLoadFinished = true;
					ToastUtils.hideLoading()
				});
			}
			//			去支付页面,暂时不用，原来外包做的
			$scope.goToPay = function() {
				switchPayWay();
				saveCache();
				if($scope.pay.type == '-1') {
					getNoPay();
				} else {
					getThirdPay();
				}
			};
			//			去支付页面
			$scope.gotopintuanPayInfo = function() {
					if(!$scope.addressList[0]) {
						ToastUtils.showError('请添加收货地址');
						return;
					}
					if($stateParams.orderType == 2) { //orderType == 2    参团
						$scope.startToPay();
					} else if($stateParams.orderType == 3) { //开团：根据activity_type的值判断种类
						switch($scope.broad.activity_type) {
							case 1: //普通拼团的开团
								$scope.startToPay();
								break;
							case 2: //幸运拼团的开团

							case 3: //团长免费的开团
								$scope.createTuan();
								break;
							default:
								break;
						}
					} else if($stateParams.orderType == 4) { //orderType=4    单买
						$scope.startToPay();
					}

				}
				/**
				 * 追加云购并跳转到订单页面
				 */
			$scope.startToPay = function(teamwar_id) {
				setTimeout(function() {
					var commitData = {
						orderType: $stateParams.orderType,
						activity_id: 0,
						goods_id: $scope.activityId,
						address_id: $scope.displayAddressList[0].address_id,
						goods_title: $scope.broad.goods_title,
						activity_type: 2,
						goods_num: $scope.goods_num,
						num: $scope.broad.price,
						//		                need_num:$scope.broad.need_num,
						//		                join_number:$scope.broad.need_num/$scope.tuan_info.need_num,
						//							num: teamwar_id
						//		                remain_num:$scope.tuan_info.need_num-$scope.tuan_info.user_num
					}
					$stateParams.orderType == 2 && (commitData.activity_id = $scope.teamwarId)
					Storage.set('commitData', [commitData]);
					$state.go('pay');
				}, 10)
			};
			//开团
			$scope.createTuan = function() {
					PintuanModel.pintuan_createTuan($scope.activityId, $scope.displayAddressList[0].address_id, function onSuccess(response, data) {
						if(data.code == 0) {
							$scope.pintuan_team = data.data.team;
							$state.go('pintuan_member', {
								team: data.data.team
							});
							//							pintuan_alert()
						} else {
							ToastUtils.showError(data.msg);
							$state.go('pintuan_main_page');
						}
					}, function onFailed(response) {
						console.log(response)
					})
				}
				//申请成功后进入成员管理界面
			$scope.gotopintuanMember = function() {
					$state.go('pintuan_member', {
						team: $scope.pintuan_team
					});
				}
				//			end 支付页面
				/**
				 * 跳转到新增收货地址页面
				 */
			$scope.startToAddressAdd = function() {
				$state.go('addressAdd');
			};
			/**
			 * 确认在页面显示地址
			 * @returns {boolean}
			 */
			$scope.isDisplayAddress = function() {
				$scope.displayAdress = $scope.addressList.some(diplayAddress);

				function diplayAddress(address) {
					return address.is_default;
				}
				if($scope.displayAdress) {
					$scope.displayAddressList = $scope.addressList;
				} else {
					$scope.displayAddressList[0] = $scope.addressList[0];
				}
			};
			/**
			 * 数据是否为空
			 * @returns {boolean}
			 */
			$scope.isDataEmpty = function() {
				var isEmpty = true;
				if($scope.addressList.length > 0) {
					isEmpty = false;
				}
				return isEmpty;
			};
			var isConnect = true; //网络是否连接
			/**
			 * 显示断网页面
			 * @returns {boolean}
			 */
			$scope.isShowDisconnect = function() {
				return $scope.isDataEmpty() && $scope.isLoadFinished && !isConnect;
			};

			//		跳转到修改地址页面
			$scope.startToAddressUpdate = function(addr) {
				$state.go('addressUpdate', {
					address: addr
				});
			};

		}
	});