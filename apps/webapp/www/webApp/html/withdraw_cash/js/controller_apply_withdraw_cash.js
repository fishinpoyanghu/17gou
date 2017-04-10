define([
    'app',
    'models/model_user',
    'utils/toastUtil',
    'html/common/global_service'
], function(app) {

    app.controller(
        'applyWithdrawCashCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel', 'ToastUtils', '$state','Global',
            function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel, ToastUtils, $state,Global ) {
                $scope.t_index=1;

                $scope.cash = {
                    wechatId: null,
                    money: null
                };
                /*$scope.tx=true;
                if($scope.cash.money===''){
                    $scope.tx=true;
                }else{
                    $scope.tx=false;
                }*/
                $scope.inviteMoney = sessionStorage.getItem('inviteMoney');


                function validate() {
                    var msg = "false";
                    var money = Number($scope.cash.money) || 0;
                    if ($scope.cash.wechatId === '') {
                        msg = "微信号不能为空";
                    } else if (money == 0) {
                        msg = "提现金额不能为空";
                    } else if (money > $scope.inviteMoney) {
                        msg = "提现金额不能大于当前师徒收益";
                    } else {
                        msg = "true";
                    }

                    return msg;
                }

                function submit_wx(){ //添加获取当前用户的数据函数
                    userModel.getLoginUserInfo(function(response){
                        var postData = {
                            weixin_id: ''
                        };
                        var code = response.data.code;
                        var data = response.data.data;
                        console.log(data.type);
                        if(code === 0){
                            /*if(!(/^(13|18|15|14|17)\d{9}$/i.test(response.data.data.phone))){
                             }*/

                            if(data.type==0){
                                $scope.tx_yh=true;//请填写您的微信账号
                                $scope.tx_wx=false;//此次提现将会直接提现到微信账号
                                if($scope.cash.wechatId===''){
                                    $scope.tx_yh=true;//请填写您的微信账号
                                }

                            }else{
                                $scope.tx_wx=true;//此次提现将会直接提现到微信账号
                                $scope.tx_yh=false;//请填写您的微信账号
                                postData.weixin_id=$scope.cash.wechatId;

                            }

                        }
                    },function(response){
                        /* ToastUtils.showError('请检查网络状态！');*/
                    });
                }

                submit_wx();

                $scope.submit = function() {
                    submit_wx();
                    var validateMsg = validate();
                    if (validateMsg !== 'true') {
                        ToastUtils.showTips(validateMsg);
                        return;
                    }
                    var postData = {
                        weixin_id: $scope.cash.wechatId,
                        money: $scope.cash.money
                    };
                    userModel.getApplyCash(postData, function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {

                            ToastUtils.showSuccess('申请提现成功');
                            /*$state.go('inviteFriends');*/ //原来的
                            $state.go('tab.account2');

                        } else {
                            ToastUtils.showMsgWithCode(code, re.msg);
                        }
                    }, function(response, data) {
                        ToastUtils.showMsgWithCode(7, '申请提现失败：' + '状态码：' + response.status);
                    }, function() {
                        $scope.isLoadFinished = true;
                        if (doRefresh) $scope.$broadcast('scroll.refreshComplete');
                    });
                };




                $scope.goBack = function() {
                    $ionicHistory.goBack();
                };

                //显示提现到微信零钱
                $scope.show_wx=function(){
                    $scope.t_index=1;
                }
                //显示提现到银行卡
                $scope.show_yh=function(){
                    $scope.t_index=2;
                }


                var gapHeight;
                if(Global.isInweixinBrowser()) {
                    $scope.leftBarHeight = innerHeight - gapHeight;
                    $scope.leftBarTop = '44px';
                    $scope.inWechatB = true;
                } else {
                    $scope.leftBarHeight = innerHeight - 44 - gapHeight;
                    if(ionic.Platform.isIOS() || ionic.Platform.isIPad()) {
                        $scope.inIosApp = true;
                    } else {
                        $scope.inIosApp = false;
                    }
                    $scope.inWechatB = false;

                }

            }
        ])

});
