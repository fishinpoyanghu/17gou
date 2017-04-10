/**
 * 余额明细
 */
define([
    'app',
    'models/model_user',
    'utils/toastUtil',
    'html/common/storage'

], function(app) {

    app.controller(
        'myAccountDetailsCtrl', ['$scope', '$ionicHistory', '$location', '$state', '$stateParams', '$timeout', 'userModel', 'ToastUtils', 'Storage',
            function($scope, $ionicHistory, $location, $state, $stateParams, $timeout, userModel, ToastUtils, Storage) {
                var sessId = Storage.get("sessId");
                // $scope.momey = Storage.get('myAccountData_' + sessId);
                $scope.accountDetailsData = [];
                $scope.isLoadFinished = true;
                $scope.page = 0;
                getAccountDetails();
                $scope.getAccountDetails = getAccountDetails;
                function getAccountDetails(doRefresh) {
                    if (!$scope.isLoadFinished) return;
                    $scope.page++;
                    $scope.isLoadFinished = false;
                    userModel.getAccountDetails($scope.page, function(xhr, re) {
                        var code = re.code;
                        if (code == 0) {
                            var data = re.data;
                            var len = data.length;
                            for (var i = 0; i < len; i++) {
                                $scope.accountDetailsData.push(data[i]);
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
                        ToastUtils.showMsgWithCode(7, '获取余额列表失败：' + '状态码：' + response.status);
                    }, function() {
                        $scope.isLoadFinished = true;
                        if (doRefresh) $scope.$broadcast('scroll.refreshComplete');
                    })
                }
                getAccount()
                function getAccount() {
                    userModel.getAccount(function(xhr, re) {
                        if (re.code == 0) {
                            $scope.momey = re.data.money;
                        } else {
                            ToastUtils.showError(re.msg);
                        }
                    }, function(xhr, re) {
                        ToastUtils.showError(re.msg);
                    }, function() {
                        ToastUtils.hideLoading();
                    })
                }
                $scope.doRefresh = function() {
                    $scope.page = 0;
                    $scope.accountDetailsData = [];
                    getAccountDetails('doRefresh');
                }
                $scope.goBack = function() {
                    $ionicHistory.goBack();
                };

            }
        ])

});
