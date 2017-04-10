/**
 * Created by Administrator on 2016/1/7.
 */
define([
	'app',
	'models/model_app',
	'html/common/constants',
	'utils/toastUtil',
	'components/view-indiana-record-item/view_indiana_record_item',
	'components/view-list-view/view_list_view',
	'components/view-list-item/view_list_item',
	'components/view-buy-pop/view_buy_pop',
	'components/view-buy-number-pop/view_buy_number_pop',
	'html/common/service_user_info',
], function(app) {

	app.controller(
		'MyPageCtrl', ['$scope', '$stateParams', '$ionicHistory', '$state', 'AppModel', 'userInfo',

			function($scope, $stateParams, $ionicHistory, $state, AppModel, userInfo) {
				$scope.uicon = $stateParams.uicon;
				$scope.unick = $stateParams.unick;
				$scope.isIndianaRecordEmpty = false;
				$scope.isWinRecordEmpty = true;
				$scope.isBuyNumShow = false;

				/**
				 * 云购记录
				 */

				$scope.requestUrl = AppModel.getRecordListUrl();

				$scope.startToMainPage = function() {
					$state.go('tab.mainpage');
				}

				$scope.goBack = function() {
					$state.go('tab.account2');
					//					$ionicHistory.goBack();
				};

				//全部
				$scope.requestParamsAll = {
					uid: null,
					status: null
				};
				$scope.callbackAll = {
					setData: function(data) {
						console.log(data)
						$scope.listAll = data;
					},
					setEmpty: function(isEmpty) {
						$scope.isAllEmpty = isEmpty;
					}
				};

				//正在进行
				$scope.requestParamsProcessing = {
					uid: null,
					status: 3
				};
				$scope.callbackRrocessing = {
					setData: function(data) {
						$scope.listProcessing = data;
					},
					setEmpty: function(isEmpty) {
						$scope.isProcessingEmpty = isEmpty;
					}
				};

				//已经结束
				$scope.requestParamsResulted = {
					uid: null,
					status: 2
				};
				$scope.callbackResulted = {
					setData: function(data) {
						$scope.listResulted = data;
					},
					setEmpty: function(isEmpty) {
						$scope.isResultedEmpty = isEmpty;
					}
				};

				//单个云购记录的回调
				$scope.itemCallBack = {
					showBuyPop: function(activity) {
						$scope.$broadcast('view-buy-pop.show', activity);
					},
					showBuyNum: function(activityId, uid) {
						$scope.$broadcast('view_buy_number_pop.show', {
							compId: '4-2-1',
							activityId: activityId,
							uid: uid
						});
					},
					showWinOne: function(unick, uicon, uid) {
						//$state.go('hispage', {uicon:uicon, unick:unick, uid:uid});

						ssjjLog.log('uid1' + userInfo.getUserInfo().uid);
						ssjjLog.log('uid2' + uid);
						if(userInfo.getUserInfo().uid == $stateParams.uid || userInfo.getUserInfo().uid == uid) {
							$state.go('myIndianaRecord');
						} else {
							$state.go('hispage', {
								uicon: uicon,
								unick: unick,
								uid: uid
							});
						}
					}
				};

			}

		])

});