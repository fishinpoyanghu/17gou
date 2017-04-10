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
        'html/common/geturl_service',
    ],
    function(app) {
        'use strict';
        app
            .controller('Christmas_DayCtrl', ['$scope', '$state','MyUrl', 'ToastUtils', 'ActivityModel', 'trolleyInfo', 'Storage', 'GoodsModel', '$timeout', 'AppModel', '$ionicPopup', 'Global', 'weChatJs','userModel' ,'$ionicSlideBoxDelegate',
                function($scope, $state,MyUrl, ToastUtils, ActivityModel, trolleyInfo, Storage, GoodsModel, $timeout, AppModel, $ionicPopup, Global, weChatJs, userModel,$ionicSlideBoxDelegate) {
                    Storage.set('fromState','Christmas_Day');
                    var myloginid=MyUrl.getSessid().split('-')[0];
                    if(!myloginid){
                            var msg='';
                            var url = window.location.href;
                            var  invite_code = url.split('inviteCode=')[1];
                            if (invite_code) {                             
                                msg={'invite_code':invite_code};  
                             }  
                             userModel.weChatLoginFromBrowser(msg); 
                             return false;   
                    }
                    $scope.making = true;  //
                    $scope.christmas_box = true;
                    $scope.christmas_bag = false;
                    $scope.christmas_older_car = false;
                    $scope.share_christmas=false;
                    $scope.sd_nowz_bg=false;//没文字的背景
                    $scope.gift_box=false;//静态关盖头
                    $scope.close_boxtop2=false;//开盖头
                    $scope.close_top=true;//关盖头
                    $scope.word2=true;//
                    $scope.word3=false;//
                    $scope.Santa_old2=false;
                    $scope.Santa_old1=false;
                    $scope.jt_box1=false;
                    $scope.xy_bg2=false;
                    $scope.xy_bg=false;
                    $scope.xy_qd=false;//愿望清单
                    $scope.share_txt_img=false;
                    $scope.ddd=true;
                    $scope.dada=false;
                    $scope.sd_text=false;

                    $scope.christmas={
                        present:"",
                        place:"",
                        talk:""
                    };

                    
                    //分享信息
                    var mTitle = '圣诞节到咯，过来写圣诞愿望清单';
                    var mImgUrls = baseUrl + 'img/Christmas_Day/share_sd.jpeg';
                    var mLink = baseUrl + '#/Christmas_Day2/';
                    var mContent = '圣诞节到咯，过来写圣诞愿望清单'; 
                     
         /*           $timeout(function(){  
                        if(Global.isInweixinBrowser()) {  
                             console.log(mLink);
                            weChatJs.wxShareToTimeline(mTitle, mLink , mImgUrls, function() {
                                // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
                            }, function(){});
                            weChatJs.wxShareToAppMessage(mTitle,mContent, mLink , mImgUrls, function() {
                                // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
                            }, function(){});

                        }
                        //由于微信分享会被其他js数据重新加载影响。所以在此处写了定时器。
                    },3000);*/
                    //自动获取当前用户填写的信息
                     var params={'userid':myloginid};
                        userModel.getchristmas_wish(params,function(reponse,xhr){
                       var  code=reponse.data.code;
                       var  data=reponse.data.data; 
                       
                        if(code==0){
                            $scope.christmas.present = data[0].present; 
                            $scope.christmas.place = data[0].place;
                            $scope.christmas.talk = data[0].talk;  
                            Storage.set('invite_code_share',data[0].invite_code); 
                            mLink= mLink+myloginid+'&inviteCode='+data[0].invite_code;  
                            $timeout(function(){  
                            share_py();
                            },1000);
                            $timeout(function(){  
                            share_py();
                            },5000);
                        }else{  
                            /*ToastUtils.showError('TA的愿望清单信息不存在');*/
                             
                        }
                         
                        },function(){ 
                           // ToastUtils.showError('请检查网络状态！');
                        });

                    //分享信息
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
                    }
	
					
                    //弹出圣诞愿望清单


                    $scope.open_list=function(){
                        $scope.close_top=false;
                        $scope.jt_box1=false;
                        $scope.gift_box=true;
                        $scope.close_boxtop2=false;
                  /*      if(!myloginid){
                            var msg='';
                            var invite_code_share =Storage.get('invite_code_share');
                            if(invite_code_share){
                                msg={'invite_code':invite_code_share};  
                            }
                            $ionicPopup.alert({
                              title: '温馨提示',
                              template:msg ,
                              okText: '确定',
                             }) 
                             userModel.weChatLoginFromBrowser(msg); 
                             return false;   
                        }*/
                        $timeout(function(){
                            $scope.xy_bg=true;
                            $timeout(function(){
                                $scope.xy_qd=true;
                                $scope.word2=false;
                                $scope.word3=true;
                            },1000)

                        },700);
                        share_py();
                    };

                    $scope.cancel=function(){
                        $scope.xy_bg=false;
                        $timeout(function(){
                            $scope.xy_bg2=true;
                            $scope.xy_qd=false;
                       },1100);
                    }
                    $scope.confirm=function(){
                        
                         var params={
                            'present':$scope.christmas.present,
                            'place':$scope.christmas.place,
                            'talk':$scope.christmas.talk
                        }
                        userModel.christmas_wish(params,function(reponse,xhr){ //提交信息到服务器
                           $scope.xy_bg=false;
                            $timeout(function(){
                            $scope.xy_bg2=true;
                            $scope.xy_qd=false;
                        },1100);
                        },function(){

                        });
                         
                        
                    }


                    //点击包裹
                    $scope.close_boxtop=function(){
                        share_py();
                        $scope.gift_box=false;
                        $scope.close_boxtop2=true;
                        $scope.sd_nowz_bg=true;
                        $scope.word3=false;
                        $timeout(function(){
                            $scope.close_boxtop2=false;
                            $scope.jt_box1=true;
                            $scope.sd_nowz_bg=true;
                            $scope.word3=false;
                            $timeout(function(){
                                $scope.christmas_bag=true;
                                $scope.Santa_old1=true;
                                $scope.Santa_old2=false;
                               $timeout(function(){
                                $scope.Santa_old1=false;
                                },1000)
                                $timeout(function(){
                                    $scope.Santa_old2=true;
                                    $timeout(function(){
                                        $scope.christmas_older_car=true;
                                        $timeout(function(){
                                            $scope.christmas_box=false;
                                            $scope.share_christmas=true;
                                            $timeout(function(){
                                                $scope.share_txt_img=true;
                                                $scope.share_christmas=false;
                                            },3500);
                                        },5000);
                                    },3000);
                                },2000);
                            },2000);
                        },1000)
                    }

                    $scope.close_rd = function () {
                        /*$scope.making = false;*/
                        $scope.sd_text = true;
                    }
                    $scope.close_model = function () {//跳转到圣诞专区
                        $scope.making = false;
                        $state.go('christmas_album');
                    }

//					播放音乐
					var audio_con = document.getElementById('audio_btn'),
						audio_c = audio_con.getElementsByTagName('audio')[0],
						audio_rotate = document.getElementById('yinfu');
					audio_c.play();
					audio_con.onclick = function(){
//						console.log(audio_con)
						if(audio_c.paused){
							audio_rotate.className = 'rotate';
							audio_con.className = '';
							audio_c.play();
							return;
						}
						console.log(audio_rotate.className = '')
						audio_con.className = 'play_yinfu';
						audio_c.pause();
					}

                }
            ]);
    });
