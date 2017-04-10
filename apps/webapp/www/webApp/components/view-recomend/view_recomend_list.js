/**
 * Created by qinliduan on 16/3/24.
 */

/**
 * 示例
 * <view-recomend type="list" goods-list="{{goodsList}}" title="猜你喜欢" details-url="activity"></view-recomend>
 */
define(['app', 'lib/ng-lazyload', 'components/view-progress/view_progress', ], function(app) {


    app.directive('viewRecomend', ['$ionicScrollDelegate',
        function($ionicScrollDelegate) {
            return {
                restrict: 'E',
                replace: false,
                scope: {
                    goodsList: '@',
                    title: '@',
                    detailsUrl: '@'
                },
                templateUrl: function(elem, attr) {
                    var path = "webApp/components/view-recomend/";
                    var fileName = "view_recomend_" + attr.type + ".html";
                    return path + fileName;
                },
                compile: function(tElm, tAttrs, transclude) {
                    return {
                        pre: function preLink(scope, iElement, iAttrs, controller) {

                        },
                        post: function postLink(scope, iElement, iAttrs, controller) {
                            scope.$watch('goodsList', function(newValue, oldValue) {
                                    scope.recomentGoods = JSON.parse(newValue);
                                })
                                // console.log(scope.recomentGoods)
                                //阻止左右滑动冒泡
                                // scope.recomentGoods = JSON.parse(scope.goodsList);
                            scope.prevenP = function($event) {
                                // $event.preventDefault();
                                $event.stopPropagation();
                            }
                            var startX, startY, endY;
                            iElement.find('ion-scroll').on('touchstart', function(e) {
                                var changedTouches = (e.changedTouches || e.originalEvent.changedTouches)[0];
                                startY = changedTouches.pageY;
                                startX = changedTouches.pageX;
                            })
                            iElement.find('ion-scroll').on('touchmove', function(e) {
                                var changedTouches = (e.changedTouches || e.originalEvent.changedTouches)[0];
                                endY = changedTouches.pageY;
                                var diff = endY - startY;
                                startY = endY;
                                if (Math.abs(changedTouches.pageX - startX) > 10) return;
                                startX = changedTouches.pageX;
                                var mainScroll;
                                if(iAttrs.delegateHandle) {
                                    mainScroll = $ionicScrollDelegate.$getByHandle(iAttrs.delegateHandle)
                                } else {
                                    mainScroll = $ionicScrollDelegate._instances[0];
                                }
                                var position = mainScroll.getScrollPosition();
                                var top = position.top - diff;
                                top = top > 0 ? top : 0;
                                mainScroll.scrollTo(position.left, top);

                            })
                        }
                    }


                }
            }
        }
    ]);
});
