/**
 * Created by suiman on 16/1/22.
 */

/**
 * 购物栏组件
 * @name viewBuyFooter
 * @restrict E
 *
 * @description
 * 购物栏组件：
 * 活动无效，可以跳到新一期活动；
 * 活动有效，可以选择参与次数，加入购物车，跳到购物车
 *
 * @param {number} activityId 活动ID
 *
 * @example
 * <view-buy-footer activity-id=1></view-buy-footer>
 *
 */

define(
	[
		'app',
		'models/model_goods',
		'html/trolley/trolley_service',
		'components/view-buy-pop/view_buy_pop',
		'components/view-buy-nature-pop/view_buy_nature_pop'
	],
	function(app) {
		app.directive('viewChoiceFooter', viewChoiceFooter);
		viewChoiceFooter.$inject = ['trolleyInfo', 'GoodsModel', 'MyUrl', 'ToastUtils', '$state', '$ionicPopup']

		function viewChoiceFooter(trolleyInfo, GoodsModel, MyUrl, ToastUtils, $state, $ionicPopup) {

			return {
				restrict: 'E',
				templateUrl: 'webApp/components/view-choice-footer/view_choice_footer.html',
				scope: {
					activity: "="
				},
				link: function postLink(scope, elem, attrs) {
					var unit; //参与单位，一元区为1，十元区为10，二人云购为4
					var goods; //活动商品

					(function init() {
						scope.isSelectionShow = false; //控制选择框显示，默认为不显示

						scope.ready = ready; //如果活动无效，或者网络错误，则不显示购物栏
						scope.showSelection = showSelection; //已登录打开选择框，未登录则跳到登录界面

						scope.addToCart = addToCart; //将商品加入购物车，参与数为参与单位（1/10）
						scope.gotoTrolley = gotoTrolley; //跳到购物车
						scope.gotoNewActivity = gotoNewActivity; //前往新一期活动
						scope.trolleySum = trolleySum; //购物车商品数量

					})();

					function ready() {
						return !!(scope.activity);
					}

					function showSelection() {
						scope.buttonText = '立即购买';
						if(MyUrl.isLogin()) {
							scope.$broadcast('view-buy-pop.show', scope.activity);
						} else {
							$state.go('login', {
								'state': STATUS.LOGIN_ABNORMAL
							});
						}
					}
					scope.gotopintuanApply = function(orderType) {
						//ordertype:  2是参团    3是开团   4是单买

						if(!MyUrl.isLogin()) {
							console.log('去登录');
							$state.go('login', {
								'state': STATUS.LOGIN_ABNORMAL
							});
							return;
						} else {
							scope.$broadcast('view-buy-nature-pop.show', scope.activity);
						}
						//				$state.go('pintuan_apply', {
						//					activityId: $scope.activityId,
						//					orderType: orderType
						//				})
					}

					function addToCart() {
						if(scope.activity.activity_type == 6) {
							var alertPopup = $ionicPopup.alert({
								title: '提示',
								template: '对不起，幸运购商品不能加入购物车',
								buttons: [{
									text: '确定'
								}]
							});
							alertPopup.then(function(res) {

								console.log('Thank you for not eating my delicious ice cream cone');
							});
							return;
						}
						scope.buttonText = '加入购物车';
						if(MyUrl.isLogin()) {
							scope.$broadcast('view-buy-pop.show', scope.activity);
						} else {
							$state.go('login', {
								'state': STATUS.LOGIN_ABNORMAL
							});
						}
					}

					function gotoTrolley() {
						$state.go('trolley');
					}

					function gotoNewActivity() {
						var newId = scope.activity.new_activity_id;
						if(newId) {
							$state.go('activity-goodsDetail', {
								activityId: newId
							});
						} else {
							ToastUtils.showWarning('抱歉，没有新活动');
						}
					}

					function trolleySum() {
						return trolleyInfo.getGoodsInfo().length;
					}

				}
			}

		}
	});