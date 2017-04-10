define([
    'app',
    'models/model_user',
    'utils/toastUtil'
], function(app) {

    app.controller(
        'commissionDetailsCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel', 'ToastUtils',
            function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel, ToastUtils) {
                $scope.inviteMoneyData = [];
                $scope.isLoadFinished = true;
                $scope.page = 0;
                getInviteMoney();
                $scope.getInviteMoney = getInviteMoney;
                function getInviteMoney(doRefresh) {
                    if (!$scope.isLoadFinished) return;
                    $scope.page++;
                    $scope.isLoadFinished = false;
                    userModel.getInviteMoney($scope.page, function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
                            var data = re.data;
                            var len = data.length;
                            for (var i = 0; i < len; i++) {
                                $scope.inviteMoneyData.push(data[i]);
                            }
                            if (len < 10) {
                                $scope.moreDataCanBeLoaded = false;
                            } else {
                                $scope.moreDataCanBeLoaded = true;
                            }
                            $scope.$broadcast('scroll.infiniteScrollComplete');
                        } else {
                            ToastUtils.showMsgWithCode(code, re.msg);
                        }
                    }, function(response, data) {
                        ToastUtils.showMsgWithCode(7, '获取师徒收益列表失败：' + '状态码：' + response.status);
                    }, function() {
                        $scope.isLoadFinished = true;
                        if (doRefresh) $scope.$broadcast('scroll.refreshComplete');
                    })
                }
                $scope.doRefresh = function() {
                    $scope.page = 0;
                    $scope.inviteMoneyData = [];
                    getInviteMoney('doRefresh');
                }
                $scope.goBack = function() {
                    $ionicHistory.goBack();
                };

            }
        ])

});
