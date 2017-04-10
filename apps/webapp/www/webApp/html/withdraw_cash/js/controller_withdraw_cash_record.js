define([
    'app',
    'models/model_user',
    'utils/toastUtil'
], function(app) {

    app.controller(
        'withdrawCashRecordCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel', 'ToastUtils',
            function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel, ToastUtils) {
                $scope.listData = [];
                $scope.isLoadFinished = true;
                $scope.page = 0;
                getData();
                $scope.getData = getData;
                function getData(doRefresh) {
                    if (!$scope.isLoadFinished) return;
                    $scope.page++;
                    $scope.isLoadFinished = false;
                    userModel.getCashRecord($scope.page, function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
                            var data = re.data;
                            var len = data.length;
                            for (var i = 0; i < len; i++) {
                                $scope.listData.push(data[i]);
                            }
                            if (len <= 10) {
                                $scope.moreDataCanBeLoaded = false;
                            } else {
                                $scope.moreDataCanBeLoaded = true;
                            }
                            $scope.$broadcast('scroll.infiniteScrollComplete');
                        } else {
                            ToastUtils.showMsgWithCode(code, re.msg);
                        }
                    }, function(response, data) {
                        ToastUtils.showMsgWithCode(7, '获取提现申请记录失败：' + '状态码：' + response.status);
                    }, function() {
                        $scope.isLoadFinished = true;
                        if (doRefresh) $scope.$broadcast('scroll.refreshComplete');
                    })
                }
                $scope.doRefresh = function() {
                    $scope.page = 0;
                    $scope.listData = [];
                    getData('doRefresh');
                }
                $scope.goBack = function() {
                    $ionicHistory.goBack();
                };

            }
        ])

});
