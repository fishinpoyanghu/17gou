/**
 * Created by suiman on 15/12/30.
 */

define(
  [
    'app',
    'components/view-countdown/view_countdown',
    'lib/ng-lazyload',
    'components/view-progress/view_progress',

  ],
  function (app) {
    //var app = require('app');

    app.directive('viewPublishItem', viewPublishItem);
    viewPublishItem.$inject = [];
    function viewPublishItem() {
      return {
        restrict: 'E',
        templateUrl: 'webApp/components/view-publish-item/view_publish_item.html',
        scope: {
          activity: '='
        },
        controller: function ($scope, $state) {
        	var targetState = ['activity-goodsDetail',]
        	if ($scope.activity.flag == 7 || $scope.activity.flag ==8) {
        		$scope.gotoDetail = function() {
                	$state.go('baituan_member', { team: $scope.activity.activity_id});
            	};
        	}else{
		        $scope.gotoDetail = function () {
		            $state.go('activity-goodsDetail', {activityId:$scope.activity.activity_id});
		        }
        	}
        },
        compile: function () {
          return {
            pre: function preLink(scope, iElem, iAttrs) {

            },
            post: function postLink(scope, iElem, iAttrs) {

              scope.timeoutCallback = function () {
                scope.$emit('view_countdown.timeout', {activityId: scope.activity.activity_id});
              }
            }
          }
        },

      }
    }


  })

