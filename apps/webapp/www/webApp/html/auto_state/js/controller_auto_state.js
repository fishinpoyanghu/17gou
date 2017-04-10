define(
    [
        'app',
        'components/view-slidebox/view_slidebox',
        'components/view-broad/view_broad',
        'html/common/constants',
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
        'models/model_user',
        'html/common/service_user_info',
        'html/common/geturl_service',
        'html/thirdParty/thirdparty_wechat_js',

    ],
    function(app) {
        'use strict';
        app
            .controller('auto_state', ['$scope', '$state',  '$stateParams','ToastUtils', 'ActivityModel', 'trolleyInfo', 'Storage', 'GoodsModel','MyUrl', '$timeout', 'AppModel', '$ionicPopup', 'Global', 'weChatJs','userModel' ,'$ionicSlideBoxDelegate',
                function($scope, $state, $stateParams, ToastUtils, ActivityModel, trolleyInfo, Storage, GoodsModel,MyUrl, $timeout, AppModel, $ionicPopup, Global, weChatJs, userModel,$ionicSlideBoxDelegate) {
                
                    // $state.params.routeurl //当前页面路由跳转 跳转格式是/autostate/turntable&invitecode=123.....等各种参数                   
                    if(MyUrl.isLogin()){                        
                           if(($state.params.routeurl.indexOf("&")>0)){   
                               $state.go($state.params.routeurl.split('&')[0]);
                           }else{   
                                 $state.go($state.params.routeurl);
                           }                       
                    }else{
                       var url = window.location.href;
                       var  invite_code = url.split('inviteCode=')[1];
                        if (invite_code) {                             
                            Storage.set('invite_code_share',invite_code);
                        }
                        if($state.params.routeurl.indexOf("&")>0){
                            var route=$state.params.routeurl.split('&')[0]; 
                            Storage.set('fromState',route); 
                            // Storage.set('fromParams',fromParams)
                        }
                        

                        gowxlogin();
                    }
                    
                    function gowxlogin(){
                        var msg='';
                        var invite_code_share =Storage.get('invite_code_share');
                        if(Storage.get('invite_code_share')){
                            msg={'invite_code':invite_code_share};
                        }                                  
                        userModel.weChatLoginFromBrowser(msg);
                    }
                    /*===============================================*/ 

                }
            ]);
    });
