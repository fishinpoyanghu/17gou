define([
	'app',
	'models/model_user',
	'models/public_function',
	'utils/toastUtil',
	'html/common/global_service',
	'components/view-progress/view_progress'
], function(app) {

	app.controller(
		'luckyBagCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel','publicFunction', 'ToastUtils', '$rootScope',
			function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel,publicFunction, ToastUtils, $rootScope) {
				$scope.listDatas = [];
				$scope.luckyBags = [];
				$scope.count = '';
				$scope.isLoadFinished = true;
				$scope.loadRequestStart = 1;
				$scope.loadRequestCount = 10;
				$scope.loadFinished = false;
				$scope.luckyBagNum = 0;
				$scope.endNoticeInfo = '没有更多啦～';
				//              getData();

				$scope.getData = getData;
				/*$scope.doRefresh = doRefresh;*/

				//获取福袋数据
				function getData(doRefresh) {
//					console.log(publicFunction.formatTime(2361226550,'yyyy年mm月dd日'))
					/* if (!$scope.isLoadFinished) return;
					 $scope.isLoadFinished = false;*/

					//获取福袋数量
					userModel.getLoginUserInfo(function(response,data) {
						var data = data.data;
						if (response.data.code == 0) {
							$scope.luckyBagNum = data.lucky_packet;
						}
					}, function() {
						ToastUtils.showError('请检查网络状态！');
					})

					//获取动态数据
					userModel.getLuckyBagList($scope.loadRequestStart, $scope.loadRequestCount, function(xhr, re) {
						var code = re.code;
						if(code == 0) {
							var data = re.data;
							$scope.luckyBags = $scope.luckyBags.concat(data);
							if(data.length < $scope.loadRequestCount) {
								$scope.loadFinished = true;
							}
							$scope.loadRequestStart = $scope.loadRequestStart + $scope.loadRequestCount;
						} else {
							/*ToastUtils.showMsgWithCode(code, re.msg);*/
						}
					}, function(response, data) {
						/*ToastUtils.showMsgWithCode(7, '获取提现申请记录失败：' + '状态码：' + response.status);*/
					}, function() {
						/*$scope.isLoadFinished = true;*/
                        $scope.$broadcast('scroll.infiniteScrollComplete');
						if(doRefresh) $scope.$broadcast('scroll.refreshComplete');

					})
				}
				$scope.loadMore = function() {
					getData();
					$scope.endNoticeInfo = '我可是有底线的';
				}

				$scope.go_mainpage = function() {
					$state.go("tab.mainpage")
				}
				//去邀请注册页面
				$scope.go_inviteFriends = function() {
					$state.go("inviteFriends")
				}
				//去福袋图文详情
				$scope.go_luckyBag_imgText = function() {
					$state.go("activityRule",{
						type:'luckyBag'
					})
				}

				/*getRecentOrderList();
				getData('');*/
				$scope.doRefresh = function() {
					$scope.loadRequestStart = 1;
					$scope.luckyBags = [];
					$scope.loadFinished = false;
					/*$scope.listData = [];*/
					getData('doRefresh');
					//                  getRecentOrderList();
				}

				$scope.goBack = function() {
					$ionicHistory.goBack();
				};
				$scope.$on('$ionicView.beforeEnter', function(ev, data) {
					$scope.doRefresh()
				})

			}
		])

});