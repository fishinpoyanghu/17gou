/**
 * Created by songmars on 16/1/5.
 */

define(
	['app',
		'models/model_app',
		'models/model_pintuan',
		'utils/toastUtil',
		'html/common/geturl_service',
		'html/common/global_service'
	],
	function(app) {
		app.controller('ShareOrderCtrl', ShowOrderCtrl);

		ShowOrderCtrl.$inject = ['$scope', '$state', '$stateParams', 'AppModel', 'PintuanModel', 'ToastUtils', 'MyUrl', '$ionicHistory', 'Global', 'Global'];

		function ShowOrderCtrl($scope, $state, $stateParams, AppModel, PintuanModel, ToastUtils, MyUrl, $ionicHistory, Global) {

			var uid = ($stateParams.uid == '所有') ? null : $stateParams.uid;
			var goodsId = ($stateParams.goodsId == '所有') ? null : $stateParams.goodsId;
			var indexFrom = 1; //从第几条数据开始返回
			var perRequestNum = 10; //每次请求多少条数据
			var isDoRefreshing = false; //是否正在做刷新操作
			$scope.isLoadFinished = false; //首次加载是否结束
			$scope.pageTitle = $stateParams.pageTitle; //页面标题\
			
			$scope.displayWhichItem = 'time';
			
			if($scope.pageTitle == '我的晒单') {
				$scope.my = 1;
			} else {
				$scope.my = 0;
			}
			$scope.pageTitle = $scope.pageTitle || '晒单分享';
			writeTitle($scope.pageTitle);
			$scope.orderlist = [];
			ToastUtils.showLoading('加载中....');
			$scope.page = 0;
			$scope.type = 'time';
			$scope.acitype_type = '';
			$scope.hasMoreData = true;

			/*点击最新、最热、二人云购*/
			$scope.changeActive = function(order_key, order_type, activity_type) {
				$scope.displayWhichItem = order_key;
				if (order_key == 'init') {
					return;
				}
				console.log($scope.displayWhichItem)
				if(!$scope.isLoadFinished) return;
				$scope.type = order_key;
				$scope.acitype_type = activity_type;
				/*if(activity_type==4){
				    $scope.acitype_type=activity_type;
				}*/
				$scope.show_aside();
				if(!$scope.isLoadFinished) return;
				$scope.order_key = order_key;
				$scope.page = 0;
				$scope.orderlist = [];
				getShareOrderData(true)
				/*if(order_type == 'none') {
				    $scope.order_type = '';
				} else if(order_type == 'asc') {
				    $scope.order_type = 'desc';
				} else {
				    $scope.order_type = 'asc';
				}*/

			}

			$scope.aside = false;
			$scope.up_icon = false;
			$scope.down_icon = true;

			//浏览过的商品栏
			$scope.show_aside = function() {
				$scope.up_icon = !$scope.up_icon;
				$scope.down_icon = !$scope.down_icon;
				$scope.aside = !$scope.aside;
			}

			//浏览过的商品弹框
			$scope.close_aside = function() {
				$scope.aside = false;
				$scope.up_icon = true;
				$scope.down_icon = false;
			}
			getShareOrderData(true);
			/*pintuan_data();*/

			/**
			 * 刷新
			 */
			$scope.doRefresh = function() {
				$scope.page = 0;
				$scope.hasMoreData = true;
				isDoRefreshing = true;
				getShareOrderData(isDoRefreshing);
				/*pintuan_data();*/
			};

			/**
			 * 加载更多
			 */
			$scope.doLoadMore = function() {
				if(!isDoRefreshing) {
					// indexFrom = $scope.orderlist.length + 1 ;
					getShareOrderData(false);
					/*pintuan_data();*/
				}

			};

			/**
			 * 数据是否为空
			 * @returns {boolean}
			 */
			$scope.isDataEmpty = function() {
				var isEmpty = true;
				if($scope.orderlist.length > 0) {
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

			/**
			 * 跳转到首页云购
			 */
			$scope.startToMainPage = function() {
				$state.go('tab.mainpage');
			};

			/**
			 * 跳转到TA的页面
			 * @param uicon
			 * @param unick
			 */
			$scope.goToHisPage = function(uicon, unick, uid) {
				$state.go('hispage', { uicon: uicon, unick: unick, uid: uid });
			};

			// $scope.doRefresh();
			$scope.goToMyAccount = function() {
				$state.go('tab.account')

			};

			$scope.back = function() {
				//        $ionicHistory.goBack();
				$state.go('tab.account2');
			}

			$scope.changeType = function(type) {

				if(!isDoRefreshing) {
					$scope.type = type;
					$scope.page = 0;
					$scope.hasMoreData = true;
					isDoRefreshing = true;
					ToastUtils.showLoading('加载中....');
					getShareOrderData(true);
					/* pintuan_data();*/

				}
			}

			/**
			 * 获取晒单数据
			 * @param isRefresh
			 */
			function getShareOrderData(isRefresh) {
				$scope.page++;
				AppModel.getShare_list($scope.type, $scope.page, $scope.my, '1', $scope.activity_type, function(response) {
					//onSuccess
					isConnect = true;
					var code = response.data.code;
					var msg = response.data.msg;
					switch(code) {
						case 0:
							var dataList = response.data.data;
							if(isRefresh) {
								$scope.orderlist = [];
								isDoRefreshing = false;
							}
							if(dataList.length >= 10) {
								$scope.hasMoreData = true;
								// dataList.pop();
							} else {
								$scope.hasMoreData = false;
							}
							$scope.orderlist = $scope.orderlist.concat(dataList);
							break;

						default:
							ToastUtils.showError(msg);
							break;
					}
				}, function(response) {
					//onFail
					isConnect = false;
					if(!$scope.isDataEmpty()) {
						ToastUtils.showError('请检查网络状态，状态码：' + response.status);
					}
				}, function() {
					//onFinal
					$scope.isLoadFinished = true;
					$scope.$broadcast('scroll.refreshComplete');
					$scope.$broadcast('scroll.infiniteScrollComplete');
					ToastUtils.hideLoading();
				});
			}

			/*获取拼团数据*/
			/*function pintuan_data(){
			    PintuanModel.pintuan_homepage(null, null, $scope.order_key, $scope.order_type, ($scope.page - 1) * $scope.pageCount + 1, $scope.pageCount, '0','1', function(xhr, re) {
			        var code = re.code;
			        if(code == 0) {
			            var data = re.data;

			        } else {
			            //ToastUtils.showMsgWithCode(code, re.msg);
			        }
			    }, function(response, data) {

			    }, function() {

			    });
			}*/

			$scope.zan = function(order) {
				try {
					if(!MyUrl.isLogin()) {
						event.preventDefault();
						$state.go('login', { 'state': STATUS.LOGIN_ABNORMAL });
						ToastUtils.showWarning('请先登录！！');
						return;
					} else {}
				} catch(e) {
					console.error('登录判断跳转出错' + e.name + '：' + e.message);
				}
				if(order.is_zan) {
					ToastUtils.showSuccess('您已经赞过~');
					return;
				}
				AppModel.zan(order.show_id, function(xhr, re) {
					var code = re.code;
					if(code == 0) {
						order.zans = Number(order.zans) + 1;
						order.is_zan = true;
						ToastUtils.showSuccess('点赞成功');
					} else {
						ToastUtils.showMsgWithCode(code, re.msg);
					}
				}, function(response, data) {
					ToastUtils.showMsgWithCode(7, '点赞失败：' + '状态码：' + response.status);
				}, null)

			}

			var gapHeight;
			if($state.current.name == 'tab.classify') {
				gapHeight = 49;
				$scope.hideCartIcon = true;
				$scope.showCartId = false;
			} else {
				gapHeight = 0;
				$scope.hideCartIcon = false;
				$scope.showCartId = true;
			}
			if(Global.isInweixinBrowser()) {
				$scope.leftBarHeight = innerHeight - gapHeight;
				$scope.leftBarTop = '44px';
				$scope.inWechatB = true;
			} else {
				$scope.leftBarHeight = innerHeight - 44 - gapHeight;
				if(ionic.Platform.isIOS() || ionic.Platform.isIPad()) {
					$scope.leftBarTop = '88px';
					$scope.inIosApp = true;
				} else {
					$scope.leftBarTop = '88px';
					$scope.inIosApp = false;
				}
				$scope.inWechatB = false;

			}

		}

	});