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
            .controller('commodity_classificationController', ['$scope', '$state', 'ToastUtils', 'ActivityModel', 'trolleyInfo', 'Storage', 'GoodsModel', '$timeout', 'AppModel', '$ionicPopup', 'Global', 'weChatJs','userModel' ,'$ionicSlideBoxDelegate',
                function($scope, $state, ToastUtils, ActivityModel, trolleyInfo, Storage, GoodsModel, $timeout, AppModel, $ionicPopup, Global, weChatJs, userModel,$ionicSlideBoxDelegate) {
                    $scope.commodity_pics=[
                        {
                            goods_type_id:"",
                            img:"img/commodity_classification/c_02.gif"
                        },
                        {
                            goods_type_id:"1",
                            img:"img/commodity_classification/c_06.gif"
                        },
                        {
                            goods_type_id:2,
                            img:"img/commodity_classification/c_07.gif"
                        },
                        {
                            goods_type_id:8,
                            img:"img/commodity_classification/c_17.gif"
                        },
                        {
                            goods_type_id:9,
                            img:"img/commodity_classification/c_18.gif",
                            url:"classification_details"
                        },
                        {
                            goods_type_id:10,
                            img:"img/commodity_classification/c_19.gif"
                        },
                        {
                            goods_type_id:12,
                            img:"img/commodity_classification/c_20.jpg"
                        },
                        {
                            goods_type_id:"",
                            img:"img/commodity_classification/c_09.gif"
                        },
                        {
                            goods_type_id:"",
                            img:"img/commodity_classification/c_10.jpg"
                        },
                        {
                            goods_type_id:19,
                            img:"img/commodity_classification/c_11.gif"
                        },
                        {
                            goods_type_id:"",
                            img:"img/commodity_classification/c_12.gif",
                        },
                        {
                            goods_type_id:3,
                            img:"img/commodity_classification/c_13.gif"
                        },
                        {
                            goods_type_id:4,
                            img:"img/commodity_classification/c_14.gif"
                        },
                        {
                            goods_type_id:6,
                            img:"img/commodity_classification/c_15.gif"
                        },
                        {
                            goods_type_id:7,
                            img:"img/commodity_classification/c_16.gif"
                        }

                    ];

                    for(var i=0;i< $scope.commodity_pics.length;i++){
                        //跳转到商品分类对应的页面
                        $scope.go_classification_details=function(goods_type_id,i){

                            if(i==8){
                                $state.go('twopeople2');
                            }
                            else if(i==0){
                                $state.go('tab.mainpage');
                            }
                             else if(i==7){
                                $state.go('baituandazhan');
                            }

                             else if(i==10){
                                $state.go('return_cash2');
                            }

                            else{
                                $scope.commodity_pics[i].goods_type_id=goods_type_id;
                                console.log($scope.commodity_pics[i].goods_type_id);
                                console.log(goods_type_id);
                                $state.go('classification_details');
                                Storage.set('commodity_goods_type_id',$scope.commodity_pics[i].goods_type_id);
                            }


                        }


                    }

                    $scope.go_mainpage=function(){
                        $state.go("tab.mainpage");
                    }

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
