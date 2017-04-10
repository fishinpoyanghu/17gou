define(
    [
        'app',
        'components/view-slidebox/view_slidebox',
        'components/view-broad/view_broad',
        'utils/toastUtil',
        'models/model_activity',
        'components/view-progress/view_progress',
        'html/trolley/trolley_service',
        'components/view-text-scoller/view_text_scoller',
        'html/common/storage',
        'components/view-countdown/view_countdown',
        'models/model_goods',
        'models/model_app',
        'html/common/global_service',
        'html/thirdParty/thirdparty_wechat_js',
        'models/model_user',
        'lib/chartJs/js/Chart'
    ],
    function(app) {
        'use strict';
        app
            .controller('invite_detailController', ['$scope', '$state', 'ToastUtils', 'ActivityModel', 'trolleyInfo', 'Storage', 'GoodsModel', '$timeout', 'AppModel', '$ionicPopup', 'Global', 'weChatJs','userModel' ,'$ionicSlideBoxDelegate',
                function($scope, $state, ToastUtils, ActivityModel, trolleyInfo, Storage, GoodsModel, $timeout, AppModel, $ionicPopup, Global, weChatJs, userModel,$ionicSlideBoxDelegate) {
                    var randomScalingFactor = function(){ return Math.round(Math.random()*100)};

                    $scope.dadada={
                        tainshu:[1,3,5,7,9,11,13,15,17,19,21,23,25,27,29,31],
                        data:[20,1,0,4,20,50,42,11,18,25,15,34,36,21,14,48],
                        data2:[40,15,25,19,45,5,22,41,78,45,65,14,26,41,35,78],
                        data3:[30,20,45,7,60,55,32,31,68,25,95,44,50,81,60,68],
                        data4:[28,48,40,19,26,27,41,50,2,10,15,36,14,49,16,36]
                    }

                    //折线图的数据start
                    var lineChartData = {
                        labels : $scope.dadada.tainshu,
                        datasets : [
                            {
                                fillColor : "rgba(112, 203, 223,0.5)",
                                strokeColor : "rgba(112, 203, 223,1)",
                                pointColor : "rgba(112, 203, 223,1)",
                                pointStrokeColor : "#fff",
                                data : $scope.dadada.data
                            },
                            {
                                fillColor : "rgba(126, 197, 126,0.5)",
                                strokeColor : "rgba(126, 197, 126,1)",
                                pointColor : "rgba(126, 197, 126,1)",
                                pointStrokeColor : "#fff",
                                data : $scope.dadada.data2
                            },
                            {
                                fillColor : "rgba(243, 183, 68,0.5)",
                                strokeColor : "rgba(243, 183, 68,1)",
                                pointColor : "rgba(243, 183, 68,1)",
                                pointStrokeColor : "#fff",
                                data : $scope.dadada.data3
                            },

                            {
                                fillColor : "rgba(239, 131, 133,0.5)",
                                strokeColor : "rgba(239, 131, 133,1)",
                                pointColor : "rgba(239, 131, 133,1)",
                                pointStrokeColor : "#fff",
                                data :$scope.dadada.data4
                            }
                        ]

                    }
                    var lineChartData2 = {
                        labels : $scope.dadada.tainshu,
                        datasets : [
                            {
                                fillColor : "rgba(126, 197, 126,0.5)",
                                strokeColor : "rgba(126, 197, 126,1)",
                                pointColor : "rgba(126, 197, 126,1)",
                                pointStrokeColor : "#fff",
                                data : $scope.dadada.data2
                            },
                            {
                                fillColor : "rgba(243, 183, 68,0.5)",
                                strokeColor : "rgba(243, 183, 68,1)",
                                pointColor : "rgba(243, 183, 68,1)",
                                pointStrokeColor : "#fff",
                                data : $scope.dadada.data3
                            },

                            {
                                fillColor : "rgba(239, 131, 133,0.5)",
                                strokeColor : "rgba(239, 131, 133,1)",
                                pointColor : "rgba(239, 131, 133,1)",
                                pointStrokeColor : "#fff",
                                data :$scope.dadada.data4
                            }
                        ]

                    }
                    //end

                    $scope.invite_earning=1;    //邀请注册收益报表

                    $scope.changeList=function(i){
                        $scope.invite_earning=i;
                    }


                    //收益流水记录
                    $scope.earnings=[
                        {data:"2017-1-7",sy:"+0.5",source:"一级邀请注册"},
                        {data:"2016-12-20",sy:"+0.5",source:"一级邀请注册"},
                        {data:"2016-12-1",sy:"+0.3",source:"二级邀请注册"},
                        {data:"2016-10-3",sy:"+0.3",source:"二级邀请注册"}
                    ]

                    console.log(lineChartData.datasets[0].data);
                    var ctx = document.getElementById("myChart").getContext("2d");
                    var ctx2 = document.getElementById("myChart2").getContext("2d");
                    window.myLine = new Chart(ctx).Line(lineChartData, {
                        responsive: true
                    });
                    window.myLine = new Chart(ctx2).Line(lineChartData2, {
                        responsive: true
                    });
                    $scope.aside = false;
                    $scope.up_icon = false;
                    $scope.down_icon = true;

                    //邀请注册 start
                    $scope.show_aside=function(){
                        $scope.up_icon = !$scope.up_icon;
                        $scope.down_icon = !$scope.down_icon;
                        $scope.aside = !$scope.aside;
                    }
                    //邀请注册弹框
                    $scope.close_aside=function() {
                        $scope.aside = false;
                        $scope.up_icon = true;
                        $scope.down_icon = false;
                    }
                    //end


                    //2017年-月份  start
                    $scope.month = false;
                    $scope.up_icon_month = false;
                    $scope.down_icon_month = true;
                    $scope.show_month=function(){
                        $scope.up_icon_month = !$scope.up_icon_month;
                        $scope.down_icon_month = !$scope.down_icon_month;
                        $scope.month = !$scope.month;
                    }
                    //end






                    //获取收益流水的数据
                    /*getLineChart();
                    $scope.getLineChart=getLineChart;
                    function getLineChart(){
                        userModel.getline_chart('',function(reponse,xhr){
                            var  code=reponse.data.code;
                            var  data=reponse.data.data;
                            if(code==0){
                                console.log(data);
                            }else{


                            }
                        },function(){
                            // ToastUtils.showError('请检查网络状态！');
                        });
                    }*/



                    var gapHeight;
                    if($state.current.name == 'tab.classify') {
                        gapHeight = 49;
                        $scope.hideCartIcon = true;
                        $scope.showCartId = false;
                    } else {
                        gapHeight = 0;
                        $scope.hideCartIcon = false;
                        $scope.showCartId = true;
                    }
                    if(Global.isInweixinBrowser()) {
                        $scope.leftBarHeight = innerHeight - gapHeight;
                        $scope.leftBarTop = '44px';
                        $scope.inWechatB = true;
                    } else {
                        $scope.leftBarHeight = innerHeight - 44 - gapHeight;
                        if (ionic.Platform.isIOS() || ionic.Platform.isIPad()) {
                            $scope.leftBarTop = '88px';
                            $scope.inIosApp = true;
                        } else {
                            $scope.leftBarTop = '88px';
                            $scope.inIosApp = false;
                        }
                        $scope.inWechatB = false;
                    }

                    $scope.doRefresh = function() {
                        /*$scope.listData = [];*/
                        // getLineChart();
                        console.log(123);
                    }
                    $scope.$on('$ionicView.beforeEnter',function(ev,data){
//                 alert('我执行了')
                    })
                }
            ]);
    });
