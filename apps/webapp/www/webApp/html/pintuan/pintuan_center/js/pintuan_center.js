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
		'models/model_address',
		'html/common/service_user_info',
		'html/common/geturl_service',
		'html/common/global_service',
		'html/thirdParty/thirdparty_wechat_js'
	],
	function(app) {
		"use strict";

		app.controller('pintuanCenterCtrl', pintuanCenterCtrl);
		pintuanCenterCtrl.$inject = ['$scope', '$state', '$stateParams', '$ionicPopup', 'GoodsModel','PintuanModel', 'addressModel', 'MyUrl', 'ToastUtils', 'userInfo', 'Global', 'weChatJs', '$ionicHistory'];

		function pintuanCenterCtrl($scope, $state, $stateParams, $ionicPopup, GoodsModel,PintuanModel, addressModel, MyUrl, ToastUtils, userInfo, Global, weChatJs, $ionicHistory) {
			
			(function init() {
				$scope.pintuan_team = $stateParams.team;

				$scope.isLogin = MyUrl.isLogin();
				$scope.isDisplayMe = {
					open_tuan: true,
					will_pay: true,
					fail: true,
					success: true
				};
				$scope.refresh = refresh;
				refresh();
			})();
			
			function refresh() {
				ToastUtils.showLoading('加载中...');
				//      getAddressList()
				PintuanModel.pintuan_myTeam_info('', onSuccess, onFailed,onFinal)
			}
			function onSuccess(response, data) {
				$scope.tuans = [];
				/**
			       * yu
			       * @flag 1  默认未成团			未成团
			       * @flag 7  人数已满，正在揭晓		成功
			       * @flag 8  人数已满，揭晓成功		成功
			       * @flag 5  付款由于某种原因失败		代付款
			       * @flag 2  人数不够自动结束		失败
			       * @flag 3  后台手动结束下架		失败
			    */
				if(data.code == 0) {
					$scope.tuans = data.data;
					for(var i = 0; i < $scope.tuans.length; i++){
						switch ($scope.tuans[i].flag){
							case '1':
									$scope.isDisplayMe.open_tuan = false;
								break;
							case '2':
							case '3':
									$scope.isDisplayMe.fail = false;
								break;
							case '5':
									$scope.isDisplayMe.will_pay = false;
								break;
							case '7':
							case '8':
									$scope.isDisplayMe.success = false;
								break;
						}
					}
				}
			}
//			团的回调
			function onFailed(response, data) {
				if(response.status !== 200) {
					ToastUtils.showError('请检查网络');
				}
			}
			function onFinal(){
				ToastUtils.hideLoading();
			}
			
			
			$scope.getHisMumber = function (pintuan_id, uid) {
				$state.go('pintuan_member', {team:pintuan_id});
            };
            //格式化时间
			$scope.formatTime = function (time) {
				var fmt = "yyyy-MM-dd hh:mm:ss";
				var day = new Date(time);
				 var o = {
			        "M+": day.getMonth() + 1, //月份 
			        "d+": day.getDate(), //日 
			        "h+": day.getHours(), //小时 
			        "m+": day.getMinutes(), //分 
			        "s+": day.getSeconds(), //秒 
			        "q+": Math.floor((day.getMonth() + 3) / 3), //季度 
			        "S": day.getMilliseconds() //毫秒 
			    };
			    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (day.getFullYear() + "").substr(4 - RegExp.$1.length));
			    for (var k in o)
			    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
			    return fmt;
            };
			$scope.gotoDetail = function (good_id) {
//          	$state.go('activity-goodsDetail', {activityId:good_id});
          	}
			$scope.startTopintuanPage = function(){
				$state.go('pintuan_main_page');
			}
			$scope.swipe = function(direction){
				var class_name = 'tab-item';
				var class_name_active = 'tab-item tab-item-active';
				var ion_content = document.getElementById('pintuan_center');
				var ion_tab_nav = ion_content.querySelectorAll('ion-tabs div.tab-nav')[0];
				var ion_tab_nav_item = ion_tab_nav.getElementsByTagName('a');
				var ion_tab_nav_item_active = ion_tab_nav.getElementsByClassName('tab-item-active')[0];
				var ion_tab_content = ion_content.querySelectorAll('ion-tabs div.tab-content')[0];
				
				if (direction) {
					if (ion_tab_nav_item_active == ion_tab_nav_item[ion_tab_nav_item.length-1]) {
						ion_tab_nav_item[0].className = class_name_active;
					}else{
						ion_tab_nav_item_active.nextElementSibling.className = class_name_active;
					}
				}else{
					if (ion_tab_nav_item_active == ion_tab_nav_item[0]) {
						ion_tab_nav_item[ion_tab_nav_item.length-1].className = class_name_active;
					}else{
						ion_tab_nav_item_active.previousElementSibling.className = class_name_active;
					}
				}
				ion_tab_nav_item_active.className = class_name;
				console.log(ion_tab_nav_item_active.classList)
				
				console.log(direction)
			}
			$scope.$on('$ionicView.beforeEnter', function() {
            	PintuanModel.pintuan_myTeam_info('', onSuccess, onFailed,onFinal)
          	})
			$scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
                    
            });
		}
	});