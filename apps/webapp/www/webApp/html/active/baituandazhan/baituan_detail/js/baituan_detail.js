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
		'html/common/service_user_info',
		'html/common/geturl_service',
		'html/common/global_service',
		'html/thirdParty/thirdparty_wechat_js',
		'models/model_user'
	],
	function(app) {
		"use strict";

		app.controller('baituanDetailCtrl', baituanDetailCtrl);
		baituanDetailCtrl.$inject = ['$scope', '$state', '$stateParams', 'GoodsModel','PintuanModel', 'MyUrl', 'ToastUtils', 'userInfo', 'Global', 'weChatJs', 'userModel','$ionicHistory'];

		function baituanDetailCtrl($scope, $state, $stateParams, GoodsModel,PintuanModel, MyUrl, ToastUtils, userInfo, Global, weChatJs,userModel, $ionicHistory) {
			(function init() {

				$scope.isLogin = MyUrl.isLogin();
				$scope.activityId = parseInt($stateParams.goods_id);
				$scope.isLoading = true;
				$scope.remain_time = []; //还剩下多少时间结束
				$scope.baituan_status = ['','正在开团','拼团结束','拼团成功','平团失败']
				$scope.refresh = refresh;
				refresh();
			})();
			$scope.$on('$ionicView.enter', function() {
				if(!$ionicHistory.backView() && !Global.isInAPP()) {
					$scope.firstInIsGoodsPage = true;
				} else {
					$scope.firstInIsGoodsPage = false;
				}
				PintuanModel.baituan_getGoodsDetail_info($scope.activityId, onSuccess, onFailed, onFinal);
				refresh();
			});

			$scope.gotoMainPage = function() {
					$state.go('tab.mainpage')
				}
				//    跳到支付页面
			$scope.gotobaituanApply = function() {
				if(!$scope.isLogin){
					console.log('去登录');
					$state.go('login');
					return;
				}
				$state.go('baituan_apply',{activityId:$scope.activityId})
			}
			
//			调到对应的团的详情成员页面
			$scope.getHisMumber = function (teamwar_id) {
				$state.go('baituan_member', {team:teamwar_id});
            };
            
            
			$scope.gotoFullIntroduce = function() {
				$state.go('activity-fullIntroduce', {
					activity: $scope.activity,
					activityId: $scope.activityId,
					goodsId: $scope.activity.goods_id
				});
			};


			//		报名加入别人的图
			$scope.baituan_joinOther = function(teamwar_id) {
				
//				PintuanModel.baituan_jointeam($scope.activityId, onSuccess, onFailed, onFinal);
			}
			var mTitle = '参与亿七购百团大战活动！';
			var mContent = '参与活动就有可能获得“奖品”';
			var mImgUrls = '';
			var mLink = window.location.href;
                $scope.doShareRecord = function() {
                	mTitle =$scope.activity.price/$scope.activity.peoplenum +'元抢购'+ $scope.activity.goods_title;
					mContent = '我正在参加亿七购百团大战，来一起和我赢取豪华礼品吧';;
                    mImgUrls = $scope.activity.goods_img[0];
                    if (Global.isInweixinBrowser()) {
                        showGuide();
//                      hideWinDialog();
                        weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, onShareSuccess, onShareCancel);
                        weChatJs.wxShareToAppMessage(mTitle,mContent,mLink, mImgUrls, onShareSuccess, onShareCancel)
                        //显示引导分享界面
                    } else if (Global.isInAPP()) {
                        $scope.isShowShare = true;
                    }
                };
			$scope.addPoint = true;
                function toAddPoint() {
                    if (!$scope.addPoint) return;
                    $scope.addPoint = false;
                    userModel.toAddPoint(function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
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
			$scope.hideGuide1 = function(){
                hideGuide();
			}
			function onShareCancel(){
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
				
//				GoodsModel.getGoodsDetail($scope.activityId, onSuccess, onFailed, onFinal);

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
						$scope.activity.isBaiTuan = true;
					}
				})();

				function getGoodsDetail() {
					PintuanModel.baituan_getGoodsImgDetail($scope.activity.goods_id, function onSuccess(response, data) {
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
								$scope.activity.isBaiTuan = true;
							}
						});
				}
			}

			function onSuccess(response, data) {
				if(data.code == 0) {
					$scope.activity = data.data;
					mTitle =$scope.activity.price/$scope.activity.peoplenum +'元抢购'+ $scope.activity.goods_title;
					mContent = '我正在参加亿七购百团大战，来一起和我赢取豪华礼品吧';;
					mLink = window.location.href;
                    mImgUrls = $scope.activity.goods_img[0];
                    setTimeout(function(){
	                    if (Global.isInweixinBrowser()) {
	                        weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, onShareSuccess, onShareCancel);
	                        weChatJs.wxShareToAppMessage(mTitle,mContent,mLink, mImgUrls, onShareSuccess, onShareCancel)
	                    }
                    },1000)
					
					$scope.getFullIntroduce();
					$scope.isLoading = false;
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

		}
	});