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
    ],
    function(app) {
        'use strict';
        app
            .controller('RedPacketController', ['$scope', '$state', 'ToastUtils', 'ActivityModel', 'trolleyInfo', 'Storage', 'GoodsModel', '$timeout', 'AppModel', '$ionicPopup', 'Global', 'weChatJs','userModel' ,'$ionicSlideBoxDelegate',
                function($scope, $state, ToastUtils, ActivityModel, trolleyInfo, Storage, GoodsModel, $timeout, AppModel, $ionicPopup, Global, weChatJs, userModel,$ionicSlideBoxDelegate) {
                    $scope.making = true;
                    $scope.qiang_suc = false;
                    $scope.Red_box=true;
                    $scope.forfinger=false;
                    $scope.Details_box=false;
                    $timeout(function(){
                        $scope.forfinger=true;
                    },1000);
                    $timeout(function(){
                        $scope.forfinger=false;
                    },3000);

                    $scope.get_redpacke_money = function () {
                        userModel.getredpacket(function(response){ 
                            var code = response.data.code;
                            if(code==0){
                                  $scope.qiang_suc = true;
                                  /*$timeout(function () {
                                      $state.go('tab.account'); }, 2000);*/
                            }else{
                                 ToastUtils.showError(response.data.msg);
                            }

                        },function(response){ 
                              ToastUtils.showError('请检查网络状态！');
                        });
                       $timeout(function(){
                          $scope.qiang_suc = false;
                           $scope.Red_box=false;
                           $scope.Details_box=true;
                       },1000);
                    }
                    $scope.close_red = function () {
                        /*$scope.making = false;*/
                        $scope.Red_box=false;
                        $scope.Details_box=true;
                    }

                    $scope.close_rd = function () {
                        $scope.making = false;
                        $state.go('tab.mainpage');
                    }


                }
            ]);
    });
