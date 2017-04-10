define(
	[
		'app',
		'components/view-slidebox/view_slidebox',
		'components/view-broad/view_broad',
		'utils/toastUtil',
		'models/model_activity',
		'components/view-progress/view_progress',
		'html/trolley/trolley_service',
		'components/view-text-scoller/view_text_scoller',
		'html/common/storage',
		'components/view-countdown/view_countdown',
		'models/model_goods',
		'models/model_app',
		'html/common/global_service',
		'html/thirdParty/thirdparty_wechat_js',
	],
	function(app) {

		'use strict';
		app
			.controller('MainPageController', ['$scope', '$state', 'ToastUtils', 'ActivityModel', 'trolleyInfo', 'Storage', 'GoodsModel', '$timeout', 'AppModel', '$ionicPopup', 'Global', 'weChatJs', '$ionicSlideBoxDelegate', '$ionicScrollDelegate',
				function($scope, $state, ToastUtils, ActivityModel, trolleyInfo, Storage, GoodsModel, $timeout, AppModel, $ionicPopup, Global, weChatJs, $ionicSlideBoxDelegate, $ionicScrollDelegate) {
					$scope.show_attention = true;
					if(Global.isInweixinBrowser()) {
						$scope.inWechatB = true;
					} else {
						$scope.inWechatB = false;
					}
					$scope.close_attention = function($event) {
						$event.stopPropagation();
						$scope.show_attention = false;
					}
					$scope.go_attention = function() {
						$state.go('attention');
					}
					$scope.go_search = function() {
						$state.go("classification_details");
						/*$state.go('search',{
							productType:1
						});*/
					}

					//                  去返现购专区
					$scope.gotoReturnCash = function() {

						$state.go('return_cash');
						var ruleImgAlert = 'img/fanxiangouRule.png';
						var fanxian = $ionicPopup.show({
							template: '<img id="fanxiangouRule" src="' + ruleImgAlert + '"><i></i>',
							buttons: [{
								text: '确定',
								type: 'button-default',
								onTap: function(e) {
									console.log(e)
								}
							}]
						})
						var timer = $timeout(function() {
							var fanxiangouRule = document.getElementById('fanxiangouRule'),
								popupBody = fanxiangouRule.parentElement,
								popup = popupBody.parentElement,
								span = fanxiangouRule.querySelector('span'),
								popupheader = popup.getElementsByClassName('popup-head')[0];
							popupheader.style.display = 'none';
							popup.style.height = '286px';
							popup.style.width = '300px';
							//										popup.style.backgroundColor = 'transparent';
							/*返回用户不是微信账户的错误*/
						}, 10)
					}
					getBanner();
					//获取广告图
					function getBanner() {
						ActivityModel.getBanner(1, function(xhr, re) {
							var code = re.code;
							if(code == 0) {
								$scope.slides = re.data;
							} else {
								ToastUtils.showMsgWithCode(code, re.msg);
							}
						}, function(response, data) {
							ToastUtils.showMsgWithCode(7, '获取广告图失败：' + '状态码：' + response.status);
						}, null)
					}

					$scope.getLuckyData = [];
					//获取最近中奖消息
					function getLuckyData_1() {
						ActivityModel.getluckyInfo(function(xhr, re) {
							var code = re.code;
							if(code == 0) {
								$scope.getLuckyData = [];
								$timeout(function() {
									$scope.getLuckyData = re.data;
								})

							} else {
								ToastUtils.showMsgWithCode(code, re.msg);
							}
						}, function(response, data) {
							ToastUtils.showMsgWithCode(7, '获取最近中奖消息失败：' + '状态码：' + response.status);
						}, null)
					}

					function initConfig() {
						$scope.isLoadFinished = true;
						$scope.pageConfig = [{
							index: 1,
							isLoading: false,
							data: [],
							goods_type_id: '',
							order_key: 'ing', //类别
							oder_type: '',
							activity_type: -4, //1、非10元；2、10元；3、限购  //原本0改成-1 是让首页不显示二人购
							emptyData: false,
							page: 0,
							pageOver: false,
							pageCount: 20
						}]

						getLuckyData_1();
						getHomeNewPublish()
						$scope.getData(true)
					}

					$scope.doRefresh = function() {
						initConfig()
					};
					$scope.timeoutCallback = function(activity) {
						GoodsModel.getGoodsDetail(activity.activity_id, function(response, data) {
							if(data.code == 0) {
								var newActivity = data.data;
								activity.status = newActivity.status;
								activity.lucky_unick = newActivity.lucky_unick;
								activity.lucky_num = newActivity.lucky_num;
								activity.lucky_user_num = newActivity.lucky_user_num;
								activity.lucky_ip = newActivity.lucky_ip;

								if($scope.getHomeNewPublishTimeOut) $timeout.cancel($scope.getHomeNewPublishTimeOut);
								$scope.getHomeNewPublishTimeOut = $timeout(function() {
									getHomeNewPublish()
								}, 10000)
							}
						});

					};

					function getHomeNewPublish() {
						ActivityModel.getHomeNewPublish(function(xhr, re) {
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
					$scope.changeActive = function(order_key, order_type, config) {
						if(!$scope.isLoadFinished) return;
						config.order_key = order_key;
						config.isLoading = true;
						if(order_type == 'none') {
							config.order_type = '';
						} else if(order_type == 'asc') {
							config.order_type = 'desc';
						} else {
							config.order_type = 'asc';
						}
						config.page = 0;
						config.data = [];
						$scope.getData(true);

					}
					$scope.getMoreData = function() {
						if($scope.pageConfig[0].isLoading) return;
						$scope.getData()
					}
					$scope.getData = function(doRefresh) {
						var postData = $scope.pageConfig[0];
						postData.isLoading = true;
						postData.page++
							ActivityModel.getActivityList(null, null, postData.order_key, postData.order_type, (postData.page - 1) * postData.pageCount + 1, postData.pageCount, '0', postData.activity_type, function(xhr, re) {
								var code = re.code;
								if(code == 0) {
									var data = re.data;
									var len = data.length;
									for(var i = 0; i < len; i++) {
										postData.data.push(data[i]);
									}
									if(len == postData.pageCount) {
										postData.pageOver = false;
									} else {
										postData.pageOver = true;
									}
								} else {
									ToastUtils.showMsgWithCode(code, re.msg);
								}
							}, function(response, data) {
								ToastUtils.showMsgWithCode(7, '获取商品列表失败：' + '状态码：' + response.status);
							}, function() {
								postData.isLoading = false;
								if(doRefresh) $scope.$broadcast('scroll.refreshComplete');
								$scope.$broadcast('scroll.infiniteScrollComplete');
							})
					}

					$scope.getPercentageProgress = function(remain_num, need_num) {
						return Math.round((need_num - remain_num) * 100 / need_num);
					};

					$scope.gotoDetail = function(id) {
						$state.go('activity-goodsDetail', {
							activityId: id
						});
					};
                    $scope.goNews = function() {
                        $state.go('myNews');
                    };

					//添加一个计算详情功能
					$scope.gotoCountDetail = function(id) {
						$state.go('countDetail', {
							activityId: id
						});
					};


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
                        if (ionic.Platform.isIOS() || ionic.Platform.isIPad()) {
                            $scope.leftBarTop = '88px';
                            $scope.inIosApp = true;
                        } else {
                            $scope.leftBarTop = '88px';
                            $scope.inIosApp = false;
                        }
                        $scope.inWechatB = false;
                    }


					//滚动触发的函数
					$scope.scroll = function(){
                        var topTab=document.getElementById('top_tab');
                        var ionContent=document.getElementById('ionContent');
                        if(Global.isInweixinBrowser()) {
                            if($ionicScrollDelegate.getScrollPosition().top >= 330){
                                document.getElementById('top_tab').style.display = 'block';
                                document.getElementById('content_tab').style.display = 'none';
                                document.getElementById('index_icon_top').style.display = 'block';

                            } else{
                                document.getElementById('content_tab').style.display = 'block';
                                document.getElementById('top_tab').style.display = 'none';
                                document.getElementById('index_icon_top').style.display = 'none';

                            }

                        } else {
                            if($ionicScrollDelegate.getScrollPosition().top >= 353){

                                if (ionic.Platform.isIOS() || ionic.Platform.isIPad()) {
                                    topTab.style.top='0px';
                                    ionContent.style.top='44px'
                                } else {
                                    topTab.style.top='0px';
                                    ionContent.style.top='44px'
                                }
                                topTab.style.top='44px';
                                ionContent.style.top='44px'
                                document.getElementById('top_tab').style.display = 'block';
                                document.getElementById('content_tab').style.display = 'none';
                                document.getElementById('index_icon_top').style.display = 'block';

                            } else{
                               document.getElementById('content_tab').style.display = 'block';
                                document.getElementById('top_tab').style.display = 'none';
                                document.getElementById('index_icon_top').style.display = 'none';

                            }

                        }
					}
					//返回最顶部
					$scope.scrollTop = function() {
						$ionicScrollDelegate.scrollTop(true);
					};
					$scope.$on('$ionicView.beforeEnter', function(ev, data) {
						initConfig();
						$timeout(function() {
							if (document.location.hash == '#/tab/mainpage') {
								$ionicSlideBoxDelegate.next();
							}
						}, 1000)

					})

					$scope.$on('$destroy', function() {
						if($scope.getHomeNewPublishTimeOut) $timeout.cancel($scope.getHomeNewPublishTimeOut);
					});

				}
			]);
	});