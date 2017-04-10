/**
 * 获奖记录
 * Created by Administrator on 2016/1/7.
 */
define([
	'app',
	'models/model_user',
	'utils/toastUtil',
	'html/common/storage'
], function(app) {

	app.controller(
		'myBalanceDetailCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel', 'ToastUtils', 'Storage', '$q',
			function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel, ToastUtils, Storage, $q) {
				var sessId = Storage.get("sessId");
				$scope.goBack = function() {
					$ionicHistory.goBack();
				};

				//去提现页面
				$scope.goToApply_withdraw_cash = function() {
					$state.go('apply_withdraw_cash');
				}

			}
		])

});