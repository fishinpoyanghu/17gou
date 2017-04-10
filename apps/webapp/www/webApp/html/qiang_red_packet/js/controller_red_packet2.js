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
            .controller('RedPacketController2', ['$scope', '$state',  '$stateParams','ToastUtils', 'ActivityModel', 'trolleyInfo', 'Storage', 'GoodsModel','MyUrl', '$timeout', 'AppModel', '$ionicPopup', 'Global', 'weChatJs','userModel' ,'$ionicSlideBoxDelegate',
                function($scope, $state, $stateParams, ToastUtils, ActivityModel, trolleyInfo, Storage, GoodsModel,MyUrl, $timeout, AppModel, $ionicPopup, Global, weChatJs, userModel,$ionicSlideBoxDelegate) {
                   var invite_code_share=$stateParams.invite_code_share;
                    if(invite_code_share){
                        Storage.set('invite_code_share',invite_code_share);
                    }
                    $scope.da2=true;//是缘分测试
                    $scope.da3=false;//是0元购机会

                    /*=======================微信红包/0元购机会=======================*/
                    $scope.making = true;
                    $scope.qiang_suc = false;
                    $scope.qiang_suc2 = false;
                    $scope.Red_box=true;
                    $scope.forfinger=false;
                    $scope.Details_box=false//
                    $scope.Details_box=true;
                    $timeout(function(){
                        $scope.forfinger=true;
                    },500);
                    $timeout(function(){
                        $scope.forfinger=false;
                    },3000);

                    $scope.get_redpacke_money = function () {
                        $scope.Details_box=true;//老王要求点击立即夺宝之后是直接登录了微信的
                        //老王要求点击立即夺宝之后是直接登录了微信的
                           userModel.getredpacket(function(response){
                            var code = response.data.code;
                            if(code==0){                               
                                
                                   $scope.Details_box=true;
                            }else{
                                 //证明红包已经抢过或活动结束
                                 ToastUtils.showError(response.data.msg);
                                 $timeout(function () {
                                      $state.go('tab.mainpage'); }, 2000) 
                            }

                        },function(response){ 
                              ToastUtils.showError('请检查网络状态！');
                        });
                        $scope.qiang_suc = false;
                        $scope.Red_box=false;
                        

                    }

                    //老王要求点击立即夺宝之后是直接登录了微信的
                    $scope.goLogin=function(){
                   /*     userModel.getredpacket(function(response){  
                        },function(response){                               
                        });*/
                        $scope.making = false;
                         gowxlogin();
                       // $state.go('tab.account');//登录了微信
                       return false;
                    }

                    $scope.close_red = function () {
                        $scope.Red_box=false;
                        $scope.making = false;
                        $state.go('tab.account');
                    }

                    $scope.close_rd = function () {
                         
                        $scope.making = false;  
                        gowxlogin();                      
                      /*  $timeout(function () {
                        $state.go('tab.account'); }, 3000);*/
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

                    /*====================缘分测试=====================*/

                    $scope.FT={
                        name1:"",
                        name2:"",
                        result:"",
                        timer:""
                    }
                    $scope.timer="";
                    $scope.resuletText=[
                        {karma_text:"绝无仅有，一生无缘。"},
                        {karma_text:"你们确定还能愉快的做朋友么，还是死心吧。"},
                        {karma_text:"茫茫人海中两人从相遇，相识，相知，这就是缘分。可惜你们没有这个福分了。"},
                        {karma_text:"我相信这就是所谓的有缘无份吧。"},
                        {karma_text:"你们相当合拍，虽然免不了有小摩擦，但总会和好的！"},
                        {karma_text:"你们在一起会很开心，是天生的一对。"},
                        {karma_text:"你们是万年一遇的绝世佳人，将会白头偕老！"}
                    ]
                    $scope.result=false;
                    $scope.fangda=false;
                    $scope.input_pop=true;
                    $scope.red_pop=true;
                    $scope.id="";
                    var mTitle = '缘分测试,快来试试吧!！';
                    var mImgUrls = baseUrl + 'img/love_icon.png';
                    var mLink = baseUrl + '#/qiangRedPacket2';
                    var mContent = '缘分测试,快来试试吧!';
                   
                    var invite_code=Storage.get('yiqigou_invite_code');                     
                    if(invite_code){
                        mLink=mLink+'/'+invite_code;
                     }
                     
                    $timeout(function(){
                        if(Global.isInweixinBrowser()) { 
                            weChatJs.wxShareToTimeline(mTitle, mLink , mImgUrls, function() {
                                // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
                            }, function(){});
                            weChatJs.wxShareToAppMessage(mTitle,mContent, mLink , mImgUrls, function() {
                                // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
                            }, function(){});
                           
                        }                     
                        //由于微信分享会被其他js数据重新加载影响。所以在此处写了定时器。
                    },2000)

                  

                    $scope.textyuanfen=function() {                      
                        if(Global.isInweixinBrowser()) {
                            weChatJs.wxShareToTimeline(mTitle, mLink , mImgUrls, function() {
                                // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
                            }, function(){});
                            weChatJs.wxShareToAppMessage(mTitle,mContent, mLink , mImgUrls, function() {
                                // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
                            }, function(){});                             
                        }
                        $scope.red_pop = false;
                        $scope.input_pop = false;
                        $timeout(function () {
                            $scope.id = Math.ceil(Math.random() * 100);
                        }, 10)
                        $timeout(function(){
                            $scope.FT.timer++;
                        },10)

                        $timeout(function(){
                            console.log($scope.id);
                            $scope.result = true;
                            if($scope.id<=0){
                                $scope.FT.result = $scope.resuletText[0].karma_text;
                            }
                            if($scope.id>=1 && $scope.id<=20){
                                $scope.FT.result = $scope.resuletText[1].karma_text;
                            }
                            if($scope.id>=21 && $scope.id<=50){
                                $scope.FT.result = $scope.resuletText[2].karma_text;
                            }
                            if($scope.id>=51 && $scope.id<=70){
                                $scope.FT.result = $scope.resuletText[3].karma_text;
                            }
                            if($scope.id>=71 && $scope.id<=80){
                                $scope.FT.result = $scope.resuletText[4].karma_text;
                            }
                            if($scope.id>=81 &&$scope.id<=98){
                                $scope.FT.result = $scope.resuletText[5].karma_text;
                            }
                            if($scope.id==100){
                                $scope.FT.result = $scope.resuletText[6].karma_text;
                            }

                            $scope.fangda = true;

                        },1000)

                    }


                    //跳转到首页
                    $scope.goIndex=function(){
                        $timeout(function(){
                            $scope.da3=true;
                            $scope.da2=false;
                        },500)
                    }

 



                }
            ]);
    });
