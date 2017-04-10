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
		//		'components/view-buy-pop/view_buy_pop',
		'components/view-choice-footer/view_choice_footer',
		'models/model_goods',
		'models/model_pintuan',
		'html/common/service_user_info',
		'html/common/geturl_service',
		'html/common/global_service',
		'html/thirdParty/thirdparty_wechat_js',
		'html/common/storage',
		'models/model_user'
	],
	function(app) {
		"use strict";

		app.controller('pintuanDetailCtrl', pintuanDetailCtrl);
		pintuanDetailCtrl.$inject = ['$scope', '$state', '$stateParams', 'GoodsModel', 'PintuanModel', 'MyUrl', 'ToastUtils', 'userInfo', 'Global', 'weChatJs', 'Storage', 'userModel', '$ionicHistory', '$ionicScrollDelegate', '$ionicPopup'];

		function pintuanDetailCtrl($scope, $state, $stateParams, GoodsModel, PintuanModel, MyUrl, ToastUtils, userInfo, Global, weChatJs, Storage, userModel, $ionicHistory, $ionicScrollDelegate, $ionicPopup) {
			(function init() {
				$scope.smglist = {};
				$scope.isLogin = MyUrl.isLogin();
				$scope.activityId = parseInt($stateParams.goods_id);
				$scope.isLoading = true;
				$scope.remain_time = []; //还剩下多少时间结束
				$scope.isLoadFinished = true; //是否加载结束
				$scope.displayNavBar = false;
				$scope.isCollect = false; //该是否被收藏，默认为没有被收藏
				$scope.pintuan_status = ['', '正在开团', '拼团结束', '拼团成功', '平团失败'];

				//默认为1，图文详情
				//默认为2，正在开团
				//默认为3，掌柜推荐
				//默认为4，你更喜欢
				$scope.pintuan_index = 1;
			})();
			$scope.$on('$ionicView.enter', function() {
				$ionicScrollDelegate.scrollTop();
				getCurrentPane();
				updataHistory()
				$scope.refresh = refresh;
				refresh();
				if(!$ionicHistory.backView() && !Global.isInAPP()) {
					$scope.firstInIsGoodsPage = true;
				} else {
					$scope.firstInIsGoodsPage = false;
				}
				$scope.isLogin = MyUrl.isLogin();
				if($scope.isLogin) {
					$scope.getCollectList()
				}
				//				refresh();
			});

			$scope.$on('$ionicView.beforeLeave', function(a, b) {
				clearInterval(timerSyncNotice);
			});

			function updataHistory() {
				var pintuanHistory = Storage.get('pintuan_goods_record'),
					pintuanHistory_goods_id = [];
				if($scope.isLogin) {
					if(pintuanHistory) {
						angular.forEach(pintuanHistory, function(data, i) {
							pintuanHistory_goods_id.push(data.goods_id)
						})
						PintuanModel.updataHistory(pintuanHistory_goods_id, function(response, data) {
							if(data.code == 0) {
								$scope.historyRecord = data.data;
								Storage.remove('pintuan_goods_record')
								//								console.log($scope.historyRecord)
							}
						}, function(response) {

						}, function() {

						})
					}
				} else {
					console.log($scope.historyRecord)
					$scope.historyRecord = pintuanHistory;
				}
			}

			//获取提示信息的数据
			$scope.smlistNotShow = false;

			function getSysnotify() {
				GoodsModel.getSysnotify('2', function onSuccess(response, data) {
					var code = data.code;
					if(0 == code) {
						$scope.data = data.data;
						$scope.detailMsgShow = [];
						if($scope.smglist.show) {
							/*if ($scope.smglist.show.id == data.data[0].id) {
								$scope.smlistNotShow = true;
							}else{
								$scope.smlistNotShow = false;
							}*/
							if($scope.smglist.show.id == data.data[0].id) {
								$scope.contentCss = "hideContent";
							} else {
								$scope.contentCss = "showContent";
							}
						}
						if(Storage.get('currentShowIdPintuan') && (Storage.get('currentShowIdPintuan')[0] != null)) {
							if(!Storage.get('currentShowIdPintuan')[0].length) {
								angular.forEach(data.data, function(n, i, obj) {
									if(parseInt(Storage.get('currentShowIdPintuan')[0].id) < parseInt(n.id)) {
										$scope.detailMsgShow.push(n)
									}
								})
							} else {
								$scope.detailMsgShow = data.data;
							}
						} else {
							$scope.detailMsgShow = data.data;
						}
						Storage.set('currentShowIdPintuan', $scope.detailMsgShow)
					} else {
						ToastUtils.showError('加载失败：' + data.msg);
					}
				}, function onFailed(response, data) {
					ToastUtils.showError('网络异常：' + '状态码：' + response.statusText);
				});
			}
			//			getSysnotify();

			var timerSyncNotice = setInterval(function() {
				var currentShowIdPintuan = Storage.get('currentShowIdPintuan') || [];
				if(currentShowIdPintuan.length && (currentShowIdPintuan[0] != null)) {
					$scope.smglist.show = currentShowIdPintuan[0];
					currentShowIdPintuan.splice(0, 1);
					Storage.set('currentShowIdPintuan', currentShowIdPintuan)
				} else {
					if($scope.smglist.show != null && !$scope.smglist.show.length) {
						Storage.set('currentShowIdPintuan', [$scope.smglist.show]);
					}
					$scope.smglist.show = [];
					getSysnotify();
				}
			}, 6000)

			$scope.scrollTop = function() {
				$ionicScrollDelegate.scrollTop(true);
			}
			$scope.gotoMainPage = function() {
				$state.go('tab.mainpage')
			}
			//    跳到支付页面
			$scope.gotopintuanApply = function(orderType) {
				//ordertype:  2是参团    3是开团   4是单买

				if(!MyUrl.isLogin()) {
					console.log('去登录');
					$state.go('login', {
						'state': STATUS.LOGIN_ABNORMAL
					});
					return;
				} else {
					console.log()
					//					$scope.$broadcast('view-buy-pop.show');
				}
				$state.go('pintuan_apply', {
					activityId: $scope.activityId,
					orderType: orderType
				})
			}

			//			调到对应的团的详情成员页面
			$scope.getHisMumber = function(teamwar_id) {
				$state.go('pintuan_member', {
					team: teamwar_id
				});
			};

			$scope.gotoFullIntroduce = function() {
				$state.go('activity-fullIntroduce', {
					activity: $scope.activity,
					activityId: $scope.activityId,
					goodsId: $scope.activity.goods_id
				});
			};
			$scope.gotoBaituan_Detail = function(goods_id) {
				$state.go('baituan_detail', {
					goods_id: goods_id
				});
			};
			//		报名加入别人的图
			$scope.pintuan_joinOther = function(teamwar_id) {

				//				PintuanModel.pintuan_jointeam($scope.activityId, onSuccess, onFailed, onFinal);
			}
			var mTitle = '参与亿七购百团大战活动！';
			var mContent = '参与活动就有可能获得“奖品”';
			var mImgUrls = '';
			var mLink = window.location.href;
			$scope.doShareRecord = function() {
				mTitle = $scope.activity.price + '元抢购' + $scope.activity.goods_title;
				mContent = $scope.activity.goods_subtitle;
				mImgUrls = $scope.activity.goods_img[0];
				if(Global.isInweixinBrowser()) {
					showGuide();
					//                      hideWinDialog();
					weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, onShareSuccess, onShareCancel);
					weChatJs.wxShareToAppMessage(mTitle, mContent, mLink, mImgUrls, onShareSuccess, onShareCancel)
					//显示引导分享界面
				} else if(Global.isInAPP()) {
					$scope.isShowShare = true;
				}
			};
			$scope.addPoint = true;

			function toAddPoint() {
				return '';
				if(!$scope.addPoint) return;
				$scope.addPoint = false;
				userModel.toAddPoint(function(xhr, re) {
					var code = re.code;
					if(code == 0) {
						ToastUtils.showShortNow(STATE_STYLE.GOOD, re.msg);
					} else {

					}
				}, function(response, data) {
					ToastUtils.showMsgWithCode(7, '获取积分失败：' + '状态码：' + response.status);
				}, function() {
					$scope.addPoint = true;
				})
			}

			function onShareSuccess() {
				hideGuide();
				toAddPoint();
			}
			$scope.hideGuide1 = function() {
				hideGuide();
			}

			function onShareCancel() {
				hideGuide();
				//              ToastUtils.showShortNow(STATE_STYLE.ERROR, '用户已取消分享');
			}

			function showGuide() {
				document.getElementById("guidepop").style.display = 'block';
			}

			function hideGuide() {
				document.getElementById("guidepop").style.display = 'none';
			}

			function refresh() {
				//				ToastUtils.showLoading('加载中...');
				PintuanModel.pintuan_getGoodsDetail_info($scope.activityId, onSuccess, onFailed, onFinal);
				initConfig()
				//				GoodsModel.getGoodsDetail($scope.activityId, onSuccess, onFailed, onFinal);

			}

			function initConfig() {
				$scope.pageConfig = [{
					index: 1,
					isLoading: false,
					data: [],
					goods_type_id: '',
					order_key: 'weight', //类别
					oder_type: '',
					activity_type: 1, //1、普通拼团；2、幸运拼团；3、团长免费  
					emptyData: false,
					page: 0,
					pageOver: false,
					pageCount: 20
				}];
				//						getcategoryList();
				getPintuanData(true);

			}

			function getPintuanData(doRefresh) {
				var postData = $scope.pageConfig[0];
				postData.isLoading = true;
				postData.page++;
				PintuanModel.pintuan_homepage(null, null, postData.order_key, postData.order_type, (postData.page - 1) * postData.pageCount + 1, postData.pageCount, '0', postData.activity_type, function(xhr, re) {
					var code = re.code;
					if(code == 0) {
						var data = re.data;
						$scope.hotGoodsList = re.data;
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

			function onSuccess(response, data) {
				if(data.code == 0) {
					$scope.activity = data.data;
					//					console.log($scope.activity.goods_id)
					if(!Storage.get('pintuan_goods_record')) {
						Storage.set('pintuan_goods_record', []);
					}
					//					console.log($scope.activity)
					var historyLisy = {
						goods_id: $scope.activity.goods_id,
						main_img: $scope.activity.img,
						price: $scope.activity.price,
						title: $scope.activity.goods_title
					}
					$scope.pintuan_goods_record = Storage.get('pintuan_goods_record');

					angular.forEach($scope.pintuan_goods_record, function(data, i, obj) {
						console.log(data)
						if($scope.activity.goods_id == data.goods_id) {
							$scope.pintuan_goods_record.splice(i, 1)
						}
					})
					$scope.pintuan_goods_record.unshift(historyLisy);
					$scope.pintuan_goods_record.length > 15 && ($scope.pintuan_goods_record.length = 15);
					Storage.set('pintuan_goods_record', $scope.pintuan_goods_record)
					//					console.log(Storage.get('pintuan_goods_record'))
					//					Storage.remove('pintuan_goods_record')

					/*$scope.pintuan_goods_record = Storage.get('pintuan_goods_record');
					angular.forEach($scope.pintuan_goods_record, function(data, i, obj) {
						if($scope.activity.goods_id == data) {
							$scope.pintuan_goods_record.splice(i, 1)
						}
					})
					$scope.pintuan_goods_record.unshift($scope.activity.goods_id);
					$scope.pintuan_goods_record.length > 15 && ($scope.pintuan_goods_record.length = 5);
					Storage.set('pintuan_goods_record', $scope.pintuan_goods_record)
					console.log($scope.pintuan_goods_record)*/

					//					$scope.activity.natures = [{
					//						name: '颜色',
					//						values: ['红白色', '蓝色', '橙色',
					//							'天蓝色', '赤橙黄绿'
					//						]
					//					}, {
					//						name: '尺寸',
					//						values: ['165cm', '185cm', '170cm',
					//							'175cm', '195cm', '205cm'
					//						]
					//					}, {
					//						name: '产地',
					//						values: ['中国', '英国', '美国',
					//							'日本', '法国', '德国'
					//						]
					//					}, {
					//						name: '生产日期',
					//						values: ['2006', '2007', '2008',
					//							'2009', '2010', '2011'
					//						]
					//					}, {
					//						name: '数量',
					//						values: ['1双', '2双', '3双',
					//							'4双', '5双', '10双'
					//						]
					//					}];

					if($scope.activity.activity_type == 2) {
						$scope.gotoBaituan_Detail($scope.activity.goods_id)
					}
					mTitle = $scope.activity.price + '元抢购' + $scope.activity.goods_title;
					mContent = $scope.activity.goods_subtitle;
					mLink = window.location.href;
					mImgUrls = $scope.activity.goods_img[0];
					$scope.isCollect = false; //该是否被收藏，默认为没有被收藏
					setTimeout(function() {
						if(Global.isInweixinBrowser()) {
							weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, onShareSuccess, onShareCancel);
							weChatJs.wxShareToAppMessage(mTitle, mContent, mLink, mImgUrls, onShareSuccess, onShareCancel)
						}
					}, 1000)
					$scope.isLoadFinished = false; //是否加载结束
					$scope.pintuan_index = 1;
					$scope.getFullIntroduce();
					$scope.isLoading = false;
					//					var timer = setTimeout(function() {
					//					var footHeight = document.getElementById('pintuan_footer').offsetHeight;
					//					var viewListHeight = document.querySelectorAll('#pintuan_detail_tab_item>.row')[0].offsetHeight;
					//					console.log(111111)
					//					document.getElementById('pintuan_detail_tab_item').style.minHeight = document.documentElement.clientHeight + 2 - footHeight + 'px';
					//					if(document.getElementById('pintuan_detail_tab_item').style.minHeight == document.documentElement.clientHeight) {
					//						clearInterval(timer)
					//					}
					//				}, 500)
				} else {
					//					ToastUtils.showError(data.msg);
					ToastUtils.showError('数据错误，请重新加载');
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
			//获得商品的图文详情
			$scope.getFullIntroduce = function() {
				$scope.getGoodsDetail = getGoodsDetail;
				$scope.content = '';
				(function initial() {
					getGoodsDetail();
					if($scope.activity == null) {
						setTimeout(function() {
							getActivity($scope.activityId);
						}, 300);
					} else {
						$scope.activity = $scope.activity;
						$scope.activity.ispintuan = true;
					}
				})();

				function getGoodsDetail() {
					PintuanModel.pintuan_getGoodsImgDetail($scope.activity.goods_id, function onSuccess(response, data) {
						var code = data.code;
						if(0 == code) {
							$scope.content = data.data.html;
							$scope.hasLoad = true;
						} else {
							ToastUtils.showError('加载失败：' + data.msg);
						}
					}, function onFailed(response, data) {
						ToastUtils.showError('网络异常：' + '状态码：' + response.statusText);
						$scope.disconnected = true;
					});
				}

				function getActivity(activityId) {
					GoodsModel.getGoodsDetail(activityId,
						function onSuccess(response, data) {
							if(data.code == 0) {
								$scope.activity = data.data;
								$scope.activity.ispintuan = true;
							}
						});
				}
			}
			//图文详情      参团列表    掌柜推荐   你更喜欢
			$scope.getImgDetail = function() { //图文详情
				$scope.pintuan_index = 1;
				scrollToTabItem()
				if(MyUrl.isLogin()) {
					$scope.getCollectList()
				}
			}
			$scope.getTimeList = function() { //参团列表
				//					$ionicScrollDelegate.scrollTo();
				$scope.pintuan_index = 2;
				scrollToTabItem();

			}
			$scope.getDoodsList = function() { //掌柜推荐
				$scope.pintuan_index = 3;
				scrollToTabItem();
			}
			$scope.getHeartGoods = function() { //你更喜欢
				if(MyUrl.isLogin()) {
					$scope.getCollectList()
				}
				$scope.pintuan_index = 4;
				scrollToTabItem();
			}
			//调到
			function scrollToTabItem() {

				var item = $scope.currentPane.querySelectorAll('#pintuan_detail_tab_item')[0];
				if(item.getBoundingClientRect().top < 0) {
					$ionicScrollDelegate.scrollTo('0', $scope.detailTabItemHeight)
				}
			}
			//				end图文详情      参团列表    掌柜推荐   你更喜欢

			$scope.gotoDetail = function(team_id, type, good_id) {
				if(type == 7) {
					console.log(type)
					$state.go('pintuan_detail', {
						goods_id: good_id
					});
				} else {
					$state.go('pintuan_member', {
						team: team_id
					});
				}
				console.log(good_id)
			};
			$scope.scroll = function() {
				if(!$scope.currentPane) {
					return;
				}
				var item = $scope.currentPane.querySelectorAll('#pintuan_detail_tab_item')[0];

				if(item.getBoundingClientRect().top < 0) {
					$scope.currentPane.querySelectorAll('#pintuan_detail_tab_item1')[0].style.display = 'block';
					$scope.currentPane.querySelectorAll('#index_icon_top')[0].style.display = 'block';
					document.getElementById("bubble-list").style.top = "8%";
				} else {
					$scope.currentPane.querySelectorAll('#pintuan_detail_tab_item1')[0].style.display = 'none';
					$scope.currentPane.querySelectorAll('#index_icon_top')[0].style.display = 'none';
					document.getElementById("bubble-list").style.top = "2%";
				}
				//					标签页跳的位置
				$scope.detailTabItemHeight = item.offsetTop;
			}
			//获得当前页面
			function getCurrentPane() {
				setTimeout(function() {

					var panes = document.querySelectorAll('div.pane');
					$scope.currentPane = {};
					angular.forEach(panes, function(n, i, arr) {
						if(n.getAttribute('nav-view') == 'active') {
							$scope.currentPane = panes[i];
						}
					})
					var footHeight = $scope.currentPane.querySelectorAll('#pintuan_footer')[0].offsetHeight;
					var viewListHeight = $scope.currentPane.querySelectorAll('#pintuan_detail_tab_item>.row')[0].offsetHeight;
					$scope.currentPane.querySelectorAll('#pintuan_detail_tab_item')[0].style.minHeight = window.screen.height - footHeight + 2 + 'px';

				}, 500)
			}
			$scope.gotoLogin = function() {
				$state.go('login', {
					'state': STATUS.LOGIN_ABNORMAL
				});
			}
			$scope.gotoPintuan_Detail = function(good_id) {
				$state.go('pintuan_detail', {
					goods_id: good_id
				});
			}
			$scope.collectGoods = function(goods_id) {
				if(MyUrl.isLogin()) {
					if(!$scope.isCollect) {
						addCollect(goods_id);
					} else {
						$scope.removeCollect(goods_id)
					}
				} else {
					$state.go('login', {
						'state': STATUS.LOGIN_ABNORMAL
					});
					ToastUtils.showWarning('请先登录！！');
					return;
				}

				function addCollect() {
					PintuanModel.addCollect(goods_id, function onsuccess(response, data) {
						if(data.code == 0) {
							$scope.isCollect = true;
							//								$ionicPopup.show({
							//									title:'提示',
							//									template:'收藏成功'
							//								});
							ToastUtils.showSuccess('收藏成功');
						} else {
							ToastUtils.showError('加载失败：' + data.msg);
						}
					}, function onfail(response, data) {
						ToastUtils.showError('加载失败：' + data.msg);
					})
				}
			}
			//取消收藏
			$scope.removeCollect = function(id) {
				PintuanModel.removeCollect(id, function success(response, data) {
					if(data.code == 0) {
						$scope.isCollect = false;
						ToastUtils.showSuccess('取消收藏成功');
					}
				});
			}
			$scope.getCollectList = function() {
				PintuanModel.getCollectList(function onSuccess(response, data) {
					if(data.code == 0) {
						$scope.collectList = data.data;
						angular.forEach($scope.collectList, function(n, i, arr) {
							if(n.goods_id == $scope.activity.goods_id) {
								$scope.activity.collect_id = n.collect_id;
								$scope.isCollect = true;
							}
						})
					}
				});
			}
		}
	});