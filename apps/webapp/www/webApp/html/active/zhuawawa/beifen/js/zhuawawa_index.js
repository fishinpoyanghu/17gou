/**
 * Created by songmars on 15/12/29.
 */

define(
	[
		'app',
		'models/model_goods',
		'models/model_app',
		'models/model_user',
		'models/public_function',
		'html/common/geturl_service',
		'html/common/global_service',
		'models/model_invite',
		'utils/httpRequest',
		'html/thirdParty/thirdparty_wechat_js',
		'html/common/storage'
	],
	function(app) {
		"use strict";

		app.controller('zhuawawaCtrl', zhuawawaCtrl);
		zhuawawaCtrl.$inject = ['$scope', '$state', '$stateParams']

		function zhuawawaCtrl($scope, $state, $stateParams) {
			//调整背景
//			$scope.skinArr = [1,0,0];
			(function(){
//				$scope.skinArr = [1,0,0];
			})
			$scope.changeShin = function(type,evt){
				$scope.skinArr = [0,0,0];
				$scope.skinArr[type-1]=1;
				console.log($scope.skinArr);
				if (!evt) {
					return;
				}
				var me = angular.element(evt.target);
				me.parent().parent().children().removeClass('current');
				me.parent().addClass('current');
			};
			$scope.changeShin(1);
			
			
			
		}
	});