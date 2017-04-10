define(['app', 'components/view-turnplate/awardRotate'], function(app, awardRotate) {
    app
        .directive('dmTurnplate', [function() {
            return {
                scope: {
                    compId: '@', //组件id，识别唯一性
                    turnplateSetting: '@', //配置
                },
                restrict: 'EAC',
                replace: false,
                template: function(elem, attr) {
                    var awardUseImg = attr.awardUseImg || false;
                    if (awardUseImg == 'true') {
                        return '<div style="width:100%;">' +
                            '<div style="max-width:800px; margin:0 auto;">' +
                            '<div style="display:block;width:95%;margin-left:auto;margin-right:auto;">' +
                            '<div style="display:block;width:100%;position:relative;" >' +
                            '<canvas style="width:100%;" id="{{::awardId}}" width="422px" height="422px"></canvas>' +
                            '<canvas style="transform: translateZ(0);position:absolute;width:31.5%;height:42.5%; left: 34.5%;top: 22.8%;z-index:2;" id="{{::pointerId}}"  ></canvas>' +
                            '<div style="position:absolute;width:29%;height:28%; left: 35.5%;top: 35.5%;z-index:2;border-radius:100%;" ng-click="start()"></div>'
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    } else {
                        return '<div style="width:100%;">' +
                            '<div style="max-width:800px; margin:0 auto;">' +
                            '<div style="display:block;width:95%;margin-left:auto;margin-right:auto;">' +
                            '<div style="display:block;width:100%;position:relative;background-image:url(./img/turnplate/turnplate-bg.png);background-size:100% 100%;">' +
                            '<canvas style="width:100%;" id="{{::awardId}}" width="422px" height="422px"></canvas>' +
                            '<canvas style="transform: translateZ(0);position:absolute;width:31.5%;height:42.5%; left: 34.5%;top: 22.8%;z-index:2;" id="{{::pointerId}}"></canvas>' +
                            '<div style="position:absolute;width:29%;height:28%; left: 35.5%;top: 35.5%;z-index:2;border-radius:100%;" ng-click="start()"></div>'
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    }

                },
                compile: function(tElm, tAttrs, transclude) {
                    return {
                        pre: function preLink(scope, iElement, iAttrs, controller) {

                        },
                        post: function postLink(scope, iElement, iAttrs, controller) {
                            var random = "" + (+new Date()) + rnd(1, 10000);
                            scope.pointerId = 'pointer_' + random;
                            scope.awardId = 'award_' + random;
                            var awardUseImg = iAttrs.awardUseImg || false;
                            var turnplate;
                            if (awardUseImg == 'true') {
                                turnplate = {
                                    awardImgUrl: '', //大转盘奖品图片
                                    awardLen: 8, //奖项个数
                                    bRotate: false //false:停止;ture:旋转
                                };
                                scope.$watch('turnplateSetting', function(newValue, oldValue) {
                                        newValue = newValue || JSON.stringify({});
                                        turnplate = angular.extend(turnplate, JSON.parse(newValue));
                                        if (turnplate.awardImgUrl == '') return;
                                        awardUseImgInit()

                                    })
                                    // turnplate = angular.extend(turnplate, JSON.parse(scope.turnplateSetting));
                                    // awardUseImgInit()
                            } else {
                                turnplate = {
                                    restaraunts: [], //大转盘奖品名称
                                    colors: [], //大转盘奖品区块对应背景颜色
                                    iconImg: [], //奖品对应的图片
                                    awardColor: '', //奖品文字颜色
                                    outsideRadius: 192, //大转盘外圆的半径
                                    textRadius: 155, //大转盘奖品位置距离圆心的距离
                                    insideRadius: 68, //大转盘内圆的半径
                                    startAngle: 0, //开始角度
                                    bRotate: false //false:停止;ture:旋转
                                };
                                scope.$watch('turnplateSetting', function(newValue, oldValue) {
                                    newValue = newValue || JSON.stringify({});
                                    turnplate = angular.extend(turnplate, JSON.parse(newValue));
                                    if (turnplate.restaraunts.length == 0) return;
                                    init(turnplate)

                                })

                                // turnplate = angular.extend(turnplate, JSON.parse(scope.turnplateSetting));
                                // init(turnplate)

                            }

                            function init(turnplate) {
                                initPointer(scope.pointerId, 'img/turnplate/turnplate-pointer.png')
                                initAwardList(scope.awardId, turnplate)
                            }
                            //绘制奖项
                            function initAwardList(canvas_id, turnplate) {
                                var iconImg = turnplate.iconImg;
                                if (iconImg && iconImg.length > 0) {
                                    var iconObj = {};
                                    icon = [];
                                    for (var i = 0, iconImgLen = iconImg.length; i < iconImgLen; i++) {
                                        if (iconImg[i] != "" && !iconObj[iconImg[i]]) {
                                            iconObj[iconImg[i]] = true;
                                            icon.push(iconImg[i])
                                        }
                                    }
                                    var loadLen = 0;
                                    //确保所有图标加载完了再绘制，不然会出现位置偏移
                                    for (var j = 0, iconLen = icon.length; j < iconLen; j++) {
                                        (function(j) {
                                            var img = new Image();
                                            img.src = icon[j];
                                            img.onload = function() {
                                                loadLen++;
                                                if (loadLen == iconLen) {
                                                    drawRouletteWheel(canvas_id, turnplate);
                                                }
                                            };
                                        })(j)
                                    }

                                } else {
                                    drawRouletteWheel(canvas_id, turnplate);
                                }


                            }
                            //初始化里面的指针
                            function initPointer(canvas_id, src) {
                                var img = new Image();
                                img.src = src;
                                img.onload = function() {
                                    drawPointer(img, canvas_id);
                                };
                            }
                            // scope.compId = 
                            //奖品用整张图片时的初始化
                            function awardUseImgInit() {
                                var award_img = new Image();
                                award_img.src = turnplate.awardImgUrl;
                                award_img.onload = function() {
                                    drawAwardImg(award_img, scope.awardId);
                                };
                                initPointer(scope.pointerId, 'img/turnplate/turnplate-pointer.png');
                            }

                            //点击抽奖
                            scope.start = function() {
                                if (turnplate.bRotate) return;
                                //获取随机数(奖品个数范围内)
                                scope.$emit('turnplate.startNow', scope.compId);
                            }

                            scope.$on('turnplate.start', function(id, compId, item, callback, duration) {
                                if (scope.compId == compId) {
                                    turnplate.bRotate = !turnplate.bRotate;
                                    rotateFn(scope.awardId, item, callback, duration);
                                }
                            });
                            //生成两个数之间的随机数
                            function rnd(n, m) {
                                var random = Math.floor(Math.random() * (m - n + 1) + n);
                                return random;

                            }
                            //绘制奖品大图
                            function drawAwardImg(award_img, canvas_id) {
                                drawImg(award_img, canvas_id, [0, 0, 422, 422]);
                            }
                            //绘制中间指针部分
                            function drawPointer(pointer_img, canvas_id) {
                                drawImg(pointer_img, canvas_id, [0, 0, 306, 149]);
                            }

                            //canvas绘制图片
                            function drawImg(award_img, canvas_id, position) {
                                var canvas = document.getElementById(canvas_id);
                                if (canvas.getContext) {
                                    var ctx = canvas.getContext("2d");
                                    ctx.drawImage(award_img, position[0], position[1], position[2], position[3]);
                                }
                            }
                            //旋转转盘 item:奖品位置; txt：提示语;初始化时正上方的item为0，每个的item值顺时针加1
                            var rotateFn = function(canvas_id, item, callback, duration) {
                                var len = awardUseImg == 'true' ? turnplate.awardLen : turnplate.restaraunts.length;
                                var angles = 360 - item * (360 / len);
                                awardRotate.stopRotate(document.querySelectorAll('#' + canvas_id));
                                awardRotate.rotate(document.querySelectorAll('#' + canvas_id), {
                                    angle: 0,
                                    animateTo: angles + 1800,
                                    duration: duration || 8000,
                                    callback: function() {
                                        turnplate.bRotate = !turnplate.bRotate;
                                        if (angular.isFunction(callback)) {
                                            callback()
                                        }

                                    }
                                });
                            };
                            //默认的文字处理函数
                            function defaultDealText(text, ctx) {
                                var line_height = 17;
                                if (text.length > 6) { //奖品名称长度超过一定范围 
                                    text = text.substring(0, 6) + "||" + text.substring(6);
                                    var texts = text.split("||");
                                    for (var j = 0; j < texts.length; j++) {
                                        ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
                                    }
                                } else {
                                    ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
                                }

                            }

                            function drawRouletteWheel(canvas_id, turnplate) {
                                var canvas = document.getElementById(canvas_id);
                                if (canvas.getContext) {
                                    //根据奖品个数计算圆周角度
                                    var arc = Math.PI / (turnplate.restaraunts.length / 2);
                                    var ctx = canvas.getContext("2d");
                                    //在给定矩形内清空一个矩形
                                    ctx.clearRect(0, 0, 422, 422);
                                    //strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式  
                                    ctx.strokeStyle = "#FFBE04";
                                    //font 属性设置或返回画布上文本内容的当前字体属性
                                    ctx.font = '16px Microsoft YaHei';
                                    // ctx.moveTo(211,0);
                                    var colors = turnplate.colors;
                                    var awardColor = turnplate.awardColor;
                                    var dealText = iAttrs.dealText;
                                    var iconImg = turnplate.iconImg;
                                    for (var i = 0; i < turnplate.restaraunts.length; i++) {

                                        var angle = turnplate.startAngle + i * arc + 1.5 * Math.PI - arc / 2;
                                        //设置奖品颜色
                                        ctx.fillStyle = colors[i] || (i % 2 == 0 ? "#FFF4D6" : "#FFFFFF");

                                        ctx.beginPath();

                                        //arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）    
                                        ctx.arc(211, 211, turnplate.outsideRadius, angle, angle + arc, false);

                                        ctx.arc(211, 211, turnplate.insideRadius, angle + arc, angle, true);
                                        ctx.stroke();
                                        ctx.fill();
                                        //锁画布(为了保存之前的画布状态)
                                        ctx.save();

                                        //----绘制奖品开始----
                                        ctx.fillStyle = awardColor ? awardColor : "#E5302F";
                                        var text = turnplate.restaraunts[i];

                                        //translate方法重新映射画布上的 (0,0) 位置
                                        ctx.translate(211 + Math.cos(angle + arc / 2) * turnplate.textRadius, 211 + Math.sin(angle + arc / 2) * turnplate.textRadius);

                                        //rotate方法旋转当前的绘图
                                        ctx.rotate(angle + arc / 2 + Math.PI / 2);

                                        /** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色。(具体根据实际情况改变) **/
                                        if (dealText == 'true') {
                                            //触发自定义文字处理函数,将deal-text属性设置为true时，控制器中必须绑定下面的自定义方法，不然奖品无法绘制出来
                                            /* 例子:
                                                    $scope.$on('turnplate.dealText', function(scope, compId, text, ctx) {
                                                        if ('9-1-1' == compId) {
                                                            return (function(text, ctx) {
                                                                var line_height = 17;
                                                                if (text.indexOf("M") > 0) { //流量包
                                                                    var texts = text.split("M");
                                                                    for (var j = 0; j < texts.length; j++) {
                                                                        ctx.font = j == 0 ? 'bold 20px Microsoft YaHei' : '16px Microsoft YaHei';
                                                                        if (j == 0) {
                                                                            ctx.fillText(texts[j] + "M", -ctx.measureText(texts[j] + "M").width / 2, j * line_height);
                                                                        } else {
                                                                            ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
                                                                        }
                                                                    }
                                                                } else if (text.indexOf("M") == -1 && text.length > 6) { //奖品名称长度超过一定范围 
                                                                    text = text.substring(0, 6) + "||" + text.substring(6);
                                                                    var texts = text.split("||");
                                                                    for (var j = 0; j < texts.length; j++) {
                                                                        ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
                                                                    }
                                                                } else {
                                                                    //在画布上绘制填色的文本。文本的默认颜色是黑色
                                                                    //measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
                                                                    ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
                                                                }
                                                            })(text, ctx)
                                                        }
                                                    });

                                            */
                                            scope.$emit('turnplate.dealText', scope.compId, text, ctx);
                                        } else {
                                            defaultDealText(text, ctx);
                                        }



                                        //添加对应图标
                                        if (iconImg[i] && iconImg[i] != '') {
                                            var img = new Image();
                                            img.src = iconImg[i];
                                            ctx.drawImage(img, -15, 10);
                                        }

                                        //把当前画布返回（调整）到上一个save()状态之前 
                                        ctx.restore();
                                        //----绘制奖品结束----
                                    }
                                }
                            }
                        }
                    }

                }
            };
        }])

});
