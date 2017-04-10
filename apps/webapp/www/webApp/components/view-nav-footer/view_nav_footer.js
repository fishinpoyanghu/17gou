/**
 * Created by suiman on 16/1/22.
 */

/**
 *
 * @example
 * <view-nav-footer activity-id=1></view-nav-footer>
 *
 */

define(
	[
		'app',
        'models/model_goods'
	],
	function(app) {
		app.directive('viewNavFooter', viewNavFooter);
        viewNavFooter.$inject = ['trolleyInfo', 'GoodsModel', 'MyUrl', 'ToastUtils', '$state', '$ionicPopup']

		function viewNavFooter(trolleyInfo, GoodsModel, MyUrl, ToastUtils, $state, $ionicPopup) {

			return {
				restrict: 'E',
				templateUrl: 'webApp/components/view-nav-footer/view_nav_footer.html',
				scope: {
					activity: "="
				},
				link: function postLink(scope, elem, attrs) {

				}

			}

		}
	});