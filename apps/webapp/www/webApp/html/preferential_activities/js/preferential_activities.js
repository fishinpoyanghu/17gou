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
        'html/common/storage',
        'models/model_user'
    ],
    function(app) {
        'use strict';
        app
            .controller('preferential_activitiesController', ['$scope', '$state', 'ToastUtils', 'ActivityModel', 'trolleyInfo', 'Storage', 'GoodsModel', '$timeout', 'AppModel', '$ionicPopup', 'Global', 'weChatJs','userModel' ,'$ionicSlideBoxDelegate','$ionicScrollDelegate',
                function($scope, $state, ToastUtils, ActivityModel, trolleyInfo, Storage, GoodsModel, $timeout, AppModel, $ionicPopup, Global, weChatJs, userModel,$ionicSlideBoxDelegate,$ionicScrollDelegate) {
                    $scope.commodity_pics=[
                        {
                           img:"img/preferential_activities/1.jpg",
                            title:"亿七拼团-爱拼团，才省钱",
                            url:"pintuan_main_page"
                        },
                        {
                           img:"img/preferential_activities/2.jpg",
                            title:"百团大战-年度聚“惠”，团长免费",
                            url:"baituandazhan"
                        },
                        {
                           img:"img/preferential_activities/3.jpg",
                            title:"二人云购-人品大PK",
                            url:"twopeople2"
                        },
                        {
                           img:"img/preferential_activities/4.jpg",
                            title:"邀新得福袋，2017“友”你一起更开怀",
                            url:"activityRule/luckyBag"
                        }
                    ];
                    $scope.categoryList = [];
                    function getCategoryList(){
                        ClassificationService.getClasses(function(categoryList){
                            $scope.categoryList = categoryList
                            // for(var i in categoryList) {
                            //   if(categoryList[i].sub) {
                            //     for(var j = 0,len = categoryList[i].sub.length;j < len;j++) {
                            //       $scope.categoryList.push(categoryList[i].sub[j]);
                            //     }
                            //   }

                            // }
                        },function(reason){
                            console.error(reason);
                        });
                    }
                }
            ]);
    });
