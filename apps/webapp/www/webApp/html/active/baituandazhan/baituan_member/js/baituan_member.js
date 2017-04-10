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
		'html/common/storage',
		'html/thirdParty/thirdparty_wechat_js'
	],
	function(app) {
		"use strict";

		app.controller('baituanMemberCtrl', baituanMemberCtrl);
		baituanMemberCtrl.$inject = ['$scope', '$state', '$stateParams', '$ionicPopup', 'GoodsModel', 'PintuanModel', 'MyUrl', 'ToastUtils', 'userInfo', 'Global', 'Storage', 'weChatJs', '$ionicHistory', '$interval'];

		function baituanMemberCtrl($scope, $state, $stateParams, $ionicPopup, GoodsModel, PintuanModel, MyUrl, ToastUtils, userInfo, Global, Storage, weChatJs, $ionicHistory, $interval) {
			(function init() {
				$scope.tuan_name_arr = []
				$scope.baituan_team = $stateParams.team;
				$scope.tuan_info = [];
				$scope.members = [];
				$scope.luckyPersons = [];

				//				$scope.isLogin = MyUrl.isLogin();
				$scope.isLoadFinished = true; //是否加载结束
				$scope.isLoading = true;
				$scope.refresh = refresh;

			})();

			function loginMember() {
				if(!MyUrl.isLogin()) {
					//		            event.preventDefault();
					$state.go('login', {
						'state': STATUS.LOGIN_ABNORMAL
					});
					ToastUtils.showWarning('请先登录！！');
					return;
				} else {
					//		          	userInfo.requestInfo();
				}
			}
			$scope.gotoMainPage = function() {
				$state.go('tab.mainpage')
			}
			$scope.gotoActivityRule = function(type) {
					$state.go('activityRule', {
						type: type
					});
				}
				//跳转到计算详情页面
			$scope.gotoCountDetail = function() {
				$state.go('baituan_countDetail', {
					activityId: $scope.tuan_info.teamwar_id
				});
			};

			/**
			 * 获取当前用户信息
			 * @returns {*}
			 */
			$scope.getCurrUserInfo = function() {
				return userInfo.getUserInfo();
			};

			function refresh() {
				ToastUtils.showLoading('加载中...');
				//      getAddressList()
				if(MyUrl.isLogin()) {
					userInfo.requestInfo();
				} else {
					loginMember();
				}
				PintuanModel.baituan_getDetail_info($scope.baituan_team, onSuccess, onFailed, onFinal)
			}

			function onSuccess(response, data) {

				if(data.code == 0) {
					$scope.tuan_info = data.data.teamdetial;
					$scope.members = data.data.teamlist;
					$scope.isReload = true;
//					分享
					mTitle =$scope.tuan_info.price/$scope.tuan_info.people_num +'元抢购'+ $scope.tuan_info.title;
					mContent = '我正在参加亿七购百团大战，来一起和我赢取豪华礼品吧';
	                mImgUrls = $scope.tuan_info.main_img;
	                mLink = window.location.href;
					setTimeout(function(){
	                    if (Global.isInweixinBrowser()) {
	                        weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, onShareSuccess, onShareCancel);
	                        weChatJs.wxShareToAppMessage(mTitle,mContent,mLink, mImgUrls, onShareSuccess, onShareCancel)
	                    }
					},1000)
//					end 分享
					$scope.tuan_members_icon = new Array(Number($scope.tuan_info.people_num));
					for(var i = 0; i < $scope.tuan_members_icon.length; i++) {
						$scope.tuan_members_icon[i] = 'img/icon.png';
					}
					$scope.isLoadFinished = false;
					//获取商品信息
					//					GoodsModel.getGoodsDetail($scope.tuan_info.goods_id, onSuccess, onFailed);
					//					通过teamdetail里面的teamleader匹配成员里的团长，
					for(var i = 0, j = 0; i < $scope.members.length; i++, j++) {
						$scope.tuan_name_arr[i] = $scope.members[i].uid;
						if($scope.tuan_info.teamleader == $scope.members[i].uid) {
							$scope.tuan_info.flag == 8 && ($scope.luckyPersons[0] = $scope.members[i]);
							j--;
							continue;
						}
						$scope.tuan_members_icon[j] = $scope.members[i].icon;
					}
					$scope.tuan_info.flag == 8 && ($scope.luckyPersons[1] = data.data.lucky);
					$scope.isLoading = false;
					ToastUtils.hideLoading()
				} else {
					ToastUtils.showError(data.msg);
					$state.go('baituandazhan');
				}
			}
			//			团的回调
			function onFailed(response, data) {
				ToastUtils.hideLoading()
			}

			function onFinal() {
				$scope.$broadcast('scroll.refreshComplete');
				//				console.log('end')
			}
			//			暂时没做
			//			$scope.gotoHisPage = function() {
			//		        var uicon = $scope.activity.lucky_uicon;
			//		        var unick = $scope.activity.lucky_unick;
			//		        var uid = $scope.activity.lucky_uid;
			//		        if(userInfo.getUserInfo().uid === uid){
			//		          $state.go('myIndianaRecord');
			//		        }else{
			//		          $state.go('hispage', {uicon: uicon, unick: unick, uid: uid});
			//		        }
			//		      }
			$scope.baituan_joinTeam = function(teamId) {
				PintuanModel.baituan_jointeam(teamId, onSuccess, onFailed);
				//				参团的回调
				function onSuccess(response, data) {}
			}
			$scope.formatSimple = function(time) {
					//格式化时间
					var fmt = "yyyy-MM-dd hh:mm:ss";
					var day = new Date(parseInt(time*1000));
					var o = {
						"M+": day.getMonth() + 1, //月份 
						"d+": day.getDate(), //日 
						"h+": day.getHours(), //小时 
						"m+": day.getMinutes(), //分 
						"s+": day.getSeconds(), //秒 
						"q+": Math.floor((day.getMonth() + 3) / 3), //季度 
						"S": day.getMilliseconds() //毫秒 
					};
					if(/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (day.getFullYear() + "").substr(4 - RegExp.$1.length));
					for(var k in o)
						if(new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
					return fmt;
				}
				//    分享到给别人参团
			var mTitle = '参与亿七购百团大战活动！';
			var mContent = '我正在参加亿七购百团大战，来一起和我赢取豪华礼品吧';
			var mImgUrls = '';
			var mLink = window.location.href;
			$scope.baituan_doShare = function() {
				mTitle =$scope.tuan_info.price/$scope.tuan_info.people_num +'元抢购'+ $scope.tuan_info.title;
				mContent = '我正在参加亿七购百团大战，来一起和我赢取豪华礼品吧';
                mImgUrls = $scope.tuan_info.main_img;
                mLink = window.location.href;
				if(Global.isInweixinBrowser()) {
					showGuide();
					weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, onShareSuccess, onShareCancel);
					weChatJs.wxShareToAppMessage(mTitle, mContent, mLink, mImgUrls, onShareSuccess, onShareCancel)
						//显示引导分享界面
				} else if(Global.isInAPP()) {
					$scope.isShowShare = true;
				}
			};
			$scope.addPoint = true;
                function toAddPoint() {
                    if (!$scope.addPoint) return;
                    $scope.addPoint = false;
                    userModel.toAddPoint(function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
                            ToastUtils.showShortNow(STATE_STYLE.GOOD, re.msg);
                        } else {

                        }
                    }, function(response, data) {
                        ToastUtils.showMsgWithCode(7, '获取积分失败：' + '状态码：' + response.status);
                    }, function() {
                        $scope.addPoint = true;
                    })
                }
			function onShareSuccess() {
                hideGuide();
                toAddPoint()
			}
			function onShareCancel(){
				hideGuide();
//              ToastUtils.showShortNow(STATE_STYLE.ERROR, '用户已取消分享');
			}
			function showGuide() {
				document.getElementById("guidepop1").style.display = 'block';
			}
			$scope.hideGuide1 = function(){
                hideGuide();
			}
			function hideGuide() {
				document.getElementById("guidepop1").style.display = 'none';
			}
			/**
			 * 追加云购并跳转到订单页面
			 */
			$scope.startToPay = function(teamwar_id) {
				setTimeout(function() {
					var commitData = {
						orderType: 1,
						activity_id: teamwar_id,
						goods_title: $scope.tuan_info.title,
						activity_type: 2,
						//		                need_num:$scope.broad.need_num,
						//		                join_number:$scope.broad.need_num/$scope.tuan_info.need_num,
						num: teamwar_id
							//		                remain_num:$scope.tuan_info.need_num-$scope.tuan_info.user_num
					}
					Storage.set('commitData', [commitData])
					$state.go('pay');
				}, 10)

			};
			/**
			 * 判断元素是否咋数组的方法
			 */
			//      $scope.inArrayus('3001',['3001','3002'])
			$scope.inArrayus = function(str, arr) {
				//      	var str = str||'3001';
				//      	var arr = arr||['3001','3002'];
				for(var i = 0; i < arr.length; i++) {
					if(str == arr[i]) {
						return true;
					}
				}
				return false;
			};
//			倒计时完成
			$scope.timeoutCallback = function() {
				$scope.isReload = false;
	            aaa();
	        };
			$scope.gotoDetail = function(good){
				$state.go('baituan_detail', {
					goods_id: $scope.tuan_info.goods_id
				});
			}
	        function aaa(){
		        var timer = setInterval(function(){
					            if($scope.isReload){
					            	clearInterval(timer)
					            }else{
						            refresh();
					            }
					        },1000);
	        }
			$scope.$on('$ionicView.beforeEnter', function(ev, data) {
				//获取用户信息
				//				userInfo.requestInfo();
				refresh();
				//				PintuanModel.baituan_getDetail_info($scope.baituan_team, onSuccess, onFailed,onFinal)
			})
		}
	});