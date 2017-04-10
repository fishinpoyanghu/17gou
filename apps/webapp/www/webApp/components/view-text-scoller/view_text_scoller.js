/**
 * Created by qinliduan on 16/3/28.
 */

/**
 * 示例
 * <view-text-scoll interval="3000" scoller-height="24" text-list={{test}}></view-text-scoll>   
 */
define(['app'], function(app) {


    app.directive('viewTextScoll', ['$interval',
        function($interval) {
            return {
                restrict: 'E',
                replace: false,
                scope: {
                    textList: '@',
                },
                templateUrl: function(elem, attr) {
                    var path = "webApp/components/view-text-scoller/";
                    var fileName = "view_text_scoller.html";
                    return path + fileName;
                },
                compile: function(tElm, tAttrs, transclude) {
                    return {
                        pre: function preLink(scope, iElement, iAttrs, controller) {

                        },
                        post: function postLink(scope, iElement, iAttrs, controller) {
                            scope.$watch('textList', function(newValue, oldValue) {
                                newValue = newValue || JSON.stringify([]);
                                var texts = scope.texts = JSON.parse(newValue);
                                if (angular.isArray(texts) && texts.length !== 0) {
                                    var textScoll = new textScoller();
                                    scope.interval = $interval(function() {
                                        textScoll.scoll();
                                    }, iAttrs.interval || 3000)

                                }

                            })
                            scope.height = iAttrs.scollerHeight || '24';
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
                                    if (len <= 1) return;
                                    this.currentIndex++;
                                    var index = this.currentIndex % len;
                                    if (index == 0) {
                                        this.textWrap.style.transform = 'translate(0, -' + this.itemHeight * (len) + 'px)';
                                        this.textWrap.style.webkitTransform = 'translate(0, -' + this.itemHeight * (len) + 'px)';
                                        this.textWrap.style.transitionDuration = '500ms';
                                        this.textWrap.style.webkitTransitionDuration = '500ms';
                                        setTimeout(function() {
                                            _self.textWrap.style.transform = 'translate(0, 0)';
                                            _self.textWrap.style.webkitTransform = 'translate(0, 0)';
                                            _self.textWrap.style.transitionDuration = '0ms';
                                            _self.textWrap.style.webkitTransitionDuration = '0ms';
                                        }, 500)
                                    } else {
                                        this.textWrap.style.transform = 'translate(0, -' + this.itemHeight * index + 'px)';
                                        this.textWrap.style.webkitTransform = 'translate(0, -' + this.itemHeight * index + 'px)';
                                        this.textWrap.style.transitionDuration = '500ms';
                                        this.textWrap.style.webkitTransitionDuration = '500ms';
                                    }

                                }
                                //生成两个数之间的随机数
                            function rnd(n, m) {
                                var random = Math.floor(Math.random() * (m - n + 1) + n);
                                return random;

                            }
                            scope.dataFormatGap = function(date) {
                                var nowTime = Math.round(new Date().getTime() / 1000);
                                var diffTime = nowTime - date;
                                var day = Math.floor(diffTime / (24 * 60 * 60));
                                if (day > 0) return day + '天前';
                                diffTime = diffTime - day * (24 * 60 * 60);
                                var hour = Math.floor(diffTime / (60 * 60));
                                if (hour > 0) return hour + '小时前';
                                diffTime = diffTime - hour * (60 * 60);
                                var minute = Math.floor(diffTime / 60);
                                if (minute > 0) return minute + '分前';
                                return '刚刚';
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
