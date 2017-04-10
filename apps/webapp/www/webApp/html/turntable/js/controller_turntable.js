/**
 * 获奖记录
 * Created by Administrator on 2016/1/7.
 */
define([
	'app',
	'models/model_user',
	'utils/toastUtil',
	'html/common/storage',
	'html/trolley/trolley_service',
	'components/view-turnplate/view_turnplate',
	'components/view-text-scoller/view_text_scoller_row',
	'models/model_goods',
	'models/model_user',
	'html/common/global_service',
	'html/common/service_user_info',
	'html/common/geturl_service',
	'html/thirdParty/thirdparty_wechat_js',
], function(app) {

	app.controller(
		'turntableCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel', 'ToastUtils', 'Storage', '$ionicPopup', '$ionicPopup', 'Global', 'weChatJs',
			function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel, ToastUtils, Storage, $ionicPopup, $ionicPopup, Global, weChatJs) {

				var loginmsg = Storage.get('first_login');
				if(loginmsg) {
					//已经登入过
					$scope.model = false;
				} else {
					Storage.set('first_login', 1);
					$scope.model = true;
					$scope.close_model = function() {
						$scope.model = false;
					}
				}

				/*var invite_code_share=$stateParams.invite_code_share;
				if(invite_code_share){
				    Storage.set('invite_code_share',invite_code_share);
				}

				function gowxlogin(){
				    var msg='';
				    var invite_code_share =Storage.get('invite_code_share');
				    if(Storage.get('invite_code_share')){
				        msg={'invite_code':invite_code_share};
				    }
				    userModel.weChatLoginFromBrowser(msg);
				}*/

				//分享到朋友圈或者朋友
				var mTitle = '加关注后才可获得一次免费抽奖机会，再分享才有一次抽奖机会，快来试试吧!';
				var mImgUrls = baseUrl + 'img/turnplate_img.png';
				var mLink = baseUrl + '#/turntable';
				var mContent = '加关注后才可获得一次免费抽奖机会，再分享才有一次抽奖机会，快来试试吧!';

				/*var invite_code=Storage.get('yiqigou_invite_code');
				if(invite_code){
				    mLink=mLink+'/'+invite_code;
				}*/

				$timeout(function() {
					if(Global.isInweixinBrowser()) {
						weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, function() {
							//ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功!!!');
							$scope.opportunity = 1;

						}, function() {});
						weChatJs.wxShareToAppMessage(mTitle, mContent, mLink, mImgUrls, function() {
							// ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功!!');
							$scope.opportunity = 1;
						}, function() {});

					}
					//由于微信分享会被其他js数据重新加载影响。所以在此处写了定时器。
				}, 3000);

				$timeout(function() {
					if(Global.isInweixinBrowser()) {
						weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, function() {
							//ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功！！！！！');
							$scope.opportunity = 1;

						}, function() {});
						weChatJs.wxShareToAppMessage(mTitle, mContent, mLink, mImgUrls, function() {
							//  ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功!!');
							$scope.opportunity = 1;
						}, function() {});

					}
					//由于微信分享会被其他js数据重新加载影响。所以在此处写了定时器。
				}, 2000);

				$scope.opportunity = 0; //抽奖机会为0

				//分享成功后的弹框
				/*    function getsuc_box() {
				        if(Global.isInweixinBrowser()) {
				            weChatJs.wxShareToTimeline(mTitle, mLink , mImgUrls, function() {
				                // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
				            }, function(){});
				            weChatJs.wxShareToAppMessage(mTitle,mContent, mLink , mImgUrls, function() {
				                // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
				            }, function(){});

				        }
				        $scope.opportunity=1;
				        console.log($scope.opportunity);
				        $ionicPopup.alert({
				            title: '温馨提示',
				            template: "<div class='sweet-alert3' ng-hide='alert_suc2'><div class='line tip'></div><div class='line long'></div><div class='placeholder'></div><div class='fix'></div></div><div style='color:red;'>用户您得了一次抽奖机会！</div>",
				            okText: '确定',
				            okType: '确定',
				        }).then(function (res) {
				            console.log('123'); 
				            $scope.opportunity=1;//若有分享的话，抽奖机会就给1次

				        })
				    }*/

				//加关注

				var sessId = Storage.get("sessId");
				$scope.goBack = function() {
					$state.go('tab.account2');
//					$ionicHistory.goBack();
				};

				getLottery();

				function getLottery() {
					if(Global.isInweixinBrowser()) {
						weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, function() {
							// ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
							$scope.opportunity = 1;
						}, function() {});
						weChatJs.wxShareToAppMessage(mTitle, mContent, mLink, mImgUrls, function() {
							// ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
							$scope.opportunity = 1;
						}, function() {});

					}
					ToastUtils.showLoading('加载中....');
					userModel.getLottery(function(xhr, re) {
						var code = re.code;
						if(code == 0) {
							var data = re.data.data;

							if(re.data.point) $scope.needPoint = re.data.point;
							var restaraunts = [];
							if(data) {
								$scope.lottery = data;

								for(var i = 0, len = data.length; i < len; i++) {
									restaraunts.push(data[i].name);
								}
								$scope.turnplate = {
									restaraunts: restaraunts, //大转盘奖品名称
								};
							} else {
								$scope.lottery = [];
								$scope.turnplate = {
									restaraunts: restaraunts, //大转盘奖品名称
								};
							}

						} else {
							ToastUtils.showMsgWithCode(code, re.msg);
						}
					}, function(response, data) {
						lotteryNone
//						ToastUtils.showMsgWithCode(7, '获取奖品列表失败：' + '状态码：' + response.status);
					}, function() {
						ToastUtils.hideLoading();
						$scope.$broadcast('scroll.refreshComplete');
					})
				}

				//判断用户是否分享
				/*            function share_turntable(){
				                $ionicPopup.confirm({
				                    title: '温馨提示',
				                    template:"<div class='sweet-alert2' ng-hide='alert_err'><div class='body'></div><div class='dot'></div></div><div style='color:red;'>用户您好，先分享后可获得一次抽奖机会！</div>",
				                    cancelText : '取消',
				                    cancelType : 'button-default',
				                    okText : '确定',
				                    okType : 'button-positive'
				                }).then(function(res) {
				                    if(res) {//logout
				                        if(Global.isInweixinBrowser()) {
				                            weChatJs.wxShareToTimeline(mTitle, mLink , mImgUrls, function() {
				                                // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
				                                //若用户分享成功就点击开始抽奖。
				                                getsuc_box();
				                                console.log("aaa");
				                            }, function(){});
				                            weChatJs.wxShareToAppMessage(mTitle,mContent, mLink , mImgUrls, function() {
				                                // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
				                                //若用户分享成功就点击开始抽奖。
				                                getsuc_box();
				                                console.log("aaa");
				                            }, function(){});

				                        }
				                    } else {
				                        //cancel logout
				                    }
				                });
				            }*/

				//监听抽奖启动信号
				$scope.$on('turnplate.startNow', function(scope, compId) {
					if(Global.isInweixinBrowser()) {
						weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, function() {
							// ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
						}, function() {});
						weChatJs.wxShareToAppMessage(mTitle, mContent, mLink, mImgUrls, function() {
							// ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
						}, function() {});

					}
					if('9-1-1' == compId) {
						/* $ionicPopup.confirm({
						         title: '<b>抽奖活动尚未开始，敬请期待~</b>',
						        
						         buttons: [{
						             text: '确定',
						             onTap: function(e) {
						                 return false;
						             }
						         }]
						     });
						   return false;*/
						getLotteryRun();
						/*     if($scope.lottery.length > 0 && ($scope.needPoint || $scope.needPoint == 0) ) {
						         $ionicPopup.confirm({
						             title: '<div>抽奖需要扣除' + $scope.needPoint + '积分</div><div>确定抽奖？</div>',
						             cancelText: '取消',
						             cancelType: 'button-default',
						             okText: '确定',
						             okType: 'button-assertive'
						         }).then(function(res) {
						             if (res) {
						                 //启动转盘
						                 getLotteryRun();
						             } else {

						             }
						         })
						         if($scope.opportunity>=1){//抽奖机会有一次，就可以点击开始抽奖
						             getLotteryRun();
						             $scope.opportunity--;
						         }
						         else if($scope.opportunity<1){//抽奖机会没了，就弹出是否分享再获得一次抽奖机会。
						             share_turntable();
						         }

						     } else {
						         $ionicPopup.confirm({
						             title: '<b>抽奖活动尚未开始，敬请期待~</b>',
						             scope: $scope,
						             buttons: [{
						                 text: '确定',
						                 onTap: function(e) {
						                     return false;
						                 }
						             }]
						         });
						     }*/

					}
				});

				function getLotteryRun() {
					if(Global.isInweixinBrowser()) {
						weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, function() {
							// ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
						}, function() {});
						weChatJs.wxShareToAppMessage(mTitle, mContent, mLink, mImgUrls, function() {
							// ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
						}, function() {});

					}
					var params = {};
					if($scope.opportunity == 1) {
						var params = {
							"share": 1
						};
					}
					userModel.getLotteryRun(params, function(xhr, re) {
						var code = re.code;
						if(code == 0) {
							var lotteryInfo = getLotteryInfo(re.data.id);
							if(lotteryInfo) {
								$scope.$broadcast('turnplate.start', '9-1-1', lotteryInfo.index, function() {
									if(lotteryInfo.name.indexOf('谢谢') > -1) {
										lotteryNone(1)
									} else {
											lotteryNone(0,code,lotteryInfo.name)
//											lotteryNone(1)
										// $scope.doRefresh()
										getLotteryList();
									}

								});
							} else {
								//找不到指定奖品处理
								lotteryNone(1);
								//                              ToastUtils.showMsgWithCode(7, '出现未知错误');
							}

						} else {
							lotteryNone(1,code,re.msg);
//							ToastUtils.showMsgWithCode(code, re.msg);
						}
					}, function(response, data) {
						lotteryNone(1)
//						ToastUtils.showMsgWithCode(7, '抽奖出现错误：' + '状态码：' + response.status);
					}, null)
				}
				$scope.$on('$stateChangeStart',
					function(event, toState, toParams, fromState, fromParams){
						if ( $scope.alertPopup) {
					    	$scope.alertPopup.close();
						}
				})
				function lotteryNone(result,code,name) {
//					result=0,表示获得奖品.1表示没获得奖品 
					var templateValue = [];
					name === undefined && (name=''); 
					templateValue[0] = '<a href="#/tab/mainpage"><img src="img/turn_result_ok.png"/></a>';
					templateValue[1] = '<a href="#/tab/mainpage"><img src="img/turn_result_none.png"/></a>';
					
//					templateValue[0] = '恭喜您获得' + '<span style="color:#f72331">' +name + '</span>';
//					templateValue[1] = '您的抽奖次数已用完';
					$scope.alertPopup = $ionicPopup.show({
						template:'<div id="turnTableResult">' 
									+ templateValue[result] 
									+ '<span>' + name+'</span>' 
								  +'</div>'

					});
					var timer = $timeout(function(){
						var trunTableResult = document.getElementById('turnTableResult'),
							popupBody = trunTableResult.parentElement,
							popup = popupBody.parentElement,
							span = trunTableResult.querySelector('span'),
							popupheader = popup.getElementsByClassName('popup-head')[0];
						popupheader.style.display = 'none';
						popup.style.height = '400px';
						popup.style.width = '400px';
						popup.style.backgroundColor = 'transparent';
						/*返回用户不是微信账户的错误*/
						if(code == 1){
							span.style.top = '69%';
							span.style.left = '18%';
							span.style.width = '60%';
						}
					},10)
				}

				//获取用户是否关注公众号
				function getwxmsg() {
					userModel.getwxmsg(function(xhr, re) {
						var code = re.code;
						if(code == 0) {
							console.log(222);

						} else {
							ToastUtils.showMsgWithCode(code, re.msg);
						}
					}, function(response, data) {
						ToastUtils.showMsgWithCode(7, '关注出现错误：' + '状态码：' + response.status);
					})
				}

				function getLotteryInfo(id) {
					var lottery = $scope.lottery;
					for(var i = 0, len = lottery.length; i < len; i++) {
						if(id == lottery[i].id) {
							return {
								index: i,
								name: lottery[i].name,
								id: id
							}
						}
					}

				}

				$scope.lotteryListData = [];
				$scope.isLoadFinished = true;

				getLotteryList()

				function getLotteryList() {
					if(!$scope.isLoadFinished) return;
					$scope.isLoadFinished = false;
					$scope.lotteryListData = [];
					userModel.getLotteryList_1(1, 40, function(xhr, re) {
						var code = re.code;
						if(code == 0) {
							var data = re.data;
							$scope.lotteryListData = data;
						} else {
							ToastUtils.showMsgWithCode(code, re.msg);
						}
					}, function(response, data) {
						ToastUtils.showMsgWithCode(7, '获取奖品列表失败：' + '状态码：' + response.status);
					}, function() {
						$scope.isLoadFinished = true;
					})
				}
				
				$scope.doRefresh = function() {
					$scope.isLoadFinished = true;

					getLottery();
					getLotteryList()
				}

				$scope.$on('$destroy', function() {
					if($scope.getLotteryTimeout) $timeout.cancel($scope.getLotteryTimeout);
				});

			}
		])

});