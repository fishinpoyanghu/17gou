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
		'models/model_activity',
		'html/common/service_user_info',
		'html/common/geturl_service',
		'html/common/global_service',
		'html/thirdParty/thirdparty_wechat_js'
	],
	function(app) {
		"use strict";

		app.controller('pintuanCtrl', pintuanCtrl);
		pintuanCtrl.$inject = ['$scope', '$state', '$stateParams', 'GoodsModel', 'PintuanModel', 'ActivityModel', 'MyUrl', 'ToastUtils', 'userInfo', 'Global', 'weChatJs', '$ionicHistory', '$timeout', '$ionicSlideBoxDelegate'];

		function pintuanCtrl($scope, $state, $stateParams, GoodsModel, PintuanModel, ActivityModel, MyUrl, ToastUtils, userInfo, Global, weChatJs, $ionicHistory, $timeout, $ionicSlideBoxDelegate) {
			(function init() {

				$scope.activityId = parseInt($stateParams.activityId);
				if(!$stateParams.activityId) {
					$scope.activityId = 13;
				}
				$scope.isLoading = true;
				$scope.isDiplay = {
					opentuan_ed: true,
					will_opentuan: true
				}

			})();
			$scope.$on('$ionicView.enter', function() {
				if(!$ionicHistory.backView() && !Global.isInAPP()) {
					$scope.firstInIsGoodsPage = true;
				} else {
					$scope.firstInIsGoodsPage = false;
				}
			});

			$scope.gotoMainPage = function() {
					$state.go('tab.mainpage')
				}
				//    跳到支付页面

			//			调到对应的团的详情成员页面
			$scope.getHisMumber = function(teamwar_id) {
				$state.go('pintuan_member', {
					team: teamwar_id
				});
			};
			$scope.gotoJoinHisTuan = function(good_id) {
				$state.go('pintuan_detail', {
					goods_id: good_id
				});
			}
			$scope.gotoFullIntroduce = function() {
				$state.go('activity-fullIntroduce', {
					activity: $scope.activity,
					activityId: $scope.activityId,
					goodsId: $scope.activity.goods_id
				});
			};
			//跳转到成员页面
			$scope.gotoDetail = function(id) {
				$state.go('pintuan_member', {
					team: id
				});
			};
			//跳转到计算详情页面
			$scope.gotoCountDetail = function(id) {
				$state.go('pintuan_countDetail', {
					activityId: id
				});
			};

			function refresh() {
				//	ToastUtils.showLoading('加载中...');
				PintuanModel.pintuan_homepage('', onSuccess, onFailed, onFinal);
				//					PintuanModel.getGoodsDetail($scope.activityId, onSuccess, onFailed, onFinal);

			}

			function onSuccess(response, data) {

				if(data.code == 0) {
					$scope.activity = data.data;
					angular.forEach($scope.activity, function(n, i) {
						switch(n.flag) {
							case '1':
								$scope.isDiplay.opentuan_ed = false;
								break;
							case '0':
								$scope.isDiplay.will_opentuan = false;
								break;
						}
					})
					$scope.isLoading = false;
				} else {
					//					ToastUtils.showError(data.msg);
					//					ToastUtils.showError(111);
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
				PintuanModel.getpintuanNewPublish(function(xhr, re) {
					var code = re.code;
					if(code == 0) {
						var data = re.data;
						if(data.length > 3) data.length = 3;
						$scope.newPublish = data;
					} else {
						//						ToastUtils.showMsgWithCode(code, re.msg);
						ToastUtils.showMsgWithCode(111111);
					}
				}, function(response, data) {
					ToastUtils.showMsgWithCode(7, '获取最新揭晓失败：' + '状态码：' + response.status);
				})
			}
			$scope.timeoutCallback = function(activity) {
				PintuanModel.pintuan_getDetail_info(activity.activity_id, function(response, data) {
					if(data.code == 0) {
						var newActivity = data.data;
						activity.lucky_unick = newActivity.lucky.nick;
						activity.lucky_num = newActivity.lucky.lucky_num;
						activity.lucky_user_num = newActivity.lucky.user_num;
						activity.flag = newActivity.teamdetial.flag;
//						if ($scope.getHomeNewPublishTimeOut) $timeout.cancel($scope.getHomeNewPublishTimeOut);
//                      $scope.getHomeNewPublishTimeOut = $timeout(function() {
//                          getHomeNewPublish()
//                      }, 10000)
					}
				}, function() {

				}, function() {

				});
			};
			$scope.$on('$ionicView.beforeEnter', function(ev, data) {

				if(Global.isInweixinBrowser()) {
					$scope.inWechatB = true;
				} else {
					if(ionic.Platform.isIOS() || ionic.Platform.isIPad()) {
						$scope.inIosApp = true;
					} else {
						$scope.inIosApp = false;
					}
					$scope.inWechatB = false;
				}
				getHomeNewPublish();
				$scope.refresh = refresh;
				refresh();
				//				PintuanModel.pintuan_homepage('', onSuccess, onFailed, onFinal);
				$timeout(function() {
					$ionicSlideBoxDelegate.next();
				}, 1000)
			})
		}
	});