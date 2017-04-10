define([
    'app',
    'models/model_app',
    'utils/toastUtil',
    'html/common/geturl_service'
], function(app) {

    app.controller(
        'commentDetailsCtrl', ['$scope', '$ionicHistory', '$state', 'ToastUtils','AppModel','MyUrl',
            function($scope, $ionicHistory, $state, ToastUtils,AppModel,MyUrl) {
                $scope.comment_placeholder = '我也说一句'
                $scope.id = $state.params.id;
                $scope.commentsList = [];
                $scope.page = 0;
                $scope.isLoadFinished = true;
                ToastUtils.showLoading('加载中....');
                getCommentList();
                $scope.getCommentList = getCommentList;
                function getCommentList(doRefresh) {
                    if (!$scope.isLoadFinished) return;
                    $scope.page++;
                    $scope.isLoadFinished = false;
                    AppModel.getCommentList($scope.id,$scope.page, function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
                            var data = re.data;
                            var len = data.length;
                            for (var i = 0; i < len; i++) {
                                $scope.commentsList.push(data[i]);
                            }
                            if (len < 20) {
                                $scope.moreDataCanBeLoaded = false;
                            } else {
                                $scope.moreDataCanBeLoaded = true;
                            }
                            $scope.$broadcast('scroll.infiniteScrollComplete');
                        } else {
                            ToastUtils.showMsgWithCode(code, re.msg);
                        }
                    }, function(response, data) {
                        ToastUtils.showMsgWithCode(7, '获取评论列表失败：' + '状态码：' + response.status);
                    }, function() {
                        $scope.isLoadFinished = true;
                        ToastUtils.hideLoading();
                        if (doRefresh) $scope.$broadcast('scroll.refreshComplete');
                    })
                }

                $scope.doRefresh = function() {
                  $scope.page = 0;
                  $scope.commentsList = [];
                  getCommentList('doRefresh');
                }

                $scope.comment_content = '';
                $scope.comment_uid = "";
                // 评论
                $scope.comment = function(){
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
                  if($scope.comment_content == '') {
                    ToastUtils.showMsgWithCode(7, '评论内容不能为空');
                    return;
                  }
                  AppModel.comment($scope.id,$scope.comment_content,$scope.comment_uid, function(xhr, re) {
                      var code = re.code;
                      if (code == 0) {
                        $scope.comment_content = '';
                        $scope.comment_uid = "";
                        $scope.comment_placeholder = '我也说一句';
                        ToastUtils.showSuccess('评论成功');
                        $scope.doRefresh()
                      } else {
                          ToastUtils.showMsgWithCode(code, re.msg);
                      }
                  }, function(response, data) {
                      ToastUtils.showMsgWithCode(7, '评论失败：' + '状态码：' + response.status);
                  },null)
                           
                }

                $scope.commentPerson = function(uid,nick)  {
                  $scope.comment_placeholder = '回复' + nick;
                  $scope.comment_uid = uid;
                }

                $scope.back = function() {
                    $ionicHistory.goBack();
                }

               


            }
        ])

});
