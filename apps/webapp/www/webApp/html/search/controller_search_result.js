/**
 * Created by luliang on 2016/1/8.
 */
define(
	[
		'app',
		'components/view-broad/view_broad',
		'models/model_activity',
		'models/model_pintuan',
		'utils/toastUtil',
		'html/common/global_service',
	],
	function(app) {
		'use strict';
		app
			.controller('SearchResultController', ['$scope', '$state', '$stateParams', 'ActivityModel', 'PintuanModel', 'ToastUtils', 'Global',
				function($scope, $state, $stateParams, ActivityModel, PintuanModel, ToastUtils, Global) {
					$scope.keyword = $stateParams.keyword;
					$scope.productType = $stateParams.productType;
					$scope.search = search;
					search()

					console.log($stateParams.productType)

					function search() {
						$scope.$broadcast('broad.refresh', '3-4-001');
					}
					$scope.go_search = function() {
						$state.go('search', {
							productType: $stateParams.productType
						});
					}

					$scope.postData = {
						index: 1,
						isLoading: false,
						data: [],
						goods_type_id: '',
						order_key: 'weight', //类别
						oder_type: '',
						activity_type: 1, //1.普通拼团 2.幸运拼团 3.团长免费
						emptyData: false,
						page: 0,
						pageOver: false,
						pageCount: 10
					}
					$scope.refresh = refresh;
					

					function refresh() {
						PintuanModel.pintuan_homepage(null, $scope.keyword, null, null, $scope.postData.index, $scope.postData.pageCount, '0', $scope.postData.activity_type, function(xhr, re) {
							var code = re.code;
							if(code == 0) {
								var data = re.data;
								var len = data.length;
								for(var i = 0; i < len; i++) {
									$scope.postData.data.push(data[i]);
								}
								if(len == $scope.postData.pageCount) {
									$scope.postData.pageOver = false;
								} else {
									$scope.postData.pageOver = true;
								}
								console.log($scope.postData.data)
							} else {
								ToastUtils.showMsgWithCode(code, re.msg);
							}
							$scope.$broadcast('scroll.infiniteScrollComplete');
						}, function(response, data) {
							ToastUtils.showMsgWithCode(7, '获取商品列表失败：' + '状态码：' + response.status);
						}, function() {
							console.log('final')
							$scope.postData.isLoading = false;
							if(doRefresh) $scope.$broadcast('scroll.refreshComplete');
							$scope.$broadcast('scroll.infiniteScrollComplete');
						})
					}
					$scope.loadMore = function() {
						console.log('bottom')
						$scope.postData.index = $scope.postData.index + $scope.postData.pageCount;
						refresh();
					}
					//回到主页
					$scope.goHome = function() {
						if($scope.productType == 1) {
							$state.go('tab.mainpage');
						} else if($scope.productType == 2) {
							$state.go('pintuan_main_page');
						}
					}
					////跳转到2人团商品详情页
					$scope.gotoPintuan_Detail = function(goods_id) {
						console.log(goods_id)
						$state.go('pintuan_detail', {
							goods_id: goods_id
						});
					};
					var gapHeight = 0;
					var innerHeight = window.innerHeight;

					if(Global.isInweixinBrowser()) {
						$scope.leftBarHeight = innerHeight - gapHeight;
						$scope.leftBarTop = '202px';
						$scope.inWechatB = true;
					} else {
						$scope.leftBarHeight = innerHeight - 44 - gapHeight;
						if(ionic.Platform.isIOS() || ionic.Platform.isIPad()) {
							$scope.leftBarTop = '264px';
							$scope.inIosApp = true;
						} else {
							$scope.leftBarTop = '88px';
							$scope.inIosApp = false;
						}
						$scope.inWechatB = false;

					}
					$scope.$on('$ionicView.enter', function() {
						$scope.postData.index = 1;
						$scope.postData.data = [];
						refresh();
					});
				}
			]);
	});