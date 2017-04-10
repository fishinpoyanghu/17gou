/**
 * Created by Administrator on 2016/1/7.
 */
define([
	'app',
	'models/model_app',
	'html/common/constants',
	'utils/toastUtil',
	'components/pintuan_view-indiana-record-item/pintuan_view_indiana_record_item',
	'components/view-list-view/view_list_view',
	'components/view-list-item/view_list_item',
	'components/view-buy-pop/view_buy_pop',
	'components/view-buy-number-pop/view_buy_number_pop',
	'html/common/service_user_info'
], function(app) {

	app.controller(
		'PintuanCenterCtrl', ['$timeout', '$ionicTabsDelegate', '$scope', '$stateParams', '$ionicHistory', '$state', 'AppModel', 'userInfo',

			function($timeout, $ionicTabsDelegate, $scope, $stateParams, $ionicHistory, $state, AppModel, userInfo) {
                $scope.uicon = $stateParams.uicon;
                $scope.unick = $stateParams.unick;
                $scope.isIndianaRecordEmpty = false;
                $scope.isWinRecordEmpty = true;
                $scope.isBuyNumShow = false;

                /**
                 * 云购记录
                 */

                $scope.requestUrl = AppModel.getRecordListUrl2();

                $scope.selectTabWithIndex = function(index) {
                    $ionicTabsDelegate.select(3);
                }


                $scope.startToMainPage = function() {
                    $state.go('tab.mainpage');
                }

                //全部
                $scope.requestParamsAll = {
                    uid: null,
                    status: null
                };

                $scope.callbackAll = {
                    setData: function(data) {
//						$ionicTabsDelegate.select(3);
                        $scope.listAll = data;
                    },
                    setEmpty: function(isEmpty) {
                        $scope.isAllEmpty = isEmpty;
                    }
                };

                //待付款
                $scope.requestParamsObligation = {
                    uid: null,
                    status: 1
                };
                $scope.callbackObligation = {
                    setData: function(data) {
                        $scope.listObligation = data;
                    },
                    setEmpty: function(isEmpty) {
                        $scope.isObligationEmpty = isEmpty;
                    }
                };

                //待成团
                $scope.requestParamsGroup = {
                    uid: null,
                    status: 2
                };
                $scope.callbackGroup = {
                    setData: function(data) {
                        $scope.listGroup = data;
                    },
                    setEmpty: function(isEmpty) {
                        $scope.isGroupEmpty = isEmpty;
                    }
                };
                //待发货
                $scope.requestShipped = {
                    uid: null,
                    status: 3
                };
                $scope.callbackShipped = {
                    setData: function(data) {
                        $scope.listShipped = data;
                    },
                    setEmpty: function(isEmpty) {
                        $scope.isShippedEmpty = isEmpty;
                    }
                };
                //待收货
                $scope.requestReceipt = {
                    uid: null,
                    status: 4
                };
                $scope.callbackReceipt = {
                    setData: function(data) {
                        $scope.listReceipt = data;
                    },
                    setEmpty: function(isEmpty) {
                        $scope.isReceiptEmpty = isEmpty;
                    }
                };

                //返回
                $scope.goBack = function() {
                    $state.go('tab.account2');
                    //                  $ionicHistory.goBack();
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

                //判断是那个页面跳转过来的
                $scope.$on('$stateChangeSuccess',function(event, toState, toParams, fromState, fromParams){
                    if (fromState.name=='addressSelect') {
                        $ionicTabsDelegate.select(3);
                    }
                })



            }

		])

});