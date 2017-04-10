/**
 * Created by songmars on 15/12/29.
 */

define(
	[
		'app',
		'components/view-progress/view_progress',
		'components/view-countdown/view_countdown',
		'components/view-buy-footer/view_buy_footer',
		'components/view-buy-number-pop/view_buy_number_pop',
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

		app.controller('pintuanCollectCtrl', pintuanCollectCtrl);
		pintuanCollectCtrl.$inject = ['$scope', '$state', '$stateParams', '$ionicPopup', 'GoodsModel', 'PintuanModel', 'MyUrl', 'ToastUtils', 'userInfo', 'Global', 'Storage', 'weChatJs', '$ionicHistory', '$interval'];

		function pintuanCollectCtrl($scope, $state, $stateParams, $ionicPopup, GoodsModel, PintuanModel, MyUrl, ToastUtils, userInfo, Global, Storage, weChatJs, $ionicHistory, $interval) {
			(function init() {
				$scope.collectList = [];

				$scope.isLogin = MyUrl.isLogin();
				$scope.isLoadFinished = true; //是否加载结束
				$scope.isLoading = true;
				$scope.refresh = refresh;

			})();

			function loginMember() {
				console.log(MyUrl.isLogin())
				if(MyUrl.isLogin()) {
					//		            event.preventDefault();
					$state.go('login', {
						'state': STATUS.LOGIN_ABNORMAL
					});
					ToastUtils.showWarning('请先登录！！');
					return;
				} else {
					//		          	userInfo.requestInfo();
				}
			}
			//返回
			$scope.goBack = function() {
				$state.go('tab.account2');
				//                  $ionicHistory.goBack();
			};
			$scope.gotoMainPage = function() {
				$state.go('tab.mainpage')
			}

			/**
			 * 获取当前用户信息
			 * @returns {*}
			 */
			$scope.getCurrUserInfo = function() {
				return userInfo.getUserInfo();
			};

			function refresh() {
				ToastUtils.showLoading('加载中...');
				//      getAddressList()
				if(MyUrl.isLogin()) {
					userInfo.requestInfo();
				} else {
					loginMember();
				}
				PintuanModel.getCollectList(onSuccess, onFailed, onFinal);
			}

			function onSuccess(response, data) {

				if(data.code == 0) {
					$scope.collectList = data.data;
					$scope.isReload = true;
					$scope.isLoading = false;
				} else {}
			}

			//			团的回调
			function onFailed(response, data) {
				if(response.status !== 200) {
					ToastUtils.showError('请检查网络');
				}
			}

			function onFinal() {
				ToastUtils.hideLoading();
			}

			$scope.formatSimple = function(time) {
					//格式化时间
					var fmt = "yyyy-MM-dd hh:mm:ss";
					var day = new Date(parseInt(time * 1000));
					var o = {
						"M+": day.getMonth() + 1, //月份 
						"d+": day.getDate(), //日 
						"h+": day.getHours(), //小时 
						"m+": day.getMinutes(), //分 
						"s+": day.getSeconds(), //秒 
						"q+": Math.floor((day.getMonth() + 3) / 3), //季度 
						"S": day.getMilliseconds() //毫秒 
					};
					if(/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (day.getFullYear() + "").substr(4 - RegExp.$1.length));
					for(var k in o)
						if(new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
					return fmt;
				}
				//    分享到给别人参团
				/**
				 * 追加云购并跳转到订单页面
				 */
			$scope.startToPay = function(teamwar_id) {
				setTimeout(function() {
					var commitData = {
						orderType: 1,
						activity_id: teamwar_id,
						goods_title: $scope.tuan_info.title,
						activity_type: 2,
						//		                need_num:$scope.broad.need_num,
						//		                join_number:$scope.broad.need_num/$scope.tuan_info.need_num,
						num: teamwar_id
							//		                remain_num:$scope.tuan_info.need_num-$scope.tuan_info.user_num
					}
					Storage.set('commitData', [commitData])
					$state.go('pay');
				}, 10)

			};
			/**
			 * 判断元素是否在数组的方法
			 */
			//      $scope.inArrayus('3001',['3001','3002'])
			$scope.inArrayus = function(str, arr) {
				//      	var str = str||'3001';
				//      	var arr = arr||['3001','3002'];
				for(var i = 0; i < arr.length; i++) {
					if(str == arr[i]) {
						return true;
					}
				}
				return false;
			};
			//			倒计时完成
			$scope.timeoutCallback = function() {
				$scope.isReload = false;
				aaa();
			};
			// 商品详情页面
			$scope.gotoDetail = function(goods_id) {
					$state.go('pintuan_detail', {
						goods_id: goods_id
					});
				}
				//开团页面
			$scope.gotopintuanApply = function(id) {
					if(!$scope.isLogin) {
						console.log('去登录');
						$state.go('login');
						return;
					}
					$state.go('pintuan_apply', {
						activityId: id
					})
				}
				//取消收藏
			$scope.removeCollect = function(id) {
				console.log(id)
				PintuanModel.removeCollect(id);
				PintuanModel.getCollectList(onSuccess, onFailed, onFinal);
			}

			function aaa() {
				var timer = setInterval(function() {
					if($scope.isReload) {
						clearInterval(timer)
					} else {
						refresh();
					}
				}, 1000);
			}
			$scope.$on('$ionicView.beforeEnter', function(ev, data) {
				//获取用户信息
				//				userInfo.requestInfo();
				refresh();
				//				PintuanModel.pintuan_getDetail_info($scope.pintuan_team, onSuccess, onFailed,onFinal)
			})
		}
	});