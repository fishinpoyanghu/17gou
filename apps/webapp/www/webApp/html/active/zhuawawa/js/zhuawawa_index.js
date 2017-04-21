/**
 * Created by songmars on 15/12/29.
 */

define(
	[
		'app',
		'models/model_game',
		'models/model_app',
		'models/model_user',
		'html/common/geturl_service',
		'html/thirdParty/thirdparty_wechat_js',
		'html/common/storage',
		'components/view-text-scoller/view_text_scoller',
		'utils/toastUtil',
		'components/view-buy-fudai/view_buy_fudai'
	],
	function(app) {
		"use strict";

		app.controller('zhuawawaCtrl', zhuawawaCtrl);
		zhuawawaCtrl.$inject = ['$scope', '$state', '$stateParams', '$interval', '$timeout', 'GameModel', 'AppModel', 'userModel', 'MyUrl', 'weChatJs', 'Storage', 'ToastUtils']

		function zhuawawaCtrl($scope, $state, $stateParams, $interval, $timeout, GameModel, AppModel, userModel, MyUrl, weChatJs, Storage, ToastUtils) {
			$scope.notFirstComZhuawawa = Storage.get('notFirstComZhuawawa');

			console.log($scope.notFirstComZhuawawa)

			$scope.awardList = [{}, {}, {}]
			$scope.catchResult = {
				title: '恭喜',
				content: '您获得商品',
				btnText: '下一步',
				display: false
			};
			$scope.popUpBox = {
				title: '恭喜',
				content: '您获得商品',
				btnText1: false,
				btnText2: false,
				display: false
			};
			$scope.config = {
				declineSpeed: 5, //绳子下降速度
				range: 25, //中奖范围
				displayRule: false,
				catchGoodConfig: {
					top: 0,
					left: 0,
					height: 0
				},
				canCatch: true, //可以夹娃娃
				costNum: [0, 1, 5, 10], //不同游戏等级花费的游戏币 等级的是1，中等级是5，高等级是10
				gameLevel: 1 //		游戏等级有三个，低等级，中等级和高等级，分别是1，2，3
			}

			$scope.jsHoldSuccess = null;
			$scope.config.range = document.body.clientWidth * 0.1;
			console.log(document.body.clientWidth)
			

			//抓取之后的动画
			function catchSucAnimate() {
//				return;
				var liHeight = document.querySelector('.jsHoldSuc li').getBoundingClientRect().height;
				$scope.config.catchGoodConfig.height = liHeight;
				var timer = $interval(function() {
					liHeight = document.querySelector('.jsHoldSuc li').getBoundingClientRect().height;
					//把元素变小
					liHeight -= 5;
					document.querySelector('.jsHoldSuc li').style.height = liHeight + 'px';
					console.log('执行')
					if(liHeight <= 50) {
						$interval.cancel(timer);
						console.log('继续执行')
						moveLine();
					}
				}, 16);

				function moveLine(obj) {
					var liLeft = 0,
						liTop = 0;
					var timer = $interval(function() {
						liLeft = liLeft + 7;
						liTop = liTop - 5;

						document.querySelector('.jsHoldSuc li').style.left = liLeft + 'px';
						document.querySelector('.jsHoldSuc li').style.top = liTop + 'px';
					}, 5, 20).then(function() {
						document.querySelector('.jsHoldSuc li').style.left = $scope.config.catchGoodConfig.left + 'px';
						document.querySelector('.jsHoldSuc li').style.top = $scope.config.catchGoodConfig.top + 'px';
						document.querySelector('.jsHoldSuc li').style.height = $scope.config.catchGoodConfig.height + 'px';
						document.querySelector('.jsHoldSuc li').innerHTML = '';
						$scope.catchResult = {
							title: '恭喜',
							content: '恭喜您获得' + $scope.catchGoodInfo.title + $scope.catchGoodInfo.sub_title,
							btnText: '下一步',
							display: true
						};
						if($scope.notFirstComZhuawawa) {
							angular.element(document.getElementById('largeDollList')).addClass('rolling-0-animation');
							document.getElementById('largeDollList').style.left = '0px';
						} else {
							//							document.getElementById('zhuawawa_guide').style.display = "block";

						}
					})
				}
			}

			
			$scope.awardLists = [];
			$scope.awardLists = $scope.awardLists.concat($scope.awardList);
			
			var machineDown = null;
			//抓娃娃
			$scope.startZhuaww = function() {
				if(!$scope.userInfo || !$scope.config.canCatch) {
					return;
				}
				console.log(22)
				if(!gameCurrencyEnoughNum()) {
					$scope.popUpBox.title = '温馨提示';
					$scope.popUpBox.content = '福袋数量不足,请充值';
					$scope.popUpBox.btnText1 = '知道了';
					$scope.popUpBox.btnText2 = '去充值';
					$scope.popUpBox.display = true;
					return;
				}
				$scope.config.canCatch = false;
				machineDown = angular.element(document.getElementById('machine-down'));
				var timer = $interval(function() {
					if(parseFloat(machineDown[0].style.height) >= 90) {
						$interval.cancel(timer);
						$timeout(function() {
							requeryCatch();
						}, 1000)
					} else {
						machineDown[0].style.height = parseFloat(machineDown[0].style.height) + 10 + '%';
					}
				}, $scope.config.declineSpeed)
				angular.element(machineDown.next().children()[1]).addClass('swing-left');
				angular.element(machineDown.next().children()[2]).addClass('swing-right');
				angular.element(document.getElementById('largeDollList'));

				function requeryCatch() {
					var goodList = angular.element(document.getElementById('largeDollList')).children(),
						standardleft = (window.innerWidth - goodList[0].clientWidth) / 2,
						catchSuccess = false;
					angular.forEach(goodList, function(n, i, arr) {
						if(Math.abs(n.getBoundingClientRect().left - standardleft) < $scope.config.range / 2) {
							//							console.log(n.getBoundingClientRect().left)
							strongOpacity('-' + $scope.config.costNum[$scope.config.gameLevel]);
							document.getElementById('machine-down').style.height = '50%';
							document.querySelector('.jsHoldSuc li').innerHTML = n.innerHTML;
							$timeout(function(){
								catchResult(n.dataset.id, n);								
							},500)
							console.log('可能成功');
							catchSuccess = true;
						}
						if(i == arr.length - 1 && !catchSuccess) {
							setCanCatch();
							console.log('失败');
							clipUp();
						}
					})

					function catchResult(game, obj) {
						if(!$scope.notFirstComZhuawawa) {
							angular.forEach($scope.awardList, function(n, i, arr) {
								if(game == n.game_goods_id) {
									$scope.catchGoodInfo = n;
								}
							})
//							document.querySelector('.jsHoldSuc li').innerHTML = obj.innerHTML;
							clipUp();
							document.getElementById('largeDollList').style.left = document.getElementById('largeDollList').getBoundingClientRect().left - 20 + 'px';
							$timeout(function() {
								catchSucAnimate();
							}, 1000)
							return;
						}
						GameModel.catchResult(game, $scope.config.gameLevel, function(response, data) {
//							return;
							if(data.code == 0) {
								var data = data.data;
								$scope.userInfo.lucky_packet = data.packet_num;
								//抓取成功的商品
								//								$scope.jsHoldSuccess = data[0];
								//								zhuanww_left
								if(data.success == 1) {
									$scope.catchGoodInfo = data;
									document.querySelector('.jsHoldSuc li').innerHTML = obj.innerHTML;
									clipUp();
//									angular.element(document.getElementById('machine-down'))[0].style.height = '20%';
									//animate停在当前位置
									document.getElementById('largeDollList').style.left = document.getElementById('largeDollList').getBoundingClientRect().left - 20 + 'px';
									angular.element(document.getElementById('largeDollList')).removeClass('rolling-0-animation');
									$timeout(function() {
										catchSucAnimate();
									}, 1000)
								} else if(data.success == 2) {
									catchFailAnimate();
									$scope.catchResult = {
										title: '恭喜',
										content: '恭喜您获得' + data.give_num + '个福袋',
										btnText: '继续',
										display: true
									};
									strongOpacity('+' + data.give_num)
								} else {
									catchFailAnimate();
									clipUp();
									setCanCatch();
								}
							} else {
								catchFailAnimate();
								ToastUtils.showError(data.msg);
								$scope.closeElem('catchResult')
								setCanCatch();
							}
						}, function(response) {
							catchFailAnimate();
							$scope.closeElem('catchResult')
							ToastUtils.showError('请检查网络状态！');
							setCanCatch();
						}, function() {

						})
					}
				}
			}
			//抓取不成功下落
			function catchFailAnimate(){
//				return
				console.log(document.querySelector('.jsHoldSuc li'));
				var liHeight = document.querySelector('.jsHoldSuc li').getBoundingClientRect().height,
					initialHeight = liHeight,
					maxHieght = document.querySelector('.machine-clip-line').getBoundingClientRect().height-12;
					console.log(document.querySelector('.machine-clip-line').getBoundingClientRect().height-12);
					angular.element(machineDown.next().children()[1]).addClass('swing-left-l');
					angular.element(machineDown.next().children()[2]).addClass('swing-right-l');
				var timerF = $interval(function() {
						liHeight = document.querySelector('.jsHoldSuc li').getBoundingClientRect().height;
						//把元素变小
						liHeight +=2;
						document.querySelector('.jsHoldSuc li').style.height = liHeight + 'px';
					},5,maxHieght/2).then(function(){
						angular.element(machineDown.next().children()[1]).removeClass('swing-left-l');
						angular.element(machineDown.next().children()[2]).removeClass('swing-right-l');
						document.querySelector('.jsHoldSuc li').innerHTML = '';
						document.querySelector('.jsHoldSuc li').style.height = initialHeight + 'px';
					});
			}
			
			
			
			

			
			
			var dolllist = angular.element(document.getElementById('largeDollList')).children();
			$scope.largeDollListHeight = 0;
//设置数值的函数
			//设置抓取这个动作是可以进行的，
			function setCanCatch() {
				$timeout(function() {
					$scope.config.canCatch = true;
					console.log($scope.config.canCatch)
				}, 1000)
			}
			//判断是否够数量的游戏币
			function gameCurrencyEnoughNum() {
				if(!$scope.notFirstComZhuawawa) {
					return true;
				}
				if($scope.userInfo.lucky_packet < $scope.config.costNum[$scope.config.gameLevel]) {
					return false;
				}
				return true;
			}
//end  设置数值的函数
//处理页面事件的函数
			//抓取福袋时减少的动画

			function strongOpacity(changeNum) {
				if(!$scope.notFirstComZhuawawa) {
					return;
				}
				var opacity = angular.element(document.querySelector('.zhuanww_left strong')),
					maxOpacity = 1;
				document.querySelector('.zhuanww_left strong').innerHTML = changeNum;
				opacity.css('opacity', maxOpacity);
				$interval(function() {
					maxOpacity -= 0.1;
					opacity.css('opacity', maxOpacity);
				}, 200, 10)
			}
			//夹子向上
			function clipUp(){
				angular.element(document.getElementById('machine-down'))[0].style.height = '20%',
				angular.element(machineDown.next().children()[1]).removeClass('swing-left');
				angular.element(machineDown.next().children()[2]).removeClass('swing-right');
			}
			//切换玩法等级
			
			$scope.changeShin = function(type, evt) {
				getGoodsList(type);
				angular.element(document.getElementsByClassName('zhuawawa')).attr('id', ['', 'doll_middle', 'doll_high'][type - 1]);
				$scope.config.gameLevel = [1, 2, 3][type - 1];
				$scope.skinArr = [0, 0, 0];
				$scope.skinArr[type - 1] = 1;
				if(!evt) {
					return;
				}
				var me = angular.element(evt.target);
				me.parent().parent().children().removeClass('current');
				me.parent().addClass('current');
			};
			//购买福袋
			$scope.buyFudai = function() {
				//支付页面会对路由进行限制，游戏进行购买福袋这样设置才可以进入支付页面
				Storage.set('newGameBuyFuDai', 'zhuawawa');
				$state.go('chongzhi', {
					'rechargeType': 2
				})
			};
			//页面跳转的函数
			$scope.gotoPage = function(route) {
				$state.go(route)
			}
			//需要显示某个隐藏元素的事件
			$scope.openElem = function(elem) {
				switch(elem) {
					case 'gameRule':
						$scope.config.displayRule = true;
						break;
				}
			}
			//隐藏某个正在显示的元素
			$scope.closeElem = function(elem) {
				setCanCatch();
				switch(elem) {
					case 'catchResult':
						$scope.catchResult.display = false,
							clipUp();
						if(!$scope.notFirstComZhuawawa) {
							Storage.set('notFirstComZhuawawa', true);
							$scope.notFirstComZhuawawa = true;
							angular.element(document.getElementById('largeDollList')).addClass('rolling-0-animation');
						}
					case 'gameRule':
						$scope.config.displayRule = false;
					case 'popUpBox':
						$scope.popUpBox.display = false;
						break;
				}
			}
//end  处理页面事件的函数
//引导页代码
			$scope.jumpOver = function() {
				Storage.set('notFirstComZhuawawa', true);
				$scope.notFirstComZhuawawa = true;
				angular.element(document.getElementById('largeDollList')).addClass('rolling-0-animation');
				document.getElementById('zhuawawa_guide').style.display = "none";
			}
			$scope.demoShow = function() {
				document.getElementById('zhuawawa_guide').style.display = "none";
			}
//end 引导页代码
//请求数据  商品，福袋，公告
				//个人信息
				function getGameCurrency() {
					//获取福袋数量
					userModel.getLoginUserInfo(function(response, data) {
						if(response.data.code == 0) {
							var data = data.data;
							$scope.userInfo = data;
							//						$scope.userInfo.lucky_packet = 5
						} else {
							ToastUtils.showError(data.msg);
						}
					}, function() {
						ToastUtils.showError('请检查网络状态！');
					})
				}
				//请求系统公告
				function getGameNotice() {
					GameModel.getGameNotice(function(response, data) {
						if(data.code == 0) {
							var data = data.data;
							$scope.gameNoticeList = data;
						} else {
							ToastUtils.showError(data.msg);
						}
					}, function(response) {
						ToastUtils.showError('请检查网络状态！');
					}, function() {})
				}
				//根据游戏等级请求商品，type:1/2/3
				function getGoodsList(type) {
					GameModel.getGoodList(type, function(response, data) {
						if(data.code == 0) {
							var data = data.data;
							$scope.awardList = data;
							$scope.awardListInxThree = data.slice(0, 3);
						} else {
							ToastUtils.showError(data.msg);
						}
					}, function(response) {
						ToastUtils.showError('请检查网络状态！');
					}, function() {
						$timeout(function() {
							dolllist = angular.element(document.getElementById('largeDollList')).children();
							$scope.largeDollListHeight = -1 * (dolllist.length - 3) * dolllist[0].offsetWidth + 'px';
							document.getElementById('animateCss').innerHTML = '@-webkit-keyframes rolling-0 {0% {transform: translate3d(' +
								$scope.largeDollListHeight + ', 0, 0);}100% {transform: translate3d(0, 0, 0);}}@keyframes rolling-0 {0% {transform: translate3d(' +
								$scope.largeDollListHeight + ', 0, 0);}100% {transform: translate3d(0, 0, 0)}}';
							if($scope.notFirstComZhuawawa) {
								angular.element(document.getElementById('largeDollList')).addClass('rolling-0-animation');
							}
						}, 100)
					})
				}
//end 请求数据，商品福袋，公告
			$scope.$on('$ionicView.enter', function() {
				getGameCurrency();
				getGameNotice();
				$scope.changeShin(1);		//默认为低等级
			})

		}
	});