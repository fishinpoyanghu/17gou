define([
    'app',
    'models/model_user',
    'utils/toastUtil',
    'html/thirdParty/thirdparty_wechat_js',
    'utils/clipboard',
    'utils/clipboard.min',
    'html/common/global_service',
    'models/model_invite',
    'html/common/geturl_service',
    'html/common/storage',
], function(app) {

    app.controller(
        'inviteFriendsCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel', 'ToastUtils', 'weChatJs', 'Global', 'AppModel', 'inviteModel','MyUrl','Storage',
            function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel, ToastUtils, weChatJs, Global, AppModel, inviteModel,MyUrl,Storage) {
                if (Global.isInweixinBrowser()) {
                    weChatJs.wxCheckIsSupport();
                    weChatJs.wxConfigInit(onInitSuccess, onInitFailed);
                }


                $scope.isShowShare = false;
                $scope.mLink = baseUrl;
                $scope.rebateInfo = {
                    title: '无',
                    content: '无',
                    rebate_money: '',
                    invite_code: '',
                    qrcode: ''
                };
                //微信分享
                var mTitle = appConfig.shareTitle || '一元就能买iphone，还送10元亿七购红包，叫我雷锋不谢';
                var mImgUrls = baseUrl + appConfig.shareImgUrls || '';
                var mLink = appConfig.shareLink || baseUrl + '#/boostrap/';
                var mContent = appConfig.shareContent || '我和你都能拿到红包哟';
                $scope.isLoadFinished = true;
                getInviteInfo();

                getRebateInfo();

                function getInviteInfo() {
                    if (!$scope.isLoadFinished) return;
                    $scope.isLoadFinished = false;
                    userModel.getInviteInfo(function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
                            var data = re.data;
                            $scope.inviteMoneyData = data;
                            sessionStorage.setItem('inviteMoney', data.money);
                        } else {
                            ToastUtils.showMsgWithCode(code, re.msg);
                        }
                    }, function(response, data) {
                        ToastUtils.showMsgWithCode(7, '获取邀请好友数和师徒收益数失败：' + '状态码：' + response.status);
                    }, function() {
                        $scope.isLoadFinished = true;
                    })
                }

                $scope.goBack = function() {
                    $state.go('tab.account2');
                };

                //获取二维码
                var defaultParams = MyUrl.getDefaultParams();
                var str = '';
                for(var i in defaultParams) {
                    str += ('&' + i + '=' + defaultParams[i]);
                }
                $scope.rebateInfo.qrcode =baseApiUrl + '?c=app&a=code' + str;


                /**q
                 *
                 * 获取邀请返利信息
                 */
                function getRebateInfo() {
                    inviteModel.getRebateInfo(function(response) {
                        //onSuccess
                        var code = response.data.code;
                        var msg = response.data.msg;
                        switch (code) {
                            case 0:
                                var data = response.data.data;
                                $scope.rebateInfo.rebate_money = data.rebate_money;
                                $scope.rebateInfo.invite_code = data.invite_code;
                                Storage.set('yiqigou_invite_code',data.invite_code)
                                //mLink = baseUrl + '#/boostrap/' + $scope.rebateInfo.invite_code;
                                mLink = baseUrl+'#/autostate/inviteFriends&inviteCode='+data.invite_code;
                                console.log(mLink);
                                 if (Global.isInweixinBrowser()) {
                                   weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, onShareSuccess, onShareCancel);
                                   weChatJs.wxShareToAppMessage(mTitle, mContent, mLink, mImgUrls, onShareSuccess, onShareCancel);
                                }
                                
                                $scope.mLink = mLink;
                                ssjjLog.log('mlink' + mLink);
                                break;
                            default:
                                ToastUtils.showError(msg);
                                break;

                        }
                    }, function(response) {
                        //onFail
                        ToastUtils.showError('请检查网络状态，状态码：' + response.status);
                    });
                }

                $scope.shareToWechat = function() {



                    if (Global.isInweixinBrowser()) {
                        showGuide();
                        //ToastUtils.showShortNow(STATE_STYLE.GOOD, '请打开右上角分享');
                        //显示引导分享界面
                    } else if (Global.isInAPP()) {
                        //app或非微信浏览器弹出分享框
                        $scope.isShowShare = true;
                        // WeChatShare.isWeChatInstalled().then(function() {
                        //     $scope.isShowShare = true;
                        // }, function() {

                        //     ToastUtils.showShortNow(STATE_STYLE.WARNING, "微信未安装");
                        //     return;

                        // });

                    } else {
                        //uc等浏览器
                        clipSuccess();
                        toAddPoint('copy');
                    }

                };
                //app分享到朋友圈
                $scope.shareToFriendsCircle = function() {
                    // var shareScene = WeChatShare.WechatShareScene.TIMELINE;
                    if (Global.isInAPP()) {
                        //app分享
                        var data = {
                            "title": mTitle,
                            "content": mContent,
                            "imgUrl": mImgUrls,
                            "targetUrl": mLink
                        }
                        dmwechat.share('wx_circle',data,function() {
                          ToastUtils.showShortNow(STATE_STYLE.GOOD, '分享成功');
                          toAddPoint()
                        },function(error) {
                          ToastUtils.showShortNow(STATE_STYLE.ERROR, error);
                        })
                            // var  wechatRequest = WeChatShare.shareLink(shareScene, mTitle, mContent, mImgUrls, mLink);
                            // wechatRequest.then(function () {
                            //     ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
                            //   },
                            //   function (error) {
                            //     ToastUtils.showShortNow(STATE_STYLE.ERROR, error);
                            //   }
                            // );

                    }

                };

                //app分享给好友
                $scope.shareToFriends = function() {
                    // var shareScene = WeChatShare.WechatShareScene.SESSION;
                    if (Global.isInAPP()) {
                        // var wechatRequest = WeChatShare.shareLink(shareScene, mTitle, mContent, mImgUrls, mLink);
                        // wechatRequest.then(function() {
                        //         ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
                        //     },
                        //     function(error) {
                        //         ToastUtils.showShortNow(STATE_STYLE.ERROR, error);
                        //     }
                        // );
                        var data = {
                            "title": mTitle,
                            "content": mContent,
                            "imgUrl": mImgUrls,
                            "targetUrl": mLink
                        }
                        dmwechat.share('wx',data,function() {
                          ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
                          toAddPoint();
                        },function(error) {
                          ToastUtils.showShortNow(STATE_STYLE.ERROR, error);
                        })

                    }

                };

                //复制链接
                $scope.copyLink = function() {

                    if (Global.isInAPP()) {

                        cordova.plugins.clipboard.copy(mLink);
                        ToastUtils.showShortNow(STATE_STYLE.GOOD, '复制成功' );
                        toAddPoint('copy')
                    }


                };

                $scope.hidePop = function() {

                    $scope.isShowShare = false;

                };
                $scope.addPoint = true;
                function toAddPoint(type) {
                    if (!$scope.addPoint) return;
                    $scope.addPoint = false;
                    userModel.toAddPoint(function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
                            if(type != 'copy') {
                                ToastUtils.showShortNow(STATE_STYLE.GOOD, re.msg);
                            }

                        } else {
                            // ToastUtils.showMsgWithCode(code, re.msg);
                        }
                    }, function(response, data) {
                        ToastUtils.showMsgWithCode(7, '获取积分失败：' + '状态码：' + response.status);
                    }, function() {
                        $scope.addPoint = true;
                    })
                }

                $scope.hideGuide1 = function() {
                    hideGuide();
                }

                function showGuide() {
                    document.getElementById("guidepop").style.display = 'block';

                }

                function hideGuide() {
                    document.getElementById("guidepop").style.display = 'none';

                }


                $scope.code = {
                    showCode: true
                }
                //显示二维码
                $scope.showDisabled = function() {
                    console.log("hold");
                    $scope.code.showCode = false;
                }
                $scope.hideDisabled = function() {
                    $scope.code.showCode = true;
                }


                function clipSuccess() {

                    var clipboard = new Clipboard('.dp-button--yellow');
                    clipboard.on('success', function(e) {
                        ssjjLog.info('Action:', e.action);
                        ssjjLog.info('Text:', e.text);
                        ssjjLog.info('Trigger:', e.trigger);
                        e.clearSelection();
                        ToastUtils.showShortNow(STATE_STYLE.GOOD, '已复制，请粘贴到微信浏览器中打开：');
                    });


                }

                function onShareSuccess() {
                    hideGuide();
                    toAddPoint('point')
                    // ToastUtils.showShortNow(STATE_STYLE.GOOD, '微信分享成功');
                }

                function onShareCancel() {
                    hideGuide();

                    // ToastUtils.showShortNow(STATE_STYLE.ERROR, '用户已取消分享');

                }

                function onSendSuccess() {
                    hideGuide();

                    ToastUtils.showShortNow(STATE_STYLE.GOOD, '发送给好友成功');
                }

                function onSendCancel() {
                    hideGuide();

                    ToastUtils.showShortNow(STATE_STYLE.ERROR, '用户已取消发送');

                }

                function onInitSuccess() {

                    // ToastUtils.showShortNow(STATE_STYLE.GOOD, 'wxconfig初始化成功');

                }

                function onInitFailed() {

                    ToastUtils.showShortNow(STATE_STYLE.ERROR, 'wxconfig初始化失败');

                }


            }
        ])

});
