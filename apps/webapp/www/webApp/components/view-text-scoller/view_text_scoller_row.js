/**
 * Created by qinliduan on 16/3/28.
 */

/**
 * 示例
 * <view-text-scoll interval="3000" scoller-height="24" text-list={{test}}></view-text-scoll>   
 */
define(['app'], function(app) {


    app.directive('viewTextScollRow', ['$interval',
        function($interval) {
            return {
                restrict: 'E',
                replace: false,
                scope: {
                    textList: '=',
                },
                templateUrl: function(elem, attr) {
                    var path = "webApp/components/view-text-scoller/";
                    var fileName = "view_text_scoller_row.html";
                    return path + fileName;
                },
                compile: function(tElm, tAttrs, transclude) {
                    return {
                        pre: function preLink(scope, iElement, iAttrs, controller) {

                        },
                        post: function postLink(scope, iElement, iAttrs, controller) {
                            scope.$watch('textList', function(newValue, oldValue) {
                                var texts = scope.texts = newValue;
                                if (angular.isArray(texts) && texts.length !== 0) {
                                    if(newValue.length > 4) {
                                        scope.bottomTexts = [];
                                        for(var i = 0;i < 4;i++) {
                                            scope.bottomTexts.push(newValue[i])
                                        } 
                                    }
                                    var textScoll = new textScoller();
                                    scope.interval = $interval(function() {
                                        textScoll.scoll();
                                    }, iAttrs.interval || 3000)

                                }

                            })
                            scope.height = iAttrs.scollerHeight * iAttrs.scollerRow || '24';
                            var random = "" + (+new Date()) + rnd(1, 10000);
                            scope.textWrapId = 'textWrap_' + random;

                            function textScoller() {
                                this.itemHeight = scope.height;
                                this.currentIndex = 0;
                                this.textWrap = document.getElementById(scope.textWrapId);
                            }
                            textScoller.prototype.scoll = function() {
                                    var texts = scope.texts;
                                    var len = texts.length;
                                    var _self = this;
                                    if (len <= 4) return;
                                    this.currentIndex++;
                                    var index = this.currentIndex  % (len/4);
                                    
                                    // if (index == 0) {
                                    //     this.textWrap.style.transform = 'translate(0, -' + this.itemHeight * (len) + 'px)';
                                    //     this.textWrap.style.webkitTransform = 'translate(0, -' + this.itemHeight * (len) + 'px)';
                                    //     this.textWrap.style.transitionDuration = '500ms';
                                    //     this.textWrap.style.webkitTransitionDuration = '500ms';
                                    //     setTimeout(function() {
                                    //         _self.textWrap.style.transform = 'translate(0, 0)';
                                    //         _self.textWrap.style.webkitTransform = 'translate(0, 0)';
                                    //         _self.textWrap.style.transitionDuration = '0ms';
                                    //         _self.textWrap.style.webkitTransitionDuration = '0ms';
                                    //     }, 500)
                                    // } else {
                                        this.textWrap.style.transform = 'translate(0, -' + this.itemHeight * index + 'px)';
                                        this.textWrap.style.webkitTransform = 'translate(0, -' + this.itemHeight * index + 'px)';
                                        this.textWrap.style.transitionDuration = '500ms';
                                        this.textWrap.style.webkitTransitionDuration = '500ms';
                                    // }

                                }
                                //生成两个数之间的随机数
                            function rnd(n, m) {
                                var random = Math.floor(Math.random() * (m - n + 1) + n);
                                return random;

                            }
                            
                            scope.$on('$destroy', function() {
                                $interval.cancel(scope.interval);
                            });
                        }
                    }


                }
            }
        }
    ]);
});
