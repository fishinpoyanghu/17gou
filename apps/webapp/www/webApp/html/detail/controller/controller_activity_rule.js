define([
	'app',
	'utils/toastUtil',
	'html/common/storage',
], function(app) {

	app.controller(
		'activityCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'ToastUtils', 'Storage', '$http', '$sce',
			function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, ToastUtils, Storage, $http, $sce) {
				var type = ['baituan', 'pintuan','public_offer','yungouRuleWeChatCenter','commonQuestionChatCenter','luckyBag','RecommendRegister'];
				console.log($state)
				ToastUtils.showLoading('加载中....');
				$scope.goBack = function() {
					$ionicHistory.goBack();
				};
				console.log($stateParams)
				if($stateParams.type != 1) {
					if($stateParams.type == type[0]) {
						var imgElem = '<img src="./img/baituan_rule.png"/>';
                        $scope.pageTitle='百团玩法介绍';
					}else if ($stateParams.type == type[1]) {
						var imgElem = '<a href="#/pintuan_main_page"><img src="./img/pintuan/pintuan_rule.jpg"/>';
                        $scope.pageTitle='拼团玩法介绍';
					}else if ($stateParams.type == type[2]) {
                        var imgElem = '<img src="./img/public_offer/public_offer_rule.jpg"/>';
                        $scope.pageTitle='消费全返';
                    }else if ($stateParams.type == type[3]) {
                        var imgElem = '<img src="./img/rule/yungouRuleWeChatCenter.png"/>';
                        $scope.pageTitle='一元购玩法规则';
                    }else if ($stateParams.type == type[4]) {
                        var imgElem = '<img src="./img/rule/commonQuestionChatCenter.png"/>';
                        $scope.pageTitle='一元购常见问题';
                    }
                    else if ($stateParams.type == type[5]) {
                        var imgElem = '<img src="./img/rule/luckyBag.jpg"/>';
                        $scope.pageTitle='福袋玩法';
                    }
                    else if ($stateParams.type == type[6]) {
                        var imgElem = '<img data-tap-disabled="true" src="./img/rule/RecommendRegister.png"/>';
                        $scope.pageTitle='邀请注册';
                    }


						$scope.html = imgElem;
						ToastUtils.hideLoading();
						return;
				}
//				if($stateParams.type != 1 && ($stateParams.type == type[0] || $stateParams.type == type[1])) {
//					var imgElem = '<img src="./img/baituan_rule.png"/>'
//					$scope.html = imgElem;
//					ToastUtils.hideLoading();
//					return;
//				}
				var url = baseUrl.replace('apps/webapp/www/', '');
				$http.get(url + 'uploads/other/one.html?t=' + (+new Date())).then(function(re) {
					$scope.html = $sce.trustAsHtml(re.data);
					ToastUtils.hideLoading();
				}, function(err) {
					ToastUtils.hideLoading();
					ToastUtils.showError('加载出错');
				})

			}
		])

});