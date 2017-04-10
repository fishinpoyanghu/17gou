/**
 * Created by qinliduan on 16/3/25.
 */

/**
 * 示例（注意：remain-time是以秒为单位）
 * <p>倒计时：<em view-countdown remain-time="{{remain_time}}"></em></p>
 */

define(
    ['app', 'models/model_red_packet', 'utils/toastUtil'],
    function(app) {
        app
            .directive('dmCounter', ['$interval', 'redPacketModel', '$timeout', 'ToastUtils', function($interval, redPacketModel, $timeout, ToastUtils) {
                return {
                    scope: {
                        remainTime: '@'
                    },
                    restrict: 'EAC',
                    replace: false,
                    template: '<div class="dm-counter-time">' +
                        '<span class="m">{{minute}}</span>' +
                        '<span class="dot">:</span>' +
                        '<span class="s">{{second}}</span>' +
                        '<span class="dot">:</span>' +
                        '<span class="d">{{ms}}</span>' +
                        '</div>',
                    link: function(scope, iElm, iAttrs, controller) {
                        function init() {
                            scope.day = '00';
                            scope.hour = '00';
                            scope.minute = '00';
                            scope.second = '00';
                            scope.ms = '00';
                        }
                        var parent = iElm.parent();
                        scope.caculateTime = function(enddate) {
                            var nowTime = Math.round(new Date().getTime());
                            var diffTime = enddate - nowTime;
                            if (diffTime < 0) {
                                parent.empty();
                                parent.append(angular.element('<div class="announce-title">揭晓中，请稍后...</div>'))
                                angular.isDefined(scope.interval) && $interval.cancel(scope.interval);
                                getResult(parent, iAttrs.activityId)
                                return;
                            }
                            var day  = Math.floor(diffTime / (24 * 60 * 60*1000));
                            diffTime = diffTime - day * (24 * 60 * 60*1000);
                            scope.day = addZero(day)
                            var hour  = Math.floor(diffTime / (60 * 60*1000));
                            diffTime = diffTime - hour * (60 * 60*1000);
                            scope.hour = addZero(hour)
                            var minute  = Math.floor(diffTime / (60*1000));
                            diffTime = diffTime - minute * (60 * 1000);
                            scope.minute = addZero(minute)
                            var second  = Math.floor(diffTime / 1000);
                            diffTime = diffTime - second *  1000;
                            scope.second = addZero(second)
                            scope.ms = addZero(Math.floor(diffTime/10))
                            if(Number(scope.ms) > 100) {
                                console.log(scope.ms)
                                console.log(diffTime)
                            }
                            
                        }
                        init();
                        scope.$watch('remainTime', function(newValue, oldValue) {
                            if (newValue == '') return;
                            var endTime = Math.round(new Date().getTime()) + (Number(newValue) * 1000)
                            scope.caculateTime(endTime)
                            scope.interval = $interval(function() {
                                scope.caculateTime(endTime)
                            }, 10)
                        })

                        function getResult(dom, activity_id) {
                            redPacketModel.getJoinResult(activity_id, function(xhr, re) {
                                var code = re.code;
                                if (code == 0) {
                                    angular.isDefined(scope.timeout) && $timeout.cancel(scope.timeout);
                                    scope.timeout = null;
                                    dom.empty();
                                    dom.append(angular.element('<div class="announce-title">已揭晓，幸运号：' + re.data.result_num + '</div>'))
                                } else if (code == 2) {
                                    scope.timeout = $timeout(function() {
                                        getResult(dom, activity_id)
                                    }, 1000)
                                } else {
                                    ToastUtils.showMsgWithCode(code, re.msg);
                                }
                            }, function(response, data) {
                                ToastUtils.showMsgWithCode(7, '获取红包结果失败;' + '状态码：' + response.status);
                            }, null)
                        }

                        function addZero(num) {
                            if(num<10) {
                              return ('0' + num)
                            }else {
                              return ('' + num);
                            }
                        }
                        scope.$on('$destroy', function() {
                            $interval.cancel(scope.interval);
                            $timeout.cancel(scope.timeout);
                        });

                    }
                };
            }])

    })
