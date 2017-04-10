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
        'models/model_pintuan',
        'html/common/global_service',
        'html/thirdParty/thirdparty_wechat_js',
        'html/common/storage',
        'models/model_user'
    ],
    function(app) {
        'use strict';
        app
            .controller('commodity_classificationController', ['$scope', '$state', 'ToastUtils', 'ActivityModel', 'trolleyInfo', 'Storage', 'GoodsModel', '$timeout', 'AppModel', '$ionicPopup', 'Global', 'weChatJs','userModel' ,'$ionicSlideBoxDelegate','PintuanModel',
                function($scope, $state, ToastUtils, ActivityModel, trolleyInfo, Storage, GoodsModel, $timeout, AppModel, $ionicPopup, Global, weChatJs, userModel,$ionicSlideBoxDelegate,PintuanModel) {
                    $scope.goods_type_id=null;
                    $scope.activity_type=null;
                    $scope.commodity_pics=[
                        {
                            goods_type_id:"null",
                            img:"img/pintuan/pintuan_banner.jpg",
                            type:""
                        },
                        {
                            goods_type_id:1,
                            img:"img/pintuan/1.jpg",
                            type:""
                        },
                        {
                            goods_type_id:2,
                            img:"img/pintuan/2.jpg",
                            type:""
                        },
                        {
                            goods_type_id:3,
                            img:"img/pintuan/3.jpg",
                            type:""
                        },
                        {
                            goods_type_id:4,
                            img:"img/pintuan/4.jpg",
                            type:""
                        },
                        {
                            goods_type_id:8,
                            img:"img/pintuan/5.jpg",
                            type:""
                        },
                        {
                            goods_type_id:6,
                            img:"img/pintuan/6.jpg",
                            type:""
                        },
                        {
                            goods_type_id:7,
                            img:"img/pintuan/7.jpg",
                            type:""
                        },
                        {
                            goods_type_id:9,
                            img:"img/pintuan/8.jpg",
                            type:""
                        }

                    ];


                    $scope.go_pintuanmainpage = function() {
                        $state.go("tab.pintuan_main_page")
                    }
                    $scope.categoryList = [];

                    function getPintuanGoodsList(){
                        PintuanModel.pintuan_homepage($scope.goods_type_id,null,'','','','','0',$scope.activity_type, function(xhr, re) {
                            var code = re.code;
                            if (code == 0) {
                                var data = re.data;
                                var len = data.length;
                            } else {

                            }

                        }, function(response, data) {

                        }, function() {

                        })
                    }
                    for(var i=0;i< $scope.commodity_pics.length;i++){
                        getPintuanGoodsList();
                        //跳转到商品分类对应的页面
                        $scope.go_classification_details=function(goods_type_id,i){

                            if(goods_type_id==8){
                                $state.go('eight_people_main_page');
                            }
                            else{
                                $scope.commodity_pics[i].goods_type_id=goods_type_id;
                                console.log($scope.commodity_pics[i].goods_type_id);
                                console.log(goods_type_id);
                                $state.go('pintuan_classification_details',{type:$scope.activity_type});
                                Storage.set('pintuan_goods_type_id',$scope.commodity_pics[i].goods_type_id);
                            }

                        }


                    }
                    getPintuanGoodsList();

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
