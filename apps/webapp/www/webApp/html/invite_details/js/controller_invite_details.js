define([
    'app',
    'models/model_user',
    'utils/toastUtil',
], function(app) {

    app.controller(
        'inviteDetailsCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel', 'ToastUtils',
            function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel, ToastUtils) {

                $scope.inviteUserData = [];
                $scope.isLoadFinished = true;
                getInviteUser();

                function getInviteUser(doRefresh) {
                    if (!$scope.isLoadFinished) return;
                    $scope.isLoadFinished = false;
                    userModel.getInviteUser(function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
                            var data = re.data;
                            $scope.inviteUserData = data;
                        } else {
                            ToastUtils.showMsgWithCode(code, re.msg);
                        }
                    }, function(response, data) {
                        ToastUtils.showMsgWithCode(7, '获取邀请好友明细失败：' + '状态码：' + response.status);
                    }, function() {
                        $scope.isLoadFinished = true;
                        if (doRefresh) $scope.$broadcast('scroll.refreshComplete');
                    })
                }
                $scope.doRefresh = function() {
                    // $scope.inviteMoneyData = [];
                    getInviteUser('doRefresh');
                }
                $scope.goBack = function() {
                    $ionicHistory.goBack();
                };


            }
        ])

});
