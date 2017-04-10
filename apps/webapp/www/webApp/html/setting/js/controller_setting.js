/**
 * Created by Administrator on 2015/11/27.
 */
define([
	'app',
	'models/model_user',
	'models/model_version',
	'html/common/geturl_service',
	'utils/toastUtil',
	'html/common/service_check_version'
], function(app) {

	app.controller('SettingCtrl', ['$scope', '$rootScope', '$ionicPopup', '$state', '$ionicHistory', 'userModel', 'MyUrl', 'ToastUtils', 'checkVersionService',
		function($scope, $rootScope, $ionicPopup, $state, $ionicHistory, userModel, MyUrl, ToastUtils, checkVersionService) {

			//writeTitle('设置');
			$scope.logout = function() {
				$ionicPopup.confirm({
					title: '确定退出？',
					cancelText: '取消',
					cancelType: 'button-default',
					okText: '确定',
					okType: 'button-positive'
				}).then(function(res) {
					if(res) { //logout
						userModel.logout(function() {
							//onLogoutSuccess
							$state.go('tab.mainpage');
							MyUrl.clear();
						}, null);
					} else {
						//cancel logout
					}
				});
			};

			$scope.version = MyUrl.getDefaultParams().v; //当前版本

			$scope.isShowCheckVersion = navigator.userAgent.indexOf('Android') > -1; //安卓客户端才显示版本更新
			$scope.goBack = function() {
				$state.go('tab.account2');
				//					$ionicHistory.goBack();
			};
			/**
			 * 版本检测
			 */
			$scope.checkVersion = function() {
				checkVersionService.checkVersion(true);
			};

		}
	]);

});