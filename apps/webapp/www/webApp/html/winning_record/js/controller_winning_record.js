/**
 * 获奖记录
 * Created by Administrator on 2016/1/7.
 */
define([
    'app',
    'models/model_invite',
    'models/model_app',
    'models/model_pintuan',
    'html/common/service_user_info',
    'components/view-list-item/view_list_item',
    'html/common/constants',
    'utils/toastUtil',
    'html/common/global_service',
    'html/thirdParty/thirdparty_wechat',
    'html/thirdParty/thirdparty_wechat_js',
    'html/common/storage',
    'models/model_goods',
    'models/model_user'
], function(app) {

    app.controller(
        'WinningRecordCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'AppModel','PintuanModel', 'userInfo', 'ToastUtils', 'weChatJs', 'WeChatShare', 'Global', 'GoodsModel', 'inviteModel','Storage','userModel',
            function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, AppModel,PintuanModel, userInfo, ToastUtils, weChatJs, WeChatShare, Global, GoodsModel, inviteModel,Storage,userModel) {
                if (Global.isInweixinBrowser()) {
                    weChatJs.wxConfigInit(onInitSuccess, onInitFailed);
                }
                var _recordView = document.getElementById('congratulation');
                setTimeout(getRebateInfo, 500);
                if(Storage.get('winning_record_status')) {
                    Storage.remove('winning_record_status');
                    initChoujiang();
                } else {
                    initDuobao();
                }


                function initDuobao() {
                    $scope.type = 'duobao';
                    $scope.requestUrl = AppModel.getWinRecordListUrl();
                    $scope.isShowShare = false;
                    $scope.requestParams = {
                        uid: null,
                        logistics_stat: null,
                        status: null
                    };

                    $scope.callBack = {
                        setData: function setData(data) {
                            $scope.recordList = data;
                            var unrecord;
                            for (var i = 0; i < data.length; i++) {
                                unrecord = data[i];
                                console.log(unrecord.goods_img);
                                if (0 == unrecord.status) {
                                    $scope.unreadRecords.push(unrecord);
                                }
                            }
                            showUnreadRecord();
                        }

                    };

                }

                function initChoujiang() {
                    $scope.type = 'zhuanpan';
                    $scope.requestUrl = '?c=nc_user&a=lotteryList2';
                    $scope.isShowShare = false;
                    $scope.requestParams = {};
                    $scope.callBack = {
                        setData: function setData(data) {
                            $scope.zhuanpanRecordList = data;

                        }
                    };
                }
                function initPintuan() {
                    $scope.type = 'pintuan';
                    $scope.requestUrl = '?c=nc_record&a=team_win_record_list';
                    $scope.isShowShare = false;
                    $scope.requestParams = {};
                    $scope.callBack = {
                        setData: function setData(data) {
                            $scope.pintuanRecordList = data;
                        }
                    };
                }
                $scope.unreadRecords = [];
                $scope.changeType = function(type) {
                        if (type == 'duobao') {
                            initDuobao()
                        } else if(type == 'pintuan') {
                        	initPintuan()
                        }else{
                            initChoujiang()
                        }
                        $timeout(function() {
                            $scope.$broadcast('view_list_view.refresh', '9-1-1')
                        })

                    }
                   
                    /**q
                     *
                     * 获取邀请返利信息
                     */
                function getRebateInfo() {
                    inviteModel.getRebateInfo(function(response) {
                        //onSuccess
                        var code = response.data.code;
                        var msg = response.data.msg;
                        $scope.invite_code=response.data.data.invite_code;
                        switch (code) {
                            case 0:
                                var data = response.data.data;
                                var invite_code = data.invite_code;
                                mLink = baseUrl + '#/boostrap/' + invite_code;
                                break;
                            default:
                                ToastUtils.showError(msg);
                                break;

                        }
                    }, function(response) {
                        ToastUtils.showError('请检查网络状态，状态码：' + response.status);
                    });
                }


                function showWinDialog() {
                    _recordView.className = _recordView.className + ' show';
                }

                function hideWinDialog() {
                    _recordView.className = _recordView.className.replace('show', '');
                }

                function showUnreadRecord() {
                    if ($scope.unreadRecords.length > 0) {
                        $timeout(function() {
                            $scope.unreadRecord = $scope.unreadRecords.pop();
                            showWinDialog();
                        }, 300);
                    }
                };

                $scope.closeUnreadRecord = function() {
                    hideWinDialog();
                    showUnreadRecord();
                };
                var mTitle = '一元就能买iPhone。我已经中奖啦，快来试试吧！';
                var mImgUrls = baseUrl + 'img/share_icon.jpg';
                var mLink = baseUrl + '#/boostrap/';
                var mContent = '一元就能买iPhone。最刺激的商城玩法！';

                $scope.doShareRecord = function(record) {
                    mTitle = '一元就能买'+ record.goods_title + '。我已经中奖啦，快来试试吧！'  ;
                    mContent = '一元就能买'+ record.goods_title + '。最刺激的商城玩法！';
                    mLink = baseUrl + '#/activity/' + record.activity_id ;
                    mLink = mLink + '?' +'inviteCode='+$scope.invite_code;
                    console.log(mLink)
                    if(record.goods_img) mImgUrls = record.goods_img;
                    if (Global.isInweixinBrowser()) {
                        showGuide();
                        hideWinDialog();
                        weChatJs.wxShareToTimeline(mTitle, mLink, mImgUrls, onShareSuccess, onShareCancel);
                        weChatJs.wxShareToAppMessage(mTitle,mContent,mLink, mImgUrls, onShareSuccess, onShareCancel)
                        //显示引导分享界面
                    } else if (Global.isInAPP()) {
                        $scope.isShowShare = true;
                    }
                };
                //app分享到朋友圈
                $scope.shareToFriendsCircle = function() {
                    if (Global.isInAPP()) {
                        //app分享
                        var data = {
                            "title": mTitle,
                            "content": mContent,
                            "imgUrl": mImgUrls,
                            "targetUrl": mLink
                        }
                        dmwechat.share('wx_circle', data, function() {
                                ToastUtils.showShortNow(STATE_STYLE.GOOD, '炫耀成功');
                                $scope.isShowShare = false;
                                toAddPoint()
                            }, function(error) {
                                ToastUtils.showShortNow(STATE_STYLE.ERROR, error);
                            })

                    }

                };

                //app分享给好友
                $scope.shareToFriends = function() {
                    if (Global.isInAPP()) {
                        var data = {
                            "title": mTitle,
                            "content": mContent,
                            "imgUrl": mImgUrls,
                            "targetUrl": mLink
                        }
                        dmwechat.share('wx', data, function() {
                            ToastUtils.showShortNow(STATE_STYLE.GOOD, '炫耀成功');
                            $scope.isShowShare = false;
                            toAddPoint()
                        }, function(error) {
                            ToastUtils.showShortNow(STATE_STYLE.ERROR, error);
                        })

                    }

                };

                $scope.addPoint = true;
                function toAddPoint() {
                    if (!$scope.addPoint) return;
                    $scope.addPoint = false;
                    userModel.toAddPoint(function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
                            ToastUtils.showShortNow(STATE_STYLE.GOOD, re.msg);
                        } else {

                        }
                    }, function(response, data) {
                        ToastUtils.showMsgWithCode(7, '获取积分失败：' + '状态码：' + response.status);
                    }, function() {
                        $scope.addPoint = true;
                    })
                }


                $scope.hidePop = function() {
                    $scope.isShowShare = false;
                };
                $scope.parseLogisticsStat = function(logistics_stat) {
                    var state = '';
                    if (0 == logistics_stat) {
                        state = '等待发货';
                    } else if (1 == logistics_stat) {
                        state = '已发货';
                    } else if (2 == logistics_stat) {
                        state = '已签收';
                    }
                    return state;
                };

                $scope.getMyActivityNum = function(activityId) {
                    $scope.hideMyActivityNum();
                    AppModel.getActivityNum(activityId, null, function(response, data) {
                        var code = data.code;
                        if (0 == code) {
                            $scope.activityNums = data.data;
                        } else {
                            ToastUtils.showError('获取失败：' + data.msg);
                        }
                    }, function(response) {
                        ToastUtils.showError('获取亿七购号失败：' + '状态码：' + response.status);
                    });
                };
                //获取拼团号码
                $scope.getMyActivityPinTuanNum = function(activityId) {
                	console.log(activityId)
                    $scope.hideMyActivityNum();
                    PintuanModel.getActivityPinTuanNum(activityId, null, function(response, data) {
                        var code = data.code;
                        if (0 == code) {
                            $scope.activityNums = data.data;
                        } else {
                            ToastUtils.showError('获取失败：' + data.msg);
                        }
                    }, function(response) {
                        ToastUtils.showError('获取亿七购号失败：' + '状态码：' + response.status);
                    });
                };

                $scope.hideMyActivityNum = function() {
                    $scope.activityNums = undefined;
                };

                $scope.startToCheckReceive = function(record, type) {
                	console.log(type)
                	console.log(record)
                    ToastUtils.showLoading('正在签收.....');
                    if (type == 'duobao') {
                        GoodsModel.checkReceive(record.activity_id, function(response, data) {
                            var code = data.code;
                            if (0 == code) {
                                record.logistics_stat = 2;
                                ToastUtils.showSuccess('成功签收');
                            } else {
                                ToastUtils.showError('签收失败：' + data.msg);
                            }
                        }, function(response) {
                            ToastUtils.showError('网络异常：' + response.statusText);
                        }, function() {
                            ToastUtils.hideLoading();
                        });
                    }else if(type == 'pintuan'){
                    	console.log(record)
                    	PintuanModel.checkTeamReceive(record.activity_id,function(response, data) {
                            var code = data.code;
                            if (0 == code) {
                                record.send = 2;
                                ToastUtils.showSuccess('成功签收');
                            } else {
                                ToastUtils.showError('签收失败：' + data.msg);
                            }
                        }, function(response) {
                            ToastUtils.showError('网络异常：' + response.statusText);
                        }, function() {
//                      	window.reload()
                            ToastUtils.hideLoading();
                        });
                    }
                    
                    else {
                        GoodsModel.checkReceiveChoujiang(record.id, function(response, data) {
                            var code = data.code;
                            if (0 == code) {
                                record.send = 2;
                                ToastUtils.showSuccess('成功签收');
                            } else {
                                ToastUtils.showError('签收失败：' + data.msg);
                            }
                        }, function(response) {
                            ToastUtils.showError('网络异常：' + response.statusText);
                        }, function() {
                            ToastUtils.hideLoading();
                        });
                    }

                };

                $scope.startToMyShareOrder = function(activity_id, address) {
                    $state.go('editShareOrder', {
                        'activity_id': activity_id,
                        'address': address
                    })
                };

                //跳转到商品id和订单号码对应的快递查询页面
                $scope.go_express_record = function(activity_id, logistics_num, logistics_id,record) {

                    $state.go('express_query', {
                        'activity_id': activity_id,
                        'logistics_num': logistics_num,
                        'logistics_id': logistics_id, 
                    })
                    Storage.set('logistics_goods_img',record.goods_img);
                    Storage.set('logistics_goods_title',record.goods_title);
                     
                };



                $scope.goToBuy = function() {
                    $state.go('tab.mainpage');
                };
                $scope.goToPintuan = function(){
                    $state.go('baituandazhan');
                }
                $scope.goToTurntable = function() {
                    $state.go('turntable');
                };

                $scope.hideGuide1 = function() {
                    hideGuide();
                }

                /**
                 * 跳转到选择地址页面
                 * @param activityId
                 */
                $scope.startToAddressSelect = function(activityId, type) {
                    if(type == 'choujiang') {
                        Storage.set('winning_record_status',true)
                    } else {
                        Storage.remove('winning_record_status')
                    }
                    $state.go('addressSelect', { activity_id: activityId, type: type });
                };

                var activity_id = $stateParams.activity_id;
                var logistics_order = $stateParams.logistics_order;//订单号码
                var address = $stateParams.address;
                $scope.goBack = function() {
                    $state.go('tab.account2');
                };

                function initial() {
                    setTimeout(function() {
                        $scope.$broadcast('view_list_view.refresh', '9-1-1');
                    }, 300);
                }

                initial();

                function showGuide() {
                    document.getElementById("guidepop").style.display = 'block';

                }

                function hideGuide() {
                    document.getElementById("guidepop").style.display = 'none';

                }

                function onShareSuccess() {
                    hideGuide();
                    toAddPoint()
                }

                function onShareCancel() {
                    hideGuide();
                    ToastUtils.showShortNow(STATE_STYLE.ERROR, '用户已取消分享');

                }

                function onInitSuccess() {



                }

                function onInitFailed() {

                    ToastUtils.showShortNow(STATE_STYLE.ERROR, 'wxconfig初始化失败');

                }




            }
        ])

});
