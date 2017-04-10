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
        'html/christmas_day/js/controller_christmas_day',
        'html/common/geturl_service',
    ],
    function(app) {
        'use strict';
        app
            .controller('Christmas_DayCtrl2', ['$scope', '$state', '$stateParams','MyUrl','ToastUtils', 'ActivityModel', 'trolleyInfo', 'Storage', 'GoodsModel', '$timeout', 'AppModel', '$ionicPopup', 'Global', 'weChatJs','userModel' ,'$ionicSlideBoxDelegate',
                function($scope, $state,$stateParams,MyUrl, ToastUtils, ActivityModel, trolleyInfo, Storage, GoodsModel, $timeout, AppModel, $ionicPopup, Global, weChatJs, userModel,$ionicSlideBoxDelegate) {
                       
                    //点击看她/他的愿望清单
                    
                    var myloginid=MyUrl.getSessid().split('-')[0]; 
                    if(!myloginid){
                            Storage.set('fromState','Christmas_Day2'); 
                            Storage.set('fromParams',{'userid':$stateParams.userid});
                            var msg='';
                            var url = window.location.href;
                            var  invite_code = url.split('inviteCode=')[1];
                            if(invite_code){
                                msg={'invite_code':invite_code};  
                            } 
                            
                             userModel.weChatLoginFromBrowser(msg); 
                             return false;   
                    }
                    $scope.xy_bg=false;//愿望清单
                    $scope.empty_pager_bg=false;    //看不到Ta的愿望清单
                    $scope.click_ohterxy=true;    //看不到Ta的愿望清单

                     $scope.christmas={
                        present:"",
                        place:"",
                        talk:""
                    };
                     //分享信息
                /*    var mTitle = '圣诞节到咯，过来写圣诞愿望清单';
                    var mImgUrls = baseUrl + 'img/love_icon.png';
                    var mLink = baseUrl + '#/Christmas_Day2/';
                    var mContent = '圣诞节到咯，过来写圣诞愿望清单'; 
                    function share_py(){
                        if(Global.isInweixinBrowser()) {
                                  console.log(mLink);
                            weChatJs.wxShareToTimeline(mTitle, mLink , mImgUrls, function() {
                                // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
                            }, function(){});
                            weChatJs.wxShareToAppMessage(mTitle,mContent, mLink , mImgUrls, function() {
                                // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
                            }, function(){});
                        }
                    }*/
                    $scope.seek_ohter_xy=function(){
                        var params={'userid':$stateParams.userid};
                        userModel.getchristmas_wish(params,function(reponse,xhr){
                       var  code=reponse.data.code;
                       var  data=reponse.data.data; 
                        if(code==0 && data.length ){
                            $scope.christmas.present = data[0].present; 
                            $scope.christmas.place = data[0].place;
                            $scope.christmas.talk = data[0].talk;  
                      /*  if (data[0].invite_code){                             
                            Storage.set('invite_code_share',data[0].invite_code);
                        }*/
                            $scope.xy_bg=true;
                            $scope.click_ohterxy=false;
                        }else{  
                            /*ToastUtils.showError('TA的愿望清单信息不存在');*/
                            $scope.empty_pager_bg=true;
                        }
                         
                        },function(){ 
                            ToastUtils.showError('请检查网络状态！');
                        });
                    }
                    //点击我也要许愿就跳转到Christmas_Day
                    $scope.goChristmas_Day=function(){
                 /*    if(!MyUrl.isLogin()){     
                        var msg='';
                        var invite_code_share =Storage.get('invite_code_share');
                        if(invite_code_share){
                            msg={'invite_code':invite_code_share};
                        }                                  
                        userModel.weChatLoginFromBrowser(msg); 
                        return false;               
                     }*/

                        $state.go("Christmas_Day");
                    };
					
//					播放音乐
					var audio_con = document.getElementById('audio_btn'),
						audio_c = audio_con.getElementsByTagName('audio')[0],
						audio_rotate = document.getElementById('yinfu');
					audio_c.play();
					audio_con.onclick = function(){
						if(audio_c.paused){
							audio_rotate.className = 'rotate';
							audio_con.className = '';
							audio_c.play();
							return;
						}
						audio_rotate.className = '';
						audio_con.className = 'play_yinfu';
						audio_c.pause();
					}
                }
            ]);
    });
