
define([
    'app',
    'models/model_red_packet',
    // 'html/common/constants',
    'components/view-counter/view_counter',
    'utils/toastUtil',
    'html/common/geturl_service'
], function(app) {

    app.controller(
        'grabRedPacketCtrl', ['$scope', '$ionicHistory', '$state', '$location', 'redPacketModel', 'ToastUtils','$ionicPopup','MyUrl','$timeout',
            function($scope, $ionicHistory, $state, $location, redPacketModel, ToastUtils,$ionicPopup,MyUrl,$timeout) {

                

                $scope.$on('$ionicView.beforeEnter', function(ev, data) {
                  ToastUtils.showLoading('加载中....');
                  init()
                })

                $scope.doRefresh = function() {
                  init()
                }

                function init() {
                  
                  getRedPacketDetail()
                }

                function getRedPacketDetail(){
                  $scope.isLoadFinished = false;
                  redPacketModel.getRedPacketDetail($state.params.activity_id, function(xhr, re) {
                      var code = re.code;
                      if (code == 0) {
                        $scope.redPacket = "";
                        $timeout(function() {
                          $scope.redPacket = re.data;
                        })
                        
                      } else {
                          ToastUtils.showMsgWithCode(code, re.msg);
                      }
                  }, function(response, data) {
                      ToastUtils.showMsgWithCode(7, '获取红包失败;' + '状态码：' + response.status);
                  },function() {
                    $scope.isLoadFinished = true ;
                    ToastUtils.hideLoading();
                    $scope.$broadcast('scroll.refreshComplete');
                  })
                }
                $scope.back = function() {
                    if(ionic.Platform.isWebView()) {
                      $ionicHistory.goBack();
                    } else {
                      history.back();
                    }
                    
                }

                $scope.join = function(redPacket) {
                  try {
                    if (!MyUrl.isLogin()) {
                      event.preventDefault();
                      $state.go('login',{'state':STATUS.LOGIN_ABNORMAL});
                      ToastUtils.showWarning('请先登录！！');
                      return;
                    } else {
                    }
                  } catch (e) {
                    console.error('登录判断跳转出错'+ e.name+'：'+ e.message);
                  }
                  if(redPacket.flag != 0) {
                    ToastUtils.showMsgWithCode(7, '该红包已经不能参与~');
                    return;
                  }
                  if(redPacket.already != 0) {
                    ToastUtils.showMsgWithCode(7, '您已经参与过该红包了~');
                    return;
                  }
                  ToastUtils.showLoading('正在努力为您抢夺红包中....');
                  joinRed()
                 
                }
                function joinRed(){
                  redPacketModel.joinRed($state.params.activity_id, function(xhr, re) {
                      var code = re.code;
                      if (code == 0) {
                        joinWait(re.data.order_id)
                      } else {
                          ToastUtils.hideLoading();
                          
                          ToastUtils.showMsgWithCode(code, re.msg);
                      }
                  }, function(response, data) {
                      ToastUtils.hideLoading();
                      
                      ToastUtils.showMsgWithCode(7, '参与红包失败;' + '状态码：' + response.status);
                  },null)
                }

                function joinWait(order_id) {
                  redPacketModel.joinWait(order_id, function(xhr, re) {
                      var code = re.code;
                      if (code == 0) {
                        angular.isDefined($scope.timeout) && $timeout.cancel($scope.timeout);
                        $scope.timeout = null;
                        $scope.redPacket.already = 1;
                        $scope.redPacket.user_num = Number($scope.redPacket.user_num) + 1;
                        $scope.redPacket.lucky_num = re.data.lucky_num;
                        var info = '<div class="text-center">抢红包成功，幸运号为：'+ re.data.lucky_num +'请耐心等待揭晓~~</div>'
                        var alertPopup = $ionicPopup.alert({
                            scope: $scope,
                            title: '<div class="pop-book-title">温馨提示</div>',
                            template: info,
                            okType: 'button-balanced',
                            okText: '确定',
                        });

                        alertPopup.then(function(res) {
                           $scope.back();
                        });

                      } else if(code == 2) {
                        $scope.timeout = $timeout(function() {
                            joinWait(order_id)
                        }, 1000)
                      } else {
                          ToastUtils.showMsgWithCode(code, re.msg);
                      }
                  }, function(response, data) {
                      ToastUtils.showMsgWithCode(7, '参与红包失败;' + '状态码：' + response.status);
                  },function() {
                    ToastUtils.hideLoading();
                  })
                }

            }
        ])

});
