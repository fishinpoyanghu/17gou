/**
 * Created by Administrator on 2015/12/29.
 */
define([
	'app',
	'models/model_user',
	'html/common/service_user_info',
	'html/common/geturl_service',
	'html/common/constants',
	'utils/toastUtil',
	'html/common/storage',
	'html/thirdParty/thirdparty_wechat',
	'html/common/global_service'
], function(app) {
	app.controller(
		'LoginCtrl', ['$scope', '$ionicHistory', '$state', 'userModel',
			'$stateParams', 'userInfo', 'MyUrl', 'ToastUtils', 'WeChatShare', 'Storage', 'Global',
			function($scope, $ionicHistory, $state, userModel, $stateParams, userInfo, MyUrl, ToastUtils, WeChatShare, Storage, Global) {
				var state = $stateParams.state;
				var inviteCode = $stateParams.invite_code;
				$scope.isLogin = MyUrl.isLogin();
				console.log($scope.isLogin)
				if(inviteCode && inviteCode != null) {
					Global.setInviteCode(inviteCode);
				}
				$scope.account = {
					phoneNumber: '',
					password: ''
				};

				$scope.goBack = function() {
					//下面的一行:没有登录又跳到登录页面,所以导致登录页面这里退回的是登录页面
					//$ionicHistory.goBack();
					//下面是暂时可以解决的
					var fromState = Storage.get('fromState')
					if(fromState == 'tab.account2') {
						$state.go('tab.mainpage');
						console.log(fromState);
					} else {
						$ionicHistory.goBack()
					}

				};

				$scope.checkLoginAbnormal = function() {
					return state === STATUS.LOGIN_ABNORMAL;
				};

				/**
				 * 跳转到找回密码页面
				 */
				$scope.toFindPasswordPage = function() {
					$state.go('findPassword');
				};
				/**
				 * 跳转到注册页面
				 */
				$scope.register = function() {
					$state.go('registerFirst');
				};

				/**
				 * 跳转页面
				 * @param status
				 */
				function startToPage(status) {
					var fromState = Storage.get('fromState')
					var fromParams = Storage.get('fromParams') || {};

					/*if(fromState) {
					    $state.go(fromState,fromParams)
					    console.log(fromState);
					}*/
						console.log(fromState);
					if(fromState == 'activity-goodsDetail') {
						$state.go('activity-goodsDetail', fromParams);
					}
					if(fromState == 'Christmas_Day') {
						$state.go('Christmas_Day');
					} else if(status === STATUS.LOGIN_ABNORMAL) {
						$scope.goBack();
					} else {
						$state.go('tab.mainpage');
					}
				}

				/**
				 * 登录按钮能否点击
				 * @returns {boolean}
				 */
				$scope.isEnableLogin = function() {
					return($scope.account.phoneNumber.length > 0) && ($scope.account.password.length > 0);
				};

				//第三方登录
				$scope.thrityLogin = function(type) {
					if(type == 'wechat') {
						$scope.wechatLogin()
					} else {
						LoginInApp(type);
					}
				}

				function LoginInApp(type) {
					dmwechat.login(type, function(code) {
						if(type == 'sina') {
							sinaLogin(code.access_token, code.open_id)
						} else {
							qqLogin(code.access_token, code.open_id)
						}
					})
				}

				function sinaLogin(access_token, open_id) {
					ToastUtils.showLoading('登录中....');
					userModel.sinaLogin(access_token, open_id, function(response, data) {
						var code = data.code;
						if(0 === code) {
							ToastUtils.showSuccess('登录成功');
							userInfo.saveUserInfo(data.data);
							if(data.data.first == 1) {
								$state.go('loginTransferPage', { sessid: data.data.sessid })
							} else {
								startToPage(state);
							}
						} else {
							ToastUtils.showError('微博登录失败：' + data.msg);
						}
					}, function(response, data) {
						ToastUtils.showError('请检查网络状态，状态码：' + response.status);
					}, function() {
						ToastUtils.hideLoading();
					});
				}

				function qqLogin(access_token, open_id) {
					ToastUtils.showLoading('登录中....');
					userModel.qqLogin(access_token, open_id, function(response, data) {
						var code = data.code;
						if(0 === code) {
							ToastUtils.showSuccess('登录成功');
							userInfo.saveUserInfo(data.data);
							if(data.data.first == 1) {
								$state.go('loginTransferPage', { sessid: data.data.sessid })
							} else {
								startToPage(state);
							}
						} else {
							ToastUtils.showError('qq登录失败：' + data.msg);
						}
					}, function(response, data) {
						ToastUtils.showError('请检查网络状态，状态码：' + response.status);
					}, function() {
						ToastUtils.hideLoading();
					});
				}
				/**
				 * 微信登录
				 */
				$scope.wechatLogin = function() {
					if(ionic.Platform.isWebView()) { //移动端app打开

						dmwechat.login('wechat', function(code) {
							ToastUtils.showLoading('登录中....');
							userModel.weChatLoginFromSDK(code.access_token, code.open_id, function(response, data) {
								var code = data.code;
								if(0 === code) {
									ToastUtils.showSuccess('登录成功');
									userInfo.saveUserInfo(data.data);
									if(data.data.first == 1) {
										$state.go('loginTransferPage', { sessid: data.data.sessid })
									} else {
										startToPage(state);
									}
								} else {
									ToastUtils.showError('微信登录失败：' + data.msg);
								}
							}, function(response, data) {
								ToastUtils.showError('请检查网络状态，状态码：' + response.status);
							}, function() {
								ToastUtils.hideLoading();
							});
						}, function(err) {
							ToastUtils.showError(err);
						})
					} else { //浏览器打开
						userModel.weChatLoginFromBrowser();
					}
				};

				/**
				 * 登录
				 */
				$scope.login = function() {
					function onFail(response) {
						ToastUtils.showError('请检查网络状态，状态码：' + response.status);
					}

					function onSuccess(response) {
						var code = response.data.code;
						var msg = response.data.msg;
						var loginUser = response.data.data;
						switch(code) {
							case 0:
								ToastUtils.showSuccess(msg);
								userInfo.saveUserInfo(loginUser);
								startToPage(state);
								break;

							default:
								//                              ToastUtils.showError(msg);
								ToastUtils.showError('登录失败');
								break;
						}
					}
					var phoneNumber = $scope.account.phoneNumber;
					var password = $scope.account.password;
					userModel.login(phoneNumber, password, onSuccess, onFail);
				};

				//检测是否显示微信登录
				function detectWeChat() {
					if(ionic.Platform.isWebView() && (ionic.Platform.isIOS() || ionic.Platform.isIPad()) && window.DMDevice) {
						DMDevice.checkApp("weixin://", function() {
							$scope.hasWeChat = true;
						}, function() {});

						DMDevice.checkApp("mqq://", function() {
							$scope.hasQQ = true;
						}, function() {});
					} else {
						$scope.hasWeChat = true;
						$scope.hasQQ = true;
					}
				};

				$scope.$on('$ionicView.beforeEnter', function(ev, data) {
					$scope.isLogin = MyUrl.isLogin();

					if($scope.isLogin) {
						$ionicHistory.goBack(-2)
					}
					detectWeChat();
					if(ionic.Platform.isWebView()) {
						$scope.isInApp = true;
					} else {
						$scope.isInApp = false;
					}
				})

				if(ionic.Platform.isWebView()) {
					window.addEventListener('native.keyboardshow', function() {
						document.body.classList.add('keyboard-open');
					});
				};
				$scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
					if(fromState.name == 'tab.account2') {
						console.log(window.location.href.split('#')[0] + '#/tab/mainpage');
						if(!!(window.history && history.pushState)) {
							history.pushState(null, null, window.location.href.split('#')[0] + '#/tab/mainpage');
						}
					}
				});

			}
		])

});