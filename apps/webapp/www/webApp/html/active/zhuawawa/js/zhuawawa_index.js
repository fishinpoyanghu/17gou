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
		'components/view-buy-fudai/view_buy_fudai'
	],
	function(app) {
		"use strict";

		app.controller('zhuawawaCtrl', zhuawawaCtrl);
		zhuawawaCtrl.$inject = ['$scope', '$state', '$stateParams', '$interval', '$timeout', 'GameModel', 'AppModel', 'userModel', 'MyUrl', 'weChatJs', 'Storage']

		function zhuawawaCtrl($scope, $state, $stateParams, $interval, $timeout, GameModel, AppModel, userModel, MyUrl, weChatJs, Storage) {
			//调整背景
			//			$scope.skinArr = [1,0,0];
			//				$scope.skinArr = [1,0,0];
			//			$scope.awardList = [
			//				['1000元', '话费卡'],
			//				['18', '幸运豆'],
			//				['500元', '京东E卡'],
			//				['佳能单反 100D'],
			//				['iPhone7 plus128G'],
			//				['15', '幸运豆'],
			//				['500元', '话费卡'],
			//				['iPad mini 4 32G']
			//			];
			$scope.catchResult = {
				title: '恭喜',
				content: '您获得商品',
				btnText: '下一步',
				display: false
			};
			$scope.popUpBox = {
				title: '恭喜',
				content: '您获得商品',
				btnText: '下一步',
				display: false
			};
			$scope.config = {
				declineSpeed: 5, //绳子下降速度
				range: 20, //中奖范围
				displayRule: false,
				costNum: [0, 1, 5, 10], //不同游戏等级花费的游戏币 等级的是1，中等级是5，高等级是10
				gameLevel: 1 //		游戏等级有三个，低等级，中等级和高等级，分别是1，2，3
			}
			$scope.config.range = document.body.clientWidth * 0.05;
			//			console.log($scope.config.range)
			//			console.log(document.body.clientWidth)

			function getGoodsList(type) {
				GameModel.getGoodList(type, function(response, data) {
					if(data.code == 0) {
						var data = data.data;
						$scope.awardList = data;
						$scope.awardListInxThree = data.slice(0,3);
						//						console.log(data)
					}
				}, function(response) {

				}, function() {
					$timeout(function() {
						dolllist = angular.element(document.getElementById('largeDollList')).children();
						$scope.largeDollListHeight = -1 * (dolllist.length-3) * dolllist[0].offsetWidth + 'px';
						//						console.log(angular.element(document.getElementById('largeDollList')).children())
						//						console.log($scope.largeDollListHeight)
					}, 1000)
				})
			}

			function getGameCurrency() {
				//获取福袋数量
				userModel.getLoginUserInfo(function(response, data) {
					var data = data.data;
					if(response.data.code == 0) {
						$scope.userInfo = data;
						//						$scope.userInfo.lucky_packet = 5
					}
				}, function() {
					ToastUtils.showError('请检查网络状态！');
				})
			}
			//判断是否够数量的游戏币
			function gameCurrencyEnoughNum() {

				//				console.log($scope.config.gameLevel)
				//				console.log($scope.config.costNum[$scope.config.gameLevel])
				//				console.log($scope.userInfo.lucky_packet)
				if($scope.userInfo.lucky_packet < $scope.config.costNum[$scope.config.gameLevel]) {
					return false;
				}
				return true;
			}
			$scope.awardLists = [];
			$scope.awardLists = $scope.awardLists.concat($scope.awardList);
			$scope.changeShin = function(type, evt) {
				getGoodsList(type);
				angular.element(document.getElementsByClassName('zhuawawa')).attr('id', ['', 'doll_middle', 'doll_high'][type - 1]);
				$scope.config.gameLevel = [1, 2, 3][type - 1];
				//				console.log($scope.config.gameLevel);
				$scope.skinArr = [0, 0, 0];
				$scope.skinArr[type - 1] = 1;
				if(!evt) {
					return;
				}
				var me = angular.element(evt.target);
				me.parent().parent().children().removeClass('current');
				me.parent().addClass('current');
			};
			$scope.changeShin(1);
			var machineDown = null;
			$scope.startZhuaww = function() {
				if(!$scope.userInfo) {
					return;
				}
				if(!gameCurrencyEnoughNum()) {
					$scope.popUpBox.title = '温馨提示';
					$scope.popUpBox.content = '福袋数量不足';
					$scope.popUpBox.btnText = '确认';
					$scope.popUpBox.display = true;
					return;
				}
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
					//					gameModel.catchResult()
					var goodList = angular.element(document.getElementById('largeDollList')).children(),
						standardleft = (window.innerWidth - goodList[0].clientWidth) / 2,
						catchSuccess = false;
					//					console.log(n.getBoundingClientRect().left)
					//					console.log(standardleft)
					angular.forEach(goodList, function(n, i, arr) {
						if(Math.abs(n.getBoundingClientRect().left - standardleft) < $scope.config.range / 2) {
							catchResult(n.dataset.id)
							console.log('可能成功');
							catchSuccess = true;
//							$scope.catchResult = {
//								title: '恭喜',
//								content: '您获得商品',
//								btnText: '下一步',
//								display: true
//							};
						}
						if(i == arr.length - 1 && !catchSuccess) {
							console.log('失败')
							catchResult(-1)
//							$scope.catchResult = {
//								title: '没关系',
//								content: '没夹中，再接再厉',
//								btnText: '开始游戏',
//								display: true
//							};
						}
					})

					function catchResult(game) {
						GameModel.catchResult(game, $scope.config.gameLevel, function(response, data) {
							if(data.code == 0) {
								var data = data.data;
								console.log($scope.userInfo.lucky_packet = data.packet_num);
								console.log(data.packet_num)
								if(data.success) {
									$scope.catchResult = {
										title: '恭喜',
										content: '恭喜您获得'+data.title,
										btnText: '继续',
										display: true
									};
								} else {
									$scope.catchResult = {
										title: '没关系',
										content: '没夹中，再接再厉',
										btnText: '开始游戏',
										display: true
									};
								}
//								console.log(data.data.success)

							}
						}, function(response) {

						}, function() {

						})
					}
				}
			}
			$scope.buyFudai = function() {
				//				console.log(MyUrl.isLogin())
				if(MyUrl.isLogin()) {
					//					console.log('com')
					$scope.$broadcast('view-buy-fudai.show');
				} else {
					$state.go('login', {
						'state': STATUS.LOGIN_ABNORMAL
					});
				}
			};
			$scope.closeElem = function(elem) {

				switch(elem) {
					case 'catchResult':
						$scope.catchResult.display = false,
							angular.element(document.getElementById('machine-down'))[0].style.height = '20%',
							angular.element(machineDown.next().children()[1]).removeClass('swing-left'),
							angular.element(machineDown.next().children()[2]).removeClass('swing-right');
					case 'gameRule':
						$scope.config.displayRule = false;
					case 'popUpBox':
						$scope.popUpBox.display = false;
						break;
				}
			}

			$scope.openElem = function(elem) {
				switch(elem) {
					case 'gameRule':
						$scope.config.displayRule = true;
						break;
					default:
						break;
				}
			}
			$scope.gotoPage = function(route) {

				$state.go(route)
			}
			var dolllist = angular.element(document.getElementById('largeDollList')).children();
			$scope.largeDollListHeight = 0;
			$timeout(function() {
				dolllist = angular.element(document.getElementById('largeDollList')).children();
				$scope.largeDollListHeight = -1 * (dolllist.length-3) * dolllist[0].offsetWidth + 'px';
				//				console.log(angular.element(document.getElementById('largeDollList')).children())
				//				console.log($scope.largeDollListHeight)
			}, 1000)

			$scope.$on('$ionicView.enter', function() {
//				getGoodsList(1);
				getGameCurrency();
			})

		}
	});