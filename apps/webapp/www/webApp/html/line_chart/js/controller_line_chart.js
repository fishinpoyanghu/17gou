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
        'models/public_function',
        'html/common/global_service',
        'html/thirdParty/thirdparty_wechat_js',
        'models/model_user',
        'lib/chartJs/js/Chart'
    ],
    function(app) {
        'use strict';
        app
            .controller('line_chartController', ['$scope', '$state', 'ToastUtils', 'ActivityModel', 'trolleyInfo', 'Storage', 'GoodsModel', '$timeout', 'AppModel', 'publicFunction', '$ionicPopup', 'Global', 'weChatJs','userModel' ,'$ionicSlideBoxDelegate','$ionicScrollDelegate',
                function($scope, $state, ToastUtils, ActivityModel, trolleyInfo, Storage, GoodsModel, $timeout, AppModel,  publicFunction, $ionicPopup, Global, weChatJs, userModel,$ionicSlideBoxDelegate,$ionicScrollDelegate) {
                    $scope.year='';
                    $scope.month='';
                    $scope.type=1;// 1:收益 2:邀请人
                    getLineChart();
                    $scope.toast=true;
                    $scope.lineChart=false;
                    $timeout(function(){
                        $scope.toast=false;
                        $scope.lineChart=true;
                    },1500);


                    //日期的数据
                    $scope.yms=[
                        {year:2016,month:10},
                        {year:2016,month:11},
                        {year:2016,month:12},
                        {year:2017,month:1},
                        {year:2017,month:2}
                    ];



                    //2017年-月份  start
                    $scope.month_box = false;
                    $scope.up_icon_month = false;
                    $scope.down_icon_month = true;
                    $scope.show_month=function(){
                        $scope.up_icon_month = !$scope.up_icon_month;
                        $scope.down_icon_month = !$scope.down_icon_month;
                        $scope.month_box = !$scope.month_box;
                    }
                    //邀请注册弹框
                    $scope.close_month=function() {
                        $scope.month_box = false;
                        $scope.up_icon_month = false;
                        $scope.down_icon_month = true;
                    }
                    //end
                    //end


                    $scope.a=new Date;
                    //获取当前月份
                    $scope.mon=$scope.a.getMonth()+1;

                    //日数
                    $scope.data=[];
                    $scope.a.setMonth($scope.mon);
                    $scope.a.setDate(0);
                    $scope.day=$scope.a.getDate();
                    for(var i=1;i<=$scope.day;i++){
                        if(i%2!=0){
                            $scope.data.push(i);
                        }

                    }
                    console.log($scope.mon);

                    console.log($scope.a.getMonth()+1);
//收益的数据
                    //日期  start
                    //选择月份
                    $scope.dd={
                        year:'',
                        month:''
                    }
                    $scope.dd.year=$scope.a.getFullYear();
                    $scope.dd.month=$scope.a.getMonth()+1;
                    $scope.year=$scope.a.getFullYear();
                    $scope.month=$scope.a.getMonth()+1;

                    console.log($scope.dd.year);
                    console.log($scope.dd.month);
                    $scope.changeMon=function(year,month){
                        $scope.year=year;
                        $scope.month=month;
                        console.log(year);
                        console.log(month);
                        $scope.dd.year=year;
                        $scope.dd.month=month;
                        getLineChart();
                    }

                    $scope.sydata=[];//折线图的数据
                    //日期 end
                    //获取收益流水的数据
                    $scope.getLineChart=getLineChart;
                    function getLineChart(doRefresh){
                        userModel.getline_chart($scope.year,$scope.month,$scope.type,'',function(reponse,xhr){

                            var  code=reponse.data.code;
                            var  data=reponse.data.data;
                            if(code==0){
                                $scope.sydata=data;
                                console.log($scope.sydata);
                                //格式化时间
                                $scope.format=function(time){
                                    return publicFunction.formatTime(time,'yyyy-MM-dd');
                                }

                                if($scope.type==1){
                                    $ionicScrollDelegate.scrollTop();
                                    //折线图的数据start
                                    var lineChartData = {
                                        labels :$scope.data,
                                        datasets : [
                                            {
                                                fillColor : "rgba(112, 203, 223,0.5)",
                                                strokeColor : "rgba(112, 203, 223,1)",
                                                pointColor : "rgba(112, 203, 223,1)",
                                                pointStrokeColor : "#fff",
                                                data : $scope.sydata.all
                                            },
                                            {
                                                fillColor : "rgba(126, 197, 126,0.5)",
                                                strokeColor : "rgba(126, 197, 126,1)",
                                                pointColor : "rgba(126, 197, 126,1)",
                                                pointStrokeColor : "#fff",
                                                data : $scope.sydata.one
                                            },
                                            {
                                                fillColor : "rgba(243, 183, 68,0.5)",
                                                strokeColor : "rgba(243, 183, 68,1)",
                                                pointColor : "rgba(243, 183, 68,1)",
                                                pointStrokeColor : "#fff",
                                                data : $scope.sydata.two
                                            },

                                            {
                                                fillColor : "rgba(239, 131, 133,0.5)",
                                                strokeColor : "rgba(239, 131, 133,1)",
                                                pointColor : "rgba(239, 131, 133,1)",
                                                pointStrokeColor : "#fff",
                                                data :$scope.sydata.three
                                            }
                                        ]

                                    }
                                    var ctx = document.getElementById("myChart").getContext("2d");
                                    window.myLine = new Chart(ctx).Line(lineChartData, {
                                        responsive: true
                                    });
                                }
                                else if($scope.type==2){
                                    $ionicScrollDelegate.scrollTop();
                                    var lineChartData2 = {
                                        labels : $scope.data,
                                        datasets : [
                                            {
                                                fillColor : "rgba(126, 197, 126,0.5)",
                                                strokeColor : "rgba(126, 197, 126,1)",
                                                pointColor : "rgba(126, 197, 126,1)",
                                                pointStrokeColor : "#fff",
                                                data : $scope.sydata.one
                                            },
                                            {
                                                fillColor : "rgba(243, 183, 68,0.5)",
                                                strokeColor : "rgba(243, 183, 68,1)",
                                                pointColor : "rgba(243, 183, 68,1)",
                                                pointStrokeColor : "#fff",
                                                data :$scope.sydata.two
                                            },

                                            {
                                                fillColor : "rgba(239, 131, 133,0.5)",
                                                strokeColor : "rgba(239, 131, 133,1)",
                                                pointColor : "rgba(239, 131, 133,1)",
                                                pointStrokeColor : "#fff",
                                                data :$scope.sydata.three
                                            }
                                        ]


                                    }
                                    var ctx2 = document.getElementById("myChart2").getContext("2d");
                                    window.myLine = new Chart(ctx2).Line(lineChartData2, {
                                        responsive: true
                                    });
                                    //end
                                }

                            }else{
                            }
                        },function(){
                            // ToastUtils.showError('请检查网络状态！');
                            /*if (doRefresh) $scope.$broadcast('scroll.refreshComplete');*/
                        });
                        if (doRefresh) $scope.$broadcast('scroll.refreshComplete');
                    }

                    console.log($scope.sydata);
                    //格式化时间
                    $scope.format=function(time){
                        return publicFunction.formatTime(time,'yyyy-MM-dd');
                    }

                    //格式化时间 end


                    $scope.invite_earning=1;    //邀请注册收益报表

                    $scope.changeList=function(i){
                        $scope.type=i;
                        getLineChart('doRefresh');
                    }



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
                        $scope.up_icon =false;
                        $scope.down_icon = true;
                    }
                    //end



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
                        getLineChart('doRefresh');
                        console.log(123);
                    }
                    $scope.$on('$ionicView.beforeEnter',function(ev,data){
                        $scope.doRefresh();
                        $ionicScrollDelegate.scrollTop();
//                 alert('我执行了')
                    })
                }
            ]);
    });
